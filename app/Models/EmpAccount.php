<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Models\EmpRole as Role;
use App\Models\EmpGroup as Group;
use App\Models\EmpLog as Log;
use Kyslik\ColumnSortable\Sortable;

class EmpAccount extends Authenticatable
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
        'lastname',
        'position',
        'emp_type',
        'emp_id',
        'is_active',
        'last_login'
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

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_developer == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasAdministratorRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_administrator == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasRdRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_rd == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasArdRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_ard == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasPstdRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_pstd == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasPlanningRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_planning == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasProjectStaffRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_project_staff == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasAccountantRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_accountant == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasBudgetRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_budget == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasCashierRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_cashier == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasPropertySupplyRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_property_supply == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasOrdinaryRole($userID = '') {
        if (empty($userID)) {
            $roles = !empty($this->roles) ? unserialize($this->roles) : [];
        } else {
            $userData = $this::find($userID);
            $roles = !empty($userData->roles) ? unserialize($userData->roles) : [];
        }

        if (count($roles) == 0) {
            return false;
        } else {
            foreach ($roles as $role) {
                $roleData = Role::find($role);

                if ($roleData->is_ordinary == 'y') {
                    return true;
                }
            }
        }

        return false;
    }

    public function getEmployee($id) {
        $userData = EmpAccount::find($id);

        if ($userData) {
            $empID = $userData->emp_id;
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
            $roleNames = [];

            $roledat = Role::whereIn('id', $roles)->orderBy('role')->get();

            foreach ($roledat as $role) {
                $roleNames[] = $role->role;
            }

            $roleName = count($roleNames) > 0 ? implode(', ', $roleNames) : '';
        } else {
            $empID = NULL;
            $fullname = NULL;
            $position = NULL;
            $signature = NULL;
            $groups = [];
            $roles = [];
            $roleName = '';
        }

        return (object) [
            'emp_id' => $empID,
            'name' => $fullname,
            'position' => $position,
            'signature' => $signature,
            'groups' => $groups,
            'roles' => $roles,
            'roleName' => $roleName,
        ];
    }

    public function getGroups($id) {
        $userData = $this::find($id);
        $groups = !empty($userData->groups) ? unserialize($userData->groups) :
                  NULL;

        return $groups;
    }

    public function getGroupHeads($userID = '') {
        $groupHeads = [];

        if (!empty($userID)) {
            $userData = $this::find($userID);
        } else {
            $userData = $this;
        }

        $groups = $userData->groups ? unserialize($userData->groups) : [];

        if (count($groups) > 0) {
            foreach ($groups as $group) {
                $groupDat = Group::find($group);
                $groupHeads[] = $groupDat->group_head;
            }
        }

        return $groupHeads;
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

    public function div() {
        return $this->hasOne('App\Models\EmpDivision', 'id', 'division');
    }
}
