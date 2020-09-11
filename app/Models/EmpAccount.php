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
use Kyslik\ColumnSortable\Sortable;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, Sortable;

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

    public $sortable = [
        'firstname',
        'middlename',
        'lastname'
    ];

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

    public function hasDeveloperRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (empty($roles)) {
            return true;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_developer == 'n') {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    public function hasAdministratorRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (empty($roles)) {
            return true;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_administrator == 'n') {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    public function hasAccountantRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (empty($roles)) {
            return true;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_accountant == 'n') {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    public function hasBudgetRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (empty($roles)) {
            return true;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_budget == 'n') {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    public function hasCashierRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (empty($roles)) {
            return true;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_cashier == 'n') {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    public function hasPropertySupplyRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (empty($roles)) {
            return true;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_property_supply == 'n') {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    public function hasOrdinaryRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (empty($roles)) {
            return true;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_ordinary == 'n') {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    public function getEmployee($id) {
        $userData = User::find($id);

        if ($userData) {
            $firstname = $userData->firstname;
            $middleInitial = !empty($userData->middlename) ?
                            ' '.$userData->middlename[0].'. ' : ' ';
            $lastname = $userData->lastname;
            $fullname = $firstname.$middleInitial.$lastname;
            $position = $userData->position;
            $signature = $userData->signature;

            $groups = !empty($userData->groups) ?
                      unserialize($userData->groups) :
                      [];
            $roles = !empty($userData->roles) ?
                     unserialize($userData->roles) :
                     [];
        } else {
            $fullname = NULL;
            $position = NULL;
            $signature = NULL;
            $groups = [];
            $roles = [];
        }

        return (object) [
            'name' => $fullname,
            'position' => $position,
            'signature' => $signature,
            'groups' => $groups,
            'roles' => $roles,
        ];
    }

    public function getGroups($id) {
        $userData = $this::find($id);
        $groups = !empty($userData->groups) ? unserialize($userData->groups) :
                  NULL;

        return $groups;
    }

    public function log($request, $msg) {
        $empID = $this->id;
        $requestURI = $request->getRequestUri();
        $method = $request->getMethod();
        $host = $request->header('host');
        $userAgent = $request->header('User-Agent');

        $instanceEmpLog = new Log;
        $instanceEmpLog->emp_id = $empID;
        $instanceEmpLog->request = $requestURI;
        $instanceEmpLog->method = $method;
        $instanceEmpLog->host = $host;
        $instanceEmpLog->user_agent = $userAgent;
        $instanceEmpLog->remarks = $msg;
        $instanceEmpLog->save();
    }
}
