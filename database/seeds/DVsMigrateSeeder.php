<?php

use Illuminate\Database\Seeder;

use App\Models\DisbursementVoucher;
use App\Models\Signatory;
use App\Models\DocumentLog as DocLog;
use Carbon\Carbon;
use App\User;
use App\Models\Supplier;

class DVsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dvsData = DB::connection('mysql-old-pftms')
                     ->table('tbldv')
                     ->get();
        $dataCount = $dvsData->count();

        foreach ($dvsData as $ctr => $dv) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Disbursement Vouchers: [ $percentage% ] migrated.\n";

            $code = $dv->code;
            $dvNo = $dv->dv_no;
            $dateDV = $dv->date_dv;
            $dateDisbursed = $dv->date_disbursed;
            $paymentMode = $dv->payment_mode;
            $particulars = $dv->particulars;
            $_sigAccounting = $dv->sig_accounting;
            $_sigAgencyHead = $dv->sig_agency_head;
            $dateAccounting = $dv->date_accounting;
            $dateAgencyHead = $dv->date_agency_head;
            $moduleClassID = $dv->module_class_id;
            $forPayment = $dv->for_payment;
            $_disbursedBy = $dv->disbursed_by;
            $fundCluster = $dv->fund_cluster;
            $otherPayment = $dv->other_payment;
            $createdAt = $dv->created_at;
            $updatedAt = $dv->updated_at;
            $deletedAt = $dv->deleted_at;

            $__sigAccountingData = DB::connection('mysql-old-pftms')
                                      ->table('tblsignatories')
                                      ->where('id', $dv->sig_accounting)
                                      ->first();
            $_sigAccountingData = $__sigAccountingData ?
                                   User::where('emp_id', $__sigAccountingData->emp_id)->first() :
                                   NULL;
            $sigAccountingData = $_sigAccountingData ?
                                  Signatory::where('emp_id', $_sigAccountingData->id)->first() :
                                  NULL;

            $__sigAgencyHeadData = DB::connection('mysql-old-pftms')
                                      ->table('tblsignatories')
                                      ->where('id', $dv->sig_agency_head)
                                      ->first();
            $_sigAgencyHeadData = $__sigAgencyHeadData ?
                                   User::where('emp_id', $__sigAgencyHeadData->emp_id)->first() :
                                   NULL;
            $sigAgencyHeadData = $_sigAgencyHeadData ?
                                  Signatory::where('emp_id', $_sigAgencyHeadData->id)->first() :
                                  NULL;
            $sigdisbursedByData = User::where('emp_id', $_disbursedBy)->first();

            $sigAccounting = isset($sigAccountingData->id) && $sigAccountingData->id ? $sigAccountingData->id : NULL;
            $sigAgencyHead = isset($sigAgencyHeadData->id) && $sigAgencyHeadData->id ? $sigAgencyHeadData->id : NULL;
            $sigdisbursedBy = isset($sigdisbursedByData->id) && $sigdisbursedByData->id ? $sigdisbursedByData->id : NULL;

            $orsOldData = DB::connection('mysql-old-pftms')
                            ->table('tblors_burs')
                            ->where('id', $dv->ors_id)
                            ->first();
            $poNo = $orsOldData->po_no;

            $orsData = DB::table('obligation_request_status')->where('po_no', $poNo)->first();

            if ($orsData) {
                $prID = $orsData->pr_id;
                $orsID = $orsData->id;
                $address = $orsData->address;
                $transactionType = $orsData->transaction_type;
                $amount = $orsData->amount;
                $sigCertified = $orsData->sig_certified_1 ? $orsData->sig_certified_1 : NULL;
                $payee = $orsData->payee;

                $instanceDV = new DisbursementVoucher;
                $instanceDV->pr_id = $prID;
                $instanceDV->ors_id = $orsID;
                $instanceDV->dv_no = $dvNo;
                $instanceDV->transaction_type = $transactionType;
                $instanceDV->payee = $payee;
                $instanceDV->address = $address;
                $instanceDV->date_dv = $dateDV;
                $instanceDV->date_disbursed = $dateDisbursed;
                $instanceDV->fund_cluster = $fundCluster;
                $instanceDV->payment_mode = $paymentMode;
                $instanceDV->particulars = $particulars;
                $instanceDV->amount = $amount;
                $instanceDV->sig_certified = $sigCertified;
                $instanceDV->sig_accounting = $sigAccounting;
                $instanceDV->sig_agency_head = $sigAgencyHead;
                $instanceDV->date_accounting = $dateAccounting;
                $instanceDV->date_agency_head = $dateAgencyHead;
                $instanceDV->for_payment = $forPayment;
                $instanceDV->module_class = $moduleClassID;
                $instanceDV->disbursed_by = $sigdisbursedBy;
                $instanceDV->deleted_at = $deletedAt;
                $instanceDV->created_at = $createdAt;
                $instanceDV->updated_at = $updatedAt;
                $instanceDV->save();

                $dvData = DB::table('inspection_acceptance_reports')
                            ->where('ors_id', $orsID)
                            ->first();

                if ($dvData) {
                    $dvID = $dvData->id;
                    $docLogData = DB::connection('mysql-old-pftms')
                                    ->table('tbldocument_logs_history')
                                    ->where('code', $code)
                                    ->get();

                    foreach ($docLogData as $log) {
                        $empFromData = User::where('emp_id', $log->emp_from)->first();
                        $empToData = User::where('emp_id', $log->emp_to)->first();

                        $instanceDocLog = new DocLog;
                        $instanceDocLog->doc_id = $dvID;
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
