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

use Carbon\Carbon;
use Auth;
use DB;

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

        return view('modules.report.obligation-ledger.index', [
            'list' => $fundProject,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
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

        return view('modules.report.disbursement-ledger.index', [
            'list' => $fundProject,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
        ]);
    }

    private function getIndexData($request, $for) {
        $keyword = trim($request->keyword);
        $userID = Auth::user()->id;

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();

        $fundProject = FundingProject::has('budget');

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
            } else {
                $project->project_type_name = 'Local Grants-In-Aid';
            }
        }

        return $fundProject;
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
                'id', 'payee', 'particulars', 'serial_no', 'amount'
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
                'dv.module_class', 'dv.pr_id', 'ors.serial_no','ors.id as ors_id'
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
                $dvIDs = $request->dv_id;
                $orsNos = $request->ors_no;
                $payees = $request->payee;
                $particulars = $request->particular;
                $allotments = $request->allotment;
                $totals = $request->amount;
            } else if ($type == 'mooe') {
                $dateDVs = $request->date_dv;
                $orsIDs = $request->ors_id;
                $dvIDs = $request->dv_id;
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

            $instanceLedger = new FundingLedger;
            $instanceLedger->project_id = $projectID;
            $instanceLedger->budget_id = $budgetID;
            $instanceLedger->ledger_for = $for;
            $instanceLedger->ledger_type = $type;
            $instanceLedger->save();

            $ledgerDat = FundingLedger::where('project_id', $projectID)->first();
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

                        $ledgerItemDat = FundingledgerItem::where('order_no', $orderNo)->first();
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

                        $ledgerItemDat = FundingledgerItem::where('order_no', $orderNo)->first();
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
                        $instanceLedgerItem->unit = $units[$dvCtr];
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
            $vouchers = DB::table('obligation_request_status as ors')->select(
                DB::raw("DATE_FORMAT(ors.date_obligated, '%Y-%m-%d') as date_obligated"),
                'ors.id', 'ors.payee', 'ors.particulars', 'ors.serial_no', 'ors.amount',
                'ledger.id as ledger_item_id', 'ledger.particulars as ledger_particulars'
            )->leftJoin('funding_ledger_items as ledger', 'ledger.ors_id', '=', 'ors.id')
             ->where('funding_source', $projectID)
             ->whereNotNull('date_obligated')
             ->orderBy('date_obligated')
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
                foreach ($vouchers as $voucher) {
                    if ($voucher->ledger_item_id) {
                        $voucher->allotments = [];
                        $ledgerAllotments = FundingLedgerAllotment::where(
                            'ledger_item_id', $voucher->ledger_item_id
                        )->get();

                        foreach ($ledgerAllotments as $allot) {
                            $voucher->allotments[] = (object) [
                                'allotment_id' => $allot->allotment_id,
                                'allotment_realign_id' => $allot->realign_allotment_id,
                                'amount' => $allot->current_cost
                            ];
                        }
                    } else {
                        $voucher->allotments = [];
                    }
                }

                dd($vouchers);

                $viewFile = 'modules.report.obligation-ledger.update-saa';
            }
        } else {
            /*
            $vouchers = DB::table('disbursement_vouchers as dv')->select(
                DB::raw("DATE_FORMAT(dv.date_disbursed, '%Y-%m-%d') as date_disbursed"),
                'dv.id', 'dv.payee', 'dv.particulars', 'dv.amount', 'dv.uacs_object_code',
                'dv.module_class', 'dv.pr_id', 'ors.serial_no','ors.id as ors_id'
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

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => 'Realignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                $viewFile = 'modules.report.disbursement-ledger.update-saa';
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

                $viewFile = 'modules.report.disbursement-ledger.update-mooe';
            } else if ($type == 'lgia') {
                $viewFile = 'modules.report.disbursement-ledger.update-lgia';
            }*/
        }

        return view($viewFile, compact(
            'allotments', 'classItemCounts', 'approvedBudgets', 'isRealignment',
            'vouchers', 'itemCounter', 'allotmentCounter', 'payees', 'id',
            'empUnits', 'mooeTitles', 'projectTitle'
        ));

        /*
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
                'id', 'payee', 'particulars', 'serial_no', 'amount'
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

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => $this->convertToOrdinal($realignCtr + 1) . ' Re-alignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                $viewFile = 'modules.report.obligation-ledger.update-saa';
            }
        } else {
            $vouchers = DB::table('disbursement_vouchers as dv')->select(
                DB::raw("DATE_FORMAT(dv.date_disbursed, '%Y-%m-%d') as date_disbursed"),
                'dv.id', 'dv.payee', 'dv.particulars', 'dv.amount', 'dv.uacs_object_code',
                'dv.module_class', 'dv.pr_id', 'ors.serial_no','ors.id as ors_id'
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

            foreach ($libRealignments as $realignCtr => $libRealign) {
                $approvedBudgets[] = (object) [
                    'label' => 'Realignment',
                    'total' => $libRealign->approved_realigned_budget];
            }

            if ($type == 'saa') {
                $viewFile = 'modules.report.disbursement-ledger.update-saa';
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

                $viewFile = 'modules.report.disbursement-ledger.update-mooe';
            } else if ($type == 'lgia') {
                $viewFile = 'modules.report.disbursement-ledger.update-lgia';
            }
        }

        return view($viewFile, compact(
            'allotments', 'classItemCounts', 'approvedBudgets', 'isRealignment',
            'vouchers', 'itemCounter', 'allotmentCounter', 'payees', 'id',
            'empUnits', 'mooeTitles', 'projectTitle'
        ));*/
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
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

    private function groupAllotments($allotments, $isRealignment = false, $currRealignData = NULL) {
        $groupedAllotments = [];
        $classItemCounts = [];
        $allotClassData = DB::table('allotment_classes')
                            ->orderBy('order_no')
                            ->get();

        if ($isRealignment) {
            $realignOrder = $currRealignData->realignment_order;
            $budgetID = $currRealignData->budget_id;
            $currRealignID = $currRealignData->id;

            $currRealignAllotments = DB::table('funding_allotment_realignments')
                                       ->where('budget_realign_id', $currRealignID)
                                       ->orderBy('order_no')
                                       ->get();

            $_allotments = [];

            foreach ($currRealignAllotments as $realignAllotCtr => $realignAllot) {
                $_allotments[$realignAllotCtr] = (object) [
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
                    $_allotments[$realignAllotCtr]->allotment_cost = $allotmentData->allotment_cost;
                    $allotCoimplementers = unserialize($allotmentData->coimplementers);

                    foreach ($allotCoimplementers as $coimp) {
                        $_allotments[$realignAllotCtr]->allotment_cost += $coimp['coimplementor_budget'];
                    }
                } else {
                    $_allotments[$realignAllotCtr]->allotment_id = NULL;
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
                            $realignAllot->allotment_id == $rAllot->allotment_id) {
                            $coimplementers = unserialize($rAllot->coimplementers);

                            $_allotments[$realignAllotCtr]->{$realignIndex} = (object) [
                                'allotment_id' => $rAllot->allotment_id,
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
            //$keyClass = preg_replace("/\s+/", "-", $class->class_name);
            $keyClass = preg_replace("/\s+/", "-", $class->code);
            $classItemCounts[$keyClass] = 0;

            foreach ($allotments as $itmCtr => $item) {
                if ($class->id == $item->allotment_class) {
                    if (!$isRealignment) {
                        $coimplementers = unserialize($item->coimplementers);

                        foreach ($coimplementers as $coimp) {
                            $item->allotment_cost += $coimp['coimplementor_budget'];
                        }
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
}
