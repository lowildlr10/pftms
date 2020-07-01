<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\FundingSource;

class ProjectsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fundingSrcData = DB::connection('mysql-old-pftms')
                            ->table('tblprojects')
                            ->get();
        $dataCount = $fundingSrcData->count();

        foreach ($fundingSrcData as $ctr => $fund) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Source of Funds: [ $percentage% ] migrated.\n";

            $funding = new FundingSource;
            $funding->reference_code = $fund->reference_code;
            $funding->source_name = $fund->project;
            $funding->save();
        }
    }
}
