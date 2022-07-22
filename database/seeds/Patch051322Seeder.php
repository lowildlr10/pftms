<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Patch051322Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orsData = DB::table('obligation_request_status')->get();
        $orsCount = $orsData->count();

        foreach ($orsData as $ctr => $ors) {
            $percentage = number_format((($ctr + 1) / $orsCount) * 100, 2);
            echo "Obligation Request Status: [ $percentage% ] migrated.\n";

            if ($ors->module_class == 2) {
                $user = DB::table('emp_accounts')->where('id', $ors->payee)->first();

                if ($user) {
                    DB::table('obligation_request_status')
                      ->where('id', $ors->id)
                      ->update(['created_by' => $ors->payee]);
                }
            } else {
                $prDat = DB::table('purchase_requests')->where('id', $ors->pr_id);

                if ($prDat) {
                    // DPSO Employee
                    DB::table('obligation_request_status')
                      ->where('id', $ors->id)
                      ->update(['created_by' => 'de1f33d0-c2ce-11ea-ac3e-99782113ea40']);
                }
            }
        }

        $dvData = DB::table('disbursement_vouchers')->get();
        $dvCount = $dvData->count();

        foreach ($dvData as $ctr => $dv) {
            $percentage = number_format((($ctr + 1) / $dvCount) * 100, 2);
            echo "Disbursement Voucher: [ $percentage% ] migrated.\n";

            if ($dv->module_class == 2) {
                $user = DB::table('emp_accounts')->where('id', $dv->payee)->first();

                if ($user) {
                    DB::table('obligation_request_status')
                      ->where('id', $dv->id)
                      ->update(['created_by' => $dv->payee]);
                }
            } else {
                $prDat = DB::table('purchase_requests')->where('id', $dv->pr_id);

                if ($prDat) {
                    // DPSO Employee
                    DB::table('disbursement_vouchers')
                      ->where('id', $dv->id)
                      ->update(['created_by' => 'de1f33d0-c2ce-11ea-ac3e-99782113ea40']);
                }
            }
        }

        $lrData = DB::table('liquidation_reports')->get();
        $lrCount = $lrData->count();

        foreach ($lrData as $ctr => $lr) {
            $percentage = number_format((($ctr + 1) / $lrCount) * 100, 2);
            echo "Liquidation Report: [ $percentage% ] migrated.\n";

            $user = DB::table('emp_accounts')->where('id', $lr->sig_claimant)->first();

            if ($user) {
                DB::table('liquidation_reports')
                  ->where('id', $lr->id)
                  ->update(['created_by' => $lr->sig_claimant]);
            }
        }

        $prData = DB::table('purchase_requests')->get();
        $prCount = $prData->count();

        foreach ($prData as $ctr => $pr) {
            $percentage = number_format((($ctr + 1) / $prCount) * 100, 2);
            echo "Purchase Request: [ $percentage% ] migrated.\n";

            DB::table('purchase_requests')
              ->where('id', $pr->id)
              ->update(['created_by' => $pr->requested_by]);
        }
    }
}
