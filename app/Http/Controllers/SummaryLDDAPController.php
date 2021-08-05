<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SummaryLDDAP as Summary;
use App\Models\SummaryLDDAPItem as SummaryItem;
use App\Models\ListDemandPayable;
use App\Models\ListDemandPayableItem;

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

class SummaryLDDAPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);

        // Get module access
        $module = 'pay_summary';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedApproval = Auth::user()->getModuleAccess($module, 'approval');
        $isAllowedApprove = Auth::user()->getModuleAccess($module, 'approve');
        $isAllowedSubmission = Auth::user()->getModuleAccess($module, 'submission');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $summaryData = Summary::whereNull('deleted_at');

        if (!empty($keyword)) {
            $summaryData = $summaryData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('department', 'like', "%$keyword%")
                    ->orWhere('entity_name', 'like', "%$keyword%")
                    ->orWhere('operating_unit', 'like', "%$keyword%")
                    ->orWhere('fund_cluster', 'like', "%$keyword%")
                    ->orWhere('sliiae_no', 'like', "%$keyword%")
                    ->orWhere('date_sliiae', 'like', "%$keyword%")
                    ->orWhere('to', 'like', "%$keyword%")
                    ->orWhere('bank_name', 'like', "%$keyword%")
                    ->orWhere('bank_address', 'like', "%$keyword%")
                    ->orWhere('lddap_no_pcs', 'like', "%$keyword%")
                    ->orWhere('total_amount_words', 'like', "%$keyword%")
                    ->orWhere('total_amount', 'like', "%$keyword%")
                    ->orWhere('status', 'like', "%$keyword%");
            });
        }

        $summaryData = $summaryData->sortable(['created_at' => 'desc'])->paginate(15);

        return view('modules.payment.summary.index', [
            'list' => $summaryData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'isAllowedApproval' => $isAllowedApproval,
            'isAllowedApprove' => $isAllowedApprove,
            'isAllowedSubmission'=> $isAllowedSubmission,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $mdsGSBs = MdsGsb::all();
        $summaryCtr = Summary::where('created_at', 'like', '%'.date('Y').'%')
                             ->count() + 1;
        $summaryCtr = str_pad($summaryCtr, 4, '0', STR_PAD_LEFT);
        $month = date('m');
        $year = date('Y');
        $sliiaeNo = "$year-$month-$summaryCtr";
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.payment.summary.create', compact(
            'mdsGSBs', 'signatories', 'sliiaeNo'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $mdsGsbID = $request->mds_gsb_id;
        $department = $request->department;
        $entityName = $request->entity_name;
        $operatingUnit = $request->operating_unit;
        $fundCluster = $request->fund_cluster;
        $sliiaeNo = $request->sliiae_no;
        $sliiaeDate = $request->sliiae_date;
        $to = $request->to;
        $bankName = $request->bank_name;
        $bankAddress = $request->bank_address;
        $totalAmount = $request->total_amount;
        $totalAmountWords = $request->total_amount_words;
        $sigCertCorrect = $request->cert_correct;
        $sigApprovedBy = $request->approved_by;
        $sigDeliveredBy = $request->delivered_by;

        $lddapIDs = $request->lddap_id;
        $dateIssues = $request->date_issue;
        $totals = $request->total;
        $allotmentPSs = $request->allotment_ps;
        $allotmentMOOEs = $request->allotment_mooe;
        $allotmentCOs = $request->allotment_co;
        $allotmentFEs = $request->allotment_fe;
        $allotmentRemarks = $request->allotment_remarks;

        $countLDDAP = count($lddapIDs);
        $documentType = 'Summary of LDDAP-ADAs Issued and Invalidated ADA Entries';
        $routeName = 'summary';

        try {
            $instanceSummary = new Summary;
            $instanceSummary->mds_gsb_id = $mdsGsbID;
            $instanceSummary->department = $department;
            $instanceSummary->entity_name = $entityName;
            $instanceSummary->operating_unit = $operatingUnit;
            $instanceSummary->fund_cluster = $fundCluster;
            $instanceSummary->sliiae_no = $sliiaeNo;
            $instanceSummary->date_sliiae = $sliiaeDate;
            $instanceSummary->to = $to;
            $instanceSummary->bank_name = $bankName;
            $instanceSummary->bank_address = $bankAddress;
            $instanceSummary->total_amount = $totalAmount;
            $instanceSummary->total_amount_words = $totalAmountWords;
            $instanceSummary->lddap_no_pcs = $countLDDAP;
            $instanceSummary->sig_cert_correct = $sigCertCorrect;
            $instanceSummary->sig_approved_by = $sigApprovedBy;
            $instanceSummary->sig_delivered_by = $sigDeliveredBy;
            $instanceSummary->save();

            $lastSummary = Summary::orderBy('created_at', 'desc')->first();
            $lastID = $lastSummary->id;

            if (is_array($lddapIDs)) {
                if (count($lddapIDs) > 0) {
                    foreach ($lddapIDs as $ctr => $lddapID) {
                        $itemNo = $ctr + 1;
                        $instanceSummaryItem = new SummaryItem;
                        $instanceSummaryItem->sliiae_id = $lastID;
                        $instanceSummaryItem->item_no = $itemNo;
                        $instanceSummaryItem->lddap_id = $lddapID;
                        $instanceSummaryItem->date_issue = $dateIssues[$ctr];
                        $instanceSummaryItem->total = $totals[$ctr];
                        $instanceSummaryItem->allotment_ps = $allotmentPSs[$ctr];
                        $instanceSummaryItem->allotment_mooe = $allotmentMOOEs[$ctr];
                        $instanceSummaryItem->allotment_co = $allotmentCOs[$ctr];
                        $instanceSummaryItem->allotment_fe = $allotmentFEs[$ctr];
                        $instanceSummaryItem->allotment_remarks = $allotmentRemarks[$ctr];
                        $instanceSummaryItem->save();
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
        $total = 0;
        $allotmentPS = 0;
        $allotmentMOOE = 0;
        $allotmentCO = 0;
        $allotmentFE = 0;

        $mdsGSBs = MdsGsb::all();
        $summaryData = Summary::find($id);
        $items = SummaryItem::where('sliiae_id', $id)
                            ->orderBy('item_no')
                            ->get();
        $mdsID = $summaryData->mds_gsb_id;
        $department = $summaryData->department;
        $entityName = $summaryData->entity_name;
        $operatingUnit = $summaryData->operating_unit;
        $fundCluster = $summaryData->fund_cluster;
        $sliiaeNo = $summaryData->sliiae_no;
        $sliiaeDate = $summaryData->date_sliiae;
        $to = $summaryData->to;
        $bankName = $summaryData->bank_name;
        $bankAddress = $summaryData->bank_address;
        $sigCertCorrect = $summaryData->sig_cert_correct;
        $sigApprovedBy = $summaryData->sig_approved_by;
        $sigDeliveredBy = $summaryData->sig_delivered_by;
        $lddapCount = $summaryData->lddap_no_pcs;
        $totalAmountWords = $summaryData->total_amount_words;
        $totalAmount = $summaryData->total_amount;

        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($items as $item) {
            $lddapData = ListDemandPayable::find($item->lddap_id);
            $item->lddap_no = $lddapData->lddap_ada_no;
        }

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.payment.summary.update', compact(
            'id', 'mdsGSBs', 'signatories', 'items', 'mdsID',
            'department', 'entityName', 'operatingUnit',
            'fundCluster', 'sliiaeNo', 'sliiaeDate',
            'to', 'bankName', 'bankAddress', 'sigCertCorrect',
            'sigApprovedBy', 'sigDeliveredBy', 'lddapCount',
            'totalAmountWords', 'totalAmount', 'total',
            'allotmentPS', 'allotmentMOOE', 'allotmentCO',
            'allotmentFE'
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
        $mdsGsbID = $request->mds_gsb_id;
        $department = $request->department;
        $entityName = $request->entity_name;
        $operatingUnit = $request->operating_unit;
        $fundCluster = $request->fund_cluster;
        $sliiaeNo = $request->sliiae_no;
        $sliiaeDate = $request->sliiae_date;
        $to = $request->to;
        $bankName = $request->bank_name;
        $bankAddress = $request->bank_address;
        $totalAmount = $request->total_amount;
        $totalAmountWords = $request->total_amount_words;
        $sigCertCorrect = $request->cert_correct;
        $sigApprovedBy = $request->approved_by;
        $sigDeliveredBy = $request->delivered_by;

        $lddapIDs = $request->lddap_id;
        $dateIssues = $request->date_issue;
        $totals = $request->total;
        $allotmentPSs = $request->allotment_ps;
        $allotmentMOOEs = $request->allotment_mooe;
        $allotmentCOs = $request->allotment_co;
        $allotmentFEs = $request->allotment_fe;
        $allotmentRemarks = $request->allotment_remarks;

        $countLDDAP = count($lddapIDs);
        $documentType = 'Summary of LDDAP-ADAs Issued and Invalidated ADA Entries';
        $routeName = 'summary';

        try {
            $instanceSummary = Summary::find($id);
            $instanceSummary->mds_gsb_id = $mdsGsbID;
            $instanceSummary->department = $department;
            $instanceSummary->entity_name = $entityName;
            $instanceSummary->operating_unit = $operatingUnit;
            $instanceSummary->fund_cluster = $fundCluster;
            $instanceSummary->sliiae_no = $sliiaeNo;
            $instanceSummary->date_sliiae = $sliiaeDate;
            $instanceSummary->to = $to;
            $instanceSummary->bank_name = $bankName;
            $instanceSummary->bank_address = $bankAddress;
            $instanceSummary->total_amount = $totalAmount;
            $instanceSummary->total_amount_words = $totalAmountWords;
            $instanceSummary->lddap_no_pcs = $countLDDAP;
            $instanceSummary->sig_cert_correct = $sigCertCorrect;
            $instanceSummary->sig_approved_by = $sigApprovedBy;
            $instanceSummary->sig_delivered_by = $sigDeliveredBy;
            $instanceSummary->save();

            if ((is_array($lddapIDs) && count($lddapIDs) > 0)) {
                SummaryItem::where('sliiae_id', $id)->delete();
            }

            if (is_array($lddapIDs)) {
                if (count($lddapIDs) > 0) {
                    foreach ($lddapIDs as $ctr => $lddapID) {
                        $itemNo = $ctr + 1;
                        $instanceSummaryItem = new SummaryItem;
                        $instanceSummaryItem->sliiae_id = $id;
                        $instanceSummaryItem->item_no = $itemNo;
                        $instanceSummaryItem->lddap_id = $lddapID;
                        $instanceSummaryItem->date_issue = $dateIssues[$ctr];
                        $instanceSummaryItem->total = $totals[$ctr];
                        $instanceSummaryItem->allotment_ps = $allotmentPSs[$ctr];
                        $instanceSummaryItem->allotment_mooe = $allotmentMOOEs[$ctr];
                        $instanceSummaryItem->allotment_co = $allotmentCOs[$ctr];
                        $instanceSummaryItem->allotment_fe = $allotmentFEs[$ctr];
                        $instanceSummaryItem->allotment_remarks = $allotmentRemarks[$ctr];
                        $instanceSummaryItem->save();
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
                return redirect()->route('summary', ['keyword' => $response->id])
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route('summary')
                                 ->with($response->alert_type, $response->msg);
            }
        } else {

                $instanceSummary = Summary::find($id);
                $documentType = 'Summary';
                $instanceSummary->delete();

                $msg = "$documentType '$id' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect()->route('summary', ['keyword' => $id])
                                 ->with('success', $msg);try {
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect()->route('summary')
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
            $instanceSummary = Summary::find($id);
            $documentType = 'Summary';
            SummaryItem::where('sliiae_id', $id)->delete();
            $instanceSummary->forceDelete();

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

    public function getListLDDAP(Request $request) {
        $keyword = trim($request->search);
        $lddapData = ListDemandPayable::select('id', 'lddap_ada_no',
                                               'total_amount', 'date_lddap');

        if ($keyword) {
            $lddapData = $lddapData->where(function($qry) use ($keyword) {
                $qry->where('lddap_ada_no', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('lddap_ada_no', 'like', "%$tag%");
                    }
                }
            });
        }

        $lddapData = $lddapData->orderBy('lddap_ada_no')->get();

        return response()->json($lddapData);
    }

    public function forApproval(Request $request, $id) {
        $documentType = 'Summary of LDDAP-ADAs Issued and Invalidated ADA Entries';
        $routeName = 'summary';

        try {
            $instanceDocLog = new DocLog;
            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);

            if ($isDocGenerated) {
                $instanceSummary = Summary::find($id);
                $instanceSummary->status = 'for_approval';
                $instanceSummary->date_for_approval = Carbon::now();
                $instanceSummary->for_approval_by = Auth::user()->id;
                $instanceSummary->save();

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
        $documentType = 'Summary of LDDAP-ADAs Issued and Invalidated ADA Entries';
        $routeName = 'summary';

        try {
            $instanceNotif = new Notif;
            $instanceSummary = Summary::find($id);
            $instanceSummary->status = 'approved';
            $instanceSummary->date_approved = Carbon::now();
            $instanceSummary->approved_by = Auth::user()->id;
            $instanceSummary->save();

            $instanceNotif->notifyApproveSummary($id, Auth::user()->id);

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

    public function submissionBank(Request $request, $id) {
        $documentType = 'Summary of LDDAP-ADAs Issued and Invalidated ADA Entries';
        $routeName = 'summary';

        try {
            $instanceSummary = Summary::find($id);
            $instanceSummary->status = 'for_submission_bank';
            $instanceSummary->date_for_submission_bank = Carbon::now();
            $instanceSummary->for_submission_bank_by = Auth::user()->id;
            $instanceSummary->save();

            $msg = "$documentType '$id' successfully set to 'For Submission to Bank'.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }
}
