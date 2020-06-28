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
use App\Models\InventoryStockIssue;
use App\Models\InventoryStock;
use App\Models\InventoryStockItem;
use App\Models\InventoryStockIssueItem;

use App\User;
use App\Models\DocumentLog as DocLog;
use App\Models\InventoryClassification;
use App\Models\ItemClassification;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\Signatory;
use App\Models\ItemUnitIssue;
use App\Models\EmpDivision;
use Carbon\Carbon;
use Auth;
use DB;

class InventoryStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // Get module access
        $isAllowedDestroy = 1;
        /*
        $module = 'inv_stocks';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedObligate = Auth::user()->getModuleAccess($module, 'obligate');
        $isAllowedPO = Auth::user()->getModuleAccess('proc_po_jo', 'is_allowed');*/

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $invStocksData = InventoryStock::with(['stockitems', 'procstatus', 'inventoryclass'])
                                       ->has('stockitems', '>', 0);

        if (!empty($keyword)) {
            $invStocksData = $invStocksData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('pr_id', 'like', "%$keyword%")
                    ->orWhere('po_id', 'like', "%$keyword%")
                    ->orWhere('entity_name', 'like', "%$keyword%")
                    ->orWhere('fund_cluster', 'like', "%$keyword%")
                    ->orWhere('inventory_no', 'like', "%$keyword%")
                    ->orWhere('division', 'like', "%$keyword%")
                    ->orWhere('office', 'like', "%$keyword%")
                    ->orWhere('responsibility_center', 'like', "%$keyword%")
                    ->orWhere('po_no', 'like', "%$keyword%")
                    ->orWhere('date_po', 'like', "%$keyword%")
                    ->orWhere('purpose', 'like', "%$keyword%")
                    ->orWhereHas('supplier', function($query) use ($keyword) {
                        $query->where('company_name', 'like', "%$keyword%");
                    })->orWhereHas('inventoryclass', function($query) use ($keyword) {
                        $query->where('classification_name', 'like', "%$keyword%")
                              ->orWhere('abbrv', 'like', "%$keyword%");
                    })->orWhereHas('procstatus', function($query) use ($keyword) {
                        $query->where('status_name', 'like', "%$keyword%");
                    })->orWhereHas('stockitems', function($query) use ($keyword) {
                        $query->where('po_item_id', 'like', "%$keyword%")
                              ->orWhere('description', 'like', "%$keyword%")
                              ->orWhere('quantity', 'like', "%$keyword%")
                              ->orWhere('amount', 'like', "%$keyword%");
                    });
            });
        }

        $invStocksData = $invStocksData->sortable(['inventory_no' => 'desc'])->paginate(15);

        foreach ($invStocksData as $invStock) {
            foreach ($invStock->stockitems as $item) {
                $stockItem = InventoryStockItem::with('stockissueditems')
                                              ->find($item->id);
                $item->available_quantity = $stockItem->quantity;

                foreach ($stockItem->stockissueditems as $issuedItem) {
                    $item->available_quantity -= $issuedItem->quantity;
                }
            }
        }

        return view('modules.inventory.stock.index', [
            'keyword' => $keyword,
            'list' => $invStocksData,
            'paper_sizes' => $paperSizes,
            'isAllowedDestroy' => $isAllowedDestroy
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateFromIAR($poID) {
        $inventoryClassifications = InventoryClassification::orderBy('classification_name')
                                                           ->get();
        $itemClassifications = ItemClassification::orderBy('classification_name')
                                                 ->get();
        $poData = PurchaseJobOrder::with('poitems')->find($poID);
        $items = $poData->poitems;

        foreach ($items as $item) {
            $unitIssueData = ItemUnitIssue::find($item->unit_issue);
            $item->unit = $unitIssueData->unit_name;
        }

        return view('modules.inventory.stock.create-from-iar', compact(
            'poID', 'inventoryClassifications', 'itemClassifications', 'items'
        ));
    }

    public function showCreate() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFromIAR(Request $request, $poID) {
        $poItemIDs = $request->po_item_ids;
        $_inventoryClassifications = $request->inventory_classifications;
        $inventoryClassifications = array_unique($request->inventory_classifications);
        $itemClassifications = $request->item_classifications;

        try {
            foreach ($inventoryClassifications as $invClass) {
                $instancePO = PurchaseJobOrder::with('poitems')->find($poID);
                $prID = $instancePO->pr_id;
                $poNo = $instancePO->po_no;
                $instanceInvClass = InventoryClassification::find($invClass);
                $invClassAbbrev = $instanceInvClass->abbrv;
                $inventoryNo = "$invClassAbbrev-$poNo";
                $datePO = $instancePO->date_po;
                $supplier = $instancePO->awarded_to;
                $instancePR = PurchaseRequest::find($prID);
                $office = $instancePR->office;
                $division = $instancePR->division;
                $purpose = $instancePR->purpose;

                $instanceInvStocks = new InventoryStock;
                $instanceInvStocks->pr_id = $prID;
                $instanceInvStocks->po_id = $poID;
                $instanceInvStocks->inventory_no = $inventoryNo;
                $instanceInvStocks->division = $division;
                $instanceInvStocks->office = $office;
                $instanceInvStocks->po_no = $poNo;
                $instanceInvStocks->date_po = $datePO;
                $instanceInvStocks->supplier = $supplier;
                $instanceInvStocks->purpose = $purpose;
                $instanceInvStocks->inventory_classification = $invClass;
                $instanceInvStocks->save();

                $invStock = DB::table('inventory_stocks')
                            ->where([['po_id', $poID], ['inventory_classification', $invClass]])
                            ->first();
                $invStockID = $invStock->id;

                foreach ($poItemIDs as $ctr => $poItemID) {
                    $_invClass = $_inventoryClassifications[$ctr];
                    $itemClass = $itemClassifications[$ctr];
                    $itemNo = $ctr + 1;
                    $poItem = PurchaseJobOrderItem::find($poItemID);
                    $unitIssue = $poItem->unit_issue;
                    $description = $poItem->item_description;
                    $quantity = $poItem->quantity;
                    $amount = $poItem->total_cost;

                    if ($_invClass == $invClass) {
                        $instanceInvStockItem = new InventoryStockItem;
                        $instanceInvStockItem->inv_stock_id = $invStockID;
                        $instanceInvStockItem->pr_id = $prID;
                        $instanceInvStockItem->po_id = $poID;
                        $instanceInvStockItem->po_item_id = $poItemID;
                        $instanceInvStockItem->item_classification = $itemClass;
                        $instanceInvStockItem->item_no = $itemNo;
                        $instanceInvStockItem->unit_issue = $unitIssue;
                        $instanceInvStockItem->description = $description;
                        $instanceInvStockItem->quantity = $quantity;
                        $instanceInvStockItem->amount = $amount;
                        $instanceInvStockItem->save();
                    }
                }
            }

            $documentType = 'Inventory Stocks';
            $routeName = 'stocks';

            $msg = "$documentType successfully created.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName)
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEditFromIAR($poID) {
        $items = InventoryStockItem::where('po_id', $poID)
                                   ->orderBy('item_no')
                                   ->get();
        $inventoryClassifications = InventoryClassification::orderBy('classification_name')
                                                           ->get();
        $itemClassifications = ItemClassification::orderBy('classification_name')
                                                 ->get();

        foreach ($items as $item) {
            $instanceInvStocks = InventoryStock::find($item->inv_stock_id);
            $unitIssueData = ItemUnitIssue::find($item->unit_issue);
            $item->unit = $unitIssueData->unit_name;
            $item->inventory_classification = $instanceInvStocks->inventory_classification;
        }

        return view('modules.inventory.stock.update-from-iar', compact(
            'poID', 'inventoryClassifications', 'itemClassifications', 'items'
        ));
    }

    public function showEdit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFromIAR(Request $request, $poID){
        $invItemIDs = $request->inv_item_ids;
        $inventoryClassifications = $request->inventory_classifications;
        $itemClassifications = $request->item_classifications;

        try {
            foreach ($invItemIDs as $ctr => $itemID) {
                $invClass = $inventoryClassifications[$ctr];
                $instanceInvStockItem = InventoryStockItem::find($itemID);
                $invStockID = $instanceInvStockItem->inv_stock_id;
                $_instanceInvStocks = InventoryStock::find($invStockID);

                if ($_instanceInvStocks->inventory_classification != $invClass) {
                    $instanceInvStocks = InventoryStock::where([
                        ['inventory_classification', $invClass],
                        ['po_id', $poID]
                    ])->first();

                    if (!$instanceInvStocks) {
                        $instancePO = PurchaseJobOrder::with('poitems')->find($poID);
                        $prID = $instancePO->pr_id;
                        $poNo = $instancePO->po_no;
                        $instanceInvClass = InventoryClassification::find($invClass);
                        $invClassAbbrev = $instanceInvClass->abbrv;
                        $inventoryNo = "$invClassAbbrev-$poNo";
                        $datePO = $instancePO->date_po;
                        $supplier = $instancePO->awarded_to;
                        $instancePR = PurchaseRequest::find($prID);
                        $office = $instancePR->office;
                        $division = $instancePR->division;
                        $purpose = $instancePR->purpose;

                        $instanceInvStocks = new InventoryStock;
                        $instanceInvStocks->pr_id = $prID;
                        $instanceInvStocks->po_id = $poID;
                        $instanceInvStocks->inventory_no = $inventoryNo;
                        $instanceInvStocks->division = $division;
                        $instanceInvStocks->office = $office;
                        $instanceInvStocks->po_no = $poNo;
                        $instanceInvStocks->date_po = $datePO;
                        $instanceInvStocks->supplier = $supplier;
                        $instanceInvStocks->purpose = $purpose;
                        $instanceInvStocks->inventory_classification = $invClass;
                        $instanceInvStocks->save();
                    }

                    $invStock = DB::table('inventory_stocks')
                                ->where([['po_id', $poID], ['inventory_classification', $invClass]])
                                ->first();
                    $invStockID = $invStock->id;

                    $instanceInvStockItem->inv_stock_id = $invStockID;
                }

                $instanceInvStockItem->item_classification = $itemClassifications[$ctr];
                $instanceInvStockItem->save();
            }

            $instanceInvStocks = InventoryStock::where('po_id', $poID)->get();

            foreach ($instanceInvStocks as $invStock) {
                $instanceInvStockItem = InventoryStockItem::where('inv_stock_id', $invStock->id)
                                                          ->orderBy('item_no')
                                                          ->get();

                foreach ($instanceInvStockItem as $ctr => $item) {
                    $item->item_no = $ctr + 1;
                    $item->save();
                }
            }

            $documentType = 'Inventory Stocks';
            $routeName = 'stocks';

            $msg = "$documentType successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName)
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    public function update(Request $request, $id){
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function showCreateIssueItem($invStockID, $invStockItemID, $classification, $type) {
        if ($type == 'multiple') {
            $invStockData = InventoryStock::with('stockitems')->find($invStockID);
        } else {
            $invStockData = InventoryStock::with(['stockitems' => function($query) use ($invStockItemID) {
                $query->where('id', $invStockItemID);
            }])->find($invStockID);
        }

        $prData = PurchaseRequest::find($invStockData->pr_id);
        $supplierData = Supplier::find($invStockData->supplier);
        $divisionData = EmpDivision::find($invStockData->division);

        foreach ($invStockData->stockitems as $item) {
            $stockItem = InventoryStockItem::with('stockissueditems')
                                           ->find($item->id);
            $item->available_quantity = $stockItem->quantity;

            foreach ($stockItem->stockissueditems as $issuedItem) {
                $item->available_quantity -= $issuedItem['quantity'];
            }

            $unitIssueData = ItemUnitIssue::find($item->unit_issue);
            $item->unit = $unitIssueData->unit_name;
        }

        $requestedBy = $prData ? $prData->requested_by : NULL;
        $stocks = $invStockData->stockitems;
        $inventoryNo = $invStockData->inventory_no;
        $office = $invStockData->office;
        $division = $divisionData->division_name;
        $poNo = $invStockData->po_no;
        $poDate = $invStockData->date_po;
        $supplier = $supplierData->company_name;
        $unitIssue = $unitIssueData->unit_name;

        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();
        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }
        $employees = User::orderBy('firstname')->get();

        if ($classification == 'ris') {
            return view('modules.inventory.stock.ris-create-issue', compact(
                'invStockID', 'classification',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees', 'office', 'division',
                'requestedBy'
            ));
        } else if ($classification == 'par') {
            return view('modules.inventory.stock.par-create-issue', compact(
                'invStockID', 'invStockItemID', 'classification',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees'
            ));
        } else {
            return view('modules.inventory.stock.ics-create-issue', compact(
                'invStockID', 'invStockItemID', 'classification',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees'
            ));
        }
    }

    public function storeIssueItem(Request $request, $invStockID, $classification) {
        $invStockItemIDs = $request->inv_stock_item_id;
        $quantities = $request->quantity;
        $datesIssued = $request->date_issued;
        $propStockNos = $request->prop_stock_no;
        $sigReceivedBy = $request->sig_received_by;

        if ($classification == 'ris') {
            $documentType = 'Requisition and Issue Slip (RIS)';

            $stockAvailables = $request->stock_available;
            $issuedRemarks = $request->issued_remarks;
            $sigApprovedBy = $request->sig_approved_by;
            $sigRequestedBy = $request->sig_requested_by;
            $sigIssuedBy = $request->sig_issued_by;
        } else if ($classification == 'par') {
            $documentType = 'Property Aknowledgement Receipt (PAR)';

            $sigIssuedBy = $request->sig_issued_by;
        } else {
            $documentType = 'Inventory Custodian Slip (ICS)';

            $estUsefulLifes = $request->est_useful_life;
            $sigReceivedFrom = $request->sig_received_from;
        }

        try {
            $instanceInvStocks = InventoryStock::find($invStockID);
            $instanceInvStockIssue = InventoryStockIssue::where([
                ['inv_stock_id', $invStockID], ['sig_received_by', $sigReceivedBy]
            ])->first();

            if (!$instanceInvStockIssue) {
                $instanceInvStockIssue = new InventoryStockIssue;
                $instanceInvStockIssue->inv_stock_id = $invStockID;
                $instanceInvStockIssue->pr_id = $instanceInvStocks->pr_id;
                $instanceInvStockIssue->po_id = $instanceInvStocks->po_id;
            }

            if ($classification == 'ris') {
                $instanceInvStockIssue->sig_requested_by = $sigRequestedBy;
                $instanceInvStockIssue->sig_approved_by = $sigApprovedBy;
                $instanceInvStockIssue->sig_issued_by = $sigIssuedBy;
                $instanceInvStockIssue->sig_received_by = $sigReceivedBy;
            } else if ($classification == 'par') {
                $instanceInvStockIssue->sig_issued_by = $sigIssuedBy;
                $instanceInvStockIssue->sig_received_by = $sigReceivedBy;
            } else {
                $instanceInvStockIssue->sig_received_from = $sigReceivedFrom;
                $instanceInvStockIssue->sig_received_by = $sigReceivedBy;
            }

            $instanceInvStockIssue->save();

            if (!$instanceInvStockIssue) {
                $invStockIssue = DB::table('inventory_stock_issues')
                                   ->where([
                    ['inv_stock_id', $invStockID], ['sig_received_by', $sigReceivedBy]
                ])->first();
                $invStockIssueID = $invStockIssue->id;
            } else {
                $invStockIssueID = $instanceInvStockIssue->id;
            }

            foreach ($invStockItemIDs as $ctr => $invStockItemID) {
                $propStockNo = serialize(preg_split("@(\s*and\s*)?[/\s,&]+@", $propStockNos[$ctr]));
                $quantity = $quantities[$ctr];

                if ($classification == 'ics' || $classification == 'par') {
                    $dateIssued = $datesIssued[$ctr];
                } else {
                    $stockAvailable = $stockAvailables[$ctr];
                    $issuedRemark = $issuedRemarks[$ctr];
                    $dateIssued = Carbon::now();
                    $dataIssued = $dateIssued->format('Y-m-d');
                }

                if ($classification == 'ics') {
                    $estUsefulLife = $estUsefulLifes[$ctr];
                }

                $instanceInvStockIssueItem = InventoryStockIssueItem::where([
                    ['inv_stock_issue_id', $invStockIssueID],
                    ['inv_stock_item_id', $invStockItemID]
                ])->first();

                if (!$instanceInvStockIssueItem) {
                    $instanceInvStockIssueItem = new InventoryStockIssueItem;
                    $instanceInvStockIssueItem->inv_stock_id = $invStockID;
                    $instanceInvStockIssueItem->inv_stock_item_id = $invStockItemID;
                    $instanceInvStockIssueItem->inv_stock_issue_id = $invStockIssueID;
                }

                $instanceInvStockIssueItem->prop_stock_no = $propStockNo;
                $instanceInvStockIssueItem->date_issued = $dateIssued;
                $instanceInvStockIssueItem->quantity = $quantity;

                if ($classification == 'ics') {
                    $instanceInvStockIssueItem->est_useful_life = $estUsefulLife;
                } else if ($classification == 'ris') {
                    $instanceInvStockIssueItem->remarks = $issuedRemark;
                    $instanceInvStockIssueItem->stock_available = $stockAvailable;
                }

                $instanceInvStockIssueItem->save();
            }

            $routeName = 'stocks';

            $msg = "$documentType successfully issued.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName)
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    public function showUpdateIssueItem($invStockIssueID, $classification) {
        $invStockIssueData = InventoryStockIssue::with('invstocks')
                                                ->find($invStockIssueID);
        $invStockIssueItemData = InventoryStockIssueItem::with('invstockitems')
                                                        ->where('inv_stock_issue_id', $invStockIssueID)
                                                        ->get();
        $invStockID = $invStockIssueData->inv_stock_id;

        $invStockData = $invStockIssueData->invstocks;
        $prData = PurchaseRequest::find($invStockData->pr_id);
        $supplierData = Supplier::find($invStockData->supplier);
        $divisionData = EmpDivision::find($invStockData->division);

        foreach ($invStockIssueItemData as $item) {
            $stockItem = InventoryStockItem::with('stockissueditems')
                                           ->find($item->inv_stock_item_id);
            $item->available_quantity = $item->invstockitems->quantity;
            $item->prop_stock_no = unserialize($item->prop_stock_no);
            $item->prop_stock_no = implode(', ', $item->prop_stock_no);

            foreach ($stockItem->stockissueditems as $issuedItem) {
                $item->available_quantity -= $issuedItem['quantity'];
            }

            $unitIssueData = ItemUnitIssue::find($item->invstockitems->unit_issue);
            $item->unit = $unitIssueData->unit_name;
        }

        $requestedBy = $prData ? $prData->requested_by : NULL;
        $stocks = $invStockIssueItemData;
        $inventoryNo = $invStockData->inventory_no;
        $office = $invStockData->office;
        $division = $divisionData->division_name;
        $poNo = $invStockData->po_no;
        $poDate = $invStockData->date_po;
        $supplier = $supplierData->company_name;
        $unitIssue = $unitIssueData->unit_name;

        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();
        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }
        $employees = User::orderBy('firstname')->get();

        if ($classification == 'ris') {
            $sigApprovedBy = $invStockIssueData->sig_approved_by;
            $sigRequestedBy = $invStockIssueData->sig_requested_by;
            $sigReceivedBy = $invStockIssueData->sig_received_by;
            $sigIssuedBy = $invStockIssueData->sig_issued_by;

            return view('modules.inventory.stock.ris-update-issue', compact(
                'invStockID', 'classification',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees', 'office', 'division',
                'sigApprovedBy', 'sigRequestedBy', 'sigReceivedBy', 'sigIssuedBy'
            ));
        } else if ($classification == 'par') {
            $sigIssuedBy = $invStockIssueData->sig_issued_by;
            $sigReceivedBy = $invStockIssueData->sig_received_by;

            return view('modules.inventory.stock.par-update-issue', compact(
                'invStockID', 'classification',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees',
                'sigIssuedBy', 'sigReceivedBy'
            ));
        } else {
            $sigReceivedFrom = $invStockIssueData->sig_received_from;
            $sigReceivedBy = $invStockIssueData->sig_received_by;

            return view('modules.inventory.stock.ics-update-issue', compact(
                'invStockID', 'classification',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees',
                'sigReceivedFrom', 'sigReceivedBy'
            ));
        }
    }

    public function updateIssueItem(Request $request, $invStockID, $classification) {
        $invStockIssueItemIDs = $request->inv_stock_issue_item_id;
        $quantities = $request->quantity;
        $datesIssued = $request->date_issued;
        $propStockNos = $request->prop_stock_no;
        $sigReceivedBy = $request->sig_received_by;
        $deletes = $request->deleted;
        $excludes = $request->excluded;

        if ($classification == 'ris') {
            $documentType = 'Requisition and Issue Slip (RIS)';

            $stockAvailables = $request->stock_available;
            $issuedRemarks = $request->issued_remarks;
            $sigApprovedBy = $request->sig_approved_by;
            $sigRequestedBy = $request->sig_requested_by;
            $sigIssuedBy = $request->sig_issued_by;
        } else if ($classification == 'par') {
            $documentType = 'Property Aknowledgement Receipt (PAR)';

            $sigIssuedBy = $request->sig_issued_by;
        } else {
            $documentType = 'Inventory Custodian Slip (ICS)';

            $estUsefulLifes = $request->est_useful_life;
            $sigReceivedFrom = $request->sig_received_from;
        }

        try {
            $instanceInvStocks = InventoryStock::find($invStockID);
            $instanceInvStockIssue = InventoryStockIssue::where([
                ['inv_stock_id', $invStockID], ['sig_received_by', $sigReceivedBy]
            ])->first();
            $invStockIssueID = $instanceInvStockIssue->id;

            if ($classification == 'ris') {
                $instanceInvStockIssue->sig_requested_by = $sigRequestedBy;
                $instanceInvStockIssue->sig_approved_by = $sigApprovedBy;
                $instanceInvStockIssue->sig_issued_by = $sigIssuedBy;
                $instanceInvStockIssue->sig_received_by = $sigReceivedBy;
            } else if ($classification == 'par') {
                $instanceInvStockIssue->sig_issued_by = $sigIssuedBy;
                $instanceInvStockIssue->sig_received_by = $sigReceivedBy;
            } else {
                $instanceInvStockIssue->sig_received_from = $sigReceivedFrom;
                $instanceInvStockIssue->sig_received_by = $sigReceivedBy;
            }

            $instanceInvStockIssue->save();

            foreach ($invStockIssueItemIDs as $ctr => $invStockIssueItemID) {
                $propStockNo = serialize(preg_split("@(\s*and\s*)?[/\s,&]+@", $propStockNos[$ctr]));
                $quantity = $quantities[$ctr];
                $delete = $deletes[$ctr];
                $exclude = $excludes[$ctr];

                if ($classification == 'ics' || $classification == 'par') {
                    $dateIssued = $datesIssued[$ctr];
                } else {
                    $stockAvailable = $stockAvailables[$ctr];
                    $issuedRemark = $issuedRemarks[$ctr];
                    $dateIssued = Carbon::now();
                    $dataIssued = $dateIssued->format('Y-m-d');
                }

                if ($classification == 'ics') {
                    $estUsefulLife = $estUsefulLifes[$ctr];
                }

                $instanceInvStockIssueItem = InventoryStockIssueItem::find($invStockIssueItemID);

                if ($delete == 'n') {
                    $instanceInvStockIssueItem->prop_stock_no = $propStockNo;
                    $instanceInvStockIssueItem->date_issued = $dateIssued;
                    $instanceInvStockIssueItem->quantity = $quantity;
                    $instanceInvStockIssueItem->excluded = $exclude;

                    if ($classification == 'ics') {
                        $instanceInvStockIssueItem->est_useful_life = $estUsefulLife;
                    } else if ($classification == 'ris') {
                        $instanceInvStockIssueItem->remarks = $issuedRemark;
                        $instanceInvStockIssueItem->stock_available = $stockAvailable;
                    }

                    $instanceInvStockIssueItem->save();
                } else {
                    $instanceInvStockIssueItem->delete();
                }
            }

            $routeName = 'stocks';

            $msg = "$documentType successfully updated issued.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName)
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    public function showRecipients($id) {
        $invStockIssues = InventoryStockIssue::with(['invstocks', 'recipient'])
                                             ->where('inv_stock_id', $id)
                                             ->get();

        foreach ($invStockIssues as $invStockIssue) {
            $invClassData = InventoryClassification::find(
                $invStockIssue->invstocks->inventory_classification
            );

            $invStockIssue->classification = strtolower($invClassData->abbrv);
        }

        return view('modules.inventory.stock.recipient', compact(
            'id', 'invStockIssues'
        ));
    }


    /**
     * Soft deletes the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIssue(Request $request, $invStockIssueID) {
        try {
            $instanceInvStockIssue = InventoryStockIssue::find($invStockIssueID);
            $documentType = 'All issued items';
            InventoryStockIssueItem::where('inv_stock_issue_id', $invStockIssueID)->delete();
            $instanceInvStockIssue->forceDelete();

            $msg = "$documentType '$invStockIssueID' successfully deleted.";
            Auth::user()->log($request, $msg);

            return redirect()->route('stocks')
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('stocks')
                             ->with('failed', $msg);
        }
    }

    public function issue(Request $request, $id) {
        try {
            $instanceInvStock = InventoryStock::find($id);
            $instanceInvStock->status = 13;
            $instanceInvStock->save();

            $documentType = "Inventory Stocks";
            $msg = "$documentType '$id' successfully deleted.";
            Auth::user()->log($request, $msg);

            return redirect()->route('stocks')
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('stocks')
                             ->with('failed', $msg);
        }
    }
}
