<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\AbstractQuotation;
use App\Models\AbstractQuotationItem;
use App\Models\PurchaseJobOrder;
use App\Models\PurchaseJobOrderItem;
use App\Models\ObligationRequestStatus;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\InventoryStock;

use App\User;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\Signatory;
use App\Models\ItemUnitIssue;
use Carbon\Carbon;
use Auth;
use DB;

class DisbursementVoucherController extends Controller
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
    public function indexProc(Request $request) {
<<<<<<< HEAD
        $pageLimit = 25;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $dvList = DB::table('tbldv as dv')
                    ->select('dv.*', 'proj.project', 'status.id AS sID', 'ors.po_no', 'pr.pr_no',
                             DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
                             'po.status as status_id', 'ors.id as ors_id', 'bid.company_name')
                    ->join('tblors_burs as ors', 'ors.id', '=', 'dv.ors_id')
                    ->join('tblpo_jo as po', 'po.po_no', '=', 'ors.po_no')
                    ->join('tblpr as pr', 'pr.id', '=', 'dv.pr_id')
                    ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'pr.requested_by')
                    ->join('tblsuppliers as bid', 'bid.id', '=', 'ors.payee')
                    ->leftJoin('tblprojects AS proj', 'proj.id', '=', 'pr.project_id')
                    ->join('tblpr_status AS status', 'status.id', '=', 'po.status')
                    ->where('dv.module_class_id', 3)
                    ->where('po.status', '<>', 3)
                    ->whereNull('dv.deleted_at');

        if (!empty($search)) {
            $dvList = $dvList->where(function ($query) use ($search) {
                                   $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.date_pr', 'LIKE', '%' . $search . '%')
                                         ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.po_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('dv.code', 'LIKE', '%' . $search . '%');
                               });
        }

        if (Auth::user()->role == 4 || Auth::user()->role == 6) {
            $dvList = $dvList->where('requested_by', Auth::user()->emp_id);
        }

        if (Auth::user()->role == 5) {
            $dvList = $dvList->where('emp.division_id', Auth::user()->division_id);
        }

        $dvList = $dvList->orderBy('pr.id', 'desc')
                         ->paginate($pageLimit);

        foreach ($dvList as $list) {
            $list->document_status = $this->checkDocStatus($list->code);
            $list->display_menu = true;
        }

        return view('pages.dv', ['search' => $search,
                                 'list' => $dvList,
                                 'pageLimit' => $pageLimit,
                                 'paperSizes' => $paperSizes,
                                 'type' => 'procurement']);
    }

    public function indexCA(Request $request) {
        $isOrdinaryUser = true;
        $pageLimit = 50;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $dvList = DB::table('tbldv as dv')
                    ->select('dv.*', DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
                             'ors.id as ors_id', 'ors.payee', 'ors.transaction_type')
                    ->join('tblors_burs as ors', 'ors.id', '=', 'dv.ors_id')
                    ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'ors.payee')
                    ->where('dv.module_class_id', 2)
                    ->whereNull('dv.deleted_at');

        if (!empty($search)) {
            if ($request->isMethod('post')) {
                $dvList = $dvList->where(function ($query)  use ($search) {
                                       $query->where('dv.date_dv', 'LIKE', '%' . $search . '%')
                                             ->orWhere('dv.particulars', 'LIKE', '%' . $search . '%')
                                             ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                             ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                             ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                             ->orWhere('dv.dv_no', 'LIKE', '%' . $search . '%')
                                             ->orWhere('dv.id', 'LIKE', '%' . $search . '%')
                                             ->orWhere('dv.ors_id', 'LIKE', '%' . $search . '%');
                                   });
            } else {
                $dvList = $dvList->where(function ($query)  use ($search) {
                                       $query->where('dv.id', 'LIKE', '%' . $search . '%');
                                   });
            }
        }
