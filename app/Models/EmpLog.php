<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class EmpLog extends Model
{
    use Sortable;

    const CREATED_AT = 'logged_at';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emp_logs';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'emp_id',
        'request',
        'method',
        'host',
        'user_agent',
        'remarks'
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

    public function getUpdatedAtColumn() {
        return null;
    }

    public function employee() {
        return $this->hasOne('App\Models\EmpAccount', 'id', 'emp_id');
    }

    public $sortable = [
        'request',
        'method',
        'host',
        'user_agent',
        'remarks',
        'logged_at'
    ];
}
