<?php

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseJobOrder;
use App\Models\ObligationRequestStatus;
use App\User;
use App\Models\Signatory;
use App\Models\ProcurementMode;
use App\Models\Supplier;
use App\Models\DocumentLog as DocLog;
use Carbon\Carbon;

class ORSBURSsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orsData = DB::connection('mysql-old-pftms')
                     ->table('tblors_burs')
                     ->get();

        foreach ($orsData as $ors) {
            $prID = NULL;
            $poNo = NULL;
            $code = $ors->code;

            if ($ors->module_class_id == 3) {
                $_supplierData = DB::connection('mysql-old-pftms')
                                   ->table('tblsuppliers')
                                   ->where('id', $ors->payee)
                                   ->first();
                $supplierData = Supplier::where('company_name', $_supplierData->company_name)
                                        ->first();
                $payee = $supplierData->id;

                $_prID = $ors->pr_id;
                $prData = DB::connection('mysql-old-pftms')
                            ->table('tblpr')
                            ->where('id', $_prID)
                            ->first();
                $prNo = $prData->pr_no;
                $instancePR = DB::table('purchase_requests')->where('pr_no', $prNo)->first();
                $prID = $instancePR->id;
                $poNo = $ors->po_no;
                $moduleClass = 3;

                $poCount = PurchaseJobOrder::where('po_no', $poNo)->count();
            } else if ($ors->module_class_id == 2) {
                $userData = User::where('emp_id', $ors->payee)
                                ->first();
                $payee = $userData->id;
                $moduleClass = 2;
            }

            $__sigCertified1Data = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $ors->sig_certified_1)
                                     ->first();
            $_sigCertified1Data = $__sigCertified1Data ?
                                  User::where('emp_id', $__sigCertified1Data->emp_id)->first() :
                                  NULL;
            $sigCertified1Data = $_sigCertified1Data ?
                                 Signatory::where('emp_id', $_sigCertified1Data->id)->first() :
                                 NULL;

            $__sigCertified2Data = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $ors->sig_certified_2)
                                     ->first();
            $_sigCertified2Data = $__sigCertified2Data ?
                                  User::where('emp_id', $__sigCertified2Data->emp_id)->first() :
                                  NULL;
            $sigCertified2Data = $_sigCertified2Data ?
                                 Signatory::where('emp_id', $_sigCertified2Data->id)->first() :
                                 NULL;

            $__sigAccountingData = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $ors->sig_accounting)
                                     ->first();
            $_sigAccountingData = $__sigAccountingData ?
                                  User::where('emp_id', $__sigAccountingData->emp_id)->first() :
                                  NULL;
            $sigAccountingData = $_sigAccountingData ?
                                 Signatory::where('emp_id', $_sigAccountingData->id)->first() :
                                 NULL;

            $__sigAgencyHeadData = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $ors->sig_agency_head)
                                     ->first();
            $_sigAgencyHeadData = $__sigAgencyHeadData ?
                                  User::where('emp_id', $__sigAgencyHeadData->emp_id)->first() :
                                  NULL;
            $sigAgencyHeadData = $_sigAgencyHeadData ?
                                 Signatory::where('emp_id', $_sigAgencyHeadData->id)->first() :
                                 NULL;
            $sigObligatedByData = User::where('emp_id', $ors->obligated_by)->first();

            $instanceORS = new ObligationRequestStatus;

            $instanceORS->pr_id = $prID;
            $instanceORS->po_no = $ors->po_no;
            $instanceORS->transaction_type = $ors->transaction_type;
            $instanceORS->document_type = strtolower($ors->document_type);
            $instanceORS->fund_cluster = $ors->fund_cluster ? $ors->fund_cluster : NULL;
            $instanceORS->serial_no = $ors->serial_no ? $ors->serial_no : NULL;
            $instanceORS->date_ors_burs = $ors->date_ors_burs ? $ors->date_ors_burs : NULL;
            $instanceORS->date_obligated = $ors->date_obligated ? $ors->date_obligated : NULL;
            $instanceORS->payee = $payee;
            $instanceORS->office = $ors->office;
            $instanceORS->address = $ors->address;
            $instanceORS->responsibility_center = $ors->responsibility_center;
            $instanceORS->particulars = $ors->particulars;
            $instanceORS->mfo_pap = $ors->mfo_pap;
            $instanceORS->uacs_object_code = $ors->uacs_object_code;
            $instanceORS->amount = $ors->amount;
            $instanceORS->sig_certified_1 = $sigCertified1Data ?
                                            $sigCertified1Data->id :
                                            NULL;
            $instanceORS->sig_certified_2 = $sigCertified2Data ?
                                            $sigCertified2Data->id :
                                            NULL;
            $instanceORS->sig_accounting = $sigAccountingData ?
                                           $sigAccountingData->id :
                                           NULL;
            $instanceORS->sig_agency_head = $sigAgencyHeadData ?
                                            $sigAgencyHeadData->id :
                                            NULL;
            $instanceORS->obligated_by = $sigObligatedByData ?
                                         $sigObligatedByData->id :
                                         NULL;
            $instanceORS->date_certified_1 = $ors->date_certified_1 ? $ors->date_certified_1 : NULL;
            $instanceORS->date_certified_2 = $ors->date_certified_2 ? $ors->date_certified_2 : NULL;
            $instanceORS->module_class = $moduleClass;
            $instanceORS->deleted_at = $ors->deleted_at ? $ors->deleted_at : NULL;
            $instanceORS->created_at = $ors->created_at;
            $instanceORS->updated_at = Carbon::now();

            if (isset($poCount) && $poCount > 0 && $moduleClass == 3) {
                $instanceORS->save();
            }

            if ($moduleClass == 2) {
                $instanceORS->save();
            }

            $orsData = DB::table('obligation_request_status')
                         ->where('po_no', $ors->po_no)
                         ->first();

            if ($orsData) {
                $orsID = $orsData->id;
                $docLogData = DB::connection('mysql-old-pftms')
                                ->table('tbldocument_logs_history')
                                ->where('code', $code)
                                ->get();

                foreach ($docLogData as $log) {
                    $empFromData = User::where('emp_id', $log->emp_from)->first();
                    $empToData = User::where('emp_id', $log->emp_to)->first();

                    $instanceDocLog = new DocLog;
                    $instanceDocLog->doc_id = $orsID;
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
