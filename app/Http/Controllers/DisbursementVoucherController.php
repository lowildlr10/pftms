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
use App\Models\LiquidationReport;
use App\Models\InventoryStock;
use App\Models\ListDemandPayable;

use App\Models\EmpAccount as User;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\Signatory;
use App\Models\ItemUnitIssue;
use App\Models\FundingProject;
use App\Models\MooeAccountTitle;
use App\Models\MfoPap;
use App\Models\DvUacsItem;
use Carbon\Carbon;
use Auth;
use DB;

use App\Plugins\Notification as Notif;

class DisbursementVoucherController extends Controller
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
        $roleHasAccountant = Auth::user()->hasAccountantRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();

        // Get module access
        $module = 'proc_dv';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedPayment = Auth::user()->getModuleAccess($module, 'payment');
        $isAllowedDisburse = Auth::user()->getModuleAccess($module, 'disburse');
        $isAllowedIAR = Auth::user()->getModuleAccess('proc_iar', 'is_allowed');
        $isAllowedLDDAP = Auth::user()->getModuleAccess('pay_lddap', 'is_allowed');

        return view('modules.procurement.dv.index', [
            'list' => $data->dv_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedPayment' => $isAllowedPayment,
            'isAllowedDisburse' => $isAllowedDisburse,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedIAR' => $isAllowedIAR,
            'isAllowedLDDAP' => $isAllowedLDDAP,
            'roleHasOrdinary' => $roleHasOrdinary,
            'roleHasAccountant' => $roleHasAccountant,
            'roleHasBudget' => $roleHasBudget,
        ]);
    }

    public function indexCA(Request $request) {
        $data = $this->getIndexData($request, 'cashadvance');

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();

        // Get module access
        $module = 'ca_dv';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedDisburse = Auth::user()->getModuleAccess($module, 'disburse');
        $isAllowedPayment = Auth::user()->getModuleAccess($module, 'payment');
        $isAllowedORS = Auth::user()->getModuleAccess('ca_ors_burs', 'is_allowed');
        $isAllowedLR = Auth::user()->getModuleAccess('ca_lr', 'is_allowed');
        $isAllowedLDDAP = Auth::user()->getModuleAccess('pay_lddap', 'is_allowed');

        return view('modules.voucher.dv.index', [
            'list' => $data->dv_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'isAllowedDisburse' => $isAllowedDisburse,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedPayment' => $isAllowedPayment,
            'isAllowedLDDAP' => $isAllowedLDDAP,
            'isAllowedORS' => $isAllowedORS,
            'isAllowedLR' => $isAllowedLR,
            'roleHasOrdinary' => $roleHasOrdinary
        ]);
    }

    private function getIndexData($request, $type) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();

        if ($type == 'procurement') {
            /*
            $dvData = DisbursementVoucher::select('id', 'pr_id', 'particulars', 'module_class', 'dv_no')
                                         ->with(['bidpayee',
            'procors' => function($query) {
                $query->select('id', 'po_no');
            },
            'pr' => function($query) use($empDivisionAccess) {
                $query->whereIn('division', $empDivisionAccess)
                      ->whereNull('date_pr_cancelled');
            }])->where('disbursement_vouchers.module_class', 3);*/

            $dvData = DisbursementVoucher::select('id', 'pr_id', 'particulars', 'module_class', 'dv_no',
                                                  'date_for_payment', 'date_disbursed')
                                         ->whereHas('pr', function($query)
                                                use($empDivisionAccess) {
                $query->whereIn('division', $empDivisionAccess)
                    ->whereNull('date_pr_cancelled');
            })->whereHas('procors', function($query)
                         use($empDivisionAccess) {
                $query->select('id', 'po_no');
            })->with('bidpayee');

            if (!empty($keyword)) {
                $dvData = $dvData->where(function($qry) use ($keyword) {
                    $qry->where('disbursement_vouchers.id', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.pr_id', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.particulars', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.transaction_type', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.dv_no', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.date_dv', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.date_disbursed', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.responsibility_center', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.mfo_pap', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.amount', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.address', 'like', "%$keyword%")
                        ->orWhere('disbursement_vouchers.fund_cluster', 'like', "%$keyword%")
                        ->orWhereHas('procors', function($query) use ($keyword) {
                            $query->where('id', 'like', "%$keyword%")
                                  ->orWhere('po_no', 'like', "%$keyword%");
                        })
                        ->orWhereHas('bidpayee', function($query) use ($keyword) {
                            $query->where('company_name', 'like', "%$keyword%")
                                  ->orWhere('address', 'like', "%$keyword%");
                        });
                });
            }

            $dvData = $dvData->sortable(['procors.po_no' => 'desc'])->paginate(15);

            foreach ($dvData as $dvDat) {
                $dvDat->doc_status = $instanceDocLog->checkDocStatus($dvDat->id);
            }


            /*
            $dvData = ObligationRequestStatus::with(['bidpayee', 'procdv'])->whereHas('pr', function($query)
                                                use($empDivisionAccess) {
                $query->whereIn('division', $empDivisionAccess);
            })->whereHas('procdv', function($query) {
                $query->whereNull('deleted_at');
            });

            if (!empty($keyword)) {
                $dvData = $dvData->where(function($qry) use ($keyword) {
                    $qry->where('id', 'like', "%$keyword%")
                        ->orWhere('pr_id', 'like', "%$keyword%")
                        ->orWhere('po_no', 'like', "%$keyword%")
                        ->orWhere('amount', 'like', "%$keyword%")
                        ->orWhere('document_type', 'like', "%$keyword%")
                        ->orWhereHas('bidpayee', function($query) use ($keyword) {
                            $query->where('company_name', 'like', "%$keyword%")
                                ->orWhere('address', 'like', "%$keyword%");
                        })->orWhereHas('procdv', function($query) use ($keyword) {
                            $query->where('id', 'like', "%$keyword%")
                                ->orWhere('particulars', 'like', "%$keyword%")
                                //->orWhere('transaction_type', 'like', "%$keyword%")
                                ->orWhere('dv_no', 'like', "%$keyword%")
                                ->orWhere('date_dv', 'like', "%$keyword%")
                                ->orWhere('date_disbursed', 'like', "%$keyword%")
                                ->orWhere('responsibility_center', 'like', "%$keyword%")
                                ->orWhere('mfo_pap', 'like', "%$keyword%")
                                ->orWhere('amount', 'like', "%$keyword%")
                                ->orWhere('address', 'like', "%$keyword%")
                                ->orWhere('fund_cluster', 'like', "%$keyword%");
                        });
                });
            }

            $dvData = $dvData->sortable(['po_no' => 'desc'])->paginate(15);

            foreach ($dvData as $dvDat) {
                $dvDat->doc_status = $instanceDocLog->checkDocStatus($dvDat->procdv['id']);
            }*/
        } else {
            $dvData = DisbursementVoucher::with('procors')->whereHas('emppayee', function($query)
                                           use($empDivisionAccess) {
                $query->whereIn('division', $empDivisionAccess);
            })->whereNull('deleted_at')->where('module_class', 2);

            if ($roleHasOrdinary) {
                $dvData = $dvData->where('payee', Auth::user()->id);
            }

            if (!empty($keyword)) {
                $dvData = $dvData->where(function($qry) use ($keyword) {
                    $qry->where('id', 'like', "%$keyword%")
                        ->orWhere('ors_id', 'like', "%$keyword%")
                        ->orWhere('particulars', 'like', "%$keyword%")
                        ->orWhere('transaction_type', 'like', "%$keyword%")
                        ->orWhere('dv_no', 'like', "%$keyword%")
                        ->orWhere('date_dv', 'like', "%$keyword%")
                        ->orWhere('date_disbursed', 'like', "%$keyword%")
                        ->orWhere('responsibility_center', 'like', "%$keyword%")
                        ->orWhere('mfo_pap', 'like', "%$keyword%")
                        ->orWhere('amount', 'like', "%$keyword%")
                        ->orWhere('address', 'like', "%$keyword%")
                        ->orWhere('fund_cluster', 'like', "%$keyword%")
                        ->orWhereHas('emppayee', function($query) use ($keyword) {
                            $query->where('firstname', 'like', "%$keyword%")
                                  ->orWhere('middlename', 'like', "%$keyword%")
                                  ->orWhere('lastname', 'like', "%$keyword%")
                                  ->orWhere('position', 'like', "%$keyword%");
                        });
                });
            }

            $dvData = $dvData->sortable(['created_at' => 'desc'])->paginate(15);

            foreach ($dvData as $dvDat) {
                $dvDat->doc_status = $instanceDocLog->checkDocStatus($dvDat->id);
                $dvDat->has_ors = ObligationRequestStatus::where('id', $dvDat->ors_id)->count();
                $dvDat->has_lr = LiquidationReport::where('dv_id', $dvDat->id)->count();
            }
        }

        return (object) [
            'keyword' => $keyword,
            'dv_data' => $dvData,
            'paper_sizes' => $paperSizes
        ];
    }

    /**
     * Show the form for creatingr the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCreateFromORS($orsID) {
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();

        $orsList = ObligationRequestStatus::all();
        $orsData = ObligationRequestStatus::with('emppayee')->find($orsID);
        $empID = $orsData->emppayee['emp_id'];
        $payee = $orsData->payee;
        $mfoPAP = $orsData->mfo_pap ?
                  unserialize($orsData->mfo_pap) :
                  [];
        $serialNo = $orsData->serial_no;
        $address = $orsData->address;
        $sigCert1 = $orsData->sig_certified_1;
        $transactionType = $orsData->transaction_type;
        $project = $orsData->funding_source;
        $priorYear = $orsData->prior_year;
        $continuing = $orsData->continuing;
        $current = $orsData->current;
        $amount = $orsData->amount;

        $mfoPAPs = MfoPap::orderBy('code')->get();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        $payees = $roleHasOrdinary ?
                User::where('id', Auth::user()->id)
                    ->orderBy('firstname')
                    ->get() :
                User::where('is_active', 'y')
                    ->whereIn('division', $empDivisionAccess)
                    ->orderBy('firstname')->get();

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

        return view('modules.voucher.dv.create', compact(
            'signatories', 'payees', 'payee', 'empID',
            'serialNo', 'address', 'amount', 'orsList',
            'orsID', 'transactionType', 'sigCert1',
            'project', 'projects', 'priorYear', 'continuing',
            'current', 'mfoPAPs', 'orsData', 'mfoPAP'
        ));
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

        $orsList = ObligationRequestStatus::all();
        $empID = '';
        $payee = '';
        $serialNo = '';
        $address = '';
        $sigCert1 = '';
        $transactionType = '';
        $orsID = NULL;
        $priorYear = NULL;
        $continuing = NULL;
        $current = NULL;
        $amount = 0.00;

        $mfoPAPs = MfoPap::orderBy('code')->get();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        $payees = $roleHasOrdinary ?
                User::where('id', Auth::user()->id)
                    ->orderBy('firstname')
                    ->get() :
                User::where('is_active', 'y')
                    ->whereIn('division', $empDivisionAccess)
                    ->orderBy('firstname')->get();

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

        return view('modules.voucher.dv.create', compact(
            'signatories', 'payees', 'payee', 'empID',
            'serialNo', 'address', 'amount', 'orsList',
            'orsID', 'transactionType', 'sigCert1',
            'projects', 'priorYear', 'continuing',
            'current', 'mfoPAPs'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $orsID = !empty($request->ors_id) ? $request->ors_id : NULL;
        $transactionType = $request->transaction_type;
        $fundCluster = $request->fund_cluster;
        $dateDV = !empty($request->date_dv) ? $request->date_dv : NULL;
        $dvNo = $request->dv_no;
        $payee = $request->payee;
        $address = $request->address;
        $modePayments = !empty($request->mode_payment) ? $request->mode_payment : [];
        $otherPayment = !empty($request->other_payment) ? $request->other_payment : NULL;
        $particulars = $request->particulars;
        $responsibilityCenter = $request->responsibility_center;
        $mfoPAP = $mfoPAP = $request->mfo_pap ? serialize($request->mfo_pap) : serialize([]);
        $project = $request->funding_source;
        $priorYear = $request->prior_year ? $request->prior_year : 0.00;
        $continuing = $request->continuing ? $request->continuing : 0.00;
        $current = $request->current ? $request->current : 0.00;
        $amount = $request->amount;
        $sigCertified = $request->sig_certified;
        $sigAccounting = $request->sig_accounting;
        $dateAccounting = !empty($request->date_accounting) ? $request->date_accounting : NULL;
        $sigAgencyHead = $request->sig_agency_head;
        $dateAgencyHead = !empty($request->date_agency_head) ? $request->date_agency_head : NULL;

        if (in_array('mds', $modePayments)) {
            $modePayment = '1';
        } else {
            $modePayment = '0';
        }

        if (in_array('commercial', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        if (in_array('ada', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        if (in_array('others', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        $routeName = 'ca-dv';

        try {
            $instanceDV = new DisbursementVoucher;
            $instanceDV->ors_id = $orsID;
            $instanceDV->transaction_type = $transactionType;
            $instanceDV->fund_cluster = $fundCluster;
            $instanceDV->date_dv = $dateDV;
            $instanceDV->dv_no = $dvNo;
            $instanceDV->payment_mode = $modePayment;
            $instanceDV->other_payment = $otherPayment;
            $instanceDV->payee = $payee;
            $instanceDV->address = $address;
            $instanceDV->particulars = $particulars;
            $instanceDV->responsibility_center = $responsibilityCenter;
            $instanceDV->mfo_pap = $mfoPAP;
            $instanceDV->prior_year = $priorYear;
            $instanceDV->continuing = $continuing;
            $instanceDV->current = $current;
            $instanceDV->amount = $amount;
            $instanceDV->sig_certified = $sigCertified;
            $instanceDV->sig_accounting = $sigAccounting;
            $instanceDV->date_accounting = $dateAccounting;
            $instanceDV->sig_agency_head = $sigAgencyHead;
            $instanceDV->date_agency_head = $dateAgencyHead;
            $instanceDV->module_class = 2;
            $instanceDV->funding_source = $project;
            $instanceDV->save();

            $documentType = 'Disbursement Voucher';

            $msg = "$documentType '$orsID' successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $orsID])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();
        $roleHasPropertySupply = Auth::user()->hasPropertySupplyRole();

        $orsList = ObligationRequestStatus::all();
        $dvData = DisbursementVoucher::with(['bidpayee', 'procors', 'emppayee'])->find($id);
        $moduleClass = $dvData->module_class;
        $orsID = $dvData->ors_id;
        $dvDate = $dvData->date_dv;
        $dvNo = $dvData->dv_no;
        $fundCluster = $dvData->fund_cluster;
        $transactionType = $dvData->transaction_type;
        $paymentModes = explode('-', $dvData->payment_mode);
        $paymentMode1 = $paymentModes[0];
        $paymentMode2 = $paymentModes[1];
        $paymentMode3 = $paymentModes[2];
        $paymentMode4 = $paymentModes[3];
        $otherPayment = $dvData->other_payment;
        $responsibilityCenter = $dvData->responsibility_center;
        $particulars = $dvData->particulars;
        $mfoPAP = $dvData->mfo_pap ?
                  unserialize($dvData->mfo_pap) :
                  [];
        $priorYear = $dvData->prior_year;
        $continuing = $dvData->continuing;
        $current = $dvData->current;
        $amount = $dvData->amount;
        $sigCertified = $dvData->sig_certified;
        $sigAccounting = $dvData->sig_accounting;
        $sigAgencyHead = $dvData->sig_agency_head;
        $dateAccounting = $dvData->date_accounting;
        $dateAgencyHead = $dvData->date_agency_head;
        $checkAdaNo = $dvData->check_ada_no;
        $dateCheckAda = $dvData->date_check_ada;
        $bankName = $dvData->bank_name;
        $bankAccountNo = $dvData->bank_account_no;
        $jevNo = $dvData->jev_no;
        $receiptPrintedName = $dvData->receipt_printed_name;
        $dateJev = $dvData->date_jev;
        $signature = $dvData->signature;
        $orNo = $dvData->or_no;
        $otherDocuments = $dvData->other_documents;
        $serialNo = $dvData->procors['serial_no'];
        $payee = $dvData->payee;
        $address = !empty($dvData->address) ? $dvData->address : $dvData->procors['address'];
        $project = $dvData->funding_source;
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        if ($moduleClass == 3) {
            $payees = Supplier::orderBy('company_name')->get();
            $tinNo = $dvData->bidpayee['tin_no'];
            $viewFile = 'modules.procurement.dv.update';
        } else if ($moduleClass == 2) {
            $payees = User::orderBy('firstname')->get();
            $tinNo = $dvData->emppayee['emp_id'];
            $viewFile = 'modules.voucher.dv.update';
        }

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        $projDat = new FundingProject;
        $mfoPAPs = MfoPap::orderBy('code')->get();
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

        return view($viewFile, compact(
            'id', 'dvData', 'dvNo', 'fundCluster', 'tinNo',
            'paymentMode1', 'paymentMode2', 'paymentMode3',
            'paymentMode4', 'otherPayment', 'responsibilityCenter',
            'mfoPAP', 'amount', 'sigCertified', 'sigAccounting',
            'sigAgencyHead', 'dateAccounting', 'dateAgencyHead',
            'checkAdaNo', 'dateCheckAda', 'bankName', 'bankAccountNo',
            'jevNo', 'receiptPrintedName', 'dateJev', 'signature',
            'orNo', 'otherDocuments', 'signatories', 'signatories',
            'serialNo', 'address', 'dvDate', 'payees', 'particulars',
            'payee', 'transactionType', 'orsList', 'orsID', 'projects',
            'project', 'priorYear', 'continuing', 'current', 'mfoPAPs'
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
        $fundCluster = $request->fund_cluster;
        $orsID = !empty($request->ors_id) ? $request->ors_id : NULL;
        $transactionType = $request->transaction_type;
        $dateDV = !empty($request->date_dv) ? $request->date_dv : NULL;
        $dvNo = $request->dv_no;
        $modePayments = !empty($request->mode_payment) ? $request->mode_payment : [];
        $otherPayment = !empty($request->other_payment) ? $request->other_payment : NULL;
        $particulars = $request->particulars;
        $responsibilityCenter = $request->responsibility_center;
        $mfoPAP = $mfoPAP = $request->mfo_pap ? serialize($request->mfo_pap) : serialize([]);
        $project = $request->funding_source;
        $priorYear = $request->prior_year ? $request->prior_year : 0.00;
        $continuing = $request->continuing ? $request->continuing : 0.00;
        $current = $request->current ? $request->current : 0.00;
        $amount = $request->amount;
        $sigCertified = $request->sig_certified;
        $sigAccounting = $request->sig_accounting;
        $dateAccounting = !empty($request->date_accounting) ? $request->date_accounting : NULL;
        $sigAgencyHead = $request->sig_agency_head;
        $dateAgencyHead = !empty($request->date_agency_head) ? $request->date_agency_head : NULL;

        if (in_array('mds', $modePayments)) {
            $modePayment = '1';
        } else {
            $modePayment = '0';
        }

        if (in_array('commercial', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        if (in_array('ada', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        if (in_array('others', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        try {
            $orsDat = ObligationRequestStatus::find($orsID);
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
                $instanceDV->ors_id = $orsID;
                $instanceDV->transaction_type = $transactionType;
                $instanceDV->sig_certified = $sigCertified;
            }

            $instanceDV->fund_cluster = $fundCluster;
            $instanceDV->date_dv = $dateDV;
            $instanceDV->dv_no = $dvNo;
            $instanceDV->payment_mode = $modePayment;
            $instanceDV->other_payment = $otherPayment;
            $instanceDV->particulars = $particulars;
            $instanceDV->responsibility_center = $responsibilityCenter;
            $instanceDV->mfo_pap = $mfoPAP;
            $instanceDV->prior_year = $priorYear;
            $instanceDV->continuing = $continuing;
            $instanceDV->current = $current;
            $instanceDV->amount = $amount;
            $instanceDV->sig_accounting = $sigAccounting;
            $instanceDV->date_accounting = $dateAccounting;
            $instanceDV->sig_agency_head = $sigAgencyHead;
            $instanceDV->date_agency_head = $dateAgencyHead;
            $instanceDV->funding_source = $project;

            if ($orsDat) {
                $instanceDV->uacs_object_code = $orsDat->uacs_object_code ?
                                                $orsDat->uacs_object_code :
                                                serialize([]);
            }

            $instanceDV->save();

            $documentType = 'Disbursement Voucher';

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
        $instanceDV = DisbursementVoucher::find($id);
        $amount = $instanceDV->amount;
        $uacsObjectCode = $instanceDV->uacs_object_code ?
                          unserialize($instanceDV->uacs_object_code) :
                          [];
        $mooeTitles = MooeAccountTitle::orderBy('order_no')->get();
        $_uacsItems = MooeAccountTitle::whereIn('id', $uacsObjectCode)
                                      ->orderBy('order_no')->get();
        $uacsItems = DB::table('dv_uacs_items as uacs_item')
                       ->select(
                           'uacs_item.id',
                           'uacs_item.uacs_id',
                           'uacs_item.description',
                           'uacs_item.amount',
                           'mooe.uacs_code'
                        )
                       ->join('mooe_account_titles as mooe', 'mooe.id', '=', 'uacs_item.uacs_id')
                       ->where('uacs_item.dv_id', $id)
                       ->orderBy('mooe.uacs_code')
                       ->get();
        $moduleClass = $instanceDV->module_class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.dv.update-uacs';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.dv.update-uacs';
        }

        return view($viewFile, [
            'id' => $id,
            'uacsObjectCode' => $uacsObjectCode,
            'uacsItems' => $uacsItems,
            '_uacsItems' => $_uacsItems,
            'mooeTitles' => $mooeTitles,
            'amount' => $amount
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUacsItems(Request $request, $id) {
        $uacsObjectCode = $request->uacs_object_code ? serialize($request->uacs_object_code) : serialize([]);
        $uacsIDs = $request->uacs_id;
        $uacsDescriptions = $request->uacs_description;
        $uacsAmounts = $request->uacs_amount;
        $uacsDvUacsIDs = $request->dv_uacs_id;

        try {
            $instanceDocLog = new DocLog;
            //$instanceNotif = new Notif;
            $instanceDV = DisbursementVoucher::find($id);
            $dvNo = $instanceDV->dv_no;
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDV->uacs_object_code = $uacsObjectCode;
            $instanceDV->save();

            if ($uacsDvUacsIDs && count($uacsDvUacsIDs) > 0) {
                foreach ($uacsDvUacsIDs as $uacsDvCtr => $uacsDvID) {
                    $instanceUacsItem = DvUacsItem::find($uacsDvID);

                    if ($instanceUacsItem) {
                        $instanceUacsItem->description = $uacsDescriptions[$uacsDvCtr];
                        $instanceUacsItem->amount = $uacsAmounts[$uacsDvCtr];
                        $instanceUacsItem->save();
                    } else {
                        $instanceUacsItem = DvUacsItem::create([
                            'dv_id' => $id,
                            'uacs_id' => $uacsIDs[$uacsDvCtr],
                            'description' => $uacsDescriptions[$uacsDvCtr],
                            'amount' => $uacsAmounts[$uacsDvCtr]
                        ]);
                        $uacsDvUacsIDs[] = $instanceUacsItem->id;
                    }
                }

                DvUacsItem::whereNotIn('id', $uacsDvUacsIDs)
                                   ->where('dv_id', $id)
                                   ->delete();
            }

            //$instanceNotif->notifyObligatedORS($id, $routeName);

            $msg = "UACS item/s in $documentType's with a DV number of '$dvNo'
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
                return redirect()->route('ca-dv', ['keyword' => $response->id])
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route('ca-dv')
                                 ->with($response->alert_type, $response->msg);
            }
        } else {
            try {
                $instanceDV = DisbursementVoucher::find($id);
                //$instanceORS = ObligationRequestStatus::where('id', $instanceDV->ors_id)->first();
                $documentType = 'Disbursement Voucher';
                $dvID = $instanceDV->id;
                $instanceDV->delete();

                /*
                if ($instanceORS) {
                    $instanceORS->delete();
                }*/

                $msg = "$documentType '$dvID' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect()->route('ca-dv', ['keyword' => $id])
                                 ->with('success', $msg);
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect()->route('ca-dv')
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
            $instanceDV = DisbursementVoucher::find($id);
            //$instanceORS = ObligationRequestStatus::where('id', $instanceDV->ors_id)->first();
            $documentType = 'Disbursement Voucher';
            $dvID = $instanceDV->id;
            $instanceDV->forceDelete();

            /*
            if ($instanceORS) {
                $instanceORS->forceDelete();
            }*/

            $msg = "$documentType '$dvID' permanently deleted.";
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
        $instanceDV = DisbursementVoucher::find($id);
        $moduleClass = $instanceDV->module_class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.dv.issue';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.dv.issue';
        }

        return view($viewFile, [
            'id' => $id
        ]);
    }

    public function issue(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);
            $docStatus = $instanceDocLog->checkDocStatus($id);

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued", $remarks);

                    //$instanceDV->notifyIssued($id, Auth::user()->id);

                    $msg = "$documentType '$id' successfully submitted to accounting unit.";
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
        $instanceDV = DisbursementVoucher::find($id);
        $moduleClass = $instanceDV->module_class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.dv.receive';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.dv.receive';
        }

        return view($viewFile, [
            'id' => $id
        ]);
    }

    public function receive(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received", $remarks);
            //$instanceDV->notifyReceived($id, Auth::user()->id);

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
        return view('modules.procurement.dv.issue-back', [
            'id' => $id
        ]);
    }

    public function issueBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued_back", $remarks);
            //$instanceDV->notifyIssuedBack($id, Auth::user()->id);

            $msg = "$documentType '$id' successfully issued back.";
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
        $instanceDV = DisbursementVoucher::find($id);
        $moduleClass = $instanceDV->module_class;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.dv.receive-back';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.dv.receive-back';
        }

        return view($viewFile, [
            'id' => $id
        ]);
    }

    public function receiveBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDocLog->logDocument($id, NULL, NULL, "-", NULL);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received_back", $remarks);

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

    public function showPayment($id) {
        $instanceDV = DisbursementVoucher::find($id);
        $moduleClass = $instanceDV->module_class;
        $dvNo = $instanceDV->dv_no ? $instanceDV->dv_no : date('Ym');

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.dv.payment';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.dv.payment';
        }

        return view($viewFile, [
            'id' => $id,
            'dvNo' => $dvNo,
            'readOnly' => false,
        ]);
    }

    public function payment(Request $request, $id) {
        $dvNo = $request->dv_no;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::with('procors')->find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
                $instancePO = PurchaseJobOrder::where('po_no', $instanceDV->procors->po_no)
                                              ->first();
                $instancePO->status = 10;
                $instancePO->save();
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDV->date_for_payment = Carbon::now();
            $instanceDV->for_payment_by = Auth::user()->id;
            $instanceDV->for_payment = 'y';
            $instanceDV->dv_no = $dvNo;
            $instanceDV->save();

            //$instanceDV->notifyPayment($id, Auth::user()->id);

            $msg = "$documentType with a DV number of '$dvNo'
                    is successfully set to 'For Payment'.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showDisburse($id) {
        $instanceDV = DisbursementVoucher::find($id);
        $moduleClass = $instanceDV->module_class;
        $dvNo = $instanceDV->dv_no;

        if ($moduleClass == 3) {
            $viewFile = 'modules.procurement.dv.disburse';
        } else if ($moduleClass == 2) {
            $viewFile = 'modules.voucher.dv.disburse';
        }

        return view($viewFile, [
            'id' => $id,
            'dvNo' => $dvNo,
            'readOnly' => true,
        ]);
    }

    public function disburse(Request $request, $id) {
        $dvNo = $request->dv_no;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::with('procors')->find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
                $instancePO = PurchaseJobOrder::where('po_no', $instanceDV->procors->po_no)
                                              ->first();
                $instancePO->status = 11;
                $instancePO->save();
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDV->date_disbursed = Carbon::now();
            $instanceDV->disbursed_by = Auth::user()->id;
            $instanceDV->save();

            //$instanceDV->notifyDisburse($id, Auth::user()->id);

            $msg = "$documentType with a DV number of '$dvNo'
                    is successfully set to 'For Disbursement'.";
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
        return view('modules.procurement.dv.remarks', [
            'id' => $id,
            'docRemarks' => $instanceDocLog
        ]);
    }

    public function logRemarks(Request $request, $id) {
        $message = $request->message;

        if (!empty($message)) {
            $instanceDV = DisbursementVoucher::find($id);
            $instanceDocLog = new DocLog;
            //$instanceDV->notifyMessage($id, Auth::user()->id, $message);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "message", $message);
            return 'Sent!';
        }
    }
}
