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
    public function index(Request $request) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // Get module access
        $module = 'proc_rfq';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedAbstract = Auth::user()->getModuleAccess('proc_abstract', 'is_allowed');

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $rfqData = PurchaseRequest::with(['funding', 'requestor'])
                                  ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
            $query->whereIn('id', $empDivisionAccess);
        })->whereHas('rfq', function($query) {
            $query->whereNotNull('id');
        });

        if ($roleHasOrdinary) {
            $rfqData = $rfqData->where('requested_by', Auth::user()->id);
        } else {
            $rfqData = $rfqData->orWhere('requested_by', Auth::user()->id);
        }

        if (!empty($keyword)) {
            $rfqData = $rfqData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('pr_no', 'like', "%$keyword%")
                    ->orWhere('date_pr', 'like', "%$keyword%")
                    ->orWhere('purpose', 'like', "%$keyword%")
                    ->orWhereHas('funding', function($query) use ($keyword) {
                        $query->where('source_name', 'like', "%$keyword%");
                    })->orWhereHas('stat', function($query) use ($keyword) {
                        $query->where('status_name', 'like', "%$keyword%");
                    })->orWhereHas('requestor', function($query) use ($keyword) {
                        $query->where('firstname', 'like', "%$keyword%")
                              ->orWhere('middlename', 'like', "%$keyword%")
                              ->orWhere('lastname', 'like', "%$keyword%");
                    })->orWhereHas('items', function($query) use ($keyword) {
                        $query->where('item_description', 'like', "%$keyword%");
                    })->orWhereHas('division', function($query) use ($keyword) {
                        $query->where('division_name', 'like', "%$keyword%");
                    })->orWhereHas('rfq', function($query) use ($keyword) {
                        $query->where('id', 'like', "%$keyword%")
                              ->orWhere('date_canvass', 'like', "%$keyword%");
                    });
            });
        }

        $rfqData = $rfqData->sortable(['pr_no' => 'desc'])->paginate(20);

        foreach ($rfqData as $rfq) {
            $rfq->doc_status = $instanceDocLog->checkDocStatus($rfq->rfq['id']);
        }

        return view('modules.procurement.rfq.index', [
            'list' => $rfqData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedUpdate' => $isAllowedUpdate,
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

            $instanceDocLog->logDocument($id, NULL, NULL, '-');

            $msg = "Request for Quotation '$prNo' successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect()->route('rfq', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('rfq', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }

    public function showIssue($id) {
        $users = User::orderBy('firstname')->get();

        return view('modules.procurement.rfq.issue', [
            'id' => $id,
            'users' => $users
        ]);
    }

    public function showReceive($id) {
        return view('modules.procurement.rfq.receive', [
            'id' => $id,
        ]);
    }

    public function issue(Request $request, $id) {
        $issuedTo = $request->issued_to;
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceRFQ = RequestQuotation::with('pr')->where('id', $id)->first();
            $prID = $instanceRFQ->pr_id;
            $prNo = $instanceRFQ->pr->pr_no;
            $requestedBy = $instanceRFQ->pr->requested_by;

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);
            $docStatus = $instanceDocLog->checkDocStatus($id);

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $instanceDocLog->logDocument($id, Auth::user()->id, $issuedTo, "issued", $remarks);
                    $issuedToName = Auth::user()->getEmployee($issuedTo)->name;

                    $instanceRFQ->notifyIssued($id, $issuedTo, $requestedBy);

                    $msg = "Request for Quotation '$prNo' successfully issued to $issuedToName.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route('rfq', ['keyword' => $id])
                                     ->with('success', $msg);
                } else {
                    $msg = "Document for Request for Quotation '$prNo' should be generated first.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route('rfq', ['keyword' => $id])
                                     ->with('warning', $msg);
                }
            } else {
                $msg = "Request for Quotation '$prNo' already issued.";
                Auth::user()->log($request, $msg);
                return redirect()->route('rfq', ['keyword' => $id])
                                 ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('rfq', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }

    public function receive(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $docStatus = $instanceDocLog->checkDocStatus($id);
            $instanceRFQ = RequestQuotation::with('pr')->where('id', $id)->first();
            $prID = $instanceRFQ->pr_id;
            $prNo = $instanceRFQ->pr->pr_no;
            $requestedBy = $instanceRFQ->pr->requested_by;
            $responsiblePerson = $docStatus->issued_to_id;

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received", $remarks);
            $instanceAbstract = AbstractQuotation::where('pr_id', $prID)->first();

            if (!$instanceAbstract) {
                $instanceAbstract = new AbstractQuotation;
                $instanceAbstract->pr_id = $prID;
                $instanceAbstract->save();

                $abstractData = AbstractQuotation::where('pr_id', $prID)->first();
                $abstractID = $abstractData->id;

                $instanceDocLog->logDocument($abstractID, Auth::user()->id, NULL, "issued");
            } else {
                AbstractQuotation::where('pr_id', $id)->restore();
            }

            $instanceRFQ->notifyReceived($id, Auth::user()->id, $responsiblePerson, $requestedBy);

            $msg = "Request for Quotation '$prNo' successfully received and ready for Abstract
                    of Quotation.";
            Auth::user()->log($request, $msg);
            return redirect()->route('rfq', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('rfq', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }
}
