<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class SummaryLDDAP extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'summary_lddaps';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'date_for_approval',
        'date_approved',
        'date_for_submission_bank',
        'mds_gsb_id',
        'department',
        'entity_name',
        'operating_unit',
        'fund_cluster',
        'sliiae_no',
        'date_sliiae',
        'to',
        'bank_name',
        'bank_address',
        'sig_cert_correct',
        'sig_approved_by',
        'sig_delivered_by',
        'sig_received_by',
        'lddap_no_pcs',
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

    public $sortable = [
        'sliiae_no',
        'date_sliiae',
        'total_amount',
        'status',
    ];
}
