<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class FundingAllotmentRealignment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funding_allotment_realignments';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'project_id',
        'budget_id',
        'allotment_id',
        'budget_realign_id',
        'order_no',
        'allotment_name',
        'realigned_allotment_cost',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
