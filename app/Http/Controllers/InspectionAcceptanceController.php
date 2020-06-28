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

class InspectionAcceptanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // Get module access
        $module = 'proc_iar';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedInspect = Auth::user()->getModuleAccess($module, 'inspect');

        $isAllowedPO = Auth::user()->getModuleAccess('proc_po_jo', 'is_allowed');
        $isAllowedDV = Auth::user()->getModuleAccess('proc_dv', 'is_allowed');

        $isAllowedCreateStocks = Auth::user()->getModuleAccess('inv_stocks', 'create');
        $isAllowedUpdateStocks = Auth::user()->getModuleAccess('inv_stocks', 'update');

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $iarData = PurchaseRequest::with(['funding', 'requestor'])
                                 ->whereHas('division', function($query)
                                            use($empDivisionAccess) {
            $query->whereIn('id', $empDivisionAccess);
        })->whereHas('iar', function($query) {
            $query->whereNotNull('id');
        });

        if (!empty($keyword)) {
            $iarData = $iarData->where(function($qry) use ($keyword) {
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
                    })->orWhereHas('po', function($query) use ($keyword) {
                        $query->where('po_no', 'like', "%$keyword%")
                              ->orWhere('id', 'like', "%$keyword%")
                              ->orWhere('date_po', 'like', "%$keyword%")
                              ->orWhere('date_po_approved', 'like', "%$keyword%")
                              ->orWhere('date_cancelled', 'like', "%$keyword%")
                              ->orWhere('place_delivery', 'like', "%$keyword%")
                              ->orWhere('date_delivery', 'like', "%$keyword%")
                              ->orWhere('delivery_term', 'like', "%$keyword%")
                              ->orWhere('payment_term', 'like', "%$keyword%")
                              ->orWhere('amount_words', 'like', "%$keyword%")
                              ->orWhere('grand_total', 'like', "%$keyword%")
                              ->orWhere('fund_cluster', 'like', "%$keyword%");
                    })->orWhereHas('iar', function($query) use ($keyword) {
                        $query->where('id', 'like', "%$keyword%")
                              ->orWhere('po_id', 'like', "%$keyword%")
                              ->orWhere('iar_no', 'like', "%$keyword%")
                              ->orWhere('date_iar', 'like', "%$keyword%")
                              ->orWhere('invoice_no', 'like', "%$keyword%")
                              ->orWhere('date_invoice', 'like', "%$keyword%");
                    });
            });
        }

        $iarData = $iarData->sortable(['pr_no' => 'desc'])->paginate(15);

        foreach ($iarData as $iar) {
            foreach ($iar->iar as $ctrIAR => $iarDat) {
                $iarDat->doc_status = $instanceDocLog->checkDocStatus($iarDat->id);
                $inventoryCount = InventoryStock::where('po_id', $iar->po[$ctrIAR]->id)
                                                ->count();

                $instanceSupplier = Supplier::find($iar->po[$ctrIAR]->awarded_to);
                $companyName = $instanceSupplier->company_name;
                $iarDat->company_name = $companyName;
                $iarDat->inventory_count = $inventoryCount;
            }
        }

        return view('modules.procurement.iar.index', [
            'list' => $iarData,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedInspect' => $isAllowedInspect,
            'isAllowedPO' => $isAllowedPO,
            'isAllowedDV' => $isAllowedDV,
            'isAllowedCreateStocks' => $isAllowedCreateStocks,
            'isAllowedUpdateStocks' => $isAllowedUpdateStocks
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit(Request $request, $id) {
        $instanceIAR = InspectionAcceptance::with('po')->find($id);
        $prID = $instanceIAR->pr_id;
        $poID = $instanceIAR->po_id;
        $instancePO = PurchaseJobOrder::with('awardee')->find($poID);
        $instancePR = PurchaseRequest::with('div')->find($prID);
        $poDate = $instanceIAR->po->date_po;
        $division = $instancePR->div['division_name'];
        $poNo = $instanceIAR->po->po_no;
        $awardee = $instancePO->awardee['company_name'];
        $poItem = PurchaseJobOrderItem::with('unitissue')
                                      ->where([
            ['po_no', $poNo], ['excluded', 'n']
        ])->get();
        $iarNo = $instanceIAR->iar_no;
        $iarDate = $instanceIAR->date_iar;
        $invoiceNo = $instanceIAR->invoice_no;
        $invoiceDate = $instanceIAR->date_invoice;
        $sigInspection = $instanceIAR->sig_inspection;
        $sigSupply = $instanceIAR->sig_supply;
        $inspectionRemarks = $instanceIAR->inspection_remarks;
        $receivedDate = $instanceIAR->date_received;
        $acceptanceRemarks = $instanceIAR->acceptance_remarks;
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.procurement.iar.update', compact(
            'poDate', 'division', 'poNo', 'poItem', 'iarNo',
            'iarDate', 'invoiceNo', 'invoiceDate', 'sigInspection',
            'sigSupply', 'inspectionRemarks', 'receivedDate',
            'acceptanceRemarks', 'signatories', 'id', 'awardee'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id) {
        $iarDate = $request->date_iar ? $request->date_iar : NULL;
        $invoiceNo = $request->invoice_no;
        $dateInvoice = $request->date_invoice ? $request->date_invoice : NULL;
        $sigInspection = $request->sig_inspection;
        $sigSupply = $request->sig_prop_supply;

        try {
            $instanceIAR = InspectionAcceptance::find($id);
            $iarNo = $instanceIAR->iar_no;

            $instanceIAR->date_iar = $iarDate;
            $instanceIAR->invoice_no = $invoiceNo;
            $instanceIAR->date_invoice = $dateInvoice;
            $instanceIAR->sig_inspection = $sigInspection;
            $instanceIAR->sig_supply = $sigSupply;
            $instanceIAR->save();

            $msg = "Inspection and Acceptance Report '$iarNo' successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect()->route('iar', ['keyword' => $iarNo])
                             ->with('success', $msg);
        } catch (Exception $e) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('iar')
                             ->with('failed', $msg);
        }
    }

    public function showIssue($id) {
        $users = User::orderBy('firstname')->get();

        return view('modules.procurement.iar.issue', [
            'id' => $id,
            'users' => $users
        ]);
    }

    public function issue(Request $request, $id) {
        $issuedTo = $request->issued_to;
        $remarks = $request->remarks;
        $instanceDocLog = new DocLog;

        try {
            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);

            if ($isDocGenerated) {
                $instanceIAR = InspectionAcceptance::find($id);
                $iarNo = $instanceIAR->iar_no;

                $instanceDocLog->logDocument($id, Auth::user()->id, $issuedTo, "issued", $remarks);
                $issuedToName = Auth::user()->getEmployee($issuedTo)->name;

                //$instanceRFQ->notifyIssued($id, $issuedTo, $requestedBy);

                $msg = "Inspection and Acceptance Report '$iarNo' successfully issued to $issuedToName.";
                Auth::user()->log($request, $msg);
                return redirect()->route('iar', ['keyword' => $id])
                                 ->with('success', $msg);
            } else {
                $msg = "Document for Inspection and Acceptance Report '$id' should be generated first.";
                Auth::user()->log($request, $msg);
                return redirect()->route('iar', ['keyword' => $id])
                                 ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('iar', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }

    public function showInspect($id) {
        return view('modules.procurement.iar.inspect', [
            'id' => $id
        ]);
    }

    public function inspect(Request $request, $id) {
        $remarks = $request->remarks;
        $instanceDocLog = new DocLog;

        try {
            $instanceIAR = InspectionAcceptance::with(['po', 'ors'])->find($id);
            $poID = $instanceIAR->po_id;
            $iarNo = $instanceIAR->iar_no;
            $inspectedBy = $instanceIAR->sig_inspection;

            $instanceDV = new DisbursementVoucher;
            $instanceDV->pr_id = $instanceIAR->pr_id;
            $instanceDV->ors_id = $instanceIAR->ors_id;
            $instanceDV->particulars = "To payment of...";
            $instanceDV->payee = $instanceIAR->po->awarded_to;
            $instanceDV->sig_certified = $instanceIAR->ors->sig_certified_1;
            $instanceDV->sig_accounting = $instanceIAR->po['sig_funds_available'];
            $instanceDV->sig_agency_head = $instanceIAR->po['sig_approval'];
            $instanceDV->amount = $instanceIAR->ors->amount;
            $instanceDV->module_class = 3;
            $instanceDV->save();

            $instancePO = PurchaseJobOrder::find($poID);
            $instancePO->status = 10;
            $instancePO->save();

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received", $remarks);

            $msg = "Inspection and Acceptance Report '$iarNo' is successfully set to
                   'Inspected' and ready for Disbursement Voucher.";
            Auth::user()->log($request, $msg);
            return redirect()->route('iar', ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('iar', ['keyword' => $id])
                             ->with('failed', $msg);
        }
    }
}
