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

        foreach ($moduleClass as $class) {
            DB::table('module_classifications')
              ->insert([
                    'id' => $class->id,
                    'classification' => $class->classification
              ]);
        }
    }
}
