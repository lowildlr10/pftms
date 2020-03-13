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

        foreach ($procModeData as $mode) {
            $procMode = new ProcurementMode;
            $procMode->mode_name = $mode->mode;
            $procMode->save();
        }
    }
}
