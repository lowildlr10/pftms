<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;
use App\Models\DocumentLog as DocLog;

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
        'code',
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

    public function logDocument($docID, $empFrom, $empTo, $action, $remarks = '') {
        $docHistory = new DocLog;
        $docHistory->code = $docID;
        $docHistory->date = Carbon::now();
        $docHistory->emp_from = $empFrom;
        $docHistory->emp_to = $empTo;
        $docHistory->action = $action;
        $docHistory->remarks = $remarks;
        $docHistory->save();
    }

    private function checkDocStatus($docID) {
        $logs = DocLog::where('code', $docID)
                      ->orderBy('created_at', 'desc')
                      ->get();
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
                            $currentStatus->issued_by = $this->getEmployeeName($log->emp_from);
                            $currentStatus->issued_to = $this->getEmployeeName($log->emp_to);
                            $currentStatus->date_issued = $log->date;
                            $currentStatus->remarks = $log->remarks;
                            break;

                        case 'received':
                            $currentStatus->received_by = $this->getEmployeeName($log->emp_from);
                            $currentStatus->date_received = $log->date;
                            break;

                        case 'issued_back':
                            $currentStatus->issued_back_remarks = $log->remarks;
                            $currentStatus->issued_back_by = $this->getEmployeeName($log->emp_from);
                            $currentStatus->date_issued_back = $log->date;
                            $currentStatus->remarks = $log->remarks;
                            break;

                        case 'received_back':
                            $currentStatus->received_back_by = $this->getEmployeeName($log->emp_from);
                            $currentStatus->date_received_back = $log->date;
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

    public function checkDuplication($data) {
        $dataCount = $this::where('pr_no', $data)
                          ->count();

        return ($dataCount > 0) ? 1 : 0;;
    }
}
