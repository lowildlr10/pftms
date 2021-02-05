<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FundingProject;
use App\Models\FundingLedger;
use App\Models\FundingLedgerItem;
use App\Models\PaperSize;

use Auth;

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

        return view('modules.fund-utilization.fund-project-lib.index', [
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
    public function showCreate() {
        $projects = FundingProject::orderBy('project_name')->get();
        return view('modules.fund-utilization.fund-project-lib.create', compact(
            'projects'
        ));
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
}
