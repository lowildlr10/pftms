<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleClassificationsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $moduleClass = DB::connection('mysql-old-pftms')
                          ->table('tblmodule_classifications')
                          ->get();
        $dataCount = $moduleClass->count();

        foreach ($moduleClass as $ctr => $class) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Module Classifications: [ $percentage% ] migrated.\n";

            DB::table('module_classifications')
              ->insert([
                    'id' => $class->id,
                    'classification' => $class->classification
              ]);
        }
    }
}
