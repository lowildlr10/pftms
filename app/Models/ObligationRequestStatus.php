<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Notifications\ObligationRequestStatus as Notif;
use App\User;
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
        'code',
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
        'amount',
        'sig_certified_1',
        'sig_certified_2',
        'sig_accounting',
        'sig_agency_head',
        'obligated_by',
        'date_certified_1',
        'date_certified_2',
        'date_received',
        'date_released',
        'module_class'
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
    public function po() {
        return $this->belongsTo('App\Models\PurchaseJobOrder', 'po_no', 'po_no');
    }

    public $sortable = [
        'serial_no',
        'particulars',
    ];

    public function notifyIssued($id, $issuedBy) {
        $orsData = $this::with('po')->find($id);
        $poNo = $orsData->po_no;
        $poID = $orsData->po->id;
        $moduleClass = $orsData->module_class;

        $instanceUser = new User;
        $users = User::get();
        $documentType = $orsData->document_type;
        $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                         'Budget Utilization Request & Status';
        $issuedByName = $instanceUser->getEmployee($issuedBy)->name;
        $issuedByGroups = $instanceUser->getEmployee($issuedBy)->groups;
        $msgNotif = "$documentType '$id' has been issued to you by $issuedByName.";

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
                'type' =>'issued',
                'msg' => $msgNotif
            ];

            $groups = $instanceUser->getEmployee($user->id)->groups;
            $roles = $instanceUser->getEmployee($user->id)->roles;

            foreach ($roles as $role) {
                $roleData = Role::find($role);
                $jsonRole = json_decode($roleData->module_access);

                if (isset($jsonRole->{$_module}->receive)) {
                    if ($jsonRole->{$_module}->receive) {
                        foreach ($issuedByGroups as $group) {
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

    public function notifyIssuedBack($id, $issuedBackBy) {
        $instanceDocLog = new DocLog;
        $orsData = $this::with('po')->find($id);
        $poNo = $orsData->po_no;
        $poID = $orsData->po->id;
        $moduleClass = $orsData->module_class;

        $instanceUser = new User;
        $documentType = $orsData->document_type;
        $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                         'Budget Utilization Request & Status';
        $issuedBackByName = $instanceUser->getEmployee($issuedBackBy)->name;
        $msgNotif = "$documentType '$id' has been issued back to you by $issuedBackByName.";

        $docStatus = $instanceDocLog->checkDocStatus($id);
        $issuedBy = $docStatus->issued_by_id;
        $user = User::find($issuedBy);

        if ($moduleClass == 3) {
            $module = 'proc-ors-burs';
        } else if ($moduleClass == 2) {
            $module = 'ca-ors-burs';
        }

        $data = (object) [
            'ors_id' => $id,
            'po_id' => $poID,
            'po_no' => $poNo,
            'module' => $module,
            'type' =>'issued_back',
            'msg' => $msgNotif
        ];

        $user->notify(new Notif($data));
    }

    public function notifyReceived($id, $receivedBy) {
        $instanceDocLog = new DocLog;
        $orsData = $this::with('po')->find($id);
        $poNo = $orsData->po_no;
        $poID = $orsData->po->id;
        $moduleClass = $orsData->module_class;

        $instanceUser = new User;
        $documentType = $orsData->document_type;
        $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                         'Budget Utilization Request & Status';
        $receivedByName = $instanceUser->getEmployee($receivedBy)->name;
        $msgNotif = "Your $documentType '$id' is now received by $receivedByName.";

        $docStatus = $instanceDocLog->checkDocStatus($id);
        $issuedBy = $docStatus->issued_by_id;
        $user = User::find($issuedBy);

        if ($moduleClass == 3) {
            $module = 'proc-ors-burs';
        } else if ($moduleClass == 2) {
            $module = 'ca-ors-burs';
        }

        $data = (object) [
            'ors_id' => $id,
            'po_id' => $poID,
            'po_no' => $poNo,
            'module' => $module,
            'type' =>'received',
            'msg' => $msgNotif
        ];

        $user->notify(new Notif($data));
    }

    public function notifyObligated($id, $obligatedBy) {
        $instanceDocLog = new DocLog;
        $orsData = $this::with('po')->find($id);
        $poNo = $orsData->po_no;
        $poID = $orsData->po->id;
        $serialNo = $orsData->serial_no;
        $moduleClass = $orsData->module_class;

        $instanceUser = new User;
        $documentType = $orsData->document_type;
        $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                         'Budget Utilization Request & Status';
        $obligatedByName = $instanceUser->getEmployee($obligatedBy)->name;
        $msgNotif = "Your $documentType with a serial number of '$serialNo' has been
                    obligated by $obligatedByName.";

        $docStatus = $instanceDocLog->checkDocStatus($id);
        $issuedBy = $docStatus->issued_by_id;
        $user = User::find($issuedBy);

        if ($moduleClass == 3) {
            $module = 'proc-ors-burs';
        } else if ($moduleClass == 2) {
            $module = 'ca-ors-burs';
        }

        $data = (object) [
            'ors_id' => $id,
            'po_id' => $poID,
            'po_no' => $poNo,
            'module' => $module,
            'type' =>'obligated',
            'msg' => $msgNotif
        ];

        $user->notify(new Notif($data));
    }

    public function notifyMessage($id, $from, $message) {
        $orsData = $this::with('po')->find($id);
        $poNo = $orsData->po_no;
        $poID = $orsData->po->id;
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
