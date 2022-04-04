<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FundingProject;
use App\Models\FundingBudget;
use App\Models\FundingAllotment;
use App\Models\AllotmentClass;
use App\Models\FundingBudgetRealignment;
use App\Models\FundingAllotmentRealignment;
use App\Models\MooeAccountTitle;
use App\Models\PaperSize;
use App\Models\Signatory;
use App\Models\EmpAccount as User;

use Carbon\Carbon;
use Auth;
use DB;

class LineItemBudgetController extends Controller
{
    public function index(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'fund_lib';
        $isAllowedCreateLIB = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdateLIB = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDeleteLIB = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroyLIB = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedApproveLIB = Auth::user()->getModuleAccess($module, 'approve');
        $isAllowedDisapproveLIB = Auth::user()->getModuleAccess($module, 'disapprove');

        $module = 'fund_librealign';
        $isAllowedCreateRealignLIB = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdateRealignLIB = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDeleteRealignLIB = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroyRealignLIB = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedApproveRealignLIB = Auth::user()->getModuleAccess($module, 'approve');
        $isAllowedDisapproveRealignLIB = Auth::user()->getModuleAccess($module, 'disapprove');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $fundBudget = $this->getIndexData($request);

        return view('modules.fund-utilization.fund-project-lib.index', [
            'list' => $fundBudget,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreateLIB,
            'isAllowedUpdate' => $isAllowedUpdateLIB,
            'isAllowedDelete' => $isAllowedDeleteLIB,
            'isAllowedDestroy' => $isAllowedDestroyLIB,
            'isAllowedApprove' => $isAllowedApproveLIB,
            'isAllowedDisapprove' => $isAllowedDisapproveLIB,
            'isAllowedCreateRealignLIB' => $isAllowedCreateRealignLIB,
            'isAllowedUpdateRealignLIB' => $isAllowedUpdateRealignLIB,
            'isAllowedDeleteRealignLIB' => $isAllowedDeleteRealignLIB,
            'isAllowedDestroyRealignLIB' => $isAllowedDestroyRealignLIB,
            'isAllowedApproveRealignLIB' => $isAllowedApproveRealignLIB,
            'isAllowedDisapproveRealignLIB' => $isAllowedDisapproveRealignLIB,
        ]);
    }

    public function indexReport(Request $request) {
        $keyword = trim($request->keyword);
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $fundBudget = $this->getIndexData($request);

        return view('modules.report.project-lib.index', [
            'list' => $fundBudget,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
        ]);
    }

