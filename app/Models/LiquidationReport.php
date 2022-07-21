<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class LiquidationReport extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'liquidation_reports';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'period_covered',
        'entity_name',
        'serial_no',
        'fund_cluster',
        'date_liquidation',
        'responsibility_center',
        'particulars',
        'amount',
        'total_amount',
        'amount_cash_dv',
        'or_no',
        'or_dtd',
        'amount_refunded',
        'amount_reimbursed',
        'sig_claimant',
        'sig_supervisor',
        'sig_accounting',
        'date_claimant',
        'date_supervisor',
        'date_accounting',
        'jev_no',
        'dv_id',
        'dv_dtd',
        'liquidated_by',
        'created_by',
        'date_liquidated'
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
     * Get the phone record associated with the...
     */
    public function dv() {
        return $this->hasOne('App\Models\DisbursementVoucher', 'id', 'dv_id');
    }

    public function empclaimant() {
        return $this->hasOne('App\Models\EmpAccount', 'id', 'sig_claimant');
    }

    public function bidclaimant() {
        return $this->hasOne('App\Models\Supplier', 'id', 'sig_claimant');
    }

    public function customclaimant() {
        return $this->hasOne('App\Models\CustomPayee', 'id', 'sig_claimant');
    }

    public $sortable = [
        'serial_no',
        'particulars',
    ];
}
