<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FundingProject;
use App\Models\FundingBudget;
use App\Models\FundingAllotment;
use App\Models\FundingLedger;
use App\Models\FundingLedgerItem;
use App\Models\AllotmentClass;
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
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $fundBudget = FundingBudget::has('project')->with('allotments');

        /*
        if (!empty($keyword)) {
            $fundBudget = $fundBudget->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('project_name', 'like', "%$keyword%");
            });
        }*/

        $fundBudget = $fundBudget->sortable(['created_at' => 'desc'])
                                 ->paginate(15);

        //dd( $fundBudget);


        return view('modules.fund-utilization.fund-project-lib.index', [
            'list' => $fundBudget,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
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
        $mooeTitles = MooeAccountTitle::orderBy('account_title')->get();
        return view('modules.fund-utilization.fund-project-lib.create', compact(
            'projects',
            'allotmentClassifications',
            'mooeTitles'
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
                    $instanceAllotment->allotment_name = $allotmentNames[$orderNo - 1];
                    $instanceAllotment->allotted_budget = $allottedBudgets[$orderNo - 1];
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {

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
