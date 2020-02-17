<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisbursementVoucher extends Model
{
    use SoftDeletes;

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
        'code',
        'ors_id',
        'dv_no',
        'date_dv',
        'date_disbursed',
        'fund_cluster',
        'payment_mode',
        'particulars',
        'sig_accounting',
        'sig_agency_head',
        'date_accounting',
        'date_agency_head',
        'other_payment',
        'module_class',
        'for_payment',
        'document_abrv',
        'disbursed_by'
    ];
}