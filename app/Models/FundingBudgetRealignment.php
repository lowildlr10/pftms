<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class FundingBudgetRealignment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funding_budget_realignments';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'project_id',
        'budget_id',
        'date_realignment',
        'date_approved',
        'date_disapproved',
        'approved_realigned_budget',
        'realignment_order',
        'created_by',
        'sig_submitted_by',
        'sig_approved_by',
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

    public function allotmentrealigments() {
        return $this->hasMany('App\FundingAllotmentRealignment', 'budget_realign_id', 'id');
    }
}
