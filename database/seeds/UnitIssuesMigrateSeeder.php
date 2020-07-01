<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ItemUnitIssue;

class UnitIssuesMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unitIssueData = DB::connection('mysql-old-pftms')
                           ->table('tblunit_issue')
                           ->get();
        $dataCount = $unitIssueData->count();

        foreach ($unitIssueData as $ctr => $unit) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Unit of Issues: [ $percentage% ] migrated.\n";

            $unitIssue = new ItemUnitIssue;
            $unitIssue->unit_name = $unit->unit;
            $unitIssue->save();
        }
    }
}
