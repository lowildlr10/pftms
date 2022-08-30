<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\AbstractQuotation;
use App\Models\AbstractQuotationItem;
use App\Models\PurchaseJobOrder;
use App\Models\PurchaseJobOrderItem;
use App\Models\ObligationRequestStatus;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\InventoryStock;

use App\Models\EmpAccount as User;
use App\Models\EmpUnit;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\Signatory;
use App\Models\ItemUnitIssue;
use App\Models\FundingProject;
use App\Models\MooeAccountTitle;
use App\Models\MfoPap;
use App\Models\OrsBursUacsItem;
use App\Models\CustomPayee;
use Carbon\Carbon;
use Auth;
use DB;

use App\Plugins\Notification as Notif;

class ObligationRequestStatusController extends Controller
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

    public function indexProc(Request $request) {
        $data = $this->getIndexData($request, 'procurement');

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAccountant = Auth::user()->hasAccountantRole();

        // Get module access
        $module = 'proc_ors_burs';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedObligate = Auth::user()->getModuleAccess($module, 'obligate');
        $isAllowedPO = Auth::user()->getModuleAccess('proc_po_jo', 'is_allowed');

        return view('modules.procurement.ors-burs.index', [
            'list' => $data->ors_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedObligate' => $isAllowedObligate,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedPO' => $isAllowedPO,
            'roleHasOrdinary' => $roleHasOrdinary,
            'roleHasBudget' => $roleHasBudget,
            'roleHasAccountant' => $roleHasAccountant,
        ]);
    }

    public function indexCA(Request $request) {
        $data = $this->getIndexData($request, 'cashadvance');

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();

        // Get module access
        $module = 'ca_ors_burs';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedObligate = Auth::user()->getModuleAccess($module, 'obligate');
        $isAllowedDV = Auth::user()->getModuleAccess('ca_dv', 'create');
        $isAllowedDVCreate = Auth::user()->getModuleAccess('ca_dv', 'is_allowed');

        return view('modules.voucher.ors-burs.index', [
            'list' => $data->ors_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'isAllowedObligate' => $isAllowedObligate,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedDV' => $isAllowedDV,
            'isAllowedDVCreate' => $isAllowedDVCreate,
            'roleHasOrdinary' => $roleHasOrdinary,
        ]);
    }

    private function getIndexData($request, $type) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

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

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();

        if ($type == 'procurement') {
            $userIDs = Auth::user()->getGroupHeads();
            $empUnitDat = EmpUnit::has('unithead')->find(Auth::user()->unit);
            $userIDs[] = Auth::user()->id;

            if ($empUnitDat && $empUnitDat->unithead) {
                $userIDs[] = $empUnitDat->unithead->id;
            }

            $orsData = PurchaseJobOrder::with('bidpayee')->whereHas('ors', function($query) {
                $query->whereNull('deleted_at');
            })->whereNull('date_cancelled');

            if ($roleHasOrdinary && (!$roleHasDeveloper || !$roleHasRD || !$roleHasPropertySupply ||
                !$roleHasAccountant || !$roleHasBudget || !$roleHasPSTD)) {
                if (Auth::user()->emp_type == 'contractual') {
                    if (Auth::user()->getDivisionAccess()) {
                        $empDivisionAccess = Auth::user()->getDivisionAccess();
                    } else {
                        $empDivisionAccess = [Auth::user()->division];
                    }
                } else {
                    $empDivisionAccess = [Auth::user()->division];
                }
            } else {
                if ($roleHasPSTD) {
                    $empDivisionAccess = [Auth::user()->division];
                } else {
                    $empDivisionAccess = Auth::user()->getDivisionAccess();
                }
            }

            $orsData = $orsData->whereHas('pr', function($query)
                    use($empDivisionAccess, $roleHasOrdinary, $userIDs) {
                $query->whereIn('division', $empDivisionAccess)
                      ->whereNull('date_pr_cancelled');

                if ($roleHasOrdinary) {
                    if (Auth::user()->emp_type == 'contractual') {
                        $query->whereIn('requested_by', $userIDs);
                    } else {
                        $query->where('requested_by', Auth::user()->id);
                    }
                }
            });

            if (!empty($keyword)) {
                $orsData = $orsData->where(function($qry) use ($keyword) {
                    $qry->where('id', 'like', "%$keyword%")
                        ->orWhere('po_no', 'like', "%$keyword%")
                        ->orWhere('date_po', 'like', "%$keyword%")
                        ->orWhere('grand_total', 'like', "%$keyword%")
                        ->orWhere('document_type', 'like', "%$keyword%")
                        ->orWhereHas('stat', function($query) use ($keyword) {
                            $query->where('status_name', 'like', "%$keyword%");
                        })->orWhereHas('bidpayee', function($query) use ($keyword) {
                            $query->where('company_name', 'like', "%$keyword%")
                                ->orWhere('address', 'like', "%$keyword%");
                        })->orWhereHas('poitems', function($query) use ($keyword) {
                            $query->where('item_description', 'like', "%$keyword%");
                        })->orWhereHas('ors', function($query) use ($keyword) {
                            $query->where('id', 'like', "%$keyword%")
                                ->orWhere('particulars', 'like', "%$keyword%")
                                ->orWhere('document_type', 'like', "%$keyword%")
                                ->orWhere('transaction_type', 'like', "%$keyword%")
                                ->orWhere('serial_no', 'like', "%$keyword%")
                                ->orWhere('date_ors_burs', 'like', "%$keyword%")
                                ->orWhere('date_obligated', 'like', "%$keyword%")
                                ->orWhere('responsibility_center', 'like', "%$keyword%")
                                ->orWhere('uacs_object_code', 'like', "%$keyword%")
                                ->orWhere('amount', 'like', "%$keyword%")
                                ->orWhere('office', 'like', "%$keyword%")
                                ->orWhere('address', 'like', "%$keyword%")
                                ->orWhere('fund_cluster', 'like', "%$keyword%");
                        });
                });
            }

            $orsData = $orsData->sortable(['po_no' => 'desc'])->paginate(15);

            foreach ($orsData as $orsDat) {
                $orsDat->doc_status = $instanceDocLog->checkDocStatus($orsDat->ors['id']);
            }
        } else {
            $orsData = ObligationRequestStatus::whereNull('deleted_at')
                                              ->where('module_class', 2);

            if ($roleHasDeveloper || $roleHasBudget || $roleHasAccountant || $roleHasRD ||
                $roleHasARD) {
            } else {
                 $orsData = $orsData->where('payee', Auth::user()->id)
                                    ->orWhere('created_by', Auth::user()->id);
            }

            if (!empty($keyword)) {
                $orsData = $orsData->where(function($qry) use ($keyword) {
                    $qry->where('id', 'like', "%$keyword%")
                        ->orWhere('particulars', 'like', "%$keyword%")
                        ->orWhere('document_type', 'like', "%$keyword%")
                        ->orWhere('transaction_type', 'like', "%$keyword%")
                        ->orWhere('serial_no', 'like', "%$keyword%")
                        ->orWhere('date_ors_burs', 'like', "%$keyword%")
                        ->orWhere('date_obligated', 'like', "%$keyword%")
                        ->orWhere('responsibility_center', 'like', "%$keyword%")
                        ->orWhere('uacs_object_code', 'like', "%$keyword%")
                        ->orWhere('amount', 'like', "%$keyword%")
                        ->orWhere('office', 'like', "%$keyword%")
                        ->orWhere('address', 'like', "%$keyword%")
                        ->orWhere('fund_cluster', 'like', "%$keyword%")
                        ->orWhereHas('emppayee', function($query) use ($keyword) {
                            $query->where('firstname', 'like', "%$keyword%")
                                  ->orWhere('middlename', 'like', "%$keyword%")
                                  ->orWhere('lastname', 'like', "%$keyword%")
                                  ->orWhere('position', 'like', "%$keyword%");
                        })->orWhereHas('bidpayee', function($query) use ($keyword) {
                            $query->where('company_name', 'like', "%$keyword%");
                        })->orWhereHas('custompayee', function($query) use ($keyword) {
                            $query->where('payee_name', 'like', "%$keyword%");
                        });
                });
            }

            $orsData = $orsData->orderBy('created_at', 'desc')
                              ->orderBy('serial_no', 'desc')
                              ->sortable(['created_at' => 'desc', 'serial_no' => 'desc'])
                              ->paginate(15);

            foreach ($orsData as $orsDat) {
                $orsDat->doc_status = $instanceDocLog->checkDocStatus($orsDat->id);
                $orsDat->has_dv = DisbursementVoucher::where('ors_id', $orsDat->id)->count();
            }
        }

        return (object) [
            'keyword' => $keyword,
            'ors_data' => $orsData,
            'paper_sizes' => $paperSizes
        ];
    }

    /**
     * Store a newly created resource from PO/JO in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  uuid $poID
     * @return \Illuminate\Http\Response
     */
    public function storeORSFromPO(Request $request, $poID) {
        $orsDocumentType = $request->ors_document_type;

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;

            $instancePO = PurchaseJobOrder::find($poID);
            $poNo = $instancePO->po_no;
            $prID = $instancePO->pr_id;
            $documentType = $instancePO->document_type;
            $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
            $grandTotal = $instancePO->grand_total;

            if ($grandTotal > 0) {
                $instanceORS = ObligationRequestStatus::withTrashed()
                                                      ->where('po_no', $poNo)
                                                      ->first();
                $prData = PurchaseRequest::find($prID);
                $project = $prData->funding_source;

                if (!$instanceORS) {
                    $instanceORS = new ObligationRequestStatus;
                    $instanceORS->pr_id = $prID;
                    $instanceORS->po_no = $poNo;
                    $instanceORS->responsibility_center = "19 001 03000 14";
                    $instanceORS->particulars = "To obligate...";
                    //$instanceORS->mfo_pap = "3-Regional Office\nA.III.c.1\nA.III.b.1\nA.III.c.2";
                    $instanceORS->payee = $instancePO->awarded_to;
                    $instanceORS->amount = $instancePO->grand_total;
                    $instanceORS->module_class = 3;
                    $instanceORS->funding_source = $project;
                    $instanceORS->save();
                } else {
                    $instanceDocLog->logDocument($instanceORS->id, Auth::user()->id, NULL, '-');
                    $instanceORS->date_obligated = NULL;
                    $instanceORS->obligated_by = NULL;
                    $instanceORS->save();
                    ObligationRequestStatus::withTrashed()->where('po_no', $poNo)->restore();
                }

                $instancePO->for_approval = 'y';
                $instancePO->with_ors_burs = 'y';

                if ($instanceORS->date_obligated) {
                    $instancePO->status = 7;
                }

                $instancePO->save();

                $instanceNotif->notifyCreatedORS($poNo);

                $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
                $msg = "$documentType '$poNo' successfully created the ORS/BURS document.";
                Auth::user()->log($request, $msg);
                return redirect()->route('proc-ors-burs', ['keyword' => $poNo])
                                 ->with('success', $msg);
            } else {
                $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
                $msg = "$documentType '$poNo' should have a grand total greater than 0 and
                        no existing ORS/BURS document.";
                Auth::user()->log($request, $msg);
                return redirect()->route('po-jo', ['keyword' => $poNo])
                                 ->with('warning', $msg);
            }

            if ($ountORS > 0) {
                ObligationRequestStatus::where('po_no', $poNo)->restore();
            }
        } catch (\Throwable $th) {
            $instanceORS = PurchaseJobOrder::find($poID);
            $poNo = $instanceORS->po_no;

            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $poNo])
                             ->with('failed', $msg);
        }
    }

    /**
     * Show the form for creatingr the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();

        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];
        $payees = $roleHasOrdinary ?
                User::where('id', Auth::user()->id)
                    ->orderBy('firstname')
                    ->get() :
                User::where('is_active', 'y')
                    ->whereIn('division', $empDivisionAccess)
                    ->orderBy('firstname')->get();
        $mooeTitles = MooeAccountTitle::orderBy('order_no')->get();
        $mfoPAPs = MfoPap::orderBy('code')->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        $projDat = new FundingProject;
        $_projects = FundingProject::orderBy('project_title');
        $projects = [];
        $tempFundSrcs = [];

        if (!$roleHasBudget && !$roleHasAdministrator && !$roleHasDeveloper) {
            $projectIDs = $projDat->getAccessibleProjects();

            $_projects = $_projects->where(function($qry) use ($projectIDs) {
                $qry->whereIn('id', $projectIDs);
            });
        }

        $_projects = $_projects->get();

        foreach ($_projects as $proj) {
            $directory = $proj->directory ? implode(' &rarr; ', unserialize($proj->directory)) : NULL;
            $projTitle = (strlen($proj->project_title) > 70 ?
                         substr($proj->project_title, 0, 70).'...' :
                         $proj->project_title);
            $projTitle = strtoupper($projTitle);
            $title = $directory ? "$directory &rarr; $projTitle" : $projTitle;

            if ($directory) {
                $tempFundSrcs['with_dir'][] = (object) [
                    'id' => $proj->id,
                    'project_title' => $title,
                ];
            } else {
                $tempFundSrcs['no_dir'][] = (object) [
                    'id' => $proj->id,
                    'project_title' => $title,
                ];
            }

            if (isset($tempFundSrcs['with_dir'])) {
                sort($tempFundSrcs['with_dir']);
            }
        }

        if (isset($tempFundSrcs['with_dir'])) {
            foreach ($tempFundSrcs['with_dir'] as $proj) {
                $projects[] = $proj;
            }
        }

        if (isset($tempFundSrcs['no_dir'])) {
            foreach ($tempFundSrcs['no_dir'] as $proj) {
                $projects[] = $proj;
            }
        }

        return view('modules.voucher.ors-burs.create', compact(
            'signatories', 'payees', 'projects', 'mooeTitles', 'mfoPAPs'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $documentType = $request->document_type;
        $transactionType = !empty($request->transaction_type) ? $request->transaction_type: 'others';
        $serialNo = $request->serial_no;
        $dateORS = !empty($request->date_ors_burs) ? $request->date_ors_burs: NULL;
        $fundCluster = $request->fund_cluster;
        $payee = $request->payee;
        $office = $request->office;
        $address = $request->address;
        $responsibilityCenter = $request->responsibility_center;
        $particulars = $request->particulars;
        $mfoPAP = $mfoPAP = $request->mfo_pap ? serialize($request->mfo_pap) : serialize([]);
        //$uacsObjectCode = $request->uacs_object_code ? serialize($request->uacs_object_code) : serialize([]);
        $project = $request->funding_source;
        $priorYear = $request->prior_year ? $request->prior_year : 0.00;
        $continuing = $request->continuing ? $request->continuing : 0.00;
        $current = $request->current ? $request->current : 0.00;
        $amount = $request->amount;
        $sigCertified1 = !empty($request->sig_certified_1) ? $request->sig_certified_1: NULL;
        $sigCertified2 = !empty($request->sig_certified_2) ? $request->sig_certified_2: NULL;
        $dateCertified1 = !empty($request->date_certified_1) ? $request->date_certified_1: NULL;
        $dateCertified2 = !empty($request->date_certified_2) ? $request->date_certified_2: NULL;

        $uacsDescriptions = $request->uacs_description;
        $uacsAmounts = $request->uacs_amount;

        $uacsObjectCode = serialize(explode(',', $request->uacs_object_code));

        $routeName = 'ca-ors-burs';

        try {
            $empData = User::where('id', $payee)->count();
            $supplierData = Supplier::where('id', $payee)->count();
            $customPayeeData = CustomPayee::where('id', $payee)
                                         ->orWhere('payee_name', $payee)
                                         ->count();

            if (!$empData && !$supplierData && !$customPayeeData) {
                $instancePayee = CustomPayee::create([
                    'payee_name' => $payee
                ]);

                $payee = $instancePayee->id->string;
            }

            $instanceORS = new ObligationRequestStatus;
            $instanceORS->document_type = $documentType;
            $instanceORS->transaction_type = $transactionType;
            $instanceORS->serial_no = $serialNo;
            $instanceORS->date_ors_burs = $dateORS;
            $instanceORS->fund_cluster = $fundCluster;
            $instanceORS->payee = $payee;
            $instanceORS->office = $office;
            $instanceORS->address = $address;
            $instanceORS->responsibility_center = $responsibilityCenter;
            $instanceORS->particulars = $particulars;
            $instanceORS->mfo_pap = $mfoPAP;
            $instanceORS->uacs_object_code = $uacsObjectCode;
            $instanceORS->sig_certified_1 = $sigCertified1;
            $instanceORS->sig_certified_2 = $sigCertified2;
            $instanceORS->date_certified_1 = $dateCertified1;
            $instanceORS->date_certified_2 = $dateCertified2;
            $instanceORS->funding_source = $project;
            $instanceORS->prior_year = $priorYear;
            $instanceORS->continuing = $continuing;
            $instanceORS->current = $current;
            $instanceORS->amount = $amount;
            $instanceORS->module_class = 2;
            $instanceORS->created_by = Auth::user()->id;
            $instanceORS->save();

            $orsID = $instanceORS->id->string;

            $this->updateUacsItems($request, $orsID);

            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                            'Budget Utilization Request & Status';

            $msg = "$documentType successfully created.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName)
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $uacsData = $this->showUacsItemData($id);

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();
        $roleHasPropertySupply = Auth::user()->hasPropertySupplyRole();

        $payees = [];

        $orsData = ObligationRequestStatus::find($id);
        $isObligated = !empty($orsData->date_obligated) ? 1 : 0;
        $moduleClass = $orsData->module_class;
        $documentType = $orsData->document_type;
        $serialNo = $orsData->serial_no;
        $dateORS = $orsData->date_ors_burs;
        $fundCluster = $orsData->fund_cluster;
        $payee = $orsData->payee;
        $office = $orsData->office;
        $address = $orsData->address;
        $responsibilityCenter = $orsData->responsibility_center;
        $particulars = $orsData->particulars;
        $dvParticulars = $orsData->particulars;
        $mfoPAP = $orsData->mfo_pap ?
                  unserialize($orsData->mfo_pap) :
                  [];
        $uacsObjectCode = $orsData->uacs_object_code ?
                          unserialize($orsData->uacs_object_code) :
                          [];
        $priorYear = $orsData->prior_year;
        $continuing = $orsData->continuing;
        $current = $orsData->current;
        $amount = $orsData->amount;
        $sigCertified1 = $orsData->sig_certified_1;
        $sigCertified2 = $orsData->sig_certified_2;
        $dateCertified1 = $orsData->date_certified_1;
        $dateCertified2 = $orsData->date_certified_2;
        $transactionType = $orsData->transaction_type;
        $project = $orsData->funding_source;
        $mooeTitles = MooeAccountTitle::orderBy('order_no')->get();
        $mfoPAPs = MfoPap::orderBy('code')->get();
        $uacsItems = DB::table('ors_burs_uacs_items as uacs_item')
                       ->select(
                           'uacs_item.id',
                           'uacs_item.uacs_id',
                           'uacs_item.description',
                           'uacs_item.amount',
                           'mooe.uacs_code'
                        )
                       ->join('mooe_account_titles as mooe', 'mooe.id', '=', 'uacs_item.uacs_id')
                       ->where('uacs_item.ors_id', $id)
                       ->orderBy('mooe.uacs_code')
                       ->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.ors-burs.update';
            $payees = Supplier::orderBy('company_name')->get();
            $supplier = DB::table('suppliers')->where('id', $payee)->first();
            $address = $address ? $address : $supplier->address;
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.ors-burs.update';
            $payees[] = User::select(
                DB::raw("CONCAT(firstname, ' ', lastname, ' [ ', position, ' ]') as name"),
                'id'
            )->orderBy('firstname')->get();
            $payees[] = Supplier::select(
                DB::raw("CONCAT(company_name, ' ', ' [ Registered Supplier ]') as company_name"),
                'id'
            )->orderBy('company_name')->get();
            $payees[] = CustomPayee::select(
                DB::raw("CONCAT(payee_name, ' [ Manually Added ]') as payee_name"),
                'id'
            )->orderBy('payee_name')->get();
            $employee = DB::table('emp_accounts')->where('id', $payee)->first();
            $address = $address ? $address : $employee->address;
        }

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        $projDat = new FundingProject;
        $_projects = FundingProject::orderBy('project_title');
        $projects = [];
        $tempFundSrcs = [];

        if (($moduleClass == 3 && !$roleHasBudget && !$roleHasAdministrator &&
            !$roleHasDeveloper && !$roleHasPropertySupply) || ($moduleClass == 2 &&
            !$roleHasBudget && !$roleHasAdministrator && !$roleHasDeveloper)) {
            $projectIDs = $projDat->getAccessibleProjects();

            $_projects = $_projects->where(function($qry) use ($projectIDs) {
                $qry->whereIn('id', $projectIDs);
            });
        }

        $_projects = $_projects->get();

        foreach ($_projects as $proj) {
            $directory = $proj->directory ? implode(' &rarr; ', unserialize($proj->directory)) : NULL;
            $projTitle = (strlen($proj->project_title) > 70 ?
                         substr($proj->project_title, 0, 70).'...' :
                         $proj->project_title);
            $projTitle = strtoupper($projTitle);
            $title = $directory ? "$directory &rarr; $projTitle" : $projTitle;

            if ($directory) {
                $tempFundSrcs['with_dir'][] = (object) [
                    'id' => $proj->id,
                    'project_title' => $title,
                ];
            } else {
                $tempFundSrcs['no_dir'][] = (object) [
                    'id' => $proj->id,
                    'project_title' => $title,
                ];
            }

            if (isset($tempFundSrcs['with_dir'])) {
                sort($tempFundSrcs['with_dir']);
            }
        }

        if (isset($tempFundSrcs['with_dir'])) {
            foreach ($tempFundSrcs['with_dir'] as $proj) {
                $projects[] = $proj;
            }
        }

        if (isset($tempFundSrcs['no_dir'])) {
            foreach ($tempFundSrcs['no_dir'] as $proj) {
                $projects[] = $proj;
            }
        }

        $uacsDisplay = '';
        $uacsObjectCode = $uacsData->uacs_object_code;
        $uacsItems = $uacsData->uacs_items;
        $_uacsItems = $uacsData->_uacs_items;
        $mooeTitles = $uacsData->mooe_titles;

        foreach ($uacsItems as $uacsCtr => $uacsItm) {
            $formatUacsAmt = number_format($uacsItm->amount, 2);
            $uacsDisplay .= "$uacsItm->uacs_code : $uacsItm->description = $formatUacsAmt\n\n";
        }

        return view($viewFile, compact(
            'id', 'documentType', 'serialNo', 'dateORS',
            'fundCluster', 'payee', 'office', 'address',
            'responsibilityCenter', 'particulars', 'mfoPAP',
            'continuing', 'current', 'amount', 'mfoPAPs',
            'sigCertified1', 'sigCertified2', 'dateCertified1',
            'dateCertified2', 'signatories', 'payees', 'isObligated',
            'transactionType', 'projects', 'project', 'mooeTitles',
            'priorYear', 'uacsObjectCode', 'uacsItems', '_uacsItems',
            'uacsDisplay'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $documentType = $request->document_type;
        $transactionType = !empty($request->transaction_type) ? $request->transaction_type: 'others';
        $serialNo = $request->serial_no;
        $dateORS = !empty($request->date_ors_burs) ? $request->date_ors_burs: NULL;
        $fundCluster = $request->fund_cluster;
        $payee = $request->payee;
        $office = $request->office;
        $address = $request->address;
        $responsibilityCenter = $request->responsibility_center;
        $particulars = $request->particulars;
        $dvParticulars = $request->particulars;
        $mfoPAP = $request->mfo_pap ? serialize($request->mfo_pap) : serialize([]);
        //$uacsObjectCode = $request->uacs_object_code ? serialize($request->uacs_object_code) : serialize([]);
        $project = $request->funding_source;
        $priorYear = $request->prior_year ? $request->prior_year : 0.00;
        $continuing = $request->continuing ? $request->continuing : 0.00;
        $current = $request->current ? $request->current : 0.00;
        $amount = $request->amount;
        $sigCertified1 = !empty($request->sig_certified_1) ? $request->sig_certified_1: NULL;
        $sigCertified2 = !empty($request->sig_certified_2) ? $request->sig_certified_2: NULL;
        $dateCertified1 = !empty($request->date_certified_1) ? $request->date_certified_1: NULL;
        $dateCertified2 = !empty($request->date_certified_2) ? $request->date_certified_2: NULL;

        $uacsObjectCode = serialize(explode(',', $request->uacs_object_code));

        try {
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;

            $empData = User::where('id', $payee)->count();
            $supplierData = Supplier::where('id', $payee)->count();
            $customPayeeData = CustomPayee::where('id', $payee)
                                         //->orWhere('payee_name', $payee)
                                         ->count();

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
                $instanceORS->transaction_type = $transactionType;

                if (!$empData && !$supplierData && !$customPayeeData) {
                    $instancePayee = CustomPayee::create([
                        'payee_name' => $payee
                    ]);

                    $payee = $instancePayee->id->string;
                }

                $instanceORS->payee = $payee;
            }

            $instanceORS->document_type = $documentType;
            $instanceORS->serial_no = $serialNo;
            $instanceORS->date_ors_burs = !empty($dateORS) ? $dateORS : $instanceORS->date_obligated;
            $instanceORS->fund_cluster = $fundCluster;
            $instanceORS->office = $office;
            $instanceORS->address = $address;
            $instanceORS->responsibility_center = $responsibilityCenter;
            $instanceORS->particulars = $particulars;
            $instanceORS->mfo_pap = $mfoPAP;
            $instanceORS->uacs_object_code = $uacsObjectCode;
            $instanceORS->sig_certified_1 = $sigCertified1;
            $instanceORS->sig_certified_2 = $sigCertified2;
            $instanceORS->date_certified_1 = $dateCertified1;
            $instanceORS->date_certified_2 = $dateCertified2;
            $instanceORS->funding_source = $project;
            $instanceORS->prior_year = $priorYear;
            $instanceORS->continuing = $continuing;
            $instanceORS->current = $current;
            $instanceORS->amount = $amount;
            $instanceORS->save();

            $instanceDV = DisbursementVoucher::where('ors_id', $id)->first();

            if ($instanceDV) {
                $particulars = str_replace("To obligate", "To payment", $particulars);

                $instanceDV->uacs_object_code = $uacsObjectCode;
                $instanceDV->particulars = $particulars;
                $instanceDV->prior_year = $priorYear;
                $instanceDV->continuing = $continuing;
                $instanceDV->current = $current;
                $instanceDV->save();
            }

            $this->updateUacsItems($request, $id);

            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                            'Budget Utilization Request & Status';

            $msg = "$documentType '$id' successfully updated.";
            Auth::user()->log($request, $msg);

            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUacsItems($id) {
        $itemData = $this->showUacsItemData($id);

        if ($itemData->module_class == 3) {
            $viewFile = 'modules.procurement.ors-burs.update-uacs';
        } else if ($itemData->module_class == 2) {
            $viewFile = 'modules.voucher.ors-burs.update-uacs';
        }

        return view($viewFile, [
            'id' => $id,
            'uacsObjectCode' => $itemData->uacs_object_code,
            'uacsItems' => $itemData->uacs_items,
            '_uacsItems' => $itemData->_uacs_items,
            'mooeTitles' => $itemData->mooe_titles,
            'amount' => $itemData->amount
        ]);
    }

    private function showUacsItemData($id) {
        $moduleClass = 2;
        $uacsObjectCode = [];
        $uacsItems = [];
        $_uacsItems = [];
        $amount = 0;

        if ($id != 'none') {
            $instanceORS = ObligationRequestStatus::find($id);
            $amount = $instanceORS->amount;
            $uacsObjectCode = $instanceORS->uacs_object_code ?
                            unserialize($instanceORS->uacs_object_code) :
                            [];
            $_uacsItems = MooeAccountTitle::whereIn('id', $uacsObjectCode)
                                    ->orderBy('order_no')->get();
            $uacsItems = DB::table('ors_burs_uacs_items as uacs_item')
                        ->select(
                            'uacs_item.id',
                            'uacs_item.uacs_id',
                            'uacs_item.description',
                            'uacs_item.amount',
                            'mooe.uacs_code'
                        )->join('mooe_account_titles as mooe', 'mooe.id', '=', 'uacs_item.uacs_id')
                        ->where('uacs_item.ors_id', $id)
                        ->orderBy('mooe.uacs_code')
                        ->get();
            $moduleClass = $instanceORS->module_class;
        }

        $mooeTitles = MooeAccountTitle::orderBy('order_no')->get();

        return (object) [
            'module_class' => $moduleClass,
            'uacs_object_code' => $uacsObjectCode,
            'uacs_items' => $uacsItems,
            '_uacs_items' => $_uacsItems,
            'mooe_titles' => $mooeTitles,
            'amount' => $amount
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function updateUacsItems($request, $id) {
        //$uacsObjectCode = $request->uacs_object_code ? serialize($request->uacs_object_code) : serialize([]);
        $uacsObjectCode = serialize(explode(',', $request->uacs_object_code));
        $uacsIDs = $request->uacs_id;
        $uacsDescriptions = $request->uacs_description;
        $uacsAmounts = $request->uacs_amount;
        $uacsOrsUacsIDs = $request->ors_uacs_id;

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instanceORS = ObligationRequestStatus::find($id);
            $serialNo = $instanceORS->serial_no;
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            $instanceORS->uacs_object_code = $uacsObjectCode;
            $instanceORS->save();

            if ($uacsOrsUacsIDs && count($uacsOrsUacsIDs) > 0) {
                foreach ($uacsOrsUacsIDs as $uacsOrsCtr => $uacsOrsID) {
                    $instanceUacsItem = OrsBursUacsItem::find($uacsOrsID);

                    if ($instanceUacsItem) {
                        $instanceUacsItem->description = $uacsDescriptions[$uacsOrsCtr];
                        $instanceUacsItem->amount = $uacsAmounts[$uacsOrsCtr];
                        $instanceUacsItem->save();
                    } else {
                        $instanceUacsItem = OrsBursUacsItem::create([
                            'ors_id' => $id,
                            'uacs_id' => $uacsIDs[$uacsOrsCtr],
                            'description' => $uacsDescriptions[$uacsOrsCtr],
                            'amount' => $uacsAmounts[$uacsOrsCtr]
                        ]);
                        $uacsOrsUacsIDs[] = $instanceUacsItem->id;
                    }
                }

                OrsBursUacsItem::whereNotIn('id', $uacsOrsUacsIDs)
                               ->where('ors_id', $id)
                               ->delete();
            }

            $instanceNotif->notifyObligatedORS($id, $routeName);

            $msg = "UACS item/s in $documentType's with a serial number of '$serialNo'
                    successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     * Soft deletes the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id) {
        $isDestroy = $request->destroy;

        if ($isDestroy) {
            $response = $this->destroy($request, $id);

            if ($response->alert_type == 'success') {
                return redirect()->route('ca-ors-burs', ['keyword' => $response->id])
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route('ca-ors-burs')
                                 ->with($response->alert_type, $response->msg);
            }
        } else {
            try {
                $instanceORS = ObligationRequestStatus::find($id);
                //$instanceDV = DisbursementVoucher::where('ors_id', $id)->first();
                $documentType = $instanceORS->document_type;
                $documentType = $documentType == 'ors' ? 'Obligation Request and Status' :
                                                 'Budget Utilization and Request Status';
                $orsID = $instanceORS->id;
                $instanceORS->delete();

                /*
                if ($instanceDV) {
                    $instanceDV->delete();
                }*/

                $msg = "$documentType '$orsID' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect()->route('ca-ors-burs', ['keyword' => $id])
                                 ->with('success', $msg);
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect()->route('ca-ors-burs')
                                 ->with('failed', $msg);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($request, $id) {
        try {
            $instanceORS = ObligationRequestStatus::find($id);
            //$instanceDV = DisbursementVoucher::where('ors_id', $id)->first();
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request and Status' :
                                             'Budget Utilization and Request Status';
            $orsID = $instanceORS->id;
            $instanceORS->forceDelete();

            /*
            if ($instanceDV) {
                $instanceDV->forceDelete();
            }*/

            $msg = "$documentType '$orsID' permanently deleted.";
            Auth::user()->log($request, $msg);

            return (object) [
                'msg' => $msg,
                'alert_type' => 'success',
                'id' => $id
            ];
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);

            return (object) [
                'msg' => $msg,
                'alert_type' => 'failed'
            ];
        }
    }

    public function showIssue($id) {
        $instanceORS = ObligationRequestStatus::find($id);
        $moduleClass = $instanceORS->module_class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.ors-burs.issue';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.ors-burs.issue';
        }

        return view($viewFile, [
            'id' => $id
        ]);
    }

    public function issue(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);
            $docStatus = $instanceDocLog->checkDocStatus($id);

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued", $remarks);

                    $instanceNotif->notifyIssuedORS($id, $routeName);

                    $msg = "$documentType '$id' successfully submitted to budget unit.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route($routeName, ['keyword' => $id])
                                     ->with('success', $msg);
                } else {
                    $msg = "Document for $documentType '$id' should be generated first.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route($routeName, ['keyword' => $id])
                                     ->with('warning', $msg);
                }
            } else {
                $msg = "$documentType '$id' already submitted.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName, ['keyword' => $id])
                                 ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showReceive($id) {
        $instanceORS = ObligationRequestStatus::find($id);
        $moduleClass = $instanceORS->module_class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.ors-burs.receive';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.ors-burs.receive';
        }

        return view($viewFile, [
            'id' => $id
        ]);
    }

    public function receive(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received", $remarks);
            $instanceNotif->notifyReceivedORS($id, $routeName);

            $msg = "$documentType '$id' successfully received.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showIssueBack($id) {
        $instanceORS = ObligationRequestStatus::find($id);
        $moduleClass = $instanceORS->module_class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.ors-burs.issue-back';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.ors-burs.issue-back';
        }

        return view($viewFile, [
            'id' => $id
        ]);
    }

    public function issueBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued_back", $remarks);
            //$instanceNotif->notifyIssuedBackORS($id, $routeName);

            $msg = "$documentType '$id' successfully submitted back.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showReceiveBack($id) {
        $instanceORS = ObligationRequestStatus::find($id);
        $moduleClass = $instanceORS->module_class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.ors-burs.receive-back';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.ors-burs.receive-back';
        }

        return view($viewFile, [
            'id' => $id
        ]);
    }

    public function receiveBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            $instanceDocLog->logDocument($id, NULL, NULL, "-", NULL);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received_back", $remarks);
            //$instanceNotif->notifyReceivedBackORS($id, $routeName);

            $msg = "$documentType '$id' successfully received back.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showObligate($id) {
        $instanceORS = ObligationRequestStatus::find($id);
        $amount = $instanceORS->amount;
        $uacsObjectCode = $instanceORS->uacs_object_code ?
                          unserialize($instanceORS->uacs_object_code) :
                          [];
        $mooeTitles = MooeAccountTitle::orderBy('order_no')->get();
        $_uacsItems = MooeAccountTitle::whereIn('id', $uacsObjectCode)
                                      ->orderBy('order_no')->get();
        $uacsItems = DB::table('ors_burs_uacs_items as uacs_item')
                       ->select(
                           'uacs_item.id',
                           'uacs_item.uacs_id',
                           'uacs_item.description',
                           'uacs_item.amount',
                           'mooe.uacs_code'
                        )
                       ->join('mooe_account_titles as mooe', 'mooe.id', '=', 'uacs_item.uacs_id')
                       ->where('uacs_item.ors_id', $id)
                       ->orderBy('mooe.uacs_code')
                       ->get();
        $moduleClass = $instanceORS->module_class;
        $serialNos = [
            (object) [
                'type' => 'current',
                'serial_no' => '01 101101 '.date('Y m').' '
            ],
            (object) [
                'type' => 'continuing',
                'serial_no' => '02 101102 '.date('Y m').' '
            ],
        ];
        $serialNo = $instanceORS->serial_no;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.ors-burs.obligate';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.ors-burs.obligate';
        }

        return view($viewFile, [
            'id' => $id,
            'serialNo' => $serialNo,
            'serialNos' => $serialNos,
            'uacsObjectCode' => $uacsObjectCode,
            'uacsItems' => $uacsItems,
            '_uacsItems' => $_uacsItems,
            'mooeTitles' => $mooeTitles,
            'amount' => $amount
        ]);
    }

    public function obligate(Request $request, $id) {
        $serialNo = $request->serial_no;
        $request->uacs_object_code = $request->uacs_object_code ?
                                    implode(',', $request->uacs_object_code):
                                    NULL;
        //$uacsObjectCode = $request->uacs_object_code ? serialize($request->uacs_object_code) : serialize([]);
        $uacsIDs = $request->uacs_id;
        $uacsDescriptions = $request->uacs_description;
        $uacsAmounts = $request->uacs_amount;

        $uacsObjectCode = serialize(explode(',', $request->uacs_object_code));

        try {
            $instanceDocLog = new DocLog;
            $instanceNotif = new Notif;
            $instanceORS = ObligationRequestStatus::with('po')->find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
                $instancePO = PurchaseJobOrder::find($instanceORS->po->id);
                $instancePO->status = 7;
                $instancePO->save();
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            $instanceORS->uacs_object_code = $uacsObjectCode;
            $instanceORS->date_ors_burs = Carbon::now();
            $instanceORS->date_obligated = Carbon::now();
            $instanceORS->obligated_by = Auth::user()->id;
            $instanceORS->serial_no = $serialNo;
            $instanceORS->save();

            $this->updateUacsItems($request, $id);

            $instanceNotif->notifyObligatedORS($id, $routeName);

            $msg = "$documentType with a serial number of '$serialNo'
                    is successfully obligated.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showLogRemarks($id) {
        $instanceDocLog = DocLog::where('doc_id', $id)
                                ->whereNotNull('remarks')
                                ->orderBy('logged_at', 'desc')
                                ->get();
        $instanceORS = ObligationRequestStatus::find($id);
        $moduleClass = $instanceORS->class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.ors-burs.remarks';
        } else {
            $viewFile = 'modules.voucher.ors-burs.remarks';
        }

        return view($viewFile, [
            'id' => $id,
            'docRemarks' => $instanceDocLog
        ]);
    }

    public function logRemarks(Request $request, $id) {
        $message = $request->message;

        if (!empty($message)) {
            $instanceORS = ObligationRequestStatus::find($id);
            $instanceDocLog = new DocLog;
            $instanceORS->notifyMessage($id, Auth::user()->id, $message);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "message", $message);
            return 'Sent!';
        }
    }
}
