<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListDemandPayable;
use App\Models\ListDemandPayableItem;
use App\Models\ObligationRequestStatus;
use App\Models\DisbursementVoucher;

use App\Models\EmpAccount as User;
use App\Models\EmpGroup;
use App\Models\EmpDivision;
use App\Models\Signatory;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\MdsGsb;
use DB;
use Auth;
use Carbon\Carbon;

use App\Plugins\Notification as Notif;

class LDDAPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'pay_lddap';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedApproval = Auth::user()->getModuleAccess($module, 'approval');
        $isAllowedApprove = Auth::user()->getModuleAccess($module, 'approve');
        $isAllowedSummary = Auth::user()->getModuleAccess($module, 'summary');
        $isAllowedCADV = Auth::user()->getModuleAccess('ca_dv', 'is_allowed');
        $isAllowedProcDV = Auth::user()->getModuleAccess('proc_dv', 'is_allowed');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $lddapData = ListDemandPayable::whereNull('deleted_at');

        if (!empty($keyword)) {
            $lddapData = $lddapData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('department', 'like', "%$keyword%")
                    ->orWhere('entity_name', 'like', "%$keyword%")
                    ->orWhere('operating_unit', 'like', "%$keyword%")
                    ->orWhere('nca_no', 'like', "%$keyword%")
                    ->orWhere('lddap_ada_no', 'like', "%$keyword%")
                    ->orWhere('date_lddap', 'like', "%$keyword%")
                    ->orWhere('total_amount_words', 'like', "%$keyword%")
                    ->orWhere('total_amount', 'like', "%$keyword%")
                    ->orWhere('status', 'like', "%$keyword%");
            });
        }

        $lddapData = $lddapData->sortable(['created_at' => 'desc'])->paginate(15);

        foreach ($lddapData as $lddapDat) {
            $lddapItems = ListDemandPayableItem::where('lddap_id', $lddapDat->id)
                                               ->orderBy('item_no')
                                               ->get();
            $orsNos = [];

            foreach ($lddapItems as $item) {
                $orsIDs = unserialize($item->ors_no);

                foreach ($orsIDs as $orsID) {

                    $orsData = ObligationRequestStatus::find($orsID);
                    $orsNos[] = $orsData->serial_no;
                }
            }

            $lddapDat->ors_nos = $orsNos;
        }

        return view('modules.payment.lddap.index', [
            'list' => $lddapData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'isAllowedApproval' => $isAllowedApproval,
            'isAllowedApprove' => $isAllowedApprove,
            'isAllowedSummary'=> $isAllowedSummary,
            'isAllowedCADV' => $isAllowedCADV,
            'isAllowedProcDV'=> $isAllowedProcDV,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $dvList = DisbursementVoucher::whereNotNull('dv_no')
                                     ->orderBy('dv_no')
                                     ->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.payment.lddap.create', compact(
            'dvList', 'signatories'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //$dvID = $request->dv_id;
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
        $mdsGsbAccntNo = $request->mds_gsb_accnt_no[0];
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

        $isMdsGsbUUID = preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $mdsGsbAccntNo);
        $documentType = 'LDDAP';
        $routeName = 'lddap';

        try {
            if (!$isMdsGsbUUID) {
                $explodeMDSGSB = explode('/', $mdsGsbAccntNo);

                if (count($explodeMDSGSB) == 2) {
                    $instanceMDSGSB = new MdsGsb;
                    $instanceMDSGSB->branch = trim($explodeMDSGSB[0]);
                    $instanceMDSGSB->sub_account_no = trim($explodeMDSGSB[1]);
                    $instanceMDSGSB->save();

                    $mdsGSBData = MdsGsb::where([['branch', trim($explodeMDSGSB[0])], ['sub_account_no', trim($explodeMDSGSB[1])]])
                                        ->first();
                    $mdsGSBID = $mdsGSBData->id;
                } else {
                    $msg = "Please separate the MDS-GSB BRANCH and DS SUB ACCOUNT NO. with '/'.";
                    return redirect()->route($routeName)
                                     ->with('warning', $msg);
                }
            } else {
                $mdsGSBID = $mdsGsbAccntNo;
            }

            $instanceLDDAP = new ListDemandPayable;
            //$instanceLDDAP->dv_id = $dvID;
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
            $instanceLDDAP->date_lddap = $lddapDate;
            $instanceLDDAP->fund_cluster = $fundCluster;
            $instanceLDDAP->mds_gsb_accnt_no = $mdsGSBID;
            $instanceLDDAP->save();

            $lastLDDAP = ListDemandPayable::orderBy('created_at', 'desc')->first();
            $lastID = $lastLDDAP->id;

            if (is_array($listCurrentCreditorName)) {
                if (count($listCurrentCreditorName) > 0) {
                    $category = 'current_year';

                    foreach ($listCurrentCreditorName as $ctr => $creditorName) {
                        $itemNo = $ctr + 1;
                        $instanceLDDAPItem = new ListDemandPayableItem;
                        $instanceLDDAPItem->lddap_id = $lastID;
                        $instanceLDDAPItem->item_no = $itemNo;
                        $instanceLDDAPItem->category = $category;
                        $instanceLDDAPItem->creditor_name = $creditorName;
                        $instanceLDDAPItem->creditor_acc_no = $listCurrentCreditorAccNo[$ctr];
                        $instanceLDDAPItem->ors_no = serialize($listCurrentOrsNo[$ctr]);
                        $instanceLDDAPItem->allot_class_uacs = $listcurrentAllotClassUacs[$ctr];
                        $instanceLDDAPItem->gross_amount = $listCurrentGrossAmount[$ctr];
                        $instanceLDDAPItem->withold_tax = $listCurrentWitholdTax[$ctr];
                        $instanceLDDAPItem->net_amount = $listCurrentNetAmount[$ctr];
                        $instanceLDDAPItem->remarks = $listCurrentRemarks[$ctr];
                        $instanceLDDAPItem->save();
                    }
                }
            }

            if (is_array($listPriorCreditorName)) {
                if (count($listPriorCreditorName) > 0) {
                    $category = 'prior_year';

                    foreach ($listPriorCreditorName as $ctr =>$creditorName) {
                        $itemNo = $ctr + 1;
                        $instanceLDDAPItem = new ListDemandPayableItem;
                        $instanceLDDAPItem->lddap_id = $lastID;
                        $instanceLDDAPItem->item_no = $itemNo;
                        $instanceLDDAPItem->category = $category;
                        $instanceLDDAPItem->creditor_name = $creditorName;
                        $instanceLDDAPItem->creditor_acc_no = $listPriorCreditorAccNo[$ctr];
                        $instanceLDDAPItem->ors_no = serialize($listPriorOrsNo[$ctr]);
                        $instanceLDDAPItem->allot_class_uacs = $listPriorAllotClassUacs[$ctr];
                        $instanceLDDAPItem->gross_amount = $listPriorGrossAmount[$ctr];
                        $instanceLDDAPItem->withold_tax = $listPriorWitholdTax[$ctr];
                        $instanceLDDAPItem->net_amount = $listPriorNetAmount[$ctr];
                        $instanceLDDAPItem->remarks = $listPriorRemarks[$ctr];
                        $instanceLDDAPItem->save();
                    }
                }
            }

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $currentGross = 0;
        $currentWithholding = 0;
        $currentNet = 0;
        $priorGross = 0;
        $priorWithholding = 0;
        $priorNet = 0;
        $totalGross = 0;
        $totalWithholding = 0;
        $totalNet = 0;

        $lddap = ListDemandPayable::find($id);
        $mdsGSB = MdsGsb::find($lddap->mds_gsb_accnt_no);
        $sigCertCorrect = $lddap->sig_cert_correct;
        $sigApproval1 = $lddap->sig_approval_1;
        $sigApproval2 = $lddap->sig_approval_2;
        $sigApproval3 = $lddap->sig_approval_3;
        $sigAgencyAuth1 = $lddap->sig_agency_auth_1;
        $sigAgencyAuth2 = $lddap->sig_agency_auth_2;
        $sigAgencyAuth3 = $lddap->sig_agency_auth_3;
        $sigAgencyAuth4 = $lddap->sig_agency_auth_4;
        $currentItems = ListDemandPayableItem::where([
            ['lddap_id', $id], ['category', 'current_year']
        ])->orderBy('item_no')->get();

        foreach ($currentItems as $curritem) {
            $curritem->ors_no = unserialize($curritem->ors_no);
            $orsLists = [];

            foreach ($curritem->ors_no as $orsNo) {
                $orsData = ObligationRequestStatus::find($orsNo);
                $orsLists[] = (object) [
                    'id' => $orsData->id,
                    'serial_no' => $orsData->serial_no
                ];
            }

            $curritem->ors_data = $orsLists;
        }

        $priorItems = ListDemandPayableItem::where([
            ['lddap_id', $id], ['category', 'prior_year']
        ])->orderBy('item_no')->get();

        foreach ($priorItems as $priorItem) {
            $priorItem->ors_no = unserialize($priorItem->ors_no);
            $orsLists = [];

            foreach ($priorItem->ors_no as $orsNo) {
                $orsData = ObligationRequestStatus::find($orsNo);
                $orsLists[] = (object) [
                    'id' => $orsData->id,
                    'serial_no' => $orsData->serial_no
                ];
            }

            $priorItem->ors_data = $orsLists;
        }

        $dvList = DisbursementVoucher::whereNotNull('dv_no')
                                     ->orderBy('dv_no')
                                     ->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.payment.lddap.update', compact(
            'id', 'lddap', 'dvList', 'signatories', 'currentItems',
            'priorItems', 'currentGross', 'currentWithholding',
            'currentNet', 'totalGross', 'totalWithholding',
            'totalNet', 'priorGross', 'priorWithholding',
            'priorNet', 'sigCertCorrect', 'sigApproval1',
            'sigApproval2', 'sigApproval3', 'sigAgencyAuth1',
            'sigAgencyAuth2', 'sigAgencyAuth3', 'sigAgencyAuth4',
            'mdsGSB'
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
        $mdsGsbAccntNo = $request->mds_gsb_accnt_no[0];
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

        $isMdsGsbUUID = preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $mdsGsbAccntNo);
        $documentType = 'LDDAP';
        $routeName = 'lddap';

        try {
            if (!$isMdsGsbUUID) {
                $explodeMDSGSB = explode('/', $mdsGsbAccntNo);

                if (count($explodeMDSGSB) == 2) {
                    $instanceMDSGSB = new MdsGsb;
                    $instanceMDSGSB->branch = trim($explodeMDSGSB[0]);
                    $instanceMDSGSB->sub_account_no = trim($explodeMDSGSB[1]);
                    $instanceMDSGSB->save();

                    $mdsGSBData = MdsGsb::where([['branch', trim($explodeMDSGSB[0])], ['sub_account_no', trim($explodeMDSGSB[1])]])
                                        ->first();
                    $mdsGSBID = $mdsGSBData->id;
                } else {
                    $msg = "Please separate the MDS-GSB BRANCH and DS SUB ACCOUNT NO. with '/'.";
                    return redirect()->route($routeName)
                                     ->with('warning', $msg);
                }
            } else {
                $mdsGSBID = $mdsGsbAccntNo;
            }

            $instanceLDDAP = ListDemandPayable::find($id);
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
            $instanceLDDAP->date_lddap = $lddapDate;
            $instanceLDDAP->fund_cluster = $fundCluster;
            $instanceLDDAP->mds_gsb_accnt_no = $mdsGSBID;
            $instanceLDDAP->save();

            if ((is_array($listCurrentCreditorName) && count($listCurrentCreditorName) > 0) ||
                (is_array($listPriorCreditorName) && count($listPriorCreditorName) > 0)) {
                ListDemandPayableItem::where('lddap_id', $id)->delete();
            }

            if (is_array($listCurrentCreditorName)) {
                if (count($listCurrentCreditorName) > 0) {
                    $category = 'current_year';

                    foreach ($listCurrentCreditorName as $ctr => $creditorName) {
                        $itemNo = $ctr + 1;
                        $instanceLDDAPItem = new ListDemandPayableItem;
                        $instanceLDDAPItem->lddap_id = $id;
                        $instanceLDDAPItem->item_no = $itemNo;
                        $instanceLDDAPItem->category = $category;
                        $instanceLDDAPItem->creditor_name = $creditorName;
                        $instanceLDDAPItem->creditor_acc_no = $listCurrentCreditorAccNo[$ctr];
                        $instanceLDDAPItem->ors_no = serialize($listCurrentOrsNo[$ctr]);
                        $instanceLDDAPItem->allot_class_uacs = $listcurrentAllotClassUacs[$ctr];
                        $instanceLDDAPItem->gross_amount = $listCurrentGrossAmount[$ctr];
                        $instanceLDDAPItem->withold_tax = $listCurrentWitholdTax[$ctr];
                        $instanceLDDAPItem->net_amount = $listCurrentNetAmount[$ctr];
                        $instanceLDDAPItem->remarks = $listCurrentRemarks[$ctr];
                        $instanceLDDAPItem->save();
                    }
                }
            }

            if (is_array($listPriorCreditorName)) {
                if (count($listPriorCreditorName) > 0) {
                    $category = 'prior_year';

                    foreach ($listPriorCreditorName as $ctr =>$creditorName) {
                        $itemNo = $ctr + 1;
                        $instanceLDDAPItem = new ListDemandPayableItem;
                        $instanceLDDAPItem->lddap_id = $id;
                        $instanceLDDAPItem->item_no = $itemNo;
                        $instanceLDDAPItem->category = $category;
                        $instanceLDDAPItem->creditor_name = $creditorName;
                        $instanceLDDAPItem->creditor_acc_no = $listPriorCreditorAccNo[$ctr];
                        $instanceLDDAPItem->ors_no = serialize($listPriorOrsNo[$ctr]);
                        $instanceLDDAPItem->allot_class_uacs = $listPriorAllotClassUacs[$ctr];
                        $instanceLDDAPItem->gross_amount = $listPriorGrossAmount[$ctr];
                        $instanceLDDAPItem->withold_tax = $listPriorWitholdTax[$ctr];
                        $instanceLDDAPItem->net_amount = $listPriorNetAmount[$ctr];
                        $instanceLDDAPItem->remarks = $listPriorRemarks[$ctr];
                        $instanceLDDAPItem->save();
                    }
                }
            }

            $msg = "$documentType '$id' successfully updated.";
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
     * Soft deletes the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id) {
        $isDestroy = $request->destroy;

        if ($isDestroy) {
            $response = $this->destroy($request, $id);

            if ($response->alert_type == 'success') {
                return redirect()->route('lddap', ['keyword' => $response->id])
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route('lddap')
                                 ->with($response->alert_type, $response->msg);
            }
        } else {

                $instanceLDDAP = ListDemandPayable::find($id);
                $documentType = 'LDDAP';
                $instanceLDDAP->delete();

                $msg = "$documentType '$id' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect()->route('lddap', ['keyword' => $id])
                                 ->with('success', $msg);try {
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect()->route('lddap')
                                 ->with('failed', $msg);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($request, $id) {
        try {
            $instanceLDDAP = ListDemandPayable::find($id);
            $documentType = 'LDDAP';
            ListDemandPayableItem::where('lddap_id', $id)->delete();
            $instanceLDDAP->forceDelete();

            $msg = "$documentType '$id' permanently deleted.";
            Auth::user()->log($request, $msg);

            return (object) [
                'msg' => $msg,
                'alert_type' => 'success',
                'id' => $id
            ];
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);

            return (object) [
                'msg' => $msg,
                'alert_type' => 'failed'
            ];
        }
    }

    public function forApproval(Request $request, $id) {
        $documentType = 'List of Due and Demandable Accounts Payable';
        $routeName = 'lddap';

        try {
            $instanceDocLog = new DocLog;
            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);

            if ($isDocGenerated) {
                $instanceLDDAP = ListDemandPayable::find($id);
                $instanceLDDAP->status = 'for_approval';
                $instanceLDDAP->date_for_approval = Carbon::now();
                $instanceLDDAP->for_approval_by = Auth::user()->id;
                $instanceLDDAP->save();

                $msg = "$documentType '$id' successfully set to 'For Approval'.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName, ['keyword' => $id])
                                ->with('success', $msg);
            } else {
                $msg = "Document for $documentType '$id' should be generated first.";
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

    public function approve(Request $request, $id) {
        $documentType = 'List of Due and Demandable Accounts Payable';
        $routeName = 'lddap';

        try {
            $instanceNotif = new Notif;
            $instanceLDDAP = ListDemandPayable::find($id);
            $instanceLDDAP->status = 'approved';
            $instanceLDDAP->date_approved = Carbon::now();
            $instanceLDDAP->approved_by = Auth::user()->id;
            $instanceLDDAP->save();

            $instanceNotif->notifyApproveLDDAP($id, Auth::user()->id);

            $msg = "$documentType '$id' successfully set to 'Approved'.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function summary(Request $request, $id) {
        $documentType = 'List of Due and Demandable Accounts Payable';
        $routeName = 'lddap';

        try {
            $instanceNotif = new Notif;
            $instanceLDDAP = ListDemandPayable::find($id);
            $instanceLDDAP->status = 'for_summary';
            $instanceLDDAP->date_for_summary = Carbon::now();
            $instanceLDDAP->for_summary_by = Auth::user()->id;
            $instanceLDDAP->save();

            $instanceNotif->notifySummaryLDDAP($id);

            $this->setRelatedDVDisbursed($id);

            $msg = "$documentType '$id' successfully set to 'For Summary'.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function getListMDSGSB(Request $request) {
        $search = trim($request->search);
        $mdsGsbData = MdsGsb::select('id', 'branch', 'sub_account_no');

        if ($search) {
            $mdsGsbData = $mdsGsbData->where('sub_account_no', 'like', "%$search%")
                                     ->orWhere('branch', 'like', "%$search%");
        }

        $mdsGsbData = $mdsGsbData->orderBy('sub_account_no')->get();

        return response()->json($mdsGsbData);
    }

    public function getListORSBURS(Request $request) {
        $keyword = trim($request->search);
        $orsData = ObligationRequestStatus::select('id', 'serial_no')
                                          ->whereNotNull('serial_no')
                                          ->whereNotNull('date_obligated')
                                          ->where([['serial_no', '<>', '-'],
                                                   ['serial_no', '<>', '.']]);

        if ($keyword) {
            $orsData = $orsData->where(function($qry) use ($keyword) {
                $qry->where('serial_no', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('serial_no', 'like', "%$tag%");
                    }
                }
            });
        }

        $orsData = $orsData->whereHas('procdv', function($query) {
            $query->whereNotNull('date_for_payment');
        })->get();

        return response()->json($orsData);
    }

    private function setRelatedDVDisbursed($id) {
        $lddapItems = ListDemandPayableItem::where('lddap_id', $id)
                                           ->get();

        foreach ($lddapItems as $item) {
            $orsIDs = unserialize($item->ors_no);

            if (count($orsIDs) > 0) {
                foreach ($orsIDs as $orsID) {
                    $instanceDV = DisbursementVoucher::where('ors_id', $orsID)->first();
                    $instanceDV->disbursed_by = Auth::user()->id;
                    $instanceDV->date_disbursed = Carbon::now();
                    $instanceDV->save();
                }
            }
        }
    }
}
