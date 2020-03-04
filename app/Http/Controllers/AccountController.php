<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Models\EmpRole;
use App\Models\EmpGroup;
use App\Division;
use App\Province;
use App\Region;
use App\Role;
use Auth;
use \Image;
use DB;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexProfile() {
        $id = Auth::user()->id;
        $employee = User::find($id);
        $divisions = Division::orderBy('division_name')->get();
        $provinces = Province::orderBy('province_name')->get();
        $regions = Region::orderBy('region_name')->get();

        if (Auth::user()->role != 1 && Auth::user()->role != 3 && Auth::user()->role != 4) {
            $roles = Role::where('id', Auth::user()->role)->get();
        } else {
            $roles = Role::all();
        }

        return view('pages.profile', ['employee' => $employee,
                                      'divisions' => $divisions,
                                      'provinces' => $provinces,
                                      'regions' => $regions,
                                      'roles' => $roles]);
    }

    public function showCreateProfile() {

    }

    public function storeProfile(Request $request) {

    }

    public function showEditProfile() {
        $id = Auth::user()->id;
    }

    public function updateProfile(Request $request) {
        $id = Auth::user()->id;
    }

    public function destroyProfile(Request $request) {
        $id = Auth::user()->id;

        try {
            $instanceUser = User::find($id);
            $instanceUser->destroy();

            //$msg = "Item classification '$className' successfully destroyed.";
            //return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }


    public function create() {
        $divisions = Division::all();
        $provinces = Province::all();
        $regions = Region::all();
        $roles = Role::all();
        $userGroups = DB::table('tblemp_groups as group')
                        ->select('group.id as group_id', DB::raw('CONCAT(emp.firstname, " ",
                                 emp.lastname, " [ ", emp.position, " ]") as group_head'),
                                 'group.group_name')
                        ->join('tblemp_accounts as emp', 'emp.id', '=', 'group.group_head')
                        ->orderBy('group.group_name')
                        ->get();

        $empID = "";
        $firstname = "";
        $middlename = "";
        $lastname = "";
        $address = "";
        $province = "";
        $region = "";
        $mobileNo = "";
        $email = "";
        $username = "";
        $password = "";
        $role = "";
        $empType = "";
        $position = "";
        $division = "";
        $gender = "";
        $active = "";
        $avatar = "";
        $signature = "";
        $group = "";
        $data = (object) ['emp_id' => $empID,
                          'firstname' => $firstname,
                          'middlename' => $middlename,
                          'lastname' => $lastname,
                          'address' => $address,
                          'province_id' => $province,
                          'region_id' => $region,
                          'mobile_no' => $mobileNo,
                          'email' => $email,
                          'username' => $username,
                          'password' => $password,
                          'role' => $role,
                          'emp_type' => $empType,
                          'position' => $position,
                          'division_id' => $division,
                          'gender' => $gender,
                          'active' => $active,
                          'avatar' => $avatar,
                          'signature' => $signature,
                          'group' => $group];

        return view('pages.create-edit-profile', ['divisions' => $divisions,
                                                                'provinces' => $provinces,
                                                                'regions' => $regions,
                                                                'roles' => $roles,
                                                                'userGroups' => $userGroups,
                                                                'toggle' => 'create',
                                                                'data' => $data]);
    }

    public function edit(Request $request, $empID) {
        $emp = User::where('emp_id', $empID)->first();
        $divisions = Division::all();
        $provinces = Province::all();
        $regions = Region::all();
        $roles = Role::all();
        $userGroups = DB::table('tblemp_groups as group')
                        ->select('group.id as group_id', DB::raw('CONCAT(emp.firstname, " ",
                                 emp.lastname, " [ ", emp.position, " ]") as group_head'),
                                 'group.group_name')
                        ->join('tblemp_accounts as emp', 'emp.id', '=', 'group.group_head')
                        ->orderBy('group.group_name')
                        ->get();

        $empID = $emp->emp_id;
        $firstname = $emp->firstname;
        $middlename = $emp->middlename;
        $lastname = $emp->lastname;
        $address = $emp->address;
        $province = $emp->province_id;
        $region = $emp->region_id;
        $mobileNo = $emp->mobile_no;
        $email = $emp->email;
        $username = $emp->username;
        $password = "";
        $role = $emp->role;
        $empType = $emp->emp_type;
        $position = $emp->position;
        $division = $emp->division_id;
        $gender = $emp->gender;
        $active =  $emp->active;
        $avatar = $emp->avatar;
        $signature = $emp->signature;
        $group = $emp->group;
        $data = (object) ['emp_id' => $empID,
                          'firstname' => $firstname,
                          'middlename' => $middlename,
                          'lastname' => $lastname,
                          'address' => $address,
                          'province_id' => $province,
                          'region_id' => $region,
                          'mobile_no' => $mobileNo,
                          'email' => $email,
                          'username' => $username,
                          'password' => $password,
                          'role' => $role,
                          'emp_type' => $empType,
                          'position' => $position,
                          'division_id' => $division,
                          'gender' => $gender,
                          'active' => $active,
                          'avatar' => $avatar,
                          'signature' => $signature,
                          'group' => $group];

        return view('pages.create-edit-profile', ['divisions' => $divisions,
                                                  'provinces' => $provinces,
                                                  'regions' => $regions,
                                                  'roles' => $roles,
                                                  'userGroups' => $userGroups,
                                                  'toggle' => 'update',
                                                  'data' => $data]);
    }

    public function store(Request $request) {
        $emp = new User;
        $empID = $request['emp_id'];
        $firstname = $request['firstname'];
        $middlename = $request['middlename'];
        $lastname = $request['lastname'];
        $address = $request['address'];
        $province = $request['province'];
        $region = $request['region'];
        $mobile_no = $request['mobile_no'];
        $email = $request['email'];
        $username = $request['username'];
        $password = $request['password'];
        $role = $request['role'];
        $position = $request['position'];
        $division = $request['division'];
        $gender = $request['gender'];
        $active = $request['active'];
        $empType = $request['emp_type'];
        $group = $request['group'];
        $avatar = $request->file('avatar');
        $signature = $request->file('signature');

        $emp->emp_id = $empID;
        $emp->firstname = $firstname;
        $emp->middlename = $middlename;
        $emp->lastname = $lastname;
        $emp->address = $address;
        $emp->region_id = $region;
        $emp->province_id = $province;
        $emp->mobile_no = $mobile_no;
        $emp->email = $email;
        $emp->username = $username;
        $emp->role = $role;
        $emp->emp_type = $empType;
        $emp->position = $position;
        $emp->division_id = $division;
        $emp->gender = $gender;
        $emp->active = $active;
        $emp->group = $group;
        $emp->password = bcrypt($password);

        try {
            $emp->emp_id = $empID;
            $emp->firstname = $firstname;
            $emp->middlename = $middlename;
            $emp->lastname = $lastname;
            $emp->address = $address;
            $emp->region_id = $region;
            $emp->province_id = $province;
            $emp->mobile_no = $mobile_no;
            $emp->email = $email;
            $emp->username = $username;
            $emp->role = $role;
            $emp->position = $position;
            $emp->division_id = $division;
            $emp->gender = $gender;
            $emp->active = $active;
            $emp->password = bcrypt($password);
            $emp->avatar = $this->uploadFile($request, 'avatar', $avatar, $emp);
            $emp->signature = $this->uploadFile($request, 'esignature', $signature, $emp);
            $emp->save();

            $msg = "Successfully created the employee $empID.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered creating the employee $empID.";
            return redirect(url()->previous())->with('failed', "Successfully updated the employee $empID.");
        }
    }

    public function update(Request $request, $empID) {
        $emp = User::where('emp_id', $empID)->first();
        $newEmpID = $request['emp_id'];
        $firstname = $request['firstname'];
        $middlename = $request['middlename'];
        $lastname = $request['lastname'];
        $address = $request['address'];
        $province = $request['province'];
        $region = $request['region'];
        $mobile_no = $request['mobile_no'];
        $email = $request['email'];
        $username = $request['username'];
        $password = $request['password'];
        $role = $request['role'];
        $empType = $request['emp_type'];
        $position = $request['position'];
        $division = $request['division'];
        $gender = $request['gender'];
        $active = $request['active'];
        $group = !empty($request['group']) ? $request['group']: 0;
        $avatar = $request->file('avatar');
        $signature = $request->file('signature');

        try {
            $emp->emp_id = $newEmpID;
            $emp->firstname = $firstname;
            $emp->middlename = $middlename;
            $emp->lastname = $lastname;
            $emp->address = $address;
            $emp->region_id = $region;
            $emp->province_id = $province;
            $emp->mobile_no = $mobile_no;
            $emp->email = $email;
            $emp->username = $username;
            $emp->role = $role;
            $emp->emp_type = $empType;
            $emp->position = $position;
            $emp->division_id = $division;
            $emp->gender = $gender;
            $emp->group = $group;
            $pathAvatar = $this->uploadFile($request, 'avatar', $avatar, $emp, $newEmpID);
            $pathSignature = $this->uploadFile($request, 'esignature', $signature, $emp, $newEmpID);

            if (!empty($active)) {
                $emp->active = $active;
            }

            if (!empty($password)) {
                $emp->password = bcrypt($password);
            }

            if (!empty($pathAvatar)) {
                $emp->avatar = $pathAvatar;
            }

            if (!empty($pathSignature)) {
                $emp->signature = $pathSignature;
            }

            $emp->save();

            $msg = "Successfully updated the employee $empID.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the employee $empID.";
            return redirect(url()->previous())->with('failed', "Successfully updated the employee $empID.");
        }
    }

    public function delete($empID) {
        try {
            $emp = User::where('emp_id', $empID)->first();
            $_emp = User::where('emp_id', $empID)->delete();
            $empID = $emp->emp_id;

            $msg = "Successfully deleted the employee $empID.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered deleting the employee $empID.";
            return redirect(url()->previous())->with('failed', "Successfully updated the employee $empID.");
        }
    }

    private function uploadFile($request, $type, $file, $db, $fileID = "") {
        $path = "";

        if (!empty($file)) {
            switch ($type) {
                case 'avatar':
                    $this->validate($request, [
                        'avatar' => 'dimensions:max_width=900,max_width=900|mimes:jpg,jpeg'
                    ]);

                    $newFileName = 'avatar-' . strtolower($fileID) . '.jpg';
                    $exists = Storage::exists($db->avatar);

                    $path = 'storage/images/employees/avatars/' . $newFileName;
                    $image = Image::make($file)->fit(300, 300);
                    Storage::put('public/images/employees/avatars/'.$newFileName, (string) $image->encode());

                    if (!empty($db->avatar)) {
                        if ($exists && ($db->avatar != $path)) {
                            Storage::delete($db->avatar);
                        }
                    }

                    break;

                case 'esignature':
                    $this->validate($request, [
                        'signature' => 'image|mimes:png,jpg|max:512'
                    ]);

                    $newFileName = 'sig-' . strtolower($fileID) . '.png';
                    $exists = Storage::exists($db->signature);

                    $path = 'storage/images/employees/signatures/' . $newFileName;
                    $request->file('signature')->storeAs(
                                'public/images/employees/signatures', $newFileName
                    );

                    if (!empty($db->signature)) {
                        if ($exists && ($path != $db->signature)) {
                            Storage::delete($db->signature);
                        }
                    }

                    break;

                default:
                    # code...
                    break;
                }
        }

        return $path;
    }

    /** Library for Roles, Groups, and Accounts */

    /**
     *  Employee Role Module
    **/
    public function indexRole(Request $request) {

    }

    public function showCreateRole() {

    }

    public function showEditRole($id) {

    }

    public function storeRole(Request $request) {

    }

    public function updateRole(Request $request, $id) {

    }

    public function deleteRole($id) {

    }

    public function destroyRole($id) {

    }

    /**
     *  Employee Group Module
    **/
    public function indexGroup(Request $request) {
        $userGroupData = EmpGroup::addSelect([
            'head_name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                               ->whereColumn('id', 'emp_groups.group_head')
                               ->limit(1)
        ])->orderBy('group_name')->get();

        return view('modules.library.group.index', [
            'list' => $userGroupData
        ]);
    }

    public function showCreateGroup() {
        $usersData = User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                  'position', 'id')
                         ->orderBy('firstname')
                         ->get();

        return view('modules.library.group.create', [
            'employees' => $usersData
        ]);
    }

    public function showEditGroup($id) {
        $usersData = User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                  'position', 'id')
                         ->orderBy('firstname')
                         ->get();
        $userGroupData = EmpGroup::find($id);
        $groupName = $userGroupData->group_name;
        $groupHead = $userGroupData->group_head;

        return view('modules.library.group.update', [
            'id' => $id,
            'groupName' => $groupName,
            'groupHead' => $groupHead,
            'employees' => $usersData
        ]);
    }

    public function storeGroup(Request $request) {
        $groupName = $request->group_name;
        $groupHead = $request->group_head;

        try {
            if (!$this->checkDuplication('EmpGroup', $groupName)) {
                $instanceEmpGroup = new EmpGroup;
                $instanceEmpGroup->group_name = $groupName;
                $instanceEmpGroup->group_head = $groupHead;
                $instanceEmpGroup->save();

                $msg = "Employee group '$groupName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Employee group '$groupName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateGroup(Request $request, $id) {
        $groupName = $request->group_name;
        $groupHead = $request->group_head;

        try {
            if (!$this->checkDuplication('EmpGroup', $groupName)) {
                $instanceEmpGroup = EmpGroup::find($id);
                $instanceEmpGroup->group_name = $groupName;
                $instanceEmpGroup->group_head = $groupHead;
                $instanceEmpGroup->save();

                $msg = "Employee group '$groupName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Employee group '$groupName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteGroup($id) {
        try {
            $instanceEmpGroup = EmpGroup::find($id);
            $groupName = $instanceEmpGroup->group_name;
            $instanceEmpGroup->delete();

            $msg = "Employee group '$groupName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyGroup($id) {
        try {
            $instanceEmpGroup = EmpGroup::find($id);
            $groupName = $instanceEmpGroup->group_name;
            $instanceEmpGroup->destroy();

            $msg = "Employee group '$groupName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Employee Acount Module
    **/
    public function indexAccount(Request $request) {

    }

    public function showCreateAccount() {

    }

    public function showEditAccount($id) {

    }

    public function storeAccount(Request $request) {

    }

    public function updateAccount(Request $request, $id) {

    }

    public function deleteAccount($id) {

    }

    public function destroyAccount($id) {

    }

    public function checkDuplication($model, $data) {
        switch ($model) {
            case 'EmpRole':
                $dataCount = EmpRole::where('role', $data)
                                    ->orWhere('role', strtolower($data))
                                    ->orWhere('role', strtoupper($data))
                                    ->count();
                break;
            case 'EmpGroup':
                $dataCount = EmpGroup::where('group_name', $data)
                                     ->orWhere('group_name', strtolower($data))
                                     ->orWhere('group_name', strtoupper($data))
                                     ->count();
                break;
            case 'User':
                $firstname = trim($data->firstname);
                $lastname = trim($data->lastname);
                $dataCount = User::where([
                    ['firstname', 'LIKE', "%$firstname%"],
                    ['lastname', 'LIKE', "%$lastname%"]
                ])->count();
                break;
            default:
                $dataCount = 0;
                break;
        }

        return ($dataCount > 0) ? 1 : 0;;
    }
}
