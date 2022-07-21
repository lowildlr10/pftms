<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Notifications\ObligationRequestStatus as Notif;
use App\Models\EmpAccount as User;
use App\Models\EmpRole as Role;
use App\Models\DocumentLog as DocLog;
use Kyslik\ColumnSortable\Sortable;

class ObligationRequestStatus extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'obligation_request_status';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_id',
        'po_no',
        'transaction_type',
        'document_type',
        'fund_cluster',
        'serial_no',
        'date_ors_burs',
        'date_obligated',
        'payee',
        'office',
        'address',
        'responsibility_center',
        'particulars',
        'mfo_pap',
        'uacs_object_code',
        'prior_year',
        'continuing',
        'current',
        'amount',
        'sig_certified_1',
        'sig_certified_2',
        'sig_accounting',
        'sig_agency_head',
        'obligated_by',
        'created_by',
        'date_certified_1',
        'date_certified_2',
        'date_received',
        'date_released',
        'module_class',
        'funding_source',
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

    /**
     * Get the phone record associated with the purchase request
     */
    public function pr() {
        return $this->belongsTo('App\Models\PurchaseRequest', 'pr_id', 'id');
    }

    public function po() {
        return $this->belongsTo('App\Models\PurchaseJobOrder', 'po_no', 'po_no');
    }

    public function procdv() {
        return $this->hasOne('App\Models\DisbursementVoucher', 'ors_id', 'id');
    }

    public function emppayee() {
        return $this->hasOne('App\Models\EmpAccount', 'id', 'payee');
    }

    public function bidpayee() {
        return $this->hasOne('App\Models\Supplier', 'id', 'payee');
    }

    public function custompayee() {
        return $this->hasOne('App\Models\CustomPayee', 'id', 'payee');
    }

    public $sortable = [
        'po_no',
        'serial_no',
        'particulars',
    ];

    public function notifyMessage($id, $from, $message) {
        $orsData = $this::with('po')->find($id);
        $poNo = isset($orsData->po_no) ? $orsData->po_no : NULL;
        $poID = isset($orsData->po->id) ? $orsData->id : NULL;
        $moduleClass = $orsData->module_class;

        $instanceUser = new User;
        $users = User::get();
        $documentType = $orsData->document_type;
        $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                         'Budget Utilization Request & Status';
        $fromName = $instanceUser->getEmployee($from)->name;
        $fromGroups = $instanceUser->getEmployee($from)->groups;
        $msgNotif = "$message - $fromName.";

        if ($moduleClass == 3) {
            $module = 'proc-ors-burs';
            $_module = 'proc_ors_burs';
        } else if ($moduleClass == 2) {
            $module = 'ca-ors-burs';
            $_module = 'ca_ors_burs';
        }

        foreach ($users as $user) {
            $notify = false;
            $data = (object) [
                'ors_id' => $id,
                'po_id' => $poID,
                'po_no' => $poNo,
                'module' => $module,
                'type' =>'message',
                'msg' => $msgNotif
            ];

            $groups = $instanceUser->getEmployee($user->id)->groups;
            $roles = $instanceUser->getEmployee($user->id)->roles;

            foreach ($roles as $role) {
                $roleData = Role::find($role);
                $jsonRole = json_decode($roleData->module_access);

                if (isset($jsonRole->{$_module}->receive)) {
                    if ($jsonRole->{$_module}->receive) {
                        foreach ($fromGroups as $group) {
                            if (in_array($group, $groups)) {
                                $notify = true;
                            }
                        }
                    }
                }
            }

            if ($notify) {
                $user->notify(new Notif($data));
            }
        }
    }
}
