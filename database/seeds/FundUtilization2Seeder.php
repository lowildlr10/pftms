<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FundUtilization2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orsData = DB::table('obligation_request_status')->where('module_class', 3)->get();
        $dvData = DB::table('disbursement_vouchers')->where('module_class', 3)->get();
        $dataCount1 = $orsData->count();
        $dataCount2 = $dvData->count();

        foreach ($orsData as $ctr => $orsDat) {
            if ($orsDat->module_class == 3) {
                $percentage = number_format((($ctr + 1) / $dataCount1) * 100, 2);
                echo "ORS/BURS: [ $percentage% ] successfully filled the 'funding_source' column.\n";

                $id = $orsDat->id;
                $prID = $orsDat->pr_id;
                $prDat = DB::table('purchase_requests')->where('id', $prID)->first();
                $project = $prDat->funding_source;

                DB::table('obligation_request_status')
                  ->where('id', $id)
                  ->update([
                      'funding_source' => $project
                    ]);
            }
        }

        foreach ($dvData as $ctr => $dvDat) {
            if ($dvDat->module_class == 3) {
                $percentage = number_format((($ctr + 1) / $dataCount2) * 100, 2);
                echo "Disbursement Vouchers: [ $percentage% ] successfully filled the 'funding_source' column.\n";

                $id = $dvDat->id;
                $prID = $dvDat->pr_id;
                $prDat = DB::table('purchase_requests')->where('id', $prID)->first();
                $project = $prDat->funding_source;

                DB::table('disbursement_vouchers')
                  ->where('id', $id)
                  ->update([
                      'funding_source' => $project
                  ]);
            }
        }
    }
}
