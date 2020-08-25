<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class MdsGsb extends Model
{

    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mds_gsb';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'branch',
        'sub_account_no',
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
        'branch',
        'sub_account_no',
    ];
}
