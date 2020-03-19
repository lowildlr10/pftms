<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class EmpLog extends Model
{
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
}
