<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class PurchaseRequest extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_requests';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_no',
        'code',
        'date_pr',
        'date_pr_approved',
        'date_pr_disapproved',
        'date_pr_cancelled',
        'funding_source',
        'requested_by',
        'office',
        'responsibility_center',
        'division',
        'approved_by',
        'sig_app',
        'sig_funds_available',
        'recommended_by',
        'purpose',
        'remarks',
        'status',
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
