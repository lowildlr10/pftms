<?php

use Illuminate\Database\Seeder;
use App\Models\MooeClassification;

class MOOEClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        /*
        $classifications = [
            ['classification_name' => 'Traveling Expenses'],
            ['classification_name' => 'Training and Scholarship Expenses'],
            ['classification_name' => 'Supplies and Materials Expenses'],
            ['classification_name' => 'Utility Expenses'],
            ['classification_name' => 'Communication Expenses'],
            ['classification_name' => 'Awards/Rewards, Prizes and Indemnities'],
            ['classification_name' => 'Survey, Research, Exploration and Development Expenses'],
            ['classification_name' => 'Generation, Transmission and Distribution Expenses'],
            ['classification_name' => 'Confidential, Intelligence and Extraordinary Expenses'],
            ['classification_name' => 'Professional Services'],
            ['classification_name' => 'General Services'],
            ['classification_name' => 'Repairs and Maintenance'],
            ['classification_name' => 'Financial Assistance/Subsidy'],
            ['classification_name' => 'Taxes, Insurance Premiums and Other Fees'],
            ['classification_name' => 'Labor and Wages'],
            ['classification_name' => 'Other Maintenance and Operating Expenses'],
        ];

        MooeClassification::insert($classifications);*/

        $classifications = [
            'Traveling Expenses',
            'Training and Scholarship Expenses',
            'Supplies and Materials Expenses',
            'Utility Expenses',
            'Communication Expenses',
            'Awards/Rewards, Prizes and Indemnities',
            'Survey, Research, Exploration and Development Expenses',
            'Generation, Transmission and Distribution Expenses',
            'Confidential, Intelligence and Extraordinary Expenses',
            'Professional Services',
            'General Services',
            'Repairs and Maintenance',
            'Financial Assistance/Subsidy',
            'Taxes, Insurance Premiums and Other Fees',
            'Labor and Wages',
            'Other Maintenance and Operating Expenses',
        ];

        foreach ($classifications as $class) {
            try {
                $instanceMooeClass = new MooeClassification;
                $instanceMooeClass->classification_name = $class;
                $instanceMooeClass->save();

                echo "MOOE Classification '$class' successfully created.\n";
            } catch (\Throwable $th) {
                echo "There is an error in seeding MOOE Classification.\n";
            }
        }
    }
}
