<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class FundingAllotment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funding_allotments';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'project_id',
        'budget_id',
        'allotment_class',
        'order_no',
        'allotment_name',
        'allotment_cost',
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
