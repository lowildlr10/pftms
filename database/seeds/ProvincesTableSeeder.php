<?php

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Province;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reqion = Region::where('region_name', 'Cordillera Administrative Region')
                        ->first();
        $provinces = [
            'Abra',
            'Apayao',
            'Baguio',
            'Benguet',
            'Ifugao',
            'Kalinga',
            'Mountain Province'
        ];

        foreach ($provinces as $prov) {
            $province = new Province;
            $province->region = $reqion->id;
            $province->province_name = $prov;
            $province->save();
        }
    }
}
