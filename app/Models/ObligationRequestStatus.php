<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class ObligationRequestStatus extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'obligation_request_status';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'code',
        'pr_id',
        'po_no',
        'transaction_type',
        'document_type',
        'fund_cluster',
        'serial_no',
        'date_ors_burs',
        'date_obligated',
        'payee',
        'office',
        'address',
        'responsibility_center',
        'particulars',
        'mfo_pap',
        'uacs_object_code',
        'amount',
        'sig_certified_1',
        'sig_certified_2',
        'sig_accounting',
        'sig_agency_head',
        'obligated_by',
        'date_certified_1',
        'date_certified_2',
        'module_class'
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
