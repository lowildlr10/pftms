<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class SummaryLDDAPItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_lddap_items';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'sliiae_id',
        'item_no',
        'lddap_id',
        'date_issue',
        'total',
        'allotment_ps',
        'allotment_mooe',
        'allotment_co',
        'allotment_fe',
        'allotment_remarks',
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

    public $sortable = [
        'date_lddap',
        'lddap_ada_no',
        'nca_no',
        'status',
        'total_amount'
    ];
}
