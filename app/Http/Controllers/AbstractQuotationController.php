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

use App\Models\EmpAccount as User;
use App\Models\EmpUnit;
use App\Models\FundingProject;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\ItemUnitIssue;
use App\Models\Signatory;
use App\Models\ProcurementMode;
use Carbon\Carbon;
use DB;
use Auth;

use App\Plugins\Notification as Notif;

class AbstractQuotationController extends Controller
{
    protected $poLetters = [
        'A','B','C','D','E','F','G','H','I','J','K','L','M',
        'N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'proc_abstract';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedApprove = Auth::user()->getModuleAccess($module, 'approve_po_jo');
        $isAllowedRFQ = Auth::user()->getModuleAccess('proc_rfq', 'is_allowed');
        $isAllowedPO = Auth::user()->getModuleAccess('proc_po_jo', 'is_allowed');

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
        $absData = PurchaseRequest::with(['funding', 'requestor', 'rfq'])
                                  ->whereHas('abstract', function($query) {
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

                $absData = $absData->whereIn('requested_by', $userIDs);
            } else {
                $empDivisionAccess = [Auth::user()->division];
                $absData = $absData->where('requested_by', Auth::user()->id);
            }
        } else {
            if ($roleHasPSTD) {
                $empDivisionAccess = [Auth::user()->division];
            } else {
                $empDivisionAccess = Auth::user()->getDivisionAccess();
            }
        }

        $absData = $absData->whereHas('division', function($query)
                use($empDivisionAccess) {
            $query->whereIn('id', $empDivisionAccess);
        });

        if (!empty($keyword)) {
            $absData = $absData->where(function($qry) use ($keyword) {
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
                    })->orWhereHas('abstract', function($query) use ($keyword) {
                        $query->where('date_abstract', 'like', "%$keyword%")
                              ->orWhere('id', 'like', "%$keyword%");
                    });
            });
        }

        $absData = $absData->sortable(['pr_no' => 'desc'])->paginate(20);

        foreach ($absData as $abs) {
            $toggle = "store";
            $countItems = AbstractQuotationItem::where('abstract_id', $abs->abstract['id'])
                                               ->count();

            if ($countItems > 0) {
                $toggle = "update";
            }

            $abs->toggle = $toggle;
        }

        return view('modules.procurement.abstract.index', [
            'list' => $absData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedApprove' => $isAllowedApprove,
            'isAllowedRFQ' => $isAllowedRFQ,
            'isAllowedPO' => $isAllowedPO,
            'roleHasOrdinary' => $roleHasOrdinary,
            'roleHasBudget' => $roleHasBudget,
            'roleHasAccountant' => $roleHasAccountant
        ]);
    }

    /**
     * Display a item segment of the resource.
     *
     * @return \Illuminate\Http\Response
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showItemSegment(Request $request, $id) {
        $instanceAbstract = AbstractQuotation::with('pr')->find($id);
        $prID = $instanceAbstract->pr_id;
        $supplierList = Supplier::orderBy('company_name')->get();
        $bidderCount = $request->bidder_count;
        $groupKey = $request->group_key;
        $groupNo = $request->group_no;
        $items = $this->getAbstractTable($prID, $groupNo, 'single');

        return view('modules.procurement.abstract.item-segment', [
            'abstractItems' => $items,
            'supplierList' => $supplierList,
            'bidderCount' => $bidderCount,
            'groupKey' => $groupKey,
            'counter' => 1,
            'currentFirstItemNo' => 1,
            'currentLastItemNo' => 0,
            'totalItemCount' => 0,
            'pages' => [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCreate($id) {
        $canSetModeProc = Auth::user()->getModuleAccess('proc_abstract', 'set_mode_proc');
        $instanceAbstract = AbstractQuotation::with('pr')->find($id);
        $prID = $instanceAbstract->pr_id;
        $items = $this->getAbstractTable($prID);
        $suppliers = Supplier::orderBy('company_name')->get();
        $procurementModes = ProcurementMode::orderBy('mode_name')->get();
        $users = User::where('is_active', 'y')
                     ->orderBy('firstname')->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.procurement.abstract.create', [
            'id' => $id,
            'suppliers' => $suppliers,
            'procurementModes' => $procurementModes,
            'users' => $users,
            'signatories' => $signatories,
            'abstractItems' => $items,
            'canSetModeProc' => $canSetModeProc,
        ]);
    }

    /**
     * Store/Update resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  string  $toggle
     * @return \Illuminate\Http\Response
     */
    private function storeUpdateAbstract($request, $id, $toggle) {
        $sigChairperson = $request->sig_chairperson;
        $sigViceChairperson = $request->sig_vice_chairperson;
        $sigFirstMember = $request->sig_first_member;
        $sigSecondMember = $request->sig_second_member;
        $sigThirdMember = $request->sig_third_member;
        $endUser = $request->sig_end_user;
        $abstractDate = $request->date_abstract;
        $modeProcurement = $request->mode_procurement;

        $sigSecondMember = !empty($sigSecondMember) ? $sigSecondMember : NULL;
        $sigThirdMember = !empty($sigThirdMember) ? $sigThirdMember : NULL;

        try {
            $instanceAbstract = AbstractQuotation::with('pr')->find($id);
            $prData = PurchaseRequest::find($instanceAbstract->pr_id);
            $prNo = $prData->pr_no;
            $instanceAbstract->sig_chairperson = $sigChairperson;
            $instanceAbstract->sig_vice_chairperson = $sigViceChairperson;
            $instanceAbstract->sig_first_member = $sigFirstMember;
            $instanceAbstract->sig_second_member = $sigSecondMember;
            $instanceAbstract->sig_third_member = $sigThirdMember;
            $instanceAbstract->sig_end_user = $endUser;
            $instanceAbstract->date_abstract = $abstractDate;
            $instanceAbstract->mode_procurement = $modeProcurement;
            $instanceAbstract->save();

            if ($toggle == 'store') {
                $msg = "Abstract of Quotation for quotation number '$prNo' successfully created.";
            } else {
                $msg = "Abstract of Quotation for quotation number '$prNo' successfully updated.";
            }

            return (object) [
                'msg' => $msg,
                'alert_type' => 'success'
            ];
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";

            return (object) [
                'msg' => $msg,
                'alert_type' => 'failed'
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id) {
        $response = $this->storeUpdateAbstract($request, $id, 'store');
        Auth::user()->log($request, $response->msg);
        return redirect()->route('abstract', ['keyword' => $id])
                         ->with($response->alert_type, $response->msg);
    }

    /**
     * Store a newly created items in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeItems(Request $request, $id) {
        $json = json_decode($request->json_data);
        $bidderCount = (int) $json->bidder_count;

        if ($bidderCount > 0) {
            $prIDItem = $json->pr_item_id;

            $selectedSuppliers = json_decode($json->select_suppliers);

            $unitCosts = json_decode($json->unit_costs);
            $totalCosts = json_decode($json->total_costs);
            $specifications = json_decode($json->specifications);
            $remarks = json_decode($json->remarks);

            $awardedTo = $json->awarded_to;
            $documentType = $json->document_type;
            $awardedRemark = $json->awarded_remark;
            $instanceAbstract = AbstractQuotation::with('pr')->find($id);
            $prID = $instanceAbstract->pr_id;

            foreach ($selectedSuppliers as $selectedCtr => $selectedsuplier) {
                $selectedSuplier = $selectedsuplier->selected_supplier;

                foreach ($unitCosts as $columnCtr => $unitcost) {
                    if ($columnCtr == $selectedCtr) {
                        $unitCost = $unitcost->unit_cost;
                        $totalCost = $totalCosts[$columnCtr]->total_cost;
                        $specification = $specifications[$columnCtr]->specification;
                        $remark = $remarks[$columnCtr]->remarks;

                        $instanceAbsItem = new AbstractQuotationItem;
                        $instanceAbsItem->abstract_id = $id;
                        $instanceAbsItem->pr_id = $prID;
                        $instanceAbsItem->pr_item_id = $prIDItem;
                        $instanceAbsItem->supplier = $selectedSuplier;
                        $instanceAbsItem->specification = $specification;
                        $instanceAbsItem->remarks = $remark;
                        $instanceAbsItem->unit_cost = $unitCost;
                        $instanceAbsItem->total_cost = $totalCost;
                        $instanceAbsItem->save();
                    }
                }
            }

            $instancePRItem = PurchaseRequestItem::find($prIDItem);
            $instancePRItem->awarded_to = $awardedTo ? $awardedTo : NULL;
            $instancePRItem->document_type = $documentType;
            $instancePRItem->awarded_remarks = $awardedRemark;
            $instancePRItem->save();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $canSetModeProc = Auth::user()->getModuleAccess('proc_abstract', 'set_mode_proc');
        $instanceAbstract = AbstractQuotation::with('pr')->find($id);
        $prID = $instanceAbstract->pr_id;
        $abstractDate = $instanceAbstract->date_abstract;
        $procurementMode = $instanceAbstract->mode_procurement;
        $chairperson = $instanceAbstract->sig_chairperson;
        $viceChairperson = $instanceAbstract->sig_vice_chairperson;
        $firstMember = $instanceAbstract->sig_first_member;
        $secondMember = $instanceAbstract->sig_second_member;
        $thirdMember = $instanceAbstract->sig_third_member;
        $endUser = $instanceAbstract->sig_end_user;
        $items = $this->getAbstractTable($prID);
        $suppliers = Supplier::orderBy('company_name')->get();
        $procurementModes = ProcurementMode::orderBy('mode_name')->get();
        $users = User::where('is_active', 'y')
                     ->orderBy('firstname')->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.procurement.abstract.update', [
            'id' => $id,
            'suppliers' => $suppliers,
            'procurementModes' => $procurementModes,
            'users' => $users,
            'signatories' => $signatories,
            'abstractItems' => $items,
            'abstractDate' => $abstractDate,
            'procurementMode' => $procurementMode,
            'chairperson' => $chairperson,
            'viceChairperson' => $viceChairperson,
            'firstMember' => $firstMember,
            'secondMember' => $secondMember,
            'thirdMember' => $thirdMember,
            'endUser' => $endUser,
            'canSetModeProc' => $canSetModeProc,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $response = $this->storeUpdateAbstract($request, $id, 'update');
        Auth::user()->log($request, $response->msg);
        return redirect()->route('abstract', ['keyword' => $id])
                         ->with($response->alert_type, $response->msg);
    }

    /**
     * Update the specified items resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateItems(Request $request, $id) {
        $json = json_decode($request->json_data);
        $bidderCount = (int) $json->bidder_count;

        if ($bidderCount > 0) {
            $prIDItem = $json->pr_item_id;

            $selectedSuppliers = json_decode($json->select_suppliers);

            $abstractItemIDs = json_decode($json->abstract_item_ids);
            $unitCosts = json_decode($json->unit_costs);
            $totalCosts = json_decode($json->total_costs);
            $specifications = json_decode($json->specifications);
            $remarks = json_decode($json->remarks);

            $awardedTo = $json->awarded_to;
            $documentType = $json->document_type;
            $awardedRemark = $json->awarded_remark;

            $instanceAbstract = AbstractQuotation::with('pr')->find($id);
            $prID = $instanceAbstract->pr_id;
            $instancePR = PurchaseRequest::find($prID);
            $prNo = $instancePR->pr_no;

            $groupNo = $this->getGroupNo($prIDItem);
            $oldBidderCount = $this->getBidderCount($groupNo, $prID, 'this', $prIDItem);

            foreach ($selectedSuppliers as $selectedCtr => $selectedsuplier) {
                $selectedSuplier = $selectedsuplier->selected_supplier;

                foreach ($unitCosts as $columnCtr => $unitcost) {
                    if ($columnCtr == $selectedCtr) {
                        $unitCost = $unitcost->unit_cost;
                        $totalCost = $totalCosts[$columnCtr]->total_cost;
                        $specification = $specifications[$columnCtr]->specification;
                        $remark = $remarks[$columnCtr]->remarks;

                        if ($bidderCount == $oldBidderCount) {
                            if (isset($abstractItemIDs[$columnCtr])) {
                                $absItemID = $abstractItemIDs[$columnCtr]->abs_item_id;
                                $instanceAbsItem = AbstractQuotationItem::find($absItemID);
                            } else {
                                $instanceAbsItem = new AbstractQuotationItem;
                                $instanceAbsItem->abstract_id = $id;
                                $instanceAbsItem->pr_id = $prID;
                                $instanceAbsItem->pr_item_id = $prIDItem;
                            }
                        } else {
                            if ($columnCtr == 0) {
                                AbstractQuotationItem::where('pr_item_id', $prIDItem)->delete();
                            }

                            $instanceAbsItem = new AbstractQuotationItem;
                            $instanceAbsItem->abstract_id = $id;
                            $instanceAbsItem->pr_id = $prID;
                            $instanceAbsItem->pr_item_id = $prIDItem;
                        }

                        $instanceAbsItem->supplier = $selectedSuplier;
                        $instanceAbsItem->specification = $specification;
                        $instanceAbsItem->remarks = $remark;
                        $instanceAbsItem->unit_cost = $unitCost;
                        $instanceAbsItem->total_cost = $totalCost;
                        $instanceAbsItem->save();
                    }
                }
            }

            $instancePRItem = PurchaseRequestItem::find($prIDItem);
            $poCount = PurchaseJobOrder::where('pr_id', $prID)
                                       ->withTrashed()
                                       ->count();

            if ($poCount > 0 && $instancePR->status >= 6) {
                $instancePOItem = PurchaseJobOrderItem::with('po')
                                                      ->where('pr_item_id', $prIDItem)
                                                      ->first();

                if ($instancePOItem) {
                    if (!empty($awardedTo)) {
                        if ($awardedTo != $instancePOItem->po->awarded_to ||
                            $documentType != $instancePOItem->po->document_type) {
                            $this->processPOItemData($prID, $prIDItem, $awardedTo, $poCount, $documentType);
                        }
                    } else {
                        PurchaseJobOrderItem::where('pr_item_id', $prIDItem)
                                            ->delete();
                    }
                } else {
                    if (!empty($awardedTo)) {
                        $this->processPOItemData($prID, $prIDItem, $awardedTo, $poCount, $documentType);
                    }
                }
            } else if ($poCount == 0 && $instancePR->status >= 6) {
                $poNo = $prNo.'-'.$this->poLetters[0];

                if ($instancePRItem->awarded_to) {
                    $this->processPOItemData($prID, $prIDItem, $awardedTo, $poCount, $documentType);
                }
            }

            $instancePRItem->awarded_to = $awardedTo ? $awardedTo : NULL;
            $instancePRItem->document_type = $documentType;
            $instancePRItem->awarded_remarks = $awardedRemark;
            $instancePRItem->save();
        }
    }

    private function processPOItemData($prID, $prIDItem, $awardedTo, $poCount, $documentType) {
        $instancePRItem = PurchaseRequestItem::with('pr')->find($prIDItem);
        $instancePR = PurchaseRequest::find($prID);
        $prNo = $instancePR->pr_no;
        $instancePOs = PurchaseJobOrder::where([
            ['pr_id', $prID], ['awarded_to', $awardedTo]
        ])->whereNull('date_po_approved')->get();
        $hasParentPO = false;

        foreach ($instancePOs as $instancePO) {
            $_instancePOItem = PurchaseJobOrderItem::where('po_no', $instancePO->po_no)
                                                   ->first();

            if ($_instancePOItem) {
                $prItemID = $_instancePOItem->pr_item_id;
                $_instancePRItem = PurchaseRequestItem::find($prItemID);

                if ($_instancePRItem->group_no == $instancePRItem->group_no &&
                    $instancePO->document_type == $documentType) {
                    $this->addItemPO(
                        $instancePO->po_no,
                        $prID,
                        $prIDItem,
                        $awardedTo
                    );
                    $hasParentPO = true;
                    break;
                }
            }
        }

        if (!$hasParentPO) {
            $newPONo = "$prNo-".$this->poLetters[$poCount];
            $this->createDocumentPO($newPONo, $prID, $documentType, $awardedTo, []);
            $this->addItemPO(
                $newPONo,
                $prID,
                $prIDItem,
                $awardedTo
            );
        }
    }

    /**
     * Remove the specified items resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteItems(Request $request, $id) {
        try {
            $instanceAbstract = AbstractQuotation::with('pr')->find($id);
            $prData = PurchaseRequest::find($instanceAbstract->pr_id);
            $prNo = $prData->pr_no;
            AbstractQuotationItem::where('abstract_id', $id)->delete();

            $msg = "Abstract of Quotation items for quotation number '$prNo' successfully deleted.";
            Auth::user()->log($request, $msg);
            return redirect()->route('abstract', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('abstract', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }

    /**
     * Approve the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approveForPO(Request $request, $id) {
        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instanceAbstract = AbstractQuotation::with('pr')->find($id);
            $prID = $instanceAbstract->pr_id;
            $instancePR = PurchaseRequest::find($prID);
            $prNo = $instancePR->pr_no;

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);
            $prItemGroups = $this->getGroups($prID);
            $winCounter = 0;

            if ($isDocGenerated) {
                $instanceAbstract->date_abstract_approved = Carbon::now();
                $instanceAbstract->save();

                foreach ($prItemGroups as $groupCtr => $group) {
                    $itemWinner = PurchaseRequestItem::select('awarded_to', 'document_type')
                                                     ->where([
                        ['pr_id', $prID], ['group_no', $group]
                    ])->whereNotNull('awarded_to')->distinct()->get();
                    $winnerCount = $itemWinner->count();

                    if ($winnerCount > 0) {
                        foreach ($itemWinner as $win) {
                            $poNo = "$prNo-".$this->poLetters[$winCounter];
                            $awardedTo = $win->awarded_to;
                            $documentType = $win->document_type;
                            $withPO = DB::table('purchase_job_orders')->where('po_no', $poNo)->first();
                            $instancePRItems = PurchaseRequestItem::where([
                                ['pr_id', $prID],
                                ['awarded_to', $awardedTo],
                                ['group_no', $group]
                            ])->orderBy('item_no')->get();

                            if ($withPO) {
                                /*
                                $instancePO = PurchaseJobOrder::where('po_no', $poNo)->first();
                                $instancePO->document_type = $documentType;
                                $instancePO->awarded_to = $awardedTo;
                                $instancePO->with_ors_burs = 'n';
                                $instancePO->status = 6;
                                $instancePO->save();*/

                                DB::table('purchase_job_orders')
                                  ->where('po_no', $poNo)
                                  ->update([
                                      'document_type' => $documentType,
                                      'awarded_to' => $awardedTo,
                                      'with_ors_burs' => 'n',
                                      'status' => 6,
                                      'updated_at' => Carbon::now()
                                    ]);

                                foreach ($instancePRItems as $item) {
                                    $prItemID = $item->id;
                                    $this->addItemPO($poNo, $prID, $prItemID, $awardedTo);
                                }
                            } else {
                                $this->createDocumentPO($poNo, $prID, $documentType, $awardedTo, $instancePRItems);
                            }

                            $winCounter++;
                        }
                    }
                }

                $instancePR->status = 6;
                $instancePR->save();

                PurchaseJobOrder::withTrashed()->where('pr_id', $prID)->restore();
                $poData = PurchaseJobOrder::withTrashed()->where('pr_id', $prID)->get();

                foreach ($poData as $po) {
                    $instanceDocLog->logDocument($po->id, Auth::user()->id, NULL, '-');
                }

                $instanceNotif->notifyApprovedForPOAbstract($id);

                $msg = "Abstract of Quotation '$prNo' successfully approved for PO/JO.";
                Auth::user()->log($request, $msg);
                return redirect()->route('abstract', ['keyword' => $id])
                                 ->with('success', $msg);
            } else {
                $msg = "Document for Abstract of Quotation '$prNo' should be generated first.";
                Auth::user()->log($request, $msg);
                return redirect()->route('abstract', ['keyword' => $id])
                                 ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('abstract', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }

    private function createDocumentPO($poNo, $prID, $documentType, $awardedTo, $prItems) {
        $instancePO = new PurchaseJobOrder;
        $instancePO->po_no = $poNo;
        $instancePO->pr_id = $prID;
        $instancePO->document_type = $documentType;
        $instancePO->awarded_to = $awardedTo;
        $instancePO->status = 6;
        $instancePO->save();

        foreach ($prItems as $item) {
            $prItemID = $item->id;
            $this->addItemPO($poNo, $prID, $prItemID, $awardedTo);
        }
    }

    private function addItemPO($poNo, $prID, $prItemID, $awardedTo) {
        $instanceAbsItem = AbstractQuotationItem::where([
            ['pr_item_id', $prItemID], ['supplier', $awardedTo]
        ])->first();
        $instancePRItem = PurchaseRequestItem::find($prItemID);
        $itemNo = $instancePRItem->item_no;
        $quantity = $instancePRItem->quantity;
        $itemDescription = $instancePRItem->item_description;
        $unitIssue = $instancePRItem->unit_issue;
        $unitCost = $instanceAbsItem->unit_cost;
        $totalCost = $instanceAbsItem->total_cost;
        $specification = $instanceAbsItem->specification;
        $specification = !empty($specification) ? "($specification)" : '';

        $poItemCount = PurchaseJobOrderItem::where('pr_item_id', $prItemID)
                                           ->count();

        if ($poItemCount > 0) {
            $instancePOItem = PurchaseJobOrderItem::where('pr_item_id', $prItemID)
                                                  ->first();
        } else {
            $instancePOItem = new PurchaseJobOrderItem;
        }

        $instancePOItem->po_no = $poNo;
        $instancePOItem->pr_id = $prID;
        $instancePOItem->pr_item_id = $prItemID;
        $instancePOItem->item_no = $itemNo;
        $instancePOItem->quantity = $quantity;
        $instancePOItem->unit_issue = $unitIssue;
        $instancePOItem->item_description = "$itemDescription $specification";
        $instancePOItem->unit_cost = $unitCost;
        $instancePOItem->total_cost = $totalCost;
        $instancePOItem->save();

    }

    public function getGroups($prID) {
        $data = [];
        $groups = PurchaseRequestItem::select('group_no')
                                     ->where('pr_id', $prID)
                                     ->distinct()
                                     ->orderBy('group_no')
                                     ->get();

        foreach ($groups as $group) {
            $data[] = $group->group_no;
        }

        $_data = array_unique($data);
        $data = [];

        foreach ($_data as $group) {
            $data[] = $group;
        }

        return $data;
    }

    private function getGroupNo($id) {
        $prItem = PurchaseRequestItem::select('group_no')
                                     ->find($id);
        return $prItem->group_no;
    }

    private function getBidderCount($groupNo, $prID, $type = 'all', $optionalParam = '') {
        $bidderCount = 0;

        if ($type == 'all') {
            $itemID = PurchaseRequestItem::select('id')->where([
                ['pr_id', $prID],
                ['group_no', $groupNo]
            ])->first();
            $bidderCount = AbstractQuotationItem::where('pr_item_id', $itemID->id)
                                                ->count();
        } else if ($type == 'this') {
            $bidderCount = AbstractQuotationItem::where('pr_item_id', $optionalParam)
                                                ->count();
        }

        return $bidderCount;
    }

    private function getAbstractTable($prID, $groupNo = 0, $toggle = "multiple") {
        if ($toggle == 'multiple') {
            $items = PurchaseRequestItem::select('group_no')
                                        ->where('pr_id', $prID)
                                        ->distinct()
                                        ->orderBy('group_no')
                                        ->get();
        } else {
            $items = PurchaseRequestItem::select('group_no')
                                        ->where('pr_id', $prID)
                                        ->where('group_no', $groupNo)
                                        ->distinct()
                                        ->orderBy('group_no')
                                        ->get();
        }

        foreach ($items as $item) {
            $arraySuppliers = [];
            $arrayPrItems = [];
            $suppliers = DB::table('abstract_quotation_items as abs')
                           ->select('bid.id', 'bid.company_name')
                           ->join('purchase_request_items as item', 'item.id', '=', 'abs.pr_item_id')
                           ->join('suppliers as bid', 'bid.id', '=', 'abs.supplier')
                           ->where([['item.group_no', $item->group_no],
                                    ['item.pr_id', $prID]])
                           ->orderBy('bid.company_name')
                           ->distinct()
                           ->get();
            $pritems = DB::table('purchase_request_items as item')
                         ->select('bid.company_name', 'item.awarded_remarks', 'item.est_unit_cost',
                                  'unit.unit_name', 'item.id as item_id', 'item.item_description',
                                  'item.quantity', 'item.awarded_to', 'item.document_type',
                                  'item.item_no')
                         ->leftJoin('suppliers as bid', 'bid.id', '=', 'item.awarded_to')
                         ->join('item_unit_issues as unit', 'unit.id', '=', 'item.unit_issue')
                         ->where([['item.group_no', $item->group_no],
                                  ['item.pr_id', $prID]])
                         ->orderBy('item.item_no')
                         ->get();
            $bidderCount = $this->getBidderCount($item->group_no, $prID);

            foreach ($suppliers as $bid) {
                $arraySuppliers[] = (object)['id' => $bid->id,
                                             'company_name' => $bid->company_name];
            }

            foreach ($pritems as $pr) {
                $arrayAbstractItems = [];
                $abstractItems = DB::table('abstract_quotation_items as abs')
                                   ->select('abs.id', 'abs.unit_cost', 'abs.total_cost', 'abs.specification',
                                            'abs.remarks')
                                   ->join('purchase_request_items as item', 'item.id', '=', 'abs.pr_item_id')
                                   ->join('suppliers as bid', 'bid.id', '=', 'abs.supplier')
                                   ->where([['item.pr_id', $prID],
                                            ['item.id', $pr->item_id]])
                                   ->orderBy('bid.company_name')
                                   ->get();

                foreach ($abstractItems as $abs) {
                    $arrayAbstractItems[] = (object)['id' => $abs->id,
                                                     'unit_cost' => $abs->unit_cost,
                                                     'total_cost' => $abs->total_cost,
                                                     'specification' => $abs->specification,
                                                     'remarks' => $abs->remarks];
                }

                $arrayPrItems[] = (object)['item_id' => $pr->item_id,
                                           'item_no' => $pr->item_no,
                                           'item_description' => $pr->item_description,
                                           'est_unit_cost' => $pr->est_unit_cost,
                                           'unit_name' => $pr->unit_name,
                                           'quantity' => $pr->quantity,
                                           'awarded_to' => $pr->awarded_to,
                                           'company_name' => $pr->company_name,
                                           'awarded_remarks' => $pr->awarded_remarks,
                                           'abstract_items' => (object)$arrayAbstractItems,
                                           'abstract_item_count' => $abstractItems->count(),
                                           'document_type' => $pr->document_type];
            }

            $item->suppliers = (object)$arraySuppliers;
            $item->pr_items = (object)$arrayPrItems;
            $item->bidder_count = $bidderCount;
            $item->pr_item_count = $pritems->count();
        }

        return $items;
    }
}
