<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class ListDemandPayableItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'list_demand_payable_items';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'lddap_id',
        'item_no',
        'category',
        'creditor_name',
        'creditor_acc_no',
        'ors_no',
        'allot_class_uacs',
        'gross_amount',
        'withold_tax',
        'net_amount',
        'remarks'
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
}
