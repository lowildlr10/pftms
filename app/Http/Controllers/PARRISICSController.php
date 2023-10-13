<?php

namespace App\Http\Controllers;

use App\PARRISICS;
use Illuminate\Http\Request;
use App\Properties;
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
use App\Models\FundingProject;
use App\Models\IndustrySector;
use DB;

class PARRISICSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parrisics = DB::table('inventory_stock_items')
        ->select('description', 'pr_no', 'inventory_no', 'inventory_stock_items.quantity', 'unit_cost', 'total_cost', 'sector_name', 'date_po', 'inventory_stock_classifications.classification_name', 'firstname', 'lastname')
        ->leftJoin('item_classifications', 'inventory_stock_items.item_classification', '=', 'item_classifications.id')
        ->leftJoin('item_unit_issues', 'inventory_stock_items.unit_issue', '=', 'item_unit_issues.id')
        ->leftJoin('inventory_stocks', 'inventory_stock_items.inv_stock_id', '=', 'inventory_stocks.id')
        ->leftJoin('purchase_requests', 'purchase_requests.id', '=', 'inventory_stock_items.pr_id')

        ->leftJoin('funding_projects', 'funding_projects.id', '=', 'purchase_requests.funding_source')
        ->leftJoin('industry_sectors', 'funding_projects.industry_sector', '=', 'industry_sectors.id')

        ->leftJoin('inventory_stock_issues', 'inventory_stocks.id', '=', 'inventory_stock_issues.inv_stock_id')
        ->leftJoin('purchase_job_order_items', 'inventory_stock_items.po_item_id', '=', 'purchase_job_order_items.id')
        ->leftJoin('emp_accounts', 'inventory_stock_issues.sig_received_by', '=', 'emp_accounts.id')
        ->leftJoin('inventory_stock_classifications', 'inventory_stocks.inventory_classification', '=', 'inventory_stock_classifications.id')
        ->get();
        // dd($description);
           return view('modules.inventory.ParRisIcs.parrisics', ['parrisics' => $parrisics]);
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
     * @param  \App\PARRISICS  $pARRISICS
     * @return \Illuminate\Http\Response
     */
    public function show(PARRISICS $pARRISICS)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PARRISICS  $pARRISICS
     * @return \Illuminate\Http\Response
     */
    public function edit(PARRISICS $pARRISICS)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PARRISICS  $pARRISICS
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PARRISICS $pARRISICS)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PARRISICS  $pARRISICS
     * @return \Illuminate\Http\Response
     */
    public function destroy(PARRISICS $pARRISICS)
    {
        //
    }
}
