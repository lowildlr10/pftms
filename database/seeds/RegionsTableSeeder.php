<?php

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            'National Capital Region',
            'Cordillera Administrative Region',
            'Region I',
            'Region II',
            'Region III',
            'Region IV-A',
            'Region IV-B',
            'Region V',
            'Region VI',
            'Region VII',
            'Region VIII',
            'Region IX',
            'Region X',
            'Region XI',
            'Region XII',
            'Caraga Administrative Region',
            'Autonomous Region in Muslim Mindanao'
        ];

        foreach ($regions as $reg) {
            $region = new Region;
            $region->region_name = $reg;
            $region->save();
        }
    }
}
