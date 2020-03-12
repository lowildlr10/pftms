<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ItemClassification;

class ItemClassificationsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itmClassData = DB::connection('mysql-old-pftms')
                          ->table('tblitem_classifications')
                          ->get();

        foreach ($itmClassData as $class) {
            $itemClass = new ItemClassification;
            $itemClass->classification_name = $class->classification;
            $itemClass->save();
        }
    }
}
