<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseRequest;
use App\Abstracts;
use App\AbstractItem;

use App\User;
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
     *  Project Module
    **/
    public function indexProject(Request $request) {
        $projectData = FundingProject::orderBy('project_title')
                                    ->get();

        return view('modules.library.project.index', [
            'list' => $projectData
        ]);
    }

    public function showCreateProject() {
        $industries = IndustrySector::orderBy('sector_name')->get();
        $municipalities = Municipality::orderBy('municipality_name')->get();
        $empUnits = EmpUnit::orderBy('unit_name')->get();
        $agencies = AgencyLGU::orderBy('agency_name')->get();
        $monitoringOffices = MonitoringOffice::orderBy('office_name')->get();

        return view('modules.library.project.create', compact(
            'industries',
            'municipalities',
            'empUnits',
            'agencies',
            'monitoringOffices',
        ));
    }

    public function showEditProject($id) {
        $fundingData = FundingProject::find($id);
        $funding = $fundingData->project_title;

        return view('modules.library.project.update', [
            'id' => $id,
            'funding' => $funding
        ]);
    }

    public function storeProject(Request $request) {
        $referenceCode = $request->reference_code;
        $sourceName = $request->source_name;

        try {
            if (!$this->checkDuplication('FundingSource', $sourceName)) {
                $instanceFundSrc = new FundingProject;
                $instanceFundSrc->project_title = $sourceName;
                $instanceFundSrc->save();

                $msg = "Funding source '$sourceName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Funding source '$sourceName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateProject(Request $request, $id) {
        $referenceCode = $request->reference_code;
        $sourceName = $request->source_name;

        try {
            $instanceFundSrc = FundingProject::find($id);
            $instanceFundSrc->project_title = $sourceName;
            $instanceFundSrc->save();

            $msg = "Funding source '$sourceName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteProject($id) {
        try {
            $instanceFundSrc = FundingProject::find($id);
            $sourceName = $instanceFundSrc->source_name;
            $instanceFundSrc->delete();

            $msg = "Funding source '$sourceName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyProject($id) {
        try {
            $instanceFundSrc = FundingProject::find($id);
            $sourceName = $instanceFundSrc->source_name;
            $instanceFundSrc->destroy();

            $msg = "Funding source '$sourceName' successfully destroyed.";
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

    public function getListAgencyLGU() {
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
}
