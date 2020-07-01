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
        $dataCount = $orsData->count();

        foreach ($orsData as $ctr => $ors) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Obligation Request Status: [ $percentage% ] migrated.\n";

            $code = $ors->code;
            $_orsID = $ors->id;
            $_prID = $ors->pr_id;
            $poNo = $ors->po_no;
            $serialNo = $ors->serial_no;
            $fundCluster = $ors->fund_cluster;
            $transactionType = $ors->transaction_type;
            $dateORS = $ors->date_ors_burs;
            $dateObligated = $ors->date_obligated;
            $_payee = $ors->payee;
            $office = $ors->office;
            $address = $ors->address;
            $responsibilityCenter = $ors->responsibility_center;
            $particulars = $ors->particulars;
            $mfoPAP = $ors->mfo_pap;
            $uacsObjectCode = $ors->uacs_object_code;
            $amount = $ors->amount;
            $_sigCertified1 = $ors->sig_certified_1;
            $_sigCertified2 = $ors->sig_certified_2;
            $_sigAccounting = $ors->sig_accounting;
            $_sigAgencyHead = $ors->sig_agency_head;
            $_sigObligatedBy = $ors->obligated_by;
            $dateCertified1 = $ors->date_certified_1;
            $dateCertified2 = $ors->date_certified_2;
            $moduleClassID = $ors->module_class_id;
            $documentType = strtolower($ors->document_type);
            $deletedAt = $ors->deleted_at;
            $createdAt = $ors->created_at;
            $updatedAt = $ors->updated_at;

            $__sigCertified1Data = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $_sigCertified1)
                                     ->first();
            $_sigCertified1Data = $__sigCertified1Data ?
                                  User::where('emp_id', $__sigCertified1Data->emp_id)->first() :
                                  NULL;
            $sigCertified1Data = $_sigCertified1Data ?
                                 Signatory::where('emp_id', $_sigCertified1Data->id)->first() :
                                 NULL;

            $__sigCertified2Data = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $_sigCertified2)
                                     ->first();
            $_sigCertified2Data = $__sigCertified2Data ?
                                  User::where('emp_id', $__sigCertified2Data->emp_id)->first() :
                                  NULL;
            $sigCertified2Data = $_sigCertified2Data ?
                                 Signatory::where('emp_id', $_sigCertified2Data->id)->first() :
                                 NULL;

            $__sigAccountingData = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $_sigAccounting)
                                     ->first();
            $_sigAccountingData = $__sigAccountingData ?
                                  User::where('emp_id', $__sigAccountingData->emp_id)->first() :
                                  NULL;
            $sigAccountingData = $_sigAccountingData ?
                                 Signatory::where('emp_id', $_sigAccountingData->id)->first() :
                                 NULL;

            $__sigAgencyHeadData = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $_sigAgencyHead)
                                     ->first();
            $_sigAgencyHeadData = $__sigAgencyHeadData ?
                                  User::where('emp_id', $__sigAgencyHeadData->emp_id)->first() :
                                  NULL;
            $sigAgencyHeadData = $_sigAgencyHeadData ?
                                 Signatory::where('emp_id', $_sigAgencyHeadData->id)->first() :
                                 NULL;
            $sigObligatedByData = User::where('emp_id', $_sigObligatedBy)->first();

            $sigCertified1 = isset($sigCertified1Data->id) && $sigCertified1Data->id ? $sigCertified1Data->id : NULL;
            $sigCertified2 = isset($sigCertified2Data->id) && $sigCertified2Data->id ? $sigCertified2Data->id : NULL;
            $sigAccounting = isset($sigAccountingData->id) && $sigAccountingData->id ? $sigAccountingData->id : NULL;
            $sigAgencyHead = isset($sigAgencyHeadData->id) && $sigAgencyHeadData->id ? $sigAgencyHeadData->id : NULL;
            $sigObligatedBy = isset($sigObligatedByData->id) && $sigObligatedByData->id ? $sigObligatedByData->id : NULL;


            if ($moduleClassID == 3) {
                $_supplierData = DB::connection('mysql-old-pftms')
                                   ->table('tblsuppliers')
                                   ->where('id', $_payee)
                                   ->first();
                $supplierData = Supplier::where('company_name', 'like', '%'.$_supplierData->company_name.'%')
                                        ->first();
                $payee = isset($supplierData->id) && $supplierData->id ? $supplierData->id : NULL;

                $prOldData = DB::connection('mysql-old-pftms')
                               ->table('tblpr')
                               ->where('id', $_prID)
                               ->first();
                $prNo = $prOldData->pr_no;
                $prData = DB::table('purchase_requests')->where('pr_no', $prNo)->first();
                $prID = $prData->id;

                $poCount = DB::table('purchase_job_orders')->where('po_no', $poNo)->count();
            } else if ($moduleClassID == 2) {
                $userData = User::where('emp_id', $_payee)
                                ->first();
                $payee = isset($userData->id) && $userData->id ? $userData->id : NULL;
            }

            if ($moduleClassID == 3) {
                if ($poCount > 0 && !empty($poNo)) {
                    $instanceORS = new ObligationRequestStatus;
                    $instanceORS->pr_id = $prID;
                    $instanceORS->po_no = $poNo;
                    $instanceORS->transaction_type = $transactionType;
                    $instanceORS->document_type = $documentType;
                    $instanceORS->fund_cluster = $fundCluster;
                    $instanceORS->serial_no = $serialNo;
                    $instanceORS->date_ors_burs = $dateORS;
                    $instanceORS->date_obligated = $dateObligated;
                    $instanceORS->payee = $payee;
                    $instanceORS->office = $office;
                    $instanceORS->address = $address;
                    $instanceORS->responsibility_center = $responsibilityCenter;
                    $instanceORS->particulars = $particulars;
                    $instanceORS->mfo_pap = $mfoPAP;
                    $instanceORS->uacs_object_code = $uacsObjectCode;
                    $instanceORS->amount = $amount;
                    $instanceORS->sig_certified_1 = $sigCertified1;
                    $instanceORS->sig_certified_2 = $sigCertified2;
                    $instanceORS->sig_accounting = $sigAccounting;
                    $instanceORS->sig_agency_head = $sigAgencyHead;
                    $instanceORS->obligated_by = $sigObligatedBy;
                    $instanceORS->date_certified_1 = $dateCertified1;
                    $instanceORS->date_certified_2 = $dateCertified2;
                    $instanceORS->module_class = $moduleClassID;
                    $instanceORS->deleted_at = $deletedAt;
                    $instanceORS->created_at = $createdAt;
                    $instanceORS->updated_at = $updatedAt;
                    $instanceORS->save();
                }
            }

            $orsData = DB::table('obligation_request_status')
                         ->where('po_no', $poNo)
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
