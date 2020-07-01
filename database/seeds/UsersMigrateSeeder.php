<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\EmpRole;
use App\Models\EmpDivision;
use App\Models\Region;
use App\Models\Province;

class UsersMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersData = DB::connection('mysql-old-pftms')
                       ->table('tblemp_accounts')
                       ->get();
        $dataCount = $usersData->count();

        foreach ($usersData as $ctr => $usr) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Emp Accounts: [ $percentage% ] migrated.\n";

            $user = new User;
            $user->emp_id = $usr->emp_id;
            $user->firstname = $usr->firstname;
            $user->middlename = $usr->middlename;
            $user->lastname = $usr->lastname;
            $user->address = $usr->address;
            $user->mobile_no = $usr->mobile_no;
            $user->email = $usr->email;
            $user->username = $usr->username;
            $user->password = $usr->password;
            $user->emp_type = $usr->emp_type;
            $user->position = $usr->position;
            $user->gender = $usr->gender;
            $user->signature = $usr->signature;
            $user->avatar = $usr->avatar;
            $user->is_active = $usr->active;
            $user->last_login = $usr->last_login;
            $user->deleted_at = $usr->deleted_at;
            $user->created_at = $usr->created_at;
            $user->updated_at = $usr->updated_at;

            $regionData = DB::connection('mysql-old-pftms')->table('tblregion')->where('id', $usr->region_id)->first();
            $region = Region::where('region_name', 'like', "%".$regionData->region."%")->first();
            $provinceData = DB::connection('mysql-old-pftms')->table('tblprovince')->where('id', $usr->province_id)->first();
            $province = Province::where('province_name', 'like', "%".$provinceData->province."%")->first();
            $divisionData = DB::connection('mysql-old-pftms')->table('tbldivision')->where('id', $usr->division_id)->first();
            $empDivision = EmpDivision::where('division_name', 'like', "%".$divisionData->division."%")->first();
            $roleData = DB::connection('mysql-old-pftms')->table('tblemp_role')->where('id', $usr->role)->first();
            $empRole = EmpRole::where('role', 'like', "%".$roleData->role."%")->first();

            $user->roles = serialize([$empRole->id]);
            $user->region = $region->id;
            $user->province = $province->id;
            $user->division = $empDivision->id;
            $user->save();
        }
    }
}
