<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\PurchaseOrder;
use App\UnitIssue;
use App\OrsBurs;
use App\InspectionAcceptance;
use App\DisbursementVoucher;
use App\InventoryStock;
use App\EmployeeLog;
use App\DocumentLogHistory;
use App\PaperSize;
use Carbon\Carbon;
use Auth;
use DB;

class PurchaseJobOrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageLimit = 10;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $prList = DB::table('tblpr as pr')
                    ->select('pr.*', 'proj.project',
                              DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                    ->join('tblpo_jo as po', 'po.pr_id', '=', 'pr.id')
                    ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'pr.requested_by')
                    ->join('tblpr_status AS status', 'status.id', '=', 'pr.status')
                    ->leftJoin('tblprojects AS proj', 'proj.id', '=', 'pr.project_id')
                    ->whereNull('pr.deleted_at')
                    ->where('status.id', '>', 5);

        if (!empty($search)) {
            $prList = $prList->where(function ($query) use ($search) {
                                   $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('po.po_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.date_pr', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.code', 'LIKE', '%' . $search . '%')
                                         ->orWhere('po.code', 'LIKE', '%' . $search . '%');
                               });
        }

        if (Auth::user()->role == 3 || Auth::user()->role == 4 || Auth::user()->role == 6) {
            $prList = $prList->where('requested_by', Auth::user()->emp_id);
        }

        if (Auth::user()->role == 5) {
            $prList = $prList->where('emp.division_id', Auth::user()->division_id);
        }

        $prList = $prList->orderBy('pr.id', 'desc')
                         ->distinct()
                         ->paginate($pageLimit, ['pr.id']);

        foreach ($prList as $list) {
            //$type = "po";
            $poItem = [];
            $suppliers = DB::table('tblpo_jo as po')
                           ->select('po.awarded_to', 'bidder.company_name', 'po.po_no', 'po.for_approval',
                                    'status.id AS sID', 'po.document_abrv', 'po.date_po_approved',
                                    'po.date_accountant_signed', 'po.code', 'po.with_ors_burs',
                                    'po.date_cancelled', 'po.date_po', 'po.created_at')
                           ->join('tblsuppliers as bidder', 'bidder.id', '=', 'po.awarded_to')
                           ->join('tblpr_status AS status', 'status.id', '=', 'po.status')
                           ->where([['po.pr_id', $list->id], ['po.awarded_to', '<>', 0]])
                           ->whereNotNull('po.awarded_to')
                           ->whereNull('po.deleted_at')
                           ->orderBy('po.po_no');

            if (!empty($search)) {
                $suppliers = $suppliers->where('po.po_no', 'LIKE', '%' . $search . '%')
                                       ->orWhere('po.code', 'LIKE', '%' . $search . '%');
            }

            $suppliers = $suppliers->get();

            foreach ($suppliers as $bid) {
                $ors = OrsBurs::where('po_no', $bid->po_no)->first();
                $orsDateIssued = "";
                $prItemCount = DB::table('tblpr_items')
                                 ->where([['pr_id', $list->id], ['awarded_to', $bid->awarded_to]])
                                 ->count();

                if (!empty($ors->date_issued)) {
                    $orsDateIssued = $ors->date_issued;
                } else {
                    $orsDateIssued = NULL;
                }

                $type = strtolower($bid->document_abrv);
                $logshist = $this->checkDocStatus($bid->code);
                $poItem[] = (object)['po_no' => $bid->po_no,
                                     'date_po' => $bid->date_po,
                                     'date_issued' => $logshist->date_issued,
                                     'date_received' => $logshist->date_received,
                                     'awarded_to' => $bid->awarded_to,
                                     'company_name' => $bid->company_name,
                                     'type' => $type,
                                     'status_id' => $bid->sID,
                                     'ors_date_issue' => $orsDateIssued,
                                     'for_approval' => $bid->for_approval,
                                     'with_ors_burs' => $bid->with_ors_burs,
                                     'date_po_approved' => $bid->date_po_approved,
                                     'date_accountant_signed' => $bid->date_accountant_signed,
                                     'item_count' => $prItemCount,
                                     'date_cancelled' => $bid->date_cancelled,
                                     'created_at' => $bid->created_at];
            }

            $list->po_item = $poItem;
        }

        return view('pages.po-jo', ['search' => $search,
                                    'list' => $prList,
                                    'pageLimit' => $pageLimit,
                                    'paperSizes' => $paperSizes]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $poNo) {
        $toggle = $request->toggle;
        $poList = DB::table('tblpo_jo as po')
                    ->select('po.*', 'bid.id as sID', 'bid.company_name', 'bid.address', 'bid.vat_no as tin')
                    ->join('tblsuppliers as bid', 'bid.id', '=', 'po.awarded_to')
                    ->where('po.po_no', $poNo)
                    ->first();
        $poJoNumbers = DB::table('tblpo_jo')->where('pr_id', $poList->pr_id)
                                    ->select('po_no')
                                    ->orderByRaw('LENGTH(po_no)')
                                    ->orderBy('po_no')
                                    ->get();
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.po_jo_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.po_jo', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();
        $unitIssue = UnitIssue::all();
        $countItems = DB::table('tblpo_jo_items as po')
                        ->join('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                        ->where('po.po_no', $poNo)
                        ->count();

        if ($countItems > 0) {
            $poItems = DB::table('tblpo_jo_items as po')
                         ->join('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                         ->where('po.po_no', $poNo)
                         ->orderByRaw('LENGTH(po.item_id)')
                         ->orderBy('po.item_id')
                         ->get();
        } else {
            $poItems = [];
            /*
            $poItems = DB::table('tblpr_items AS itm')
                         ->select('itm.*', 'abs.unit_cost', 'abs.total_cost')
                         ->join('tblabstract_items AS abs', 'abs.pr_item_id', '=', 'itm.item_id')
                         ->join('tblunit_issue AS unit', 'unit.id', '=', 'itm.unit_issue')
                         ->where([['itm.pr_id', $poList->pr_id],
                                  ['itm.awarded_to', $poList->awarded_to],
                                  ['abs.supplier_id', $poList->awarded_to],
                                  ['itm.document_type', $toggle]])
                         ->orderByRaw('LENGTH(itm.item_id)')
                         ->orderBy('itm.item_id')
                         ->get();*/
        }

        if ($toggle == 'po') {
            return view('pages.create-po', ['po' => $poList,
                                            'poJoNumbers' => $poJoNumbers,
                                            'signatories' => $signatories,
                                            'poItems' => $poItems,
                                            'grandTotal' => 0,
                                            'unitIssue' => $unitIssue]);
        } else if ($toggle == 'jo') {
            return view('pages.create-jo', ['jo' => $poList,
                                            'poJoNumbers' => $poJoNumbers,
                                            'signatories' => $signatories,
                                            'joItems' => $poItems,
                                            'grandTotal' => 0,
                                            'unitIssue' => $unitIssue]);
        }
    }

    public function showIssuedTo($poNo) {
        $issuedTo = User::orderBy('firstname')->get();
        $po = DB::table('tblpo_jo')->where('po_no', $poNo)->first();

        return view('pages.view-po-jo-issue', ['key' => $poNo,
                                               'issuedTo' => $issuedTo,
                                               'type' => $po->document_abrv]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $poNo)
    {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $ors = OrsBurs::where('po_no', $poNo)->first();
        $iar = InspectionAcceptance::where('iar_no', 'LIKE', "%$poNo%")->first();
        $invStocks = InventoryStock::where('po_no', $poNo)
                                    ->orWhere('inventory_no', 'LIKE', "%$poNo%")
                                    ->get();
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            $prID = $request->pr_id;
            $toggle = $request->type;
            $poNoNew = $request->po_no;
            $datePO = $request->date_po;
            $placeDelivery = $request->place_delivery;
            $deliveryTerm = $request->delivery_term;
            $dateDelivery = $request->date_delivery;
            $paymentTerm = $request->payment_term;
            $amountWords = $request->amount_words;
            $sigFundsAvailable = $request->sig_funds_available;
            $sigApproval = $request->sig_approval;
            $sigDepartment = $request->sig_department;
            $grandTotal = $request->grand_total;

            $array_ItemID = $request->item_id;
            $array_UnitIssue = $request->unit;
            $array_ItemDescription = $request->item_description;
            $array_Quantity = $request->quantity;
            $array_UnitCost = $request->unit_cost;
            $array_TotalCost = $request->total_cost;
            $array_PO_JO_Number = $request->po_jo_no;
            $array_Exclude = $request->exclude;

            if ($poNo != $poNoNew) {
                $poCountValdiation = DB::table('tblpo_jo')
                                    ->where('po_no', $poNoNew)
                                    ->count();

                if (is_array($array_PO_JO_Number) && count($array_PO_JO_Number) > 0) {
                    foreach ($array_PO_JO_Number as $ctrKey => $poNoTempDat) {
                        if ($poNoTempDat == $poNo) {
                            $array_PO_JO_Number[$ctrKey] = $poNoNew;
                        }
                    }
                }

                if ($poCountValdiation) {
                    $msg = "There is an existing $poNoNew in the PO/JO list.";
                    return redirect(url()->previous())->with('failed', $msg);
                }
            }

            $countItems = DB::table('tblpo_jo_items as po')
                            ->join('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                            ->where('po.po_no', $poNo)
                            ->count();

            $po->po_no = $poNoNew;
            $po->date_po = $datePO;
            $po->place_delivery = $placeDelivery;
            $po->date_delivery = $dateDelivery;
            $po->payment_term = $paymentTerm;
            $po->amount_words = $amountWords;
            $po->grand_total = $grandTotal;
            $po->sig_funds_available = $sigFundsAvailable;
            $po->sig_approval = $sigApproval;

            if ($toggle == 'po') {
                $po->delivery_term = $deliveryTerm;
                $po->save();

                DB::table('tblpo_jo')
                  ->where('po_no', $poNo)
                  ->update(['po_no' => $poNoNew]);

                if (is_array($array_ItemID) && count($array_ItemID) > 0) {
                    foreach ($array_ItemID as $key => $itemID) {
                        $inv = InventoryStock::where([['po_no', $poNo],
                                                    ['po_item_id', $itemID]])->first();
                        $totalCost = $array_Quantity[$key] * $array_UnitCost[$key];

                        if ($countItems > 0) {
                            DB::table('tblpo_jo_items')
                            ->where('item_id', $itemID)
                            ->where('po_no', $poNo)
                            ->where('pr_id', $prID)
                            ->update(['quantity' => $array_Quantity[$key],
                                        'unit_issue' => $array_UnitIssue[$key],
                                        'item_description' => $array_ItemDescription[$key],
                                        'unit_cost' => $array_UnitCost[$key],
                                        'total_cost' => $totalCost,
                                        'po_no' => $array_PO_JO_Number[$key],
                                        'excluded' => $array_Exclude[$key]]);
                        } else {
                            $hasItem = DB::table('tblpo_jo_items')
                                        ->where('item_id', $itemID)
                                        ->first();
                            if (!$hasItem) {
                                DB::table('tblpo_jo_items')->insert([
                                    ['item_id' => $itemID,
                                    'po_no' => $poNo,
                                    'pr_id' => $prID,
                                    'quantity' => $array_Quantity[$key],
                                    'unit_issue' => $array_UnitIssue[$key],
                                    'item_description' => $array_ItemDescription[$key],
                                    'unit_cost' => $array_UnitCost[$key],
                                    'total_cost' => $totalCost,
                                    'po_no' => $array_PO_JO_Number[$key],
                                    'excluded' => $array_Exclude[$key]]
                                ]);
                            }
                        }

                        if ($inv) {
                            $oldPO_No = $inv->po_no;
                            $inventoryNo = str_replace($oldPO_No, $array_PO_JO_Number[$key],
                                                    $inv->inventory_no);
                            $inv->po_no = $array_PO_JO_Number[$key];
                            $inv->inventory_no = $inventoryNo;
                            $inv->save();
                        }
                    }
                }
            } else if ($toggle == 'jo') {
                $po->sig_department = $sigDepartment;
                $po->save();

                DB::table('tblpo_jo')
                  ->where('po_no', $poNo)
                  ->update(['po_no' => $poNoNew]);

                if (is_array($array_ItemID) && count($array_ItemID) > 0) {
                    foreach ($array_ItemID as $key => $itemID) {
                        $inv = InventoryStock::where([['po_no', $poNo],
                                                    ['po_item_id', $itemID]])->first();
                        $unitCost = $array_TotalCost[$key] / $array_Quantity[$key];
                        $unitCost = (float)$unitCost;

                        if ($countItems > 0) {
                            DB::table('tblpo_jo_items')
                            ->where('item_id', $itemID)
                            ->where('po_no', $poNo)
                            ->where('pr_id', $prID)
                            ->update(['quantity' => $array_Quantity[$key],
                                        'unit_issue' => $array_UnitIssue[$key],
                                        'item_description' => $array_ItemDescription[$key],
                                        'unit_cost' => $unitCost,
                                        'total_cost' => $array_TotalCost[$key],
                                        'po_no' => $array_PO_JO_Number[$key],
                                        'excluded' => $array_Exclude[$key]]);
                        } else {
                            $hasItem = DB::table('tblpo_jo_items')
                                        ->where('item_id', $itemID)
                                        ->first();
                            if (!$hasItem) {
                                DB::table('tblpo_jo_items')->insert([
                                    ['item_id' => $itemID,
                                    'po_no' => $poNo,
                                    'pr_id' => $prID,
                                    'quantity' => $array_Quantity[$key],
                                    'unit_issue' => $array_UnitIssue[$key],
                                    'item_description' => $array_ItemDescription[$key],
                                    'unit_cost' => $unitCost,
                                    'total_cost' => $array_TotalCost[$key],
                                    'po_no' => $array_PO_JO_Number[$key],
                                    'excluded' => $array_Exclude[$key]]
                                ]);
                            }
                        }

                        if ($inv) {
                            $oldPO_No = $inv->po_no;
                            $inventoryNo = str_replace($oldPO_No, $array_PO_JO_Number[$key],
                                                    $inv->inventory_no);
                            $inv->po_no = $array_PO_JO_Number[$key];
                            $inv->inventory_no = $inventoryNo;
                            $inv->save();
                        }
                    }
                }
            }

            if ($poNo != $poNoNew) {
                if ($ors) {
                    $ors->po_no = $poNoNew;
                }

                if ($iar) {
                    DB::table('tbliar')
                      ->where('iar_no', 'LIKE', "%$poNo%")
                      ->update(['iar_no' => "IAR-$poNoNew"]);
                }

                if (count($invStocks) > 0) {
                    foreach ($invStocks as $_stock) {
                        $inventoryNoNew = str_replace($poNo, $poNoNew, $_stock->inventory_no);

                        DB::table('tblinventory_stocks')
                          ->where('po_no', $poNo)
                          ->orWhere('inventory_no', 'LIKE', "%$poNo%")
                          ->update([
                              'po_no' => $poNoNew,
                              'inventory_no' => $inventoryNoNew
                            ]);
                    }
                }
            }

            if ($ors) {
                $ors->amount = $grandTotal;
                $ors->save();
            }

            $logEmpMessage = "updated the " . strtolower($docName) . " $poNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "$docName $poNo successfully updated.";
            return redirect(url('procurement/po-jo?search=' . $poNoNew))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the $docName $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function createORS_BURS($poNo) {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $countORS = DB::table('tblors_burs')->where('po_no', $poNo)->count();

        try {
            if ($countORS == 0) {
                if ($po->grand_total > 0) {
                    $ors = new OrsBurs;
                    $ors->pr_id = $po->pr_id;
                    $ors->po_no = $poNo;
                    $ors->responsibility_center = "19 001 03000 14";
                    $ors->particulars = "To obligate...";
                    $ors->mfo_pap = "3-Regional Office\nA.III.c.1\nA.III.b.1\nA.III.c.2";
                    $ors->payee = $po->awarded_to;
                    $ors->amount = $po->grand_total;
                    $ors->module_class_id = 3;
                    $ors->code = $this->generateTrackerCode('ORS', $poNo, 3);
                    $ors->save();

                    $po->for_approval = 'y';
                    $po->with_ors_burs = 'y';
                    $po->save();

                    $logEmpMessage = "created the obligation report status for $poNo.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "Successfully created the Obligation Report Status document for $poNo.";
                    return redirect(url('procurement/ors-burs?search='.$poNo))->with('success', $msg);
                } else {
                    $msg = "Please edit first the Purchase/Job Order for $poNo.";
                    return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
                }
            } else {
                $ors = DB::table('tblors_burs')
                         ->where('po_no', $poNo)
                         ->first();

                if (!empty($ors->deleted_at)) {
                    /*
                    $ors->payee = $po->awarded_to;
                    $ors->amount = $po->grand_total;
                    $ors->save();*/

                    DB::table('tblors_burs')
                      ->where('po_no', $poNo)
                      ->update([
                          'payee' => $po->awarded_to,
                          'amount' => $po->grand_total,
                          'updated_at' => Carbon::now()
                      ]);
                    OrsBurs::where('po_no', $poNo)->restore();

                    $po->for_approval = 'y';
                    $po->with_ors_burs = 'y';
                    $po->save();

                    $msg = "Successfully created the Obligation Report Status document for $poNo.";
                    return redirect(url('procurement/ors-burs?search='.$poNo))->with('success', $msg);
                } else {
                    $msg = "The Obligation/Budget Utilization Report Status for $poNo is already created.";
                    return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
                }
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered creating the Obligation Report Status for $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function accountantSigned($poNo) {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $isDocGenerated = $this->checkDocGenerated($po->code);
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            if ($isDocGenerated) {
                $po->date_accountant_signed = date('Y-m-d H:i:s');
                $po->save();

                $logEmpMessage = "set the " . strtolower($docName) . " $poNo to cleared/signed by accountant.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "$docName $poNo is now set to to cleared/signed by accountant.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('success', $msg);
            } else {
                $msg = "Generate first the " . strtolower($docName) . " $poNo document.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered approving the $docName $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function approve($poNo) {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $isDocGenerated = $this->checkDocGenerated($po->code);
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            if ($isDocGenerated) {
                $po->date_po_approved = date('Y-m-d H:i:s');
                $po->save();

                $logEmpMessage = "set the " . strtolower($docName) . " $poNo to approved.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "$docName $poNo is now set to approved.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('success', $msg);
            } else {
                $msg = "Generate first the " . strtolower($docName) . " $poNo document.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered approving the $docName $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function issue(Request $request, $poNo) {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $remarks = $request['remarks'];
        $issuedTo = $request['issued_to'];
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            $code = $po->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $docStatus = $this->checkDocStatus($code);

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $this->logTrackerHistory($code, Auth::user()->emp_id, $issuedTo, "issued", $remarks);

                    $logEmpMessage = "issued the " . strtolower($docName) . " $poNo.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "$docName $poNo is now set to issued.";
                    return redirect(url('procurement/po-jo?search=' . $poNo))->with('success', $msg);
                } else {
                    $msg = "Generate first the " . strtolower($docName) . " $poNo document.";
                    return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
                }
            } else {
                $msg = "$docName $poNo already issued.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered issuing the $docName $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function receive($poNo) {
        $po = DB::table('tblpo_jo')->where('po_no', $poNo)->first();
        $code = $po->code;
        $docStatus = $this->checkDocStatus($code);
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            if (!empty($docStatus->date_issued)) {
                if (empty($docStatus->date_received)) {
                    $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "received");

                    $logEmpMessage = "received the " . strtolower($docName) . " $poNo.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "$docName $poNo is now set to received.";
                    return redirect(url('procurement/po-jo?search=' . $poNo))->with('success', $msg);
                } else {
                    $msg = "$docName $poNo is already received.";
                    return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
                }
            } else {
                $msg = "You should issue the " . strtolower($docName) . " $poNo first.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered issuing the $docName $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function cancel($poNo) {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            if ($po) {
                $po->date_cancelled = date('Y-m-d H:i:s');
                $po->status = 3;
                $po->save();

                $logEmpMessage = "cancelled the " . strtolower($docName) . " $poNo.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "$docName $poNo is now set to cancelled.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('success', $msg);
            } else {
                $msg = "There is an error cancelling $docName $poNo.";
                return redirect(url()->previous())->with('failed', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered cancelling the $docName $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function unCancel($poNo) {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $ors = OrsBurs::where('po_no', $poNo)->first();
        $iar = InspectionAcceptance::where('iar_no', 'LIKE', '%'.$poNo.'%')
                                   ->first();
        $dv = DisbursementVoucher::where('ors_id', $ors)->first();
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            if ($po) {
                $po->date_cancelled = NULL;
                $po->status = 6;

                if ($ors) {
                    if (!empty($ors->date_obligated)) {
                        $po->status = 7;
                    }
                }

                if ($iar) {
                    $po->status = 9;
                }

                if ($dv) {
                    $po->status = 10;
                }

                $po->save();

                $logEmpMessage = "uncancelled the " . strtolower($docName) . " $poNo.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "$docName $poNo is now uncancelled.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('success', $msg);
            } else {
                $msg = "There is an error uncancelling $docName $poNo.";
                return redirect(url()->previous())->with('failed', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered uncancelling the $docName $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function delivery(Request $request, $poNo) {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            if ($po->status == 7) {
                $po->status = 8;
                $po->save();

                $logEmpMessage = "set the " . strtolower($docName) . " $poNo to for delivery.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "$docName $poNo is ready for delivery.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('success', $msg);
            } else {
                $msg = "You must obligate the Obligation/Budget Utilization Report Status of $poNo first.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered setting the $docName $poNo to 'For Delivery'.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function inspection(Request $request, $poNo) {
        $po = PurchaseOrder::where('po_no', $poNo)->first();
        $ors = OrsBurs::where('po_no', $poNo)->first();
        $docName = $this->getDocumentName($po->document_abrv);

        try {
            if ($po->status == 8) {
                $newIAR_No = "IAR-" . $poNo;
                $iar = new InspectionAcceptance;
                $iar->iar_no = $newIAR_No;
                $iar->pr_id = $ors->pr_id;
                $iar->ors_id = $ors->id;
                $iar->code = $this->generateTrackerCode('IAR', $poNo, 3);
                $iar->save();

                $po->status = 9;
                $po->save();

                $logEmpMessage = "set the " . strtolower($docName) . " $poNo to for inspection.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "$docName $poNo is ready for inspection.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('success', $msg);
            } else {
                $msg = "You must obligate the Obligation/Budget Utilization Report Status of $poNo first.";
                return redirect(url('procurement/po-jo?search=' . $poNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered receiving the $docName $poNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    private function getDocumentName($docName) {
        if ($docName == 'PO') {
            $documentName = "Purchase Order";
        } else if ($docName == 'JO') {
            $documentName = "Job Order";
        } else {
            $documentName = "Purchase/Job Order";
        }

        return $documentName;
    }

    private function checkDocGenerated($code) {
        $logs = DB::table('tbldocument_logs_history')
                  ->where([
                        ['code', $code],
                        ['action', 'document_generated']
                    ])
                  ->orderBy('logshist.created_at', 'desc')
                  ->count();

        return $logs;
    }

    private function checkDocStatus($code) {
        $logs = DB::table('tbldocument_logs_history')
                  ->where('code', $code)
                  ->orderBy('created_at', 'desc')
                  ->get();
        $currentStatus = (object) ["issued_by" => NULL,
                                   "date_issued" => NULL,
                                   "received_by" => NULL,
                                   "date_received" => NULL,
                                   "issued_back_by" => NULL,
                                   "date_issued_back" => NULL,
                                   "received_back_by" => NULL,
                                   "date_received_back" => NULL];

        if (count($logs) > 0) {
            foreach ($logs as $log) {
                if ($log->action != "-") {
                    switch ($log->action) {
                        case 'issued':
                            $currentStatus->issued_by = $log->action;
                            $currentStatus->date_issued = $log->date;
                            break;

                        case 'received':
                            $currentStatus->received_by = $log->action;
                            $currentStatus->date_received = $log->date;
                            break;

                        case 'issued_back':
                            $currentStatus->issued_back_by = $log->action;
                            $currentStatus->date_issued_back = $log->date;
                            break;

                        case 'received_back':
                            $currentStatus->received_back_by = $log->action;
                            $currentStatus->date_received_back = $log->date;
                            break;

                        default:
                            # code...
                            break;
                    }
                } else {
                    break;
                }
            }
        }

        return $currentStatus;
    }

    private function generateTrackerCode($modAbbr, $pKey, $modClass) {
        $modAbbr = strtoupper($modAbbr);
        $pKey = strtoupper($pKey);

        return $modAbbr . "-" . $pKey . "-" . $modClass . "-" . date('mdY');
    }

    private function logEmployeeHistory($msg, $emp = "") {
        $empLog = new EmployeeLog;
        $empLog->emp_id = empty($emp) ? Auth::user()->emp_id: $emp;
        $empLog->message = $msg;
        $empLog->save();
    }

    private function logTrackerHistory($code, $empFrom, $empTo, $action, $remarks = "") {
        $docHistory = new DocumentLogHistory;
        $docHistory->code = $code;
        $docHistory->date = Carbon::now();
        $docHistory->emp_from = $empFrom;
        $docHistory->emp_to = $empTo;
        $docHistory->action = $action;
        $docHistory->remarks = $remarks;
        $docHistory->save();
    }
}
