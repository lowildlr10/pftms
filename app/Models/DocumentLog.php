<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

class DocumentLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document_logs';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'date',
        'emp_from',
        'emp_to',
        'action',
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

    public function logDocument($docID, $empFrom, $empTo, $action, $remarks = '') {
        $instanceDocLog = new $this;
        $instanceDocLog->doc_id = $docID;
        $instanceDocLog->logged_at = Carbon::now();
        $instanceDocLog->emp_from = $empFrom;
        $instanceDocLog->emp_to = $empTo;
        $instanceDocLog->action = $action;
        $instanceDocLog->remarks = $remarks;
        $instanceDocLog->save();
    }

    public function checkDocGenerated($docID) {
        $logCount = $this::where([
            ['doc_id', $docID],
            ['action', 'document_generated']
        ])->orderBy('logged_at', 'desc')->count();

        return $logCount ? 1 : 0;
    }

    public function checkDocStatus($docID) {
        $logs = $this::where('doc_id', $docID)
                     ->orderBy('created_at', 'desc')
                     ->get();
        $user = new User;
        $currentStatus = (object) ["issued_by" => NULL,
                                    "issued_to" => NULL,
                                    "date_issued" => NULL,
                                    "received_by" => NULL,
                                    "date_received" => NULL,
                                    "issued_back_by" => NULL,
                                    "date_issued_back" => NULL,
                                    "received_back_by" => NULL,
                                    "date_received_back" => NULL,
                                    "issued_remarks" => NULL,
                                    "issued_back_remarks" => NULL,
                                    "issued_remarks" => NULL,
                                    "issued_back_remarks" => NULL];

        if (count($logs) > 0) {
            foreach ($logs as $log) {
                if ($log->action != "-") {
                    switch ($log->action) {
                        case 'issued':
                            $currentStatus->issued_remarks = $log->remarks;
                            $currentStatus->issued_by = $user->getEmployeeName($log->emp_from);
                            $currentStatus->issued_to = $user->getEmployeeName($log->emp_to);
                            $currentStatus->date_issued = $log->logged_at;
                            $currentStatus->remarks = $log->remarks;
                            break;

                        case 'received':
                            $currentStatus->received_by = $user->getEmployeeName($log->emp_from);
                            $currentStatus->date_received = $log->logged_at;
                            break;

                        case 'issued_back':
                            $currentStatus->issued_back_remarks = $log->remarks;
                            $currentStatus->issued_back_by = $user->getEmployeeName($log->emp_from);
                            $currentStatus->date_issued_back = $log->logged_at;
                            $currentStatus->remarks = $log->remarks;
                            break;

                        case 'received_back':
                            $currentStatus->received_back_by = $user->getEmployeeName($log->emp_from);
                            $currentStatus->date_received_back = $log->logged_at;
                            break;

                        default:
                            # code...
                            break;
                    }
                } else {
                    break;
                }
            }
        }

        return $currentStatus;
    }
}
