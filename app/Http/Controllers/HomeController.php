<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use Auth;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $firstname = Auth::user()->firstname;
        $middlename = "";
        $lastname = Auth::user()->lastname;

        if (!empty(Auth::user()->middlename)) {
            $middlename = Auth::user()->middlename[0] . '.';
        }

        $fullname = "$firstname $middlename $lastname";
        $fullname = strtoupper($fullname);

        $pr = $this->getDataProcurement();

        return view('dashboard.index', ['fullname' => $fullname,
                                        'pr' => $pr]);
    }

    public function indexSearchAll(Request $request) {
        $search = trim($request->search);

        return view();
    }

    private function getDataProcurement() {
        $empDivisionAccess = Auth::user()->getDivisionAccess();

        if (!Auth::user()->hasOrdinaryRole()) {
            //$pendingLastMonth = '';
            $prPendingTotal = PurchaseRequest::where('status', 1)
                                             ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
                $query->whereIn('id', $empDivisionAccess);
            })->count();
            $prApprovedTotal = PurchaseRequest::where('status', '>=', 5)
                                              ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
                $query->whereIn('id', $empDivisionAccess);
            })->count();
            $poForDeliveryTot = DB::table('purchase_job_orders as po')
                                  ->join('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                                  ->where('po.status', 8)
                                  ->count();
            $poDeliveredTot = DB::table('purchase_job_orders as po')
                                ->join('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                                ->where('po.status', '>=', 9)
                                ->count();
        } else {
            //$pendingLastMonth = '';
            $prPendingTotal = PurchaseRequest::where([
                ['status', 1],
                ['requested_by', Auth::user()->id]
            ])->count();
            $prApprovedTotal = PurchaseRequest::where([
                ['status', '>=', 5],
                ['requested_by', Auth::user()->id]
            ])->count();
            $poForDeliveryTot = DB::table('purchase_job_orders as po')
                                  ->join('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                                  ->where([
                ['po.status', 8],
                ['pr.requested_by', Auth::user()->id]
            ])->count();
            $poDeliveredTot = DB::table('purchase_job_orders as po')
                                ->join('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                                ->where([
                ['po.status', '>=', 9],
                ['pr.requested_by', Auth::user()->id]
            ])->count();
        }

        $prPendingTotal = number_format($prPendingTotal);
        $prApprovedTotal = number_format($prApprovedTotal);
        $poForDeliveryTot = number_format($poForDeliveryTot);

        return (object) ['total_pending' => $prPendingTotal,
                         'total_approved' => $prApprovedTotal,
                         'total_for_delivery' => $poForDeliveryTot,
                         'total_delivered' => $poDeliveredTot];
    }
}
