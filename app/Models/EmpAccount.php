<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emp_accounts';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'emp_id',
        'division',
        'province',
        'region',
        'group',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'position',
        'emp_type',
        'username'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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

    public function roles()
    {
        return $this->belongsToMany('App\Models\EmpRole', 'emp_accounts', 'id', 'role');
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }

        return false;
    }

    public function isActive() {
        return $this->active;
    }

    public function hasRole($role)
    {
        if ($this->roles()->where('emp_roles.role', $role)->first()) {
            return true;
        }

        return false;
    }

    public function hasModuleAccess($role, $module, $access) {
        $roleData = $this->roles()->where('emp_roles.id', $role)->first();
        $jsonRole = json_decode($roleData->module_access);

        /*
        if (strtolower($roleData->role) === 'developer') {
            return true;
        }*/

        if (!isset($jsonRole->{$module})) {
            return false;
        }

        if (!isset($jsonRole->{$module}->{$access})) {
            return false;
        }

        return $jsonRole->{$module}->{$access} ? true : false;
    }
}
