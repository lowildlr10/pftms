<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProcurementMode;

class ProcurementModesMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procModeData = DB::connection('mysql-old-pftms')
                          ->table('tblmode_procurement')
                          ->get();
        $dataCount = $procModeData->count();

        foreach ($procModeData as $ctr => $mode) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Procurement Modes: [ $percentage% ] migrated.\n";

            $procMode = new ProcurementMode;
            $procMode->mode_name = $mode->mode;
            $procMode->save();
        }
    }
}
