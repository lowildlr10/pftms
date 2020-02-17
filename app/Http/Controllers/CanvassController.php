<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseRequest;
use App\Canvass;
use App\Abstracts;
use App\PurchaseOrder;
use App\OrsBurs;
use App\InspectionAcceptance;
use App\DisbursementVoucher;
use App\InventoryStock;

use App\User;
use App\EmployeeLog;
use App\DocumentLogHistory;
use App\PaperSize;
use Carbon\Carbon;
use DB;
use Auth;

class CanvassController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $canvassList = DB::table('tblcanvass AS canvass')
                          ->select('canvass.*', 'pr.pr_no', 'pr.date_pr','proj.project', 'pr.purpose',
                                    DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                          ->join('tblpr AS pr', 'pr.id', '=', 'canvass.pr_id')
                          ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'pr.requested_by')
                          ->join('tblpr_status AS status', 'status.id', '=', 'pr.status')
                          ->leftJoin('tblprojects AS proj', 'proj.id', '=', 'pr.project_id')
                          ->whereNull('canvass.deleted_at');

        if (!empty($search)) {
            $canvassList = $canvassList->where(function ($query) use ($search) {
                                   $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.date_pr', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.code', 'LIKE', '%' . $search . '%')
                                         ->orWhere('canvass.code', 'LIKE', '%' . $search . '%');
                               });
        }

        if (Auth::user()->role == 3 || Auth::user()->role == 4 || Auth::user()->role == 6) {
            $canvassList = $canvassList->where('requested_by', Auth::user()->emp_id);
        }

        if (Auth::user()->role == 5) {
            $canvassList = $canvassList->where('emp.division_id', Auth::user()->division_id);
        }

        $canvassList = $canvassList->orderBy('pr.id', 'desc')
                                   ->paginate($pageLimit);

        foreach ($canvassList as $list) {
            $list->document_status = $this->checkDocStatus($list->code);
        }

        return view('pages.canvass', ['search' => $search,
                                      'list' => $canvassList,
                                      'pageLimit' => $pageLimit,
                                      'paperSizes' => $paperSizes]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $canvass = Canvass::where('pr_id', $id)->first();
        $items = DB::table('tblpr_items AS itm')->select('itm.quantity', 'unit.unit', 'itm.item_description',
                                                         'itm.est_unit_cost', 'itm.est_total_cost', 'itm.group_no',
                                                         'itm.item_id')
                                                ->join('tblunit_issue AS unit', 'unit.id','=', 'itm.unit_issue')
                                                ->where('itm.pr_id' , '=', $id)
                                                ->orderByRaw('LENGTH(itm.item_id)')
                                                ->orderBy('itm.item_id')
                                                ->get();
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.rfq', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();


        return view('pages.edit-rfq', ['prID' => $id,
                                       'prItems' => $items,
                                       'canvass' => $canvass,
                                       'signatories' => $signatories]);
    }

    public function showIssuedTo($id) {
        $issuedTo = User::orderBy('firstname')->get();

        return view('pages.view-rfq-issue', ['key' => $id,
                                             'issuedTo' => $issuedTo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $itemID = $request['pr_item_id'];
        $groupNo = $request['canvass_group'];
        $dateCanvas = $request['date_canvass'];
        $sigRFQ = $request['sig_rfq'];

        try {
            $rfq = Canvass::where('pr_id', $id)->first();
            $rfq->date_canvass = $dateCanvas;
            $rfq->sig_rfq = $sigRFQ;
            $rfq->save();

            $pr = PurchaseRequest::where('id', $id)->first();
            $prNo = $pr->pr_no;
            $pr->status = 5;
            $pr->save();

            foreach ($itemID as $key => $itmID) {
                $group = $groupNo[$key];
                DB::table('tblpr_items')->where('item_id', $itmID)
                                        ->update(['group_no' => $group]);
            }

            // Delete dependent documents
            Abstracts::where('pr_id', $id)->delete();
            //DB::table('tblabstract_items')->where('pr_id', $id)->delete();
            PurchaseOrder::where('pr_id', $id)->delete();
            //DB::table('tblpo_jo_items')->where('pr_id', $id)->delete();
            OrsBurs::where('pr_id', $id)->delete();
            InspectionAcceptance::where('pr_id', $id)->delete();
            DisbursementVoucher::where('pr_id', $id)->delete();
            InventoryStock::where('pr_id', $id)->delete();
            //DB::table('tblinventory_stocks_issue')->where('pr_id', $id)->delete();

            $this->logTrackerHistory($rfq->code, Auth::user()->emp_id, 0, '-');

            $logEmpMessage = "updated the request for quotation $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Request for Quotation $prNo successfully updated.";
            return redirect(url('procurement/rfq?search=' . $prNo))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the Request for Quotation $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function issue(Request $request, $id) {
        $pr = PurchaseRequest::where('id', $id)->first();
        $prNo = $pr->pr_no;

        try {
            $rfq = Canvass::where('pr_id', $id)->first();
            $issuedTo = $request['issued_to'];
            $remarks = $request['remarks'];
            $code = $rfq->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $docStatus = $this->checkDocStatus($code);

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $this->logTrackerHistory($code, Auth::user()->emp_id, $issuedTo, "issued", $remarks);

                    $logEmpMessage = "issued the request for quotation $prNo.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "Request for Quotation $prNo is now set to issued.";
                    return redirect(url('procurement/rfq?search=' . $prNo))->with('success', $msg);
                } else {
                    $msg = "Generate first the Request for Quotation $prNo document.";
                    return redirect(url('procurement/rfq?search=' . $prNo))->with('warning', $msg);
                }
            } else {
                $msg = "Request for Quotation $prNo is already issued.";
                return redirect(url('procurement/rfq?search=' . $prNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered issuing the Request for Quotation $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function receive($id) {
        $pr = PurchaseRequest::where('id', $id)->first();
        $prNo = $pr->pr_no;

        try {
            $rfq = Canvass::where('pr_id', $id)->first();
            $code = $rfq->code;
            $docStatus = $this->checkDocStatus($code);

            if (!empty($docStatus->date_issued)) {
                $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "received");

                $abstract = DB::table('tblabstract')
                               ->where('pr_id', $id)
                               ->first();

                if (!$abstract) {
                    $abstract = new Abstracts;
                    $abstract->pr_id = $id;
                    $abstract->code = $this->generateTrackerCode('ABSTRACT', $id, 3);
                    $abstract->save();

                    $code = $abstract->code;
                    $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "issued");
                } else {
                    Abstracts::where('pr_id', $id)->restore();
                }

                $logEmpMessage = "received the request for quotation $prNo.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "Request for Quotation $prNo is now set to received and ready for abstract.";
                return redirect(url('procurement/rfq?search=' . $prNo))->with('success', $msg);
            } else {
                $msg = "You should issue this Request for Quotation $prNo first.";
                return redirect(url('procurement/rfq?search=' . $prNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered receiving the Request for Quotation $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    private function checkDocGenerated($code) {
        $logs = DB::table('tbldocument_logs_history')
                  ->where([
                        ['code', $code],
                        ['action', 'document_generated']
                    ])
                  ->orderBy('logshist.created_at', 'desc')
                  ->count();

        return $logs;
    }

    private function checkDocStatus($code) {
        $logs = DB::table('tbldocument_logs_history')
                  ->where('code', $code)
                  ->orderBy('created_at', 'desc')
                  ->get();
        $currentStatus = (object) ["issued_by" => NULL,
                                   "date_issued" => NULL,
                                   "received_by" => NULL,
                                   "date_received" => NULL,
                                   "issued_back_by" => NULL,
                                   "date_issued_back" => NULL,
                                   "received_back_by" => NULL,
                                   "date_received_back" => NULL];

        if (count($logs) > 0) {
            foreach ($logs as $log) {
                if ($log->action != "-") {
                    switch ($log->action) {
                        case 'issued':
                            $currentStatus->issued_by = $log->action;
                            $currentStatus->date_issued = $log->date;
                            break;

                        case 'received':
                            $currentStatus->received_by = $log->action;
                            $currentStatus->date_received = $log->date;
                            break;

                        case 'issued_back':
                            $currentStatus->issued_back_by = $log->action;
                            $currentStatus->date_issued_back = $log->date;
                            break;

                        case 'received_back':
                            $currentStatus->received_back_by = $log->action;
                            $currentStatus->date_received_back = $log->date;
                            break;

                        default:
                            # code...
                            break;
                    }
                } else {
                    break;
                }
            }
        }

        return $currentStatus;
    }

    private function generateTrackerCode($modAbbr, $pKey, $modClass) {
        $modAbbr = strtoupper($modAbbr);
        $pKey = strtoupper($pKey);

        return $modAbbr . "-" . $pKey . "-" . $modClass . "-" . date('mdY');
    }

    private function logEmployeeHistory($msg, $emp = "") {
        $empLog = new EmployeeLog;
        $empLog->emp_id = empty($emp) ? Auth::user()->emp_id: $emp;
        $empLog->message = $msg;
        $empLog->save();
    }

    private function logTrackerHistory($code, $empFrom, $empTo, $action, $remarks = "") {
        $docHistory = new DocumentLogHistory;
        $docHistory->code = $code;
        $docHistory->date = Carbon::now();
        $docHistory->emp_from = $empFrom;
        $docHistory->emp_to = $empTo;
        $docHistory->action = $action;
        $docHistory->remarks = $remarks;
        $docHistory->save();
    }
}
