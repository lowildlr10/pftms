<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RIS extends Model
{
    public function stockissueditems() {
        return $this->hasMany('App\Models\InventoryStockIssueItem', 'inv_stock_item_id', 'id');
    }
    public function item_classification_data(){
        return $this->belongsTo(ItemClassification::Class,'item_classification');
    }
    public function item_unit_issues(){
        return $this->belongsTo(ItemUnitIssue::Class,'item_unit_issues');
    }
    public function inventory_stocks(){
        return $this->belongsTo(InventoryStock::Class,'inventory_stocks');
    }
    public function inventory_stock_classifications(){
        return $this->belongsTo(InventoryClassification::Class,'inventoryinventory_stock_classifications');
    }
    public function inventory_stock_issues(){
        return $this->belongsTo(InventoryStockIssue::Class,'inventory_stock_issues');
    }
    public function signatories(){
        return $this->belongsTo(Signatory::Class,'signatories');
    }
    public function inventory_stock_issue_items(){
        return $this->belongsTo(InventoryStockIssueItem::Class,'inventory_stock_issue_items');
    }
    public function emp_accounts(){
        return $this->belongsTo(EmpAccount::Class,'emp_accounts');
    }
    public function purchase_requests(){
        return $this->belongsTo(PurchaseRequest::Class,'purchase_requests');
    }
    public function purchase_job_order_items(){
        return $this->belongsTo(PurchaseJobOrderItem::Class,'purchase_job_order_items');
    }
}
