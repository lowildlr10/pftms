<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseRequest;
use App\Abstracts;
use App\AbstractItem;

use App\Models\EmpAccount as User;
use App\Models\EmpDivision;
use App\Models\EmpUnit;
use App\Models\EmpGroup;
use App\Models\EmpRole;
use App\Models\FundingProject;
use App\Models\InventoryClassification;
use App\Models\ItemClassification;
use App\Models\ItemUnitIssue;
use App\Models\PaperSize;
use App\Models\ProcurementMode;
use App\Models\Province;
use App\Models\Region;
use App\Models\Signatory;
use App\Models\Supplier;
use App\Models\SupplierClassification;
use App\Models\IndustrySector;
use App\Models\Municipality;
use App\Models\MonitoringOffice;
use App\Models\AgencyLGU;
use App\Models\MfoPap;
use App\Models\UacsObjectClassification;
use App\Models\UacsObjectCode;

use DB;
use Auth;
use App\Plugin\DuplicateChecker;

class LibraryController extends Controller
{
    protected $moduleLabels = [
        'pr' => 'Purchase Request',
            'pr_approval' => 'Approval',
            'pr_within_app' => 'Within APP',
            'pr_funds_available' => 'Funds Available',
            'pr_recommended_by' => 'Recommended By',
        'rfq' => 'Request for Quotations',
            'rfq_truly_yours' => 'Truly Yours',
        'abs' => 'Abstract of Quotation',
            'abs_chairperson' => 'Chairperson',
            'abs_vice_chair' => 'Vice Chairperson',
            'abs_member' => 'Member',
        'po' => 'Purchase Order',
            'po_funds_available' => 'Chief Accountant/Head of Accounting Division/Funds Available',
            'po_approved' => 'Regional Director or Authorized Representative',
        'jo' => 'Job Order',
            'jo_Requisitioning' => 'Requisitioning Office/Department',
            'jo_funds_available' => 'Funds Available',
            'jo_approved' => 'Authorized Signatory',
        'ors' => 'Obligation/Budget Utilization & Report Status',
            'ors_approval' => 'Approval',
            'ors_funds_available' => 'Funds Available',
        'iar' => 'Inspection & Acceptance Report',
            'iar_inspection' => 'Inspection Office/Inspection Committee',
            'iar_prop_supply' => 'Supply and/or Property Custodian',
        'dv' => 'Disbursement Voucher',
            'dv_supervisor' => 'Printed Name, Designation and Signature of Supervisor',
            'dv_accounting' => 'Head, Accounting Unit/Authorized Representative',
            'dv_agency_head' => 'Agency Head/Authorized Representative',
        'ris' => 'Requisition & Issue Slip',
            'ris_approved_by' => 'Approved By',
            'ris_issued_by' => 'Issued By',
        'par' => 'Property Aknowledgement Receipt',
            'par_issued_by' => 'Issued By',
        'ics' => 'Inventory Custodian Slip',
            'ics_received_from' => 'Received From',
        'lr' => 'Liquidation Report',
            'lr_immediate_sup' => 'Immediate Supervisor',
            'lr_accounting' => 'Head, Accounting Division Unit',
        'lddap' => 'List of Due and Demandable Accounts Payable',
            'lddap_cert_correct' => 'Certified Correct',
            'lddap_approval' => 'Approval',
            'lddap_agency_auth' => 'Agency Authorized Signatories',
        'summary' => 'Summary of LDDAP-ADAs Issued and Invalidated ADA Entries',
            'summary_cert_correct' => 'Certified Correct',
            'summary_approved_by' => 'Approved By',
            'summary_delivered_by' => 'Delivered By',
            'summary_received_by' => 'Received By',
        'lib' => 'Line Item Budget',
            'lib_approved_by' => 'Approved By',
        'librealign' => 'Line Item Budget Realignment',
            'librealign_approved_by' => 'Approved By',
    ];
    protected $modules = [
        'pr' => [
            'pr_approval' => 'approval',
            'pr_within_app' => 'within_app',
            'pr_funds_available' => 'funds_available',
            'pr_recommended_by' => 'recommended_by',
        ],
        'rfq' => [
            'rfq_truly_yours' => 'truly_yours',
        ],
        'abs' => [
            'abs_chairperson' => 'chairperson',
            'abs_vice_chair' => 'vice_chair',
            'abs_member' => 'member',
        ],
        'po' => [
            'po_funds_available' => 'funds_available',
            'po_approved' => 'approved',
        ],
        'jo' => [
            'jo_Requisitioning' => 'requisitioning',
            'jo_funds_available' => 'funds_available',
            'jo_approved' => 'approved',
        ],
        'ors' => [
            'ors_approval' => 'approval',
            'ors_funds_available' => 'funds_available',
        ],
        'iar' => [
            'iar_inspection' => 'inspection',
            'iar_prop_supply' => 'prop_supply',
        ],
        'dv' => [
            'dv_supervisor' => 'supervisor',
            'dv_accounting' => 'accounting',
            'dv_agency_head' => 'agency_head',
        ],
        'ris' => [
            'ris_approved_by' => 'approved_by',
            'ris_issued_by' => 'issued_by',
        ],
        'par' => [
            'par_issued_by' => 'issued_by',
        ],
        'ics' => [
            'ics_received_from' => 'received_from',
        ],
        'lr' => [
            'lr_immediate_sup' => 'immediate_sup',
            'lr_accounting' => 'accounting',
        ],
        'lddap' => [
            'lddap_cert_correct' => 'cert_correct',
            'lddap_approval' => 'approval',
            'lddap_agency_auth' => 'agency_auth',
        ],
        'summary' => [
            'summary_cert_correct' => 'cert_correct',
            'summary_approved_by' => 'approved_by',
            'summary_delivered_by' => 'delivered_by',
            'summary_received_by' => 'received_by',
        ],
        'lib' => [
            'lib_approved_by' => 'approved_by',
        ],
        'librealign' => [
            'librealign_approved_by' => 'approved_by',
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
     *  Agency/LGU Module
    **/
    public function indexAgencyLGU(Request $request) {
        $agencyData = AgencyLGU::orderBy('agency_name')
                               ->with(['_region', '_province', '_municipality'])
                               ->get();

        foreach ($agencyData as $agency) {
            $agency->region_name = $agency->region ?
                                   $agency->_region->region_name : NULL;
            $agency->province_name = $agency->province ?
                                     $agency->_province->province_name : NULL;
            $agency->municipality_name = $agency->municipality ?
                                         $agency->_municipality->region_name : NULL;
        }

        return view('modules.library.agency-lgu.index', [
            'list' => $agencyData
        ]);
    }

    public function showCreateAgencyLGU() {
        $regions = Region::orderBy('region_name')->get();
        $provinces = Province::orderBy('province_name')->get();
        $municipalities = Municipality::orderBy('municipality_name')->get();

        return view('modules.library.agency-lgu.create', compact(
            'regions',
            'provinces',
            'municipalities',
        ));
    }

    public function showEditAgencyLGU($id) {
        $agencyData = AgencyLGU::find($id);
        $regions = Region::orderBy('region_name')->get();
        $provinces = Province::orderBy('province_name')->get();
        $municipalities = Municipality::orderBy('municipality_name')->get();

        $region = $agencyData->region;
        $province = $agencyData->province;
        $municipality = $agencyData->municipality;
        $agencyName = $agencyData->agency_name;

        return view('modules.library.agency-lgu.update', compact(
            'id',
            'region',
            'province',
            'municipality',
            'agencyName',
            'regions',
            'provinces',
            'municipalities',
        ));
    }

    public function storeAgencyLGU(Request $request) {
        $region = $request->region;
        $province = $request->province;
        $municipality = $request->municipality;
        $agencyName = $request->agency_lgu;

        try {
            if (!$this->checkDuplication('AgencyLGU', $agencyName)) {
                $instanceAgency = new AgencyLGU;
                $instanceAgency->region = $region;
                $instanceAgency->province = $province;
                $instanceAgency->municipality = $municipality;
                $instanceAgency->agency_name = $agencyName;
                $instanceAgency->save();

                $msg = "Agency/LGU '$agencyName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Agency/LGU '$agencyName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateAgencyLGU(Request $request, $id) {
        $region = $request->region;
        $province = $request->province;
        $municipality = $request->municipality;
        $agencyName = $request->agency_lgu;

        try {
            $instanceAgency = AgencyLGU::find($id);
            $instanceAgency->region = $region;
            $instanceAgency->province = $province;
            $instanceAgency->municipality = $municipality;
            $instanceAgency->agency_name = $agencyName;
            $instanceAgency->save();

            $msg = "Agency/LGU '$agencyName' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteAgencyLGU($id) {
        try {
            $instanceAgency = AgencyLGU::find($id);
            $agencyName = $instanceAgency->agency_name;
            $instanceAgency->delete();

            $msg = "Agency/LGU '$agencyName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyAgencyLGU($id) {
        try {
            $instanceAgency = AgencyLGU::find($id);
            $agencyName = $instanceAgency->agency_name;
            $instanceAgency->destroy();

            $msg = "Agency/LGU '$agencyName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Industry/Sector Module
    **/
    public function indexIndustrySector(Request $request) {
        $industryData = IndustrySector::orderBy('sector_name')
                                      ->get();

        return view('modules.library.industry-sector.index', [
            'list' => $industryData
        ]);
    }

    public function showCreateIndustrySector() {
        return view('modules.library.industry-sector.create');
    }

    public function showEditIndustrySector($id) {
        $industryData = IndustrySector::find($id);
        $industrySector = $industryData->sector_name;

        return view('modules.library.industry-sector.update', compact(
            'id',
            'industrySector',
        ));
    }

    public function storeIndustrySector(Request $request) {
        $industrySector = $request->industry_sector;

        try {
            if (!$this->checkDuplication('IndustrySector', $industrySector)) {
                $instanceIndustry = new IndustrySector;
                $instanceIndustry->sector_name = $industrySector;
                $instanceIndustry->save();

                $msg = "Industry/Sector '$industrySector' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Industry/Sector '$industrySector' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateIndustrySector(Request $request, $id) {
        $industrySector = $request->industry_sector;

        try {
            $instanceIndustry = IndustrySector::find($id);
            $instanceIndustry->sector_name = $industrySector;
            $instanceIndustry->save();

            $msg = "Industry/Sector '$industrySector' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteIndustrySector($id) {
        try {
            $instanceIndustry = IndustrySector::find($id);
            $industrySector = $instanceIndustry->sector_name;
            $instanceIndustry->delete();

            $msg = "Industry/Sector '$industrySector' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyIndustrySector($id) {
        try {
            $instanceIndustry = IndustrySector::find($id);
            $industrySector = $instanceIndustry->sector_name;
            $instanceIndustry->destroy();

            $msg = "Industry/Sector '$industrySector' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Item Classification Module
    **/
    public function indexItemClassification(Request $request) {
        $itemClassData = ItemClassification::orderBy('classification_name')
                                           ->get();

        return view('modules.library.item-classification.index', [
            'list' => $itemClassData
        ]);
    }

    public function showCreateItemClassification() {
        return view('modules.library.item-classification.create');
    }

    public function showEditItemClassification($id) {
        $itemClassData = ItemClassification::find($id);
        $classification = $itemClassData->classification_name;

        return view('modules.library.item-classification.update', [
            'id' => $id,
            'classification' => $classification
        ]);
    }

    public function storeItemClassification(Request $request) {
        $className = $request->classification_name;

        try {
            if (!$this->checkDuplication('ItemClassification', $className)) {
                $instanceItemClass = new ItemClassification;
                $instanceItemClass->classification_name = $className;
                $instanceItemClass->save();

                $msg = "Item classification '$className' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Item classification '$className' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateItemClassification(Request $request, $id) {
        $className = $request->classification_name;

        try {
            $instanceItemClass = ItemClassification::find($id);
            $instanceItemClass->classification_name = $className;
            $instanceItemClass->save();

            $msg = "Item classification '$className' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteItemClassification($id) {
        try {
            $instanceItemClass = ItemClassification::find($id);
            $className = $instanceItemClass->classification_name;
            $instanceItemClass->delete();

            $msg = "Item classification '$className' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyItemClassification($id) {
        try {
            $instanceItemClass = ItemClassification::find($id);
            $className = $instanceItemClass->classification_name;
            $instanceItemClass->destroy();

            $msg = "Item classification '$className' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  MFO/PAP Module
    **/
    public function indexMfoPap(Request $request) {
        $mfoPapData = MfoPap::orderBy('code')
                            ->get();

        return view('modules.library.mfo-pap.index', [
            'list' => $mfoPapData
        ]);
    }

    public function showCreateMfoPap() {
        return view('modules.library.mfo-pap.create');
    }

    public function showEditMfoPap($id) {
        $mfoPapDat = MfoPap::find($id);
        $code = $mfoPapDat->code;
        $description = $mfoPapDat->description;

        return view('modules.library.mfo-pap.update', compact(
            'id', 'code', 'description'
        ));
    }

    public function storeMfoPap(Request $request) {
        $code = $request->code;
        $description = $request->description;

        try {
            if (!$this->checkDuplication('MfoPap', $code)) {
                $instanceMfoPap = new MfoPap;
                $instanceMfoPap->code = $code;
                $instanceMfoPap->description = $description;
                $instanceMfoPap->save();

                $msg = "MFO/PAP '$description' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "MFO/PAP '$description' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateMfoPap(Request $request, $id) {
        $code = $request->code;
        $description = $request->description;

        try {
            $instanceMfoPap = MfoPap::find($id);
            $instanceMfoPap->code = $code;
            $instanceMfoPap->description = $description;
            $instanceMfoPap->save();

            $msg = "MFO/PAP '$description' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteMfoPap($id) {
        try {
            $instanceMfoPap = MfoPap::find($id);
            $description = $instanceMfoPap->description;
            $instanceMfoPap->delete();

            $msg = "MFO/PAP '$description' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyMfoPap($id) {
        try {
            $instanceMfoPap = ItemClassification::find($id);
            $description = $instanceMfoPap->description;
            $instanceMfoPap->destroy();

            $msg = "MFO/PAP '$description' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Monitoring Office Module
    **/
    public function indexMonitoringOffice(Request $request) {
        $monitorOfficeData = MonitoringOffice::orderBy('office_name')
                                             ->with(['_agencylgu'])
                                             ->get();

        foreach ($monitorOfficeData as $office) {
            $office->agency_lgu_name = $office->agency_lgu ?
                                       $office->_agencylgu->agency_name : NULL;
        }

        return view('modules.library.monitoring-office.index', [
            'list' => $monitorOfficeData
        ]);
    }

    public function showCreateMonitoringOffice() {
        $agencyLGUs = AgencyLGU::orderBy('agency_name')->get();

        return view('modules.library.monitoring-office.create', [
            'agencies' => $agencyLGUs,
        ]);
    }

    public function showEditMonitoringOffice($id) {
        $monitorOfficeData = MonitoringOffice::find($id);
        $agencies = AgencyLGU::orderBy('agency_name')->get();

        $agencyLGU = $monitorOfficeData->agency_lgu;
        $officeName = $monitorOfficeData->office_name;

        return view('modules.library.monitoring-office.update', compact(
            'id',
            'agencies',
            'agencyLGU',
            'officeName'
        ));
    }

    public function storeMonitoringOffice(Request $request) {
        $agencyLGU = $request->agency_lgu;
        $officeName = $request->office_name;

        try {
            if (!$this->checkDuplication('MonitoringOffice', $officeName)) {
                $instanceMonitorOffice = new MonitoringOffice;
                $instanceMonitorOffice->agency_lgu = $agencyLGU;
                $instanceMonitorOffice->office_name = $officeName;
                $instanceMonitorOffice->save();

                $msg = "Monitoring office '$officeName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Monitoring office '$officeName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateMonitoringOffice(Request $request, $id) {
        $agencyLGU = $request->agency_lgu;
        $officeName = $request->office_name;

        try {
            $instanceMonitorOffice = MonitoringOffice::find($id);
            $instanceMonitorOffice->agency_lgu = $agencyLGU;
            $instanceMonitorOffice->office_name = $officeName;
            $instanceMonitorOffice->save();

            $msg = "Monitoring office '$officeName' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteMonitoringOffice($id) {
        try {
            $instanceMonitorOffice = MonitoringOffice::find($id);
            $officeName = $instanceMonitorOffice->office_name;
            $instanceMonitorOffice->delete();

            $msg = "Monitoring office '$officeName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyMonitoringOffice($id) {
        try {
            $instanceMonitorOffice = MonitoringOffice::find($id);
            $officeName = $instanceMonitorOffice->classification_name;
            $instanceMonitorOffice->destroy();

            $msg = "Monitoring office '$officeName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Project Module
    **/
    public function indexProject(Request $request) {

        // Get module access
        $module = 'lib_funding';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');

        // User groups
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();
        $roleHasAdministrator = Auth::user()->hasOrdinaryRole();
        $roleHasRD = Auth::user()->hasRdRole();
        $roleHasARD = Auth::user()->hasArdRole();
        $roleHasPSTD = Auth::user()->hasPstdRole();
        $roleHasPlanning = Auth::user()->hasPlanningRole();
        $roleHasProjectStaff = Auth::user()->hasProjectStaffRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAccountant = Auth::user()->hasAccountantRole();
        $roleHasPropertySupply = Auth::user()->hasPropertySupplyRole();
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();

        $projDat = new FundingProject;
        $projectData = FundingProject::orderBy('project_title');

        if ($roleHasDeveloper || $roleHasRD || $roleHasARD || $roleHasPlanning || $roleHasBudget) {
        } else {
            $projectIDs = $projDat->getAccessibleProjects();

            $projectData = $projectData->where(function($qry) use ($projectIDs) {
                $qry->whereIn('id', $projectIDs);
            });
        }

        $projectData = $projectData->get();

        $directories = [];

        foreach ($projectData as $proj) {
            $_projectSites = [];
            $projectSites = $proj->project_site ? unserialize($proj->project_site) : [];
            $_directories = $proj->directory ? unserialize($proj->directory) : [];
            $projID = $proj->id;
            $projTitle = (strlen($proj->project_title) > 30 ?
                         substr($proj->project_title, 0, 30).'...' :
                         $proj->project_title);

            foreach ($projectSites as $site) {
                $projSiteDat = Region::select('region_name as name')
                                 ->find($site);
                $projSiteDat = !$projSiteDat ? DB::table('provinces as prov')
                                                 ->select(DB::raw("CONCAT(prov.province_name, ', ', reg.region_name) as name"))
                                                 ->leftJoin('regions as reg', 'reg.id', '=', 'prov.region')
                                                 ->where('prov.id', $site)->first() : $projSiteDat;
                $projSiteDat = !$projSiteDat ? DB::table('municipalities as mun')
                                                 ->select(DB::raw("CONCAT(mun.municipality_name, ', ', prov.province_name, ', ', reg.region_name) as name"))
                                                 ->leftJoin('regions as reg', 'reg.id', '=', 'mun.region')
                                                 ->leftJoin('provinces as prov', 'prov.id', '=', 'mun.province')
                                                 ->where('mun.id', $site)->first() : $projSiteDat;

                if ($projSiteDat) {
                    $_projectSites[] = $projSiteDat->name;
                }
            }

            $proj->project_site = $_projectSites;

            if (count($_directories) > 0) {
                $dirs = $_directories;
                array_shift($dirs);

                $directory = count($dirs) > 0 ? implode(' / ', $dirs) : NULL;

                if (!isset($directories['folder'])) {
                    $directories['folder'][0]['name'] = $_directories[0];

                    $directories['folder'][0]['files'][] = (object) [
                        'id' => $projID,
                        'directory' => $directory,
                        'title' => $projTitle
                    ];
                } else {
                    $hasExisting = false;

                    foreach ($directories['folder'] as $dirKey => $dir) {
                        if ($dir['name'] == $_directories[0]) {
                            $hasExisting = true;

                            $directories['folder'][$dirKey]['files'][] = (object) [
                                'id' => $projID,
                                'directory' => $directory,
                                'title' => $projTitle
                            ];

                            sort($directories['folder'][$dirKey]['files']);
                            break;
                        }
                    }

                    if (!$hasExisting) {
                        $newKey = count($directories['folder']);
                        $directories['folder'][$newKey]['name'] = $_directories[0];
                        $directories['folder'][$newKey]['files'][] = (object) [
                            'id' => $projID,
                            'directory' => $directory,
                            'title' => $projTitle
                        ];

                        sort($directories['folder'][$newKey]['files']);
                    }

                    sort($directories['folder']);
                }
            } else {
                $directories['file'][] = (object) [
                    'id' => $projID,
                    'title' => $projTitle
                ];
            }
        }

        return view('modules.library.project.index', [
            'list' => $projectData,
            'directories' => $directories,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
        ]);
    }

    public function showCreateProject() {
        $projects = FundingProject::get();
        $industries = IndustrySector::orderBy('sector_name')->get();
        $projectSites1 = DB::table('regions as _reg')
                           ->select('_reg.id', '_reg.region_name as name')
                           ->orderBy('name');
        $projectSites2 = DB::table('provinces as _prov')
                           ->select('_prov.id', DB::raw("CONCAT(_prov.province_name, ', ', __reg.region_name) as name"))
                           ->leftJoin('regions as __reg', '__reg.id', '=', '_prov.region')
                           ->orderBy('name');
        $projectSites = DB::table('municipalities as mun')
                          ->select('mun.id', DB::raw("CONCAT(mun.municipality_name, ', ', prov.province_name, ', ', reg.region_name) as name"))
                          ->leftJoin('regions as reg', 'reg.id', '=', 'mun.region')
                          ->leftJoin('provinces as prov', 'prov.id', '=', 'mun.province')
                          ->union($projectSites2)
                          ->union($projectSites1)
                          ->get();

        $empUnits = EmpUnit::orderBy('unit_name')->get();
        $empGroups = EmpGroup::orderBy('group_name')->get();
        $agencies = AgencyLGU::orderBy('agency_name')->get();
        $monitoringOffices = MonitoringOffice::orderBy('office_name')->get();

        $directories = [];

        foreach ($projects as $proj) {
            $_directories = $proj->directory ? unserialize($proj->directory) : [];

            if (count($_directories) > 0) {
                $dir = implode(' / ', $_directories);

                $directories['directory'][] = $dir;

                foreach ($_directories as $dirItem) {
                    if (!isset($directories['items'])) {
                        $directories['items'][] = $dirItem;
                    } else {
                        if (!in_array($dirItem, $directories['items'])) {
                            $directories['items'][] = $dirItem;
                        }
                    }
                }
            }
        }

        if (isset($directories['directory'])) {
            sort($directories['directory']);
        }

        if (isset($directories['items'])) {
            sort($directories['items']);
        }

        return view('modules.library.project.create', compact(
            'industries',
            'projectSites',
            'empUnits',
            'agencies',
            'monitoringOffices',
            'empGroups',
            'directories'
        ));
    }

    public function showEditProject($id) {
        $projects = DB::table('funding_projects')->get();
        $projectData = FundingProject::find($id);
        $industries = IndustrySector::orderBy('sector_name')->get();
        $projectSites1 = DB::table('regions as _reg')
                           ->select('_reg.id', '_reg.region_name as name')
                           ->orderBy('name');
        $projectSites2 = DB::table('provinces as _prov')
                           ->select('_prov.id', DB::raw("CONCAT(_prov.province_name, ', ', __reg.region_name) as name"))
                           ->leftJoin('regions as __reg', '__reg.id', '=', '_prov.region')
                           ->orderBy('name');
        $projectSites = DB::table('municipalities as mun')
                          ->select('mun.id', DB::raw("CONCAT(mun.municipality_name, ', ', prov.province_name, ', ', reg.region_name) as name"))
                          ->leftJoin('regions as reg', 'reg.id', '=', 'mun.region')
                          ->leftJoin('provinces as prov', 'prov.id', '=', 'mun.province')
                          ->union($projectSites2)
                          ->union($projectSites1)
                          ->get();
        $empUnits = EmpUnit::orderBy('unit_name')->get();
        $agencies = AgencyLGU::orderBy('agency_name')->get();
        $monitoringOffices = MonitoringOffice::orderBy('office_name')->get();
        $empGroups = EmpGroup::orderBy('group_name')->get();

        $industrySector = $projectData->industry_sector;
        $projectSite = $projectData->project_site ? unserialize($projectData->project_site) : [];
        $implementingAgency = $projectData->implementing_agency;
        $implementingBudget = $projectData->implementing_project_cost;
        $comimplementingAgencyLGUs = unserialize($projectData->comimplementing_agency_lgus);
        $proponentUnits = unserialize($projectData->proponent_units);
        $dateFrom = $projectData->date_from;
        $dateTo = $projectData->date_to;
        $projectCost = $projectData->project_cost;
        $monitoringOffice  = $projectData->monitoring_offices ?
                             unserialize($projectData->monitoring_offices) : [];
        $accessGroup  = $projectData->access_groups ?
                        unserialize($projectData->access_groups) : [];
        $directory = $projectData->directory ?
                     implode(' / ', unserialize($projectData->directory)) : NULL;
        $projectTitle = $projectData->project_title;
        $projectType = $projectData->project_type;
        $projectLeader = $projectData->project_leader;

        $coimplementingCount = $comimplementingAgencyLGUs ? count($comimplementingAgencyLGUs) : 0;
        $propenentCount = $proponentUnits ? count($proponentUnits) : 0;
        $comimplementingAgencyLGUs = $coimplementingCount > 0 ? $comimplementingAgencyLGUs : [];
        $proponentUnits = $propenentCount > 0 ? $proponentUnits : [];
        $directories = [];

        foreach ($projects as $proj) {
            $_directories = $proj->directory ? unserialize($proj->directory) : [];

            if (count($_directories) > 0) {
                $dir = implode(' / ', $_directories);

                $directories['directory'][] = $dir;

                foreach ($_directories as $dirItem) {
                    if (!isset($directories['items'])) {
                        $directories['items'][] = $dirItem;
                    } else {
                        if (!in_array($dirItem, $directories['items'])) {
                            $directories['items'][] = $dirItem;
                        }
                    }
                }
            }
        }

        if (isset($directories['directory'])) {
            sort($directories['directory']);
        }

        if (isset($directories['items'])) {
            //sort($directories['items']);
        }

        return view('modules.library.project.update', compact(
            'id',
            'industries',
            'projectSites',
            'empUnits',
            'agencies',
            'monitoringOffices',
            'empGroups',
            'industrySector',
            'projectSite',
            'implementingAgency',
            'implementingBudget',
            'comimplementingAgencyLGUs',
            'proponentUnits',
            'dateFrom',
            'dateTo',
            'projectCost',
            'monitoringOffice',
            'accessGroup',
            'projectTitle',
            'projectType',
            'projectLeader',
            'coimplementingCount',
            'propenentCount',
            'directories',
            'directory'
        ));
    }

    public function storeProject(Request $request) {
        $directory = $request->directory ? serialize($request->directory) : serialize([]);
        $projectTitle = $request->project_title;
        $industrySector = $request->industry_sector;
        $projectSite = $request->project_site ? serialize($request->project_site) : serialize([]);
        $implementingAgency = $request->implementing_agency;
        $implementingBudget = $request->implementing_project_cost;
        $withCoimplementingAgency = $request->with_coimplementing_agency == 'on' ? true : false;
        $projectCost = $request->project_cost;
        $projectLeader = $request->project_leader;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $monitoringOffice = $request->monitoring_office;
        $accessGroup = $request->access_group;
        $projectType = $request->project_type;

        $comimplementingAgencyLGUs = $request->comimplementing_agency_lgus;
        $coimplementingProjectCosts = $request->coimplementing_project_costs;
        $proponentUnits = $request->proponent_units;
        $coimplementingAgencies = [];

        try {
            $newMonitoringOffices = [];

            if ($withCoimplementingAgency) {
                foreach ($comimplementingAgencyLGUs as $coimpCtr => $agency) {
                    $agencyLguDat = AgencyLGU::find($agency);
                    $coimpProjCost = $coimplementingProjectCosts[$coimpCtr] ?
                                     $coimplementingProjectCosts[$coimpCtr] :
                                     0;

                    if (!$agencyLguDat) {
                        $instanceAgency = new AgencyLGU;
                        $instanceAgency->agency_name = $agency;
                        $instanceAgency->save();

                        $agency = $instanceAgency->id->string;
                    }

                    $coimplementingAgencies[] = [
                        'comimplementing_agency_lgu' => $agency,
                        'coimplementing_project_cost' => $coimpProjCost
                    ];
                }
            }

            $industryDat = IndustrySector::find($industrySector);
            $agencyLguDat = AgencyLGU::find($implementingAgency);

            if (!$industryDat) {
                $instanceIndustry = new IndustrySector;
                $instanceIndustry->sector_name = $industrySector;
                $instanceIndustry->save();

                $industrySector = $instanceIndustry->id->string;
            }

            if (!$agencyLguDat) {
                $instanceAgency = new AgencyLGU;
                $instanceAgency->agency_name = $implementingAgency;
                $instanceAgency->save();

                $implementingAgency = $instanceAgency->id->string;
            }

            foreach ($monitoringOffice as $monitOffice) {
                $monitoringDat = MonitoringOffice::find($monitOffice);

                if (!$monitoringDat) {
                    $instanceMonit = new MonitoringOffice;
                    $instanceMonit->office_name = $monitOffice;
                    $instanceMonit->save();

                    $monitOffice = $instanceMonit->id->string;
                }

                $newMonitoringOffices[] = $monitOffice;
            }

            $instanceProject = new FundingProject;
            $instanceProject->directory = $directory;
            $instanceProject->project_title = $projectTitle;
            $instanceProject->industry_sector = $industrySector;
            $instanceProject->project_site = $projectSite;
            $instanceProject->implementing_agency = $implementingAgency;
            $instanceProject->implementing_project_cost = $implementingBudget;
            $instanceProject->comimplementing_agency_lgus = serialize($coimplementingAgencies);
            $instanceProject->proponent_units = serialize($proponentUnits);
            $instanceProject->date_from = $dateFrom;
            $instanceProject->date_to = $dateTo;
            $instanceProject->project_cost = $projectCost;
            $instanceProject->project_leader = $projectLeader;
            $instanceProject->monitoring_offices = serialize($newMonitoringOffices);
            $instanceProject->access_groups = $accessGroup ? serialize($accessGroup) : serialize([]);
            $instanceProject->project_type = $projectType;
            $instanceProject->created_by = Auth::user()->id;
            $instanceProject->save();

            $msg = "Project '$projectTitle' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateProject(Request $request, $id) {
        $directory = $request->directory ? serialize($request->directory) : serialize([]);
        $projectTitle = $request->project_title;
        $industrySector = $request->industry_sector;
        $projectSite = $request->project_site ? serialize($request->project_site) : serialize([]);
        $implementingAgency = $request->implementing_agency;
        $implementingBudget = $request->implementing_project_cost;
        $withCoimplementingAgency = $request->with_coimplementing_agency == 'on' ? true : false;
        $projectCost = $request->project_cost;
        $projectLeader = $request->project_leader;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $monitoringOffice = $request->monitoring_office;
        $accessGroup = $request->access_group;
        $projectType = $request->project_type;

        $comimplementingAgencyLGUs = $request->comimplementing_agency_lgus;
        $coimplementingProjectCosts = $request->coimplementing_project_costs;
        $proponentUnits = $request->proponent_units;
        $coimplementingAgencies = [];

        try {
            $newMonitoringOffices = [];

            if ($withCoimplementingAgency) {
                foreach ($comimplementingAgencyLGUs as $coimpCtr => $agency) {
                    $agencyLguDat = AgencyLGU::find($agency);
                    $coimpProjCost = $coimplementingProjectCosts[$coimpCtr] ?
                                     $coimplementingProjectCosts[$coimpCtr] :
                                     0;

                    if (!$agencyLguDat) {
                        $instanceAgency = new AgencyLGU;
                        $instanceAgency->agency_name = $agency;
                        $instanceAgency->save();

                        $agency = $instanceAgency->id->string;
                    }

                    $coimplementingAgencies[] = [
                        'comimplementing_agency_lgu' => $agency,
                        'coimplementing_project_cost' => $coimpProjCost
                    ];
                }
            }

            $industryDat = IndustrySector::find($industrySector);
            $agencyLguDat = AgencyLGU::find($implementingAgency);

            if (!$industryDat) {
                $instanceIndustry = new IndustrySector;
                $instanceIndustry->sector_name = $industrySector;
                $instanceIndustry->save();

                $industrySector = $instanceIndustry->id->string;
            }

            if (!$agencyLguDat) {
                $instanceAgency = new AgencyLGU;
                $instanceAgency->agency_name = $implementingAgency;
                $instanceAgency->save();

                $implementingAgency = $instanceAgency->id->string;
            }

            foreach ($monitoringOffice as $monitOffice) {
                $monitoringDat = MonitoringOffice::find($monitOffice);

                if (!$monitoringDat) {
                    $instanceMonit = new MonitoringOffice;
                    $instanceMonit->office_name = $monitOffice;
                    $instanceMonit->save();

                    $monitOffice = $instanceMonit->id->string;
                }

                $newMonitoringOffices[] = $monitOffice;
            }

            $instanceProject = FundingProject::find($id);
            $oldProjectType = $instanceProject->project_type;
            $instanceProject->directory = $directory;
            $instanceProject->project_title = $projectTitle;
            $instanceProject->industry_sector = $industrySector;
            $instanceProject->project_site = $projectSite;
            $instanceProject->implementing_agency = $implementingAgency;
            $instanceProject->implementing_project_cost = $implementingBudget;
            $instanceProject->comimplementing_agency_lgus = serialize($coimplementingAgencies);
            $instanceProject->proponent_units = serialize($proponentUnits);
            $instanceProject->date_from = $dateFrom;
            $instanceProject->date_to = $dateTo;
            $instanceProject->project_cost = $projectCost;
            $instanceProject->project_leader = $projectLeader;
            $instanceProject->monitoring_offices = serialize($newMonitoringOffices);
            $instanceProject->access_groups = $accessGroup ? serialize($accessGroup) : serialize([]);
            $instanceProject->project_type = $projectType;
            $instanceProject->save();

            if ($oldProjectType != $projectType) {
                DB::table('funding_ledger_allotments')
                  ->where('project_id', $id)
                  ->delete();
                DB::table('funding_ledger_items')
                  ->where('project_id', $id)
                  ->delete();
                DB::table('funding_ledgers')
                  ->where('project_id', $id)
                  ->delete();
            }

            $msg = "Project '$projectTitle' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteProject($id) {
        try {
            $instanceProject = FundingProject::find($id);
            $projectTitle = $instanceProject->project_title;
            $instanceProject->delete();

            $msg = "Project '$projectTitle' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyProject($id) {
        try {
            $instanceProject = FundingProject::find($id);
            $projectTitle = $instanceProject->project_title;
            $instanceProject->delete();

            $msg = "Project '$projectTitle' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Signatory Module
    **/
    public function indexSignatory(Request $request) {
        $signatoryData = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->orderBy('id')->get();

        return view('modules.library.signatory.index', [
            'list' => $signatoryData
        ]);
    }

    public function showCreateSignatory() {
        $usersData = User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                  'id')
                         ->orderBy('firstname')
                         ->get();

        return view('modules.library.signatory.create', [
            'label' => $this->moduleLabels,
            'modules' => $this->modules,
            'employees' => $usersData
        ]);
    }

    public function showEditSignatory($id) {
        $signatoryData = Signatory::find($id);
        $empID = $signatoryData->emp_id;
        $isActive = $signatoryData->is_active;
        $module = json_decode($signatoryData->module);
        $usersData = User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                  'id')
                         ->orderBy('firstname')
                         ->get();

        return view('modules.library.signatory.update', [
            'id' => $id,
            'employees' => $usersData,
            'empID' => $empID,
            'moduleAccess' => $module,
            'label' => $this->moduleLabels,
            'modules' => $this->modules,
            'isActive' => $isActive
        ]);
    }

    public function storeSignatory(Request $request) {
        $empID = $request->emp_id;
        $module = $request->module;
        $module = str_replace("\n", '', $module);
        $module = trim($module);
        //$module = preg_replace('/\s/', '', $module );
        $sigName =  User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                        ->where('id', $empID)
                        ->first();

        try {
            if (!$this->checkDuplication('Signatory', $empID)) {
                $instanceSignatory = new Signatory;
                $instanceSignatory->emp_id = $empID;
                $instanceSignatory->module = $module;
                $instanceSignatory->save();

                $msg = "Signatory '".$sigName->name."' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Signatory '".$sigName->name."' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateSignatory(Request $request, $id) {
        $empID = $request->emp_id;
        $isActive = $request->is_active;
        $module = $request->module;
        $module = str_replace("\n", '', $module);
        $module = trim($module);
        //$module = preg_replace('/\s/', '', $module );
        $sigName =  User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                        ->where('id', $empID)
                        ->first();

        try {
            $instanceSignatory = Signatory::find($id);
            $instanceSignatory->emp_id = $empID;
            $instanceSignatory->is_active = $isActive;
            $instanceSignatory->module = $module;
            $instanceSignatory->save();

            $msg = "Signatory '".$sigName->name."' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteSignatory($id) {
        try {
            $instanceSignatory = Signatory::find($id);
            $empID = $instanceSignatory->emp_id;
            $sigName =  User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                            ->where('id', $empID)
                            ->first();
            $instanceSignatory->delete();

            $msg = "Signatory '".$sigName->name."' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroySignatory($id) {
        try {
            $instanceSignatory = Signatory::find($id);
            $empID = $instanceSignatory->emp_id;
            $sigName =  User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                            ->where('id', $empID)
                            ->first();
            $instanceSignatory->destroy();

            $msg = "Signatory '".$sigName->name."' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Supplier Classification Module
    **/
    public function indexSupplierClassification(Request $request) {
        $supplierClassData = SupplierClassification::orderBy('classification_name')
                                                   ->get();

        return view('modules.library.supplier-classification.index', [
            'list' => $supplierClassData
        ]);
    }

    public function showCreateSupplierClassification() {
        return view('modules.library.supplier-classification.create');
    }

    public function showEditSupplierClassification($id) {
        $supplierClassData = SupplierClassification::find($id);
        $classification = $supplierClassData->classification_name;

        return view('modules.library.supplier-classification.update', [
            'id' => $id,
            'classification' => $classification
        ]);
    }

    public function storeSupplierClassification(Request $request) {
        $classificationName = $request->classification_name;

        try {
            if (!$this->checkDuplication('SupplierClassification', $classificationName)) {
                $instanceSupClass = new SupplierClassification;
                $instanceSupClass->classification_name = $classificationName;
                $instanceSupClass->save();

                $msg = "Supplier classification '$classificationName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Supplier classification '$classificationName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateSupplierClassification(Request $request, $id) {
        $classificationName = $request->classification_name;

        try {
            $instanceSupClass = SupplierClassification::find($id);
            $instanceSupClass->classification_name = $classificationName;
            $instanceSupClass->save();

            $msg = "Supplier classification '$classificationName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteSupplierClassification($id) {
        try {
            $instanceSupClass = SupplierClassification::find($id);
            $classificationName = $instanceSupClass->classification_name;
            $instanceSupClass->delete();

            $msg = "Supplier classification '$classificationName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroySupplierClassification($id) {
        try {
            $instanceSupClass = SupplierClassification::find($id);
            $classificationName = $instanceSupClass->classification_name;
            $instanceSupClass->destroy();

            $msg = "Supplier classification '$classificationName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Supplier Module
    **/
    public function indexSupplier(Request $request) {
        $supplierData = Supplier::addSelect([
            'classification' => SupplierClassification::select('classification_name')
                                                          ->whereColumn('id', 'suppliers.classification')
                                                          ->limit(1)
        ])->orderBy('company_name')->get();

        return view('modules.library.supplier.index', [
            'list' => $supplierData
        ]);
    }

    public function showCreateSupplier() {
        $supClassData = SupplierClassification::orderBy('classification_name')
                                              ->get();

        return view('modules.library.supplier.create', [
            'classifications' => $supClassData
        ]);
    }

    public function showEditSupplier($id) {
        $supClassData = SupplierClassification::orderBy('classification_name')
                                              ->get();
        $supplierData = Supplier::find($id);
        $classification = $supplierData->classification;
        $isActive = $supplierData->is_active;
        $bankName = $supplierData->bank_name;
        $accountName = $supplierData->account_name;
        $accountNo = $supplierData->account_no;
        $companyName = $supplierData->company_name;
        $dateFiled = $supplierData->date_filed;
        $address = $supplierData->address;
        $email = $supplierData->email;
        $websiteURL = $supplierData->website_url;
        $faxNo = $supplierData->fax_no;
        $telephoneNo = $supplierData->telephone_no;
        $mobileNo = $supplierData->mobile_no;
        $dateEstablished = $supplierData->date_established;
        $tinNo = $supplierData->tin_no;
        $vatNo = $supplierData->vat_no;
        $contactPerson = $supplierData->contact_person;
        $natureBusiness = $supplierData->nature_business;
        $natureBusinessOthers = $supplierData->nature_business_others;
        $deliveryVehicleNo = $supplierData->delivery_vehicle_no;
        $productLines = $supplierData->product_lines;
        $creditAccomodation = $supplierData->credit_accomodation;
        $attachments = $supplierData->attachment;
        $attachmentOthers = $supplierData->attachment_others;

        $_attachments = explode('-', $attachments);
        $attachment1 = 0;
        $attachment2 = 0;
        $attachment3 = 0;
        $attachment4 = 0;
        $attachment5 = 0;
        $attachment6 = 0;
        $attachment7 = 0;

        foreach ($_attachments as $attachment) {
            switch ($attachment) {
                case '1':
                    $attachment1 = 1;
                    break;
                case '2':
                    $attachment2 = 1;
                    break;
                case '3':
                    $attachment3 = 1;
                    break;
                case '4':
                    $attachment4 = 1;
                    break;
                case '5':
                    $attachment5 = 1;
                    break;
                case '6':
                    $attachment6 = 1;
                    break;
                case '7':
                    $attachment7 = 1;
                    break;
                default:
                    # code...
                    break;
            }
        }

        return view('modules.library.supplier.update', [
            'id' => $id,
            'classifications' => $supClassData,
            'classification' => $classification,
            'isActive' => $isActive,
            'bankName' => $bankName,
            'accountName' => $accountName,
            'accountNo' => $accountNo,
            'companyName' => $companyName,
            'dateFiled' => $dateFiled,
            'address' => $address,
            'email' => $email,
            'websiteURL' => $websiteURL,
            'faxNo' => $faxNo,
            'telephoneNo' => $telephoneNo,
            'mobileNo' => $mobileNo,
            'dateEstablished' => $dateEstablished,
            'tinNo' => $tinNo,
            'vatNo' => $vatNo,
            'contactPerson' => $contactPerson,
            'natureBusiness' => $natureBusiness,
            'natureBusinessOthers' => $natureBusinessOthers,
            'deliveryVehicleNo' => $deliveryVehicleNo,
            'productLines' => $productLines,
            'creditAccomodation' => $creditAccomodation,
            'attachment' => $attachments,
            'attachmentOthers' => $attachmentOthers,
            'attachment1' => $attachment1,
            'attachment2' => $attachment2,
            'attachment3' => $attachment3,
            'attachment4' => $attachment4,
            'attachment5' => $attachment5,
            'attachment6' => $attachment6,
            'attachment7' => $attachment7,
        ]);
    }

    public function storeSupplier(Request $request) {
        $classification = $request->classification;
        $isActive = $request->is_active;
        $bankName = $request->bank_name;
        $accountName = $request->account_name;
        $accountNo = $request->account_no;
        $companyName = $request->company_name;
        $dateFiled = $request->date_filed;
        $address = $request->address;
        $email = $request->email;
        $websiteURL = $request->website_url;
        $faxNo = $request->fax_no;
        $telephoneNo = $request->telephone_no;
        $mobileNo = $request->mobile_no;
        $dateEstablished = $request->date_established;
        $tinNo = $request->tin_no;
        $vatNo = $request->vat_no;
        $contactPerson = $request->contact_person;
        $natureBusiness = $request->nature_business;
        $natureBusinessOthers = $request->nature_business_others;
        $deliveryVehicleNo = $request->delivery_vehicle_no;
        $productLines = $request->product_lines;
        $creditAccomodation = $request->credit_accomodation;
        $attachments = $request->attachment;
        $attachmentOthers = $request->attachment_others;

        try {
            if (!$this->checkDuplication('Supplier', $companyName)) {
                $instanceSuplier = new Supplier;
                $instanceSuplier->classification = $classification;
                $instanceSuplier->is_active = $isActive;
                $instanceSuplier->bank_name = $bankName;
                $instanceSuplier->account_name = $accountName;
                $instanceSuplier->account_no = $accountNo;
                $instanceSuplier->company_name = $companyName;
                $instanceSuplier->date_filed = $dateFiled;
                $instanceSuplier->address = $address;
                $instanceSuplier->email = $email;
                $instanceSuplier->website_url = $websiteURL;
                $instanceSuplier->fax_no = $faxNo;
                $instanceSuplier->telephone_no = $telephoneNo;
                $instanceSuplier->mobile_no = $mobileNo;
                $instanceSuplier->date_established = $dateEstablished;
                $instanceSuplier->tin_no = $tinNo;
                $instanceSuplier->vat_no = $vatNo;
                $instanceSuplier->contact_person = $contactPerson;
                $instanceSuplier->nature_business = $natureBusiness;
                $instanceSuplier->nature_business_others = $natureBusinessOthers;
                $instanceSuplier->delivery_vehicle_no = $deliveryVehicleNo;
                $instanceSuplier->product_lines = $productLines;
                $instanceSuplier->credit_accomodation = $creditAccomodation;
                $instanceSuplier->attachment = $attachments;
                $instanceSuplier->attachment_others = $attachmentOthers;
                $instanceSuplier->save();

                $msg = "Supplier '$companyName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Supplier '$companyName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateSupplier(Request $request, $id) {
        $classification = $request->classification;
        $isActive = $request->is_active;
        $bankName = $request->bank_name;
        $accountName = $request->account_name;
        $accountNo = $request->account_no;
        $companyName = $request->company_name;
        $dateFiled = $request->date_filed;
        $address = $request->address;
        $email = $request->email;
        $websiteURL = $request->website_url;
        $faxNo = $request->fax_no;
        $telephoneNo = $request->telephone_no;
        $mobileNo = $request->mobile_no;
        $dateEstablished = $request->date_established;
        $tinNo = $request->tin_no;
        $vatNo = $request->vat_no;
        $contactPerson = $request->contact_person;
        $natureBusiness = $request->nature_business;
        $natureBusinessOthers = $request->nature_business_others;
        $deliveryVehicleNo = $request->delivery_vehicle_no;
        $productLines = $request->product_lines;
        $creditAccomodation = $request->credit_accomodation;
        $attachments = $request->attachment;
        $attachmentOthers = $request->attachment_others;

        try {
            $instanceSuplier = Supplier::find($id);
            $instanceSuplier->classification = $classification;
            $instanceSuplier->is_active = $isActive;
            $instanceSuplier->bank_name = $bankName;
            $instanceSuplier->account_name = $accountName;
            $instanceSuplier->account_no = $accountNo;
            $instanceSuplier->company_name = $companyName;
            $instanceSuplier->date_filed = $dateFiled;
            $instanceSuplier->address = $address;
            $instanceSuplier->email = $email;
            $instanceSuplier->website_url = $websiteURL;
            $instanceSuplier->fax_no = $faxNo;
            $instanceSuplier->telephone_no = $telephoneNo;
            $instanceSuplier->mobile_no = $mobileNo;
            $instanceSuplier->date_established = $dateEstablished;
            $instanceSuplier->tin_no = $tinNo;
            $instanceSuplier->vat_no = $vatNo;
            $instanceSuplier->contact_person = $contactPerson;
            $instanceSuplier->nature_business = $natureBusiness;
            $instanceSuplier->nature_business_others = $natureBusinessOthers;
            $instanceSuplier->delivery_vehicle_no = $deliveryVehicleNo;
            $instanceSuplier->product_lines = $productLines;
            $instanceSuplier->credit_accomodation = $creditAccomodation;
            $instanceSuplier->attachment = $attachments;
            $instanceSuplier->attachment_others = $attachmentOthers;
            $instanceSuplier->save();

            $msg = "Supplier '$companyName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteSupplier($id) {
        try {
            $instanceSuplier = Supplier::find($id);
            $companyName = $instanceSuplier->company_name;
            $instanceSuplier->delete();

            $msg = "Supplier '$companyName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroySupplier($id) {
        try {
            $instanceSuplier = Supplier::find($id);
            $companyName = $instanceSuplier->company_name;
            $instanceSuplier->destroy();

            $msg = "Supplier '$companyName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  UACS Object Classifications Module
    **/
    public function indexUacsClassification(Request $request) {
        $uacsClassData = UacsObjectClassification::orderBy('classification_name')
                                                 ->get();

        return view('modules.library.uacs-classification.index', [
            'list' => $uacsClassData
        ]);
    }

    public function showCreateUacsClassification() {
        return view('modules.library.uacs-classification.create');
    }

    public function showEditUacsClassification($id) {
        $uacsClassData = UacsObjectClassification::find($id);
        $className = $uacsClassData->classification_name;

        return view('modules.library.uacs-classification.update', [
            'id' => $id,
            'classificationName' => $className
        ]);
    }

    public function storeUacsClassification(Request $request) {
        $className = $request->classification_name;

        try {
            if (!$this->checkDuplication('UacsObjectClassification', $className)) {
                $instanceUacsClass = new UacsObjectClassification;
                $instanceUacsClass->classification_name = $className;
                $instanceUacsClass->save();

                $msg = "UACS Object Classification '$className' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "UACS Object Classification '$className' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateUacsClassification(Request $request, $id) {
        $className = $request->classification_name;

        try {
            $instanceUacsClass = UacsObjectClassification::find($id);
            $instanceUacsClass->classification_name = $className;
            $instanceUacsClass->save();

            $msg = "UACS Object Classification '$className' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteUacsClassification($id) {
        try {
            $instanceUacsClass = UacsObjectClassification::find($id);
            $className = $instanceUacsClass->classification_name;
            $instanceUacsClass->delete();

            $msg = "UACS Object Classification '$className' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyUacsClassification($id) {
        try {
            $instanceUacsClass = UacsObjectClassification::find($id);
            $className = $instanceUacsClass->classification_name;
            $instanceUacsClass->destroy();

            $msg = "UACS Object Classification '$className' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  UACS Object Codes Module
    **/
    public function indexUacsObjCode(Request $request) {
        $_uacsObjCodeDat = UacsObjectCode::orderBy('uacs_code')
                                         ->get();
        $uacsObjCodeDat = [];

        foreach ($_uacsObjCodeDat as $uacs) {
            $uacsClassDat = UacsObjectClassification::find($uacs->classification_id);
            $uacsAccTitles = explode('::', $uacs->account_title);

            $uacs->classification = $uacsClassDat->classification_name;

            if (count($uacsAccTitles) > 1) {
                $uacs->account_title = $uacsAccTitles[1];
                $uacs->account_title_header = $uacsAccTitles[0];
                $keyString = strtolower(preg_replace('/\s+/', '', $uacsAccTitles[0]));
                $uacsObjCodeDat[$keyString][] = $uacs;
            } else {
                $uacsObjCodeDat[] = $uacs;
            }
        }

        return view('modules.library.uacs-code.index', [
            'list' => $uacsObjCodeDat
        ]);
    }

    public function showCreateUacsObjCode() {
        $uacsClassifications = DB::table('mooe_classifications')
                                 ->orderBy('classification_name')
                                 ->get();

        return view('modules.library.uacs-code.create', [
            'uacsClassifications' => $uacsClassifications,
        ]);
    }

    public function showEditUacsObjCode($id) {
        $uacsClassifications = DB::table('mooe_classifications')
                                 ->orderBy('classification_name')
                                 ->get();
        $uacsObjCodeDat = UacsObjectCode::find($id);
        $uacsClassification = $uacsObjCodeDat->classification_id;
        $accountTitles = explode('::', $uacsObjCodeDat->account_title);
        $uacsCode = $uacsObjCodeDat->uacs_code;
        $description = $uacsObjCodeDat->description;

        if (count($accountTitles) > 1) {
            $accountTitleHeader = $accountTitles[0];
            $accountTitle = $accountTitles[1];
        } else {
            $accountTitleHeader = '';
            $accountTitle = $accountTitles[0];
        }

        return view('modules.library.uacs-code.update', [
            'id' => $id,
            'uacsClassifications' => $uacsClassifications,
            'uacsClassification' => $uacsClassification,
            'accountTitleHeader' => $accountTitleHeader,
            'accountTitle' => $accountTitle,
            'uacsCode' => $uacsCode,
            'description' => $description
        ]);
    }

    public function storeUacsObjCode(Request $request) {
        $classification = $request->classification;
        $_accountTitleHeader = $request->account_title_header;
        $_accountTitle = $request->account_title;
        $accountTitle = !empty($_accountTitleHeader) ?
                        "$_accountTitleHeader::$_accountTitle" :
                         $_accountTitle;
        $uacsCode = $request->uacs_code;
        $description = $request->description;

        try {
            if (!$this->checkDuplication('UacsObjectCode', $uacsCode)) {
                $instanceUacsCode = new UacsObjectCode;
                $instanceUacsCode->classification_id = $classification;
                $instanceUacsCode->account_title = $accountTitle;
                $instanceUacsCode->uacs_code = $uacsCode;
                $instanceUacsCode->description = $description;
                $instanceUacsCode->order_no = 0;
                $instanceUacsCode->save();

                $msg = "UACS Object Code '$uacsCode - $accountTitle' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "UACS Object Code '$uacsCode - $accountTitle' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateUacsObjCode(Request $request, $id) {
        $classification = $request->classification;
        $_accountTitleHeader = $request->account_title_header;
        $_accountTitle = $request->account_title;
        $accountTitle = !empty($_accountTitleHeader) ?
                        "$_accountTitleHeader::$_accountTitle" :
                         $_accountTitle;
        $uacsCode = $request->uacs_code;
        $description = $request->description;

        try {
            $instanceUacsCode = UacsObjectCode::find($id);
            $instanceUacsCode->classification_id = $classification;
            $instanceUacsCode->account_title = $accountTitle;
            $instanceUacsCode->uacs_code = $uacsCode;
            $instanceUacsCode->description = $description;
            $instanceUacsCode->save();

            $msg = "UACS Object Code '$uacsCode - $accountTitle' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteUacsObjCode($id) {
        try {
            $instanceUacsCode = UacsObjectCode::find($id);
            $uacsCode = $instanceUacsCode->uacs_code;
            $accountTitle = $instanceUacsCode->account_title;
            $instanceUacsCode->delete();

            $msg = "UACS Object Code '$uacsCode - $accountTitle' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyUacsObjCode($id) {
        try {
            $instanceUacsCode = UacsObjectCode::find($id);
            $uacsCode = $instanceUacsCode->uacs_code;
            $accountTitle = $instanceUacsCode->account_title;
            $instanceUacsCode->destroy();

            $msg = "UACS Object Code '$uacsCode - $accountTitle' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Item Unit Issue Module
    **/
    public function indexUnitissue(Request $request) {
        $unitIssueData = ItemUnitIssue::orderBy('unit_name')
                                      ->get();

        return view('modules.library.unit-issue.index', [
            'list' => $unitIssueData
        ]);
    }

    public function showCreateUnitissue() {
        return view('modules.library.unit-issue.create');
    }

    public function showEditUnitissue($id) {
        $unitIssueData = ItemUnitIssue::find($id);
        $unit = $unitIssueData->unit_name;

        return view('modules.library.unit-issue.update', [
            'id' => $id,
            'unit' => $unit
        ]);
    }

    public function storeUnitissue(Request $request) {
        $unitName = $request->unit_name;

        try {
            if (!$this->checkDuplication('ItemUnitIssue', $unitName)) {
                $instanceUnitIssue = new ItemUnitIssue;
                $instanceUnitIssue->unit_name = $unitName;
                $instanceUnitIssue->save();

                $msg = "Unit of issue '$unitName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Unit of issue '$unitName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateUnitissue(Request $request, $id) {
        $unitName = $request->unit_name;

        try {
            $instanceUnitIssue = ItemUnitIssue::find($id);
            $instanceUnitIssue->unit_name = $unitName;
            $instanceUnitIssue->save();

            $msg = "Unit of issue '$unitName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteUnitissue($id) {
        try {
            $instanceUnitIssue = ItemUnitIssue::find($id);
            $unitName = $instanceUnitIssue->unit_name;
            $instanceUnitIssue->delete();

            $msg = "Unit of issue '$unitName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyUnitissue($id) {
        try {
            $instanceUnitIssue = ItemUnitIssue::find($id);
            $unitName = $instanceUnitIssue->unit_name;
            $instanceUnitIssue->destroy();

            $msg = "Unit of issue '$unitName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Procurement Mode Module
    **/
    public function indexProcurementMode(Request $request) {
        $procModeData = ProcurementMode::orderBy('mode_name')
                                       ->get();

        return view('modules.library.procurement-mode.index', [
            'list' => $procModeData
        ]);
    }

    public function showCreateProcurementMode() {
        return view('modules.library.procurement-mode.create');
    }

    public function showEditProcurementMode($id) {
        $procModeData = ProcurementMode::find($id);
        $mode = $procModeData->mode_name;

        return view('modules.library.procurement-mode.update', [
            'id' => $id,
            'mode' => $mode
        ]);
    }

    public function storeProcurementMode(Request $request) {
        $modeName = $request->mode_name;

        try {
            if (!$this->checkDuplication('ProcurementMode', $modeName)) {
                $instanceProcMode = new ProcurementMode;
                $instanceProcMode->mode_name = $modeName;
                $instanceProcMode->save();

                $msg = "Procurement mode '$modeName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Procurement mode '$modeName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateProcurementMode(Request $request, $id) {
        $modeName = $request->mode_name;

        try {
            $instanceProcMode = ProcurementMode::find($id);
            $instanceProcMode->mode_name = $modeName;
            $instanceProcMode->save();

            $msg = "Procurement mode '$modeName' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteProcurementMode($id) {
        try {
            $instanceProcMode = ProcurementMode::find($id);
            $modeName = $instanceProcMode->mode_name;
            $instanceProcMode->delete();

            $msg = "Procurement mode '$modeName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyProcurementMode($id) {
        try {
            $instanceProcMode = ProcurementMode::find($id);
            $modeName = $instanceProcMode->mode_name;
            $instanceProcMode->destroy();

            $msg = "Procurement mode '$modeName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Inventory Stock Classification Module
    **/
    public function indexInventoryClassification(Request $request) {
        $inventoryData = InventoryClassification::orderBy('classification_name')
                                                ->get();

        return view('modules.library.inventory-classification.index', [
            'list' => $inventoryData
        ]);
    }

    public function showCreateInventoryClassification() {
        return view('modules.library.inventory-classification.create');
    }

    public function showEditInventoryClassification($id) {
        $inventoryData = InventoryClassification::find($id);
        $className = $inventoryData->classification_name;
        $abbrv = $inventoryData->abbrv;

        return view('modules.library.inventory-classification.update', [
            'id' => $id,
            'classification' => $className,
            'abbrv' => $abbrv
        ]);
    }

    public function storeInventoryClassification(Request $request) {
        $className = $request->classification_name;
        $abbrv = $request->abbrv;

        try {
            if (!$this->checkDuplication('InventoryClassification', $className)) {
                $instanceInvClass = new InventoryClassification;
                $instanceInvClass->classification_name = $className;
                $instanceInvClass->abbrv = $abbrv;
                $instanceInvClass->save();

                $msg = "Inventory classification '$className' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Inventory classification '$className' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateInventoryClassification(Request $request, $id) {
        $className = $request->classification_name;
        $abbrv = $request->abbrv;

        try {
            $instanceInvClass = InventoryClassification::find($id);
            $instanceInvClass->classification_name = $className;
            $instanceInvClass->abbrv = $abbrv;
            $instanceInvClass->save();

            $msg = "Inventory classification '$className' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteInventoryClassification($id) {
        try {
            $instanceInvClass = InventoryClassification::find($id);
            $className = $instanceInvClass->classification_name;
            $instanceInvClass->delete();

            $msg = "Inventory classification '$className' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyInventoryClassification($id) {
        try {
            $instanceInvClass = InventoryClassification::find($id);
            $className = $instanceInvClass->classification_name;
            $instanceInvClass->destroy();

            $msg = "Inventory classification '$className' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Paper Size Module
    **/
    public function indexPaperSize(Request $request) {
        $paperSizeData = PaperSize::orderBy('paper_type')
                                  ->get();

        return view('modules.library.paper-size.index', [
            'list' => $paperSizeData
        ]);
    }

    public function showCreatePaperSize() {
        return view('modules.library.paper-size.create');
    }

    public function showEditPaperSize($id) {
        $paperSizeData = PaperSize::find($id);
        $paperType = $paperSizeData->paper_type;
        $unit = $paperSizeData->unit;
        $width = $paperSizeData->width;
        $height = $paperSizeData->height;

        return view('modules.library.paper-size.update', [
            'id' => $id,
            'paperType' => $paperType,
            'unit' => $unit,
            'width' => $width,
            'height' => $height
        ]);
    }

    public function storePaperSize(Request $request) {
        $paperType = $request->paper_type;
        $unit = $request->unit;
        $width = $request->width;
        $height = $request->height;

        try {
            if (!$this->checkDuplication('PaperSize', $paperType)) {
                $instancePaperSize = new PaperSize;
                $instancePaperSize->paper_type = $paperType;
                $instancePaperSize->unit = $unit;
                $instancePaperSize->width = $width;
                $instancePaperSize->height = $height;
                $instancePaperSize->save();

                $msg = "Paper size '$paperType' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Paper size '$paperType' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updatePaperSize(Request $request, $id) {
        $paperType = $request->paper_type;
        $unit = $request->unit;
        $width = $request->width;
        $height = $request->height;

        try {
            $instancePaperSize = PaperSize::find($id);
            $instancePaperSize->paper_type = $paperType;
                $instancePaperSize->unit = $unit;
                $instancePaperSize->width = $width;
                $instancePaperSize->height = $height;
            $instancePaperSize->save();

            $msg = "Paper size '$paperType' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deletePaperSize($id) {
        try {
            $instancePaperSize = PaperSize::find($id);
            $paperType = $instancePaperSize->paper_type;
            $instancePaperSize->delete();

            $msg = "Paper size '$paperType' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyPaperSize($id) {
        try {
            $instancePaperSize = PaperSize::find($id);
            $paperType = $instancePaperSize->paper_type;
            $instancePaperSize->destroy();

            $msg = "Paper size '$paperType' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function checkDuplication($model, $data) {
        switch ($model) {
            case 'AgencyLGU':
                $dataCount = AgencyLGU::where('agency_name', $data)
                                      ->orWhere('agency_name', strtolower($data))
                                      ->orWhere('agency_name', strtoupper($data))
                                      ->count();
                break;
            case 'IndustrySector':
                $dataCount = IndustrySector::where('sector_name', $data)
                                      ->orWhere('sector_name', strtolower($data))
                                      ->orWhere('sector_name', strtoupper($data))
                                      ->count();
                break;
            case 'EmpDivision':
                $dataCount = EmpDivision::where('division_name', $data)
                                        ->orWhere('division_name', strtolower($data))
                                        ->orWhere('division_name', strtoupper($data))
                                        ->count();
                break;
            case 'ItemClassification':
                $dataCount = ItemClassification::where('classification_name', $data)
                                               ->orWhere('classification_name', strtolower($data))
                                               ->orWhere('classification_name', strtoupper($data))
                                               ->count();
                break;
            case 'MfoPap':
                $dataCount = MfoPap::where('code', $data)
                                   ->orWhere('description', strtolower($data))
                                   ->count();
                break;
            case 'ProcurementMode':
                $dataCount = ProcurementMode::where('mode_name', $data)
                                            ->orWhere('mode_name', strtolower($data))
                                            ->orWhere('mode_name', strtoupper($data))
                                            ->count();
                break;
            case 'FundingSource':
                $dataCount = FundingProject::where('project_title', $data)
                                          ->orWhere('project_title', strtolower($data))
                                          ->orWhere('project_title', strtoupper($data))
                                          ->count();
                break;
            case 'Signatory':
                $dataCount = Signatory::where('emp_id', $data)
                                      ->count();
                break;
            case 'SupplierClassification':
                $dataCount = SupplierClassification::where('classification_name', $data)
                                                   ->orWhere('classification_name', strtolower($data))
                                                   ->orWhere('classification_name', strtoupper($data))
                                                   ->count();
                break;
            case 'Supplier':
                $dataCount = Supplier::where('company_name', $data)
                                     ->orWhere('company_name', strtolower($data))
                                     ->orWhere('company_name', strtoupper($data))
                                     ->count();
                break;
            case 'UacsObjectClassification':
                $dataCount = UacsObjectClassification::where('classification_name', $data)
                                          ->orWhere('classification_name', strtolower($data))
                                          ->orWhere('classification_name', strtoupper($data))
                                          ->count();
                break;
            case 'UacsObjectCode':
                $dataCount = UacsObjectCode::where('uacs_code', $data)
                                          ->orWhere('uacs_code', strtolower($data))
                                          ->orWhere('uacs_code', strtoupper($data))
                                          ->count();
                break;
            case 'ItemUnitIssue':
                $dataCount = ItemUnitIssue::where('unit_name', $data)
                                          ->orWhere('unit_name', strtolower($data))
                                          ->orWhere('unit_name', strtoupper($data))
                                          ->count();
                break;
            default:
                $dataCount = 0;
                break;
        }

        return ($dataCount > 0) ? 1 : 0;;
    }

    public function getListAgencyLGU(Request $request) {
        $keyword = trim($request->search);
        $agencyLGUData = AgencyLGU::select('id', 'agency_name');

        if ($keyword) {
            $agencyLGUData = $agencyLGUData->where(function($qry) use ($keyword) {
                $qry->where('agency_name', 'like', "%$keyword%");
            });
        }

        $agencyLGUData = $agencyLGUData->orderBy('agency_name')
                                       ->get();

        return response()->json($agencyLGUData);
    }

    public function getListIndustrySector(Request $request) {
        $keyword = trim($request->search);
        $industrySecData = IndustrySector::select('id', 'sector_name');

        if ($keyword) {
            $industrySecData = $industrySecData->where(function($qry) use ($keyword) {
                $qry->where('sector_name', 'like', "%$keyword%");
            });
        }

        $industrySecData = $industrySecData->orderBy('sector_name')
                                       ->get();

        return response()->json($industrySecData);
    }

    public function getListMonitoringOffice(Request $request) {
        $keyword = trim($request->search);
        $monitoringData = MonitoringOffice::select('id', 'office_name');

        if ($keyword) {
            $monitoringData = $monitoringData->where(function($qry) use ($keyword) {
                $qry->where('office_name', 'like', "%$keyword%");
            });
        }

        $monitoringData = $monitoringData->orderBy('office_name')
                                       ->get();

        return response()->json($monitoringData);
    }
}
