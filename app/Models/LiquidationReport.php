<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class LiquidationReport extends Model
{
    use SoftDeletes;

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
        'code',
        'period_covered',
        'entity_name',
        'serial_no',
        'fund_cluster',
        'date_liquidation',
        'responsibility_center',
        'particulars',
        'amount',
        'total_amount',
        'amount_cash_dv'.
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
}
