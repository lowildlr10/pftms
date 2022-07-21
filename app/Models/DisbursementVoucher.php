<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class DisbursementVoucher extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'disbursement_vouchers';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_id',
        'ors_id',
        'dv_no',
        'payee',
        'date_dv',
        'date_disbursed',
        'fund_cluster',
        'payment_mode',
        'other_payment',
        'particulars',
        'responsibility_center',
        'mfo_pap',
        'prior_year',
        'continuing',
        'current',
        'amount',
        'sig_certified',
        'sig_accounting',
        'sig_agency_head',
        'date_accounting',
        'date_agency_head',
        'check_ada_no',
        'date_check_ada',
        'bank_name',
        'bank_account_no',
        'jev_no',
        'receipt_printed_name',
        'date_jev',
        'signature',
        'or_no',
        'other_documents',
        'module_class',
        'for_payment',
        'disbursed_by',
        'created_by',
        'funding_source',
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

    /**
     * Get the phone record associated with the purchase request
     */
    public function pr() {
        return $this->belongsTo('App\Models\PurchaseRequest', 'pr_id', 'id');
    }

    public function procors() {
        return $this->belongsTo('App\Models\ObligationRequestStatus', 'ors_id', 'id');
    }

    public function emppayee() {
        return $this->hasOne('App\Models\EmpAccount', 'id', 'payee');
    }

    public function bidpayee() {
        return $this->hasOne('App\Models\Supplier', 'id', 'payee');
    }

    public function custompayee() {
        return $this->hasOne('App\Models\CustomPayee', 'id', 'payee');
    }

    public $sortable = [
        'dv_no',
        'particulars',
    ];
}
