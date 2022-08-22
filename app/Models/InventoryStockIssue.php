<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class InventoryStockIssue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_stock_issues';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'inv_stock_id',
        'pr_id',
        'po_id',
        'sig_requested_by',
        'sig_approved_by',
        'sig_issued_by',
        'sig_received_from',
        'sig_received_by'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public static function boot() {
         parent::boot();
         self::creating(function($model) {
             $model->id = self::generateUuid();
         });
    }

    public static function generateUuid() {
         return Uuid::generate();
    }

    public function invstocks() {
        return $this->belongsTo('App\Models\InventoryStock', 'inv_stock_id', 'id');
    }

    public function recipient() {
        return $this->hasOne('App\Models\EmpAccount', 'id', 'sig_received_by');
    }

    public function stockissueitems() {
        return $this->hasMany('App\Models\InventoryStockIssueItem', 'inv_stock_issue_id', 'id')->orderBy('item_no');
    }

}
