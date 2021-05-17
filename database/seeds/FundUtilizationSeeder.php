<?php

use Illuminate\Database\Seeder;

class FundUtilizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AllotmentClassSeeder::class,
            MOOEClassificationSeeder::class,
            MOOEAccountTitleSeeder::class,
        ]);
    }
}
