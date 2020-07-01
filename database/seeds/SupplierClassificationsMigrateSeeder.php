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
        $dataCount = $supClassData->count();

        foreach ($supClassData as $ctr => $class) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Supplier Classifications: [ $percentage% ] migrated.\n";

            $supClass = new SupplierClassification;
            $supClass->classification_name = $class->classification;
            $supClass->save();
        }
    }
}
