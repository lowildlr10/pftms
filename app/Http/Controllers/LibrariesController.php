<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseRequest;
use App\Abstracts;
use App\AbstractItem;

use App\User;
use App\Division;
use App\UnitIssue;
use App\Projects;
use App\Status;
use App\ItemClassification;
use App\Supplier;
use App\SupplierClassification;
use App\ModeProcurement;
use App\Signatory;
use DB;
use App\UserGroups;

class LibrariesController extends Controller
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

    /* Divisions */
    public function indexDivisions(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $divisionList = new Division;

        if (!empty($search)) {
            $divisionList = $divisionList::where('division', 'LIKE', '%' . $search . '%')
                                         ->paginate($pageLimit);
        } else {
            $divisionList = $divisionList::paginate($pageLimit);
        }

        return view('pages.divisions', ['search' => $search,
                                        'pageLimit' => $pageLimit,
                                        'list' => $divisionList]);
    }



    /* Item Classification */
    public function indexItemClass(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $itemClassList = new ItemClassification;

        if (!empty($search)) {
            $itemClassList = $itemClassList::where('classification', 'LIKE', '%' . $search . '%')
                                     ->paginate($pageLimit);
        } else {
            $itemClassList = $itemClassList::paginate($pageLimit);
        }

        return view('pages.item-classification', ['search' => $search,
                                                  'pageLimit' => $pageLimit,
                                                  'list' => $itemClassList]);
    }



    /* Modes of Procurement */
    public function indexModesProcurement(Request $request) {
        $pageLimit = 25;
        $search = trim($request['search']);
        $modeProcurementList = new ModeProcurement;

        if (!empty($search)) {
            $modeProcurementList = $modeProcurementList::where('mode', 'LIKE', '%' . $search . '%')
                                     ->paginate($pageLimit);
        } else {
            $modeProcurementList = $modeProcurementList::paginate($pageLimit);
        }

        return view('pages.mode-procurement', ['search' => $search,
                                     'pageLimit' => $pageLimit,
                                     'list' => $modeProcurementList]);
    }



    /* Procurement Status */
    public function indexStatus(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $statusList = new Status;

        if (!empty($search)) {
            $statusList = $statusList::where('status', 'LIKE', '%' . $search . '%')
                                     ->paginate($pageLimit);
        } else {
            $statusList = $statusList::paginate($pageLimit);
        }

        return view('pages.status', ['search' => $search,
                                     'pageLimit' => $pageLimit,
                                     'list' => $statusList]);
    }



    /* Projects */
    public function indexProjects(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $projectList = new Projects;

        if (!empty($search)) {
            $projectList = $projectList::where('project', 'LIKE', '%' . $search . '%')
                                       ->paginate($pageLimit);
        } else {
            $projectList = $projectList::paginate($pageLimit);
        }

        return view('pages.projects', ['search' => $search,
                                       'pageLimit' => $pageLimit,
                                       'list' => $projectList]);
    }



    /* Signatories */
    public function indexSignatories(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $signatoryList = DB::table('tblsignatories AS sig')
                           ->select(DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
                                    'sig.id', 'sig.position')
                           ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id');

        if (!empty($search)) {
            $signatoryList = $signatoryList->where(function ($query)  use ($search) {
                                 $query->where('sig.emp_id', 'LIKE', '%' . $search . '%')
                                       ->orWhere('sig.position', 'LIKE', '%' . $search . '%')
                                       ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                       ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                       ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%');
                             });
        }

        $signatoryList = $signatoryList->orderBy('emp.firstname')
                                       ->paginate($pageLimit);

        return view('pages.signatories', ['search' => $search,
                                          'pageLimit' => $pageLimit,
                                          'list' => $signatoryList]);
    }




    /* Supplier Classifications */
    public function indexSupplierClass(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $supplierClassList = new SupplierClassification;

        if (!empty($search)) {
            $supplierClassList = $supplierClassList::where('classification', 'LIKE', '%' . $search . '%')
                                     ->paginate($pageLimit);
        } else {
            $supplierClassList = $supplierClassList::paginate($pageLimit);
        }

        return view('pages.supplier-classification', ['search' => $search,
                                                      'pageLimit' => $pageLimit,
                                                      'list' => $supplierClassList]);
    }



    /* Suppliers */
    public function indexSuppliers(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $filter = $request['filter'];
        $supplierList = DB::table('tblsuppliers as bid')
                          ->select('bid.*', 'class.classification')
                          ->join('tblsupplier_classifications as class', 'class.id', '=', 'bid.class_id');
        $classifications = SupplierClassification::orderBy('classification')->get();

        if (!empty($search)) {
            $supplierList = $supplierList->where(function ($query)  use ($search) {
                                   $query->where('bid.company_name', 'LIKE', '%' . $search . '%')
                                         ->orWhere('bid.address', 'LIKE', '%' . $search . '%')
                                         ->orWhere('bid.contact_person', 'LIKE', '%' . $search . '%')
                                         ->orWhere('bid.mobile_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('class.classification', 'LIKE', '%' . $search . '%');
                               });
        }

        if (!empty($filter) && $filter != 0) {
            $supplierList = $supplierList->where('bid.class_id', '=', $filter);
        }

        $supplierList = $supplierList->orderBy('bid.company_name')
                                     ->paginate($pageLimit);

        return view('pages.suppliers', ['search' => $search,
                                        'pageLimit' => $pageLimit,
                                        'list' => $supplierList,
                                        'classifications' => $classifications,
                                        'filter' => $filter]);
    }



    /* Units of Issue */
    public function indexUnitIssues(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $unitList = new UnitIssue;

        if (!empty($search)) {
            $unitList = $unitList::where('unit', 'LIKE', '%' . $search . '%')
                                  ->paginate($pageLimit);
        } else {
            $unitList = $unitList::paginate($pageLimit);
        }

        return view('pages.unit-issue', ['search' => $search,
                                         'pageLimit' => $pageLimit,
                                         'list' => $unitList]);
    }



    /* User Accounts */
    public function indexEmployees(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $users = DB::table('tblemp_accounts as emp')
                   ->join('tbldivision as division', 'division.id', '=', 'emp.division_id')
                   ->join('tblprovince as province', 'province.id', '=', 'emp.province_id')
                   ->join('tblregion as region', 'region.id', '=', 'emp.region_id')
                   ->whereNull('deleted_at');

        if (!empty($search)) {
            $users = $users->where(function ($query)  use ($search) {
                                   $query->where('emp.emp_id', 'LIKE', '%' . $search . '%')
                                         ->orWhere('division.division', 'LIKE', '%' . $search . '%')
                                         ->orWhere('province.province', 'LIKE', '%' . $search . '%')
                                         ->orWhere('region.region', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.emp_type', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.position', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%');
                               });
        }

        $users = $users->orderBy('emp.firstname')
                       ->paginate($pageLimit);

        return view('pages.accounts', ['search' => $search,
                                       'pageLimit' => $pageLimit,
                                       'users' => $users]);
    }

    /* User Groups */
    public function indexUserGroups(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $userGroupList = DB::table('tblemp_groups as group')
                           ->select('group.id as group_id', 'group.group_name',
                                    DB::raw("CONCAT(emp.firstname, ' ', emp.lastname,
                                            '[ ', emp.position, ' ]') as group_head"))
                           ->join('tblemp_accounts as emp', 'emp.id', '=', 'group.group_head');

        if (!empty($search)) {
            $userGroupList = $userGroupList->where(function ($query)  use ($search) {
                                    $query->where('emp.emp_id', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.position', 'LIKE', '%' . $search . '%')
                                          ->orWhere('group.group_name', 'LIKE', '%' . $search . '%');
                                });
        }

        $userGroupList = $userGroupList->orderBy('group.group_name')
                       ->paginate($pageLimit);

        return view('pages.user-group', ['search' => $search,
                                          'pageLimit' => $pageLimit,
                                          'list' => $userGroupList]);
    }



    /* Create */
    public function create(Request $request, $type)
    {
        switch ($type) {
            case 'division':
                return view('pages.create-edit-division',
                            ['division' => '',
                             'key' => '']);
                break;

            case 'item_classification':
                return view('pages.create-edit-item-class',
                            ['classification' => '',
                             'key' => '']);
                break;

            case 'mode_procurement':
                return view('pages.create-edit-mode-procurement',
                            ['classification' => '',
                             'key' => '']);
                break;

            case 'project':
                return view('pages.create-edit-project',
                            ['referenceCode' => '',
                             'project' => '',
                             'key' => '']);
                break;

            case 'signatory':
                $employees = User::orderBy('firstname')->get();

                return view('pages.create-edit-signatory',
                            ['employees' => $employees,
                             'empID' => '',
                             'position' => '',
                             'pReq' => '',
                             'rfq' => '',
                             'abs' => '',
                             'poJo' => '',
                             'ors' => '',
                             'iar' => '',
                             'dv' => '',
                             'ris' => '',
                             'par' => '',
                             'ics' => '',
                             'liquidation' => '',
                             'prSignType' => '',
                             'abstractSignType' => '',
                             'poJoSignType' => '',
                             'orsBursSignType' => '',
                             'iarSignType' => '',
                             'dvSignType' => '',
                             'risSignType' => '',
                             'parSignType' => '',
                             'icsSignType' => '',
                             'liquidationSignType' => '',
                             'active' => '',
                             'lddap' => '',
                             'lddapSignType' => '',
                             'key' => '']);
                break;

            case 'supplier_classification':
                return view('pages.create-edit-supplier-classification',
                            ['classification' => '',
                             'key' => '']);
                break;

            case 'supplier':
                $supplierClass = SupplierClassification::orderBy('classification')->get();
                $attachment = array();

                return view('pages.create-edit-supplier', ['supplierClass' => $supplierClass,
                                                           'classID' => '',
                                                           'companyName' => '',
                                                           'dateFile' => NULL,
                                                           'address' => '',
                                                           'email' => '',
                                                           'urlAddress' => '',
                                                           'telephoneNo' => '',
                                                           'faxNo' => '',
                                                           'mobileNo' => '',
                                                           'dateEstablished' => NULL,
                                                           'vatNo' => '',
                                                           'contactPerson' => '',
                                                           'natureBusiness' => '',
                                                           'natureBusinessOthers' => '',
                                                           'deliveryVehicleNo' => 0,
                                                           'productLines' => '',
                                                           'creditAccomodation' => '',
                                                           'attachment' => $attachment,
                                                           'attachmentValue' => '',
                                                           'attachmentOthers' => '',
                                                           'nameBank' => '',
                                                           'accountName' => '',
                                                           'accountNo' => '',
                                                           'active' => 'y',
                                                           'key' => '']);
                break;

            case 'unit_issue':
                return view('pages.create-edit-unit-issue',
                            ['unit' => '',
                             'key' => '']);
                break;

            case 'user_group':
                $users = User::select(DB::raw('CONCAT(firstname, " ", lastname,
                                      " [ ", position, " ]") AS name'),
                                      'id')
                              ->orderBy('firstname')
                              ->get();

                return view('pages.create-edit-user-group',
                            ['groupName' => '',
                             'groupHead' => '',
                             'users' => $users,
                             'key' => '']);
                break;

            default:
                # code...
                break;
        }
    }

    /* Store */
    public function store(Request $request, $type)
    {
        switch ($type) {
            case 'division':
                $dataCheck = Division::where('division', $request['division'])
                                     ->count();

                if ($dataCheck == 0) {
                    $division = new Division;
                    $division->division = $request['division'];
                    $division->save();

                    return redirect(url()->previous())->with('success', "Created '" . $request['division'] .
                                                                    "' new division.");
                } else {
                    return redirect(url()->previous())->with('warning', "Division name has duplicate.");
                }

                break;

            case 'item_classification':
                $dataCheck = ItemClassification::where('classification', $request['classification'])
                                               ->count();

                if ($dataCheck == 0) {
                    $classification = new ItemClassification;
                    $classification->classification = $request['classification'];
                    $classification->save();

                    return redirect(url()->previous())->with('success', "Created '" . $request['classification'] .
                                                                    "' new item classification.");
                } else {
                    return redirect(url()->previous())->with('warning', "Item classification name has duplicate.");
                }

                break;

            case 'mode_procurement':
                $dataCheck = ModeProcurement::where('mode', $request['classification'])
                                            ->count();

                if ($dataCheck == 0) {
                    $classification = new ModeProcurement;
                    $classification->mode = $request['classification'];
                    $classification->save();

                    return redirect(url()->previous())->with('success', "Created '" . $request['classification'] .
                                                                    "' new mode of procurement.");
                    break;
                } else {
                    return redirect(url()->previous())->with('warning', "Mode of procurement has duplicate.");
                }


            case 'project':
                $dataCheck = Projects::where('reference_code', $request['reference_code'])
                                     ->orWhere('project', 'LIKE', '%' . $request['project'] . '%')
                                     ->count();

                if ($dataCheck == 0) {
                    $project = new Projects;
                    $project->reference_code = $request['reference_code'];
                    $project->project = $request['project'];
                    $project->save();

                    return redirect(url()->previous())->with('success', "Created '" . $request['project'] .
                                                                    "' new project/charging.");
                } else {
                    return redirect(url()->previous())->with('warning', "Reference code or project/charging name has duplicate.");
                }

                break;

            case 'signatory':
                $signatory = new Signatory;
                $signatory->emp_id = $request['employee'];
                $signatory->position = $request['position'];
                $_modCheckBox = array($request['p_req'], $request['rfq'], $request['abs'],
                                      $request['po_jo'], $request['ors'], $request['iar'],
                                      $request['dv'], $request['ris'], $request['par'],
                                      $request['ics'], $request['liquidation'],
                                      $request['lddap']);
                $_modDropDown = array($request['pr_sign_type'],
                                      $request['abstract_sign_type'],
                                      $request['po_jo_sign_type'],
                                      $request['ors_burs_sign_type'],
                                      $request['iar_sign_type'],
                                      $request['dv_sign_type'],
                                      $request['ris_sign_type'],
                                      $request['par_sign_type'],
                                      $request['ics_sign_type'],
                                      $request['liquidation_sign_type'],
                                      $request['lddap_sign_type']);
                $modCheckBox = array();
                $modDropDown = array();

                foreach ($_modCheckBox as $checkBox) {
                    if (empty($checkBox)) {
                        $checkBox = 'n';
                    }

                    $modCheckBox[] = $checkBox;
                }

                foreach ($_modDropDown as $dropDown) {
                    if (empty($dropDown)) {
                        $dropDown = '';
                    }

                    $modDropDown[] = $dropDown;
                }

                $signatory->p_req = $modCheckBox[0];
                $signatory->rfq = $modCheckBox[1];
                $signatory->abs = $modCheckBox[2];
                $signatory->po_jo = $modCheckBox[3];
                $signatory->ors = $modCheckBox[4];
                $signatory->iar = $modCheckBox[5];
                $signatory->dv = $modCheckBox[6];
                $signatory->ris = $modCheckBox[7];
                $signatory->par = $modCheckBox[8];
                $signatory->ics = $modCheckBox[9];
                $signatory->liquidation = $modCheckBox[10];
                $signatory->lddap = $modCheckBox[11];
                $signatory->pr_sign_type = $modDropDown[0];
                $signatory->abstract_sign_type = $modDropDown[1];
                $signatory->po_jo_sign_type = $modDropDown[2];
                $signatory->ors_burs_sign_type = $modDropDown[3];
                $signatory->iar_sign_type = $modDropDown[4];
                $signatory->dv_sign_type = $modDropDown[5];
                $signatory->ris_sign_type = $modDropDown[6];
                $signatory->par_sign_type = $modDropDown[7];
                $signatory->ics_sign_type = $modDropDown[8];
                $signatory->liquidation_sign_type = $modDropDown[9];
                $signatory->lddap_sign_type = $modDropDown[10];
                $signatory->active = $request['active'];

                $signatory->save();

                return redirect(url()->previous())->with('success', "Created new signatory.");

                break;

            case 'supplier_classification':
                $dataCheck = SupplierClassification::where('classification', $request['classification'])
                                                   ->count();

                if ($dataCheck == 0) {
                    $classification = new SupplierClassification;
                    $classification->classification = $request['classification'];
                    $classification->save();

                    return redirect(url()->previous())->with('success', "Created '" . $request['classification'] .
                                                                    "' new supplier classification.");
                } else {
                    return redirect(url()->previous())->with('warning', "Supplier classification name has duplicate.");
                }

                break;

            case 'supplier':
                $supplier = new Supplier;
                $isUnique = true;

                $classID = $request['class_id'];
                $nameBank = $request['name_bank'];
                $accountName = $request['account_name'];
                $accountNo = $request['account_no'];
                $companyName = $request['company_name'];
                $dateFile = $request['date_file'];
                $address = $request['address'];
                $email = $request['email'];
                $urlAddress = $request['url_address'];
                $telephoneNo = $request['telephone_no'];
                $faxNo = $request['fax_no'];
                $mobileNo = $request['mobile_no'];
                $dateEstablished = $request['date_established'];
                $vatNo = $request['vat_no'];
                $contactPerson = $request['contact_person'];
                $natureBusiness = $request['nature_business'];
                $natureBusinessOthers = $request['nature_business_others'];
                $deliveryVehicleNo = $request['delivery_vehicle_no'];
                $productLines = $request['product_lines'];
                $creditAccomodation = $request['credit_accomodation'];
                $attachment = $request['attachment'];
                $attachmentOthers = $request['attachment_others'];
                $active = $request['active'];

                $_supplier = Supplier::all();

                foreach ($_supplier as $checkVal) {
                    if ($companyName == $checkVal->company_name) {
                        $isUnique = false;
                        break;
                    }
                }

                if (empty($dateEstablished)) {
                    $dateEstablished = NULL;
                }

                if ($isUnique) {
                    $supplier->class_id = $classID;
                    $supplier->name_bank = $nameBank;
                    $supplier->account_name = $accountName;
                    $supplier->account_no = $accountNo;
                    $supplier->company_name = $companyName;
                    $supplier->date_file = $dateFile;
                    $supplier->address = $address;
                    $supplier->email = $email;
                    $supplier->url_address = $urlAddress;
                    $supplier->telephone_no = $telephoneNo;
                    $supplier->fax_no = $faxNo;
                    $supplier->mobile_no = $mobileNo;
                    $supplier->date_established = $dateEstablished;
                    $supplier->vat_no = $vatNo;
                    $supplier->contact_person = $contactPerson;
                    $supplier->nature_business = $natureBusiness;
                    $supplier->nature_business_others = $natureBusinessOthers;
                    $supplier->delivery_vehicle_no = $deliveryVehicleNo;
                    $supplier->product_lines = $productLines;
                    $supplier->credit_accomodation = $creditAccomodation;
                    $supplier->attachment = $attachment;
                    $supplier->attachment_others = $attachmentOthers;
                    $supplier->active = $active;
                    $supplier->save();

                    return redirect(url()->previous())->with('success', "Created new supplier " .
                                                                    strtoupper($companyName) . ".");
                } else {
                    return redirect(url()->previous())->with('warning', "Supplier has a duplicate.");
                }

                break;

            case 'unit_issue':
                $dataCheck = UnitIssue::where('unit', $request['unit'])
                                      ->count();

                if ($dataCheck == 0) {
                    $unit = new UnitIssue;
                    $unit->unit = $request['unit'];
                    $unit->save();

                    return redirect(url()->previous())->with('success', "Created '" . $request['unit'] .
                                                                    "' new unit of issue.");
                } else {
                    return redirect(url()->previous())->with('warning', "Unit issue name has duplicate.");
                }

                break;

            case 'user_group':
                $dataCheck = UserGroups::where('group_name', $request['group_name'])
                                       ->count();

                if ($dataCheck == 0) {
                    $userGroup = new UserGroups;
                    $userGroup->group_name = $request['group_name'];
                    $userGroup->group_head = $request['group_head'];
                    $userGroup->save();

                    return redirect(url()->previous())->with('success', "Created a new group '" .
                                                                        $request['group_name'] .
                                                                        "'.");
                } else {
                    return redirect(url()->previous())->with('warning', "Group name has duplicate.");
                }

                break;

            default:
                # code...
                break;
        }
    }

    /* Edit */
    public function edit(Request $request, $type)
    {
        $key = $request['key'];

        switch ($type) {
            case 'division':
                $division = Division::find($key);
                return view('pages.create-edit-division',
                            ['division' => $division->division,
                             'key' => $key]);
                break;

            case 'item_classification':
                $classification = ItemClassification::find($key);
                return view('pages.create-edit-item-class',
                            ['classification' => $classification->classification,
                             'key' => $key]);
                break;

            case 'mode_procurement':
                $classification = ModeProcurement::find($key);
                return view('pages.create-edit-mode-procurement',
                            ['classification' => $classification->mode,
                             'key' => $key]);
                break;

            case 'project':
                $project = Projects::find($key);
                return view('pages.create-edit-project',
                            ['referenceCode' => $project->reference_code,
                             'project' => $project->project,
                             'key' => $key]);
                break;

            case 'signatory':
                $signatory = Signatory::find($key);
                $employees = User::orderBy('firstname')->get();

                return view('pages.create-edit-signatory',
                            ['employees' => $employees,
                             'empID' => $signatory->emp_id,
                             'position' => $signatory->position,
                             'pReq' => $signatory->p_req,
                             'rfq' => $signatory->rfq,
                             'abs' => $signatory->abs,
                             'poJo' => $signatory->po_jo,
                             'ors' => $signatory->ors,
                             'iar' => $signatory->iar,
                             'dv' => $signatory->dv,
                             'ris' => $signatory->ris,
                             'par' => $signatory->par,
                             'ics' => $signatory->ics,
                             'liquidation' => $signatory->liquidation,
                             'prSignType' => $signatory->pr_sign_type,
                             'abstractSignType' => $signatory->abstract_sign_type,
                             'poJoSignType' => $signatory->po_jo_sign_type,
                             'orsBursSignType' => $signatory->ors_burs_sign_type,
                             'iarSignType' => $signatory->iar_sign_type,
                             'dvSignType' => $signatory->dv_sign_type,
                             'risSignType' => $signatory->ris_sign_type,
                             'parSignType' => $signatory->par_sign_type,
                             'icsSignType' => $signatory->ics_sign_type,
                             'liquidationSignType' => $signatory->liquidation_sign_type,
                             'active' => $signatory->active,
                             'lddap' => $signatory->lddap,
                             'lddapSignType' => $signatory->lddap_sign_type,
                             'key' => $key]);

                break;

            case 'supplier_classification':
                $classification = SupplierClassification::find($key);
                return view('pages.create-edit-supplier-classification',
                            ['classification' => $classification->classification,
                             'key' => $key]);
                break;

            case 'supplier':
                $supplier = Supplier::find($key);
                $supplierClass = SupplierClassification::orderBy('classification')->get();
                $attachment = explode('-', $supplier->attachment);

                return view('pages.create-edit-supplier', ['supplierClass' => $supplierClass,
                                                           'classID' => $supplier->class_id,
                                                           'companyName' => $supplier->company_name,
                                                           'dateFile' => $supplier->date_file,
                                                           'address' => $supplier->address,
                                                           'email' => $supplier->email,
                                                           'urlAddress' => $supplier->url_address,
                                                           'telephoneNo' => $supplier->telephone_no,
                                                           'faxNo' => $supplier->fax_no,
                                                           'mobileNo' => $supplier->mobile_no,
                                                           'dateEstablished' => $supplier->date_established,
                                                           'vatNo' => $supplier->vat_no,
                                                           'contactPerson' => $supplier->contact_person,
                                                           'natureBusiness' => $supplier->nature_business,
                                                           'natureBusinessOthers' => $supplier->nature_business_others,
                                                           'deliveryVehicleNo' => $supplier->delivery_vehicle_no,
                                                           'productLines' => $supplier->product_lines,
                                                           'creditAccomodation' => $supplier->credit_accomodation,
                                                           'attachment' => $attachment,
                                                           'attachmentOthers' => $supplier->attachment_others,
                                                           'attachmentValue' => $supplier->attachment,
                                                           'nameBank' => $supplier->name_bank,
                                                           'accountName' => $supplier->account_name,
                                                           'accountNo' => $supplier->account_no,
                                                           'active' => $supplier->active,
                                                           'key' => $key]);
                break;

            case 'unit_issue':
                $unit = UnitIssue::find($key);
                return view('pages.create-edit-unit-issue',
                            ['unit' => $unit->unit,
                             'key' => $key]);
                break;

            case 'user_group':
                $userGroup = UserGroups::find($key);
                $users = User::select(DB::Raw("CONCAT(firstname, ' ', lastname) as name"),
                                      'id')
                              ->orderBy('firstname')
                              ->get();

                return view('pages.create-edit-user-group',
                            ['groupName' => $userGroup->group_name,
                             'groupHead' => $userGroup->group_head,
                             'users' => $users,
                             'key' => $key]);
                break;

            default:
                # code...
                break;
        }
    }

    /* Update */
    public function update(Request $request, $type)
    {
        $key = $request['key'];

        switch ($type) {
            case 'division':
                $division = Division::find($key);
                $oldDivision = $division->division;
                $division->division = $request['division'];
                $division->save();

                return redirect(url()->previous())
                       ->with('success', "Updated division name from '" . $oldDivision . "'' to '" .
                                     $request['division'] . "'.");
                break;

            case 'item_classification':
                $classification = ItemClassification::find($key);
                $oldClassification = $classification->classification;
                $classification->classification = $request['classification'];
                $classification->save();

                return redirect(url()->previous())
                       ->with('success', "Updated item classification name from '" .
                                     $oldClassification . "'' to '" .
                                     $request['classification'] . "'.");
                break;

            case 'mode_procurement':
                $classification = ModeProcurement::find($key);
                $oldClassification = $classification->mode;
                $classification->mode = $request['classification'];
                $classification->save();

                return redirect(url()->previous())
                       ->with('success', "Updated mode of procurement name from '" .
                                     $oldClassification . "'' to '" .
                                     $request['classification'] . "'.");
                break;

            case 'project':
                $project = Projects::find($key);
                $project->reference_code = $request['reference_code'];
                $oldProject = $project->project;
                $project->project = $request['project'];
                $project->save();

                return redirect(url()->previous())
                       ->with('success', "Updated mode of procurement name from '" .
                                     $oldProject . "'' to '" .
                                     $request['project'] . "'.");
                break;

            case 'signatory':
                $signatory = Signatory::find($key);
                $signatory->emp_id = $request['employee'];
                $signatory->position = $request['position'];
                $_modCheckBox = array($request['p_req'], $request['rfq'], $request['abs'],
                                      $request['po_jo'], $request['ors'], $request['iar'],
                                      $request['dv'], $request['ris'], $request['par'],
                                      $request['ics'], $request['liquidation'],
                                      $request['lddap']);
                $_modDropDown = array($request['pr_sign_type'],
                                      $request['abstract_sign_type'],
                                      $request['po_jo_sign_type'],
                                      $request['ors_burs_sign_type'],
                                      $request['iar_sign_type'],
                                      $request['dv_sign_type'],
                                      $request['ris_sign_type'],
                                      $request['par_sign_type'],
                                      $request['ics_sign_type'],
                                      $request['liquidation_sign_type'],
                                      $request['lddap_sign_type']);
                $modCheckBox = array();
                $modDropDown = array();

                foreach ($_modCheckBox as $checkBox) {
                    if (empty($checkBox)) {
                        $checkBox = 'n';
                    }

                    $modCheckBox[] = $checkBox;
                }

                foreach ($_modDropDown as $dropDown) {
                    if (empty($dropDown)) {
                        $dropDown = '';
                    }

                    $modDropDown[] = $dropDown;
                }

                $signatory->p_req = $modCheckBox[0];
                $signatory->rfq = $modCheckBox[1];
                $signatory->abs = $modCheckBox[2];
                $signatory->po_jo = $modCheckBox[3];
                $signatory->ors = $modCheckBox[4];
                $signatory->iar = $modCheckBox[5];
                $signatory->dv = $modCheckBox[6];
                $signatory->ris = $modCheckBox[7];
                $signatory->par = $modCheckBox[8];
                $signatory->ics = $modCheckBox[9];
                $signatory->liquidation = $modCheckBox[10];
                $signatory->lddap = $modCheckBox[11];
                $signatory->pr_sign_type = $modDropDown[0];
                $signatory->abstract_sign_type = $modDropDown[1];
                $signatory->po_jo_sign_type = $modDropDown[2];
                $signatory->ors_burs_sign_type = $modDropDown[3];
                $signatory->iar_sign_type = $modDropDown[4];
                $signatory->dv_sign_type = $modDropDown[5];
                $signatory->ris_sign_type = $modDropDown[6];
                $signatory->par_sign_type = $modDropDown[7];
                $signatory->ics_sign_type = $modDropDown[8];
                $signatory->liquidation_sign_type = $modDropDown[9];
                $signatory->lddap_sign_type = $modDropDown[10];
                $signatory->active = $request['active'];
                $signatory->save();

                return redirect(url()->previous())->with('success', "Signatory Updated.");

                break;

            case 'supplier_classification':
                $classification = SupplierClassification::find($key);
                $oldClassification = $classification->classification;
                $classification->classification = $request['classification'];
                $classification->save();

                return redirect(url()->previous())
                       ->with('success', "Updated supplier classification name from '" .
                                     $oldClassification . "'' to '" .
                                     $request['classification'] . "'.");
                break;

            case 'supplier':
                $supplier = Supplier::find($key);

                $classID = $request['class_id'];
                $companyName = $request['company_name'];
                $dateFile = $request['date_file'];
                $address = $request['address'];
                $email = $request['email'];
                $urlAddress = $request['url_address'];
                $telephoneNo = $request['telephone_no'];
                $faxNo = $request['fax_no'];
                $mobileNo = $request['mobile_no'];
                $dateEstablished = $request['date_established'];
                $vatNo = $request['vat_no'];
                $contactPerson = $request['contact_person'];
                $natureBusiness = $request['nature_business'];
                $natureBusinessOthers = $request['nature_business_others'];
                $deliveryVehicleNo = $request['delivery_vehicle_no'];
                $productLines = $request['product_lines'];
                $creditAccomodation = $request['credit_accomodation'];
                $attachment = $request['attachment'];
                $attachmentOthers = $request['attachment_others'];
                $nameBank = $request['name_bank'];
                $accountName = $request['account_name'];
                $accountNo = $request['account_no'];
                $active = $request['active'];

                if (empty($dateEstablished)) {
                    $dateEstablished = NULL;
                }

                $supplier->class_id = $classID;
                $supplier->company_name = $companyName;
                $supplier->date_file = $dateFile;
                $supplier->address = $address;
                $supplier->email = $email;
                $supplier->url_address = $urlAddress;
                $supplier->telephone_no = $telephoneNo;
                $supplier->fax_no = $faxNo;
                $supplier->mobile_no = $mobileNo;
                $supplier->date_established = $dateEstablished;
                $supplier->vat_no = $vatNo;
                $supplier->contact_person = $contactPerson;
                $supplier->nature_business = $natureBusiness;
                $supplier->nature_business_others = $natureBusinessOthers;
                $supplier->delivery_vehicle_no = $deliveryVehicleNo;
                $supplier->product_lines = $productLines;
                $supplier->credit_accomodation = $creditAccomodation;
                $supplier->attachment = $attachment;
                $supplier->attachment_others = $attachmentOthers;
                $supplier->name_bank = $nameBank;
                $supplier->account_name = $accountName;
                $supplier->account_no = $accountNo;
                $supplier->active = $active;
                $supplier->save();

                return redirect(url()->previous())
                                     ->with('success', "Updated the supplier information of " .
                                                   $companyName . ".");
                break;

            case 'unit_issue':
                $unit = UnitIssue::find($key);
                $oldCUnit = $unit->unit;
                $unit->unit = $request['unit'];
                $unit->save();

                return redirect(url()->previous())
                       ->with('success', "Updated unit of issue name from '" .
                                     $oldCUnit . "'' to '" .
                                     $request['unit'] . "'.");
                break;

            case 'user_group':
                $userGroup = UserGroups::find($key);
                $oldCUserGroup = $userGroup->group_name;
                $userGroup->group_name = $request['group_name'];
                $userGroup->group_head = $request['group_head'];
                $userGroup->save();

                return redirect(url()->previous())
                       ->with('success', "Updated unit of issue name from '" .
                                     $oldCUserGroup . "'' to '" .
                                     $request['group_name'] . "'.");
                break;

            default:
                # code...
                break;
        }
    }

    /* Delete */
    public function delete(Request $request, $type)
    {
        $key = $request['key'];

        switch ($type) {
            case 'division':
                $division = Division::find($key);
                $divName = $division->division;
                $empDataCount = User::where('division_id', $division->id)->count();

                if ($empDataCount > 0) {
                    return redirect(url()->previous())
                           ->with('warning', "Cannot delete this division.");
                } else {
                    $division->delete();

                    return redirect(url()->previous())
                           ->with('success', "Successfully deleted '" . $divName . "'.");
                }

                break;

            case 'item_classification':
                $classification = ItemClassification::find($key);
                $className = $classification->classification;
                $classification->delete();

                return redirect(url()->previous())
                       ->with('success', "Successfully deleted '" . $className . "'.");
                break;

            case 'mode_procurement':
                $classification = ModeProcurement::find($key);
                $className = $classification->mode;
                $abstractDataCount = Abstracts::where('mode_procurement_id', $classification->id)
                                              ->count();

                if ($abstractDataCount > 0) {
                    return redirect(url()->previous())
                           ->with('warning', "Cannot delete this mode of procurement.");
                } else {
                    $classification->delete();

                    return redirect(url()->previous())
                           ->with('success', "Successfully deleted '" . $className . "'.");
                }

                break;

            case 'project':
                $project = Projects::find($key);
                $projectName = $project->project;
                $prDataCount = PurchaseRequest::where('project_id', $project->id)
                                              ->count();

                if ($prDataCount > 0) {
                    return redirect(url()->previous())
                           ->with('warning', "Cannot delete this project/charging.");
                } else {
                    $project->delete();

                    return redirect(url()->previous())
                           ->with('success', "Successfully deleted '" . $projectName . "'.");
                }

                break;


            case 'signatory':
                $signatory = Signatory::find($key);
                $sig = $signatory->emp_id;
                $signatory->delete();

                return redirect(url()->previous())
                       ->with('success', "Successfully deleted '" . $sig . "'.");
                break;

            case 'supplier_classification':
                $classification = SupplierClassification::find($key);
                $className = $classification->classification;
                $supplierDataCount = Supplier::where('class_id', $classification->id)->count();

                if ($supplierDataCount > 0) {
                    return redirect(url()->previous())
                           ->with('warning', "Cannot delete this classification.");
                } else {
                    $classification->delete();

                    return redirect(url()->previous())
                           ->with('success', "Successfully deleted '" . $className . "'.");
                }

                break;

            case 'supplier':
                $supplier = Supplier::find($key);
                $SupplierName = $supplier->company_name;
                $abstractDataCount = AbstractItem::where('supplier_id', $supplier->id)->count();

                if ($abstractDataCount > 0) {
                    return redirect(url()->previous())
                           ->with('warning', "Cannot delete this supplier.");
                } else {
                    $supplier->delete();

                    return redirect(url()->previous())
                           ->with('success', "Successfully deleted '" . $SupplierName . "'.");
                }

                break;

            case 'unit_issue':
                $unit = UnitIssue::find($key);
                $unitName = $unit->unit;
                $prDataCount = DB::table('tblpr_items')->where('unit_issue', $unit->id)->count();
                $poDataCount = DB::table('tblpo_jo_items')->where('unit_issue', $unit->id)->count();

                if ($prDataCount > 0 || $poDataCount > 0) {
                    return redirect(url()->previous())
                           ->with('warning', "Cannot delete this unit.");
                } else {
                    $unit->delete();

                    return redirect(url()->previous())
                           ->with('success', "Successfully deleted '" . $unitName . "'.");
                }

                break;

            case 'user_group':
                $userGroup = UserGroups::find($key);
                $userGroupName = $userGroup->group_name;
                $empDataCount = DB::table('tblemp_accounts')->where('group', $userGroup->id)->count();

                if ($empDataCount > 0) {
                    return redirect(url()->previous())
                           ->with('warning', "Cannot delete this unit.");
                } else {
                    $userGroup->delete();

                    return redirect(url()->previous())
                           ->with('success', "Successfully deleted '" . $userGroupName . "'.");
                }

                break;

            default:
                # code...
                break;
        }
    }

}
