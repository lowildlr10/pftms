<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class AgencyLGU extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agency_lgus';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'region',
        'province',
        'municipality',
        'agency_name',
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
        'agency_name',
    ];

    public function region() {
        return $this->hasOne('App\Models\Region', 'id', 'region');
    }

    public function province() {
        return $this->hasOne('App\Models\Province', 'id', 'province');
    }

    public function municipality() {
        return $this->hasOne('App\Models\Municipality', 'id', 'municipality');
    }
}
