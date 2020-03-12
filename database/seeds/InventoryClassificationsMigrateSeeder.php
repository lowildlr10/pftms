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

        foreach ($invClassData as $class) {
            $invClass = new InventoryClassification;
            $invClass->classification_name = $class->classification;
            $invClass->save();
        }
    }
}
