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
        $user->role = $empRole->id;
        $user->firstname = 'Lennel Threian';
        $user->middlename = 'Lapitan';
        $user->lastname = 'Estabaya';
        $user->gender = 'male';
        $user->position = 'MIS';
        $user->emp_type = 'contractual';
        $user->username = 'dostcarmis';
        $user->email = 'dostcar.mis@gmail.com';
        $user->email_verified_at = Carbon::now();
        $user->password =  Hash::make('dostcarmis');
        $user->address = 'DOST-CAR Km.6 BSU Compound La Trinidad, Benguet';
        $user->mobile_no = '+639999999999';
        $user->save();
    }
}
