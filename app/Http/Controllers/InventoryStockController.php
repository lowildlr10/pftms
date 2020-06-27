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

use App\User;
use App\Models\DocumentLog as DocLog;
use App\Models\InventoryClassification;
use App\Models\ItemClassification;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\Signatory;
use App\Models\ItemUnitIssue;
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
                              ->orWhere('stock_available', 'like', "%$keyword%")
                              ->orWhere('amount', 'like', "%$keyword%")
                              ->orWhere('est_useful_life', 'like', "%$keyword%");
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

    public function showIssueItem($invStockID, $invStockItemID, $classification, $type) {
        if ($type == 'multiple') {
            $invStockData = InventoryStock::with('stockitems')->find($invStockID);
        } else {
            $invStockData = InventoryStock::with(['stockitems' => function($query) use ($invStockItemID) {
                $query->where('id', $invStockItemID);
            }])->find($invStockID);
        }

        $supplierData = Supplier::find($invStockData->supplier);

        foreach ($invStockData->stockitems as $item) {
            $stockItem = InventoryStockItem::with('stockissueditems')
                                           ->find($item->id);
            $item->available_quantity = $stockItem->quantity;
            $unitIssueData = ItemUnitIssue::find($item->unit_issue);
            $item->unit = $unitIssueData->unit_name;

            foreach ($stockItem->stockissueditems as $issuedItem) {
                $item->available_quantity -= $issuedItem->quantity;
            }
        }

        $stocks = $invStockData->stockitems;
        $inventoryNo = $invStockData->inventory_no;
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
            return view('modules.inventory.stock.ris-issue', compact(
                'invStockID', 'invStockItemID', 'classification', 'type',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees'
            ));
        } else if ($classification == 'par') {
            return view('modules.inventory.stock.par-issue', compact(
                'invStockID', 'invStockItemID', 'classification', 'type',
                'invStockID', 'invStockItemID', 'classification', 'type',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees'
            ));
        } else {
            return view('modules.inventory.stock.ics-issue', compact(
                'invStockID', 'invStockItemID', 'classification', 'type',
                'stocks', 'inventoryNo', 'poNo', 'poDate', 'supplier',
                'unitIssue', 'signatories', 'employees'
            ));
        }
    }

    public function storeIssueItem($invStockID, $invStockItemID, $classification, $type) {

    }
}
