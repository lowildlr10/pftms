<?php

use Illuminate\Database\Seeder;

use App\Models\InspectionAcceptance;
use App\Models\Signatory;
use App\Models\DocumentLog as DocLog;
use Carbon\Carbon;
use App\User;

class IARsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $iarsData = DB::connection('mysql-old-pftms')
                     ->table('tbliar')
                     ->get();
        $dataCount = $iarsData->count();

        foreach ($iarsData as $ctr => $iar) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Inspection and Acceptance Reports: [ $percentage% ] migrated.\n";

            $iarNo = $iar->iar_no;
            $poNo = trim(str_replace('IAR-', '', $iarNo));
            $code = $iar->code;
            $prID = $iar->pr_id;
            $orsID = $iar->ors_id;
            $dateIAR = $iar->date_iar ? $iar->date_iar : NULL;
            $invoiceNo = $iar->invoice_no ? $iar->invoice_no : NULL;
            $dateInvoice = $iar->date_invoice ? $iar->date_invoice : NULL;
            $_sigInspection = $iar->sig_inspection ? $iar->sig_inspection : NULL;
            $_sigSupply = $iar->sig_supply ? $iar->sig_supply : NULL;
            $createdAt = $iar->created_at ? $iar->created_at : NULL;
            $updatedAt = $iar->updated_at ? $iar->updated_at : NULL;
            $deletedAt = $iar->deleted_at ? $iar->deleted_at : NULL;

            $__sigInspectionData = DB::connection('mysql-old-pftms')
                                      ->table('tblsignatories')
                                      ->where('id', $iar->sig_inspection)
                                      ->first();
            $_sigInspectionData = $__sigInspectionData ?
                                   User::where('emp_id', $__sigInspectionData->emp_id)->first() :
                                   NULL;
            $sigInspectionData = $_sigInspectionData ?
                                  Signatory::where('emp_id', $_sigInspectionData->id)->first() :
                                  NULL;

            $__sigSupplyData = DB::connection('mysql-old-pftms')
                                      ->table('tblsignatories')
                                      ->where('id', $iar->sig_supply)
                                      ->first();
            $_sigSupplyData = $__sigSupplyData ?
                                   User::where('emp_id', $__sigSupplyData->emp_id)->first() :
                                   NULL;
            $sigSupplyData = $_sigSupplyData ?
                                  Signatory::where('emp_id', $_sigSupplyData->id)->first() :
                                  NULL;

            $sigInspection = isset($sigInspectionData->id) && $sigInspectionData->id ? $sigInspectionData->id : NULL;
            $sigSupply = isset($sigSupplyData->id) && $sigSupplyData->id ? $sigSupplyData->id : NULL;

            $poData = DB::table('purchase_job_orders')->where('po_no', $poNo)->first();
            $orsData = DB::table('obligation_request_status')->where('po_no', $poNo)->first();

            if ($poData && $orsData) {
                $poID = $poData->id;
                $prID = $poData->pr_id;
                $orsData = DB::table('obligation_request_status')->where('po_no', $poNo)->first();
                $orsID = $orsData->id;

                $instanceIAR = new InspectionAcceptance;
                $instanceIAR->iar_no = $iarNo;
                $instanceIAR->po_id = $poID;
                $instanceIAR->pr_id = $prID;
                $instanceIAR->ors_id = $orsID;
                $instanceIAR->date_iar = $dateIAR;
                $instanceIAR->invoice_no = $invoiceNo;
                $instanceIAR->date_invoice = $dateInvoice;
                $instanceIAR->sig_inspection = $sigInspection;
                $instanceIAR->sig_supply = $sigSupply;
                $instanceIAR->deleted_at = $deletedAt;
                $instanceIAR->created_at = $createdAt;
                $instanceIAR->updated_at = $updatedAt;
                $instanceIAR->save();

                $iarData = DB::table('inspection_acceptance_reports')
                             ->where('iar_no', $iarNo)
                             ->first();

                if ($iarData) {
                    $iarID = $iarData->id;
                    $docLogData = DB::connection('mysql-old-pftms')
                                    ->table('tbldocument_logs_history')
                                    ->where('code', $code)
                                    ->get();

                    foreach ($docLogData as $log) {
                        $empFromData = User::where('emp_id', $log->emp_from)->first();
                        $empToData = User::where('emp_id', $log->emp_to)->first();

                        $instanceDocLog = new DocLog;
                        $instanceDocLog->doc_id = $iarID;
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
    }
}
