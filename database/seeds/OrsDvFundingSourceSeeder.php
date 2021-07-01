<?php

use Illuminate\Database\Seeder;

class OrsDvFundingSourceSeeder extends Seeder
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
        $mooeTitles = DB::table('mooe_account_titles')->orderBy('order_no')->get();
        $dataCount1 = $orsData->count();
        $dataCount2 = $dvData->count();

        foreach ($orsData as $ctr => $orsDat) {
            if ($orsDat->module_class == 3) {
                $percentage = number_format((($ctr + 1) / $dataCount1) * 100, 2);
                echo "ORS/BURS: [ $percentage% ] successfully filled the 'funding_source' and 'uacs_object_code' column.\n";

                $id = $orsDat->id;
                $prID = $orsDat->pr_id;
                $uacsCode = $orsDat->uacs_object_code;
                $prDat = DB::table('purchase_requests')->where('id', $prID)->first();
                $project = $prDat->funding_source;
                $uacsCodes = [];

                foreach ($mooeTitles as $mooe) {
                    if (stripos($uacsCode, $mooe->uacs_code) === true) {
                        $uacsCodes[] = $mooe->id;
                    }
                }

                $uacsCodes = array_unique($uacsCodes);
                $uacsCodes = serialize($uacsCodes);

                DB::table('obligation_request_status')
                  ->where('id', $id)
                  ->update([
                      'funding_source' => $project,
                      'uacs_object_code' => $uacsCodes,
                    ]);
            }
        }

        foreach ($dvData as $ctr => $dvDat) {
            if ($dvDat->module_class == 3) {
                $percentage = number_format((($ctr + 1) / $dataCount2) * 100, 2);
                echo "Disbursement Vouchers: [ $percentage% ] successfully filled the 'funding_source' and 'uacs_object_code' column.\n";

                $id = $dvDat->id;
                $prID = $dvDat->pr_id;
                $orsID = $dvDat->ors_id;
                $prDat = DB::table('purchase_requests')->where('id', $prID)->first();
                $orsDat = DB::table('obligation_request_status')->where('id', $orsID)->first();
                $uacsCodes = $orsDat->uacs_object_code ? $orsDat->uacs_object_code :
                             serialze([]);
                $project = $prDat->funding_source;

                DB::table('disbursement_vouchers')
                  ->where('id', $id)
                  ->update([
                      'funding_source' => $project,
                      'uacs_object_code' => $uacsCodes
                  ]);
            }
        }
    }
}
