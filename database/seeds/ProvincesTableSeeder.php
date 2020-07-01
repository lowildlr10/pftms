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

        $dataCount = count($provinces);

        foreach ($provinces as $ctr => $prov) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Provinces: [ $percentage% ] migrated.\n";

            $province = new Province;
            $province->region = $reqion->id;
            $province->province_name = $prov;
            $province->save();
        }
    }
}
