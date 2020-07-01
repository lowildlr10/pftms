<?php

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\RequestQuotation;
use App\User;
use App\Models\Signatory;
use App\Models\DocumentLog as DocLog;

class RFQsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rfqsData = DB::connection('mysql-old-pftms')
                      ->table('tblcanvass')
                      ->get();
        $dataCount = $rfqsData->count();

        foreach ($rfqsData as $ctr => $rfq) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Request for Quotations: [ $percentage% ] migrated.\n";

            $code = $rfq->code;
            $prID = $rfq->pr_id;
            $prData = DB::connection('mysql-old-pftms')
                        ->table('tblpr')
                        ->where('id', $prID)
                        ->first();
            $prNo = $prData->pr_no;
            $instancePR = DB::table('purchase_requests')->where('pr_no', $prNo)->first();

            $__sigRFQData = DB::connection('mysql-old-pftms')
                              ->table('tblsignatories')
                              ->where('id', $rfq->sig_rfq)
                              ->first();
            $_sigRFQData = $__sigRFQData ?
                           User::where('emp_id', $__sigRFQData->emp_id)->first() :
                           NULL;
            $sigRFQData = $_sigRFQData ?
                          Signatory::where('emp_id', $_sigRFQData->id)->first() :
                          NULL;

            $instanceRFQ = new RequestQuotation;
            $instanceRFQ->pr_id = $instancePR->id;
            $instanceRFQ->date_canvass = $rfq->date_canvass;
            $instanceRFQ->sig_rfq = $sigRFQData ? $sigRFQData->id : NULL;
            $instanceRFQ->deleted_at = $rfq->deleted_at;
            $instanceRFQ->created_at = $rfq->created_at;
            $instanceRFQ->updated_at = $rfq->updated_at;
            $instanceRFQ->save();

            $rfqData = DB::table('request_quotations')->where('pr_id', $instancePR->id)->first();

            $docLogData = DB::connection('mysql-old-pftms')
                            ->table('tbldocument_logs_history')
                            ->where('code', $code)
                            ->get();

            foreach ($docLogData as $log) {
                $empFromData = User::where('emp_id', $log->emp_from)->first();
                $empToData = User::where('emp_id', $log->emp_to)->first();

                $instanceDocLog = new DocLog;
                $instanceDocLog->doc_id = $rfqData->id;
                $instanceDocLog->logged_at = $log->date;
                $instanceDocLog->emp_from = $empFromData ? $empFromData->id :
                                            NULL;
                $instanceDocLog->emp_to = $empToData ? $empToData->id :
                                          NULL;
                $instanceDocLog->action = $log->action;
                $instanceDocLog->remarks = $log->remarks;
                $instanceDocLog->created_at = $log->created_at;
                $instanceDocLog->updated_at = $log->updated_at;
                $instanceDocLog->save();
            }
        }
    }
}
