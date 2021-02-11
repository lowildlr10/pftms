<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FundingProject;
use App\Models\FundingBudget;
use App\Models\FundingAllotment;
use App\Models\FundingLedger;
use App\Models\FundingLedgerItem;
use App\Models\AllotmentClass;
use App\Models\FundingBudgetRealignment;
use App\Models\FundingAllotmentRealignment;
use App\Models\MooeAccountTitle;
use App\Models\PaperSize;

use Auth;
use DB;

class LineItemBudgetController extends Controller
{
    public function index(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'fund_project';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        //$isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedDestroy = true;

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $fundBudget = FundingBudget::has('project');

        if (!empty($keyword)) {
            $fundBudget = $fundBudget->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('date_from', 'like', "%$keyword%")
                    ->orWhere('date_to', 'like', "%$keyword%")
                    ->orWhere('approved_budget', 'like', "%$keyword%")
                    ->orWhere('is_active', 'like', "%$keyword%");
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
                                     ->join('funding_allotments as allot', 'allot.id', '=',
                                            'r_allot.allotment_id')
                                     ->where('r_allot.budget_realign_id', $budgetRealignID)
                                     ->orderBy('allot.order_no')
                                     ->get();

                foreach ($realignedAllots as $allotRealign) {
                    $allotments[] = (object) [
                        'id' => $allotRealign->id,
                        'allotment_id' => $allotRealign->allotment_id,
                        'budget_realign_id' => $budgetRealignID,
                        'allotment_class' => $allotRealign->allotment_class,
                        'allotment_name' => $allotRealign->allotment_name,
                        'order_no' => $allotRealign->order_no,
                        'allotted_budget' => $allotRealign->realigned_allotment,
                    ];
                }

                $realignments[] = (object) [
                    'id' => $budRealign->id,
                    'date_disapproved' => $budRealign->date_disapproved,
                    'date_approved' => $budRealign->date_approved,
                    'realigned_budget' => $budRealign->realigned_budget,
                    'realigned_allotments' => $allotments
                ];
            }

            $budget->realignments = (object) $realignments;
            $countRealignments = count($realignments);
            $budget->current_budget = isset($realignments[$countRealignments - 1]) ?
                                      $realignments[$countRealignments - 1]->realigned_budget :
                                      $budget->approved_budget;
            $budget->current_realigned_allotments = isset($realignments[$countRealignments - 1]) ?
                                                    $realignments[$countRealignments - 1]->realigned_allotments :
                                                    FundingAllotment::where('budget_id', $budget->id)
                                                                    ->orderBy('order_no')
                                                                    ->get();
        }

