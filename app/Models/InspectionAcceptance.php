<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class InspectionAcceptance extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inspection_acceptance_reports';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'iar_no';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'iar_no',
        'po_no',
        'code',
        'pr_id',
        'ors_id',
        'date_iar',
        'invoice_no',
        'date_invoice',
        'sig_inspection',
        'sig_supply',
        'date_inspected',
        'inspection_remarks',
        'date_received',
        'acceptance_remarks',
        'specify_quantity',
        'remarks_recommendation',
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
