<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FundingProject;
use App\Models\FundingBudget;
use App\Models\FundingAllotment;
use App\Models\FundingLedger;
use App\Models\FundingLedgerItem;
use App\Models\FundingLedgerAllotment;
use App\Models\FundingBudgetRealignment;
use App\Models\FundingAllotmentRealignment;
use App\Models\ObligationRequestStatus;
use App\Models\DisbursementVoucher;
use App\Models\PurchaseRequest;
use App\Models\AllotmentClass;
use App\Models\PaperSize;
use App\Models\EmpAccount as User;
use App\Models\EmpUnit;
use App\Models\Supplier;
use App\Models\MooeAccountTitle;
use App\Models\OrsBursUacsItem;
use App\Models\DvUacsItem;

use Carbon\Carbon;
use Auth;
use DB;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class LedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexObligation(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'report_orsledger';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $fundProject = $this->getIndexData($request, 'obligation');
        $directories = $this->getProjectDirectory($request, 'obligation');

        return view('modules.report.obligation-ledger.index', [
            'list' => $fundProject,
            'directories' => $directories,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'dirCtr' => 0,
        ]);
    }

    public function indexDisbursement(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'report_dvledger';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $fundProject = $this->getIndexData($request, 'disbursement');
        $directories = $this->getProjectDirectory($request, 'disbursement');

        return view('modules.report.disbursement-ledger.index', [
            'list' => $fundProject,
            'directories' => $directories,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'dirCtr' => 0,
        ]);
    }

    private function getIndexData($request, $for) {
        $keyword = trim($request->keyword);

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
        $fundProject = FundingProject::whereHas('budget', function($query) {
            $query->whereNotNull('date_approved');
        });

        if ($roleHasDeveloper || $roleHasRD || $roleHasARD || $roleHasPlanning ||
            $roleHasBudget || $roleHasAccountant) {
        } else {
            $projectIDs = $projDat->getAccessibleProjects();

            $fundProject = $fundProject->where(function($qry) use ($projectIDs) {
                $qry->whereIn('id', $projectIDs);
            });
        }

        if (!empty($keyword)) {
            $fundProject = $fundProject->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('project_title', 'like', "%$keyword%")
                    ->orWhere('project_leader', 'like', "%$keyword%")
                    ->orWhere('date_from', 'like', "%$keyword%")
                    ->orWhere('date_to', 'like', "%$keyword%")
                    ->orWhere('project_cost', 'like', "%$keyword%")
                    ->orWhereHas('allotments', function($query) use ($keyword) {
                        $query->where('budget_id', 'like', "%$keyword%")
                              ->orWhere('allotment_name', "%$keyword%")
                              ->orWhere('allotment_cost', "%$keyword%");
                    })->orWhereHas('site', function($query) use ($keyword) {
                        $query->where('id', 'like', "%$keyword%")
                              ->orWhere('municipality_name', 'like', "%$keyword%");
                    });
            });
        }

        $fundProject = $fundProject->sortable(['project_title' => 'desc'])
                                   ->paginate(15);

        foreach ($fundProject as $project) {
            $ledgerDat = FundingLedger::where([
                ['project_id', $project->id], ['ledger_for', $for]
            ])->first();
            $project->has_ledger = $ledgerDat ? true : false;
            $project->ledger_id = $ledgerDat ? $ledgerDat->id : NULL;

            if ($project->project_type == 'saa') {
                $project->project_type_name = 'Special Projects';
            } else if ($project->project_type == 'mooe') {
                $project->project_type_name = 'Maintenance and Other Operating Expenses';
            } else if ($project->project_type == 'lgia') {
                $project->project_type_name = 'Local Grants-In-Aid';
            } else if ($project->project_type == 'setup') {
                $project->project_type_name = 'Small Enterprise Technology Upgrading Program';
            }
        }

        return $fundProject;
    }

    private function getProjectDirectory($request, $for) {
        $keyword = trim($request->keyword);
        $directories = [];

        $projectData = $this->getIndexData($request, $for);

        foreach ($projectData as $proj) {
            $_directories = $proj->directory ? unserialize($proj->directory) : [];
            $projID = $proj->id;
            $projTitle = (strlen($proj->project_title) > 30 ?
                         substr($proj->project_title, 0, 30).'...' :
                         $proj->project_title);

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

        return $directories;
    }

    public function showLedger(Request $request, $id, $for, $type) {
        $itemCounter = 0;
        $allotmentCounter = 0;

        $ledgerDat = FundingLedger::find($id);
        $projectID = $ledgerDat->project_id;
        $projectDat = FundingProject::find($projectID);
        $projectTitle = $projectDat->project_title;
        $allotmentClasses = AllotmentClass::orderBy('order_no')->get();
        $libData = FundingBudget::where('project_id', $projectID)
                                ->whereNotNull('date_approved')
                                ->first();
        $libID = $libData->id;
        $allotments = FundingAllotment::where('budget_id', $libID)
                                      ->orderBy('order_no')
                                      ->get();
        $libRealignments = FundingBudgetRealignment::orderBy('realignment_order')
                                                   ->where('project_id', $projectID)
                                                   ->whereNotNull('date_approved')
                                                   ->get();
        $lastBudgetData = $libRealignments->count() > 0 ?
                          $libRealignments->last() :
                          ($libData ? $libData : NULL);
        $isRealignment = $libRealignments->count() > 0 ? true : false;


        $groupedAllotments = $this->groupAllotments(
            $allotments, $isRealignment, $lastBudgetData
        );
        $allotments = $groupedAllotments->grouped_allotments;
        $allotmentHeaders = $groupedAllotments->allotment_headers;
        $classItemCounts = $groupedAllotments->class_item_counts;

        $mooeTitles = json_decode($this->getMooeTitles($request)->content(), true);
        $empUnits = json_decode($this->getUnits($request)->content(), true);
        $payees = json_decode($this->getPayees($request)->content(), true);

        foreach ($mooeTitles as $mooeCtr => $mooeTitle) {
            $mooeTitles[$mooeCtr] = (object) $mooeTitle;
        }

        foreach ($empUnits as $unitCtr => $empUnt) {
            $empUnits[$unitCtr] = (object) $empUnt;
        }

        foreach ($payees as $payCtr => $pay) {
            $payees[$payCtr] = (object) $pay;
        }

        if ($for == 'obligation') {
            $approvedBudgets = [
                (object) [
                    'label' => 'Approved Budget',
                    'total' => $libData->approved_budget
                ]
            ];

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => $this->convertToOrdinal($realignCtr + 1) . ' Re-alignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                $viewFile = 'modules.report.obligation-ledger.show-saa';
            }
        } else {
            $approvedBudgets = [
                (object) [
                    'label' => 'LIB',
                    'total' => $libData->approved_budget
                ]
            ];

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => 'Realignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                $viewFile = 'modules.report.disbursement-ledger.show-saa';
            } else if ($type == 'mooe') {
                $viewFile = 'modules.report.disbursement-ledger.show-mooe';
            } else if ($type == 'lgia') {
                $viewFile = 'modules.report.disbursement-ledger.show-lgia';
            } else if ($type == 'setup') {
                $viewFile = 'modules.report.disbursement-ledger.show-setup';
            }
        }

        $groupedVouchers = $this->groupVouchersByMonth(
            $id, $projectID, $for, $type, $allotmentHeaders,
            $approvedBudgets[count($approvedBudgets) - 1]->total
        );

        return view($viewFile, compact(
            'allotments', 'classItemCounts', 'approvedBudgets', 'isRealignment',
            'groupedVouchers', 'itemCounter', 'allotmentCounter', 'payees', 'id',
            'empUnits', 'mooeTitles', 'projectTitle', 'allotmentHeaders'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate(Request $request, $projectID, $for, $type) {
        $itemCounter = 0;
        $allotmentCounter = 0;
        $projectDat = FundingProject::find($projectID);
        $projectTitle = $projectDat->project_title;
        $allotmentClasses = AllotmentClass::orderBy('order_no')->get();
        $libData = FundingBudget::where('project_id', $projectID)
                                ->whereNotNull('date_approved')
                                ->first();
        $libID = $libData->id;
        $allotments = FundingAllotment::where('budget_id', $libID)
                                      ->orderBy('order_no')
                                      ->get();
        $libRealignments = FundingBudgetRealignment::orderBy('realignment_order')
                                                   ->where('project_id', $projectID)
                                                   ->whereNotNull('date_approved')
                                                   ->get();
        $lastBudgetData = $libRealignments->count() > 0 ?
                          $libRealignments->last() :
                          ($libData ? $libData : NULL);
        $isRealignment = $libRealignments->count() > 0 ? true : false;


        $groupedAllotments = $this->groupAllotments(
            $allotments, $isRealignment, $lastBudgetData
        );
        $allotments = $groupedAllotments->grouped_allotments;
        $classItemCounts = $groupedAllotments->class_item_counts;

        $mooeTitles = json_decode($this->getMooeTitles($request)->content(), true);
        $empUnits = json_decode($this->getUnits($request)->content(), true);
        $payees = json_decode($this->getPayees($request)->content(), true);

        foreach ($mooeTitles as $mooeCtr => $mooeTitle) {
            $mooeTitles[$mooeCtr] = (object) $mooeTitle;
        }

        foreach ($empUnits as $unitCtr => $empUnt) {
            $empUnits[$unitCtr] = (object) $empUnt;
        }

        foreach ($payees as $payCtr => $pay) {
            $payees[$payCtr] = (object) $pay;
        }

        if ($for == 'obligation') {
            $vouchers = ObligationRequestStatus::select(
                DB::raw("DATE_FORMAT(date_obligated, '%Y-%m-%d') as date_obligated"),
                'id', 'payee', 'particulars', 'serial_no', 'prior_year', 'continuing',
                'current', 'amount', 'uacs_object_code'
            )->where([['funding_source', $projectID]])
             ->whereNotNull('date_obligated')
             ->orderBy('date_obligated')
             ->get();
            $approvedBudgets = [
                (object) [
                    'label' => 'Approved Budget',
                    'total' => $libData->approved_budget
                ]
            ];

            foreach ($vouchers as $ors) {
                $_particulars = '';
                $uacsItems = OrsBursUacsItem::where('ors_id', $ors->id)->get();

                foreach ($uacsItems as $uacsItem) {
                    $_particulars .= "$uacsItem->description\n";
                }

                $ors->new_particulars = $_particulars;
            }

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => $this->convertToOrdinal($realignCtr + 1) . ' Re-alignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                $viewFile = 'modules.report.obligation-ledger.create-saa';
            }
        } else {
            $vouchers = DB::table('disbursement_vouchers as dv')->select(
                DB::raw("DATE_FORMAT(dv.date_disbursed, '%Y-%m-%d') as date_disbursed"),
                'dv.id', 'dv.payee', 'dv.particulars', 'dv.amount', 'dv.uacs_object_code',
                'dv.module_class', 'dv.pr_id', 'ors.serial_no','ors.id as ors_id',
                'dv.prior_year', 'dv.continuing', 'dv.current'
            )->leftJoin('obligation_request_status as ors', 'ors.id', '=', 'dv.ors_id')
             ->where([['dv.funding_source', $projectID]])
             ->whereNotNull('dv.date_disbursed')
             ->orderBy('dv.date_disbursed')
             ->get();
            $approvedBudgets = [
                (object) [
                    'label' => 'LIB',
                    'total' => $libData->approved_budget
                ]
            ];

            foreach ($vouchers as $dv) {
                $_particulars = '';
                $uacsItems = DvUacsItem::where('dv_id', $dv->id)->get();

                foreach ($uacsItems as $uacsItem) {
                    $_particulars .= "$uacsItem->description\n";
                }

                $dv->new_particulars = $_particulars;
            }

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => 'Realignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                $viewFile = 'modules.report.disbursement-ledger.create-saa';
            } else if ($type == 'mooe') {
                foreach ($vouchers as $dv) {
                    $dv->uacs_object_code = unserialize($dv->uacs_object_code);

                    if ($dv->module_class == 3) {
                        $prID = $dv->pr_id;
                        $prDat = PurchaseRequest::find($prID);
                        $userID = $prDat->requested_by;
                        $userDat = User::find($userID);
                        $dv->unit = $userDat->unit;
                    } else {
                        $userID = $dv->payee;
                        $userDat = User::find($userID);
                        $dv->unit = $userDat->unit;
                    }
                }

                $viewFile = 'modules.report.disbursement-ledger.create-mooe';
            } else if ($type == 'lgia') {
                $viewFile = 'modules.report.disbursement-ledger.create-lgia';
            } else if ($type == 'setup') {
                $viewFile = 'modules.report.disbursement-ledger.create-setup';
            }
        }

        return view($viewFile, compact(
            'allotments', 'classItemCounts', 'approvedBudgets', 'isRealignment',
            'vouchers', 'itemCounter', 'allotmentCounter', 'payees', 'projectID',
            'empUnits', 'mooeTitles', 'projectTitle'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $projectID, $for, $type) {
        if ($for == 'obligation') {
            $documentType = 'Obligation Ledger';
            $routeName = 'report-obligation-ledger';

            if ($type == 'saa') {
                $allotmentIDs = $request->allotment_id;
                $allotmentRealignIDs = $request->allot_realign_id;
                $dateORS = $request->date_ors_burs;
                $payees = $request->payee;
                $particulars = $request->particular;
                $orsIDs = $request->ors_id;
                $orsNos = $request->ors_no;
                $totals = $request->amount;
                $allotments = $request->allotment;
            }
        } else {
            $documentType = 'Disbursement Ledger';
            $routeName = 'report-disbursement-ledger';

            if ($type == 'saa') {
                $allotmentIDs = $request->allotment_id;
                $allotmentRealignIDs = $request->allot_realign_id;
                $dateDVs = $request->date_dv;
                $orsIDs = $request->ors_id;
                $dvIDs = $request->dv_id ? $request->dv_id : [];
                $orsNos = $request->ors_no;
                $payees = $request->payee;
                $particulars = $request->particular;
                $allotments = $request->allotment;
                $totals = $request->amount;
            } else if ($type == 'mooe') {
                $dateDVs = $request->date_dv;
                $orsIDs = $request->ors_id;
                $dvIDs = $request->dv_id ? $request->dv_id : [];
                $orsNos = $request->ors_no;
                $payees = $request->payee;
                $particulars = $request->particular;
                $mooeObjectCodes = $request->uacs_object_code;
                $priorYears = $request->prior_year;
                $continuings = $request->continuing;
                $currents = $request->current;
                $units = $request->unit;
            } else if ($type == 'lgia') {
                $dateDVs = $request->date_dv;
                $orsIDs = $request->ors_id;
                $dvIDs = $request->dv_id ? $request->dv_id : [];
                $orsNos = $request->ors_no;
                $payees = $request->payee;
                $particulars = $request->particular;
                $priorYears = $request->prior_year;
                $continuings = $request->continuing;
                $currents = $request->current;
                $totals = $request->amount;
            }
        }

        try {
            $libDat = FundingBudget::where('project_id', $projectID)->first();
            $budgetID = $libDat->id;

            $instanceLedger = FundingLedger::where([
                ['project_id', $projectID],
                ['ledger_for', $for],
                ['ledger_type', $type]
            ])->withTrashed()->first();

            if (!$instanceLedger) {
                $instanceLedger = new FundingLedger;
            } else {
                if ($instanceLedger->deleted_at) {
                    $instanceLedger->restore();
                }

                $ledgerID = $instanceLedger->id;
                $ledgerItems = DB::table('funding_ledger_items')
                                 ->where('ledger_id', $ledgerID)
                                 ->get();

                foreach ($ledgerItems as $item) {
                    FundingLedgerAllotment::where('ledger_item_id', $item->id)
                                        ->delete();
                    FundingLedgerItem::destroy($item->id);

                }
            }

            $instanceLedger->project_id = $projectID;
            $instanceLedger->budget_id = $budgetID;
            $instanceLedger->ledger_for = $for;
            $instanceLedger->ledger_type = $type;
            $instanceLedger->save();

            $ledgerDat = FundingLedger::where([
                ['project_id', $projectID],
                ['ledger_for', $for],
                ['ledger_type', $type]
            ])->first();
            $ledgerID = $ledgerDat->id;

            $orderNo = 1;

            if ($for == 'obligation') {
                foreach ($orsIDs as $orsCtr => $orsID) {
                    if ($type == 'saa') {
                        $instanceLedgerItem = new FundingledgerItem;
                        $instanceLedgerItem->project_id = $projectID;
                        $instanceLedgerItem->budget_id = $budgetID;
                        $instanceLedgerItem->ledger_id = $ledgerID;
                        $instanceLedgerItem->date_ors_dv = $dateORS[$orsCtr];
                        $instanceLedgerItem->ors_id = $orsID;
                        $instanceLedgerItem->ors_no = $orsNos[$orsCtr];
                        $instanceLedgerItem->payee = $payees[$orsCtr];
                        $instanceLedgerItem->particulars = $particulars[$orsCtr];
                        $instanceLedgerItem->total = $totals[$orsCtr];
                        $instanceLedgerItem->order_no = $orderNo;
                        $instanceLedgerItem->save();

                        $ledgerItemDat = DB::table('funding_ledger_items')
                                           ->where([
                                               ['order_no', $orderNo],
                                               ['ledger_id', $ledgerID]
                                            ])->first();
                        $ledgerItemID = $ledgerItemDat->id;

                        foreach ($allotments[$orsCtr] as $allotCtr => $total) {
                            if ($total > 0) {
                                $instanceLedgerAllotments = new FundingLedgerAllotment;
                                $instanceLedgerAllotments->project_id = $projectID;
                                $instanceLedgerAllotments->budget_id = $budgetID;
                                $instanceLedgerAllotments->ledger_id = $ledgerID;
                                $instanceLedgerAllotments->ledger_item_id = $ledgerItemID;
                                $instanceLedgerAllotments->allotment_id = $allotmentIDs[$allotCtr];
                                $instanceLedgerAllotments->realign_allotment_id = $allotmentRealignIDs[$allotCtr];
                                $instanceLedgerAllotments->current_cost = $total;
                                $instanceLedgerAllotments->save();
                            }
                        }

                        $orderNo++;
                    }
                }
            } else {
                foreach ($dvIDs as $dvCtr => $dvID) {
                    if ($type == 'saa') {
                        $instanceLedgerItem = new FundingledgerItem;
                        $instanceLedgerItem->project_id = $projectID;
                        $instanceLedgerItem->budget_id = $budgetID;
                        $instanceLedgerItem->ledger_id = $ledgerID;
                        $instanceLedgerItem->date_ors_dv = $dateDVs[$dvCtr];
                        $instanceLedgerItem->ors_id = $orsIDs[$dvCtr];
                        $instanceLedgerItem->dv_id = $dvID;
                        $instanceLedgerItem->ors_no = $orsNos[$dvCtr];
                        $instanceLedgerItem->payee = $payees[$dvCtr];
                        $instanceLedgerItem->particulars = $particulars[$dvCtr];
                        $instanceLedgerItem->total = $totals[$dvCtr];
                        $instanceLedgerItem->order_no = $orderNo;
                        $instanceLedgerItem->save();

                        $ledgerItemDat = DB::table('funding_ledger_items')
                                           ->where([
                                                ['order_no', $orderNo],
                                                ['ledger_id', $ledgerID
                                            ]])->first();
                        $ledgerItemID = $ledgerItemDat->id;

                        foreach ($allotments[$dvCtr] as $allotCtr => $total) {
                            if ($total > 0) {
                                $instanceLedgerAllotments = new FundingLedgerAllotment;
                                $instanceLedgerAllotments->project_id = $projectID;
                                $instanceLedgerAllotments->budget_id = $budgetID;
                                $instanceLedgerAllotments->ledger_id = $ledgerID;
                                $instanceLedgerAllotments->ledger_item_id = $ledgerItemID;
                                $instanceLedgerAllotments->allotment_id = $allotmentIDs[$allotCtr];
                                $instanceLedgerAllotments->realign_allotment_id = $allotmentRealignIDs[$allotCtr];
                                $instanceLedgerAllotments->current_cost = $total;
                                $instanceLedgerAllotments->save();
                            }
                        }

                        $orderNo++;
                    } else if ($type == 'mooe') {
                        $instanceLedgerItem = new FundingledgerItem;
                        $instanceLedgerItem->project_id = $projectID;
                        $instanceLedgerItem->budget_id = $budgetID;
                        $instanceLedgerItem->ledger_id = $ledgerID;
                        $instanceLedgerItem->date_ors_dv = $dateDVs[$dvCtr];
                        $instanceLedgerItem->ors_id = $orsIDs[$dvCtr];
                        $instanceLedgerItem->dv_id = $dvID;
                        $instanceLedgerItem->ors_no = $orsNos[$dvCtr];
                        $instanceLedgerItem->payee = $payees[$dvCtr];
                        $instanceLedgerItem->particulars = $particulars[$dvCtr];
                        $instanceLedgerItem->mooe_account = serialize($mooeObjectCodes[$dvCtr]);
                        $instanceLedgerItem->prior_year = $priorYears[$dvCtr];
                        $instanceLedgerItem->continuing = $continuings[$dvCtr];
                        $instanceLedgerItem->current = $currents[$dvCtr];
                        $instanceLedgerItem->unit = $units[$dvCtr] != '-' ? $units[$dvCtr] : NULL;
                        $instanceLedgerItem->order_no = $orderNo;
                        $instanceLedgerItem->save();

                        $orderNo++;
                    } else if ($type == 'lgia') {
                        $instanceLedgerItem = new FundingledgerItem;
                        $instanceLedgerItem->project_id = $projectID;
                        $instanceLedgerItem->budget_id = $budgetID;
                        $instanceLedgerItem->ledger_id = $ledgerID;
                        $instanceLedgerItem->date_ors_dv = $dateDVs[$dvCtr];
                        $instanceLedgerItem->ors_id = $orsIDs[$dvCtr];
                        $instanceLedgerItem->dv_id = $dvID;
                        $instanceLedgerItem->ors_no = $orsNos[$dvCtr];
                        $instanceLedgerItem->payee = $payees[$dvCtr];
                        $instanceLedgerItem->particulars = $particulars[$dvCtr];
                        $instanceLedgerItem->prior_year = $priorYears[$dvCtr];
                        $instanceLedgerItem->continuing = $continuings[$dvCtr];
                        $instanceLedgerItem->current = $currents[$dvCtr];
                        $instanceLedgerItem->total = $totals[$dvCtr];
                        $instanceLedgerItem->order_no = $orderNo;
                        $instanceLedgerItem->save();

                        $orderNo++;
                    }
                }
            }

            $msg = "$documentType successfully updated.";
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
    public function showEdit(Request $request, $id, $for, $type) {
        $itemCounter = 0;
        $allotmentCounter = 0;

        $ledgerDat = FundingLedger::find($id);
        $projectID = $ledgerDat->project_id;
        $projectDat = FundingProject::find($projectID);
        $projectTitle = $projectDat->project_title;
        $allotmentClasses = AllotmentClass::orderBy('order_no')->get();
        $libData = FundingBudget::where('project_id', $projectID)
                                ->whereNotNull('date_approved')
                                ->first();
        $libID = $libData->id;
        $allotments = FundingAllotment::where('budget_id', $libID)
                                      ->orderBy('order_no')
                                      ->get();
        $libRealignments = FundingBudgetRealignment::orderBy('realignment_order')
                                                   ->where('project_id', $projectID)
                                                   ->whereNotNull('date_approved')
                                                   ->get();
        $lastBudgetData = $libRealignments->count() > 0 ?
                          $libRealignments->last() :
                          ($libData ? $libData : NULL);
        $isRealignment = $libRealignments->count() > 0 ? true : false;


        $groupedAllotments = $this->groupAllotments(
            $allotments, $isRealignment, $lastBudgetData
        );
        $allotments = $groupedAllotments->grouped_allotments;
        $allotmentHeaders = $groupedAllotments->allotment_headers;
        $classItemCounts = $groupedAllotments->class_item_counts;

        $mooeTitles = json_decode($this->getMooeTitles($request)->content(), true);
        $empUnits = json_decode($this->getUnits($request)->content(), true);
        $payees = json_decode($this->getPayees($request)->content(), true);

        foreach ($mooeTitles as $mooeCtr => $mooeTitle) {
            $mooeTitles[$mooeCtr] = (object) $mooeTitle;
        }

        foreach ($empUnits as $unitCtr => $empUnt) {
            $empUnits[$unitCtr] = (object) $empUnt;
        }

        foreach ($payees as $payCtr => $pay) {
            $payees[$payCtr] = (object) $pay;
        }

        if ($for == 'obligation') {
            $vouchers = [];
            $_vouchers = DB::table('obligation_request_status as ors')->select(
                DB::raw("DATE_FORMAT(ors.date_obligated, '%Y-%m-%d') as date_obligated"),
                'ors.id', 'ors.payee', 'ors.particulars', 'ors.serial_no', 'ors.amount',
                'ledger.id as ledger_item_id', 'ledger.particulars as ledger_particulars',
                'ledger.ors_no', 'ledger.total', 'ledger.ledger_id'
            )->leftJoin('funding_ledger_items as ledger', 'ledger.ors_id', '=', 'ors.id')
             ->where('funding_source', $projectID)
             ->whereNotNull('date_obligated')
             ->orderBy('ors.date_obligated')
             ->get();
            $approvedBudgets = [
                (object) [
                    'label' => 'Approved Budget',
                    'total' => $libData->approved_budget
                ]
            ];

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => $this->convertToOrdinal($realignCtr + 1) . ' Re-alignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                foreach ($_vouchers as $voucher) {
                    if ($voucher->ledger_id == $id) {
                        if ($voucher->ledger_item_id) {
                            $voucher->allotments = [];
                            $ledgerAllotments = FundingLedgerAllotment::where(
                                'ledger_item_id', $voucher->ledger_item_id
                            )->get();

                            foreach ($allotmentHeaders as $allotHead) {
                                $amount = 0;

                                foreach ($ledgerAllotments as $allot) {
                                    if ($allot->allotment_id == $allotHead->allotment_id) {
                                        if (!empty($allot->allotment_id) && !empty($allotHead->allotment_id)) {
                                            $amount = $allot->current_cost;
                                            break;
                                        }
                                    }

                                    if ($allot->realign_allotment_id == $allotHead->realign_allotment_id) {
                                        if (!empty($allot->realign_allotment_id) && !empty($allotHead->realign_allotment_id)) {
                                            $amount = $allot->current_cost;
                                            break;
                                        }
                                    }
                                }

                                $voucher->allotments[] = (object) [
                                    'amount' => $amount
                                ];
                            }
                        } else {
                            foreach ($allotmentHeaders as $headCtr => $allotHead) {
                                $voucher->allotments[] = (object) [
                                    'amount' => 0
                                ];
                            }
                        }

                        $vouchers[] = $voucher;
                    }
                }

                $viewFile = 'modules.report.obligation-ledger.update-saa';
            }
        } else {
            $vouchers = [];
            $_vouchers = DB::table('disbursement_vouchers as dv')->select(
                DB::raw("DATE_FORMAT(dv.date_disbursed, '%Y-%m-%d') as date_disbursed"),
                'dv.id', 'dv.payee', 'dv.particulars', 'dv.amount', 'dv.uacs_object_code',
                'dv.module_class', 'dv.pr_id', 'ors.serial_no','ors.id as ors_id',
                'ledger.id as ledger_item_id', 'ledger.particulars as ledger_particulars',
                'ledger.ors_no', 'ledger.prior_year', 'ledger.continuing', 'ledger.current',
                'ledger.total', 'ledger.mooe_account as mooe_account',
                'ledger.unit as ledger_unit', 'ledger.ledger_id', 'dv.prior_year as dv_prior_year',
                'dv.continuing as dv_continuing', 'dv.current as dv_current'
            )->leftJoin('obligation_request_status as ors', 'ors.id', '=', 'dv.ors_id')
             ->leftJoin('funding_ledger_items as ledger', 'ledger.dv_id', '=', 'dv.id')
             ->where('dv.funding_source', $projectID)
             ->whereNotNull('date_disbursed')
             ->orderBy('dv.date_disbursed')
             ->get();
            $approvedBudgets = [
                (object) [
                    'label' => 'LIB',
                    'total' => $libData->approved_budget
                ]
            ];

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => 'Realignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                foreach ($_vouchers as $voucher) {
                    if ($voucher->ledger_id == $id) {
                        if ($voucher->ledger_item_id) {
                            $voucher->allotments = [];
                            $ledgerAllotments = FundingLedgerAllotment::where(
                                'ledger_item_id', $voucher->ledger_item_id
                            )->get();

                            foreach ($allotmentHeaders as $allotHead) {
                                $amount = 0;

                                foreach ($ledgerAllotments as $allot) {
                                    if ($allot->allotment_id == $allotHead->allotment_id) {
                                        if (!empty($allot->allotment_id) && !empty($allotHead->allotment_id)) {
                                            $amount = $allot->current_cost;
                                            break;
                                        }
                                    }

                                    if ($allot->realign_allotment_id == $allotHead->realign_allotment_id) {
                                        if (!empty($allot->realign_allotment_id) && !empty($allotHead->realign_allotment_id)) {
                                            $amount = $allot->current_cost;
                                            break;
                                        }
                                    }
                                }

                                $voucher->allotments[] = (object) [
                                    'amount' => $amount
                                ];
                            }
                        } else {
                            foreach ($allotmentHeaders as $headCtr => $allotHead) {
                                $voucher->allotments[] = (object) [
                                    'amount' => 0
                                ];
                            }
                        }
                    }

                    $vouchers[] = $voucher;
                }

                $viewFile = 'modules.report.disbursement-ledger.update-saa';
            } else if ($type == 'mooe') {
                foreach ($_vouchers as $voucher) {
                    if ($voucher->ledger_id == $id) {
                        $voucher->uacs_object_code = unserialize($voucher->uacs_object_code);
                        $voucher->mooe_account = $voucher->mooe_account ? unserialize($voucher->mooe_account) : [];

                        if ($voucher->module_class == 3) {
                            $prID = $voucher->pr_id;
                            $prDat = PurchaseRequest::find($prID);
                            $userID = $prDat->requested_by;
                            $userDat = User::find($userID);
                            $voucher->unit = $userDat->unit;
                        } else {
                            $userID = $voucher->payee;
                            $userDat = User::find($userID);
                            $voucher->unit = $userDat->unit;
                        }

                        $vouchers[] = $voucher;
                    }
                }

                $viewFile = 'modules.report.disbursement-ledger.update-mooe';
            } else if ($type == 'lgia') {
                foreach ($_vouchers as $voucher) {
                    if ($voucher->ledger_id == $id) {
                        $vouchers[] = $voucher;
                    }
                }

                $viewFile = 'modules.report.disbursement-ledger.update-lgia';
            } else if ($type == 'setup') {
                foreach ($_vouchers as $voucher) {
                    if ($voucher->ledger_id == $id) {
                        $vouchers[] = $voucher;
                    }
                }

                $viewFile = 'modules.report.disbursement-ledger.update-setup';
            }
        }

        return view($viewFile, compact(
            'allotments', 'classItemCounts', 'approvedBudgets', 'isRealignment',
            'vouchers', 'itemCounter', 'allotmentCounter', 'payees', 'id',
            'empUnits', 'mooeTitles', 'projectTitle', 'allotmentHeaders'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $for, $type) {
        if ($for == 'obligation') {
            $documentType = 'Obligation Ledger';
            $routeName = 'report-obligation-ledger';

            if ($type == 'saa') {
                $allotmentIDs = $request->allotment_id;
                $allotmentRealignIDs = $request->allot_realign_id;
                $dateORS = $request->date_ors_burs;
                $payees = $request->payee;
                $particulars = $request->particular;
                $orsIDs = $request->ors_id;
                $ledgerItemIDs = $request->ledger_item_id;
                $orsNos = $request->ors_no;
                $totals = $request->amount;
                $allotments = $request->allotment;
            }
        } else {
            $documentType = 'Disbursement Ledger';
            $routeName = 'report-disbursement-ledger';

            if ($type == 'saa') {
                $allotmentIDs = $request->allotment_id;
                $allotmentRealignIDs = $request->allot_realign_id;
                $dateDVs = $request->date_dv;
                $orsIDs = $request->ors_id;
                $dvIDs = $request->dv_id;
                $ledgerIDs = $request->ledger_id;
                $orsNos = $request->ors_no;
                $payees = $request->payee;
                $particulars = $request->particular;
                $allotments = $request->allotment;
                $totals = $request->amount;
            } else if ($type == 'mooe') {
                $dateDVs = $request->date_dv;
                $orsIDs = $request->ors_id;
                $dvIDs = $request->dv_id;
                $ledgerIDs = $request->ledger_id;
                $orsNos = $request->ors_no;
                $payees = $request->payee;
                $particulars = $request->particular;
                $mooeObjectCodes = $request->uacs_object_code;
                $priorYears = $request->prior_year;
                $continuings = $request->continuing;
                $currents = $request->current;
                $units = $request->unit;
            } else if ($type == 'lgia') {
                $dateDVs = $request->date_dv;
                $orsIDs = $request->ors_id;
                $dvIDs = $request->dv_id;
                $ledgerIDs = $request->ledger_id;
                $orsNos = $request->ors_no;
                $payees = $request->payee;
                $particulars = $request->particular;
                $priorYears = $request->prior_year;
                $continuings = $request->continuing;
                $currents = $request->current;
                $totals = $request->amount;
            }
        }

        try {
            $instanceLedger = FundingLedger::find($id);
            $projectID = $instanceLedger->project_id;
            $budgetID = $instanceLedger->budget_id;
            $instanceLedger->ledger_for = $for;
            $instanceLedger->ledger_type = $type;
            $instanceLedger->save();

            $ledgerItems = DB::table('funding_ledger_items')
                             ->where('ledger_id', $id)
                             ->get();

            foreach ($ledgerItems as $item) {
                FundingLedgerAllotment::where('ledger_item_id', $item->id)
                                      ->delete();
                FundingLedgerItem::destroy($item->id);

            }

            $orderNo = 1;

            if ($for == 'obligation') {
                foreach ($orsIDs as $orsCtr => $orsID) {
                    if ($type == 'saa') {
                        $instanceLedgerItem = new FundingledgerItem;
                        $instanceLedgerItem->project_id = $projectID;
                        $instanceLedgerItem->budget_id = $budgetID;
                        $instanceLedgerItem->ledger_id = $id;
                        $instanceLedgerItem->ors_id = $orsID;
                        $instanceLedgerItem->date_ors_dv = $dateORS[$orsCtr];
                        $instanceLedgerItem->ors_no = $orsNos[$orsCtr];
                        $instanceLedgerItem->payee = $payees[$orsCtr];
                        $instanceLedgerItem->particulars = $particulars[$orsCtr];
                        $instanceLedgerItem->total = $totals[$orsCtr];
                        $instanceLedgerItem->order_no = $orderNo;
                        $instanceLedgerItem->save();

                        $ledgerItemDat = DB::table('funding_ledger_items')
                                           ->where([
                                               ['order_no', $orderNo],
                                               ['ledger_id', $id]
                                            ])->first();
                        $ledgerItemID = $ledgerItemDat->id;

                        foreach ($allotments[$orsCtr] as $allotCtr => $total) {
                            if ((double) $total > 0) {
                                $instanceLedgerAllotments = new FundingLedgerAllotment;
                                $instanceLedgerAllotments->project_id = $projectID;
                                $instanceLedgerAllotments->budget_id = $budgetID;
                                $instanceLedgerAllotments->ledger_id = $id;
                                $instanceLedgerAllotments->ledger_item_id = $ledgerItemID;
                                $instanceLedgerAllotments->allotment_id = $allotmentIDs[$allotCtr];
                                $instanceLedgerAllotments->realign_allotment_id = $allotmentRealignIDs[$allotCtr];
                                $instanceLedgerAllotments->current_cost = $total;
                                $instanceLedgerAllotments->save();
                            }
                        }

                        $orderNo++;
                    }
                }
            } else {
                foreach ($dvIDs as $dvCtr => $dvID) {
                    if ($type == 'saa') {
                        $instanceLedgerItem = new FundingledgerItem;
                        $instanceLedgerItem->project_id = $projectID;
                        $instanceLedgerItem->budget_id = $budgetID;
                        $instanceLedgerItem->ledger_id = $id;
                        $instanceLedgerItem->date_ors_dv = $dateDVs[$dvCtr];
                        $instanceLedgerItem->ors_id = $orsIDs[$dvCtr];
                        $instanceLedgerItem->dv_id = $dvID;
                        $instanceLedgerItem->ors_no = $orsNos[$dvCtr];
                        $instanceLedgerItem->payee = $payees[$dvCtr];
                        $instanceLedgerItem->particulars = $particulars[$dvCtr];
                        $instanceLedgerItem->total = $totals[$dvCtr];
                        $instanceLedgerItem->order_no = $orderNo;
                        $instanceLedgerItem->save();

                        $ledgerItemDat = DB::table('funding_ledger_items')
                                           ->where([
                                               ['order_no', $orderNo],
                                               ['ledger_id', $id]
                                            ])->first();
                        $ledgerItemID = $ledgerItemDat->id;

                        foreach ($allotments[$dvCtr] as $allotCtr => $total) {
                            if ((double) $total > 0) {
                                $instanceLedgerAllotments = new FundingLedgerAllotment;
                                $instanceLedgerAllotments->project_id = $projectID;
                                $instanceLedgerAllotments->budget_id = $budgetID;
                                $instanceLedgerAllotments->ledger_id = $id;
                                $instanceLedgerAllotments->ledger_item_id = $ledgerItemID;
                                $instanceLedgerAllotments->allotment_id = $allotmentIDs[$allotCtr];
                                $instanceLedgerAllotments->realign_allotment_id = $allotmentRealignIDs[$allotCtr];
                                $instanceLedgerAllotments->current_cost = $total;
                                $instanceLedgerAllotments->save();
                            }
                        }

                        $orderNo++;
                    } else if ($type == 'mooe') {
                        $instanceLedgerItem = new FundingledgerItem;
                        $instanceLedgerItem->project_id = $projectID;
                        $instanceLedgerItem->budget_id = $budgetID;
                        $instanceLedgerItem->ledger_id = $id;
                        $instanceLedgerItem->date_ors_dv = $dateDVs[$dvCtr];
                        $instanceLedgerItem->ors_id = $orsIDs[$dvCtr];
                        $instanceLedgerItem->dv_id = $dvID;
                        $instanceLedgerItem->ors_no = $orsNos[$dvCtr];
                        $instanceLedgerItem->payee = $payees[$dvCtr];
                        $instanceLedgerItem->particulars = $particulars[$dvCtr];
                        $instanceLedgerItem->mooe_account = serialize($mooeObjectCodes[$dvCtr]);
                        $instanceLedgerItem->prior_year = $priorYears[$dvCtr];
                        $instanceLedgerItem->continuing = $continuings[$dvCtr];
                        $instanceLedgerItem->current = $currents[$dvCtr];
                        $instanceLedgerItem->unit = $units[$dvCtr] != '-' ? $units[$dvCtr] : NULL;
                        $instanceLedgerItem->order_no = $orderNo;
                        $instanceLedgerItem->save();

                        $orderNo++;
                    } else if ($type == 'lgia') {
                        $instanceLedgerItem = new FundingledgerItem;
                        $instanceLedgerItem->project_id = $projectID;
                        $instanceLedgerItem->budget_id = $budgetID;
                        $instanceLedgerItem->ledger_id = $id;
                        $instanceLedgerItem->date_ors_dv = $dateDVs[$dvCtr];
                        $instanceLedgerItem->ors_id = $orsIDs[$dvCtr];
                        $instanceLedgerItem->dv_id = $dvID;
                        $instanceLedgerItem->ors_no = $orsNos[$dvCtr];
                        $instanceLedgerItem->payee = $payees[$dvCtr];
                        $instanceLedgerItem->particulars = $particulars[$dvCtr];
                        $instanceLedgerItem->prior_year = $priorYears[$dvCtr];
                        $instanceLedgerItem->continuing = $continuings[$dvCtr];
                        $instanceLedgerItem->current = $currents[$dvCtr];
                        $instanceLedgerItem->total = $totals[$dvCtr];
                        $instanceLedgerItem->order_no = $orderNo;
                        $instanceLedgerItem->save();

                        $orderNo++;
                    }
                }
            }

            $msg = "$documentType successfully updated.";
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
     * Soft deletes the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id, $for) {
        $isDestroy = $request->destroy;
        $routeName = $for == 'obligation' ? 'report-obligation-ledger' :
                             'report-disbursement-ledger';

        if ($isDestroy) {
            $response = $this->destroy($request, $id, $for);

            if ($response->alert_type == 'success') {
                return redirect()->route($routeName)
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route($routeName)
                                 ->with($response->alert_type, $response->msg);
            }
        } else {
            try {
                $instanceLedger = FundingLedger::find($id);
                $documentType = $for == 'obligation' ? 'Obligation Ledger' :
                                'Disbursement';
                $instanceLedger->delete();

                $msg = "$documentType '$id' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName)
                                 ->with('success', $msg);
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName)
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
    public function destroy($request, $id, $for) {
        try {
            $ledgerItems = DB::table('funding_ledger_items')
                             ->where('ledger_id', $id)
                             ->get();

            foreach ($ledgerItems as $item) {
                FundingLedgerAllotment::where('ledger_item_id', $item->id)
                                      ->delete();
                FundingLedgerItem::destroy($item->id);

            }

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $instanceLedger = FundingLedger::find($id);
            $instanceLedger->forceDelete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $documentType = $for == 'obligation' ? 'Obligation Ledger' :
                            'Disbursement';

            $msg = "$documentType '$id' permanently deleted.";
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

    public function getPayees(Request $request) {
        $keyword = trim($request->search);

        $payees = [];
        $empPayees = User::select('id', 'firstname', 'lastname');
        $supplierPayees = Supplier::select('id', 'company_name');

        if ($keyword) {
            $empPayees = $empPayees->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('firstname', 'like', "%$keyword%")
                    ->orWhere('lastname', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('firstname', 'like', "%$tag%")
                            ->orWhere('lastname', 'like', "%$tag%");
                    }
                }
            });

            $supplierPayees = $supplierPayees->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('company_name', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('company_name', 'like', "%$tag%");
                    }
                }
            });
        }

        $empPayees = $empPayees->orderBy('firstname')->get();
        $supplierPayees = $supplierPayees->orderBy('company_name')->get();

        foreach ($empPayees as $emp) {
            $payees[] = (object) [
                'id' => $emp->id,
                'name' => $emp->firstname.' '.$emp->lastname
            ];
        }

        foreach ($supplierPayees as $bid) {
            $payees[] = (object) [
                'id' => $bid->id,
                'name' => $bid->company_name
            ];
        }

        return response()->json($payees);
    }

    public function getUnits(Request $request) {
        $keyword = trim($request->search);

        $units = [];
        $empUnits = EmpUnit::select('id', 'unit_name');

        if ($keyword) {
            $empUnits = $empUnits->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('unit_name', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('unit_name', 'like', "%$tag%");
                    }
                }
            });
        }

        $empUnits = $empUnits->orderBy('unit_name')->get();

        foreach ($empUnits as $unit) {
            $units[] = (object) [
                'id' => $unit->id,
                'name' => $unit->unit_name
            ];
        }

        return response()->json($units);
    }

    public function getMooeTitles(Request $request) {
        $keyword = trim($request->search);

        $mooes = [];
        $mooeTitles = MooeAccountTitle::select('id', 'uacs_code', 'account_title',
                                               'order_no');

        if ($keyword) {
            $mooeTitles = $mooeTitles->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('uacs_code', 'like', "%$keyword%")
                    ->orWhere('account_title', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('uacs_code', 'like', "%$tag%")
                            ->orWhere('account_title', 'like', "%$tag%");
                    }
                }
            });
        }

        $mooeTitles = $mooeTitles->orderBy('order_no')->get();

        foreach ($mooeTitles as $mooe) {
            $mooes[] = (object) [
                'id' => $mooe->id,
                'name' => $mooe->account_title,
                'uacs_code' => $mooe->uacs_code,
            ];
        }

        return response()->json($mooes);
    }

    public function import(Request $request) {
        $file = $request->file('file_import');
        $rows = Excel::ToArray (new LedgerController, $file);
        //WithCalculatedFormulas::calculate(false);

        dd($rows);

        foreach ($array as $row) {
            //dd($row->calculate(false));
        }
    }

    private function groupAllotments($allotments, $isRealignment = false, $currRealignData = NULL) {
        $groupedAllotments = [];
        $classItemCounts = [];
        $allotmentHeaders = [];
        $realignOrder = $currRealignData->realignment_order;
        $allotClassData = DB::table('allotment_classes')
                            ->orderBy('order_no')
                            ->get();

        if ($isRealignment) {
            $budgetID = $currRealignData->budget_id;
            $currRealignID = $currRealignData->id;

            $currRealignAllotments = DB::table('funding_allotment_realignments')
                                       ->where('budget_realign_id', $currRealignID)
                                       ->orderBy('order_no')
                                       ->get();
            $_allotments = [];

            foreach ($currRealignAllotments as $realignAllotCtr => $realignAllot) {
                $_allotments[$realignAllotCtr] = (object) [
                    'uacs_id' => $realignAllot->uacs_id,
                    'allotment_class' => $realignAllot->allotment_class,
                    'allotment_name' => $realignAllot->allotment_name,
                    "realignment_$realignOrder" => (object) [
                        'allotment_cost' => $realignAllot->realigned_allotment_cost,
                    ],
                ];

                if ($realignAllot->allotment_id) {
                    $allotmentData = DB::table('funding_allotments')
                                       ->where('id', $realignAllot->allotment_id)
                                       ->first();
                    $_allotments[$realignAllotCtr]->allotment_id = $allotmentData->id;
                    $_allotments[$realignAllotCtr]->uacs_id = $allotmentData->uacs_id;
                    $_allotments[$realignAllotCtr]->allotment_cost = $allotmentData->allotment_cost;
                    $allotCoimplementers = unserialize($allotmentData->coimplementers);

                    foreach ($allotCoimplementers as $coimp) {
                        $_allotments[$realignAllotCtr]->allotment_cost += $coimp['coimplementor_budget'];
                    }
                } else {
                    $_allotments[$realignAllotCtr]->allotment_id = NULL;
                    $_allotments[$realignAllotCtr]->uacs_id = NULL;
                    $_allotments[$realignAllotCtr]->allotment_cost = 0;
                }

                for ($realignOrderCtr = 1; $realignOrderCtr <= $realignOrder; $realignOrderCtr++) {
                    $realignIndex = "realignment_$realignOrderCtr";

                    $budgetRealignData = DB::table('funding_budget_realignments')
                                        ->where([
                                            ['budget_id', $budgetID],
                                            ['realignment_order', $realignOrderCtr],
                                        ])->first();
                    $realignID = $budgetRealignData->id;
                    $realignAllotmentData = DB::table('funding_allotment_realignments')
                                              ->where('budget_realign_id', $realignID)
                                              ->orderBy('order_no')
                                              ->get();
                    $hasRealignAllot = false;

                    foreach ($realignAllotmentData as $rAllotCtr => $rAllot) {
                        if (strtolower(trim($realignAllot->allotment_name)) == strtolower(trim($rAllot->allotment_name)) ||
                            ($realignAllot->allotment_id == $rAllot->allotment_id &&
                            !empty($realignAllot->allotment_id) && !empty($realignAllot->allotment_id))) {
                            $coimplementers = unserialize($rAllot->coimplementers);

                            $_allotments[$realignAllotCtr]->{$realignIndex} = (object) [
                                'allotment_id' => $rAllot->allotment_id,
                                'uacs_id' => $rAllot->uacs_id,
                                'allotment_realign_id' => $rAllot->id,
                                'allotment_cost' => $rAllot->realigned_allotment_cost,
                            ];

                            foreach ($coimplementers as $coimp) {
                                $_allotments[$realignAllotCtr]->{$realignIndex}->allotment_cost += $coimp['coimplementor_budget'];
                            }

                            $hasRealignAllot = true;
                            break;
                        }
                    }

                    if (!$hasRealignAllot) {
                        $_allotments[$realignAllotCtr]->{$realignIndex} = (object) [
                            'allotment_id' => NULL,
                            'allotment_realign_id' => NULL,
                            'allotment_cost' => 0,
                        ];
                    }
                }
            }

            $allotments = $_allotments;
        }

        foreach ($allotClassData as $class) {
            $keyClass = preg_replace("/\s+/", "-", $class->code);
            $classItemCounts[$keyClass] = 0;

            foreach ($allotments as $itmCtr => $item) {
                if ($class->id == $item->allotment_class) {
                    if (!$isRealignment) {
                        $coimplementers = unserialize($item->coimplementers);

                        foreach ($coimplementers as $coimp) {
                            $item->allotment_cost += $coimp['coimplementor_budget'];
                        }

                        $allotmentHeaders[] = (object) [
                            'allotment_id' => $item->id,
                            'realign_allotment_id' => NULL,
                            'allotment_name' => $item->allotment_name,
                            'allotment_cost' => $item->allotment_cost,
                        ];
                    } else {
                        $realignKey = "realignment_$realignOrder";
                        $allotmentHeaders[] = (object) [
                            'allotment_id' => $item->{$realignKey}->allotment_id,
                            'realign_allotment_id' => $item->{$realignKey}->allotment_realign_id,
                            'allotment_name' => $item->allotment_name,
                            'allotment_cost' => $item->{$realignKey}->allotment_cost,
                        ];
                    }

                    if (count(explode('::', $item->allotment_name)) > 1) {
                        $keyAllotment = preg_replace(
                            "/\s+/", "-", explode('::', $item->allotment_name)[0]
                        );
                        $groupedAllotments[$keyClass][$keyAllotment][] = $item;
                        $classItemCounts[$keyClass]++;
                    } else {
                        $groupedAllotments[$keyClass][$itmCtr + 1] = $item;
                        $classItemCounts[$keyClass]++;
                    }
                }
            }
        }

        return (object) [
            'allotment_headers' => $allotmentHeaders,
            'grouped_allotments' => $groupedAllotments,
            'class_item_counts' => $classItemCounts
        ];
    }

    private function convertToOrdinal($number) {
        $suffix = [
            'th',
            'st',
            'nd',
            'rd',
            'th',
            'th',
            'th',
            'th',
            'th',
            'th'
        ];

        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number. 'th';
        } else {
            return $number. $suffix[$number % 10];
        }
    }

    private function groupVouchersByMonth($id, $projectID, $for, $type, $allotmentHeaders, $currentBudget) {
        $groupedVouchers = [];
        $monthDates = [];
        $_monthDates = [];
        $remainings = [];
        $remaining = $currentBudget;
        $totals = [];
        $total = 0;
        $totalPriorYear = 0;
        $totalContinuing = 0;
        $totalCurrent = 0;

        if ($for == 'obligation') {
            $__vouchers = DB::table('obligation_request_status as ors')->select(
                DB::raw("DATE_FORMAT(ors.date_obligated, '%Y-%m-%d') as date_obl_dis")
            )->leftJoin('funding_ledger_items as ledger', 'ledger.ors_id', '=', 'ors.id')
             ->where([['funding_source', $projectID], ['ledger.ledger_id', $id]])
             ->whereNotNull('date_obligated')
             ->orderBy('date_obligated')
             ->get();
        } else {
            $__vouchers = DB::table('disbursement_vouchers as dv')->select(
                DB::raw("DATE_FORMAT(dv.date_disbursed, '%Y-%m-%d') as date_obl_dis"),
                'ledger.unit as ledger_unit', 'ledger.id as ledger_id'
            )->leftJoin('obligation_request_status as ors', 'ors.id', '=', 'dv.ors_id')
             ->leftJoin('funding_ledger_items as ledger', 'ledger.dv_id', '=', 'dv.id')
             ->where([['dv.funding_source', $projectID], ['ledger.ledger_id', $id]])
             ->whereNotNull('date_disbursed')
             ->orderBy('date_disbursed')
             ->get();
        }

        foreach ($allotmentHeaders as $allotHead) {
            $totals[] = 0;
            $remainings[] = $allotHead->allotment_cost;
        }

        foreach ($__vouchers as $voucher) {
            $monthDate = date("m-Y", strtotime($voucher->date_obl_dis));
            $monthlabel = date("F", strtotime($voucher->date_obl_dis));

            if (count($monthDates) > 0) {
                $_monthDates[] = $monthDate;
            } else if (count($monthDates) == 0) {
                $_monthDates[] = $monthDate;
            }
        }

        if (count($__vouchers) > 0) {
            $fromMonthDate = explode('-', $_monthDates[0]);
            $toMonthDate = explode('-', $_monthDates[count($_monthDates) - 1]);

            for ($year = $fromMonthDate[1]; $year <= $toMonthDate[1]; $year++) {
                $fromMonth = 1;
                $toMonth = 12;

                if ($year == $toMonthDate[1]) {
                    $toMonth = $toMonthDate[0];
                }

                if ($year == $fromMonthDate[1]) {
                    $fromMonth = $fromMonthDate[0];
                }

                for ($month = $fromMonth; $month <= $toMonth ; $month++) {
                    $monthDate = date("Y-m", strtotime("$year-$month-01"));
                    $monthlabel = date("F", strtotime("$year-$month-01"));

                    $monthDates[] = (object) [
                        'year' => $year,
                        'month_date' => $monthDate,
                        'month_label' => $monthlabel
                    ];
                }
            }

            foreach ($monthDates as $moCtr => $moDate) {
                $monthTotals = [];
                $year = $moDate->year;
                $monthDate = $moDate->month_date;
                $monthlabel = $moDate->month_label;
                $groupedVouchers[$moCtr] = (object) [
                    'month_date' => $monthDate,
                    'month_label' => "$monthlabel",
                    'month_prior_year' => 0,
                    'month_continuing' => 0,
                    'month_current' => 0,
                    'month_total' => 0,
                    'total_prior_year' => $totalPriorYear,
                    'total_continuing' => $totalContinuing,
                    'total_current' => $totalCurrent,
                    'total' => $total,
                    'remaining' => $remaining
                ];

                foreach ($allotmentHeaders as $allotHead) {
                    $monthTotals[] = 0;
                }

                if ($for == 'obligation') {
                    $groupedVouchers[$moCtr]->vouchers = [];
                    $vouchers = [];
                    $_vouchers = DB::table('obligation_request_status as ors')->select(
                        DB::raw("DATE_FORMAT(ors.date_obligated, '%Y-%m-%d') as date_obligated"),
                        'ors.id', 'ors.payee', 'ors.particulars', 'ors.serial_no', 'ors.amount',
                        'ledger.id as ledger_item_id', 'ledger.particulars as ledger_particulars',
                        'ledger.ors_no', 'ledger.ledger_id'
                    )->leftJoin('funding_ledger_items as ledger', 'ledger.ors_id', '=', 'ors.id')
                    ->where([
                        ['funding_source', $projectID],
                        ['date_obligated', 'like', "$monthDate%"]
                    ])->whereNotNull('date_obligated')
                    ->orderBy('ors.date_obligated')
                    ->get();

                    if ($type == 'saa') {
                        foreach ($_vouchers as $voucher) {
                            if ($voucher->ledger_id == $id) {
                                $total += $voucher->amount;
                                $remaining -= $voucher->amount;

                                $groupedVouchers[$moCtr]->month_total += $voucher->amount;
                                $groupedVouchers[$moCtr]->total = $total;
                                $groupedVouchers[$moCtr]->remaining = $remaining;

                                if ($voucher->ledger_item_id) {
                                    $voucher->allotments = [];
                                    $ledgerAllotments = FundingLedgerAllotment::where([
                                        ['ledger_id', $id],
                                        ['ledger_item_id', $voucher->ledger_item_id]
                                    ])->get();

                                    foreach ($allotmentHeaders as $headCtr => $allotHead) {
                                        $amount = 0;

                                        foreach ($ledgerAllotments as $allot) {
                                            if ($allot->allotment_id == $allotHead->allotment_id) {
                                                if (!empty($allot->allotment_id) && !empty($allotHead->allotment_id)) {
                                                    $amount = $allot->current_cost;
                                                    break;
                                                }
                                            }

                                            if ($allot->realign_allotment_id == $allotHead->realign_allotment_id) {
                                                if (!empty($allot->realign_allotment_id) && !empty($allotHead->realign_allotment_id)) {
                                                    $amount = $allot->current_cost;
                                                    break;
                                                }
                                            }
                                        }

                                        $monthTotals[$headCtr] += $amount;
                                        $totals[$headCtr] += $amount;
                                        $remainings[$headCtr] -= $amount;

                                        $voucher->allotments[] = (object) [
                                            'amount' => $amount
                                        ];
                                    }
                                } else {
                                    foreach ($allotmentHeaders as $headCtr => $allotHead) {
                                        $voucher->allotments[] = (object) [
                                            'amount' => 0
                                        ];
                                    }
                                }

                                $vouchers[] = $voucher;
                            }
                        }

                        $groupedVouchers[$moCtr]->vouchers = $vouchers;
                        $groupedVouchers[$moCtr]->month_totals = $monthTotals;
                        $groupedVouchers[$moCtr]->totals = $totals;
                        $groupedVouchers[$moCtr]->remainings = $remainings;
                    }
                } else {
                    $groupedVouchers[$moCtr]->vouchers = [];
                    $vouchers = [];
                    $_vouchers = DB::table('disbursement_vouchers as dv')->select(
                        DB::raw("DATE_FORMAT(dv.date_disbursed, '%Y-%m-%d') as date_disbursed"),
                        'dv.id', 'dv.payee', 'dv.particulars', 'dv.amount', 'dv.uacs_object_code',
                        'dv.module_class', 'dv.pr_id', 'ors.serial_no','ors.id as ors_id',
                        'ledger.id as ledger_item_id', 'ledger.particulars as ledger_particulars',
                        'ledger.ors_no', 'ledger.prior_year', 'ledger.continuing', 'ledger.current',
                        'ledger.total', 'ledger.mooe_account as mooe_account',
                        'ledger.unit as ledger_unit', 'ledger.ledger_id'
                    )->leftJoin('obligation_request_status as ors', 'ors.id', '=', 'dv.ors_id')
                    ->leftJoin('funding_ledger_items as ledger', 'ledger.dv_id', '=', 'dv.id')
                    ->where([
                        ['dv.funding_source', $projectID],
                        ['dv.date_disbursed', 'like', "$monthDate%"]
                    ])->whereNotNull('date_disbursed')
                    ->orderBy('dv.date_disbursed')
                    ->get();

                    if ($type == 'saa') {
                        foreach ($_vouchers as $voucher) {
                            if ($voucher->ledger_id == $id) {
                                $total += $voucher->amount;
                                $remaining -= $voucher->amount;

                                $groupedVouchers[$moCtr]->month_total += $voucher->amount;
                                $groupedVouchers[$moCtr]->total = $total;
                                $groupedVouchers[$moCtr]->remaining = $remaining;

                                if ($voucher->ledger_item_id) {
                                    $voucher->allotments = [];
                                    $ledgerAllotments = FundingLedgerAllotment::where([
                                        ['ledger_id', $id],
                                        ['ledger_item_id', $voucher->ledger_item_id]
                                    ])->get();

                                    foreach ($allotmentHeaders as $headCtr => $allotHead) {
                                        $amount = 0;

                                        foreach ($ledgerAllotments as $allot) {
                                            if ($allot->allotment_id == $allotHead->allotment_id) {
                                                if (!empty($allot->allotment_id) && !empty($allotHead->allotment_id)) {
                                                    $amount = $allot->current_cost;
                                                    break;
                                                }
                                            }

                                            if ($allot->realign_allotment_id == $allotHead->realign_allotment_id) {
                                                if (!empty($allot->realign_allotment_id) && !empty($allotHead->realign_allotment_id)) {
                                                    $amount = $allot->current_cost;
                                                    break;
                                                }
                                            }
                                        }

                                        $monthTotals[$headCtr] += $amount;
                                        $totals[$headCtr] += $amount;
                                        $remainings[$headCtr] -= $amount;

                                        $voucher->allotments[] = (object) [
                                            'amount' => $amount
                                        ];
                                    }
                                } else {
                                    foreach ($allotmentHeaders as $headCtr => $allotHead) {
                                        $voucher->allotments[] = (object) [
                                            'amount' => 0
                                        ];
                                    }
                                }

                                $vouchers[] = $voucher;
                            }
                        }

                        $groupedVouchers[$moCtr]->vouchers = $vouchers;
                        $groupedVouchers[$moCtr]->month_totals = $monthTotals;
                        $groupedVouchers[$moCtr]->totals = $totals;
                        $groupedVouchers[$moCtr]->remainings = $remainings;
                    } else if ($type == 'mooe') {
                        foreach ($_vouchers as $voucher) {
                            if ($voucher->ledger_id == $id) {
                                $total += $voucher->amount;
                                $groupedVouchers[$moCtr]->month_total += $voucher->amount;
                                $groupedVouchers[$moCtr]->total += $total;

                                $voucher->uacs_object_code = unserialize($voucher->uacs_object_code);
                                $voucher->mooe_account = $voucher->mooe_account ? unserialize($voucher->mooe_account) : [];

                                $groupedVouchers[$moCtr]->month_prior_year += $voucher->prior_year;
                                $groupedVouchers[$moCtr]->month_continuing += $voucher->continuing;
                                $groupedVouchers[$moCtr]->month_current += $voucher->current;

                                if ($voucher->module_class == 3) {
                                    $prID = $voucher->pr_id;
                                    $prDat = PurchaseRequest::find($prID);
                                    $userID = $prDat->requested_by;
                                    $userDat = User::find($userID);
                                    $voucher->unit = $userDat->unit;
                                } else {
                                    $userID = $voucher->payee;
                                    $userDat = User::find($userID);
                                    $voucher->unit = $userDat->unit;
                                }

                                $vouchers[] = $voucher;
                            }
                        }

                        $groupedVouchers[$moCtr]->vouchers = $vouchers;
                    } else if ($type == 'lgia' || $type == 'setup') {
                        foreach ($_vouchers as $voucher) {
                            if ($voucher->ledger_id == $id) {
                                $totalPriorYear == $voucher->prior_year;
                                $totalContinuing += $voucher->continuing;
                                $totalCurrent += $voucher->current;
                                $total += $voucher->amount;

                                $groupedVouchers[$moCtr]->month_prior_year += $voucher->prior_year;
                                $groupedVouchers[$moCtr]->month_continuing += $voucher->continuing;
                                $groupedVouchers[$moCtr]->month_current += $voucher->current;
                                $groupedVouchers[$moCtr]->month_total += $voucher->amount;
                                $groupedVouchers[$moCtr]->total_prior_year = $totalPriorYear;
                                $groupedVouchers[$moCtr]->total_continuing = $totalContinuing;
                                $groupedVouchers[$moCtr]->total_current = $totalCurrent;
                                $groupedVouchers[$moCtr]->total = $total;

                                $vouchers[] = $voucher;
                            }
                        }

                        $groupedVouchers[$moCtr]->vouchers = $vouchers;
                    }
                }
            }
        } else {
            $groupedAllotments = [];
        }

        return $groupedVouchers;
    }
}
