<?php

use Illuminate\Database\Seeder;
use App\Models\AllotmentClass;
use App\Models\MooeClassification;
use App\Models\MooeAccountTitle;

class FundUtilizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AllotmentClass::truncate();
        MooeClassification::truncate();
        MooeAccountTitle::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            AllotmentClassSeeder::class,
            MOOEClassificationSeeder::class,
            MOOEAccountTitleSeeder::class,
            OrsDvFundingSourceSeeder::class,
        ]);
    }
}
