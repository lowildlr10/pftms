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
            'Capital Outlay (CO)',
            'Maintenance and other Operating Expenses (MOOE)'
        ];

        foreach ($classes as $class) {
            try {
                $instanceAllotmentClass = new AllotmentClass;
                $instanceAllotmentClass->class_name = $class;
                $instanceAllotmentClass->save();

                echo "Fund Allotment Class '$class' successfully created.\n";
            } catch (\Throwable $th) {
                echo "There is an error in seeding Fund Allotment Class.\n";
            }
        }
    }
}
