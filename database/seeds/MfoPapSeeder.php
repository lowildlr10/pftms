<?php

use Illuminate\Database\Seeder;
use App\Models\MfoPap;

class MfoPapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orsDat = DB::table('obligation_request_status')->get();
        $dvDat = DB::table('disbursement_vouchers')->get();
        $mfoPAPs = [
            (object) [
                'code' => 'A.III.c.1',
                'description' => 'Test 1'
            ], (object) [
                'code' => 'A.III.c.2',
                'description' => 'Test 2'
            ], (object) [
                'code' => 'A.III.b.1',
                'description' => 'Test 3'
            ]
        ];
        $mfoCodes = ['A.III.c.1', 'A.III.c.2', 'A.III.b.1'];
        $dataCount = count($mfoPAPs);

        foreach ($mfoPAPs as $ctr => $mfo) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "MFO/PAP: [ $percentage% ] migrated.\n";

            $instanceMFO = new MfoPap;
            $instanceMFO->code = $mfo->code;
            $instanceMFO->description = $mfo->description;
            $instanceMFO->save();
        }

        $dataCount = $orsDat->count();

        foreach ($orsDat as $ctr => $ors) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "ORS/BURS: [ $percentage% ] successfully filled the 'mfo_pap' column.\n";

            $mfoPAP = [];

            foreach ($mfoCodes as $mfo) {
                if (stripos($ors->mfo_pap, $mfo) === true) {
                    $mfoPAP[] = $mfo;
                }
            }

            DB::table('obligation_request_status')
              ->where('id', $ors->id)
              ->update([
                'mfo_pap' => serialize($mfoPAP)
            ]);
        }

        $dataCount = $dvDat->count();

        foreach ($dvDat as $ctr => $dv) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "DV: [ $percentage% ] successfully filled the 'mfo_pap' column.\n";

            $mfoPAP = [];

            foreach ($mfoCodes as $mfo) {
                if (stripos($dv->mfo_pap, $mfo) === true) {
                    $mfoPAP[] = $mfo;
                }
            }

            DB::table('disbursement_vouchers')
              ->where('id', $dv->id)
              ->update([
                'mfo_pap' => serialize($mfoPAP)
            ]);
        }
    }
}
