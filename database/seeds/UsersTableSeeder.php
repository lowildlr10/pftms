<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User;
use App\Models\EmpRole;
use App\Models\EmpDivision;
use App\Models\Region;
use App\Models\Province;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $region = Region::where('region_name', 'like', "%cordillera%")->first();
        $province = Province::where('province_name', 'like', "%benguet%")->first();
        $empRole = EmpRole::where('role', 'like', "%developer%")->first();
        $empDivision = EmpDivision::where('division_name', 'like', "%office of the%")->first();

        $user->emp_id = 'MIS';
        $user->division = $empDivision->id;
        $user->province = $province->id;
        $user->region = $region->id;
        $user->roles = serialize([$empRole->id]);
        $user->firstname = 'Admin';
        $user->middlename = 'Admin';
        $user->lastname = 'Admin';
        $user->gender = 'male';
        $user->position = 'MIS';
        $user->emp_type = 'contractual';
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->email_verified_at = Carbon::now();
        $user->password =  Hash::make('admin');
        $user->address = 'Change address';
        $user->mobile_no = '+639999999999';
        $user->is_active = 'y';
        $user->save();
    }
}
