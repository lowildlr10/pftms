<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class InventoryStockIssueItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_stock_issue_items';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'inv_stock_id',
        'inv_stock_item_id',
        'inv_stock_issue_id',
        'prop_stock_no',
        'quantity',
        'remarks',
        'excluded',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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

    public function invstockitems() {
        return $this->belongsTo('App\Models\InventoryStockItem', 'inv_stock_item_id', 'id');
    }

    public function invstockissue() {
        return $this->belongsTo('App\Models\InventoryStockIssue', 'inv_stock_issue_id', 'id');
    }
}
