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

        foreach ($unitIssueData as $unit) {
            $unitIssue = new ItemUnitIssue;
            $unitIssue->unit_name = $unit->unit;
            $unitIssue->save();
        }
    }
}
