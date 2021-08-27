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
        $fundRAOD = $this->getIndexData($request);

        return view('modules.report.registry-allotment.index', [
            'list' => $fundRAOD,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
        ]);
    }

    private function getIndexData($request) {
        $keyword = trim($request->keyword);

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();

        $fundRAOD = new RegAllotment;

        if (!empty($keyword)) {
            $fundRAOD = $fundRAOD->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('period_ending', 'like', "%$keyword%")
                    ->orWhere('entity_name', 'like', "%$keyword%")
                    ->orWhere('fund_cluster', 'like', "%$keyword%")
                    ->orWhere('legal_basis', 'like', "%$keyword%")
                    ->orWhere('mfo_pap', 'like', "%$keyword%")
                    ->orWhere('sheet_no', 'like', "%$keyword%");
            });
        }

        $fundRAOD = $fundRAOD->sortable(['period_ending' => 'desc'])
                             ->paginate(15);

        return $fundRAOD;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        return view('modules.report.registry-allotment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $periodEnding = $request->period_ending;

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
    public function showEdit($id) {
        //
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

    public function getVouchers(Request $request) {
        $periodEnding = $request->period_ending;
        $employees = User::orderBy('firstname')->get();
        $suppliers = Supplier::orderBy('company_name')->get();
        $uacsObjects = MooeAccountTitle::orderBy('uacs_code')->get();
        $vouchers = DB::table('obligation_request_status as ors')
                      ->select(
                          'ors.id as ors_id', 'dv.id as dv_id', 'ors.serial_no as serial_no',
                          'ors.date_obligated as date_obligated', 'ors.payee as payee',
                          'ors.uacs_object_code as uacs_object', 'ors.amount as obligation',
                          'ors.continuing as continuing', 'ors.current as current',
                          'ors.particulars', 'dv.amount as disbursement'
                      )->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'ors.id')
                      ->where('ors.date_obligated', 'like', "%$periodEnding%")
                      ->orderBy('ors.date_obligated')
                      ->get();

        return view('modules.report.registry-allotment.vouchers-list', compact(
            'employees', 'suppliers', 'vouchers', 'uacsObjects'
        ));
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

    public function getUacsObject(Request $request) {
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
}
