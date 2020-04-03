<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class PurchaseJobOrder extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_job_orders';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'po_no',
        'pr_id',
        'date_po',
        'date_po_approved',
        'date_cancelled',
        'awarded_to',
        'place_delivery',
        'date_delivery',
        'delivery_term',
        'payment_term',
        'amount_words',
        'grand_total',
        'fund_cluster',
        'sig_department',
        'sig_approval',
        'sig_funds_available',
        'date_accountant_signed',
        'for_approval',
        'with_ors_burs',
        'status',
        'document_abrv'
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
}