        return view('modules.fund-utilization.fund-project-lib.index', [
            'list' => $fundBudget,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $projects = FundingProject::orderBy('project_name')->get();
        $allotmentClassifications = AllotmentClass::orderBy('class_name')->get();
        return view('modules.fund-utilization.fund-project-lib.create', compact(
            'projects',
            'allotmentClassifications',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     */
    public function showCreateRealignment($id) {
        $remainingBudget = 0;
        $allotmentClassifications = AllotmentClass::orderBy('class_name')->get();
        $budgetData = FundingBudget::find($id);
        $budgetRealignedData = FundingBudgetRealignment::whereNotNull(
            'date_disapproved'
        )->where('budget_id', $id)
        ->orderBy('realignment_order', 'desc')
        ->first();

        $approvedBudget = $budgetRealignedData ? $budgetRealignedData->realigned_budget :
                          $budgetData->approved_budget;
        $allotments = $budgetRealignedData ?
            DB::table('funding_allotment_realignments as r_allot')
              ->select('allot.id as id', 'allot.allotment_name as allotment_name',
                       'r_allot.realigned_allotment as allotted_budget',
                       'allot.allotment_class as allotment_class')
              ->join('funding_allotments as allot', 'allot.id', '=',
                     'r_allot.allotment_id')
              ->where('r_allot.budget_realign_id', $budgetRealignID)
              ->orderBy('allot.order_no')
              ->get() :
            FundingAllotment::where('budget_id', $id)
                            ->orderBy('order_no')
                            ->get();

        return view('modules.fund-utilization.fund-project-lib.create-realignment', compact(
            'id',
            'approvedBudget',
            'remainingBudget',
            'allotments',
            'allotmentClassifications'
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
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $approvedBudget = $request->approved_budget;

        $allotmentNames = $request->allotment_name;
        $allotmentClasses = $request->allot_class;
        $allottedBudgets = $request->allotted_budget;

        $documentType = 'Line-Item Budgets';
        $routeName = 'fund-project-lib';

        try {
            $instanceFundingBudget = new FundingBudget;

            DB::table('funding_budgets')
              ->where('project_id', $project)
              ->update(['is_active' => 'n']);

            $instanceFundingBudget->project_id = $project;
            $instanceFundingBudget->date_from = $dateFrom;
            $instanceFundingBudget->date_to = $dateTo;
            $instanceFundingBudget->approved_budget = $approvedBudget;
            $instanceFundingBudget->is_active = 'y';
            $instanceFundingBudget->created_by = Auth::user()->id;
            $instanceFundingBudget->save();

            $lastFundBudget = FundingBudget::orderBy('created_at', 'desc')->first();
            $lastID = $lastFundBudget->id;

            if (count($allotmentClasses) > 0) {
                $orderNo = 0;

                foreach ($allotmentClasses as $ctr => $allotmentClass) {
                    $orderNo += 1;
                    $instanceAllotment = new FundingAllotment;
                    $instanceAllotment->project_id = $project;
                    $instanceAllotment->budget_id = $lastID;
                    $instanceAllotment->allotment_class = $allotmentClass;
                    $instanceAllotment->order_no = $orderNo;
                    $instanceAllotment->allotment_name = $allotmentNames[$ctr];
                    $instanceAllotment->allotted_budget = $allottedBudgets[$ctr];
                    $instanceAllotment->save();
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

        $allotmentIDs = $request->allotment_id;
        $allotmentNames = $request->allotment_name;
        $allotmentClasses = $request->allot_class;
        $allottedBudgets = $request->allotted_budget;

        //dd([$proposedBudget, $allotmentIDs, $allotmentNames, $allotmentClasses, $allottedBudgets]);



            $budgetRealignedData = FundingBudgetRealignment::whereNotNull(
                'date_disapproved'
            )->where('budget_id', $id)
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
            $instanceBudgetRealigned->realigned_budget = $proposedBudget;
            $instanceBudgetRealigned->realignment_order = $realignmentOrder;
            $instanceBudgetRealigned->created_by = Auth::user()->id;
            $instanceBudgetRealigned->save();

            $orderNo = 0;
            $lastID = DB::table('funding_budget_realignments')
                        ->where('realignment_order', $realignmentOrder)
                        ->first();

            foreach ($allottedBudgets as $ctr => $allotBudget) {
                $orderNo++;
                $instanceRealignedAllot = new FundingAllotmentRealignment;
                $instanceRealignedAllot->project_id = $projectID;
                $instanceRealignedAllot->budget_id = $id;
                $instanceRealignedAllot->allotment_id = isset($allotmentIDs[$ctr]) ?
                    $allotmentIDs[$ctr] :
                    $this->storeAdditionalAllotment($projectID, $id, $allotmentClasses[$ctr],
                                                    $allotmentNames[$ctr], $orderNo);
                $instanceRealignedAllot->budget_realign_id = $lastID->id;
                $instanceRealignedAllot->realigned_allotment = $allottedBudgets[$ctr];
                $instanceRealignedAllot->save();

                if (isset($allotmentIDs[$ctr])) {
                    $this->updateAllotment($allotmentIDs[$ctr], $allotmentNames[$ctr], $allotmentClasses[$ctr],
                                           $orderNo);
                }
            }
        try {
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function storeAdditionalAllotment($projectID, $budgetID, $allotmentClass,
                                             $allotmentName, $orderNo) {
        $instanceAllotment = new FundingAllotment;
        $instanceAllotment->project_id = $projectID;
        $instanceAllotment->budget_id = $budgetID;
        $instanceAllotment->allotment_class = $allotmentClass;
        $instanceAllotment->order_no = $orderNo;
        $instanceAllotment->allotment_name = $allotmentName;
        $instanceAllotment->allotted_budget = 0.00;
        $instanceAllotment->save();

        $lastID = DB::table('funding_allotments')
                    ->where([
            ['budget_id', $budgetID],
            ['allotment_name', $allotmentName]
        ])->first();

        return $lastID->id;
    }

    public function updateAllotment($id, $allotmentName, $allotmentClass, $orderNo) {
        $instanceAllotment = FundingAllotment::find($id);
        $instanceAllotment->allotment_name = $allotmentName;
        $instanceAllotment->allotment_class = $allotmentClass;
        $instanceAllotment->order_no = $orderNo;
        $instanceAllotment->save();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $budget = FundingBudget::find($id);
        $budgetID = $budget->id;
        $remainingBudget = $budget->approved_budget;
        $allotments = FundingAllotment::where('budget_id', $budgetID)
                                      ->orderBy('order_no')
                                      ->get();
        $projects = FundingProject::orderBy('project_name')->get();
        $allotmentClassifications = AllotmentClass::orderBy('class_name')->get();

        foreach ($allotmentClassifications as $item) {
            $remainingBudget -= $item->allotted_budget;
        }
        return view('modules.fund-utilization.fund-project-lib.update', compact(
            'id',
            'projects',
            'allotmentClassifications',
            'budget',
            'allotments',
            'remainingBudget'
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
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $approvedBudget = $request->approved_budget;
        $isActive = $request->is_active;

        $allotmentIDs = $request->allotment_id;
        $allotmentNames = $request->allotment_name;
        $allotmentClasses = $request->allot_class;
        $allottedBudgets = $request->allotted_budget;

        $documentType = 'Line-Item Budgets';
        $routeName = 'fund-project-lib';

        try {
            $instanceFundingBudget = FundingBudget::find($id);
            $projectID = $instanceFundingBudget->project_id;

            if ($isActive == 'y') {
                DB::table('funding_budgets')
                  ->where([
                      ['project_id', $projectID],
                      ['id', '<>', $id]
                    ])
                  ->update(['is_active' => 'n']);
            }

            $instanceFundingBudget->project_id = $project;
            $instanceFundingBudget->date_from = $dateFrom;
            $instanceFundingBudget->date_to = $dateTo;
            $instanceFundingBudget->approved_budget = $approvedBudget;
            $instanceFundingBudget->is_active = $isActive;
            $instanceFundingBudget->save();

            if (count($allotmentIDs) > 0) {
                $orderNo = 0;

                foreach ($allotmentIDs as $ctr => $allotmentID) {
                    $orderNo += 1;
                    $instanceAllotment = FundingAllotment::find($allotmentID);
                    $instanceAllotment->allotment_class = $allotmentClasses[$ctr];
                    $instanceAllotment->order_no = $orderNo;
                    $instanceAllotment->allotment_name = $allotmentNames[$ctr];
                    $instanceAllotment->allotted_budget = $allottedBudgets[$ctr];
                    $instanceAllotment->save();
                }

                FundingAllotment::whereNotIn('id', $allotmentIDs)
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
                return redirect()->route($routeName, ['keyword' => $response->id])
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
                return redirect()->route($routeName, ['keyword' => $id])
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
}
