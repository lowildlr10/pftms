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

        foreach ($fundingSrcData as $fund) {
            $funding = new FundingSource;
            $funding->reference_code = $fund->reference_code;
            $funding->source_name = $fund->project;
            $funding->save();
        }
    }
}
