<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class PurchaseRequestItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_request_items';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_id',
        'item_no',
        'quantity',
        'unit_issue',
        'item_description',
        'est_unit_cost',
        'est_total_cost',
        'awarded_to',
        'awarded_remarks',
        'group_no',
        'document_type'
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
}