    private function getIndexData($request) {
        $keyword = trim($request->keyword);
        $userID = Auth::user()->id;

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
        $fundBudget = FundingBudget::whereHas('project', function($query) {
            $query->whereNotNull('id');
        });

        if ($roleHasDeveloper || $roleHasRD || $roleHasARD || $roleHasPlanning || $roleHasBudget) {
        } else {
            $projectIDs = $projDat->getAccessibleProjects();

            $fundBudget = $fundBudget->where(function($qry) use ($userID, $projectIDs) {
                $qry->where('created_by', $userID)
                    ->orWhere('sig_submitted_by', $userID)
                    ->orWhereIn('project_id', $projectIDs);
            });
        }

        if (!empty($keyword)) {
            $fundBudget = $fundBudget->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('date_from', 'like', "%$keyword%")
                    ->orWhere('date_to', 'like', "%$keyword%")
                    ->orWhere('approved_budget', 'like', "%$keyword%")
                    ->orWhere('project_id', 'like', "%$keyword%");
            });
        }

        $fundBudget = $fundBudget->sortable(['created_at' => 'desc'])
                                 ->paginate(15);

        foreach ($fundBudget as $key => $budget) {
            $realignments = [];
            $budgetID = $budget->id;
            $realignedBudgets = DB::table('funding_budget_realignments')
                                  ->where('budget_id', $budgetID)
                                  ->orderBy('realignment_order')
                                  ->get();

            foreach ($realignedBudgets as $budRealign) {
                $budgetRealignID = $budRealign->id;
                $allotments = [];
                $realignedAllots = DB::table('funding_allotment_realignments as r_allot')
                                     ->select('allot.id as id', 'r_allot.allotment_name as allotment_name',
                                              'r_allot.realigned_allotment_cost as allotment_cost',
                                              'r_allot.allotment_class as allotment_class',
                                              'r_allot.coimplementers', 'r_allot.id as r_allot_id',
                                              'r_allot.justification', 'r_allot.allotment_id as allotment_id',
                                              'r_allot.order_no')
                                    ->leftJoin('funding_allotments as allot', 'allot.id', '=',
                                               'r_allot.allotment_id')
                                     ->where('r_allot.budget_realign_id', $budgetRealignID)
                                     ->orderBy('r_allot.order_no')
                                     ->get();

                foreach ($realignedAllots as $allotRealign) {
                    $allotmentCost = $allotRealign->allotment_cost;
                    $coimplementers = unserialize($allotRealign->coimplementers);

                    foreach ($coimplementers as $coimplementer) {
                        $allotmentCost += $coimplementer['coimplementor_budget'];
                    }

                    $allotments[] = (object) [
                        'id' => $allotRealign->id,
                        'allotment_id' => $allotRealign->allotment_id,
                        'budget_realign_id' => $budgetRealignID,
                        'allotment_class' => $allotRealign->allotment_class,
                        'allotment_name' => $allotRealign->allotment_name,
                        'order_no' => $allotRealign->order_no,
                        'allotment_cost' => $allotmentCost,
                    ];
                }

                $realignments[] = (object) [
                    'id' => $budRealign->id,
                    'date_disapproved' => $budRealign->date_disapproved,
                    'date_approved' => $budRealign->date_approved,
                    'realigned_budget' => $budRealign->approved_realigned_budget,
                    'realigned_allotments' => $allotments
                ];
            }

            $budget->realignments = (object) $realignments;
            $countRealignments = count($realignments);
            $budget->count_realignments = $countRealignments;
            $budget->current_budget = isset($realignments[$countRealignments - 2]) ?
                                      $realignments[$countRealignments - 2]->realigned_budget :
                                      $budget->approved_budget;
            $budget->current_realigned_budget = isset($realignments[$countRealignments - 1]) ?
                                                $realignments[$countRealignments - 1] :
                                                FundingBudget::find($budget->id);

            if (isset($realignments[$countRealignments - 1])) {
                $budget->current_realigned_allotments = $realignments[$countRealignments - 1]->realigned_allotments;
            } else {
                $fundAllotments = FundingAllotment::where('budget_id', $budget->id)
                                                  ->orderBy('order_no')
                                                  ->get();

                foreach ($fundAllotments as $allot) {
                    $coimplementers = unserialize($allot->coimplementers);

                    foreach ($coimplementers as $coimplementer) {
                        $allot->allotment_cost += $coimplementer['coimplementor_budget'];
                    }
                }

                $budget->current_realigned_allotments = $fundAllotments;
            }
        }

        return $fundBudget;
    }

    public function showPrint($id) {
        $realignments = FundingBudgetRealignment::where('budget_id', $id)
                                                ->orderBy('realignment_order')
                                                ->get();

        return view('modules.fund-utilization.fund-project-lib.print', compact(
            'id', 'realignments'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $userUnit = Auth::user()->unit;
        $userGroups = Auth::user()->groups ? unserialize(Auth::user()->groups) : [];
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();

        $allotmentClassifications = AllotmentClass::orderBy('class_name')->get();
        $users = User::where('is_active', 'y')
                     ->orderBy('firstname');

        $projects = [];
        $projDat = new FundingProject;
        $_projects = FundingProject::doesntHave('budget')
                                  ->orderBy('project_title');

        if ($roleHasBudget || $roleHasAdministrator || $roleHasDeveloper) {
            $users = $users->get();
        } else {
            $projectIDs = $projDat->getAccessibleProjects();
            $_projects = $_projects->whereIn('id', $projectIDs);
            $users = $users->where('id', Auth::user()->id)->get();
        }

        $_projects = $_projects->get();

        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        $tempFundSrcs = [];

        foreach ($_projects as $proj) {
            $directory = $proj->directory ? implode(' &rarr; ', unserialize($proj->directory)) : NULL;
            $projTitle = (strlen($proj->project_title) > 70 ?
                         substr($proj->project_title, 0, 70).'...' :
                         $proj->project_title);
            $projTitle = strtoupper($projTitle);
            $title = $directory ? "$directory &rarr; $projTitle" : $projTitle;
            $coimpAgencies = $proj->comimplementing_agency_lgus;
            $projectCost = $proj->project_cost;
            $implementAgency = $proj->implementing_agency;
            $implementProjCost = $proj->implementing_project_cost ;

            if ($directory) {
                $tempFundSrcs['with_dir'][] = (object) [
                    'id' => $proj->id,
                    'project_title' => $title,
                    'comimplementing_agency_lgus' => $coimpAgencies,
                    'project_cost' => $projectCost,
                    'implementing_agency' => $implementAgency,
                    'implementing_project_cost' => $implementProjCost,
                ];
            } else {
                $tempFundSrcs['no_dir'][] = (object) [
                    'id' => $proj->id,
                    'project_title' => $title,
                    'comimplementing_agency_lgus' => $coimpAgencies,
                    'project_cost' => $projectCost,
                    'implementing_agency' => $implementAgency,
                    'implementing_project_cost' => $implementProjCost,
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

        return view('modules.fund-utilization.fund-project-lib.create', compact(
            'projects',
            'allotmentClassifications',
            'users',
            'signatories'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $project = $request->project;
        $approvedBudget = $request->approved_budget;
        $submittedBy = $request->submitted_by;
        $approvedBy = $request->approved_by;

        $rowTypes = $request->row_type;
        $allotmentNames = $request->allotment_name;
        $uacsCodes = $request->uacs_code;
        $allotmentClasses = $request->allot_class;
        $allottedBudgets = $request->allotted_budget;
        $coimplementorIDs = $request->coimplementor_id;
        $coimplementorBudgets = $request->coimplementor_budget;

        /*
        echo "rowTypes, allotmentNames, allotmentClasses, allottedBudgets,
             coimplementors, coimplementorBudgets";
        dd($rowTypes, $allotmentNames, $allotmentClasses, $allottedBudgets,
           $coimplementors, $coimplementorBudgets);*/

        $documentType = 'Line-Item Budgets';
        $routeName = 'fund-project-lib';

        try {
            $projectData = FundingProject::find($project);
            $dateFrom = $projectData->date_from;
            $dateTo = $projectData->date_to;

            $instanceFundingBudget = new FundingBudget;
            $instanceFundingBudget->project_id = $project;
            $instanceFundingBudget->date_from = $dateFrom;
            $instanceFundingBudget->date_to = $dateTo;
            $instanceFundingBudget->approved_budget = $approvedBudget;
            $instanceFundingBudget->created_by = Auth::user()->id;
            $instanceFundingBudget->sig_submitted_by = $submittedBy;
            $instanceFundingBudget->sig_approved_by = $approvedBy;
            $instanceFundingBudget->save();

            $lastFundBudget = FundingBudget::orderBy('created_at', 'desc')->first();
            $lastID = $lastFundBudget->id;

            if (count($allotmentClasses) > 0) {
                $coimplementers = [];
                $orderNo = 0;
                $headerName = "";

                foreach ($allotmentClasses as $ctr => $allotmentClass) {
                    if ($rowTypes[$ctr] == 'header') {
                        $headerName = $allotmentNames[$ctr];
                    } else if ($rowTypes[$ctr] == 'item') {
                        $orderNo += 1;

                        if ($coimplementorIDs[$ctr]) {
                            foreach ($coimplementorIDs[$ctr] as $ctrCoimp => $coimpID) {
                                $coimplementers[] = [
                                    'id' => $coimpID,
                                    'coimplementor_budget' => $coimplementorBudgets[$ctr][$ctrCoimp]
                                ];
                            }
                        }

                        $instanceAllotment = new FundingAllotment;
                        $instanceAllotment->project_id = $project;
                        $instanceAllotment->budget_id = $lastID;
                        $instanceAllotment->allotment_class = $allotmentClass;
                        $instanceAllotment->uacs_id = !empty($uacsCodes[$ctr]) ? $uacsCodes[$ctr] : NULL;
                        $instanceAllotment->order_no = $orderNo;
                        $instanceAllotment->allotment_name = empty($headerName) ? $allotmentNames[$ctr] :
                                                            "$headerName::".$allotmentNames[$ctr];
                        $instanceAllotment->allotment_cost = $allottedBudgets[$ctr];
                        $instanceAllotment->coimplementers = serialize($coimplementers);
                        $instanceAllotment->save();

                        $coimplementers = [];
                    } else if ($rowTypes[$ctr] == 'header-break') {
                        $headerName = "";
                    }
                }
            }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeRealignment(Request $request, $id) {
        $proposedBudget = $request->approved_budget;
        $dateRealignment = $request->date_realignment;
        $submittedBy = $request->submitted_by;
        $approvedBy = $request->approved_by;

        $rowTypes = $request->row_type;
        $allotmentIDs = $request->allotment_id;
        $allotmentNames = $request->allotment_name;
        $uacsCodes = $request->uacs_code;
        $allotmentClasses = $request->allot_class;
        $allottedBudgets = $request->allotted_budget;
        $coimplementorIDs = $request->coimplementor_id;
        $coimplementorBudgets = $request->coimplementor_budget;
        $justifications = $request->justification;

        //echo '$rowTypes, $allotmentIDs, $allotmentNames, $allotmentClasses, $allottedBudgets, $justifications';
        //dd($rowTypes, $allotmentIDs, $allotmentNames, $allotmentClasses, $allottedBudgets, $justifications);

        $documentType = 'Line-Item Budget Realignment';
        $routeName = 'fund-project-lib';

        try {
            $budgetRealignedData = FundingBudgetRealignment::whereNull('date_disapproved')
                                                           ->where('budget_id', $id)
                                                           ->orderBy('realignment_order', 'desc')
                                                           ->first();
            $fundingBudgetData = FundingBudget::find($id);
            $projectID = $fundingBudgetData->project_id;
            $realignmentOrder = $budgetRealignedData ?
                                $budgetRealignedData->realignment_order + 1 :
                                1;

            $instanceBudgetRealigned = new FundingBudgetRealignment;
            $instanceBudgetRealigned->project_id = $projectID;
            $instanceBudgetRealigned->budget_id = $id;
            $instanceBudgetRealigned->date_realignment = $dateRealignment;
            $instanceBudgetRealigned->approved_realigned_budget = $proposedBudget;
            $instanceBudgetRealigned->realignment_order = $realignmentOrder;
            $instanceBudgetRealigned->created_by = Auth::user()->id;
            $instanceBudgetRealigned->sig_submitted_by = $submittedBy;
            $instanceBudgetRealigned->sig_approved_by = $approvedBy;
            $instanceBudgetRealigned->save();

            $lastID = DB::table('funding_budget_realignments')
                        ->where('realignment_order', $realignmentOrder)
                        ->first();

            if (count($allotmentIDs) > 0) {
                $coimplementers = [];
                $orderNo = 0;
                $headerName = "";

                foreach ($allotmentIDs as $ctr => $allotmentID) {
                    if ($rowTypes[$ctr] == 'header') {
                        $headerName = $allotmentNames[$ctr];
                    } else if ($rowTypes[$ctr] == 'item') {
                        $orderNo += 1;

                        if ($coimplementorIDs[$ctr]) {
                            foreach ($coimplementorIDs[$ctr] as $ctrCoimp => $coimpID) {
                                $coimplementers[] = [
                                    'id' => $coimpID,
                                    'coimplementor_budget' => $coimplementorBudgets[$ctr][$ctrCoimp]
                                ];
                            }
                        }

                        $instanceRealignedAllot = new FundingAllotmentRealignment;
                        $instanceRealignedAllot->project_id = $projectID;
                        $instanceRealignedAllot->budget_id = $id;
                        $instanceRealignedAllot->allotment_id = $allotmentID ? $allotmentID : NULL;
                        $instanceRealignedAllot->budget_realign_id = $lastID->id;
                        $instanceRealignedAllot->uacs_id = !empty($uacsCodes[$ctr]) ? $uacsCodes[$ctr] : NULL;
                        $instanceRealignedAllot->allotment_class = $allotmentClasses[$ctr];
                        $instanceRealignedAllot->order_no = $orderNo;
                        $instanceRealignedAllot->realigned_allotment_cost = $allottedBudgets[$ctr];
                        $instanceRealignedAllot->coimplementers = serialize($coimplementers);
                        $instanceRealignedAllot->allotment_name = empty($headerName) ? $allotmentNames[$ctr] :
                                                                  "$headerName::".$allotmentNames[$ctr];
                        $instanceRealignedAllot->justification = $justifications[$ctr];
                        $instanceRealignedAllot->save();

                        $coimplementers = [];
                    } else if ($rowTypes[$ctr] == 'header-break') {
                        $headerName = "";
                    }
                }
            }

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
        $userUnit = Auth::user()->unit;
        $userGroups = Auth::user()->groups ? unserialize(Auth::user()->groups) : [];
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();

        $itemCounter = 1;

        $budget = FundingBudget::find($id);
        $budgetID = $budget->id;
        $remainingBudget = $budget->approved_budget;
        $allotments = FundingAllotment::where('budget_id', $budgetID)
                                      ->orderBy('order_no')
                                      ->get();
        $uacsCodes = MooeAccountTitle::orderBy('account_title')->get();
        $allotmentClassifications = AllotmentClass::orderBy('class_name')->get();

        $projDat = new FundingProject;
        $project = FundingProject::find($budget->project_id);
        $projectID = $project->id;
        $projects = [];
        $_projects = FundingProject::whereDoesntHave('budget', function($qry) use ($projectID) {
            $qry->where('project_id', '<>', $projectID);
        })->orderBy('project_title');

        if (!$roleHasBudget && !$roleHasAdministrator && !$roleHasDeveloper) {
            $projectIDs = $projDat->getAccessibleProjects();
            $projectIDs[] = $projectID;
            $_projects = $_projects->whereIn('id', $projectIDs);
        }

        $_projects = $_projects->get();

        $implementingAgency = $project->implementing_agency;
        $coimplementors = unserialize($project->comimplementing_agency_lgus);

        $allotmentData = $this->groupAllotments($allotments, $remainingBudget);
        $remainingBudget = $allotmentData->remaining_budget;
        $groupedAllotments = $allotmentData->grouped_allotments;

        $users = User::where('is_active', 'y')
                     ->orderBy('firstname')->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        $tempFundSrcs = [];

        foreach ($_projects as $proj) {
            $directory = $proj->directory ? implode(' &rarr; ', unserialize($proj->directory)) : NULL;
            $projTitle = (strlen($proj->project_title) > 70 ?
                         substr($proj->project_title, 0, 70).'...' :
                         $proj->project_title);
            $projTitle = strtoupper($projTitle);
            $title = $directory ? "$directory &rarr; $projTitle" : $projTitle;
            $coimpAgencies = $proj->comimplementing_agency_lgus;
            $projectCost = $proj->project_cost;
            $implementAgency = $proj->implementing_agency;
            $implementProjCost = $proj->implementing_project_cost ;

            if ($directory) {
                $tempFundSrcs['with_dir'][] = (object) [
                    'id' => $proj->id,
                    'project_title' => $title,
                    'comimplementing_agency_lgus' => $coimpAgencies,
                    'project_cost' => $projectCost,
                    'implementing_agency' => $implementAgency,
                    'implementing_project_cost' => $implementProjCost,
                ];
            } else {
                $tempFundSrcs['no_dir'][] = (object) [
                    'id' => $proj->id,
                    'project_title' => $title,
                    'comimplementing_agency_lgus' => $coimpAgencies,
                    'project_cost' => $projectCost,
                    'implementing_agency' => $implementAgency,
                    'implementing_project_cost' => $implementProjCost,
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

        return view('modules.fund-utilization.fund-project-lib.update', compact(
            'id',
            'projects',
            'uacsCodes',
            'allotmentClassifications',
            'budget',
            'allotments',
            'remainingBudget',
            'users',
            'signatories',
            'groupedAllotments',
            'itemCounter',
            'implementingAgency',
            'coimplementors'
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
        $project = $request->project;
        $approvedBudget = $request->approved_budget;
        $submittedBy = $request->submitted_by;
        $approvedBy = $request->approved_by;

        $rowTypes = $request->row_type;
        $allotmentIDs = $request->allotment_id;
        $allotmentNames = $request->allotment_name;
        $uacsCodes = $request->uacs_code;
        $allotmentClasses = $request->allot_class;
        $allottedBudgets = $request->allotted_budget;
        $coimplementorIDs = $request->coimplementor_id;
        $coimplementorBudgets = $request->coimplementor_budget;

        //echo '$rowTypes, $allotmentIDs, $allotmentNames, $allotmentClasses, $allottedBudgets';
        //dd($rowTypes, $allotmentIDs, $allotmentNames, $allotmentClasses, $allottedBudgets);

        $newAllotmentIDs = $allotmentIDs;
        $documentType = 'Line-Item Budgets';
        $routeName = 'fund-project-lib';

        try {
            $instanceFundingBudget = FundingBudget::find($id);
            $projectID = $instanceFundingBudget->project_id;

            $projectData = FundingProject::find($project);
            $dateFrom = $projectData->date_from;
            $dateTo = $projectData->date_to;

            if ($instanceFundingBudget->date_disapproved) {
                $instanceFundingBudget->date_disapproved = NULL;
            }

            $instanceFundingBudget->project_id = $project;
            $instanceFundingBudget->date_from = $dateFrom;
            $instanceFundingBudget->date_to = $dateTo;
            $instanceFundingBudget->approved_budget = $approvedBudget;
            $instanceFundingBudget->sig_submitted_by = $submittedBy;
            $instanceFundingBudget->sig_approved_by = $approvedBy;
            $instanceFundingBudget->save();

            if (count($allotmentIDs) > 0) {
                $coimplementers = [];
                $orderNo = 0;
                $headerName = "";

                foreach ($allotmentIDs as $ctr => $allotmentID) {
                    if ($rowTypes[$ctr] == 'header') {
                        $headerName = $allotmentNames[$ctr];
                    } else if ($rowTypes[$ctr] == 'item') {
                        $orderNo += 1;

                        $instanceAllotment = $allotmentID ? FundingAllotment::find($allotmentID) :
                                            new FundingAllotment;

                        if (!$allotmentID) {
                            $instanceAllotment->project_id = $project;
                            $instanceAllotment->budget_id = $id;
                        }

                        if ($coimplementorIDs[$ctr]) {
                            foreach ($coimplementorIDs[$ctr] as $ctrCoimp => $coimpID) {
                                $coimplementers[] = [
                                    'id' => $coimpID,
                                    'coimplementor_budget' => $coimplementorBudgets[$ctr][$ctrCoimp]
                                ];
                            }
                        }

                        $instanceAllotment->uacs_id = !empty($uacsCodes[$ctr]) ? $uacsCodes[$ctr] : NULL;
                        $instanceAllotment->allotment_class = $allotmentClasses[$ctr];
                        $instanceAllotment->order_no = $orderNo;
                        $instanceAllotment->allotment_name = empty($headerName) ? $allotmentNames[$ctr] :
                                                            "$headerName::".$allotmentNames[$ctr];
                        $instanceAllotment->allotment_cost = $allottedBudgets[$ctr];
                        $instanceAllotment->coimplementers = serialize($coimplementers);
                        $instanceAllotment->save();

                        if (!$allotmentID && is_int($ctr)) {
                            $newAddedAllotment = DB::table('funding_allotments')
                                                ->where([
                                                    ['budget_id', $id],
                                                    ['allotment_name', $instanceAllotment->allotment_name]
                                                ])->first();
                            $newAllotmentIDs[] = $newAddedAllotment->id;
                        }

                        $coimplementers = [];
                    } else if ($rowTypes[$ctr] == 'header-break') {
                        $headerName = "";
                    }
                }

                $newAllotmentIDs = array_filter($newAllotmentIDs);
                FundingAllotment::whereNotIn('id', $newAllotmentIDs)
                                ->where('budget_id', $id)
                                ->delete();
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

    public function updateRealignment(Request $request, $id) {
        $proposedBudget = $request->approved_budget;
        $dateRealignment = $request->date_realignment;
        $submittedBy = $request->submitted_by;
        $approvedBy = $request->approved_by;

        $rowTypes = $request->row_type;
        $allotmentIDs = $request->allotment_id;
        $realignedAllotIDs = $request->allotment_realign_id;
        $allotmentNames = $request->allotment_name;
        $uacsCodes = $request->uacs_code;
        $allotmentClasses = $request->allot_class;
        $allottedBudgets = $request->allotted_budget;
        $coimplementorIDs = $request->coimplementor_id;
        $coimplementorBudgets = $request->coimplementor_budget;
        $justifications = $request->justification;

        /*echo '$rowTypes, $allotmentIDs, $realignedAllotIDs, $allotmentNames,
              $allotmentClasses, $allottedBudgets, $justifications';
        dd($rowTypes, $allotmentIDs, $realignedAllotIDs, $allotmentNames,
           $allotmentClasses, $allottedBudgets, $justifications);*/

        $newRealignAllotIDs = $realignedAllotIDs;
        $documentType = 'Line-Item Budget Realignment';
        $routeName = 'fund-project-lib';

        try {
            $budgetRealignedData = FundingBudgetRealignment::where('budget_id', $id)
                                                           ->orderBy('realignment_order', 'desc')
                                                           ->first();
            $fundingBudgetData = FundingBudget::find($id);
            $realignedBudgetID = $budgetRealignedData->id;
            $projectID = $fundingBudgetData->project_id;
            $realignmentOrder = $budgetRealignedData->realignment_order;

            $instanceBudgetRealigned = FundingBudgetRealignment::find($realignedBudgetID);
            $instanceBudgetRealigned->date_disapproved = NULL;
            $instanceBudgetRealigned->approved_realigned_budget = $proposedBudget;
            $instanceBudgetRealigned->sig_submitted_by = $submittedBy;
            $instanceBudgetRealigned->sig_approved_by = $approvedBy;
            $instanceBudgetRealigned->save();

            if (count($allotmentIDs) > 0) {
                $coimplementers = [];
                $orderNo = 0;
                $headerName = "";

                foreach ($allotmentIDs as $ctr => $allotmentID) {
                    if ($rowTypes[$ctr] == 'header') {
                        $headerName = $allotmentNames[$ctr];
                    } else if ($rowTypes[$ctr] == 'item') {
                        $orderNo += 1;

                        $instanceRealignedAllot = $realignedAllotIDs[$ctr] ?
                                                  FundingAllotmentRealignment::find($realignedAllotIDs[$ctr]) :
                                                  new FundingAllotmentRealignment;

                        if (!$realignedAllotIDs[$ctr]) {
                            $instanceRealignedAllot->project_id = $projectID;
                            $instanceRealignedAllot->budget_id = $id;
                        }

                        if ($coimplementorIDs[$ctr]) {
                            foreach ($coimplementorIDs[$ctr] as $ctrCoimp => $coimpID) {
                                $coimplementers[] = [
                                    'id' => $coimpID,
                                    'coimplementor_budget' => $coimplementorBudgets[$ctr][$ctrCoimp]
                                ];
                            }
                        }

                        $instanceRealignedAllot->uacs_id = !empty($uacsCodes[$ctr]) ? $uacsCodes[$ctr] : NULL;
                        $instanceRealignedAllot->allotment_id = $allotmentID ? $allotmentID : NULL;
                        $instanceRealignedAllot->budget_realign_id = $realignedBudgetID;
                        $instanceRealignedAllot->allotment_class = $allotmentClasses[$ctr];
                        $instanceRealignedAllot->order_no = $orderNo;
                        $instanceRealignedAllot->realigned_allotment_cost = $allottedBudgets[$ctr];
                        $instanceRealignedAllot->coimplementers = serialize($coimplementers);
                        $instanceRealignedAllot->allotment_name = empty($headerName) ? $allotmentNames[$ctr] :
                                                                  "$headerName::".$allotmentNames[$ctr];
                        $instanceRealignedAllot->justification = $justifications[$ctr];
                        $instanceRealignedAllot->save();

                        if (!$realignedAllotIDs[$ctr] && is_int($ctr)) {
                            $newAddedAllotment = DB::table('funding_allotment_realignments')
                                                   ->where([
                                                    ['budget_realign_id', $realignedBudgetID],
                                                    ['allotment_name', $instanceRealignedAllot->allotment_name]
                                                ])->first();
                            $newRealignAllotIDs[] = $newAddedAllotment->id;
                        }

                        $coimplementers = [];
                    } else if ($rowTypes[$ctr] == 'header-break') {
                        $headerName = "";
                    }
                }

                $newRealignAllotIDs = array_filter($newRealignAllotIDs);
                $test = FundingAllotmentRealignment::whereNotIn('id', $newRealignAllotIDs)
                                ->where('budget_realign_id', $realignedBudgetID)
                                ->delete();
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

    public function showCreateEditRealignment(Request $request, $id, $type) {
        $itemCounter = 1;

        $allotmentClassifications = AllotmentClass::orderBy('class_name')->get();
        $budgetData = FundingBudget::find($id);
        $uacsCodes = MooeAccountTitle::orderBy('account_title')->get();
        $budgetRealignedData = FundingBudgetRealignment::where('budget_id', $id)
                                                       ->orderBy('realignment_order', 'desc')
                                                       ->first();
        $approvedBudget = $budgetRealignedData ? $budgetRealignedData->approved_realigned_budget :
                          $budgetData->approved_budget;
        $remainingBudget = $approvedBudget;
        $allotments = $budgetRealignedData ?
                      DB::table('funding_allotment_realignments as r_allot')
                        ->select('allot.id as id', 'r_allot.allotment_name as allotment_name',
                                'r_allot.realigned_allotment_cost as allotment_cost',
                                'r_allot.allotment_class as allotment_class', 'r_allot.coimplementers',
                                'r_allot.id as r_allot_id', 'r_allot.justification',
                                'r_allot.uacs_id')
                        ->leftJoin('funding_allotments as allot', 'allot.id', '=',
                                   'r_allot.allotment_id')
                        ->where('r_allot.budget_realign_id', $budgetRealignedData->id)
                        ->orderBy('r_allot.order_no')
                        ->get() :
                      FundingAllotment::where('budget_id', $id)
                                      ->orderBy('order_no')
                                      ->get();

        $allotmentData = $this->groupAllotments($allotments, $remainingBudget);
        $remainingBudget = $allotmentData->remaining_budget;
        $groupedAllotments = $allotmentData->grouped_allotments;

        $dateRealignment = $budgetRealignedData ? $budgetRealignedData->date_realignment : NULL;
        $approvedBudget = round($approvedBudget, 2);

        $project = FundingProject::find($budgetData->project_id);
        $implementingAgency = $project->implementing_agency;
        $coimplementors = unserialize($project->comimplementing_agency_lgus);

        if ($type == 'create') {
            $viewFile = 'modules.fund-utilization.fund-project-lib.create-realignment';
        } else {
            $viewFile = 'modules.fund-utilization.fund-project-lib.update-realignment';
        }

        $users = User::where('is_active', 'y')
                     ->orderBy('firstname')->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view($viewFile, compact(
            'id',
            'dateRealignment',
            'approvedBudget',
            'remainingBudget',
            'allotments',
            'uacsCodes',
            'allotmentClassifications',
            'users',
            'signatories',
            'budgetData',
            'budgetRealignedData',
            'groupedAllotments',
            'itemCounter',
            'implementingAgency',
            'coimplementors'
        ));
    }

    /**
     * Soft deletes the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id) {
        $isDestroy = $request->destroy;
        $routeName = 'fund-project-lib';

        if ($isDestroy) {
            $response = $this->destroy($request, $id);

            if ($response->alert_type == 'success') {
                return redirect()->route($routeName)
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route($routeName)
                                 ->with($response->alert_type, $response->msg);
            }
        } else {
            try {
                $instanceBudget = FundingBudget::find($id);
                $documentType = 'Line-Item Budgets';
                $instanceBudget->delete();

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
    public function destroy($request, $id) {
        try {
            FundingAllotment::where('budget_id', $id)
                            ->delete();

            $instanceBudget = FundingBudget::find($id);
            $documentType = 'Line-Item Budgets';
            $instanceBudget->forceDelete();

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

    public function destroyRealignment(Request $request, $id) {
        $routeName = 'fund-project-lib';
        $documentType = 'List-Item Budget Realignments';

        try {
            FundingAllotmentRealignment::where('budget_realign_id', $id)
                                       ->delete();
            FundingBudgetRealignment::destroy($id);

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

    public function getListAllotmentClass(Request $request) {
        $keyword = trim($request->search);
        $classData = AllotmentClass::select('id', 'class_name');

        if ($keyword) {
            $classData = $classData->where(function($qry) use ($keyword) {
                $qry->where('class_name', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('class_name', 'like', "%$tag%");
                    }
                }
            });
        }

        $classData = $classData->orderBy('class_name')
                               ->get();

        return response()->json($classData);
    }

    public function getListAccountTitle(Request $request) {
        $keyword = trim($request->search);
        $accountTitleData = MooeAccountTitle::select('id', 'account_title', 'uacs_code');

        if ($keyword) {
            $accountTitleData = $accountTitleData->where(function($qry) use ($keyword) {
                $qry->where('account_title', 'like', "%$keyword%")
                    ->orWhere('uacs_code', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('account_title', 'like', "%$tag%")
                            ->orWhere('uacs_code', 'like', "%$tag%");
                    }
                }
            });
        }

        $accountTitleData = $accountTitleData->orderBy('account_title')
                                             ->get();

        return response()->json($accountTitleData);
    }

    public function approve(Request $request, $id, $isRealignment) {
        $documentType = 'Line-Item Budgets';
        $routeName = 'fund-project-lib';

        try {
            //$instanceNotif = new Notif;
            $instanceBudget = !$isRealignment ? FundingBudget::find($id) :
                              FundingBudgetRealignment::find($id);
            $budgetID = !$isRealignment ? $instanceBudget->id :
                        $instanceBudget->budget_id;
            $instanceBudget->date_approved = Carbon::now();
            $instanceBudget->save();

            //$instanceNotif->notifyApproveSummary($id, Auth::user()->id);

            $msg = "$documentType '$budgetID' successfully set to 'Approved'.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $budgetID])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function disapprove(Request $request, $id, $isRealignment) {
        $documentType = 'Line-Item Budgets';
        $routeName = 'fund-project-lib';

        try {
            //$instanceNotif = new Notif;
            $instanceBudget = !$isRealignment ? FundingBudget::find($id) :
                              FundingBudgetRealignment::find($id);
            $budgetID = !$isRealignment ? $instanceBudget->id :
                        $instanceBudget->budget_id;
            $instanceBudget->date_disapproved = Carbon::now();
            $instanceBudget->save();

            //$instanceNotif->notifyDisapprovedPR($id);

            $msg = "$documentType '$budgetID' successfully disapproved.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    private function groupAllotments($allotments, $remainingBudget) {
        $groupedAllotments = [];

        foreach ($allotments as $itmCtr => $item) {
            if (count(explode('::', $item->allotment_name)) > 1) {
                $keyAllotment = preg_replace("/\s+/", "-", explode('::', $item->allotment_name)[0]);
                $groupedAllotments[$keyAllotment][] = $item;
            } else {
                $groupedAllotments[$itmCtr + 1] = $item;
            }

            $remainingBudget -= $item->allotment_cost;
        }

        return (object) [
            'grouped_allotments' => $groupedAllotments,
            'remaining_budget' => $remainingBudget
        ];
    }
}
