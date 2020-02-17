<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrsBurs;
use App\PurchaseOrder;
use App\DisbursementVoucher;
use App\Supplier;
use App\EmployeeLog;
use App\DocumentLogHistory;
use App\LiquidationReport;
use App\PaperSize;
use App\User;
use Carbon\Carbon;
use Auth;
use DB;

class OrsBursController extends Controller
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
        $orsList = DB::table('tblors_burs as ors')
        			 ->select('ors.*', 'ors.id as ors_id', 'bid.company_name', 'bid.address',
        					   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
        					  'proj.project', 'status.id AS sID', 'ors.date_ors_burs')
        			 ->join('tblpr as pr', 'pr.id', '=', 'ors.pr_id')
                     ->join('tblpo_jo as po', 'po.po_no', '=', 'ors.po_no')
                     ->join('tblpr_status AS status', 'status.id', '=', 'po.status')
        			 ->join('tblsuppliers as bid', 'bid.id', '=', 'ors.payee')
        			 ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'pr.requested_by')
        			 ->leftJoin('tblprojects AS proj', 'proj.id', '=', 'pr.project_id')
                     ->where('ors.module_class_id', 3)
                     ->where('po.status', '<>', 3)
        			 ->whereNull('ors.deleted_at');

        if (!empty($search)) {
            $orsList = $orsList->where(function ($query)  use ($search) {
                                   $query->where('ors.po_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.particulars', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('bid.company_name', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.document_type', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.code', 'LIKE', '%' . $search . '%');
                               });
        }

        if (Auth::user()->role == 3 || Auth::user()->role == 6) {
            $orsList = $orsList->where('requested_by', Auth::user()->emp_id);
        }

        if (Auth::user()->role == 5) {
            $orsList = $orsList->where('emp.division_id', Auth::user()->division_id);
        }

        $orsList = $orsList->orderBy('pr.id', 'desc')
                           ->paginate($pageLimit);

        foreach ($orsList as $list) {
            $list->document_status = $this->checkDocStatus($list->code);
            $list->display_menu = true;
        }

        return view('pages.ors-burs', ['search' => $search,
                                       'list' => $orsList,
                                       'pageLimit' => $pageLimit,
                                       'paperSizes' => $paperSizes,
                                       'type' => 'procurement',
                                       'colSpan' => 8]);
    }

    public function indexCashAdvLiquidation(Request $request)
    {
        $isOrdinaryUser = true;
        $pageLimit = 50;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $orsList = DB::table('tblors_burs as ors')
                     ->select('ors.*', DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
                              'ors.id as ors_id', 'ors.date_ors_burs')
                     ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'ors.payee')
                     ->where('ors.module_class_id', 2)
                     ->whereNull('ors.deleted_at');

        if (!empty($search)) {
            $orsList = $orsList->where(function ($query)  use ($search) {
                                   $query->where('ors.payee', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.id', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.date_ors_burs', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.particulars', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.code', 'LIKE', '%' . $search . '%');
                               });
        }

        if (Auth::user()->role != 1 && Auth::user()->role != 4) {
            $orsList = $orsList->where('ors.payee', Auth::user()->emp_id);
            $isOrdinaryUser = true;
        } else {
            $isOrdinaryUser = false;
        }

        $orsList = $orsList->orderBy('ors.id', 'desc')
                           ->paginate($pageLimit);

        foreach ($orsList as $list) {
            $dvCount = DisbursementVoucher::where('ors_id', $list->id)->count();
            $list->dv_count = $dvCount;
            $list->document_status = $this->checkDocStatus($list->code);

            if ($dvCount > 0) {
                $dv = DB::table('tbldv')->where('ors_id', $list->id)->first();
                $list->dv_id = $dv->id;
            }

            if (!$isOrdinaryUser) {
                if (!empty($list->document_status->date_issued) &&
                    $list->payee != Auth::user()->emp_id) {
                    $list->display_menu = true;
                } else {
                    $list->display_menu = false;
                }

                if ($list->payee == Auth::user()->emp_id) {
                    $list->display_menu = true;
                }
            } else {
                $list->display_menu = true;
            }
        }

        return view('pages.ors-burs', ['search' => $search,
                                       'list' => $orsList,
                                       'pageLimit' => $pageLimit,
                                       'paperSizes' => $paperSizes,
                                       'type' => 'cashadvance',
                                       'colSpan' => 5]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCreate(Request $request) {
        $moduleType = $request->module_type;
        $actionURL = "";
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.ors_burs_sign_type', 'sig.active',
                                  DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.ors', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();

        switch ($moduleType) {
            case 'cashadvance':
                if (Auth::user()->role != 1 && Auth::user()->role != 3 && Auth::user()->role != 4) {
                    $payees = DB::table('tblemp_accounts')
                                ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                        'position', 'emp_id')
                                ->where('emp_id', Auth::user()->emp_id)
                                ->orderBy('firstname')
                                ->get();
                } else {
                    $payees = DB::table('tblemp_accounts')
                                ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                         'position', 'emp_id')
                                ->where('active', 'y')
                                ->orderBy('firstname')
                                ->get();
                }

                $actionURL = url('cadv-reim-liquidation/ors-burs/store');
                break;

            case 'procurement':
                $payees = Supplier::all();
                $actionURL = url('procurement/ors-burs/store');
                break;

            default:
                # code...
                break;
        }

        return view('pages.create-ors-burs', ['actionURL' => $actionURL,
                                              'moduleType' => $moduleType,
                                              'payees' => $payees,
                                              'signatories' => $signatories]);
    }

    public function showEdit(Request $request, $key) {
        $moduleType = $request->module_type;
        $actionURL = "";
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.ors_burs_sign_type', 'sig.active',
                                  DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.ors', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();

        switch ($moduleType) {
            case 'cashadvance':
                $ors = DB::table('tblors_burs as ors')
                         ->select('ors.*', DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'ors.payee')
                         ->where([['ors.id', $key],
                                  ['module_class_id', 2]])
                         ->first();

                if (Auth::user()->role == 1 || Auth::user()->role == 3 || Auth::user()->role == 4) {
                    $payees = DB::table('tblemp_accounts')
                                ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                         'position', 'emp_id')
                                ->where('active', 'y')
                                ->orderBy('firstname')
                                ->get();
                } else {
                    $payees = DB::table('tblemp_accounts')
                                ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                                         'position', 'emp_id')
                                ->where('emp_id', Auth::user()->emp_id)
                                ->orderBy('firstname')
                                ->get();
                }

                $actionURL = url('cadv-reim-liquidation/ors-burs/update/' . $key);
                break;
            case 'procurement':
                $payees = Supplier::all();
                $ors = DB::table('tblors_burs as ors')
                         ->join('tblsuppliers as bid', 'bid.id', '=', 'ors.payee')
                         ->where([['ors.id', $key],
                                  ['module_class_id', 3]])
                         ->first();
                $actionURL = url('procurement/ors-burs/update/' . $key);
                break;
            default:
                # code...
                break;
        }

        return view('pages.edit-ors-burs', ['ors' => $ors,
                                            'actionURL' => $actionURL,
                                            'moduleType' => $moduleType,
                                            'payees' => $payees,
                                            'signatories' => $signatories]);
    }

    public function store(Request $request) {
        $documentType = $request->document_type;
        $moduleType = $request->module_type;
        $transactionType = !empty($request->transaction_type) ? $request->transaction_type: 'others';
        $getLastID = OrsBurs::orderBy('id', 'desc')->first();
        $pKey = $getLastID->id + 1;
        $docType = $this->getDocumentName($documentType);

        if ($moduleType == 'cashadvance') {
            $moduleClassID = 2;
            $redirectURL = "cadv-reim-liquidation/ors-burs?search=" . $pKey;
        } else if ($moduleType == 'procurement') {
            $moduleClassID = 3;
            $redirectURL = "procurement/ors-burs?search=" . $pKey;
        }

        try {
            $serialNo = $request->serial_no;
            $dateORS_BURS = !empty($request->date_ors_burs) ? $request->date_ors_burs: NULL;
            $fundCluster = $request->fund_cluster;
            $payee = $request->payee;
            $office = $request->office;
            $address = $request->address;
            $responsibilityCenter = $request->responsibility_center;
            $particulars = $request->particulars;
            $mfoPAP = $request->mfo_pap;
            $uacsObjectCode = $request->uacs_object_code;
            $amount = $request->amount;
            $sigCertified1 = !empty($request->sig_agency_head) ? $request->sig_agency_head: NULL;
            $sigCertified2 = !empty($request->sig_budget) ? $request->sig_budget: NULL;
            $dateCertified1 = !empty($request->date_certified_1) ? $request->date_certified_1: NULL;
            $dateCertified2 = !empty($request->date_certified_2) ? $request->date_certified_2: NULL;

            $ors = new OrsBurs;
            $ors->document_type = $documentType;
            $ors->transaction_type = $transactionType;
            $ors->serial_no = $serialNo;
            $ors->date_ors_burs = $dateORS_BURS;
            $ors->fund_cluster = $fundCluster;
            $ors->payee = $payee;
            $ors->office = $office;
            $ors->address = $address;
            $ors->responsibility_center = $responsibilityCenter;
            $ors->particulars = $particulars;
            $ors->mfo_pap = $mfoPAP;
            $ors->uacs_object_code = $uacsObjectCode;
            $ors->amount = $amount;
            $ors->sig_certified_1 = $sigCertified1;
            $ors->sig_certified_2 = $sigCertified2;
            $ors->date_certified_1 = $dateCertified1;
            $ors->date_certified_2 = $dateCertified2;
            $ors->module_class_id = $moduleClassID;
            $ors->code = $this->generateTrackerCode($documentType, $pKey, $moduleClassID);
            $ors->save();

            $logEmpMessage = "saved the " . strtolower($docType) . " $pKey.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "$docType $pKey successfully saved.";
            return redirect(url($redirectURL))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the $docType $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function update(Request $request, $key) {
        $documentType = $request->document_type;
        $moduleType = $request->module_type;
        $transactionType = !empty($request->transaction_type) ? $request->transaction_type: 'others';
        $docType = $this->getDocumentName($documentType);
        $_ors = OrsBurs::where('id', $key)->first();
        $ors = OrsBurs::where('id', $key)->first();

        try {
            $oldCode = $ors->code;
            $serialNo = $request->serial_no;
            $dateORS_BURS = !empty($request->date_ors_burs) ? $request->date_ors_burs: NULL;
            $fundCluster = $request->fund_cluster;
            $office = $request->office;
            $address = $request->address;
            $responsibilityCenter = $request->responsibility_center;
            $particulars = $request->particulars;
            $mfoPAP = $request->mfo_pap;
            $uacsObjectCode = $request->uacs_object_code;
            $amount = $request->amount;
            $sigCertified1 = !empty($request->sig_certified_1) ? $request->sig_certified_1: NULL;
            $sigCertified2 = !empty($request->sig_certified_2) ? $request->sig_certified_2: NULL;
            $dateCertified1 = !empty($request->date_certified_1) ? $request->date_certified_1: NULL;
            $dateCertified2 = !empty($request->date_certified_2) ? $request->date_certified_2: NULL;

            if (strpos($oldCode, strtoupper($documentType)) === false) {
                $newCode = str_replace($ors->document_type,
                                       strtoupper($documentType),
                                       $oldCode);
                $docHistory = DocumentLogHistory::where('code', $oldCode)->get();

                foreach ($docHistory as $dHist) {
                    $dHist->code = $newCode;
                    $dHist->save();
                }
            } else {
                $newCode = $oldCode;
            }

            if ($moduleType == 'cashadvance') {
                $ors->amount = $amount;
                $pKey = $_ors->id;
                $redirectURL = "cadv-reim-liquidation/ors-burs?search=" . $pKey;
            } else if ($moduleType == 'procurement') {
                $pKey = $_ors->po_no;
                $redirectURL = "procurement/ors-burs?search=" . $pKey;
            }

            $ors->document_type = $documentType;
            $ors->transaction_type = $transactionType;
            $ors->serial_no = $serialNo;
            $ors->date_ors_burs = $dateORS_BURS;
            $ors->fund_cluster = $fundCluster;
            $ors->office = $office;
            $ors->address = $address;
            $ors->responsibility_center = $responsibilityCenter;
            $ors->particulars = $particulars;
            $ors->mfo_pap = $mfoPAP;
            $ors->uacs_object_code = $uacsObjectCode;
            $ors->sig_certified_1 = $sigCertified1;
            $ors->sig_certified_2 = $sigCertified2;
            $ors->date_certified_1 = $dateCertified1;
            $ors->date_certified_2 = $dateCertified2;
            $ors->code = $newCode;
            $ors->save();

            $logEmpMessage = "updated the " . strtolower($docType) . " $pKey.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "$docType $pKey successfully updated.";
            return redirect(url($redirectURL))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the $docType $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showIssuedTo(Request $request, $id) {
        $issuedTo = User::orderBy('firstname')->get();
        $ors = DB::table('tblors_burs')->where('id', $id)->first();
        $issueBack = (int)$request['back'];

        return view('pages.view-ors-burs-issue', ['key' => $id,
                                                  'issuedTo' => $issuedTo,
                                                  'type' => $ors->document_type,
                                                  'issueBack' => $issueBack]);
    }

    public function showReceived(Request $request, $id) {
        $issuedTo = User::orderBy('firstname')->get();
        $ors = DB::table('tblors_burs')->where('id', $id)->first();
        $issueBack = (int)$request['back'];

        return view('pages.view-ors-burs-issue', ['key' => $id,
                                                  'issuedTo' => $issuedTo,
                                                  'type' => $ors->document_type,
                                                  'issueBack' => $issueBack]);
    }

    public function delete($id) {
        $ors = OrsBurs::where('id', $id)->first();
        $dv = DisbursementVoucher::where('ors_id', $id)->first();
        $dvID = !empty($dv) ? $dv->id: 0;
        $liq = LiquidationReport::where('dv_id', $dvID)->first();
        $liqID = !empty($liq) ? $liq->id: 0;
        $docType = $this->getDocumentName($ors->document_type);
        $pKey = $ors->id;

        try {
            OrsBurs::where('id', $id)->delete();
            $msg = "$docType $pKey successfully deleted.";
            $logEmpMessage = "deleted the " . strtolower($docType) . " $pKey.";

            if ($dv) {
                DisbursementVoucher::where('ors_id', $id)->delete();
                $msg = "$docType $pKey and Disbursement Voucher $dvID successfully deleted.";
                $logEmpMessage = "deleted the " . strtolower($docType) .
                                 " $pKey and disbursement voucher $dvID.";
            }

            if ($liq) {
                LiquidationReport::where('dv_id', $dv->id)->delete();
                $msg = "$docType $pKey, Disbursement Voucher $dvID, and
                        Liquidation Report $liqID successfully deleted.";
                $logEmpMessage = "deleted the " . strtolower($docType) .
                                 " $pKey, disbursement voucher $dvID, and
                                 liquidation report $liqID.";
            }

            $this->logEmployeeHistory($logEmpMessage);

            return redirect(url()->previous())->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered deleting the $docType $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function issue(Request $request, $id) {
        $ors = OrsBurs::where('id', $id)->first();
        $docType = $this->getDocumentName($ors->document_type);

        if ($ors->module_class_id == 3) {
            $pKey = $ors->po_no;
            $redirectURL = "procurement/ors-burs?search=" . $pKey;
        } else if ($ors->module_class_id == 2) {
            $pKey = $ors->id;
            $redirectURL = "cadv-reim-liquidation/ors-burs?search=" . $pKey;
        }

        try {
            $issueBack = $request['back'];
            $remarks = $request['remarks'];
            $issuedTo = $request['issued_to'];

            $code = $ors->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $docStatus = $this->checkDocStatus($code);

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
                    $msg = "$docType $docType is already issued.";
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
        $ors = OrsBurs::where('id', $id)->first();
        $docType = $this->getDocumentName($ors->document_type);

        if ($ors->module_class_id == 3) {
            $pKey = $ors->po_no;
            $redirectURL = "procurement/ors-burs?search=" . $pKey;
        } else if ($ors->module_class_id == 2) {
            $pKey = $ors->id;
            $redirectURL = "cadv-reim-liquidation/ors-burs?search=" . $pKey;
        }

        try {
            $receiveBack = $request['back'];
            $code = $ors->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $docStatus = $this->checkDocStatus($code);

            if (!$receiveBack) {
                if (!empty($docStatus->date_issued) && empty($docStatus->date_received)) {
                    if ($isDocGenerated) {
                        $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "received");

                        $logEmpMessage = "received the $docType $pKey.";
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
                $receiveBackResponse = $this->receiveBack($pKey, $code, $docType, $docStatus);

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

    public function createDV($id) {
        $ors = OrsBurs::where('id', $id)->first();
        $getLastID = DisbursementVoucher::orderBy('id', 'desc')->first();
        $code = $ors->code;
        $dvKey = $getLastID->id + 1;

        try {
            $docType = $this->getDocumentName($ors->document_type);
            $pKey = $ors->id;
            $isDocGenerated = $this->checkDocGenerated($code);
            $redirectURL = "cadv-reim-liquidation/ors-burs?search=" . $pKey;

            if ($isDocGenerated) {
                if ($ors->module_class_id == 2) {
                    $dv = new DisbursementVoucher;
                    $dv->ors_id = $ors->id;
                    $dv->module_class_id = $ors->module_class_id;
                    $dv->particulars = "To payment of...";
                    $dv->code = $this->generateTrackerCode('DV', $dvKey, 2);
                    $dv->save();

                    $logEmpMessage = "created the disbursement voucher $dvKey.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $redirectURL = "cadv-reim-liquidation/dv?search=" . $dvKey;
                    $msg = "Disbursement Voucher $dvKey successfully created.";
                    return redirect(url($redirectURL))->with('success', $msg);
                }
            } else {
                $msg = "Generate first the $docType $pKey document.";
                return redirect(url($redirectURL))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered creating the
                    Disbursement Voucher $dvKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function obligate($id) {
        $ors = OrsBurs::where('id', $id)->first();
        $po = PurchaseOrder::where('po_no', $ors->po_no)->first();
        $code = $ors->code;
        $docType = $this->getDocumentName($ors->document_type);
        $isDocGenerated = $this->checkDocGenerated($code);

        if ($ors->module_class_id == 3) {
            $pKey = $ors->po_no;
            $redirectURL = "procurement/ors-burs?search=" . $pKey;
        } else if ($ors->module_class_id == 2) {
            $pKey = $ors->id;
            $redirectURL = "cadv-reim-liquidation/ors-burs?search=" . $pKey;
        }

        try {
            if ($isDocGenerated) {
                if (empty($ors->date_obligated)) {
                    if ($ors->module_class_id == 2) {

                    } else {
                        if (isset($po)) {
                            $po->status = 7;
                            $po->save();
                        }
                    }

                    $ors->obligated_by = Auth::user()->emp_id;
                    $ors->date_obligated = date('Y-m-d H:i:s');
                    $ors->save();

                    $logEmpMessage = "obligated the " . strtolower($docType) . " $pKey.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "$docType $pKey is now set to obligated.";
                    return redirect(url($redirectURL))->with('success', $msg);
                } else {
                    $msg = "$docType $pKey is already obligated.";
                    return redirect(url($redirectURL))->with('warning', $msg);
                }
            } else {
                $msg = "Generate first the $docType $pKey document.";
                return redirect(url($redirectURL))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered obligating the $docType $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    private function getDocumentName($docName) {
        $docName = strtoupper($docName);

        if ($docName == 'ORS') {
            $documentName = "Obligation Request and Status";
        } else if ($docName == 'BURS') {
            $documentName = "Budget Utilization Request and Status";
        } else {
            $documentName = "Obligation/Budget Utilization Request and Status";
        }

        return $documentName;
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
