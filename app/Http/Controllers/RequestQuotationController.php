<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\RequestQuotation;
use App\Models\AbstractQuotation;
use App\Models\PurchaseJobOrder;
use App\Models\ObligationRequestStatus;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\InventoryStock;

use App\User;
use App\Models\FundingSource;
use App\Models\ItemUnitIssue;
use App\Models\Signatory;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use Carbon\Carbon;
use DB;
use Auth;

class RequestQuotationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($keyword = '') {
        $keyword = trim($keyword);
        $instanceDocLog = new DocLog;

        // Get module access
        $module = 'proc_rfq';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedAbstract = Auth::user()->getModuleAccess('proc_abs', 'is_allowed');

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();

        $rfqData = RequestQuotation::whereHas('pr', function($query)
                    use ($empDivisionAccess) {
            $query->whereIn('division', $empDivisionAccess)
                  ->orderBy('pr_no', 'desc');
        });

        if ($roleHasOrdinary) {
            $rfqData = $rfqData->whereHas('pr', function($query) {
                $query->where('requested_by', Auth::user()->id);
            });
        }

        if (!empty($keyword)) {
            $rfqData = $rfqData->where('id', $keyword);
        }

        $rfqData = $rfqData->get();

        foreach ($rfqData as $rfq) {
            $instanceFundSource = FundingSource::find($rfq->pr->funding_source);
            $fundingSource = !empty($instanceFundSource->source_name) ?
                              $instanceFundSource->source_name : '';
            $requestedBy = Auth::user()->getEmployeeName($rfq->pr->requested_by);

            $rfq->doc_status = $instanceDocLog->checkDocStatus($rfq->id);
            $rfq->pr->funding_source = $fundingSource;
            $rfq->pr->requested_by = $requestedBy;
        }

        return view('modules.procurement.rfq.index', [
            'list' => $rfqData,
            'paperSizes' => $paperSizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedAbstract' => $isAllowedAbstract,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $rfqData = RequestQuotation::find($id);
        $prID = $rfqData->pr_id;
        $rfqDate = $rfqData->date_canvass;
        $sigRFQ = $rfqData->sig_rfq;
        $canvassedBy = $rfqData->canvassed_by;
        $prItemData = PurchaseRequestItem::where('pr_id', $prID)->get();
        $unitIssues = ItemUnitIssue::orderBy('unit_name')->get();
        $users = User::where('is_active', 'y')
                    ->orderBy('firstname')->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.procurement.rfq.update', [
            'id' => $id,
            'users' => $users,
            'unitIssues' => $unitIssues,
            'prItems' => $prItemData,
            'signatories' => $signatories,
            'rfqDate' => $rfqDate,
            'sigRFQ' => $sigRFQ,
            'canvassedBy' => $canvassedBy,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $instanceDocLog = new DocLog;

        $itemIDs = $request->pr_item_id;
        $groupNos = $request->canvass_group;
        $rfqDate = $request->date_canvass;
        $sigRFQ = $request->sig_rfq;
        $canvassedBy = $request->canvassed_by;

        try {
            $instanceRFQ = RequestQuotation::find($id);
            $prID = $instanceRFQ->pr_id;
            $instanceRFQ->date_canvass = $rfqDate;
            $instanceRFQ->sig_rfq = $sigRFQ;
            $instanceRFQ->canvassed_by = $canvassedBy;
            $instanceRFQ->save();

            foreach ($itemIDs as $key => $itemID) {
                $groupNo = $groupNos[$key];

                $instancePRItem = PurchaseRequestItem::find($itemID);
                $instancePRItem->group_no = $groupNo;
                $instancePRItem->save();
            }

            $instancePR = PurchaseRequest::find($prID);
            $prNo = $instancePR->pr_no;
            $instancePR->status = 5;
            $instancePR->save();

            // Delete dependent documents
            AbstractQuotation::where('pr_id', $prID)->delete();
            //DB::table('tblabstract_items')->where('pr_id', $prID)->delete();
            PurchaseJobOrder::where('pr_id', $prID)->delete();
            //DB::table('tblpo_jo_items')->where('pr_id', $prID)->delete();
            ObligationRequestStatus::where('pr_id', $prID)->delete();
            InspectionAcceptance::where('pr_id', $prID)->delete();
            DisbursementVoucher::where('pr_id', $prID)->delete();
            InventoryStock::where('pr_id', $prID)->delete();
            //DB::table('tblinventory_stocks_issue')->where('pr_id', $id)->delete();

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, '-');

            $msg = "Request for Quotation '$prNo' successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showIssue($id) {
        $users = User::orderBy('firstname')->get();

        return view('modules.procurement.rfq.issue', [
            'id' => $id,
            'users' => $users
        ]);
    }

    public function issue(Request $request, $id) {
        $issuedTo = $request->issued_to;
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceRFQ = RequestQuotation::find($id);
            $prID = $instanceRFQ->pr_id;

            $instancePR = PurchaseRequest::find($prID);
            $prNo = $instancePR->pr_no;

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);
            $docStatus = $instanceDocLog->checkDocStatus($id);

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $instanceDocLog->logTrackerHistory($id, Auth::user()->emp_id, $issuedTo, "issued", $remarks);
                    $issuedToName = Auth::user()->getEmployeeName($issuedTo);

                    $msg = "Request for Quotation '$prNo' successfully issued to $issuedToName.";
                    Auth::user()->log($request, $msg);
                    return redirect(url()->previous())->with('success', $msg);
                } else {
                    $msg = "Document for Request for Quotation '$prNo' should be generated first.";
                    Auth::user()->log($request, $msg);
                    return redirect(url()->previous())->with('warning', $msg);
                }
            } else {
                $msg = "Request for Quotation '$prNo' already issued.";
                Auth::user()->log($request, $msg);
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function receive(Request $request, $id) {
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
}
