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

    /**
     * Get the phone record associated with the purchase request
     */
    public function pr() {
        return $this->belongsTo('App\Models\PurchaseRequest', 'pr_id', 'id');
    }

    public function pritem() {
        return $this->belongsTo('App\Models\PurchaseRequestItem', 'pr_item_id', 'id');
    }

    public function po() {
        return $this->belongsTo('App\Models\PurchaseJobOrder', 'po_no', 'po_no');
    }

    public function unitissue() {
        return $this->hasOne('App\Models\ItemUnitIssue', 'id', 'unit_issue');
    }
}
