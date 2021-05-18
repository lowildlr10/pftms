<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmpAccount as User;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;
use DB;

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
        'doc_id',
        'logged_at',
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
                     ->orderBy('logged_at', 'desc')
                     ->get();
        $user = new User;
        $currentStatus = (object) [
            'issued_by' => NULL,
            'issued_to' => NULL,
            'issued_by_id' => NULL,
            'issued_to_id' => NULL,
            'date_issued' => NULL,
            'issued_remarks' => NULL,

            'received_by' => NULL,
            'received_by_id' => NULL,
            'date_received' => NULL,
            'received_remarks' => NULL,

            'issued_back_by' => NULL,
            'issued_back_to' => NULL,
            'issued_back_by_id' => NULL,
            'issued_back_to_id' => NULL,
            'date_issued_back' => NULL,
            'issued_back_remarks' => NULL,

            'received_back_by' => NULL,
            'received_back_by_id' => NULL,
            'date_received_back' => NULL,
            'received_back_remarks' => NULL,
        ];

        if (count($logs) > 0) {
            foreach ($logs as $log) {
                if ($log->action != "-") {
                    switch ($log->action) {
                        case 'issued':
                            $currentStatus->issued_by = $user->getEmployee($log->emp_from)->name;
                            $currentStatus->issued_to = $user->getEmployee($log->emp_to)->name;
                            $currentStatus->issued_by_id = $log->emp_from;
                            $currentStatus->issued_to_id = $log->emp_to;
                            $currentStatus->date_issued = $log->logged_at;
                            $currentStatus->issued_remarks = $log->remarks;
                            break;

                        case 'received':
                            $currentStatus->received_by = $user->getEmployee($log->emp_from)->name;
                            $currentStatus->received_by_id = $log->emp_from;
                            $currentStatus->date_received = $log->logged_at;
                            $currentStatus->received_remarks = $log->remarks;
                            break;

                        case 'issued_back':
                            $currentStatus->issued_back_by = $user->getEmployee($log->emp_from)->name;
                            $currentStatus->issued_back_to = $user->getEmployee($log->emp_to)->name;
                            $currentStatus->issued_back_by_id = $log->emp_from;
                            $currentStatus->issued_back_to_id = $log->emp_to;
                            $currentStatus->date_issued_back = $log->logged_at;
                            $currentStatus->issued_back_remarks = $log->remarks;
                            break;

                        case 'received_back':
                            $currentStatus->received_back_by = $user->getEmployee($log->emp_from)->name;
                            $currentStatus->received_back_by_id = $log->emp_from;
                            $currentStatus->date_received_back = $log->logged_at;
                            $currentStatus->received_back_remarks = $log->remarks;
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

    public function checkDocHistory($code) {
        $logs = DB::table('document_logs')
                  ->where('doc_id', $code)
                  ->orderBy('created_at', 'desc')
                  ->get();
        $history = "";

        if (count($logs) > 0) {
            foreach ($logs as $log) {
                switch ($log->action) {
                    case 'issued':
                        if (empty($log->remarks)) {
                            $history .= "<strong class='orange-text'>*</strong>$log->created_at : Document submitted.<br>";
                        } else {
                            $history .= "<strong class='orange-text'>*</strong>$log->created_at : Document submitted ($log->remarks).<br>";
                        }

                        break;

                    case 'received':
                        if (empty($log->remarks)) {
                            $history .= "<strong class='green-text'>*</strong>$log->created_at : Document received.<br>";
                        } else {
                            $history .= "<strong class='green-text'>*</strong>$log->created_at : Document received ($log->remarks).<br>";
                        }


                        break;

                    case 'issued_back':
                        if (empty($log->remarks)) {
                            $history .= "<strong class='orange-text'>*</strong>$log->created_at : Document issued back.<br>";
                        } else {
                            $history .= "<strong class='orange-text'>*</strong>$log->created_at : Document issued back ($log->remarks).<br>";
                        }
                        break;

                    case 'received_back':
                        if (empty($log->remarks)) {
                            $history .= "<strong class='green-text'>*</strong>$log->created_at : Document received back.<br>";
                        } else {
                            $history .= "<strong class='green-text'>*</strong>$log->created_at : Document received back ($log->remarks).<br>";
                        }
                        break;

                    default:
                        # code...
                        break;
                }

            }
        }

        return $history;
    }
}
