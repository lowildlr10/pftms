<?php

namespace App\Http\Controllers;

use App\RIS;
use App\PreventiveModel;
use App\Properties;
use Illuminate\Http\Request;
use App\Models\InventoryStockItem;
use App\Models\InventoryStock;
use App\Models\ItemUnitIssue;
use App\Models\InventoryClassification;
use App\Models\InventoryStockIssue;
use App\Models\PurchaseRequest;
use App\Models\PurchaseJobOrderItem;
use DB;

class RisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $_ris = DB::table('inventory_stock_items')
        ->select('description', 'inventory_no', 'inventory_stock_items.quantity', 'unit_cost', 'total_cost', 'date_po', 'inventory_stock_classifications.classification_name', 'firstname', 'lastname')
        ->join('item_classifications', 'inventory_stock_items.item_classification', '=', 'item_classifications.id')
        ->join('item_unit_issues', 'inventory_stock_items.unit_issue', '=', 'item_unit_issues.id')
        ->join('inventory_stocks', 'inventory_stock_items.inv_stock_id', '=', 'inventory_stocks.id')
        ->join('inventory_stock_issues', 'inventory_stocks.id', '=', 'inventory_stock_issues.inv_stock_id')
        ->join('purchase_job_order_items', 'inventory_stock_items.po_item_id', '=', 'purchase_job_order_items.id')
        ->join('emp_accounts', 'inventory_stock_issues.sig_received_by', '=', 'emp_accounts.id')
        ->join('inventory_stock_classifications', 'inventory_stocks.inventory_classification', '=', 'inventory_stock_classifications.id')
        ->where('inventory_stock_classifications.classification_name', 'Requisition and Issue Slip (RIS)')
        ->get();
        return view('modules.inventory.RIS.ris', ['_ris' => $_ris]);
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
     * @param  \App\RIS  $rIS
     * @return \Illuminate\Http\Response
     */
    public function show(RIS $rIS)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RIS  $rIS
     * @return \Illuminate\Http\Response
     */
    public function edit(RIS $rIS)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RIS  $rIS
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RIS $rIS)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RIS  $rIS
     * @return \Illuminate\Http\Response
     */
    public function destroy(RIS $rIS)
    {
        //
    }
}
