<?php

namespace App\Http\Controllers;

use App\PreventiveModel;
use App\Properties;
use Illuminate\Http\Request;
use App\Models\InventoryStockItem;
use App\Models\InventoryStock;
use App\Models\ItemUnitIssue;
use App\Models\InventoryClassification;
use App\Models\InventoryStockIssue;
use App\Models\Signatory;
use App\Models\InventoryStockIssueItem;
use App\Models\EmpAccount;
use App\Models\PurchaseRequest;
use App\Models\PurchaseJobOrderItem;
use DB;

class ParController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    $description = DB::table('inventory_stock_items')
    ->select('description', 'pr_no', 'inventory_no', 'inventory_stock_items.quantity', 'unit_cost', 'total_cost', 'date_po', 'inventory_stock_classifications.classification_name', 'firstname', 'lastname')
    ->join('item_classifications', 'inventory_stock_items.item_classification', '=', 'item_classifications.id')
    ->join('item_unit_issues', 'inventory_stock_items.unit_issue', '=', 'item_unit_issues.id')
    ->join('inventory_stocks', 'inventory_stock_items.inv_stock_id', '=', 'inventory_stocks.id')
    ->join('purchase_requests', 'purchase_requests.id', '=', 'inventory_stock_items.pr_id')
    ->join('inventory_stock_issues', 'inventory_stocks.id', '=', 'inventory_stock_issues.inv_stock_id')
    ->join('purchase_job_order_items', 'inventory_stock_items.po_item_id', '=', 'purchase_job_order_items.id')
    ->join('emp_accounts', 'inventory_stock_issues.sig_received_by', '=', 'emp_accounts.id')
    ->join('inventory_stock_classifications', 'inventory_stocks.inventory_classification', '=', 'inventory_stock_classifications.id')
        ->where('inventory_stock_classifications.classification_name', 'Property Aknowledgement Receipt (PAR)')
        ->get();
        // dd($description);
           return view('modules.inventory.Par.par', ['description' => $description]);
}



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
