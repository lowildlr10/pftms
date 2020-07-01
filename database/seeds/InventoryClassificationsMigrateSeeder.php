<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryClassification;

class InventoryClassificationsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invClassData = DB::connection('mysql-old-pftms')
                          ->table('tblinventory_classification')
                          ->get();
        $dataCount = $invClassData->count();

        foreach ($invClassData as $ctr => $class) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Inventory Classifications: [ $percentage% ] migrated.\n";

            $invClass = new InventoryClassification;
            $invClass->classification_name = $class->classification;

            if (strpos(strtolower($invClass->classification_name), 'ris') !== false) {
                $invClass->abbrv = 'RIS';
            }

            if (strpos(strtolower($invClass->classification_name), 'ics') !== false) {
                $invClass->abbrv = 'ICS';
            }

            if (strpos(strtolower($invClass->classification_name), 'par') !== false) {
                $invClass->abbrv = 'PAR';
            }

            $invClass->save();
        }
    }
}
