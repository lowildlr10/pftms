<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class RegAllotmentItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funding_reg_allotment_items';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'reg_allotment_id',
        'ors_id',
        'order_no',
        'date_received',
        'date_obligated',
        'date_released',
        'payee',
        'particulars',
        'serial_number',
        'uacs_object_code',
        'allotments',
        'obligations',
        'unobligated_allot',
        'disbursement',
        'due_demandable',
        'not_due_demandable',
        'is_excluded'
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
