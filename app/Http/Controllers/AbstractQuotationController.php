<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
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

class AbstractQuotationController extends Controller
{
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
        $isAllowedPO = Auth::user()->getModuleAccess('proc_po_jo', 'is_allowed');

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $absData = PurchaseRequest::with(['funding', 'requestor', 'rfq'])
                                  ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
            $query->whereIn('id', $empDivisionAccess);
        })->whereHas('abstract', function($query) {
            $query->whereNotNull('id');
        });

        if ($roleHasOrdinary) {
            $absData = $absData->where('requested_by', Auth::user()->id);
        }

        if (!empty($keyword)) {
            $absData = $absData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('pr_no', 'like', "%$keyword%")
                    ->orWhere('date_pr', 'like', "%$keyword%")
                    ->orWhere('purpose', 'like', "%$keyword%")
                    ->orWhereHas('funding', function($query) use ($keyword) {
                        $query->where('source_name', 'like', "%$keyword%");
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
                        $query->where('date_abstract', 'like', "%$keyword%");
                    });
            });
        }

        $absData = $absData->sortable(['pr_no' => 'desc'])->paginate(20);

        foreach ($absData as $abs) {
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
        }

        return view('modules.procurement.abstract.index', [
            'list' => $absData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedApprove' => $isAllowedApprove,
            'isAllowedPO' => $isAllowedPO,
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

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCreate($id) {
        $instanceAbstract = AbstractQuotation::with('pr')->find($id);
        $prID = $instanceAbstract->pr->id;
        $prNo = $instanceAbstract->pr->pr_no;
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
            'prID' => $prID,
            'suppliers' => $suppliers,
            'procurementModes' => $procurementModes,
            'users' => $users,
            'signatories' => $signatories,
            'abstractItems' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id) {
        //
    }

    /**
     * Store a newly created items in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeItems(Request $request, $id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Update the specified items resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateItems(Request $request, $id) {
        //
    }

    /**
     * Remove the specified items resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteItems($id) {
        //
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
            $instanceAbstract = AbstractQuotation::find($id);
            $prID = $instancePR->pr_id;
            $prNo = $instancePR->pr_no;
            $instancePR = PurchaseRequest::find($prID);

            $requestedBy = $instancePR->requested_by;
            $instancePR->date_pr_approved = Carbon::now();
            $instancePR->status = 5;
            $instancePR->save();

            if (!$instanceAbstract) {
                $instanceAbstract = new RequestQuotation;
                $instanceAbstract->pr_id = $id;
                $instanceAbstract->save();

                $rfqData = RequestQuotation::where('pr_id', $id)->first();
                $rfqID = $rfqData->id;
                $instanceDocLog->logDocument($id, Auth::user()->id, NULL, 'received');
            }

            $instancePR->notifyApproved($prNo, $requestedBy);

            $msg = "Purchase request '$prNo' successfully approved.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
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
                                  'item.quantity', 'item.awarded_to', 'item.document_type')
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
                                   ->join('purchase_request_items as item', 'item.id', '=', 'abs.pr_item_id')
                                   ->where([['item.pr_id', $prID],
                                            ['item.id', $pr->item_id]])
                                   ->orderBy('abs.supplier')
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
                                           'unit_name' => $pr->unit_name,
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
}
