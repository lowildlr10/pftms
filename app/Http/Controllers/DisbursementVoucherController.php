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
use App\Models\ListDemandPayable;

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
        $data = $this->getIndexData($request, 'procurement');

        // Get module access
        $module = 'proc_dv';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedPayment = Auth::user()->getModuleAccess($module, 'payment');
        $isAllowedIAR = Auth::user()->getModuleAccess('proc_iar', 'is_allowed');

        return view('modules.procurement.dv.index', [
            'list' => $data->dv_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedPayment' => $isAllowedPayment,
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
                    ->orWhere('pr_id', 'like', "%$keyword%")
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
            $dvDat->doc_status = $instanceDocLog->checkDocStatus($dvDat->procdv['id']);
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
        $fundCluster = $request->fund_cluster;
        $dateDV = !empty($request->date_dv) ? $request->date_dv : NULL;
        $dvNo = $request->dv_no;
        $modePayments = !empty($request->mode_payment) ? $request->mode_payment : [];
        $otherPayment = !empty($request->other_payment) ? $request->other_payment : NULL;
        $particulars = $request->particulars;
        $responsibilityCenter = $request->responsibility_center;
        $mfoPAP = $request->mfo_pap;
        $amount = $request->amount;
        $sigAccounting = $request->sig_accounting;
        $dateAccounting = !empty($request->date_accounting) ? $request->date_accounting : NULL;
        $sigAgencyHead = $request->sig_agency_head;
        $dateAgencyHead = !empty($request->date_agency_head) ? $request->date_agency_head : NULL;

        if (in_array('mds', $modePayments)) {
            $modePayment = '1';
        } else {
            $modePayment = '0';
        }

        if (in_array('commercial', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        if (in_array('ada', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        if (in_array('others', $modePayments)) {
            $modePayment .= '-1';
        } else {
            $modePayment .= '-0';
        }

        try {
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDV->fund_cluster = $fundCluster;
            $instanceDV->date_dv = $dateDV;
            $instanceDV->dv_no = $dvNo;
            $instanceDV->payment_mode = $modePayment;
            $instanceDV->other_payment = $otherPayment;
            $instanceDV->particulars = $particulars;
            $instanceDV->responsibility_center = $responsibilityCenter;
            $instanceDV->mfo_pap = $mfoPAP;
            $instanceDV->amount = $amount;
            $instanceDV->sig_accounting = $sigAccounting;
            $instanceDV->date_accounting = $dateAccounting;
            $instanceDV->sig_agency_head = $sigAgencyHead;
            $instanceDV->date_agency_head = $dateAgencyHead;
            $instanceDV->save();

            $documentType = 'Disbursement Voucher';

            $msg = "$documentType '$id' successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    public function showIssue($id) {
        return view('modules.procurement.dv.issue', [
            'id' => $id
        ]);
    }

    public function issue(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);
            $docStatus = $instanceDocLog->checkDocStatus($id);

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued", $remarks);

                    //$instanceDV->notifyIssued($id, Auth::user()->id);

                    $msg = "$documentType '$id' successfully issued to accounting unit.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route($routeName, ['keyword' => $id])
                                     ->with('success', $msg);
                } else {
                    $msg = "Document for $documentType '$id' should be generated first.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route($routeName, ['keyword' => $id])
                                     ->with('warning', $msg);
                }
            } else {
                $msg = "$documentType '$id' already issued.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName, ['keyword' => $id])
                                 ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showReceive($id) {
        return view('modules.procurement.dv.receive', [
            'id' => $id
        ]);
    }

    public function receive(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received", $remarks);
            //$instanceDV->notifyReceived($id, Auth::user()->id);

            $msg = "$documentType '$id' successfully received.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showIssueBack($id) {
        return view('modules.procurement.dv.issue-back', [
            'id' => $id
        ]);
    }

    public function issueBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued_back", $remarks);
            //$instanceDV->notifyIssuedBack($id, Auth::user()->id);

            $msg = "$documentType '$id' successfully issued back.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showReceiveBack($id) {
        return view('modules.procurement.dv.receive-back', [
            'id' => $id
        ]);
    }

    public function receiveBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDocLog->logDocument($id, NULL, NULL, "-", NULL);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received_back", $remarks);

            $msg = "$documentType '$id' successfully received back.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showPayment($id) {
        $instanceDV = DisbursementVoucher::find($id);
        $dvNo = $instanceDV->dv_no;
        return view('modules.procurement.dv.payment', [
            'id' => $id,
            'dvNo' => $dvNo
        ]);
    }

    public function payment(Request $request, $id) {
        $dvNo = $request->dv_no;

        try {
            $instanceDocLog = new DocLog;
            $instanceDV = DisbursementVoucher::with('procors')->find($id);
            $moduleClass = $instanceDV->module_class;
            $documentType = 'Disbursement Voucher';

            if ($moduleClass == 3) {
                $routeName = 'proc-dv';
                $instancePO = PurchaseJobOrder::where('po_no', $instanceDV->procors->po_no)
                                              ->first();
                $instancePO->status = 10;
                $instancePO->save();
            } else if ($moduleClass == 2) {
                $routeName = 'ca-dv';
            }

            $instanceDV->date_disbursed = Carbon::now();
            $instanceDV->disbursed_by = Auth::user()->id;
            $instanceDV->for_payment = 'y';
            $instanceDV->dv_no = $dvNo;
            $instanceDV->save();

            //$instanceDV->notifyPayment($id, Auth::user()->id);

            $msg = "$documentType with a DV number of '$dvNo'
                    is successfully set to 'For Payment'.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showLogRemarks($id) {
        $instanceDocLog = DocLog::where('doc_id', $id)
                                ->whereNotNull('remarks')
                                ->orderBy('logged_at', 'desc')
                                ->get();
        return view('modules.procurement.dv.remarks', [
            'id' => $id,
            'docRemarks' => $instanceDocLog
        ]);
    }

    public function logRemarks(Request $request, $id) {
        $message = $request->message;

        if (!empty($message)) {
            $instanceDV = DisbursementVoucher::find($id);
            $instanceDocLog = new DocLog;
            //$instanceDV->notifyMessage($id, Auth::user()->id, $message);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "message", $message);
            return 'Sent!';
        }
    }

    /*
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

    */
}
