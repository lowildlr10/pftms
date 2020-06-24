<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class ListDemandPayable extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'list_demand_payables';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'dv_id',
        'date_for_approval',
        'date_approved',
        'department',
        'entity_name',
        'operating_unit',
        'nca_no',
        'lddap_ada_no',
        'date_lddap',
        'fund_cluster',
        'mds_gsb_accnt_no',
        'sig_cert_correct',
        'sig_approval_1',
        'sig_approval_2',
        'sig_approval_3',
        'sig_agency_auth_1',
        'sig_agency_auth_2',
        'sig_agency_auth_3',
        'sig_agency_auth_4',
        'total_amount_words',
        'total_amount',
        'status'
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
    public function dv() {
        return $this->hasOne('App\Models\DisbursementVoucher', 'id', 'dv_id');
    }

    public $sortable = [
        'date_lddap',
        'lddap_ada_no',
        'nca_no',
        'status',
        'total_amount'
    ];
}
