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
// Selects the columns we need for the output. Qualified column names with aliases to avoid ambiguity.
->select('description', 'pr_no', 'inventory_no', 'inventory_stock_items.quantity', 'unit_cost', 'total_cost', 'date_po', 'inventory_stock_classifications.classification_name', 'firstname', 'lastname')
->join('item_classifications', 'inventory_stock_items.item_classification', '=', 'item_classifications.id') // Joins the item_classifications table to inventory_stock_items on the foreign key relationship.
->join('item_unit_issues', 'inventory_stock_items.unit_issue', '=', 'item_unit_issues.id') // Joins item_unit_issues to inventory_stock_items on the foreign key.
->join('inventory_stocks', 'inventory_stock_items.inv_stock_id', '=', 'inventory_stocks.id') // Joins inventory_stocks to inventory_stock_items on the foreign key.
->join('purchase_requests', 'purchase_requests.id', '=', 'inventory_stock_items.pr_id') // Joins purchase_requests to inventory_stock_items on the foreign key pr_id.
->join('inventory_stock_issues', 'inventory_stocks.id', '=', 'inventory_stock_issues.inv_stock_id') // Joins inventory_stock_issues to inventory_stocks on the foreign key.
->join('purchase_job_order_items', 'inventory_stock_items.po_item_id', '=', 'purchase_job_order_items.id') // Joins purchase_job_order_items to inventory_stock_items on the foreign key.
->join('emp_accounts', 'inventory_stock_issues.sig_received_by', '=', 'emp_accounts.id') // Joins emp_accounts to inventory_stock_issues on the foreign key.
->join('inventory_stock_classifications', 'inventory_stocks.inventory_classification', '=', 'inventory_stock_classifications.id') // Joins inventory_stock_classifications on the foreign key.
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
