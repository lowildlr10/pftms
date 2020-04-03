<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class AbstractQuotationItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'abstract_quotation_items';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'abstract_id',
        'pr_id',
        'pr_item_id',
        'supplier',
        'specification',
        'remarks',
        'unit_cost',
        'total_cost'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
