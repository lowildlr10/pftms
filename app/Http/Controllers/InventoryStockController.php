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
    public function index() {
        //
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
}
