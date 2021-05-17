<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class FundingBudget extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funding_budgets';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'project_id',
        'date_approved',
        'date_disapproved',
        'date_from',
        'date_to',
        'approved_budget',
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

    public $sortable = [
        'project_id',
        'date_disapproved',
        'date_approved',
        'date_from',
        'date_to',
        'approved_budget',
        'is_active',
        'created_by',
        'approved_by',
        'disapproved_by',
    ];

    public function project() {
        return $this->HasOne('App\Models\FundingProject', 'id', 'project_id');
    }

    public function budgets() {
        return $this->hasMany('App\Models\FundingBudget', 'id', 'budget_id');
    }

    public function allotments() {
        return $this->hasMany('App\Models\FundingAllotment', 'budget_id', 'id');
    }

    public function currentrealignment() {
        return $this->HasOne('App\Models\FundingBudgetRealignment', 'id', 'budget_id')
                    ->whereNotNull('date_approved')
                    ->orderBy('realignment_order', 'desc');
    }

    public function realignments() {
        return $this->hasMany('App\Models\FundingBudgetRealignment', 'budget_id', 'id')
                    ->orderBy('realignment_order');
    }
}
