<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class FundingProject extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funding_projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'industry_sector',
        'project_site ',
        'proponent_units',
        'comimplementing_agency_lgus',
        'date_from',
        'date_to',
        'project_cost',
        'project_title',
        'industry_sector',
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
        'date_from',
        'date_to',
        'project_cost',
        'project_title',
    ];

    public function budget() {
        return $this->hasOne('App\Models\FundingBudget', 'project_id', 'id')
                    ->where('is_active', 'y');
    }
}