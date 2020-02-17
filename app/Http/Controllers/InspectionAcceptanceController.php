<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InspectionAcceptance;
use App\PurchaseOrder;
use App\Supplier;
use App\DisbursementVoucher;
use App\EmployeeLog;
use App\DocumentLogHistory;
use App\PaperSize;
use App\InventoryStock;
use Carbon\Carbon;
use DB;
use Auth;

class InspectionAcceptanceController extends Controller
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
        $pageLimit = 10;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $prList = DB::table('tblpr as pr')
                    ->select('pr.*', 'proj.project',
                              DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                    ->join('tblpo_jo as po', 'po.pr_id', '=', 'pr.id')
                    ->join('tbliar as iar as iar', 'iar.iar_no', 'LIKE',
                            DB::RAW('CONCAT("%", po.po_no, "%")'))
                    ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'pr.requested_by')
                    ->join('tblpr_status AS status', 'status.id', '=', 'pr.status')
                    ->leftJoin('tblprojects AS proj', 'proj.id', '=', 'pr.project_id')
                    ->whereNull('pr.deleted_at')
                    ->where('status.id', 6);

        if (!empty($search)) {
            $prList = $prList->where(function ($query) use ($search) {
                                   $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('po.po_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('iar.iar_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.date_pr', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('iar.code', 'LIKE', '%' . $search . '%');
                               });
        }

        if (Auth::user()->role == 3 || Auth::user()->role == 4 || Auth::user()->role == 6) {
            $prList = $prList->where('requested_by', Auth::user()->emp_id);
        }

        if (Auth::user()->role == 5) {
            $prList = $prList->where('emp.division_id', Auth::user()->division_id);
        }

        $prList = $prList->orderBy('pr.id', 'desc')
                         ->distinct()
                         ->paginate($pageLimit, ['pr.id']);

        foreach ($prList as $list) {
            $countIAR = 0;
            $iarItem = array();
            $suppliers = DB::table('tblpo_jo as po')
                           ->select('po.awarded_to', 'bidder.company_name', 'po.po_no', 'iar.iar_no',
                                    'status.id AS sID', 'iar.date_iar', 'iar.code')
                           ->join('tbliar as iar as iar', 'iar.iar_no', 'LIKE',
                                  DB::RAW('CONCAT("%", po.po_no, "%")'))
                           ->join('tblsuppliers as bidder', 'bidder.id', '=', 'po.awarded_to')
                           ->join('tblpr_status AS status', 'status.id', '=', 'po.status')
                           ->where([['iar.pr_id', $list->id],
                                    ['po.awarded_to', '<>', 0],
                                    ['po.status', '>', 8]])
                           ->whereNotNull('po.awarded_to')
                           ->whereNull('iar.deleted_at')
                           ->orderBy('po.po_no');

            if (!empty($search)) {
                $suppliers = $suppliers->where('iar.iar_no', 'LIKE', '%' . $search . '%');
            }

            $suppliers = $suppliers->get();

            $countIAR = count($suppliers);

            foreach ($suppliers as $bid) {
                $inventoryCount = InventoryStock::where('po_no', $bid->po_no)->count();
                $logshist = $this->checkDocStatus($bid->code);
                $iarItem[] = (object)['pr_no' => $list->pr_no,
                                      'po_no' => $bid->po_no,
                                      'iar_no' => $bid->iar_no,
                                      'date_iar' => $bid->date_iar,
                                      'date_issued' => $logshist->date_issued,
                                      'date_recieved' => $logshist->date_received,
                                      'awarded_to' => $bid->awarded_to,
                                      'company_name' => $bid->company_name,
                                      'status_id' => $bid->sID,
                                      'inventory_count' => $inventoryCount];
            }

            $list->iar_item = $iarItem;
            $list->iar_count = $countIAR;
        }

        return view('pages.iar', ['search' => $search,
                                  'list' => $prList,
                                  'pageLimit' => $pageLimit,
                                  'paperSizes' => $paperSizes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $iarNo)
    {
        try {
            $dateIAR = $request['date_iar'];
            $invoiceNo = $request['invoice_no'];
            $dateInvoice = $request['date_invoice'];
            $sigInspection = $request['sig_inspection'];
            $sigSupply = $request['sig_supply'];
            $iar = InspectionAcceptance::where('iar_no', $iarNo)->first();

            if (empty($dateInvoice)) {
                $dateInvoice = NULL;
            }

            $iar->date_iar = $dateIAR;
            $iar->invoice_no = $invoiceNo;
            $iar->date_invoice = $dateInvoice;
            $iar->sig_inspection = $sigInspection;
            $iar->sig_supply = $sigSupply;
            $iar->save();

            $msg = "Inspection and Acceptance Report $iarNo successfully updated.";
            return redirect(url('procurement/iar?search=' . $iarNo))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the Inspection and
                    Acceptance Report $iarNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($poNo)
    {
        $iar = DB::table('tbliar as iar')
                 ->join('tblpo_jo as po', 'po.pr_id', '=', 'iar.pr_id')
                 ->join('tblpr as pr', 'pr.id', '=', 'iar.pr_id')
                 ->join('tbldivision as div', 'div.id', '=', 'pr.pr_division_id')
                 ->where('po.po_no', $poNo)
                 ->where('iar_no', 'LIKE', '%' . $poNo . '%')
                 ->first();
        $poItems = DB::table('tblpo_jo_items as po')
                             ->join('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                             ->where('po.po_no', $poNo)
                             ->where('po.excluded', 'n')
                             ->get();
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.iar_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->orderBy('emp.firstname')
                         ->where([['sig.iar', 'y'],
                                  ['sig.active', 'y']])
                         ->get();
        $suppliers = Supplier::all();

        return view('pages.create-iar', ['iar' => $iar,
                                         'poItems' => $poItems,
                                         'signatories' => $signatories,
                                         'suppliers' => $suppliers]);
    }

    public function showIssuedTo($iarNo) {
        $issuedTo = DB::table('tblemp_accounts as emp')
                      ->join('tblsignatories as sig', 'sig.emp_id', '=', 'emp.emp_id')
                      ->where([['iar', 'y'], ['iar_sign_type', 'inspector']])
                      ->get();

        return view('pages.view-iar-issue', ['key' => $iarNo,
                                             'issuedTo' => $issuedTo]);
    }

    public function issue(Request $request, $iarNo) {
        $iar = InspectionAcceptance::where('iar_no', $iarNo)->first();

        try {
            $remarks = $request['remarks'];
            $issuedTo = $request['issued_to'];
            $code = $iar->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $docStatus = $this->checkDocStatus($code);

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $this->logTrackerHistory($code, Auth::user()->emp_id, $issuedTo, "issued", $remarks);

                    $logEmpMessage = "issued the inspection and acceptance report $iarNo.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "Inspection and Acceptance $iarNo is now set to issued.";
                    return redirect(url('procurement/iar?search=' . $iarNo))
                                   ->with('success', $msg);
                } else {
                    $msg = "Generated first the Inspection and Acceptance $iarNo document.";
                    return redirect(url('procurement/iar?search=' . $iarNo))
                                   ->with('warning', $msg);
                }
            } else {
                $msg = "Inspection and Acceptance $iarNo is already issued.";
                return redirect(url('procurement/iar?search=' . $iarNo))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered issuing the Inspection and
                    Acceptance Report $iarNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function inspect($iarNo) {
        $iar = InspectionAcceptance::where('iar_no', $iarNo)->first();

        try {
            $poNo = substr($iarNo, 4);
            $po = PurchaseOrder::where('po_no', $poNo)->first();
            $code = $iar->code;
            $isDocGenerated = $this->checkDocGenerated($code);

            if ($isDocGenerated) {
                $dv = new DisbursementVoucher;
                $dv->pr_id = $iar->pr_id;
                $dv->ors_id = $iar->ors_id;
                $dv->particulars = "To payment of...";
                $dv->sig_accounting = $po->sig_funds_available;
                $dv->sig_agency_head = $po->sig_approval;
                $dv->module_class_id = 3;
                $dv->code = $this->generateTrackerCode('DV', $poNo, 3);
                $dv->save();

                $po->status = 10;
                $po->save();

                $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "received");

                $logEmpMessage = "received and set to inspected the inspection and
                                  acceptance report $iarNo.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "Inspection and Acceptance Report $iarNo is now set to recieved
                        & inspected and ready for Disbursement Voucher.";
                return redirect(url('procurement/iar?search=' . $iarNo))
                               ->with('success', $msg);
            } else {
                $msg = "Generate first the Inspection and Acceptance
                        Report $iarNo document first.";
                return redirect(url('procurement/iar?search=' . $iarNo))
                               ->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered receiving and set to inspected the
                    Inspection and Acceptance Report $iarN.";
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
