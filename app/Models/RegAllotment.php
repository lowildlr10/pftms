<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class RegAllotment extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funding_reg_allotments';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'period_ending',
        'entity_name',
        'fund_cluster',
        'legal_basis',
        'mfo_pap',
        'sheet_no',
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
