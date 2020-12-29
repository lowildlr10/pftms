<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FundingAllotment;
use App\Models\FundingAllotmentRealignment;
use App\Models\FundingBudget;
use App\Models\FundingBudgetRealignment;
use App\Models\FundingLedger;
use App\Models\FundingLedgerItem;
use App\Models\FundingProject;

use App\User;
use App\Models\PaperSize;
use DB;
use Auth;
use Carbon\Carbon;

class FundProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $fundProjData = FundingProject::whereNull('deleted_at');

        if (!empty($keyword)) {
            $fundProjData = $fundProjData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('project_name', 'like', "%$keyword%");
            });
        }

        $fundProjData = $fundProjData->orderBy('project_name')
                                     ->sortable(['created_at' => 'desc'])
                                     ->paginate(15);

        return view('modules.fund-utilization.fund-project.index', [
            'list' => $fundProjData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('modules.fund-utilization.fund-project.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $fundProjectData = FundingProject::find($id);

        $projectID = $fundProjectData->id;
        $projectName = $fundProjectData->project_name;

        $fundBudget = FundingBudget::where('project_id', $projectID)->first();

        return view('modules.fund-utilization.fund-project.update', [
            'id' => $id,
            'projectName' => $projectName
        ]);
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
}
