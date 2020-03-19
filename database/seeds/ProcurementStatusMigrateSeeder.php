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

        foreach ($procStatusData as $stat) {
            DB::table('procurement_status')
              ->insert([
                    'id' => $stat->id,
                    'status_name' => $stat->status
              ]);
        }
    }
}