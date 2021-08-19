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
use App\Models\RegAllotment;
use App\Models\RegAllotmentItem;
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

class RegAllotmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'report_dvledger';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        //$fundProject = $this->getIndexData($request, 'disbursement');

        return view('modules.report.registry-allotment.index', [
            //'list' => $fundProject,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            //'isAllowedCreate' => $isAllowedCreate,
            //'isAllowedUpdate' => $isAllowedUpdate,
            //'isAllowedDelete' => $isAllowedDelete,
            //'isAllowedDestroy' => $isAllowedDestroy,
            //'dirCtr' => 0,
        ]);
    }

    private function getIndexData($request, $for) {
        $keyword = trim($request->keyword);

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();

        $projDat = new FundingProject;
        $fundProject = FundingProject::whereHas('budget', function($query) {
            $query->whereNotNull('date_approved');
        });

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

        if (!$roleHasBudget && !$roleHasAdministrator && !$roleHasDeveloper) {
            $projectIDs = $projDat->getAccessibleProjects();

            $fundProject = $fundProject->where(function($qry) use ($projectIDs) {
                $qry->whereIn('id', $projectIDs);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
