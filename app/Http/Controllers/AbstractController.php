<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\AbstractQuotation;
use App\Models\AbstractQuotationItem;
use App\Models\PurchaseJobOrder;
use App\Models\ObligationRequestStatus;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\InventoryStock;

use App\User;
use App\Models\FundingSource;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\ItemUnitIssue;
use App\Models\Signatory;
use App\Models\ProcurementMode;
use Carbon\Carbon;
use DB;
use Auth;

class AbstractController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // Get module access
        $module = 'proc_abstract';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedApprove = Auth::user()->getModuleAccess($module, 'approve_po_jo');
        $isAllowedPO = Auth::user()->getModuleAccess('proc_po_jo', 'is_allowed');

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();

        $absData = AbstractQuotation::whereHas('pr', function($query)
                    use ($empDivisionAccess) {
            $query->whereIn('division', $empDivisionAccess);
        });

        if ($roleHasOrdinary) {
            $absData = $absData->whereHas('pr', function($query) {
                $query->where('requested_by', Auth::user()->id);
            });
        } else {
            $absData = $absData->whereHas('pr', function($query) {
                $query->orWhere('requested_by', Auth::user()->id);
            });
        }

        if (!empty($keyword)) {
            $absData = $absData->where('pr_id', $keyword);
        }

        $absData = $absData->whereHas('pr', function($query)
                    use ($empDivisionAccess) {
            $query->orderBy('pr_no', 'desc');
        });

        $absData = $absData->get();

        foreach ($absData as $abs) {
            $instanceFundSource = FundingSource::find($abs->pr->funding_source);
            $fundingSource = !empty($instanceFundSource->source_name) ?
                              $instanceFundSource->source_name : '';
            $requestedBy = Auth::user()->getEmployee($abs->pr->requested_by)->name;
            $toggle = "create";
            $countAbstract = AbstractQuotationItem::where('pr_id', $abs->pr_id)
                                                  ->distinct()
                                                  ->count();

            if ($countAbstract > 0) {
                $toggle = "edit";
            } else {
                $toggle = "create";
            }

            $abs->toggle = $toggle;
            $abs->doc_status = $instanceDocLog->checkDocStatus($abs->id);
            $abs->pr->funding_source = $fundingSource;
            $abs->pr->requested_by = $requestedBy;
        }

        return view('modules.procurement.abstract.index', [
            'list' => $absData,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedApprove' => $isAllowedApprove,
            'isAllowedPO' => $isAllowedPO,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate(Request $request, $id) {
        $prNo = $request['pr_no'];
        $toggle = $request['toggle'];
        $abstractData = Abstracts::where('pr_id', $id)->first();

        if (!$abstractData) {
            $abstractNew = new Abstracts;
            $abstractNew->pr_id = $id;
            $abstractNew->save();

            $abstractData = Abstracts::where('pr_id', $id)->first();
        }

        $supplierList = Supplier::orderBy('company_name')->get();
        $modeProcurement = ModeProcurement::all();
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.abstract_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.abs', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();
        $employees = DB::table('tblemp_accounts')
                       ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                'emp_id', 'position')
                       ->orderBy('firstname')
                       ->get();
        $endUser = PurchaseRequest::where('id', $id)->first()->requested_by;
        $items = $this->getAbstractTable($id);

        return view('pages.create-edit-abstract', ['prID' => $id,
                                                   'prNo' => $prNo,
                                                   'toggle' => $toggle,
                                                   'abstractData' => $abstractData,
                                                   'list' => $items,
                                                   'supplierList' => $supplierList,
                                                   'signatories' => $signatories,
                                                   'employees' => $employees,
                                                   'mode' => $modeProcurement,
                                                   'endUser' => $endUser]);
    }

    public function getSegment(Request $request, $id) {
        $supplierList = Supplier::orderBy('company_name')->get();
        $bidderCount = $request['bidder_count'];
        $groupKey = $request['group_key'];
        $groupNo = $request['group_no'];
        $items = $this->getAbstractTable($id, $groupNo, 'single');

        return view('pages.create-edit-abstract-segment', ['list' => $items,
                                                                  'supplierList' => $supplierList,
                                                                  'bidderCount' => $bidderCount,
                                                                  'groupKey' => $groupKey]);
    }

    private function getAbstractTable($id, $groupNo = 0, $toggle = "multiple") {
        if ($toggle == 'multiple') {
            $items = DB::table('tblpr_items')
                       ->select('group_no')
                       ->where('pr_id', $id)
                       ->distinct()
                       ->orderBy('group_no', 'asc')
                       ->get();
        } else {
            $items = DB::table('tblpr_items')
                       ->select('group_no')
                       ->where('pr_id', $id)
                       ->where('group_no', $groupNo)
                       ->distinct()
                       ->orderBy('group_no', 'asc')
                       ->get();
        }

        foreach ($items as $item) {
            $arraySuppliers = [];
            $arrayPrItems = [];
            $suppliers = DB::table('tblabstract_items as abs')
                           ->select('bid.id', 'bid.company_name')
                           ->join('tblpr_items as item', 'item.item_id', '=', 'abs.pr_item_id')
                           ->join('tblsuppliers as bid', 'bid.id', '=', 'abs.supplier_id')
                           ->where([['item.group_no', $item->group_no],
                                    ['item.pr_id', $id]])
                           ->orderBy('bid.id')
                           ->distinct()
                           ->get();
            $pritems = DB::table('tblpr_items as item')
                         ->select('bid.company_name', 'item.awarded_remarks', 'item.est_unit_cost',
                                  'unit.unit', 'item.item_id', 'item.item_description', 'item.quantity',
                                  'item.awarded_to', 'item.document_type')
                         ->leftJoin('tblsuppliers as bid', 'bid.id', '=', 'item.awarded_to')
                         ->join('tblunit_issue as unit', 'unit.id', '=', 'item.unit_issue')
                         ->where([['item.group_no', $item->group_no],
                                  ['item.pr_id', $id]])
                         ->orderByRaw('LENGTH(item.item_id)')
                         ->orderBy('item.item_id')
                         ->get();

            $bidderCount = $this->getBidderCount($item->group_no, $id);

            foreach ($suppliers as $bid) {
                $arraySuppliers[] = (object)['id' => $bid->id,
                                             'company_name' => $bid->company_name];
            }

            foreach ($pritems as $pr) {
                $arrayAbstractItems = [];
                $abstractItems = DB::table('tblabstract_items as abs')
                                   ->join('tblpr_items as item', 'item.item_id', '=', 'abs.pr_item_id')
                                   ->where([['item.pr_id', $id],
                                            ['item.item_id', $pr->item_id]])
                                   ->orderBy('abs.supplier_id')
                                   ->get();

                foreach ($abstractItems as $abs) {
                    $arrayAbstractItems[] = (object)['abstract_id' => $abs->abstract_id,
                                                     'unit_cost' => $abs->unit_cost,
                                                     'total_cost' => $abs->total_cost,
                                                     'specification' => $abs->specification,
                                                     'remarks' => $abs->remarks];
                }

                $arrayPrItems[] = (object)['item_id' => $pr->item_id,
                                           'item_description' => $pr->item_description,
                                           'est_unit_cost' => $pr->est_unit_cost,
                                           'unit' => $pr->unit,
                                           'quantity' => $pr->quantity,
                                           'awarded_to' => $pr->awarded_to,
                                           'company_name' => $pr->company_name,
                                           'awarded_remarks' => $pr->awarded_remarks,
                                           'abstract_items' => (object)$arrayAbstractItems,
                                           'document_type' => $pr->document_type];
            }

            $item->suppliers = (object)$arraySuppliers;
            $item->pr_items = (object)$arrayPrItems;
            $item->bidder_count = $bidderCount;
        }

        return $items;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*
    public function storeUpdate(Request $request, $id)
    {
        $poLetter = ['A','B','C','D','E','F','G','H','I','J','K','L','M',
                     'N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $abstract = Abstracts::where('pr_id', $id)->first();
        $sigChairperson = $request->sig_chairperson;
        $sigViceChairperson = $request->sig_vice_chairperson;
        $sigFirstMember = $request->sig_first_member;
        $sigSecondMember = $request->sig_second_member;
        $sigThirdMember = $request->sig_third_member;
        $endUser = $request->sig_end_user;
        $abstractDate = $request->date_abstract;
        $modeProcurement = $request->mode_procurement;
        $prNo = $request->pr_no;
        $prID = $request->pr_id;
        $toggle = $request->toggle;

        $groupNo = $request->group_no;
        $bidderCount = $request->bidder_count;
        $selectedSupplier = $request->selected_supplier;
        $itemID = $request->item_id;
        $abstractID = $request->abstract_id;
        $unitCost = $request->unit_cost;
        $totalCost = $request->total_cost;
        $specification = $request->specification;
        $remarks = $request->remarks;
        $awardedTo = $request->awarded_to;
        $documentType = $request->document_type;
        $awarderRemarks = $request->awarded_remarks;

        try {
            $abstract->sig_chairperson = $sigChairperson;
            $abstract->sig_vice_chairperson = $sigViceChairperson;
            $abstract->sig_first_member = $sigFirstMember;
            $abstract->sig_second_member = $sigSecondMember;
            $abstract->sig_third_member = $sigThirdMember;
            $abstract->sig_end_user = $endUser;
            $abstract->date_abstract = $abstractDate;
            $abstract->mode_procurement_id = $modeProcurement;
            $abstract->save();
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the Abstract of Quotation $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }

        foreach ($bidderCount as $grpKey => $bidCount) {
            $grpNo = $groupNo[$grpKey];
            $oldBidderCount = $this->getBidderCount($grpNo, $prID);
            $newAwarded[] = (object) ['awarded_to' => '',
                                      'document_type' => ''];

            foreach ($itemID[$grpKey] as $itemKey => $id) {
                if (isset($selectedSupplier[$grpKey]) && (count($selectedSupplier[$grpKey]) > 0)) {
                    // Initialization of PO/JO item create/update variables
                    $awardTo = $awardedTo[$grpKey][$itemKey];
                    $awardRemarks = $awarderRemarks[$grpKey][$itemKey];
                    $docType = $documentType[$grpKey][$itemKey];
                    $poData =  PurchaseOrder::where('pr_id', $prID)->get();
                    $poCount = $poData->count();
                    $newPO_No = "";

                    if (empty($awardTo)) {
                        $awardTo = NULL;
                    }

                    // Create/update the abstract items
                    foreach ($selectedSupplier[$grpKey] as $supplierKey => $supplier) {
                        if ($toggle == 'edit') {
                            try {
                                if ($oldBidderCount == $bidCount) {
                                    $absID = $abstractID[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems = AbstractItem::where('abstract_id', $absID)
                                                                 ->where('pr_item_id', $id)
                                                                 ->first();

                                    $abstractItems->supplier_id = $supplier;
                                    $abstractItems->unit_cost = $unitCost[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems->total_cost = $totalCost[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems->specification = $specification[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems->remarks = $remarks[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems->save();
                                } else {
                                    if ($supplierKey == 0) {
                                        $abstractItems = AbstractItem::where('pr_item_id', $id)
                                                                     ->delete();
                                    }

                                    $abstractItems = new AbstractItem;
                                    $abstractItems->abstract_id = $id . '-' . ($supplierKey + 1);
                                    $abstractItems->pr_id = $prID;
                                    $abstractItems->pr_item_id = $id;
                                    $abstractItems->supplier_id = $supplier;
                                    $abstractItems->unit_cost = $unitCost[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems->total_cost = $totalCost[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems->specification = $specification[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems->remarks = $remarks[$grpKey][$itemKey][$supplierKey];
                                    $abstractItems->save();
                                }
                            } catch (Exception $e) {
                                $msg = "There is an error encountered updating the Abstract of Quotation $prNo.";
                                return redirect(url()->previous())->with('failed', $msg);
                            }
                        } else if ($toggle == 'create') {
                            try {
                                $abstractItems = new AbstractItem;
                                $abstractItems->abstract_id = $id . '-' . ($supplierKey + 1);
                                $abstractItems->pr_id = $prID;
                                $abstractItems->pr_item_id = $id;
                                $abstractItems->supplier_id = $supplier;
                                $abstractItems->unit_cost = $unitCost[$grpKey][$itemKey][$supplierKey];
                                $abstractItems->total_cost = $totalCost[$grpKey][$itemKey][$supplierKey];
                                $abstractItems->specification = $specification[$grpKey][$itemKey][$supplierKey];
                                $abstractItems->remarks = $remarks[$grpKey][$itemKey][$supplierKey];
                                $abstractItems->save();
                            } catch (Exception $e) {
                                $msg = "There is an error encountered updating the Abstract of Quotation $prNo.";
                                return redirect(url()->previous())->with('failed', $msg);
                            }
                        }
                    }

                    // PO/JO items create/update
                    if ($poCount > 0) {
                        $pr = DB::table('tblpr_items')
                                ->select('awarded_to')
                                ->where('item_id', $id)
                                ->first();

                        if (!empty($awardTo)) {
                            $countNotApproved = PurchaseOrder::select('awarded_to', 'document_abrv')
                                                             ->where([['pr_id', $prID],
                                                                     ['awarded_to', $awardTo],
                                                                     ['document_abrv', $docType]])
                                                             ->whereNull('date_po_approved')
                                                             ->count();

                            if ($countNotApproved == 0) {
                                $po = new PurchaseOrder;
                                $newPO_No = $prNo . '-' . $poLetter[$poCount];
                                $po->po_no = $newPO_No;
                                $po->pr_id = $prID;
                                $po->awarded_to = $awardTo;
                                $po->document_abrv = $docType;
                                $po->code =  $this->generateTrackerCode($docType, $newPO_No, 3);
                                $po->save();
                            }

                            if (empty($newPO_No)) {
                                $poDat = DB::table('tblpo_jo')
                                            ->where([['pr_id', $prID],
                                                    ['awarded_to', $awardTo],
                                                    ['document_abrv', $docType]])
                                            ->whereNull('date_po_approved')
                                            ->first();
                                $newPO_No = $poDat->po_no;
                            }

                            if (empty($pr->awarded_to)) {
                                $this->addPO_JO_Item($id, $newPO_No, $awardTo);
                            }
                        }

                    }

                    // Continuaation of create/update the abstract items
                    DB::table('tblpr_items')
                      ->where('item_id', $id)
                      ->update(['awarded_to' => $awardTo,
                                'awarded_remarks' => $awardRemarks,
                                'document_type' => $docType]);
                }
            }
        }

        if ($toggle == 'edit') {
            $logEmpMessage = "updated the abstract item/s for purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Abstract of Quotation $prNo successfully updated.";
            return redirect(url('procurement/abstract?search=' . $prNo))->with('success', $msg);
        } else if ($toggle == 'create') {
            $logEmpMessage = "created the abstract item/s for purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Abstract of Quotation $prNo successfully created.";
            return redirect(url('procurement/abstract?search=' . $prNo))->with('success', $msg);
        }
    }*/

    public function storeUpdate(Request $request, $id) {
        $abstract = Abstracts::where('pr_id', $id)->first();
        $sigChairperson = $request->sig_chairperson;
        $sigViceChairperson = $request->sig_vice_chairperson;
        $sigFirstMember = $request->sig_first_member;
        $sigSecondMember = $request->sig_second_member;
        $sigThirdMember = $request->sig_third_member;
        $endUser = $request->sig_end_user;
        $abstractDate = $request->date_abstract;
        $modeProcurement = $request->mode_procurement;
        $prNo = $request->pr_no;
        $toggle = $request->toggle;

        try {
            $abstract->sig_chairperson = $sigChairperson;
            $abstract->sig_vice_chairperson = $sigViceChairperson;
            $abstract->sig_first_member = $sigFirstMember;
            $abstract->sig_second_member = $sigSecondMember;
            $abstract->sig_third_member = $sigThirdMember;
            $abstract->sig_end_user = $endUser;
            $abstract->date_abstract = $abstractDate;
            $abstract->mode_procurement_id = $modeProcurement;
            $abstract->save();
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the Abstract of Quotation $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }

        if ($toggle == 'edit') {
            $logEmpMessage = "updated the abstract item/s for purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Abstract of Quotation $prNo successfully updated.";
            return redirect(url('procurement/abstract?search=' . $prNo))->with('success', $msg);
        } else if ($toggle == 'create') {
            $logEmpMessage = "created the abstract item/s for purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Abstract of Quotation $prNo successfully created.";
            return redirect(url('procurement/abstract?search=' . $prNo))->with('success', $msg);
        }
    }

    public function storeUpdateItems(Request $request, $prID) {
        $poLetter = ['A','B','C','D','E','F','G','H','I','J','K','L','M',
                     'N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $toggle = $request->toggle;
        $prNo = $request->pr_no;
        $bidderCount = (int) $request->bidder_count;

        $listSelectedSupplier = explode(',', $request->list_selected_supplier);
        $prItemID = $request->pr_item_id;
        $listAbstractID = explode(',', $request->list_abstract_id);
        $listUnitCost = explode(',', $request->list_unit_cost);
        $listTotalCost = explode(',', $request->list_total_cost);
        $listSpecification = explode(',', $request->list_specification);
        $listRemarks = explode(',', $request->list_remarks);
        $awardedTo = (int) $request->awarded_to;
        $documentType = $request->document_type;
        $awarderRemarks = $request->awarded_remarks;

        $groupNo = $this->getGroupNo($prItemID);
        $oldBidderCount = $this->getBidderCount($groupNo, $prID, 'this', $prItemID);

        $poData =  PurchaseOrder::where('pr_id', $prID)->get();
        $poCount = $poData->count();
        $newPO_No = "";

        foreach ($listSelectedSupplier as $ctr => $supplier) {
            if ($toggle == 'create') {
                try {
                    $abstractItems = new AbstractItem;
                    $abstractItems->abstract_id = $prItemID . '-' . ($ctr + 1);
                    $abstractItems->pr_id = $prID;
                    $abstractItems->pr_item_id = $prItemID;
                    $abstractItems->supplier_id = $supplier;
                    $abstractItems->unit_cost = $listUnitCost[$ctr];
                    $abstractItems->total_cost = $listTotalCost[$ctr];
                    $abstractItems->specification = $listSpecification[$ctr];
                    $abstractItems->remarks = $listRemarks[$ctr];
                    $abstractItems->save();
                } catch (Exception $e) {
                    return "error";
                }
            } else if ($toggle == 'edit') {
                try {
                    if ($oldBidderCount == $bidderCount) {
                        $abstractItems = AbstractItem::where('abstract_id', $listAbstractID[$ctr])
                                                     ->where('pr_item_id', $prItemID)
                                                     ->first();

                        $abstractItems->supplier_id = $supplier;
                        $abstractItems->unit_cost = $listUnitCost[$ctr];
                        $abstractItems->total_cost = $listTotalCost[$ctr];
                        $abstractItems->specification = $listSpecification[$ctr];
                        $abstractItems->remarks = $listRemarks[$ctr];
                        $abstractItems->save();
                    } else {
                        if ($ctr == 0) {
                            AbstractItem::where('pr_item_id', $prItemID)
                                        ->delete();
                        }

                        $abstractItems = new AbstractItem;
                        $abstractItems->abstract_id = $prItemID . '-' . ($ctr + 1);
                        $abstractItems->pr_id = $prID;
                        $abstractItems->pr_item_id = $prItemID;
                        $abstractItems->supplier_id = $supplier;
                        $abstractItems->unit_cost = $listUnitCost[$ctr];
                        $abstractItems->total_cost = $listTotalCost[$ctr];
                        $abstractItems->specification = $listSpecification[$ctr];
                        $abstractItems->remarks = $listRemarks[$ctr];
                        $abstractItems->save();
                    }
                } catch (Exception $e) {
                    return "error";
                }
            }
        }

        // PO/JO items create/update
        if ($poCount > 0) {
            $pr = DB::table('tblpr_items')
                    ->select('awarded_to')
                    ->where('item_id', $prItemID)
                    ->first();

            if (!empty($awardedTo) || $awardedTo != 0) {
                $countNotApproved = PurchaseOrder::select('awarded_to', 'document_abrv')
                                                 ->where([['pr_id', $prID],
                                                         ['awarded_to', $awardedTo],
                                                         ['document_abrv', $documentType]])
                                                 ->whereNull('date_po_approved')
                                                 ->count();

                if ($countNotApproved == 0) {
                    $po = new PurchaseOrder;
                    $newPO_No = $prNo . '-' . $poLetter[$poCount];
                    $po->po_no = $newPO_No;
                    $po->pr_id = $prID;
                    $po->awarded_to = $awardedTo;
                    $po->document_abrv = $documentType;
                    $po->code =  $this->generateTrackerCode($documentType, $newPO_No, 3);
                    $po->save();
                }

                if (empty($newPO_No)) {
                    $poDat = DB::table('tblpo_jo')
                                ->where([['pr_id', $prID],
                                        ['awarded_to', $awardedTo],
                                        ['document_abrv', $documentType]])
                                ->whereNull('date_po_approved')
                                ->first();
                    $newPO_No = $poDat->po_no;
                }

                if (empty($pr->awarded_to)) {
                    $this->addPO_JO_Item($prItemID, $newPO_No, $awardedTo);
                }
            }

        }

        // Continuaation of create/update the abstract items
        DB::table('tblpr_items')
          ->where('item_id', $prItemID)
          ->update(['awarded_to' => $awardedTo,
                    'awarded_remarks' => $awarderRemarks,
                    'document_type' => $documentType]);

        // For debugging
        return "poCount = $poCount; awardedTo = $awardedTo; ".
               "prID = $prID; awardedTo = $awardedTo; ".
               "prItemID = $prItemID; documentType = $documentType;".
               "groupNo = $groupNo; oldBidderCount = $oldBidderCount;".
               "bidderCount = $bidderCount;";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $items = $this->getAbstractTable($id);

        return view('pages.view-abstract-items', ['list' => $items]);
    }

    public function getGroup($id) {
        $_data = [];
        $groupNumbers = DB::table('tblpr_items')->select('group_no')
                          ->where('pr_id', $id)
                          ->distinct()
                          ->get();

        foreach ($groupNumbers as $grpNo) {
            $_data[] = $grpNo->group_no;
        }

        $_data = array_unique($_data);

        foreach ($_data as $value) {
            $data[] = $value;
        }

        return json_encode($data);
    }

    private function getGroupNo($prItemID) {
        $prItem = DB::table('tblpr_items')->select('group_no')
                                          ->where('item_id', $prItemID)
                                          ->first();

        return $prItem->group_no;
    }

    private function getBidderCount($groupNo, $prID, $type = 'all', $optionalParam = '') {
        $bidderCount = 0;

        if ($type == 'all') {
            $itemID = DB::table('tblpr_items')->select('item_id')
                                              ->where([['pr_id', $prID],
                                                      ['group_no', $groupNo]])
                                              ->first();
            $bidderCount = AbstractItem::where('pr_item_id', $itemID->item_id)
                                       ->count();

            return $bidderCount;
        } else if ($type == 'this') {
            $bidderCount = AbstractItem::where('pr_item_id', $optionalParam)
                                       ->count();

            return $bidderCount;
        }
    }

    public function delete($id) {
        $pr = PurchaseRequest::find($id);
        $prNo = $pr->pr_no;

        try {
            $abstract = Abstracts::where('pr_id', $id)->first();
            $abstract->date_abstract_approve = NULL;
            $abstract->date_abstract = NULL;
            $abstract->save();

            $pr->status = 5;
            $pr->save();

             // Delete dependent documents
            DB::table('tblpr_items')
              ->where('pr_id', $id)
              ->update(['awarded_remarks' => NULL,
                        'awarded_to' => NULL,
                        'document_type' => 'PO']);

            DB::table('tblabstract_items')->where('pr_id', $id)->delete();
            PurchaseOrder::where('pr_id', $id)->forceDelete();
            DB::table('tblpo_jo_items')->where('pr_id', $id)->delete();
            OrsBurs::where('pr_id', $id)->forceDelete();
            InspectionAcceptance::where('pr_id', $id)->forceDelete();
            DisbursementVoucher::where('pr_id', $id)->forceDelete();
            InventoryStock::where('pr_id', $id)->forceDelete();
            DB::table('tblinventory_stocks_issue')->where('pr_id', $id)->delete();

            $logEmpMessage = "deleted the abstract of quotation $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Abstract of Quotation $prNo successfully deleted.";
            return redirect(url('procurement/abstract?search=' . $prNo))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered deleting the Abstract of Quotation $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function approve($id) {
        $pr = PurchaseRequest::find($id);
        $abstract = Abstracts::where('pr_id', $id)->first();
        $prNo = $pr->pr_no;

        //try {
            $code = $abstract->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $prItemGroup = json_decode($this->getGroup($id));
            $msg = "";
            $arrPOS = ['A','B','C','D','E','F','G','H','I','J','K','L','M',
                       'N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
            $cntWin = 0;

            if ($pr->status == 5) {
                if ($isDocGenerated) {
                    $abstract->date_abstract_approve = date('Y-m-d H:i:s');
                    $abstract->save();

                    foreach ($prItemGroup as $grp) {
                        $itemWinner = DB::table('tblpr_items')
                                        ->select('awarded_to', 'document_type')
                                        ->where([['pr_id', $id],
                                                 ['awarded_to', '<>', 0],
                                                 ['group_no', $grp]])
                                        ->whereNotNull('awarded_to')
                                        ->distinct()
                                        ->get();
                        $winCnt = count($itemWinner);

                        if ($winCnt > 0) {
                            foreach ($itemWinner as $win) {
                                $poNo = $pr->pr_no."-".$arrPOS[$cntWin];

                                $po = DB::table('tblpo_jo')
                                        ->where('po_no', $poNo)
                                        ->first();
                                $prItems = DB::table('tblpr_items')
                                             ->where([['pr_id', $id],
                                                     ['awarded_to', $win->awarded_to],
                                                     ['group_no', $grp]])
                                             ->orderByRaw('LENGTH(item_id)')
                                             ->orderBy('item_id')
                                             ->get();

                                if ($po) {
                                    /*
                                    $po = PurchaseOrder::where('po_no', $poNo)->first();
                                    $po->document_abrv = $win->document_type;
                                    $po->awarded_to = $win->awarded_to;
                                    $po->with_ors_burs = 'n';
                                    $po->save();*/

                                    DB::table('tblpo_jo')
                                      ->where('po_no', $poNo)
                                      ->update([
                                          'document_abrv' => $win->document_type,
                                          'awarded_to' => $win->awarded_to,
                                          'with_ors_burs' => 'n',
                                          'updated_at' => Carbon::now()
                                      ]);

                                    foreach ($prItems as $pItem) {
                                        $itemID = $pItem->item_id;
                                        $this->addPO_JO_Item($itemID, $poNo, $win->awarded_to);
                                    }

                                    PurchaseOrder::where('po_no', $poNo)->restore();
                                } else {
                                    $this->createPO_JO($poNo, $id, $win->document_type, $win->awarded_to, $prItems);
                                }

                                $cntWin++;
                            }
                        }
                    }

                    $pr->status = 6;
                    $pr->save();

                    InspectionAcceptance::where('pr_id', $id)->restore();
                    DisbursementVoucher::where('pr_id', $id)->restore();
                    InventoryStock::where('pr_id', $id)->restore();

                    $logEmpMessage = "approved for purchase/job order $prNo.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "Purchase Request $prNo is now ready for Purchase/Job Order.";
                    return redirect(url('procurement/abstract?search=' . $prNo))->with('success', $msg);
                } else {
                    $msg = "Generate first the Abstract of Quotation $prNo document first.";
                    return redirect(url('procurement/abstract?search=' . $prNo))->with('warning', $msg);
                }
            } else {
                $msg = "You must edit the Abstract of Quotation $prNo first.";
                return redirect(url('procurement/abstract?search=' . $prNo))->with('warning', $msg);
            }
        /*} catch (Exception $e) {
            $msg = "There is an error encountered setting to PO/JO the Abstract of Quotation $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }*/
    }

    private function createPO_JO($poNo, $prID, $documentType, $awardedTo, $prItems) {
        $po = new PurchaseOrder;
        $po->po_no = $poNo;
        $po->pr_id = $prID;
        $po->document_abrv = $documentType;
        $po->awarded_to = $awardedTo;
        $po->code = $this->generateTrackerCode($documentType, $poNo, 3);
        $po->save();

        foreach ($prItems as $pItem) {
            $itemID = $pItem->item_id;
            $this->addPO_JO_Item($itemID, $poNo, $awardedTo);
        }
    }

    private function addPO_JO_Item($itemID, $poNo, $awardTo) {
        $prItem = DB::table('tblpr_items as pr')
                    ->select('pr.pr_id', 'pr.quantity', 'pr.unit_issue', 'pr.item_description',
                             'abs.specification', 'abs.unit_cost', 'abs.total_cost')
                    ->join('tblabstract_items as abs', function($join) use($awardTo) {
                            $join->on('abs.pr_item_id', '=', 'pr.item_id')
                                 ->where('abs.supplier_id', '=', $awardTo);
                        })
                    ->where('item_id', $itemID)
                    ->first();
        $poItem = DB::table('tblpo_jo_items')
                    ->where('item_id', $itemID)
                    ->first();

        if ($prItem) {
            $itemDescription = empty($prItem->specification) ?
                               $prItem->item_description :
                               $prItem->item_description . " (" . $prItem->specification . ")";
            $item = ['item_id' => $itemID,
                     'po_no' => $poNo,
                     'pr_id' => $prItem->pr_id,
                     'quantity' => $prItem->quantity,
                     'unit_issue' => $prItem->unit_issue,
                     'item_description' => $itemDescription,
                     'unit_cost' => $prItem->unit_cost,
                     'total_cost' => $prItem->total_cost];

            if ($poItem) {
                DB::table('tblpo_jo_items')
                  ->where('item_id', $itemID)
                  ->update($item);
            } else {
                DB::table('tblpo_jo_items')
                  ->insert($item);
            }
        }
    }
}
