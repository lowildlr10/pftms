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

        return view('dashboard.index', ['fullname' => $fullname]);
    }

    public function indexSearchAll(Request $request) {
        $search = trim($request->search);


    }

    public function showDashboard($dashboardID) {
        switch ($dashboardID) {
            case 'dashboard-1':
                $data = $this->getDataProcurement();
                break;

            case 'dashboard-2':
                $data = $this->getDataProcurement();
                break;

            case 'dashboard-3':
                $data = $this->getDataProcurement();
                break;

            case 'dashboard-4':
                $data = $this->getDataProcurement();
                break;

            default:
                break;
        }

        $viewPage = "dashboard.$dashboardID";
        return view($viewPage, compact('data'));
    }



    private function getDataProcurement() {
        $pendingTotal = 0;
        $pending = 0;
        $approvedTotal = 0;
        $approved = 0;
        $disapprovedTotal = 0;
        $disapproved = 0;
        $cancelledTotal = 0;
        $cancelled = 0;
        $forDeliveryTotal = 0;
        $forDelivery = 0;
        $deliveredTotal = 0;
        $delivered = 0;

        $empDivisionAccess = Auth::user()->getDivisionAccess();

        if (!Auth::user()->hasOrdinaryRole()) {
            $pendingTotalDat = PurchaseRequest::where('status', 1)
                                             ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
                $query->whereIn('id', $empDivisionAccess);
            });
            $pendingTotal = $pendingTotalDat->count();
            $pending = $pendingTotalDat->whereMonth('updated_at', '=', date('m'))->count();

            $approvedTotalDat = PurchaseRequest::where('status', '>=', 5)
                                              ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
                $query->whereIn('id', $empDivisionAccess);
            });
            $approvedTotal = $approvedTotalDat->count();
            $approved = $approvedTotalDat->whereMonth('updated_at', '=', date('m'))->count();

            $disapprovedTotalDat = PurchaseRequest::where('status', 2)
                                              ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
                $query->whereIn('id', $empDivisionAccess);
            });
            $disapprovedTotal = $disapprovedTotalDat->count();
            $disapproved = $disapprovedTotalDat->whereMonth('updated_at', '=', date('m'))->count();

            $cancelledTotalDat = PurchaseRequest::where('status', 3)
                                              ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
                $query->whereIn('id', $empDivisionAccess);
            });
            $cancelledTotal = $cancelledTotalDat->count();
            $cancelled = $cancelledTotalDat->whereMonth('updated_at', '=', date('m'))->count();

            $forDeliveryTotalDat = DB::table('purchase_job_orders as po')
                                  ->join('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                                  ->where('po.status', 8);
            $forDeliveryTotal = $forDeliveryTotalDat->count();
            $forDelivery = $forDeliveryTotalDat->whereMonth('po.updated_at', '=', date('m'))
                                               ->count();

            $deliveredTotalDat = DB::table('purchase_job_orders as po')
                                ->join('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                                ->where('po.status', '>=', 9);
            $deliveredTotal = $deliveredTotalDat->count();
            $delivered = $deliveredTotalDat->whereMonth('po.updated_at', '=', date('m'))
                                           ->count();
        } else {
            //$pendingLastMonth = '';
            $pendingTotalDat = PurchaseRequest::where([
                ['status', 1],
                ['requested_by', Auth::user()->id]
            ]);
            $pendingTotal = $pendingTotalDat->count();
            $pending = $pendingTotalDat->whereMonth('updated_at', '=', date('m'))
                                       ->count();

            $approvedTotalDat = PurchaseRequest::where([
                ['status', '>=', 5],
                ['requested_by', Auth::user()->id]
            ]);
            $approvedTotal = $approvedTotalDat->count();
            $approved = $approvedTotalDat->whereMonth('updated_at', '=', date('m'))
                                       ->count();

            $disapprovedTotalDat = PurchaseRequest::where([
                ['status', 2],
                ['requested_by', Auth::user()->id]
            ]);
            $disapprovedTotal = $disapprovedTotalDat->count();
            $disapproved = $disapprovedTotalDat->whereMonth('updated_at', '=', date('m'))
                                       ->count();

            $cancelledTotalDat = PurchaseRequest::where([
                ['status', 3],
                ['requested_by', Auth::user()->id]
            ]);
            $cancelledTotal = $cancelledTotalDat->count();
            $cancelled = $cancelledTotalDat->whereMonth('updated_at', '=', date('m'))
                                       ->count();

            $forDeliveryTotalDat = DB::table('purchase_job_orders as po')
                                  ->join('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                                  ->where([
                ['po.status', 8],
                ['pr.requested_by', Auth::user()->id]
            ]);
            $forDeliveryTotal = $forDeliveryTotalDat->count();
            $forDelivery = $forDeliveryTotalDat->whereMonth('po.updated_at', '=', date('m'))
                                       ->count();

            $deliveredTotalDat = DB::table('purchase_job_orders as po')
                                ->join('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                                ->where([
                ['po.status', '>=', 9],
                ['pr.requested_by', Auth::user()->id]
            ]);
            $deliveredTotal = $deliveredTotalDat->count();
            $delivered = $deliveredTotalDat->whereMonth('po.updated_at', '=', date('m'))
                                         ->count();
        }

        $_pendingTotal = number_format($pendingTotal);
        $_pending = number_format($pending);
        $_approvedTotal = number_format($approvedTotal);
        $_approved = number_format($approved);
        $_disapprovedTotal = number_format($disapprovedTotal);
        $_disapproved = number_format($disapproved);
        $_cancelledTotal = number_format($cancelledTotal);
        $_cancelled = number_format($cancelled);
        $_forDeliveryTotal = number_format($forDeliveryTotal);
        $_forDelivery = number_format($forDelivery);
        $_deliveredTotal = number_format($deliveredTotal);
        $_delivered = number_format($delivered);

        return (object) [
            'total_pending' => $pendingTotal,
            'pending' => $pending,
            'total_approved' => $approvedTotal,
            'approved' => $approved,
            'total_disapproved' => $disapprovedTotal,
            'disapproved' => $disapproved,
            'total_cancelled' => $cancelledTotal,
            'cancelled' => $cancelled,
            'total_for_delivery' => $forDeliveryTotal,
            'for_delivery' => $forDelivery,
            'total_delivered' => $deliveredTotal,
            'delivered' => $delivered,

            'str_total_pending' => $_pendingTotal,
            'str_pending' => $_pending,
            'str_total_approved' => $_approvedTotal,
            'str_approved' => $_approved,
            'str_total_disapproved' => $_disapprovedTotal,
            'str_disapproved' => $_disapproved,
            'str_total_cancelled' => $_cancelledTotal,
            'str_cancelled' => $_cancelled,
            'str_total_for_delivery' => $_forDeliveryTotal,
            'str_for_delivery' => $_forDelivery,
            'str_total_delivered' => $_deliveredTotal,
            'str_delivered' => $_delivered
        ];
    }
}
