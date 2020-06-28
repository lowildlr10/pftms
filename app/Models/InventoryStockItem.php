<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class InventoryStockItem extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_stock_items';

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
        'po_item_id',
        'item_classification',
        'unit_issue',
        'description',
        'quantity',
        'stock_available',
        'amount',
        'est_useful_life',
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

    public function stockissueditems() {
        return $this->hasMany('App\Models\InventoryStockIssueItem', 'inv_stock_item_id', 'id');
    }
}
