<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class PurchaseJobOrderItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_job_order_items';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'po_no',
        'pr_id',
        'pr_item_id',
        'item_no',
        'stock_no',
        'quantity',
        'unit_issue',
        'item_description',
        'unit_cost',
        'total_cost',
        'excluded'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function boot() {
         parent::boot();
         self::creating(function($model) {
             $model->id = self::generateUuid();
         });
    }

    public static function generateUuid() {
         return Uuid::generate();
    }
}
