<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\EmpAccount as User;
use App\EmployeeLog;
use App\DocumentLogHistory;
use App\ListDueDemandAccPay;
use App\PaperSize;
use Carbon\Carbon;
use DB;
use Auth;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexLDDAP(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $paperSizes = PaperSize::all();
        $lddapList = DB::table('tbllddap as lddap')
                       ->select('lddap.*', 'dv.dv_no')
                       ->leftJoin('tbldv as dv', 'dv.id', '=', 'lddap.dv_id')
                       ->whereNull('lddap.deleted_at');

        if (!empty($search)) {
            $lddapList = $lddapList->where(function ($query) use ($search) {
                                    $query->where('lddap.date_for_approval', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.date_approved', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.total_amount_words', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.total_amount', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.status', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.department', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.entity_name', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.operating_unit', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.nca_no', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.lddap_ada_no', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.lddap_date', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.fund_cluster', 'LIKE', '%' . $search . '%')
                                          ->orWhere('lddap.mds_gsb_accnt_no', 'LIKE', '%' . $search . '%');
                                });
        }

        $lddapList = $lddapList->orderBy('lddap.lddap_id', 'desc')
                               ->paginate($pageLimit);

        foreach ($lddapList as $list) {
            $list->document_status = $this->checkDocStatus($list->code);
            $list->status = str_replace('_', ' ', strtoupper($list->status));
        }

        return view('pages.lddap', ['search' => $search,
                                    'list' => $lddapList,
                                    'pageLimit' => $pageLimit,
                                    'paperSizes' => $paperSizes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $actionURL = url('payment/lddap/store');
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.lddap_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.lddap', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();
        $dv = DB::table('tbldv')
                ->orderBy('id')
                ->get();

        return view('pages.create-lddap', ['actionURL' => $actionURL,
                                           'signatories' => $signatories,
                                           'dvDocs' => $dv]);
    }

    /**
     * Show the form for editing resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showEdit($lddapID) {
        $lddap = ListDueDemandAccPay::find($lddapID);
        $currentItems = DB::table('tbllddap_items')
                          ->where([
                              ['lddap_id', $lddapID],
                              ['category', 'current_year']
                            ])
                          ->get();
        $priorItems = DB::table('tbllddap_items')
                        ->where([
                            ['lddap_id', $lddapID],
                            ['category', 'prior_year']
                          ])
                        ->get();
        $actionURL = url('payment/lddap/update/' . $lddapID);
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.lddap_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.lddap', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();
        $dv = DB::table('tbldv')
                ->orderBy('id')
                ->get();

        return view('pages.edit-lddap', ['lddap' => $lddap,
                                         'currentItems' => $currentItems,
                                         'priorItems' => $priorItems,
                                         'currentGross' => 0,
                                         'currentWithholding' => 0,
                                         'currentNet' => 0,
                                         'priorGross' => 0,
                                         'priorWithholding' => 0,
                                         'priorNet' => 0,
                                         'totalGross' => 0,
                                         'totalWithholding' => 0,
                                         'totalNet' => 0,
                                         'actionURL' => $actionURL,
                                         'signatories' => $signatories,
                                         'dvDocs' => $dv]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $dvID = $request->dv_id;
        $sigCertCorrect = $request->sig_cert_correct;
        $sigApproval1 = $request->sig_approval_1;
        $sigApproval2 = $request->sig_approval_2;
        $sigApproval3 = $request->sig_approval_3;
        $totalAmountWords = $request->total_amount_words;
        $totalAmount = $request->total_amount;
        $sigAgencyAuth1 = $request->sig_agency_auth_1;
        $sigAgencyAuth2 = $request->sig_agency_auth_2;
        $sigAgencyAuth3 = $request->sig_agency_auth_3;
        $sigAgencyAuth4 = $request->sig_agency_auth_4;
        $department = $request->department;
        $entityName = $request->entity_name;
        $operatingUnit = $request->operating_unit;
        $ncaNo = $request->nca_no;
        $lddapAdaNo = $request->lddap_ada_no;
        $lddapDate = $request->lddap_date;
        $fundCluster = $request->fund_cluster;
        $mdsGsbAccntNo = $request->mds_gsb_accnt_no;
        $listCurrentCreditorName = $request->current_creditor_name;
        $listCurrentCreditorAccNo = $request->current_creditor_acc_no;
        $listCurrentOrsNo = $request->current_ors_no;
        $listcurrentAllotClassUacs = $request->current_allot_class_uacs;
        $listCurrentGrossAmount = $request->current_gross_amount;
        $listCurrentWitholdTax = $request->current_withold_tax;
        $listCurrentNetAmount = $request->current_net_amount;
        $listCurrentRemarks = $request->current_remarks;
        $listPriorCreditorName = $request->prior_creditor_name;
        $listPriorCreditorAccNo = $request->prior_creditor_acc_no;
        $listPriorOrsNo = $request->prior_ors_no;
        $listPriorAllotClassUacs = $request->prior_allot_class_uacs;
        $listPriorGrossAmount = $request->prior_gross_amount;
        $listPriorWitholdTax = $request->prior_withold_tax;
        $listPriorNetAmount = $request->prior_net_amount;
        $listPriorRemarks = $request->prior_remarks;

        try {
            $instanceLDDAP = new ListDueDemandAccPay;
            $instanceLDDAP->dv_id = $dvID;
            $instanceLDDAP->sig_cert_correct = $sigCertCorrect;
            $instanceLDDAP->sig_approval_1 = $sigApproval1;
            $instanceLDDAP->sig_approval_2 = $sigApproval2;
            $instanceLDDAP->sig_approval_3 = $sigApproval3;
            $instanceLDDAP->total_amount_words = $totalAmountWords;
            $instanceLDDAP->total_amount = $totalAmount;
            $instanceLDDAP->sig_agency_auth_1 = $sigAgencyAuth1;
            $instanceLDDAP->sig_agency_auth_2 = $sigAgencyAuth2;
            $instanceLDDAP->sig_agency_auth_3 = $sigAgencyAuth3;
            $instanceLDDAP->sig_agency_auth_4 = $sigAgencyAuth4;
            $instanceLDDAP->department = $department;
            $instanceLDDAP->entity_name = $entityName;
            $instanceLDDAP->operating_unit = $operatingUnit;
            $instanceLDDAP->nca_no = $ncaNo;
            $instanceLDDAP->lddap_ada_no = $lddapAdaNo;
            $instanceLDDAP->lddap_date = $lddapDate;
            $instanceLDDAP->fund_cluster = $fundCluster;
            $instanceLDDAP->mds_gsb_accnt_no = $mdsGsbAccntNo;
            $instanceLDDAP->save();

            if (is_array($listCurrentCreditorName)) {
                if (count($listCurrentCreditorName) > 0) {
                    $category = 'current_year';

                    foreach ($listCurrentCreditorName as $ctr => $creditorName) {
                        $itemID = "cy-$pKey-" . ($ctr + 1);
                        $instanceLDDAPItem =

                        DB::table('tbllddap_items')->insert([
                            'lddap_item_id' => $itemID,
                            'lddap_id' => $pKey,
                            'category' => $category,
                            'creditor_name' => $creditorName,
                            'creditor_acc_no' => $listCurrentCreditorAccNo[$ctr],
                            'ors_no' => $listCurrentOrsNo[$ctr],
                            'allot_class_uacs' => $listcurrentAllotClassUacs[$ctr],
                            'gross_amount' => $listCurrentGrossAmount[$ctr],
                            'withold_tax' => $listCurrentWitholdTax[$ctr],
                            'net_amount' => $listCurrentNetAmount[$ctr],
                            'remarks' => $listCurrentRemarks[$ctr],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                }
            }

            if (is_array($listPriorCreditorName)) {
                if (count($listPriorCreditorName) > 0) {
                    $category = 'prior_year';

                    foreach ($listPriorCreditorName as $ctr =>$creditorName) {
                        $itemID = "py-$pKey-" . ($ctr + 1);

                        DB::table('tbllddap_items')->insert([
                            'lddap_item_id' => $itemID,
                            'lddap_id' => $pKey,
                            'category' => $category,
                            'creditor_name' => $creditorName,
                            'creditor_acc_no' => $listPriorCreditorAccNo[$ctr],
                            'ors_no' => $listPriorOrsNo[$ctr],
                            'allot_class_uacs' => $listPriorAllotClassUacs[$ctr],
                            'gross_amount' => $listPriorGrossAmount[$ctr],
                            'withold_tax' => $listPriorWitholdTax[$ctr],
                            'net_amount' => $listPriorNetAmount[$ctr],
                            'remarks' => $listPriorRemarks[$ctr],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                }
            }

            $documentType = 'LDDAP';
            $routeName = 'lddap';

            $msg = "$documentType successfully created.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName)
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $lddapID) {
        $lddap = ListDueDemandAccPay::find($lddapID);
        $pKey = $lddapID;
        $redirectURL = 'payment/lddap';

        $dvID = $request->dv_id;
        $sigCertCorrect = $request->sig_cert_correct;
        $sigApproval1 = $request->sig_approval_1;
        $sigApproval2 = $request->sig_approval_2;
        $sigApproval3 = $request->sig_approval_3;
        $totalAmountWords = $request->total_amount_words;
        $totalAmount = $request->total_amount;
        $sigAgencyAuth1 = $request->sig_agency_auth_1;
        $sigAgencyAuth2 = $request->sig_agency_auth_2;
        $sigAgencyAuth3 = $request->sig_agency_auth_3;
        $sigAgencyAuth4 = $request->sig_agency_auth_4;
        $department = $request->department;
        $entityName = $request->entity_name;
        $operatingUnit = $request->operating_unit;
        $ncaNo = $request->nca_no;
        $lddapAdaNo = $request->lddap_ada_no;
        $lddapDate = $request->lddap_date;
        $fundCluster = $request->fund_cluster;
        $mdsGsbAccntNo = $request->mds_gsb_accnt_no;
        $listCurrentCreditorName = $request->current_creditor_name;
        $listCurrentCreditorAccNo = $request->current_creditor_acc_no;
        $listCurrentOrsNo = $request->current_ors_no;
        $listcurrentAllotClassUacs = $request->current_allot_class_uacs;
        $listCurrentGrossAmount = $request->current_gross_amount;
        $listCurrentWitholdTax = $request->current_withold_tax;
        $listCurrentNetAmount = $request->current_net_amount;
        $listCurrentRemarks = $request->current_remarks;
        $listPriorCreditorName = $request->prior_creditor_name;
        $listPriorCreditorAccNo = $request->prior_creditor_acc_no;
        $listPriorOrsNo = $request->prior_ors_no;
        $listPriorAllotClassUacs = $request->prior_allot_class_uacs;
        $listPriorGrossAmount = $request->prior_gross_amount;
        $listPriorWitholdTax = $request->prior_withold_tax;
        $listPriorNetAmount = $request->prior_net_amount;
        $listPriorRemarks = $request->prior_remarks;

        $lddap->dv_id = $dvID;
        $lddap->sig_cert_correct = $sigCertCorrect;
        $lddap->sig_approval_1 = $sigApproval1;
        $lddap->sig_approval_2 = $sigApproval2;
        $lddap->sig_approval_3 = $sigApproval3;
        $lddap->total_amount_words = $totalAmountWords;
        $lddap->total_amount = $totalAmount;
        $lddap->sig_agency_auth_1 = $sigAgencyAuth1;
        $lddap->sig_agency_auth_2 = $sigAgencyAuth2;
        $lddap->sig_agency_auth_3 = $sigAgencyAuth3;
        $lddap->sig_agency_auth_4 = $sigAgencyAuth4;
        $lddap->department = $department;
        $lddap->entity_name = $entityName;
        $lddap->operating_unit = $operatingUnit;
        $lddap->nca_no = $ncaNo;
        $lddap->lddap_ada_no = $lddapAdaNo;
        $lddap->lddap_date = $lddapDate;
        $lddap->fund_cluster = $fundCluster;
        $lddap->mds_gsb_accnt_no = $mdsGsbAccntNo;

        try {
            $lddap->save();

            DB::table('tbllddap_items')->where('lddap_id', $lddapID)->delete();

            if (is_array($listCurrentCreditorName)) {
                if (count($listCurrentCreditorName) > 0) {
                    $category = 'current_year';

                    foreach ($listCurrentCreditorName as $ctr => $creditorName) {
                        $itemID = "cy-$pKey-" . ($ctr + 1);

                        DB::table('tbllddap_items')->insert([
                            'lddap_item_id' => $itemID,
                            'lddap_id' => $pKey,
                            'category' => $category,
                            'creditor_name' => $creditorName,
                            'creditor_acc_no' => $listCurrentCreditorAccNo[$ctr],
                            'ors_no' => $listCurrentOrsNo[$ctr],
                            'allot_class_uacs' => $listcurrentAllotClassUacs[$ctr],
                            'gross_amount' => $listCurrentGrossAmount[$ctr],
                            'withold_tax' => $listCurrentWitholdTax[$ctr],
                            'net_amount' => $listCurrentNetAmount[$ctr],
                            'remarks' => $listCurrentRemarks[$ctr],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                }
            }

            if (is_array($listPriorCreditorName)) {
                if (count($listPriorCreditorName) > 0) {
                    $category = 'prior_year';

                    foreach ($listPriorCreditorName as $ctr =>$creditorName) {
                        $itemID = "py-$pKey-" . ($ctr + 1);

                        DB::table('tbllddap_items')->insert([
                            'lddap_item_id' => $itemID,
                            'lddap_id' => $pKey,
                            'category' => $category,
                            'creditor_name' => $creditorName,
                            'creditor_acc_no' => $listPriorCreditorAccNo[$ctr],
                            'ors_no' => $listPriorOrsNo[$ctr],
                            'allot_class_uacs' => $listPriorAllotClassUacs[$ctr],
                            'gross_amount' => $listPriorGrossAmount[$ctr],
                            'withold_tax' => $listPriorWitholdTax[$ctr],
                            'net_amount' => $listPriorNetAmount[$ctr],
                            'remarks' => $listPriorRemarks[$ctr],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                }
            }

            $logEmpMessage = "updated the list of due and demandable accounts payable $pKey.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "List Of Due And Demandable Accounts Payable $pKey successfully updated.";
            return redirect(url($redirectURL))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the List
                    Of Due And Demandable Accounts Payable $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     * Soft delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($lddapID) {
        try {
            ListDueDemandAccPay::where('lddap_id', $lddapID)->delete();

            $logEmpMessage = "deleted the list of due and demandable accounts payable $lddapID.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "List Of Due And Demandable Accounts Payable $lddapID
                    successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered deleting the List
                    Of Due And Demandable Accounts Payable $lddapID.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function forApproval($lddapID) {
        $lddap = ListDueDemandAccPay::where('lddap_id', $lddapID)->first();
        $isDocGenerated = $this->checkDocGenerated($lddap->code);
        $docName = 'LDDAP';
        $redirectURL = 'payment/lddap?search=' . $lddapID;

        /*
        try {
            if ($isDocGenerated) {
                $lddap->date_for_approval = date('Y-m-d H:i:s');
                $lddap->status = 'for_approval';
                $lddap->save();

                $logEmpMessage = "set the " . $docName . " $lddapID to for approval.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "$docName $lddapID is now set to 'For Approval'.";
                return redirect(url($redirectURL))->with('success', $msg);
            } else {
                $msg = "Generate first the " . $docName . " $lddapID document.";
                return redirect(url($redirectURL))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered setting the
                   $docName $lddapID to for approval.";
            return redirect(url()->previous())->with('failed', $msg);
        }*/

        $lddap->date_for_approval = date('Y-m-d H:i:s');
        $lddap->status = 'for_approval';
        $lddap->save();

        $logEmpMessage = "set the " . $docName . " $lddapID to for approval.";
        $this->logEmployeeHistory($logEmpMessage);

        $msg = "$docName $lddapID is now set to 'For Approval'.";
        return redirect(url($redirectURL))->with('success', $msg);
    }

    public function approve($lddapID) {
        $lddap = ListDueDemandAccPay::where('lddap_id', $lddapID)->first();
        $docName = 'LDDAP';
        $redirectURL = 'payment/lddap?search=' . $lddapID;

        try {
            if (!empty($lddap->date_for_approval)) {
                $lddap->date_approved = date('Y-m-d H:i:s');
                $lddap->status = 'approved';
                $lddap->save();

                $logEmpMessage = "set the " . $docName . " $lddapID to approved.";
                $this->logEmployeeHistory($logEmpMessage);

                $msg = "$docName $lddapID is now set to 'Approved'.";
                return redirect(url($redirectURL))->with('success', $msg);
            } else {
                $msg = "Generate first the " . $docName . " $lddapID document.";
                return redirect(url($redirectURL))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered approving the $docName $lddapID.";
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