=======
        $data = $this->getIndexData($request, 'procurement');

        // Get module access
        $module = 'proc_dv';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedDisburse = Auth::user()->getModuleAccess($module, 'disburse');
        $isAllowedIAR = Auth::user()->getModuleAccess('proc_iar', 'is_allowed');

        return view('modules.procurement.dv.index', [
            'list' => $data->dv_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDisburse' => $isAllowedDisburse,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedIAR' => $isAllowedIAR,
        ]);
    }

    public function indexCA(Request $request) {
        $data = $this->getIndexData($request, 'procurement');

        // Get module access
        $module = 'proc_dv';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedDisburse = Auth::user()->getModuleAccess($module, 'disburse');
        $isAllowedIAR = Auth::user()->getModuleAccess('proc_iar', 'is_allowed');

        return view('modules.procurement.dv.index', [
            'list' => $data->dv_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDisburse' => $isAllowedDisburse,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedIAR' => $isAllowedIAR,
        ]);
    }
>>>>>>> procurement

    private function getIndexData($request, $type) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $dvData = ObligationRequestStatus::with(['bidpayee', 'procdv'])->whereHas('pr', function($query)
                                             use($empDivisionAccess) {
            $query->whereIn('division', $empDivisionAccess);
        })->whereHas('procdv', function($query) {
            $query->whereNull('deleted_at');
        });

        if (!empty($keyword)) {
            $dvData = $dvData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('po_no', 'like', "%$keyword%")
                    ->orWhere('amount', 'like', "%$keyword%")
                    ->orWhere('document_type', 'like', "%$keyword%")
                    ->orWhereHas('bidpayee', function($query) use ($keyword) {
                        $query->where('company_name', 'like', "%$keyword%")
                              ->orWhere('address', 'like', "%$keyword%");
                    })->orWhereHas('procdv', function($query) use ($keyword) {
                        $query->where('id', 'like', "%$keyword%")
                              ->orWhere('particulars', 'like', "%$keyword%")
                              //->orWhere('transaction_type', 'like', "%$keyword%")
                              ->orWhere('dv_no', 'like', "%$keyword%")
                              ->orWhere('date_dv', 'like', "%$keyword%")
                              ->orWhere('date_disbursed', 'like', "%$keyword%")
                              ->orWhere('responsibility_center', 'like', "%$keyword%")
                              ->orWhere('mfo_pap', 'like', "%$keyword%")
                              ->orWhere('amount', 'like', "%$keyword%")
                              ->orWhere('address', 'like', "%$keyword%")
                              ->orWhere('fund_cluster', 'like', "%$keyword%");
                    });
            });
        }

        $dvData = $dvData->sortable(['po_no' => 'desc'])->paginate(15);

        foreach ($dvData as $dvDat) {
            $dvDat->doc_status = $instanceDocLog->checkDocStatus($dvDat->dv['id']);
        }

        return (object) [
            'keyword' => $keyword,
            'dv_data' => $dvData,
            'paper_sizes' => $paperSizes
        ];
    }

    private function getIndexData($request, $type) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $orsData = DisbursementVoucher::with('bidpayee')->whereHas('pr', function($query)
                   use($empDivisionAccess) {
            $query->whereIn('division', $empDivisionAccess);
        })->whereHas('procdv', function($query) {
            $query->whereNull('deleted_at');
        });

        if (!empty($keyword)) {
            $orsData = $orsData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('po_no', 'like', "%$keyword%")
                    ->orWhere('date_po', 'like', "%$keyword%")
                    ->orWhere('grand_total', 'like', "%$keyword%")
                    ->orWhere('document_type', 'like', "%$keyword%")
                    ->orWhereHas('stat', function($query) use ($keyword) {
                        $query->where('status_name', 'like', "%$keyword%");
                    })->orWhereHas('bidpayee', function($query) use ($keyword) {
                        $query->where('company_name', 'like', "%$keyword%")
                              ->orWhere('address', 'like', "%$keyword%");
                    })->orWhereHas('poitems', function($query) use ($keyword) {
                        $query->where('item_description', 'like', "%$keyword%");
                    })->orWhereHas('ors', function($query) use ($keyword) {
                        $query->where('id', 'like', "%$keyword%")
                              ->orWhere('particulars', 'like', "%$keyword%")
                              ->orWhere('document_type', 'like', "%$keyword%")
                              ->orWhere('transaction_type', 'like', "%$keyword%")
                              ->orWhere('serial_no', 'like', "%$keyword%")
                              ->orWhere('date_ors_burs', 'like', "%$keyword%")
                              ->orWhere('date_obligated', 'like', "%$keyword%")
                              ->orWhere('responsibility_center', 'like', "%$keyword%")
                              ->orWhere('uacs_object_code', 'like', "%$keyword%")
                              ->orWhere('amount', 'like', "%$keyword%")
                              ->orWhere('office', 'like', "%$keyword%")
                              ->orWhere('address', 'like', "%$keyword%")
                              ->orWhere('fund_cluster', 'like', "%$keyword%");
                    });
            });
        }

        $orsData = $orsData->sortable(['po_no' => 'desc'])->paginate(15);

        foreach ($orsData as $orsDat) {
            $orsDat->doc_status = $instanceDocLog->checkDocStatus($orsDat->ors['id']);
        }

        return (object) [
            'keyword' => $keyword,
            'ors_data' => $orsData,
            'paper_sizes' => $paperSizes
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $dvData = DisbursementVoucher::with(['bidpayee', 'procors'])->find($id);
        $moduleClass = $dvData->module_class;
        $dvDate = $dvData->date_dv;
        $dvNo = $dvData->dv_no;
        $fundCluster = $dvData->fund_cluster;
        $paymentModes = explode('-', $dvData->payment_mode);
        $paymentMode1 = $paymentModes[0];
        $paymentMode2 = $paymentModes[1];
        $paymentMode3 = $paymentModes[2];
        $paymentMode4 = $paymentModes[3];
        $otherPayment = $dvData->other_payment;
        $responsibilityCenter = $dvData->responsibility_center;
        $particulars = $dvData->particulars;
        $mfoPAP = $dvData->mfo_pap;
        $amount = $dvData->amount;
        $sigCertified = $dvData->sig_certified;
        $sigAccounting = $dvData->sig_accounting;
        $sigAgencyHead = $dvData->sig_agency_head;
        $dateAccounting = $dvData->date_accounting;
        $dateAgencyHead = $dvData->date_agency_head;
        $checkAdaNo = $dvData->check_ada_no;
        $dateCheckAda = $dvData->date_check_ada;
        $bankName = $dvData->bank_name;
        $bankAccountNo = $dvData->bank_account_no;
        $jevNo = $dvData->jev_no;
        $receiptPrintedName = $dvData->receipt_printed_name;
        $dateJev = $dvData->date_jev;
        $signature = $dvData->signature;
        $orNo = $dvData->or_no;
        $otherDocuments = $dvData->other_documents;
        $tinNo = $dvData->bidpayee['tin_no'];
        $serialNo = $dvData->procors['serial_no'];
        $address = !empty($dvData->address) ? $dvData->address : $dvData->procors['address'];
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        if ($moduleClass == 3) {
            $payees = Supplier::orderBy('company_name')->get();
            $payee = $dvData->bidpayee['id'];
        } else if ($moduleClass == 2) {

        }

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.procurement.dv.update', compact(
            'id', 'dvData', 'dvNo', 'fundCluster', 'tinNo',
            'paymentMode1', 'paymentMode2', 'paymentMode3',
            'paymentMode4', 'otherPayment', 'responsibilityCenter',
            'mfoPAP', 'amount', 'sigCertified', 'sigAccounting',
            'sigAgencyHead', 'dateAccounting', 'dateAgencyHead',
            'checkAdaNo', 'dateCheckAda', 'bankName', 'bankAccountNo',
            'jevNo', 'receiptPrintedName', 'dateJev', 'signature',
            'orNo', 'otherDocuments', 'signatories', 'signatories',
            'serialNo', 'address', 'dvDate', 'payees', 'particulars',
            'payee'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        /*
        $moduleType = $request->module_type;
        $getLastID = DisbursementVoucher::orderBy('id', 'desc')->first();
        $pKey = $getLastID->id + 1;
        $dv = DisbursementVoucher::where('id', $id)->first();

        if ($moduleType == 'cashadvance') {
            $pKey = $id;
            $redirectURL = "cadv-reim-liquidation/dv?search=" . $pKey;
        } else if ($moduleType == 'procurement') {
            $pr = PurchaseRequest::where('id', $dv->pr_id)->first();
            $pKey = $pr->pr_no;
            $redirectURL = "procurement/dv?search=" . $pKey;
        }

        try {
            $fundCluster = $request->fund_cluster;
            $dvNo = $request->dv_no;
            $dateDV = $request->date_dv;
            $paymentMode1 = $request->mds_check;
            $paymentMode2 = $request->commercial_check;
            $paymentMode3 = $request->ada;
            $paymentMode4 = $request->others_check;
            $otherPayment = $request->other_payment;
            $particulars = $request->particulars;
            $sigAccounting = $request->sig_accounting;
            $sigAgencyHead = $request->sig_agency_head;
            $dateAccounting = $request->date_accounting;
            $dateAgencyHead = $request->date_agency_head;
            $paymentMode = "";

            if (empty($dateDV)) {
                $dateDV = NULL;
            }

            if (empty($otherPayment)) {
                $otherPayment = NULL;
            }

            if (empty($paymentMode1)) {
                $paymentMode .= "0";
            } else {
                $paymentMode .= "1";
            }

            if (empty($paymentMode2)) {
                $paymentMode .= "-0";
            } else {
                $paymentMode .= "-1";
            }

            if (empty($paymentMode3)) {
                $paymentMode .= "-0";
            } else {
                $paymentMode .= "-1";
            }

            if (empty($paymentMode4)) {
                $paymentMode .= "-0";
            } else {
                $paymentMode .= "-1";
            }

            $dv->fund_cluster = $fundCluster;
            $dv->dv_no = $dvNo;
            $dv->date_dv = $dateDV;
            $dv->payment_mode = $paymentMode;
            $dv->other_payment = $otherPayment;
            $dv->particulars = $particulars;
            $dv->sig_accounting = $sigAccounting;
            $dv->sig_agency_head = $sigAgencyHead;
            $dv->date_accounting = $dateAccounting;
            $dv->date_agency_head = $dateAgencyHead;
            $dv->save();

            $logEmpMessage = "updated the disbursement voucher $id.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Disbursement Voucher $pKey successfully updated.";
            return redirect(url($redirectURL))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the
                    Disbursement Voucher $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }*/
    }

    public function showIssuedTo(Request $request, $id) {
        $issuedTo = User::orderBy('firstname')->get();
        $issueBack = (int)$request['back'];

        return view('pages.view-dv-issue', ['key' => $id,
                                            'issuedTo' => $issuedTo,
                                            'issueBack' => $issueBack]);
    }

    public function issue(Request $request, $id) {
        $dv = DisbursementVoucher::where('id', $id)->first();

        if ($dv->module_class_id == 3) {
            $pr = PurchaseRequest::where('id', $dv->pr_id)->first();
            $pKey = $pr->pr_no;
            $redirectURL = "procurement/dv?search=" . $pKey;
        } else if ($dv->module_class_id == 2) {
            $pKey = $id;
            $redirectURL = "cadv-reim-liquidation/dv?search=" . $pKey;
        }

        try {
            $issueBack = $request['back'];
            $remarks = $request['remarks'];
            $issuedTo = $request['issued_to'];
            $code = $dv->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $docStatus = $this->checkDocStatus($code);

            if (!$issueBack) {
                if (empty($docStatus->date_issued)) {
                    if ($isDocGenerated) {
                        $this->logTrackerHistory($code, Auth::user()->emp_id, $issuedTo, "issued", $remarks);

                        $logEmpMessage = "issued the disbursement voucher $pKey.";
                        $this->logEmployeeHistory($logEmpMessage);

                        $msg = "Disbursement Voucher $pKey is now set to issued.";
                        return redirect(url($redirectURL))->with('success', $msg);
                    } else {
                        $msg = "Generate first the Disbursement Voucher $pKey document.";
                        return redirect(url($redirectURL))->with('warning', $msg);
                    }
                } else {
                    $msg = "Disbursement Voucher $pKey is already issued.";
                    return redirect(url($redirectURL))->with('warning', $msg);
                }
            } else {
                $issueBackResponse = $this->issueBack($pKey, $code, "Disbursement Voucher", $docStatus,
                                                      $issuedTo, $remarks);

                if ($issueBackResponse->status == 'success') {
                    return redirect(url($redirectURL))->with('success', $issueBackResponse->msg);
                } else {
                    return redirect(url()->previous())->with('failed', $issueBackResponse->msg);
                }
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered issuing the Disbursement Voucher $pKey.";
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
        $dv = DisbursementVoucher::where('id', $id)->first();

        if ($dv->module_class_id == 3) {
            $pr = PurchaseRequest::where('id', $dv->pr_id)->first();
            $pKey = $pr->pr_no;
            $redirectURL = "procurement/dv?search=" . $pKey;
        } else if ($dv->module_class_id == 2) {
            $pKey = $id;
            $redirectURL = "cadv-reim-liquidation/dv?search=" . $pKey;
        }

        try {
            $receiveBack = $request['back'];
            $code = $dv->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $docStatus = $this->checkDocStatus($code);

            if (!$receiveBack) {
                if (!empty($docStatus->date_issued) && empty($docStatus->date_received)) {
                    if ($isDocGenerated) {
                        $this->logTrackerHistory($code, Auth::user()->emp_id, 0, "received");

                        $logEmpMessage = "received the disbursement voucher $pKey.";
                        $this->logEmployeeHistory($logEmpMessage);

                        $msg = "Disbursement Voucher $pKey is now set to received.";
                        return redirect(url($redirectURL))->with('success', $msg);
                    } else {
                        $msg = "Generate first the Disbursement Voucher $pKey document.";
                        return redirect(url($redirectURL))->with('warning', $msg);
                    }
                } else {
                    $msg = "Disbursement Voucher $pKey is already received.";
                    return redirect(url($redirectURL))->with('warning', $msg);
                }
            } else {
                $receiveBackResponse = $this->receiveBack($pKey, $code, "Disbursement Voucher",
                                                          $docStatus);

                if ($receiveBackResponse->status == 'success') {
                    return redirect(url($redirectURL))->with('success', $receiveBackResponse->msg);
                } else {
                    return redirect(url()->previous())->with('failed', $receiveBackResponse->msg);
                }
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered receiving the Disbursement Voucher $pKey.";
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

    public function createLiquidation($id) {
        $dv = DisbursementVoucher::where('id', $id)->first();
        $ors = OrsBurs::where('id', $dv->ors_id)->first();
        $getLastID = LiquidationReport::orderBy('id', 'desc')->first();
        $code = $dv->code;
        $liquidationKey = !empty($getLastID->id) ? $getLastID->id + 1: 1;

        try {
            $docType = 'Disbursement Voucher';
            $pKey = $dv->id;
            $isDocGenerated = $this->checkDocGenerated($code);
            $redirectURL = "cadv-reim-liquidation/dv?search=" . $pKey;

            if ($isDocGenerated) {
                if ($dv->module_class_id == 2) {
                    if (!empty($dv->dv_no)) {
                        $liq = new LiquidationReport;
                        $liq->dv_id = $id;
                        $liq->dv_no = $dv->dv_no;
                        $liq->sig_claimant = $ors->payee;
                        $liq->code = $this->generateTrackerCode('LR', $liquidationKey, 2);
                        $liq->save();

                        $logEmpMessage = "created the disbursement voucher $liquidationKey.";
                        $this->logEmployeeHistory($logEmpMessage);

                        $redirectURL = "cadv-reim-liquidation/liquidation?search=" . $dv->dv_no;
                        $msg = "Disbursement Voucher $liquidationKey successfully created.";
                        return redirect(url($redirectURL))->with('success', $msg);
                    } else {
                        $msg = "Fill-up first the DV Number of Disbursement Voucher $id.";
                        return redirect(url($redirectURL))->with('warning', $msg);
                    }
                } else {
                    $msg = "There is a problem creating the Liquidation Report for
                            Disbursement Voucher $id.";
                    return redirect(url($redirectURL))->with('warning', $msg);
                }
            } else {
                $msg = "Generate first the $docType $pKey document.";
                return redirect(url($redirectURL))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered creating the
                    Disbursement Voucher $liquidationKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function payment($id)
    {
        $dv = DisbursementVoucher::find($id);
        $ors = DB::table('tblors_burs as ors')
                 ->join('tbldv as dv', 'dv.ors_id', '=', 'ors.id')
                 ->first();
        $docType = "Disbursement Voucher";

        if ($dv->module_class_id == 3) {
            $pKey = $ors->po_no;
            $redirectURL = "procurement/dv?search=" . $pKey;
        } else if ($dv->module_class_id == 2) {
            $pKey = $dv->id;
            $redirectURL = "cadv-reim-liquidation/dv?search=" . $pKey;
        }

        try {
            $code = $dv->code;
            $isDocGenerated = $this->checkDocGenerated($code);
            $po = PurchaseOrder::where('po_no', $ors->po_no)->first();

            if ($isDocGenerated) {
                if (empty($dv->date_disbursed)) {
                    if ($dv->module_class_id == 2) {

                    } else {
                        if (isset($po)) {
                            $po->status = 11;
                            $po->save();
                        }
                    }

                    $dv->disbursed_by = Auth::user()->emp_id;
                    $dv->date_disbursed = date('Y-m-d H:i:s');
                    $dv->for_payment = 'y';
                    $dv->save();

                    $logEmpMessage = "disbursed the " . strtolower($docType) . " $pKey.";
                    $this->logEmployeeHistory($logEmpMessage);

                    $msg = "$docType $pKey is now set to payment.";
                    return redirect(url($redirectURL))->with('success', $msg);
                } else {
                    $msg = "$docType $pKey is already disbursed.";
                    return redirect(url($redirectURL))->with('warning', $msg);
                }
            } else {
                $msg = "Generate first the $docType $pKey document.";
                return redirect(url($redirectURL))->with('warning', $msg);
            }
        } catch (Exception $e) {
            $msg = "There is an error encountered disbursing the $docType $pKey.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showLogRemarks($id) {
        $instanceDocLog = DocLog::where('doc_id', $id)
                                ->whereNotNull('remarks')
                                ->orderBy('logged_at', 'desc')
                                ->get();
        return view('modules.procurement.ors-burs.remarks', [
            'id' => $id,
            'docRemarks' => $instanceDocLog
        ]);
    }

    public function logRemarks(Request $request, $id) {
        $message = $request->message;

        if (!empty($message)) {
            $instanceORS = ObligationRequestStatus::find($id);
            $instanceDocLog = new DocLog;
            $instanceORS->notifyMessage($id, Auth::user()->id, $message);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "message", $message);
            return 'Sent!';
        }
    }
}
