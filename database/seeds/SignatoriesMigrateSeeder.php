<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Signatory;
use App\User;


class SignatoriesMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $signatoryData = DB::connection('mysql-old-pftms')
                           ->table('tblsignatories')
                           ->get();
        $signatories = [];

        foreach ($signatoryData as $sig) {
            $userData = DB::connection('mysql-old-pftms')
                              ->table('tblsupplier_classifications')
                              ->where('id', $sup->class_id)
                              ->first();
            $user = User::where('emp_id', $sig->emp_id)
                        ->first();


        }
    }
}
