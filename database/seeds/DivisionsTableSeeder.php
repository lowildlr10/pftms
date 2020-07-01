<?php

use Illuminate\Database\Seeder;
use App\Models\EmpDivision;

class DivisionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisions = [
            'Technical Services Division',
            'Finance and Administrative Services',
            'Office of the Regional Director',
            'USTC - Baguio',
            'PSTC - Mountain Province',
            'PSTC - Abra',
            'PSTC - Apayao',
            'PSTC - Benguet',
            'PSTC - Ifugao',
            'PSTC - Kalinga'
        ];

        $dataCount = count($divisions);

        foreach ($divisions as $ctr => $div) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Inventory Stocks: [ $percentage% ] migrated.\n";

            $division = new EmpDivision;
            $division->division_name = $div;
            $division->save();
        }
    }
}
