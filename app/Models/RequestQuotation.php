<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Notifications\RequestQuotation as Notif;
use App\User;
use Kyslik\ColumnSortable\Sortable;
use App\Models\PurchaseRequest;

class RequestQuotation extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_quotations';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_id',
        'date_canvass',
        'sig_rfq',
        'canvassed_by',
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
     * Get the phone record associated with the request for quotation.
     */
    public function pr() {
        return $this->belongsTo('App\Models\PurchaseRequest', 'pr_id', 'id');
    }

    public function signatory() {
        return $this->hasOne('App\Models\Signatory', 'id', 'sig_rfq');
    }

    public function canvasser() {
        return $this->hasOne('App\User', 'id', 'canvassed_by');
    }

    public $sortable = [
        'date_canvass',
    ];

    public function notifyIssued($id, $responsiblePerson, $requestedBy) {
        $rfqData = $this::with('pr')->find($id);
        $prID = $rfqData->pr_id;
        $prData = PurchaseRequest::find($prID);
        $prNo = $prData->pr_no;

        if ($responsiblePerson == $requestedBy) {
            $user = User::find($requestedBy);
            $msgNotif = "Request for Quotation for Purchase Request '$prNo' is now issued to you.";
            $data = (object) [
                'rfq_id' => $id,
                'pr_id' => $prID,
                'pr_no' => $prNo,
                'module' => 'proc-rfq',
                'type' => 'issued',
                'msg' => $msgNotif,
            ];
            $user->notify(new Notif($data));
        } else {
            $user = User::find($responsiblePerson);
            $msgNotif = "Request for Quotation for Purchase Request '$prNo' is now issued to you.";
            $data = (object) [
                'rfq_id' => $id,
                'pr_id' => $prID,
                'pr_no' => $prNo,
                'module' => 'proc-rfq',
                'type' => 'issued',
                'msg' => $msgNotif,
            ];
            $user->notify(new Notif($data));

            $user = User::find($requestedBy);
            $responsiblePersonName = $user->getEmployee($responsiblePerson)->name;
            $msgNotif = "Your Request for Quotation for Purchase Request '$prNo' is now issued to
                         $responsiblePersonName.";
            $data = (object) [
                'rfq_id' => $id,
                'pr_id' => $prID,
                'pr_no' => $prNo,
                'module' => 'proc-rfq',
                'type' => 'issued',
                'msg' => $msgNotif,
            ];
            $user->notify(new Notif($data));
        }
    }

    public function notifyReceived($id, $receivedBy, $responsiblePerson, $requestedBy) {
        $rfqData = $this::with('pr')->find($id);
        $prID = $rfqData->pr_id;
        $prData = PurchaseRequest::find($prID);
        $prNo = $prData->pr_no;
        $user = new User;
        $receivedByName = $user->getEmployee($receivedBy)->name;
        $requestedByName = $user->getEmployee($requestedBy)->name;

        if ($responsiblePerson == $requestedBy) {
            $user = User::find($requestedBy);
            $msgNotif = "Your Request for Quotation '$prNo' has been received by $receivedByName and
                        it is now ready for Abstract for Quotation.";
            $data = (object) [
                'rfq_id' => $id,
                'pr_id' => $prID,
                'pr_no' => $prNo,
                'module' => 'proc-rfq',
                'type' => 'received',
                'msg' => $msgNotif,
            ];
            $user->notify(new Notif($data));
        } else {
            $user = User::find($responsiblePerson);
            $msgNotif = "Request for Quotation '$prNo' of $requestedByName has been received
                         by $receivedByName and it is now ready for Abstract for Quotation.";
            $data = (object) [
                'rfq_id' => $id,
                'pr_id' => $prID,
                'pr_no' => $prNo,
                'module' => 'proc-rfq',
                'type' => 'received',
                'msg' => $msgNotif,
            ];
            $user->notify(new Notif($data));

            $user = User::find($requestedBy);
            $msgNotif = "Your Request for Quotation '$prNo' has been received by $receivedByName and
                        it is now ready for Abstract for Quotation.";
            $data = (object) [
                'rfq_id' => $id,
                'pr_id' => $prID,
                'pr_no' => $prNo,
                'module' => 'proc-rfq',
                'type' => 'received',
                'msg' => $msgNotif,
            ];
            $user->notify(new Notif($data));
        }
    }
}
