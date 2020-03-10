<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Supplier extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'suppliers';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'classification',
        'company_name',
        'date_filed',
        'address',
        'bank_name',
        'account_name',
        'account_no',
        'email',
        'website_url',
        'telephone_no',
        'fax_no',
        'mobile_no',
        'date_established',
        'vat_no',
        'contact_person',
        'nature_business',
        'nature_business_others',
        'delivery_vehicle_no',
        'product_lines',
        'credit_accomodation',
        'attachment',
        'attachment_others',
        'is_active',
        'blacklisted_at'
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
