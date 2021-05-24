<?php

use Illuminate\Database\Seeder;
use App\Models\AllotmentClass;

class AllotmentClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $classes = [
            'Personnel Services',
            'Maintenance and other Operating Expenses',
            'Financial Expenses',
            'Capital Outlay',
        ];

        $classCodes = [
            'PS',
            'MOOE',
            'FinEx',
            'CO',
        ];

        foreach ($classes as $ctr => $class) {
            try {
                $instanceAllotmentClass = new AllotmentClass;
                $instanceAllotmentClass->code = $classCodes[$ctr];
                $instanceAllotmentClass->class_name = $class;
                $instanceAllotmentClass->order_no = $ctr + 1;
                $instanceAllotmentClass->save();

                echo "Fund Allotment Class '$class' successfully created.\n";
            } catch (\Throwable $th) {
                echo "There is an error in seeding Fund Allotment Class.\n";
            }
        }
    }
}
