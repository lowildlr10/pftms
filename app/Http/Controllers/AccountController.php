<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\User;
use App\Models\EmpRole;
use App\Models\EmpGroup;
use App\Models\EmpDivision;
use App\Models\EmpLog;
use App\Models\Province;
use App\Models\Region;

use Auth;
use DB;
use Intervention\Image\ImageManagerStatic as Image;

class AccountController extends Controller
{
    protected $moduleLabels = [
        'ca_ors_burs' => 'Cash Advance, Reimbursement, & Liquidation - Obligation/Budget Utilization Report Status',
            'caors_burs_create' => 'Create',
            'caors_burs_update' => 'Update',
            'caors_burs_delete' => 'Delete',
            'caors_burs_destroy' => 'Destroy',
            'caors_burs_issue' => 'Issue',
            'caors_burs_issue_back' => 'Issue Back',
            'caors_burs_receive' => 'Receive',
            'caors_burs_receive_back' => 'Receive Back',
            'caors_burs_obligate' => 'Obligate',
        'ca_dv' => 'Cash Advance, Reimbursement, & Liquidation - Disbursement Voucher',
            'cadv_create' => 'Create',
            'cadv_update' => 'Update',
            'cadv_delete' => 'Delete',
            'cadv_destroy' => 'Destroy',
            'cadv_issue' => 'Issue',
            'cadv_issue_back' => 'Issue Back',
            'cadv_receive' => 'Receive',
            'cadv_receive_back' => 'Receive Back',
            'cadv_payment' => 'Set to For Payment',
        'ca_lr' => 'Cash Advance, Reimbursement, & Liquidation - Liquidation Report',
            'calr_create' => 'Create',
            'calr_update' => 'Update',
            'calr_delete' => 'Delete',
            'calr_destroy' => 'Destroy',
            'calr_issue' => 'Issue',
            'calr_issue_back' => 'Issue Back',
            'calr_receive' => 'Receive',
            'calr_receive_back' => 'Receive Back',
            'calr_liquidate' => 'Liquidate',
        'proc_pr' => 'Procurement - Purchase Request',
            'pr_create' => 'Create',
            'pr_update' => 'Update',
            'pr_delete' => 'Delete',
            'pr_destroy' => 'Destroy',
            'pr_cancel' => 'Cancel',
            'pr_approve' => 'Approve',
            'pr_disapprove' => 'Disapprove',
        'proc_rfq' => 'Procurement - Request for Quotation',
            'rfq_update' => 'Update',
            'rfq_issue' => 'Issue',
            'rfq_receive' => 'Receive',
        'proc_abstract' => 'Procurement - Abstract of Quotation',
            'abstract_create' => 'Create',
            'abstract_update' => 'Update',
            'abstract_delete' => 'Delete',
            'abstract_approve_po_jo' => 'Set to Approved for PO/JO',
        'proc_po_jo' => 'Procurement - Purchase/Job Order',
            'po_jo_update' => 'Update',
            'po_jo_delete' => 'Delete',
            'po_jo_destroy' => 'Destroy',
            'po_jo_signed' => 'signed',
            'po_jo_approve' => 'Approve',
            'po_jo_cancel' => 'Cancel',
            'po_jo_uncancel' => 'Un-cancel',
            'po_jo_issue' => 'Issue',
            'po_jo_receive' => 'Receive',
            'po_jo_obligate' => 'delivery',
            'po_jo_obligate' => 'inspection',
        'proc_ors_burs' => 'Procurement - Obligation/Budget Utilization Report Status',
            'pors_burs_create' => 'Create (From PO/JO)',
            'pors_burs_update' => 'Update',
            'pors_burs_delete' => 'Delete',
            'pors_burs_destroy' => 'Destroy',
            'pors_burs_issue' => 'Issue',
            'pors_burs_issue_back' => 'Issue Back',
            'pors_burs_receive' => 'Receive',
            'pors_burs_receive_back' => 'Receive Back',
            'pors_burs_obligate' => 'Obligate',
        'proc_iar' => 'Procurement - Inspection and Acceptance Report',
            'iar_update' => 'Update',
            'iar_issue' => 'Issue',
            'iar_inspect' => 'inspect',
        'proc_dv' => 'Procurement - Disbursement Voucher',
            'pdv_create' => 'Create',
            'pdv_update' => 'Update',
            'pdv_delete' => 'Delete',
            'pdv_destroy' => 'Destroy',
            'pdv_issue' => 'Issue',
            'pdv_issue_back' => 'Issue Back',
            'pdv_receive' => 'Receive',
            'pdv_receive_back' => 'Receive Back',
            'pdv_payment' => 'payment',
        'pay_lddap' => 'Payment - List of Due and Demandable Accounts Payable',
            'lddap_create' => 'Create',
            'lddap_update' => 'Update',
            'lddap_delete' => 'Delete',
            'lddap_destroy' => 'Destroy',
            'lddap_approval' => 'Approval',
            'lddap_approve' => 'Approve',
            'lddap_summary' => 'Set to For Summary',
        //'inv_stock',
        'track_pr_rfq' => 'Voucher Tracking - PR to RFQ',
        'track_rfq_abs' => 'Voucher Tracking - RFQ ti Abstract',
        'track_abs_po' => 'Voucher Tracking - Abstract to PO/JO',
        'track_po_ors' => 'Voucher Tracking - PO/JO to ORS/BURS',
        'track_po_iar' => 'Voucher Tracking - PO/JO to IAR',
        'track_iar_stock' => 'Voucher Tracking - IAR to PAR/RIS/ICS',
        'track_iar_dv' => 'Voucher Tracking - IAR to DV',
        'track_ors_dv' => 'Voucher Tracking - ORS/BURS to DV',
        'track_dv_lddap' => 'Voucher Tracking - DV to LDDAP',
        'track_dis_sum' => 'Voucher Tracking - Disburse to Summary',
        'track_sum_bank' => 'Voucher Tracking - Summary to Bank',
        'lib_inv_class' => 'Libraries - Inventory Classifications',
            'inv_class_create' => 'Create',
            'inv_class_update' => 'Update',
            'inv_class_delete' => 'Delete',
            'inv_class_destroy' => 'Destroy',
        'lib_item_class' => 'Libraries - Item Classifications',
            'item_class_create' => 'Create',
            'item_class_update' => 'Update',
            'item_class_delete' => 'Delete',
            'item_class_destroy' => 'Destroy',
        'lib_proc_mode' => 'Libraries - Modes of Procurement',
            'proc_mode_create' => 'Create',
            'proc_mode_update' => 'Update',
            'proc_mode_delete' => 'Delete',
            'proc_mode_destroy' => 'Destroy',
        'lib_funding' => 'Libraries - Source of Funds',
            'funding_create' => 'Create',
            'funding_update' => 'Update',
            'funding_delete' => 'Delete',
            'funding_destroy' => 'Destroy',
        'lib_signatory' => 'Libraries - Signatories',
            'signatory_create' => 'Create',
            'signatory_update' => 'Update',
            'signatory_delete' => 'Delete',
            'signatory_destroy' => 'Destroy',
        'lib_sup_class' => 'Libraries - Supplier Classification',
            'sup_class_create' => 'Create',
            'sup_class_update' => 'Update',
            'sup_class_delete' => 'Delete',
            'sup_class_destroy' => 'Destroy',
        'lib_supplier' => 'Libraries - Suppliers',
            'supplier_create' => 'Create',
            'supplier_update' => 'Update',
            'supplier_delete' => 'Delete',
            'supplier_destroy' => 'Destroy',
        'lib_unit_issue' => 'Libraries - Unit of Issues',
            'unit_issue_create' => 'Create',
            'unit_issue_update' => 'Update',
            'unit_issue_delete' => 'Delete',
            'unit_issue_destroy' => 'Destroy',
        'lib_paper_size' => 'Libraries - Paper Sizes',
            'paper_size_create' => 'Create',
            'paper_size_update' => 'Update',
            'paper_size_delete' => 'Delete',
            'paper_size_destroy' => 'Destroy',
        'acc_division' => 'Accounts Management - Divisions',
            'division_create' => 'Create',
            'division_update' => 'Update',
            'division_delete' => 'Delete',
            'division_destroy' => 'Destroy',
        'acc_role' => 'Accounts Management - Roles',
            'role_create' => 'Create',
            'role_update' => 'Update',
            'role_delete' => 'Delete',
            'role_destroy' => 'Destroy',
        'acc_group' => 'Accounts Management - Groups',
            'group_create' => 'Create',
            'group_update' => 'Update',
            'group_delete' => 'Delete',
            'group_destroy' => 'Destroy',
        'acc_account' => 'Accounts Management - User Accounts',
            'account_create' => 'Create',
            'account_update' => 'Update',
            'account_delete' => 'Delete',
            'account_destroy' => 'Destroy',
        'acc_user_log' => 'Accounts Management - User Logs',
            'user_log_destroy' => 'Destroy',
        'place_region' => 'Places - Regions',
            'region_create' => 'Create',
            'region_update' => 'Update',
            'region_delete' => 'Delete',
            'region_destroy' => 'Destroy',
        'place_province' => 'Places - Provinces',
            'province_create' => 'Create',
            'province_update' => 'Update',
            'province_delete' => 'Delete',
            'province_destroy' => 'Destroy',
    ];
    protected $modules = [
        'ca_ors_burs' => [
            'caors_burs_create' => 'create',
            'caors_burs_update' => 'update',
            'caors_burs_delete' => 'delete',
            'caors_burs_destroy' => 'destroy',
            'caors_burs_issue' => 'issue',
            'caors_burs_issue_back' => 'issue_back',
            'caors_burs_receive' => 'receive',
            'caors_burs_receive_back' => 'receive_back',
            'caors_burs_obligate' => 'obligate',
        ],
        'ca_dv' => [
            'cadv_create' => 'create',
            'cadv_update' => 'update',
            'cadv_delete' => 'delete',
            'cadv_destroy' => 'destroy',
            'cadv_issue' => 'issue',
            'cadv_issue_back' => 'issue_back',
            'cadv_receive' => 'receive',
            'cadv_receive_back' => 'receive_back',
            'cadv_payment' => 'payment',
        ],
        'ca_lr' => [
            'calr_create' => 'create',
            'calr_update' => 'update',
            'calr_delete' => 'delete',
            'calr_destroy' => 'destroy',
            'calr_issue' => 'issue',
            'calr_issue_back' => 'issue_back',
            'calr_receive' => 'receive',
            'calr_receive_back' => 'receive_back',
            'calr_liquidate' => 'liquidate',
        ],
        'proc_pr' => [
            'pr_create' => 'create',
            'pr_update' => 'update',
            'pr_delete' => 'delete',
            'pr_destroy' => 'destroy',
            'pr_cancel' => 'cancel',
            'pr_approve' => 'approve',
            'pr_disapprove' => 'disapprove',
        ],
        'proc_rfq' => [
            'rfq_update' => 'update',
            'rfq_issue' => 'issue',
            'rfq_receive' => 'receive',
        ],
        'proc_abstract' => [
            'abstract_create' => 'create',
            'abstract_update' => 'update',
            'abstract_delete' => 'delete',
            'abstract_approve_po_jo' => 'approve_po_jo',
        ],
        'proc_po_jo' => [
            'po_jo_update' => 'update',
            'po_jo_delete' => 'delete',
            'po_jo_destroy' => 'destroy',
            'po_jo_signed' => 'signed',
            'po_jo_approve' => 'approve',
            'po_jo_cancel' => 'cancel',
            'po_jo_cancel' => 'uncancel',
            'po_jo_issue' => 'issue',
            'po_jo_receive' => 'receive',
            'po_jo_obligate' => 'delivery',
            'po_jo_obligate' => 'inspection',
        ],
        'proc_ors_burs' => [
            'pors_burs_create' => 'create',
            'pors_burs_update' => 'update',
            'pors_burs_delete' => 'delete',
            'pors_burs_destroy' => 'destroy',
            'pors_burs_issue' => 'issue',
            'pors_burs_issue_back' => 'issue_back',
            'pors_burs_receive' => 'receive',
            'pors_burs_receive_back' => 'receive_back',
            'pors_burs_obligate' => 'obligate',
        ],
        'proc_iar' => [
            'iar_update' => 'update',
            'iar_issue' => 'issue',
            'iar_inspect' => 'inspect',
        ],
        'proc_dv' => [
            'pdv_create' => 'create',
            'pdv_update' => 'update',
            'pdv_delete' => 'delete',
            'pdv_destroy' => 'destroy',
            'pdv_issue' => 'issue',
            'pdv_issue_back' => 'issue_back',
            'pdv_receive' => 'receive',
            'pdv_receive_back' => 'receive_back',
            'pdv_payment' => 'payment',
        ],
        'pay_lddap' => [
            'lddap_create' => 'create',
            'lddap_update' => 'update',
            'lddap_delete' => 'delete',
            'lddap_destroy' => 'destroy',
            'lddap_approval' => 'approval',
            'lddap_approve' => 'approve',
            'lddap_summary' => 'summary',
        ],
        //'inv_stock',
        'track_pr_rfq' => [],
        'track_rfq_abs' => [],
        'track_abs_po' => [],
        'track_po_ors' => [],
        'track_po_iar' => [],
        'track_iar_stock' => [],
        'track_iar_dv' => [],
        'track_ors_dv' => [],
        'track_dv_lddap' => [],
        'track_dis_sum' => [],
        'track_sum_bank' => [],
        'lib_inv_class' => [
            'inv_class_create' => 'create',
            'inv_class_update' => 'update',
            'inv_class_delete' => 'delete',
            'inv_class_destroy' => 'destroy',
        ],
        'lib_item_class' => [
            'item_class_create' => 'create',
            'item_class_update' => 'update',
            'item_class_delete' => 'delete',
            'item_class_destroy' => 'destroy',
        ],
        'lib_proc_mode' => [
            'proc_mode_create' => 'create',
            'proc_mode_update' => 'update',
            'proc_mode_delete' => 'delete',
            'proc_mode_destroy' => 'destroy',
        ],
        'lib_funding' => [
            'funding_create' => 'create',
            'funding_update' => 'update',
            'funding_delete' => 'delete',
            'funding_destroy' => 'destroy',
        ],
        'lib_signatory' => [
            'signatory_create' => 'create',
            'signatory_update' => 'update',
            'signatory_delete' => 'delete',
            'signatory_destroy' => 'destroy',
        ],
        'lib_sup_class' => [
            'sup_class_create' => 'create',
            'sup_class_update' => 'update',
            'sup_class_delete' => 'delete',
            'sup_class_destroy' => 'destroy',
        ],
        'lib_supplier' => [
            'supplier_create' => 'create',
            'supplier_update' => 'update',
            'supplier_delete' => 'delete',
            'supplier_destroy' => 'destroy',
        ],
        'lib_unit_issue' => [
            'unit_issue_create' => 'create',
            'unit_issue_update' => 'update',
            'unit_issue_delete' => 'delete',
            'unit_issue_destroy' => 'destroy',
        ],
        'lib_paper_size' => [
            'paper_size_create' => 'create',
            'paper_size_update' => 'update',
            'paper_size_delete' => 'delete',
            'paper_size_destroy' => 'destroy',
        ],
        'acc_division' => [
            'division_create' => 'create',
            'division_update' => 'update',
            'division_delete' => 'delete',
            'division_destroy' => 'destroy',
        ],
        'acc_role' => [
            'role_create' => 'create',
            'role_update' => 'update',
            'role_delete' => 'delete',
            'role_destroy' => 'destroy',
        ],
        'acc_group' => [
            'group_create' => 'create',
            'group_update' => 'update',
            'group_delete' => 'delete',
            'group_destroy' => 'destroy',
        ],
        'acc_account' => [
            'account_create' => 'create',
            'account_update' => 'update',
            'account_delete' => 'delete',
            'account_destroy' => 'destroy',
        ],
        'acc_user_log' => [
            'user_log_destroy' => 'destroy',
        ],
        'place_region' => [
            'region_create' => 'create',
            'region_update' => 'update',
            'region_delete' => 'delete',
            'region_destroy' => 'destroy',
        ],
        'place_province' => [
            'province_create' => 'create',
            'province_update' => 'update',
            'province_delete' => 'delete',
            'province_destroy' => 'destroy',
        ],
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*
    public function __construct()
    {
        $this->middleware('auth');
    }*/

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexProfile($type = 'profile') {
        $id = Auth::user()->id;
        $userData = User::addSelect([
            'division' => EmpDivision::select('division_name')
                                     ->whereColumn('id', 'emp_accounts.division')
                                     ->limit(1),
            'province' => Province::select('province_name')
                                  ->whereColumn('id', 'emp_accounts.province')
                                  ->limit(1),
            'region' => Region::select('region_name')
                              ->whereColumn('id', 'emp_accounts.region')
                              ->limit(1),
            'group' => EmpGroup::select('group_name')
                              ->whereColumn('id', 'emp_accounts.group')
                              ->limit(1),
            'role' => EmpRole::select('role')
                             ->whereColumn('id', 'emp_accounts.role')
                             ->limit(1)
        ])->where('id', $id)->first();

        $employeeID = $userData->emp_id;
        $division = $userData->division;
        $province = $userData->province;
        $region = $userData->region;
        $group = $userData->group;
        $role = $userData->role;
        $firstname = $userData->firstname;
        $middlename = $userData->middlename;
        $lastname = $userData->lastname;
        $gender = $userData->gender;
        $position = $userData->position;
        $employType = $userData->emp_type;
        $username = $userData->username;
        $email = $userData->email;
        $address = $userData->address;
        $mobileNo = $userData->mobile_no;
        $avatar = $userData->avatar;
        $signature = $userData->signature;
        $lastLogin = $userData->last_login;

        return view('modules.profile.index', [
            'employeeID' => $employeeID,
            'division' => $division,
            'province' => $province,
            'region' => $region,
            'role' => $role,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'gender' => $gender,
            'position' => $position,
            'employType' => $employType,
            'username' => $username,
            'email' => $email,
            'address' => $address,
            'mobileNo' => $mobileNo,
            'avatar' => $avatar,
            'signature' => $signature,
            'lastLogin' => $lastLogin,
        ]);
    }

    public function showCreateProfile($type = 'profile') {
        $divisions = EmpDivision::orderBy('division_name')->get();
        $provinces = Province::orderBy('province_name')->get();
        $regions = Region::orderBy('region_name')->get();

        $viewDir = 'modules.profile.create';
        $flashData = [
            'divisions' => $divisions,
            'provinces' => $provinces,
            'regions' => $regions
        ];

        if ($type != 'profile') {
            $roles = EmpRole::orderBy('role')->get();
            $groups = EmpGroup::orderBy('group_name')->get();
            $viewDir = 'modules.library.account.create';
            $flashData['roles'] = $roles;
            $flashData['groups'] = $groups;

            return (object) [
                'view_dir' => $viewDir,
                'flash_data' => $flashData
            ];
        }

        return view($viewDir, $flashData);
    }

    public function showEditProfile($type = 'profile', $_id = '') {
        $id = $type == 'profile' ? Auth::user()->id : $_id;
        $divisions = EmpDivision::orderBy('division_name')->get();
        $provinces = Province::orderBy('province_name')->get();
        $regions = Region::orderBy('region_name')->get();

        $userData = User::find($id);
        $employeeID = $userData->emp_id;
        $division = $userData->division;
        $province = $userData->province;
        $region = $userData->region;
        $firstname = $userData->firstname;
        $middlename = $userData->middlename;
        $lastname = $userData->lastname;
        $gender = $userData->gender;
        $position = $userData->position;
        $employType = $userData->emp_type;
        $username = $userData->username;
        $email = $userData->email;
        $address = $userData->address;
        $mobileNo = $userData->mobile_no;
        $avatar = $userData->avatar;
        $signature = $userData->signature;

        $flashData = [
            'id' => $id,
            'divisions' => $divisions,
            'provinces' => $provinces,
            'regions' => $regions,
            'employeeID' => $employeeID,
            'division' => $division,
            'province' => $province,
            'region' => $region,
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
            'gender' => $gender,
            'position' => $position,
            'employType' => $employType,
            'username' => $username,
            'email' => $email,
            'address' => $address,
            'mobileNo' => $mobileNo,
            'avatar' => $avatar,
            'signature' => $signature,
        ];

        if ($type != 'profile') {
            $roles = EmpRole::orderBy('role')->get();
            $groups = EmpGroup::orderBy('group_name')->get();
            $viewDir = 'modules.library.account.update';
            $role = !empty($userData->roles) ? unserialize($userData->roles) : [];
            $group = !empty($userData->groups) ? unserialize($userData->groups) : [];
            $isActive = $userData->is_active;
            $flashData['roles'] = $roles;
            $flashData['groups'] = $groups;
            $flashData['role'] = $role;
            $flashData['group'] = $group;
            $flashData['isActive'] = $isActive;

            return (object) [
                'view_dir' => $viewDir,
                'flash_data' => $flashData
            ];
        }

        return view('modules.profile.update', $flashData);
    }

    public function storeProfile(Request $request, $type = 'profile') {
        $data = $this->storeUser($request);

        return redirect('login')->with($data->alert_type, $data->msg);
    }

    public function updateProfile(Request $request) {
        $data = $this->updateUser($request);

        return redirect(url()->previous())->with($data->alert_type, $data->msg);
    }

    private function storeUser($request, $type = 'profile') {
        $empID = $request->emp_id;
        $firstname = $request->firstname;
        $middlename = $request->middlename;
        $lastname = $request->lastname;
        $address = $request->address;
        $province = $request->province;
        $region = $request->region;
        $mobileNo = $request->mobile_no;
        $email = $request->email;
        $username = $request->username;
        $password = $request->password;
        $empType = $request->emp_type;
        $position = $request->position;
        $division = $request->division;
        $gender = $request->gender;
        $avatar = $request->file('avatar');
        $signature = $request->file('signature');

        if ($type != 'profile') {
            $roles = $request->roles ? serialize($request->roles) : NULL;
            $groups = $request->groups ? serialize($request->groups) : NULL;
            $isActive = $request->is_active;
        }

        try {
            $instanceEmpAccount = new User;
            $instanceEmpAccount->emp_id = $empID;
            $instanceEmpAccount->firstname = $firstname;
            $instanceEmpAccount->middlename = $middlename;
            $instanceEmpAccount->lastname = $lastname;
            $instanceEmpAccount->address = $address;
            $instanceEmpAccount->province = $province;
            $instanceEmpAccount->region = $region;
            $instanceEmpAccount->mobile_no = $mobileNo;
            $instanceEmpAccount->email = $email;
            $instanceEmpAccount->username = $username;
            $instanceEmpAccount->emp_type = $empType;
            $instanceEmpAccount->position = $position;
            $instanceEmpAccount->division = $division;
            $instanceEmpAccount->gender = $gender;

            if ($type != 'profile') {
                $instanceEmpAccount->roles = $roles;
                $instanceEmpAccount->groups = $groups;
                $instanceEmpAccount->is_active = $isActive;
            }

            $msgAlertType = '';
            $msg = '';

            $pathAvatar = $this->uploadFile($request, 'avatar', $avatar, $instanceEmpAccount, $empID);
            $pathSignature = $this->uploadFile($request, 'esignature', $signature, $instanceEmpAccount, $empID);

            if (!empty($password)) {
                $instanceEmpAccount->password = bcrypt($password);
            }

            if (!empty($pathAvatar)) {
                $instanceEmpAccount->avatar = $pathAvatar;
            }

            if (!empty($pathSignature)) {
                $instanceEmpAccount->signature = $pathSignature;
            }

            $instanceEmpAccount->save();

            $msgAlertType = 'success';
            $msg = $type == 'profile' ?
                   "Profile successfully created. Please contact your
                    administrator for your account approval." :
                    "User account of '$firstname' with an employee ID of
                    '$empID' successfully created.";
        } catch (\Throwable $th) {
            $msgAlertType = 'failed';
            $msg = "Unknown error has occured. Please try again.";
        }

        return (object) [
            'alert_type' => $msgAlertType,
            'msg' => $msg
        ];
    }

    private function updateUser($request, $type = 'profile', $_id = '') {
        $id = Auth::user()->id;
        $empID = $request->emp_id;
        $firstname = $request->firstname;
        $middlename = $request->middlename;
        $lastname = $request->lastname;
        $address = $request->address;
        $province = $request->province;
        $region = $request->region;
        $mobileNo = $request->mobile_no;
        $email = $request->email;
        $username = $request->username;
        $password = $request->password;
        $empType = $request->emp_type;
        $position = $request->position;
        $division = $request->division;
        $gender = $request->gender;
        $avatar = $request->file('avatar');
        $signature = $request->file('signature');

        if ($type != 'profile') {
            $id = $_id;
            $roles = $request->roles ? serialize($request->roles) : NULL;
            $groups = $request->groups ? serialize($request->groups) : NULL;
            $isActive = $request->is_active;
        }

        $msgAlertType = '';
        $msg = '';

        try {
            $instanceEmpAccount = User::find($id);
            $instanceEmpAccount->emp_id = $empID;
            $instanceEmpAccount->firstname = $firstname;
            $instanceEmpAccount->middlename = $middlename;
            $instanceEmpAccount->lastname = $lastname;
            $instanceEmpAccount->address = $address;
            $instanceEmpAccount->province = $province;
            $instanceEmpAccount->region = $region;
            $instanceEmpAccount->mobile_no = $mobileNo;
            $instanceEmpAccount->email = $email;
            $instanceEmpAccount->username = $username;
            $instanceEmpAccount->emp_type = $empType;
            $instanceEmpAccount->position = $position;
            $instanceEmpAccount->division = $division;
            $instanceEmpAccount->gender = $gender;

            if ($type != 'profile') {
                $instanceEmpAccount->roles = $roles;
                $instanceEmpAccount->groups = $groups;
                $instanceEmpAccount->is_active = $isActive;
            }

            $pathAvatar = $this->uploadFile($request, 'avatar', $avatar, $instanceEmpAccount, $empID);
            $pathSignature = $this->uploadFile($request, 'esignature', $signature, $instanceEmpAccount, $empID);

            if (!empty($password)) {
                $instanceEmpAccount->password = bcrypt($password);
            }

            if (!empty($pathAvatar)) {
                $instanceEmpAccount->avatar = $pathAvatar;
            }

            if (!empty($pathSignature)) {
                $instanceEmpAccount->signature = $pathSignature;
            }

            $instanceEmpAccount->save();

            $msgAlertType = 'success';
            $msg = $type == 'profile' ?
                   'Profile successfully updated.' :
                    "User account of '$firstname' with an employee ID of
                    '$empID' successfully updated.";
        } catch (\Throwable $th) {
            $msgAlertType = 'failed';
            $msg = "Unknown error has occured. Please try again.";
        }

        return (object) [
            'alert_type' => $msgAlertType,
            'msg' => $msg
        ];
    }

    /** Library for Roles, Groups, and Accounts */

    /**
     *  Employee Diviision Module
    **/
    public function indexDivision(Request $request) {
        $empDivData = EmpDivision::orderBy('division_name')
                                 ->get();

        return view('modules.library.division.index', [
            'list' => $empDivData
        ]);
    }

    public function showCreateDivision() {
        return view('modules.library.division.create');
    }

    public function showEditDivision($id) {
        $divisionData = EmpDivision::find($id);
        $division = $divisionData->division_name;

        return view('modules.library.division.update', [
            'id' => $id,
            'division' => $division
        ]);
    }

    public function storeDivision(Request $request) {
        $divisionName = $request->division_name;

        try {
            if (!$this->checkDuplication('EmpDivision', $divisionName)) {
                $instanceEmpDiv = new EmpDivision;
                $instanceEmpDiv->division_name = $divisionName;
                $instanceEmpDiv->save();

                $msg = "Employee division '$divisionName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Employee division '$divisionName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateDivision(Request $request, $id) {
        $divisionName = $request->division_name;

        try {
            $instanceEmpDiv = EmpDivision::find($id);
            $instanceEmpDiv->division_name = $divisionName;
            $instanceEmpDiv->save();

            $msg = "Employee division '$divisionName' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteDivision($id) {
        try {
            $instanceEmpDiv = EmpDivision::find($id);
            $divisionName = $instanceEmpDiv->division_name;
            $instanceEmpDiv->delete();

            $msg = "Employee division '$divisionName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyDivision($id) {
        try {
            $instanceEmpDiv = EmpDivision::find($id);
            $divisionName = $instanceEmpDiv->division_name;
            $instanceEmpDiv->destroy();

            $msg = "Employee division '$divisionName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Employee Role Module
    **/
    public function indexRole(Request $request) {
        $userRoleData = EmpRole::orderBy('role')->get();

        return view('modules.library.role.index', [
            'list' => $userRoleData
        ]);
    }

    public function showCreateRole() {
        return view('modules.library.role.create', [
            'label' => $this->moduleLabels,
            'modules' => $this->modules
        ]);
    }

    public function showEditRole($id) {
        $userRoleData = EmpRole::find($id);
        $role = $userRoleData->role;
        $isOrdinary = $userRoleData->is_ordinary;
        $module = json_decode($userRoleData->module_access);

        return view('modules.library.role.update', [
            'id' => $id,
            'role' => $role,
            'moduleAccess' => $module,
            'label' => $this->moduleLabels,
            'modules' => $this->modules,
            'isOrdinary' => $isOrdinary
        ]);
    }

    public function storeRole(Request $request) {
        $roleName = $request->role;
        $isOrdinary = $request->is_ordinary;
        $moduleAccess = $request->module_access;
        $moduleAccess = str_replace("\n", '', $moduleAccess);
        $moduleAccess = trim($moduleAccess);
        $moduleAccess = preg_replace('/\s/', '', $moduleAccess );

        try {
            if (!$this->checkDuplication('EmpRole', $roleName)) {
                $instanceEmpRole = new EmpRole;
                $instanceEmpRole->role = $roleName;
                $instanceEmpRole->is_ordinary = $isOrdinary;
                $instanceEmpRole->module_access = $moduleAccess;
                $instanceEmpRole->save();

                $msg = "Role '$roleName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Role '$roleName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateRole(Request $request, $id) {
        $roleName = $request->role;
        $isOrdinary = $request->is_ordinary;
        $moduleAccess = $request->module_access;
        $moduleAccess = str_replace("\n", '', $moduleAccess);
        $moduleAccess = trim($moduleAccess);
        $moduleAccess = preg_replace('/\s/', '', $moduleAccess );

        try {
            $instanceEmpRole = EmpRole::find($id);
            $instanceEmpRole->role = $roleName;
            $instanceEmpRole->is_ordinary = $isOrdinary;
            $instanceEmpRole->module_access = $moduleAccess;
            $instanceEmpRole->save();

            $msg = "Role '$roleName' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteRole($id) {
        try {
            $instanceEmpRole = EmpRole::find($id);
            $roleName = $instanceEmpRole->role;
            $instanceEmpRole->delete();

            $msg = "Role '$roleName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyRole($id) {
        try {
            $instanceEmpRole = EmpRole::find($id);
            $roleName = $instanceEmpRole->role;
            $instanceEmpRole->destroy();

            $msg = "Role '$roleName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
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
        $divisionData = EmpDivision::orderBy('division_name')->get();

        return view('modules.library.group.create', [
            'employees' => $usersData,
            'divisions' => $divisionData
        ]);
    }

    public function showEditGroup($id) {
        $usersData = User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                  'position', 'id')
                         ->orderBy('firstname')
                         ->get();
        $divisionData = EmpDivision::orderBy('division_name')->get();
        $userGroupData = EmpGroup::find($id);
        $groupName = $userGroupData->group_name;
        $division = unserialize($userGroupData->division_access);
        $groupHead = $userGroupData->group_head;

        return view('modules.library.group.update', [
            'id' => $id,
            'groupName' => $groupName,
            'groupHead' => $groupHead,
            'employees' => $usersData,
            'divisions' => $divisionData,
            'divisionAccess' => $division,
        ]);
    }

    public function storeGroup(Request $request) {
        $groupName = $request->group_name;
        $divisions = $request->divisions ? serialize($request->divisions) : NULL;
        $groupHead = $request->group_head;

        try {
            if (!$this->checkDuplication('EmpGroup', $groupName)) {
                $instanceEmpGroup = new EmpGroup;
                $instanceEmpGroup->group_name = $groupName;
                $instanceEmpGroup->division_access = $divisions;
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
        $divisions = $request->divisions ? serialize($request->divisions) : NULL;
        $groupHead = $request->group_head;

        try {
            $instanceEmpGroup = EmpGroup::find($id);
            $instanceEmpGroup->group_name = $groupName;
            $instanceEmpGroup->division_access = $divisions;
            $instanceEmpGroup->group_head = $groupHead;
            $instanceEmpGroup->save();

            $msg = "Employee group '$groupName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
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
        $userData = User::addSelect([
            'division' => EmpDivision::select('division_name')
                          ->whereColumn('id', 'emp_accounts.division')
                          ->limit(1)
        ])->orderBy('firstname')->get();

        return view('modules.library.account.index', [
            'list' => $userData
        ]);
    }

    public function showCreateAccount() {
        $data = $this->showCreateProfile('account');

        $viewDir = $data->view_dir;
        $flashData = $data->flash_data;

        return view($viewDir, $flashData);
    }

    public function showEditAccount($id) {
        $data = $this->showEditProfile('account', $id);

        $viewDir = $data->view_dir;
        $flashData = $data->flash_data;

        return view($viewDir, $flashData);
    }

    public function storeAccount(Request $request) {
        $data = $this->storeUser($request, 'account');

        return redirect(url()->previous())->with($data->alert_type, $data->msg);
    }

    public function updateAccount(Request $request, $id) {
        $data = $this->updateUser($request, 'account', $id);

        return redirect(url()->previous())->with($data->alert_type, $data->msg);
    }

    public function deleteAccount($id) {
        try {
            $instanceUser = User::find($id);
            $firstname = $instanceUser->firstname;
            $instanceUser->delete();

            $msg = "User '$firstname' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyAccount($id) {
        try {
            User::destroy($id);

            $msg = "User '$id' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Employee Log Module
    **/
    public function indexLogs(Request $request) {
        $userLogData = EmpLog::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'emp_logs.emp_id')
                          ->limit(1)
        ])->orderBy('logged_at', 'desc')->get();

        return view('modules.library.user-log.index', [
            'list' => $userLogData
        ]);
    }

    public function destroyLogs($id) {
        try {
            EmpLog::destroy($id);

            $msg = "Employee log '$id' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    private function uploadFile($request, $type, $file, $db, $fileID = "") {
        $path = "";

        if (!empty($file)) {
            Image::configure(['driver' => 'gd']);

            switch ($type) {
                case 'avatar':
                    /*
                    $this->validate($request, [
                        'avatar' => 'mimes:jpg,jpeg'
                    ]);*/

                    $newFileName = 'avatar-' . strtolower($fileID) . '.jpg';
                    $exists = Storage::exists($db->avatar);

                    $path = 'storage/images/employees/avatars/' . $newFileName;
                    $image = Image::make($file)->resize(300, 300);
                    Storage::put('public/images/employees/avatars/'.$newFileName, (string) $image->encode());

                    if (!empty($db->avatar)) {
                        if ($exists && ($db->avatar != $path)) {
                            Storage::delete($db->avatar);
                        }
                    }

                    break;

                case 'esignature':
                    $this->validate($request, [
                        'signature' => 'mimes:png'
                    ]);

                    $newFileName = 'sig-' . strtolower($fileID) . '.png';
                    $exists = Storage::exists($db->signature);

                    $path = 'storage/images/employees/signatures/' . $newFileName;
                    $image = Image::make($file)->resize(null, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    Storage::put('public/images/employees/signatures/'.$newFileName, (string) $image->encode());

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
