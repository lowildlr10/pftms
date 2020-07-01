<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementStatusMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procStatusData = DB::connection('mysql-old-pftms')
                            ->table('tblpr_status')
                            ->get();
        $dataCount = $procStatusData->count();

        foreach ($procStatusData as $ctr => $stat) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Procurement Status: [ $percentage% ] migrated.\n";

            DB::table('procurement_status')
              ->insert([
                    'id' => $stat->id,
                    'status_name' => $stat->status
              ]);
        }
    }
}
