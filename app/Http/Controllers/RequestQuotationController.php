<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\RequestQuotation;
use App\Models\AbstractQuotation;
use App\Models\PurchaseJobOrder;
use App\Models\ObligationRequestStatus;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\InventoryStock;

use App\User;
use App\Models\FundingSource;
use App\Models\Signatory;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use Carbon\Carbon;
use DB;
use Auth;

class RequestQuotationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($keyword = '') {
        $keyword = trim($keyword);
        $instanceDocLog = new DocLog;

        // Get module access
        $module = 'proc_rfq';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedAbstract = Auth::user()->getModuleAccess('proc_abs', 'is_allowed');

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();

        $rfqData = RequestQuotation::whereHas('pr', function($query)
                    use ($empDivisionAccess) {
            $query->whereIn('division', $empDivisionAccess)
                  ->orderBy('pr_no', 'desc');
        });

        if ($roleHasOrdinary) {
            $rfqData = $rfqData->whereHas('pr', function($query) {
                $query->where('requested_by', Auth::user()->id);
            });
        }

        if (!empty($keyword)) {
            $rfqData = $rfqData->where('id', $keyword);
        }

        $rfqData = $rfqData->get();

        foreach ($rfqData as $rfq) {
            $instanceFundSource = FundingSource::find($rfq->pr->funding_source);
            $fundingSource = !empty($instanceFundSource->source_name) ?
                              $instanceFundSource->source_name : '';
            $requestedBy = Auth::user()->getEmployeeName($rfq->pr->requested_by);

            $rfq->doc_status = $instanceDocLog->checkDocStatus($rfq->id);
            $rfq->pr->funding_source = $fundingSource;
            $rfq->pr->requested_by = $requestedBy;
        }

        return view('modules.procurement.rfq.index', [
            'list' => $rfqData,
            'paperSizes' => $paperSizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedAbstract' => $isAllowedAbstract,
        ]);
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
