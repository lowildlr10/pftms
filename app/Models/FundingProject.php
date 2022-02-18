<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;
use Auth;

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
        'project_site',
        'implementing_agency',
        'implementing_project_cost',
        'comimplementing_agency_lgus',
        'proponent_units',
        'date_from',
        'date_to',
        'project_cost',
        'project_leader',
        'project_title',
        'monitoring_offices',
        'access_groups',
        'project_type',
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

    public function ledger() {
        return $this->hasOne('App\Models\FundingLedger', 'project_id', 'id');
    }

    public function budget() {
        return $this->hasOne('App\Models\FundingBudget', 'project_id', 'id');
    }

    public function budgetrealigns() {
        return $this->hasMany('App\FundingAllotmentRealignment', 'project_id', 'id');
    }

    public function allotments() {
        return $this->hasMany('App\FundingAllotment', 'project_id', 'id');
    }

    public function allotrealigns() {
        return $this->hasMany('App\FundingAllotmentRealignment', 'project_id', 'id');
    }

    public function site() {
        return $this->hasOne('App\Models\Municipality', 'project_site', 'id');
    }

    public function implementing() {
        return $this->hasOne('App\Models\AgencyLGU', 'implementing_agency', 'id');
    }

    public function getAccessibleProjects() {
        $projectIDs = [];
        $userID = Auth::user()->id;
        $userUnit = Auth::user()->unit;
        $userGroups = Auth::user()->groups ? unserialize(Auth::user()->groups) : [];
        $fundProject = $this->get();

        foreach ($fundProject as $project) {
            $createdBy = $project->created_by;
            $units = $project->proponent_units ? unserialize($project->proponent_units) :
                     [];
            $accessGroups = $project->access_groups ? unserialize($project->access_groups) :
                            [];

            foreach ($accessGroups as $accessGrp) {
                if (in_array($accessGrp, $userGroups)) {
                    $projectIDs[] = $project->id;
                }
            }

            foreach ($units as $unit) {
                if ($unit == $userUnit) {
                    $projectIDs[] = $project->id;
                }
            }

            if ($createdBy == $userID) {
                $projectIDs[] = $project->id;
            }
        }

        return $projectIDs;
    }
}
