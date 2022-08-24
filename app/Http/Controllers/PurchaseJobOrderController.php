<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\AbstractQuotation;
use App\Models\AbstractQuotationItem;
use App\Models\PurchaseJobOrder;
use App\Models\PurchaseJobOrderItem;
use App\Models\ObligationRequestStatus;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\InventoryStock;
use App\Models\InventoryStockItem;

use App\Models\EmpAccount as User;
use App\Models\EmpUnit;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\Signatory;
use App\Models\ItemUnitIssue;
use Carbon\Carbon;
use Auth;
use DB;

use App\Plugins\Notification as Notif;

class PurchaseJobOrderController extends Controller
{
    protected $poLetters = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK',
        'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV',
        'AW', 'AX', 'AY', 'AZ'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // Get module access
        $module = 'proc_po_jo';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedAccountantSigned = Auth::user()->getModuleAccess($module, 'signed');
        $isAllowedApprove = Auth::user()->getModuleAccess($module, 'approve');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedCancel = Auth::user()->getModuleAccess($module, 'cancel');
        $isAllowedUncancel = Auth::user()->getModuleAccess($module, 'uncancel');
        $isAllowedDelivery = Auth::user()->getModuleAccess($module, 'delivery');
        $isAllowedInspection = Auth::user()->getModuleAccess($module, 'inspection');
        $isAllowedORSCreate = Auth::user()->getModuleAccess('proc_ors_burs', 'create');
        $isAllowedORS = Auth::user()->getModuleAccess('proc_ors_burs', 'is_allowed');
        $isAllowedAbstract = Auth::user()->getModuleAccess('proc_abs', 'is_allowed');
        $isAllowedIAR = Auth::user()->getModuleAccess('proc_iar', 'is_allowed');

        // User groups
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();
        $roleHasAdministrator = Auth::user()->hasOrdinaryRole();
        $roleHasRD = Auth::user()->hasRdRole();
        $roleHasARD = Auth::user()->hasArdRole();
        $roleHasPSTD = Auth::user()->hasPstdRole();
        $roleHasPlanning = Auth::user()->hasPlanningRole();
        $roleHasProjectStaff = Auth::user()->hasProjectStaffRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAccountant = Auth::user()->hasAccountantRole();
        $roleHasPropertySupply = Auth::user()->hasPropertySupplyRole();
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();

        $userIDs = Auth::user()->getGroupHeads();
        $empUnitDat = EmpUnit::has('unithead')->find(Auth::user()->unit);
        $userIDs[] = Auth::user()->id;

        if ($empUnitDat && $empUnitDat->unithead) {
            $userIDs[] = $empUnitDat->unithead->id;
        }

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $poData = PurchaseRequest::with(['funding', 'requestor'])
                                 ->whereHas('po', function($query) {
            $query->whereNotNull('id');
        })->whereNull('date_pr_cancelled');

        if ($roleHasOrdinary && (!$roleHasDeveloper || !$roleHasRD || !$roleHasPropertySupply ||
            !$roleHasAccountant || !$roleHasBudget || !$roleHasPSTD)) {
            if (Auth::user()->emp_type == 'contractual') {
                if (Auth::user()->getDivisionAccess()) {
                    $empDivisionAccess = Auth::user()->getDivisionAccess();
                } else {
                    $empDivisionAccess = [Auth::user()->division];
                }

                $poData = $poData->whereIn('requested_by', $userIDs);
            } else {
                $empDivisionAccess = [Auth::user()->division];
                $poData = $poData->where('requested_by', Auth::user()->id);
            }
        } else {
            if ($roleHasPSTD) {
                $empDivisionAccess = [Auth::user()->division];
            } else {
                $empDivisionAccess = Auth::user()->getDivisionAccess();
            }
        }

        $poData = $poData->whereHas('division', function($query)
                use($empDivisionAccess) {
            $query->whereIn('id', $empDivisionAccess);
        });

