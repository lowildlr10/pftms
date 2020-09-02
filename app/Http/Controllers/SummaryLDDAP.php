<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SummaryLDDAP as Summary;
use App\Models\SummaryLDDAPItem as SummaryItem;
use App\Models\ListDemandPayable;
use App\Models\ListDemandPayableItem;

use App\User;
use App\Models\EmpGroup;
use App\Models\EmpDivision;
use App\Models\Signatory;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\MdsGsb;
use DB;
use Auth;
use Carbon\Carbon;

class SummaryLDDAP extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'pay_summary';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedApproval = Auth::user()->getModuleAccess($module, 'approval');
        $isAllowedApprove = Auth::user()->getModuleAccess($module, 'approve');
        $isAllowedSubmission = Auth::user()->getModuleAccess($module, 'submission');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $summaryData = Summary::whereNull('deleted_at');

        if (!empty($keyword)) {
            $summaryData = $summaryData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('department', 'like', "%$keyword%")
                    ->orWhere('entity_name', 'like', "%$keyword%")
                    ->orWhere('operating_unit', 'like', "%$keyword%")
                    ->orWhere('fund_cluster', 'like', "%$keyword%")
                    ->orWhere('sliiae_no', 'like', "%$keyword%")
                    ->orWhere('date_sliiae', 'like', "%$keyword%")
                    ->orWhere('to', 'like', "%$keyword%")
                    ->orWhere('bank_name', 'like', "%$keyword%")
                    ->orWhere('bank_address', 'like', "%$keyword%")
                    ->orWhere('lddap_no_pcs', 'like', "%$keyword%")
                    ->orWhere('total_amount_words', 'like', "%$keyword%")
                    ->orWhere('total_amount', 'like', "%$keyword%")
                    ->orWhere('status', 'like', "%$keyword%");
            });
        }

        $summaryData = $summaryData->sortable(['created_at' => 'desc'])->paginate(15);

        return view('modules.payment.summary.index', [
            'list' => $summaryData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'isAllowedApproval' => $isAllowedApproval,
            'isAllowedApprove' => $isAllowedApprove,
            'isAllowedSubmission'=> $isAllowedSubmission,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $mdsGSBs = MdsGsb::all();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.payment.summary.create', compact(
            'mdsGSBs', 'signatories'
        ));
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

    public function getListLDDAP(Request $request) {
        $search = trim($request->search);
        $lddapData = ListDemandPayable::select('id', 'lddap_ada_no',
                                               'total_amount', 'date_lddap');

        if ($search) {
            $lddapData = $lddapData->where('lddap_ada_no', 'like', "%$search%");
        }

        $lddapData = $lddapData->orderBy('lddap_ada_no')->get();

        return response()->json($lddapData);
    }
}
