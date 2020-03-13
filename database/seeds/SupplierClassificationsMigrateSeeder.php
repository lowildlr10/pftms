<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SupplierClassification;

class SupplierClassificationsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supClassData = DB::connection('mysql-old-pftms')
                          ->table('tblsupplier_classifications')
                          ->get();

        foreach ($supClassData as $class) {
            $supClass = new SupplierClassification;
            $supClass->classification_name = $class->classification;
            $supClass->save();
        }
    }
}