        if (!empty($keyword)) {
            $poData = $poData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('pr_no', 'like', "%$keyword%")
                    ->orWhere('date_pr', 'like', "%$keyword%")
                    ->orWhere('purpose', 'like', "%$keyword%")
                    ->orWhereHas('funding', function($query) use ($keyword) {
                        $query->where('project_title', 'like', "%$keyword%");
                    })->orWhereHas('stat', function($query) use ($keyword) {
                        $query->where('status_name', 'like', "%$keyword%");
                    })->orWhereHas('requestor', function($query) use ($keyword) {
                        $query->where('firstname', 'like', "%$keyword%")
                              ->orWhere('middlename', 'like', "%$keyword%")
                              ->orWhere('lastname', 'like', "%$keyword%");
                    })->orWhereHas('items', function($query) use ($keyword) {
                        $query->where('item_description', 'like', "%$keyword%");
                    })->orWhereHas('division', function($query) use ($keyword) {
                        $query->where('division_name', 'like', "%$keyword%");
                    })->orWhereHas('po', function($query) use ($keyword) {
                        $query->where('po_no', 'like', "%$keyword%")
                              ->orWhere('id', 'like', "%$keyword%")
                              ->orWhere('date_po', 'like', "%$keyword%")
                              ->orWhere('date_po_approved', 'like', "%$keyword%")
                              ->orWhere('date_cancelled', 'like', "%$keyword%")
                              ->orWhere('place_delivery', 'like', "%$keyword%")
                              ->orWhere('date_delivery', 'like', "%$keyword%")
                              ->orWhere('delivery_term', 'like', "%$keyword%")
                              ->orWhere('payment_term', 'like', "%$keyword%")
                              ->orWhere('amount_words', 'like', "%$keyword%")
                              ->orWhere('grand_total', 'like', "%$keyword%")
                              ->orWhere('fund_cluster', 'like', "%$keyword%");
                    });
            });
        }

        $poData = $poData->sortable(['pr_no' => 'desc'])->paginate(15);

        foreach ($poData as $po) {
            foreach ($po->po as $poDat) {
                $poDat->doc_status = $instanceDocLog->checkDocStatus($poDat->id);

                $instanceSupplier = Supplier::find($poDat->awarded_to);
                $companyName = $instanceSupplier->company_name;
                $poDat->company_name = $companyName;
            }
        }

        return view('modules.procurement.po-jo.index', [
            'list' => $poData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'isAllowedAccountantSigned' => $isAllowedAccountantSigned,
            'isAllowedApprove' => $isAllowedApprove,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedCancel' => $isAllowedCancel,
            'isAllowedUncancel' => $isAllowedUncancel,
            'isAllowedDelivery' => $isAllowedDelivery,
            'isAllowedInspection' => $isAllowedInspection,
            'isAllowedORSCreate' => $isAllowedORSCreate,
            'isAllowedORS' => $isAllowedORS,
            'isAllowedAbstract' => $isAllowedAbstract,
            'isAllowedIAR' => $isAllowedIAR,
            'roleHasOrdinary' => $roleHasOrdinary,
            'roleHasBudget' => $roleHasBudget,
            'roleHasAccountant' => $roleHasAccountant,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showItems($id) {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate($prID) {
        $suppliers = Supplier::select('suppliers.id', 'suppliers.company_name')
                             ->join('abstract_quotation_items as item', 'item.supplier', '=', 'suppliers.id')
                             ->where('item.pr_id', $prID)
                             ->orderBy('suppliers.company_name')
                             ->distinct()
                             ->get();

        return view('modules.procurement.po-jo.create', [
            'prID' => $prID,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $prID) {
        $awardedTo = $request->awarded_to;
        $documentType = $request->document_type;

        try {
            $instancePR = PurchaseRequest::find($prID);
            $prNo = $instancePR->pr_no;

            foreach ($this->poLetters as $letter) {
                $poNo = "$prNo-$letter";
                $countPO = DB::table('purchase_job_orders')
                             ->where('po_no', $poNo)
                             ->count();

                if ($countPO == 0) {
                    $instancePO = new PurchaseJobOrder;
                    $instancePO->po_no = $poNo;
                    $instancePO->pr_id = $prID;
                    $instancePO->awarded_to = $awardedTo;
                    $instancePO->document_type = $documentType;
                    $instancePO->status = 6;
                    $instancePO->save();

                    $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
                    $msg = "$documentType '$poNo' successfully created.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route('po-jo', ['keyword' => $poNo])
                                     ->with('success', $msg);
                }
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $prID])
                             ->with('failed', $msg);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit(Request $request, $id) {
        $unitIssues = ItemUnitIssue::orderBy('unit_name')->get();
        $awardees = Supplier::orderBy('company_name')->get();
        $instancePO = PurchaseJobOrder::with(['poitems', 'awardee'])->find($id);
        $prID = $instancePO->pr_id;
        $poNo = $instancePO->po_no;
        $poDate = $instancePO->date_po;
        $awardedTo = $instancePO->awarded_to;
        $companyName = $instancePO->awardee['company_name'];
        $companyAddress = $instancePO->awardee['address'];
        $companyTinNo = $instancePO->awardee['tin_no'];
        $placeDelivery = $instancePO->place_delivery;
        $dateDelivery = $instancePO->date_delivery;
        $deliveryTerm = $instancePO->delivery_term;
        $paymentTerm = $instancePO->payment_term;
        $amountWords = $instancePO->amount_words;
        $grandTotal = $instancePO->grand_total;
        $fundCluster = $instancePO->fund_cluster;
        $sigDepartment = $instancePO->sig_department;
        $sigApproval = $instancePO->sig_approval;
        $sigFundsAvailable = $instancePO->sig_funds_available;
        $documentType = $instancePO->document_type;
        $poItems = $instancePO->poitems;
        $instanceAbstract = AbstractQuotation::with('modeproc')->where('pr_id', $prID)->first();
        $modeProcurement = isset($instanceAbstract->modeproc->mode_name) ? $instanceAbstract->modeproc->mode_name : 'N/A';
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();
        $poNumbers = PurchaseJobOrder::select('po_no')->where('pr_id', $prID)->get();

        if ($documentType == 'po') {
            $view = 'modules.procurement.po-jo.po-update';
        } else {
            $view = 'modules.procurement.po-jo.jo-update';
        }

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view($view, compact(
            'id', 'poNo', 'poDate', 'companyName', 'companyAddress',
            'companyTinNo', 'placeDelivery', 'dateDelivery',
            'deliveryTerm', 'amountWords', 'grandTotal', 'paymentTerm',
            'fundCluster', 'sigDepartment', 'sigApproval',
            'sigFundsAvailable', 'poItems', 'signatories',
            'instancePO' , 'poNumbers', 'modeProcurement',
            'unitIssues', 'awardees', 'awardedTo'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $awardedTo = $request->awarded_to;
        $poDate = $request->date_po;
        $placeDelivery = $request->place_delivery;
        $deliveryTerm = $request->delivery_term;
        $dateDelivery = $request->date_delivery;
        $paymentTerm = $request->payment_term;
        $amountWords = $request->amount_words;
        $sigFundsAvailable = $request->sig_funds_available;
        $sigApproval = $request->sig_approval;
        $sigDepartment = $request->sig_department;
        $grandTotal = $request->grand_total;

        $itemIDs = $request->item_id;
        $unitIssues = $request->unit;
        $itemDescriptions = $request->item_description;
        $quantities = $request->quantity;
        $unitCosts = $request->unit_cost;
        $totalCosts = $request->total_cost;
        $poNumbers = $request->po_jo_no;
        $excludes = $request->exclude;

        try {
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;
            $documentType = $instancePO->document_type;

            $instancePO->awarded_to = $awardedTo;
            $instancePO->date_po = $poDate;
            $instancePO->place_delivery = $placeDelivery;
            $instancePO->date_delivery = $dateDelivery;
            $instancePO->payment_term = $paymentTerm;
            $instancePO->amount_words = $amountWords;
            $instancePO->grand_total = $grandTotal;
            $instancePO->sig_funds_available = $sigFundsAvailable;
            $instancePO->sig_approval = $sigApproval;

            if ($documentType == 'po') {
                $instancePO->delivery_term = $deliveryTerm;
            } else {
                $instancePO->sig_department = $sigDepartment;
            }

            $instancePO->save();

            if (is_array($itemIDs) && count($itemIDs)) {
                foreach ($itemIDs as $itemCtr => $itemID) {
                    $instancePOItem = PurchaseJobOrderItem::find($itemID);
                    $instancePOItem->unit_issue = $unitIssues[$itemCtr];
                    $instancePOItem->item_description = $itemDescriptions[$itemCtr];
                    $instancePOItem->quantity = $quantities[$itemCtr];
                    $instancePOItem->unit_cost = $unitCosts[$itemCtr];
                    $instancePOItem->total_cost = $totalCosts[$itemCtr];
                    $instancePOItem->po_no = $poNumbers[$itemCtr];
                    $instancePOItem->excluded = $excludes[$itemCtr];
                    $instancePOItem->save();

                    $instanceInvStockItem = InventoryStockItem::where('po_item_id', $itemID)->first();

                    if ($instanceInvStockItem) {
                        $instanceInvStockItem->quantity = $quantities[$itemCtr];
                        $instanceInvStockItem->unit_issue = $unitIssues[$itemCtr];
                        $instanceInvStockItem->description = $itemDescriptions[$itemCtr];
                        $instanceInvStockItem->amount = $totalCosts[$itemCtr];
                        $instanceInvStockItem->save();
                    }
                }
            }

            $instanceORS = ObligationRequestStatus::where('po_no', $poNo)->first();

            if ($instanceORS) {
                $orsID = $instanceORS->id;
                $instanceORS->po_no = $poNo;
                $instanceORS->payee = $awardedTo;
                $instanceORS->amount = $grandTotal;
                $instanceORS->save();

                $instanceDV = DisbursementVoucher::where('ors_id', $orsID)->first();

                if ($instanceDV) {
                    $instanceDV->payee = $awardedTo;
                    $instanceDV->amount = $grandTotal;
                    $instanceDV->save();
                }
            }

            $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
            $msg = "$documentType '$poNo' successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $poNo])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo')
                             ->with('failed', $msg);
        }
    }

    /**
     * Soft deletes the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id) {
        $isDestroy = $request->destroy;

        if ($isDestroy) {
            $response = $this->destroy($request, $id);

            if ($response->alert_type == 'success') {
                return redirect()->route('po-jo', ['keyword' => $response->pr_id])
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route('po-jo')
                                 ->with($response->alert_type, $response->msg);
            }
        } else {
            try {
                $instancePO = PurchaseJobOrder::find($id);
                $documentType = $instancePO->document_type;
                $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
                $poNo = $instancePO->po_no;
                $prID = $instancePO->pr_id;
                $countPOItem = PurchaseJobOrderItem::where('po_no', $poNo)->count();

                if ($countPOItem > 0) {
                    $msg = "Transfer first the item/s of $documentType '$poNo' to other document.";
                    Auth::user()->log($request, $msg);

                    return redirect()->route('po-jo', ['keyword' => $prID])
                                     ->with('warning', $msg);
                }

                $instancePO->delete();

                $msg = "$documentType '$poNo' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect()->route('po-jo', ['keyword' => $prID])
                                 ->with('success', $msg);
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect()->route('po-jo')
                                 ->with('failed', $msg);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($request, $id) {
        try {
            $instancePO = PurchaseJobOrder::find($id);
            $documentType = $instancePO->document_type;
            $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
            $prID = $instancePO->pr_id;
            $poNo = $instancePO->po_no;
            $poID = $instancePO->id;
            $instanceORS = ObligationRequestStatus::where('po_no', $poNo)->first();
            $orsID = $instanceORS ? $instanceORS->id : NULL;
            $instanceDV = InspectionAcceptance::where('ors_id', $orsID)->first();
            $instanceIAR = InspectionAcceptance::where('po_id', $poID)->first();
            $countPOItem = PurchaseJobOrderItem::where('po_no', $poNo)->count();

            if ($countPOItem > 0) {
                $msg = "Transfer first the item/s of $documentType '$poNo' to other document.";
                Auth::user()->log($request, $msg);

                return (object) [
                    'msg' => $msg,
                    'alert_type' => 'warning',
                    'pr_id' => $prID
                ];
            }

            if ($instanceDV) {
                $instanceDV->forceDelete();
            }

            if ($instanceIAR) {
                $instanceIAR->forceDelete();
            }

            if ($instanceORS) {
                $instanceORS->forceDelete();
            }

            $instancePO->forceDelete();

            $msg = "$documentType '$poNo' permanently deleted.";
            Auth::user()->log($request, $msg);

            return (object) [
                'msg' => $msg,
                'alert_type' => 'success',
                'pr_id' => $prID
            ];
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);

            return (object) [
                'msg' => $msg,
                'alert_type' => 'failed'
            ];
        }
    }

    public function accountantSigned(Request $request, $id) {
        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;
            $documentType = $instancePO->document_type == 'po' ?
                            'Purchase Order' : 'Job Order';

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);

            if ($isDocGenerated) {
                $instancePO->date_accountant_signed = Carbon::now();
                $instancePO->save();

                $instanceNotif->notifyAccountantSignedPO($id);

                $msg = "$documentType '$poNo' is successfully set to
                       'Cleared/Signed by Accountant'.";
                Auth::user()->log($request, $msg);
                return redirect()->route('po-jo', ['keyword' => $id])
                                 ->with('success', $msg);
            } else {
                $msg = "Document for $documentType '$poNo' should be generated first.";
                Auth::user()->log($request, $msg);
                return redirect()->route('po-jo', ['keyword' => $id])
                                     ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function approve(Request $request, $id) {
        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;
            $documentType = $instancePO->document_type == 'po' ?
                            'Purchase Order' : 'Job Order';

            $instancePO->date_po_approved = Carbon::now();
            $instancePO->save();

            $instanceNotif->notifyApprovedPO($id);

            $msg = "$documentType '$poNo' is successfully set to
                   'Approved'.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showIssue($id) {
        $users = User::orderBy('firstname')->get();

        return view('modules.procurement.po-jo.issue', [
            'id' => $id,
            'users' => $users
        ]);
    }

    public function issue(Request $request, $id) {
        $issuedTo = $request->issued_to;
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;
            $documentType = $instancePO->document_type == 'po' ?
                            'Purchase Order' : 'Job Order';

            $instanceDocLog->logDocument($id, Auth::user()->id, $issuedTo, "issued", $remarks);
            $issuedToName = Auth::user()->getEmployee($issuedTo)->name;

            $instanceNotif->notifyIssuedPO($id, $issuedTo);

            $msg = "$documentType '$poNo' successfully issued to $issuedToName.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('rfq', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }

    public function showReceive($id) {
        return view('modules.procurement.po-jo.receive', [
            'id' => $id,
        ]);
    }

    public function receive(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;
            $documentType = $instancePO->document_type == 'po' ?
                            'Purchase Order' : 'Job Order';

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received", $remarks);
            $instanceNotif->notifyReceivedPO($id);

            $msg = "$documentType '$poNo' successfully received.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('rfq', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }

    public function delivery(Request $request, $id) {
        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;
            $documentType = $instancePO->document_type == 'po' ?
                            'Purchase Order' : 'Job Order';

            if ($instancePO->status == 7) {
                $instancePO->status = 8;
                $instancePO->save();

                $instanceNotif->notifyDeliveredPO($id);

                $msg = "$documentType '$poNo' is successfully set to
                       'For Delivery'.";
                Auth::user()->log($request, $msg);
                return redirect()->route('po-jo', ['keyword' => $id])
                                 ->with('success', $msg);
            } else {
                $msg = "ORS/BURS document for this $documentType '$poNo' should be
                       obligated first.";
                Auth::user()->log($request, $msg);
                return redirect()->route('po-jo', ['keyword' => $id])
                                 ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function inspection(Request $request, $id) {
        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instancePO = PurchaseJobOrder::with('ors')->find($id);
            $poNo = $instancePO->po_no;
            $prID = $instancePO->pr_id;
            $orsID = $instancePO->ors['id'];
            $documentType = $instancePO->document_type == 'po' ?
                            'Purchase Order' : 'Job Order';

            $iarNo = "IAR-" . $poNo;
            $instanceIAR = InspectionAcceptance::withTrashed()->where('iar_no', $iarNo)->first();

            if (!$instanceIAR) {
                $instanceIAR = new InspectionAcceptance;
                $instanceIAR->iar_no = $iarNo;
                $instanceIAR->pr_id = $prID;
                $instanceIAR->ors_id = $orsID;
                $instanceIAR->po_id = $id;
                $instanceIAR->save();
            } else {
                $instanceDocLog->logDocument($instanceIAR->id, Auth::user()->id, NULL, '-');
                InspectionAcceptance::withTrashed()->where('iar_no', $iarNo)->restore();
            }

            $instancePO->status = 9;
            $instancePO->save();

            $instanceNotif->notifyInspectionPO($id);

            $msg = "$documentType '$poNo' is now ready for inspection.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function cancel(Request $request, $id) {
        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;
            $instancePO->date_cancelled = Carbon::now();
            $instancePO->save();

            //$instanceDocLog->logDocument($id, Auth::user()->id, NULL, '-');
            $instanceNotif->notifyCancelledPO($id);

            $msg = "Purchase/Job Order '$poNo' successfully cancelled.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function uncancel(Request $request, $id) {
        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;
            $instancePO->date_cancelled = NULL;
            $instancePO->save();

            $instanceNotif->notifyRestoredPO($id);

            $msg = "Purchase/Job Order '$poNo' successfully restored.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function restore(Request $request, $id) {
        try {
            $instanceDocLog = new DocLog;
            PurchaseJobOrder::withTrashed()
                            ->where('id', $id)
                            ->restore();
            $instancePO = PurchaseJobOrder::find($id);
            $poNo = $instancePO->po_no;

            $msg = "Purchase/Job Order '$poNo' successfully restored.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }
}
