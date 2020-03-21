<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\User;
use App\Models\DocumentLog as DocLog;
use App\Notifications\PurchaseRequest as Notif;

class PurchaseRequest extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_requests';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_no',
        'date_pr',
        'date_pr_approved',
        'date_pr_disapproved',
        'date_pr_cancelled',
        'funding_source',
        'requested_by',
        'office',
        'responsibility_center',
        'division',
        'approved_by',
        'sig_app',
        'sig_funds_available',
        'recommended_by',
        'purpose',
        'remarks',
        'status',
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

    public function rfq() {
        return $this->hasMany('App\Models\RequestQuotation', 'pr_id');
    }

    public function checkDuplication($data) {
        $dataCount = $this::where('pr_no', $data)
                          ->count();

        return ($dataCount > 0) ? 1 : 0;;
    }

    public function notifyForApproval($prNo, $requestedBy) {
        $users = User::where('is_active', 'y')
                     ->get();
        $userData = User::find($requestedBy);
        $prData = $this::where('pr_no', $prNo)->first();
        $prID = $prData->id;
        $requestorName = $userData->firstname .
                         (!empty($userData->middlename) ? ' '.$userData->middlename[0].'. ' : ' ') .
                         $userData->lastname;
        $msgNotif =  "$requestorName created a new Purchase Request with a PR number of $prNo.";
        $data = (object) [
            'pr_id' => $prID,
            'pr_no' => $prNo,
            'module' => 'proc-pr',
            'type' => 'for-approval',
            'msg' => $msgNotif,
        ];

        foreach ($users as $user) {
            if (!$user->hasOrdinaryRole($user->id)) {
                $user->notify(new Notif($data));
            }
        }
    }

    public function notifyApproved($prNo, $requestedBy) {
        $user = User::find($requestedBy);
        $prData = $this::where('pr_no', $prNo)->first();
        $prID = $prData->id;
        $msgNotif = "Your Purchase Request '$prNo' is now approved.";
        $data = (object) [
            'pr_id' => $prID,
            'pr_no' => $prNo,
            'module' => 'proc-pr',
            'type' => 'approved',
            'msg' => $msgNotif,
        ];
        $user->notify(new Notif($data));
    }

    public function notifyDisapproved($prNo, $requestedBy) {
        $user = User::find($requestedBy);
        $prData = $this::where('pr_no', $prNo)->first();
        $prID = $prData->id;
        $msgNotif = "Your Purchase Request '$prNo' has been disapproved.";
        $data = (object) [
            'pr_id' => $prID,
            'pr_no' => $prNo,
            'module' => 'proc-pr',
            'type' => 'disapproved',
            'msg' => $msgNotif,
        ];
        $user->notify(new Notif($data));
    }

    public function notifyCancelled($prNo, $requestedBy) {
        $user = User::find($requestedBy);
        $prData = $this::where('pr_no', $prNo)->first();
        $prID = $prData->id;
        $msgNotif = "Your Purchase Request '$prNo' has been cancelled.";
        $data = (object) [
            'pr_id' => $prID,
            'pr_no' => $prNo,
            'module' => 'proc-pr',
            'type' => 'cancelled',
            'msg' => $msgNotif,
        ];
        $user->notify(new Notif($data));
    }
}
