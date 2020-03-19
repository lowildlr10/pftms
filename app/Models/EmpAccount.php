<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Models\EmpRole as Role;
use App\Models\EmpGroup as Group;
use App\Models\EmpLog as Log;

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
        'groups',
        'roles',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'position',
        'emp_type',
        'username',
        'email',
        'address',
        'mobile_no',
        'is_active',
        'avatar',
        'signature'
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

    public function hasModuleAccess($roleIDs, $module, $access) {
        $roleIDs = unserialize($roleIDs);

        if (!is_array($roleIDs) && empty($roleIDs)) {
            return false;
        }

        foreach ($roleIDs as $roleID) {
            $roleData = Role::find($roleID);
            $jsonRole = json_decode($roleData->module_access);

            if (!isset($jsonRole->{$module})) {
                return false;
            }

            if (!isset($jsonRole->{$module}->{$access})) {
                return false;
            }

            if ($jsonRole->{$module}->{$access}) {
                return true;
            }
        }

        return false;
    }

    public function getModuleAccess($module, $action) {
        return $this->hasModuleAccess($this->roles, $module, $action);
    }

    public function getDivisionAccess() {
        $divisionAccess = [];
        $userGroups = !empty($this->groups) ? unserialize($this->groups) : [];
        $empGroupData = Group::whereIn('id', $userGroups)->get();

        if (!empty($empGroupData)) {
            foreach ($empGroupData as $group) {
                $_divisionAccess = !empty($group->division_access) ?
                                   unserialize($group->division_access) : [];

                if (!empty($_divisionAccess)) {
                    foreach ($_divisionAccess as $access) {
                        $divisionAccess[] = $access;
                    }
                }
            }
        }

        return $divisionAccess;
    }

    public function hasOrdinaryRole() {
        $roles = !empty($this->roles) ? unserialize($this->roles) : [];

        if (empty($roles)) {
            return true;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_ordinary == 'n') {
                    return false;
                }
            }
        }

        return true;
    }

    public function log($request, $msg) {
        $instanceEmpLog = new Log;
        $info = $request->header('User-Agent');

        dd($request);

        //$instanceEmpLog->save();
    }
}
