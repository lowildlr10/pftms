<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\EmployeeLog;
use App\DocumentLogHistory;
use App\PaperSize;
use Carbon\Carbon;
use DB;
use Auth;
use App\LiquidationReport;


class LiquidationController extends Controller
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
    public function indexCashAdvLiquidation(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $liqList = DB::table('tblliquidation as liq')
                     ->select('liq.*', DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                     ->join('tblemp_accounts as emp', 'emp.emp_id', '=', 'liq.sig_claimant')
                     ->whereNull('liq.deleted_at');

        if (!empty($search)) {
            $liqList = $liqList->where(function ($query)  use ($search) {
                                    $query->where('liq.period_covered', 'LIKE', '%' . $search . '%')
                                          ->orWhere('liq.particulars', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                          ->orWhere('liq.serial_no', 'LIKE', '%' . $search . '%')
                                          ->orWhere('liq.dv_no', 'LIKE', '%' . $search . '%')
                                          ->orWhere('liq.id', 'LIKE', '%' . $search . '%');
                                });
        }

        if (Auth::user()->role != 1 && Auth::user()->role != 3) {
            $liqList = $liqList->where('liq.sig_claimant', Auth::user()->emp_id);
            $isOrdinaryUser = true;
        } else {
            $isOrdinaryUser = false;
        }

        $liqList = $liqList->orderBy('liq.id', 'desc')
                           ->paginate($pageLimit);

        foreach ($liqList as $list) {
            $list->document_status = $this->checkDocStatus($list->code);

            if (!$isOrdinaryUser) {
                if (!empty($list->document_status->date_issued) &&
                    $list->sig_claimant != Auth::user()->emp_id) {
                    $list->display_menu = true;
                } else {
                    $list->display_menu = false;
                }

                if ($list->sig_claimant == Auth::user()->emp_id) {
                    $list->display_menu = true;
                }
            } else {
                $list->display_menu = true;
            }
        }

        return view('pages.liquidation', ['search' => $search,
                                          'list' => $liqList,
                                          'pageLimit' => $pageLimit,
                                          'paperSizes' => $paperSizes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate()
    {
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.liquidation_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.liquidation', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();
        $actionURL = url('cadv-reim-liquidation/ors-burs/store');

        if (Auth::user()->role != 1 && Auth::user()->role != 3) {
            $claimants = DB::table('tblemp_accounts')
                           ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                   'position', 'emp_id')
                           ->where('emp_id', Auth::user()->emp_id)
                           ->orderBy('firstname')
                           ->get();
        } else {
            $claimants = DB::table('tblemp_accounts')
                           ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                   'position', 'emp_id')
                           ->where('active', 'y')
                           ->orderBy('firstname')
                           ->get();
        }
        return view('pages.create-liquidation', ['actionURL' => $actionURL,
                                                 'claimants' => $claimants,
                                                 'signatories' => $signatories]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $liquidationData = LiquidationReport::find($id);
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.liquidation_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.liquidation', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();
        $actionURL = url('cadv-reim-liquidation/liquidation/update/' . $id);

        if (Auth::user()->role != 1 && Auth::user()->role != 3) {
            $claimants = DB::table('tblemp_accounts')
                           ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                   'position', 'emp_id')
                           ->where('emp_id', Auth::user()->emp_id)
                           ->orderBy('firstname')
                           ->get();
        } else {
            $claimants = DB::table('tblemp_accounts')
                           ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                   'position', 'emp_id')
                           ->where('active', 'y')
                           ->orderBy('firstname')
                           ->get();
        }

        return view('pages.edit-liquidation', ['dat' => $liquidationData,
                                               'actionURL' => $actionURL,
                                               'claimants' => $claimants,
                                               'signatories' => $signatories]);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $liq = LiquidationReport::find($id);
        $periodCover = $request->period_covered;
        $serialNo = $request->serial_no;
        $dateLiquidation = !empty($request->date_liquidation) ? $request->date_liquidation: NULL;
        $responsibilityCenter = $request->responsibility_center;
        $particulars = $request->particulars;
        $amount = $request->amount;
        $totalAmount = $request->total_amount;
        $AmountCashAdvance = $request->amount_cash_adv;
        $orNo = $request->or_no;
        $orDTD = !empty($request->or_dtd) ? $request->or_dtd: NULL;
        $amountRefunded = $request->amount_refunded;
        $amountReimbursed = $request->amount_reimbursed;
        $sigClaimant = $request->sig_claimant;
        $sigSupervisor = $request->sig_supervisor;
        $sigAccounting = $request->sig_accounting;
        $dateClaimant = !empty($request->date_claimant) ? $request->date_claimant: NULL;
        $dateSupervisor = !empty($request->date_supervisor) ? $request->date_supervisor: NULL;
        $dateAccounting = !empty($request->date_accounting) ? $request->date_accounting: NULL;
        $entityNamwe = $request->entity_name;
        $fundCluster = $request->fund_cluster;
        $jevNo = $request->jev_no;
        $redirectURL = "cadv-reim-liquidation/liquidation?search=" . $id;

        if ($liq) {
            $liq->period_covered = $periodCover;
            $liq->serial_no = $serialNo;
            $liq->date_liquidation = $dateLiquidation;
            $liq->responsibility_center = $responsibilityCenter;
            $liq->particulars = $particulars;
            $liq->amount = $amount;
            $liq->total_amount = $totalAmount;
            $liq->amount_cash_adv = $AmountCashAdvance;
            $liq->or_no = $orNo;
            $liq->or_dtd = $orDTD;
            $liq->amount_refunded = $amountRefunded;
            $liq->amount_reimbursed = $amountReimbursed;
            $liq->sig_claimant = $sigClaimant;
            $liq->sig_supervisor = $sigSupervisor;
            $liq->sig_accounting = $sigAccounting;
            $liq->date_claimant = $dateClaimant;
            $liq->date_supervisor = $dateSupervisor;
            $liq->date_accounting = $dateAccounting;
            $liq->entity_name = $entityNamwe;
            $liq->fund_cluster = $fundCluster;
            $liq->jev_no = $jevNo;

            try {
                $liq->save();

                $logEmpMessage = "updated the liquidation report $id.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "Liquidation Report $id successfully updated.";
                return redirect(url($redirectURL))->with('success', $msg);
            } catch (\Throwable $th) {
                $msg = "There is an error encountered updating the
                        Liquidation Report $id.";
                return redirect(url()->previous())->with('failed', $msg);
            }
        } else {
            $msg = "Liquidation Report $id not found. Try again later.";
            return redirect(url()->previous())->with('warning', $msg);
        }
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

    public function showIssuedTo(Request $request, $id) {
        $issuedTo = User::orderBy('firstname')->get();
        $issueBack = (int)$request->back;

        return view('pages.view-liquidation-issue', ['key' => $id,
                                                     'issuedTo' => $issuedTo,
                                                     'issueBack' => $issueBack]);
    }

    public function issue(Request $request, $id) {
        $liq = LiquidationReport::find($id);
        $docType = "Liquidation Report";
        $pKey = $id;
        $redirectURL = "cadv-reim-liquidation/liquidation?search=" . $pKey;
        $issueBack = $request->back;
        $remarks = $request->remarks;
        $issuedTo = $request->issued_to;
        $code = $liq->code;
        $isDocGenerated = $this->checkDocGenerated($code);
        $docStatus = $this->checkDocStatus($code);

        try {
            if (!$issueBack) {
                if (empty($docStatus->date_issued)) {
                    if ($isDocGenerated) {
                        $this->logTrackerHistory($code, Auth::user()->emp_id, $issuedTo, "issued", $remarks);

                        $logEmpMessage = "issued the " . strtolower($docType) . " $pKey.";
                        $this->logEmployeeHistory($logEmpMessage);

                        $msg = "$docType $pKey is now set to issued.";
                        return redirect(url($redirectURL))->with('success', $msg);
                    } else {
                        $msg = "Generate first the $docType $pKey document.";
                        return redirect(url($redirectURL))->with('warning', $msg);
                    }
                } else {
                    $msg = "$docType $pKey is already issued.";
                    return redirect(url($redirectURL))->with('warning', $msg);
                }
            } else {
                $issueBackResponse = $this->issueBack($pKey, $code, $docType, $docStatus,
                                                      $issuedTo, $remarks);

                if ($issueBackResponse->status == 'success') {
                    return redirect(url($redirectURL))->with('success', $issueBackResponse->msg);
                } else {
                    return redirect(url()->previous())->with('failed', $issueBackResponse->msg);
                }
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered issuing the $docType $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    private function issueBack($pKey, $code, $docType, $docStatus, $issuedTo, $remarks) {
        if (empty($docStatus->date_issued_back)) {
            $this->logTrackerHistory($code, Auth::user()->emp_id, $issuedTo, "issued_back", $remarks);

            $logEmpMessage = "issued back the " . strtolower($docType) . " $pKey.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Issued back the $docType $pKey document.";
            $status = "success";
        } else {
            $msg = "There is an error encountered issuing back the $docType $pKey.";
            $status = "failed";
        }

        return (object) ['msg' => $msg,
                         'status' => $status];
    }

    public function receive(Request $request, $id) {
        $liq = LiquidationReport::find($id);
        $docType = "Liquidation Report";
        $pKey = $id;
        $redirectURL = "cadv-reim-liquidation/liquidation?search=" . $pKey;
        $receiveBack = $request->back;
        $code = $liq->code;
        $isDocGenerated = $this->checkDocGenerated($code);
        $docStatus = $this->checkDocStatus($code);

        try {
            if (!$receiveBack) {
                if (!empty($docStatus->date_issued) && empty($docStatus->date_received)) {
                    if ($isDocGenerated) {
                        $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "received");

                        $logEmpMessage = "received the " . strtolower($docType) . " $pKey.";
                        $this->logEmployeeHistory($logEmpMessage);

                        $msg = "$docType $pKey is now set to received.";
                        return redirect(url($redirectURL))->with('success', $msg);
                    } else {
                        $msg = "Generate first the $docType $pKey document.";
                        return redirect(url($redirectURL))->with('warning', $msg);
                    }
                } else {
                    $msg = "$docType $pKey is already received.";
                    return redirect(url($redirectURL))->with('warning', $msg);
                }
            } else {
                $receiveBackResponse = $this->receiveBack($pKey, $code, $docType,
                                                          $docStatus);

                if ($receiveBackResponse->status == 'success') {
                    return redirect(url($redirectURL))->with('success', $receiveBackResponse->msg);
                } else {
                    return redirect(url()->previous())->with('failed', $receiveBackResponse->msg);
                }
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered receiving the $docType $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function receiveBack($pKey, $code, $docType, $docStatus) {
        if (!empty($docStatus->date_issued_back) && empty($docStatus->date_received_back)) {
            $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "-");
            $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "received_back");

            $logEmpMessage = "received back the $docType $pKey.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Received back the $docType $pKey document.";
            $status = "success";
        } else {
            $msg = "There is an error encountered issuing back the $docType $pKey.";
            $status = "failed";
        }

        return (object) ['msg' => $msg,
                         'status' => $status];
    }

    public function liquidate($id) {
        $liq = LiquidationReport::find($id);
        $docType = "Liquidation Report";
        $pKey = $id;
        $redirectURL = "cadv-reim-liquidation/liquidation?search=" . $pKey;
        $code = $liq->code;
        $isDocGenerated = $this->checkDocGenerated($code);

        try {
            if ($isDocGenerated) {
                if (empty($liq->date_liquidated)) {
                    $liq->liquidated_by = Auth::user()->emp_id;
                    $liq->date_liquidated = date('Y-m-d H:i:s');
                    $liq->save();

                    $logEmpMessage = "liquidated the " . strtolower($docType) . " $pKey.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "$docType $pKey is now set to liquidated.";
                    return redirect(url($redirectURL))->with('success', $msg);
                } else {
                    $msg = "$docType $pKey is already liquidated.";
                    return redirect(url($redirectURL))->with('warning', $msg);
                }
            } else {
                $msg = "Generate first the $docType $pKey document.";
                return redirect(url($redirectURL))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered liquidating the $docType $pKey.";
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
                                   "date_received_back" => NULL,
                                   "issued_remarks" => NULL,
                                   "issued_back_remarks" => NULL];

        if (count($logs) > 0) {
            foreach ($logs as $log) {
                if ($log->action != "-") {
                    switch ($log->action) {
                        case 'issued':
                            $currentStatus->issued_remarks = $log->remarks;
                            $currentStatus->issued_by = $log->action;
                            $currentStatus->date_issued = $log->created_at;
                            break;

                        case 'received':
                            $currentStatus->received_by = $log->action;
                            $currentStatus->date_received = $log->created_at;
                            break;

                        case 'issued_back':
                            $currentStatus->issued_back_remarks = $log->remarks;
                            $currentStatus->issued_back_by = $log->action;
                            $currentStatus->date_issued_back = $log->created_at;
                            break;

                        case 'received_back':
                            $currentStatus->received_back_by = $log->action;
                            $currentStatus->date_received_back = $log->created_at;
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
