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
use App\Models\EmpGroup;
use App\Models\EmpDivision;
use App\Models\ItemUnitIssue;
use App\Models\FundingSource;
use App\Models\Signatory;
use App\Models\EmpLog;
use App\Models\DocumentLog;
use App\Models\PaperSize;
use DB;
use Auth;

use App\Notifications\PurchaseReqAction;

class PurchaseRequestController extends Controller
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
    public function index(Request $request) {
        Auth::user()->log($request, 'sdasd');
        // Get module access
        $module = 'proc_pr';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedCancel = Auth::user()->getModuleAccess($module, 'cancel');
        $isAllowedApprove = Auth::user()->getModuleAccess($module, 'approve');
        $isAllowedDisapprove = Auth::user()->getModuleAccess($module, 'disapprove');
        $isAllowedRFQ = Auth::user()->getModuleAccess($module, 'is_allowed');

        // User groups
        $empDivisionAccess = Auth::user()->getDivisionAccess();

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $prData = PurchaseRequest::addSelect([
            'funding_source' => FundingSource::select('source_name')
                                       ->whereColumn('id', 'purchase_requests.funding_source')
                                       ->limit(1),
            'name' =>  User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                           ->whereColumn('id', 'purchase_requests.requested_by')
                           ->limit(1),
            'status_name' =>  DB::table('procurement_status')
                                ->select('status_name')
                                ->whereColumn('id', 'purchase_requests.status')
                                ->limit(1),
        ])->whereIn('division', $empDivisionAccess)->orderBy('pr_no')->get();

        return view('modules.procurement.pr.index', [
            'list' => $prData,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'isAllowedCancel' => $isAllowedCancel,
            'isAllowedApprove' => $isAllowedApprove,
            'isAllowedDisapprove' => $isAllowedDisapprove,
            'isAllowedRFQ' => $isAllowedRFQ,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $itemNo = 0;
        $status = 1;
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $unitIssues = ItemUnitIssue::orderBy('unit_name')->get();
        $fundingSources = FundingSource::orderBy('source_name')->get();
        $divisions = $roleHasOrdinary ?
                    EmpDivision::where('id', Auth::user()->division)
                               ->orderBy('division_name')
                               ->get() :
                     EmpDivision::orderBy('division_name')->get();
        $users = $roleHasOrdinary ?
                User::where('id', Auth::user()->id)
                    ->orderBy('firstname')
                    ->get() :
                User::where('is_active', 'y')
                    ->orderBy('firstname')->get();
        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.procurement.pr.create', [
            'users' => $users,
            'signatories' => $signatories,
            'unitIssues' => $unitIssues,
            'fundingSources' => $fundingSources,
            'divisions' => $divisions,
            'itemNo' => $itemNo,
            'status' => $status
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $itemNo = 0;
        $toggle = "edit";
        $unitIssue = UnitIssue::all();
        $projects = Projects::all();
        $division = Division::all();
        $approvedBy = DB::table('signatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.pr_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('emp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->orderBy('emp.firstname')
                         ->where([['sig.p_req', 'y'],
                                  ['sig.active', 'y']])
                         ->get();
        $pr = DB::table('purchase_requests')
                ->where('id', $id)
                ->first();
        $prItems = DB::table('purchase_requests_items')
                     ->where('pr_id', $id)
                     ->orderByRaw('LENGTH(item_id)')
                     ->orderBy('item_id')
                     ->get();

        if (Auth::user()->role == 1 || Auth::user()->role == 2 || Auth::user()->role == 5) {
            $requestedBy = User::orderBy('firstname')->get();
        } else {
            $requestedBy = User::where('emp_id', Auth::user()->emp_id)->get();
        }

        return view('modules.procuremnt.pr.update', [
            'requestedBy' => $requestedBy,
            'approvedBy' => $approvedBy,
            'unitIssue' => $unitIssue,
            'projects' => $projects,
            'divisions' => $division,
            'itemNo' => $itemNo,
            'id' => $id,
            'toggle' => $toggle,
            'pr' => $pr,
            'prItems' => $prItems
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // Parent variables
        $prDate = $request->date_pr;
        $prNo = '';
        $divisionID = $request->division;
        $purpose = $request->purpose;
        $remarks = $request->remarks;
        $projectID = $request->project;
        $requestedBy = $request->requested_by;
        $approvedBy = $request->approved_by;
        $recommendedBy = $request->recommended_by;
        $office = $request->office;

        // PR Item variables
        $unitIssues = $request->unit;
        $itemDescriptions = $request->item_description;
        $quantities = $request->quantity;
        $unitCosts = $request->unit_cost;
        $totalCosts = $request->total_cost;

        try {
            // Auto Generate pr_no if empty
            $prSequence = PurchaseRequest::select('id', 'pr_no')
                                         ->orderBy('pr_no')
                                         ->get();
            $currentYearMonth = date('y') . date('m');

            if (count($prSequence) > 0) {
                $prNumber = "";

                foreach ($prSequence as $key => $_prNumber) {
                    if (substr($_prNumber->pr_no, 0, 4) == $currentYearMonth) {
                        $prNumber = $_prNumber->pr_no;
                    }
                }

                $prSequenceNumber = (int)substr($prNumber, 4) + 1;
                $prNo = $currentYearMonth . str_pad($prSequenceNumber, 3, '0', STR_PAD_LEFT);
            } else {
                $prNo = $currentYearMonth . '001';
            }

            $instancePR = new PurchaseRequest;

            if (!$instancePR->checkDuplication($prNo)) {
                // Storing main PR data
                $instancePR->date_pr = $prDate;
                $instancePR->pr_no = $prNo;
                $instancePR->funding_source = $projectID;
                $instancePR->purpose = $purpose;
                $instancePR->remarks = $remarks;
                $instancePR->division = $divisionID;
                $instancePR->requested_by = $requestedBy;
                $instancePR->approved_by = $approvedBy;
                $instancePR->recommended_by = $recommendedBy;
                $instancePR->office = $office;
                $instancePR->status = 1;
                $instancePR->save();

                // Storing PR Items data
                $prData = PurchaseRequest::where('pr_no', $prNo)->first();
                $prID = $prData->id;

                foreach ($unitIssues as $arrayKey => $unit) {
                    $description = $itemDescriptions[$arrayKey];
                    $quantity = $quantities[$arrayKey];
                    $unitCost = $unitCosts[$arrayKey];
                    $totalCost =  $quantity * $unitCost;

                    $instancePRItem = new PurchaseRequestItem;
                    $instancePRItem->pr_id = $prID;
                    $instancePRItem->item_no = $arrayKey + 1;
                    $instancePRItem->quantity = $quantity;
                    $instancePRItem->unit_issue = $unit;
                    $instancePRItem->item_description = $description;
                    $instancePRItem->est_unit_cost = $unitCost;
                    $instancePRItem->est_total_cost = $totalCost;
                    $instancePRItem->save();
                }

                //$this->notifyForApproval($prNo, $requestedBy);
                $prData->logDocument($prID, Auth::user()->id, NULL, 'issued');

                $msg = "Purchase Request '$prNo' successfully created.";
                Auth::user()->log($request, $msg);
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Purchase Request '$prNo' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
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
        $pr = PurchaseRequest::where('id', $id)->first();
        $canvass = Canvass::where('pr_id', $id)->first();
        $prNo = $pr->pr_no;

        $prDate = $request['date_pr'];
        $divisionID = $request['division'];
        $purpose = $request['purpose'];
        $remarks = $request['remarks'];
        $projectID = $request['project'];
        $requestedBy = $request['requested_by'];
        $approvedBy = $request['approved_by'];
        //$sigApp = $request['sig_app'];
        //$sigFundsAvailable = $request['sig_funds_available'];
        $recommendedBy = $request['recommended_by'];
        $office = $request['office'];

        // PR items variables
        $itemIDs = $request['item_id'];
        $unitIssue = $request['unit'];
        $itemDescription = $request['item_description'];
        $quantity = $request['quantity'];
        $unitCost = $request['unit_cost'];
        $totalCost = $request['total_cost'];

        // Save data
        try {
            $pr->date_pr = $prDate;
            $pr->date_pr = $prDate;
            $pr->project_id = $projectID;
            $pr->purpose = $purpose;
            $pr->remarks = $remarks;
            $pr->pr_division_id = $divisionID;
            $pr->requested_by = $requestedBy;
            $pr->approved_by = $approvedBy;
            //$pr->sig_app = $sigApp;
            //$pr->sig_funds_available = $sigFundsAvailable;
            $pr->recommended_by = $recommendedBy;
            $pr->office = $office;

            // Delete other dependent documents
            if ($pr->status >= 5) {
                Canvass::where('pr_id', $id)->forceDelete();
                Abstracts::where('pr_id', $id)->forceDelete();
                DB::table('tblabstract_items')->where('pr_id', $id)->delete();
                PurchaseOrder::where('pr_id', $id)->forceDelete();
                DB::table('tblpo_jo_items')->where('pr_id', $id)->delete();
                OrsBurs::where('pr_id', $id)->forceDelete();
                InspectionAcceptance::where('pr_id', $id)->forceDelete();
                DisbursementVoucher::where('pr_id', $id)->forceDelete();
                InventoryStock::where('pr_id', $id)->forceDelete();
                DB::table('tblinventory_stocks_issue')->where('pr_id', $id)->delete();
            }

            // Update pr items
            foreach ($unitIssue as $arrayKey => $unit) {
                $description = $itemDescription[$arrayKey];
                $qnty = $quantity[$arrayKey];
                $unCost = $unitCost[$arrayKey];
                $totCost =  $qnty * $unCost;

                if ($pr->status < 5) {
                    $itemID = $id . "-" . ($arrayKey + 1);

                    if ($arrayKey == 0) {
                        DB::table('purchase_requests_items')->where('pr_id', $id)->delete();
                    }

                    DB::table('purchase_requests_items')->insert(
                        ['item_id' => $itemID,
                         'pr_id' => $id,
                         'quantity' => $qnty,
                         'unit_issue' => $unit,
                         'item_description' => $description,
                         'est_unit_cost' => $unCost,
                         'est_total_cost' => $totCost]
                    );
                } else {
                    $itemID = $itemIDs[$arrayKey];
                    $itemIDsCount = count($itemIDs) - 1;

                    if ($itemIDsCount == $arrayKey) {
                        $pr->status = 1;
                        $this->notifyForApproval($prNo, $requestedBy);
                    }

                    DB::table('purchase_requests_items')
                      ->where('item_id', $itemID)
                      ->update([
                            'quantity' => $qnty,
                            'unit_issue' => $unit,
                            'item_description' => $description,
                            'est_unit_cost' => $unCost,
                            'est_total_cost' => $totCost
                        ]
                    );
                }
            }

            $pr->save();

            $sig = DB::table('purchase_requests as pr')
                     ->join('signatories as sig', 'sig.id', '=', 'pr.sig_app')
                     ->where('sig.id', 53)
                     ->first();

            $this->logTrackerHistory($pr->code, Auth::user()->emp_id, 0, '-');
            $this->logTrackerHistory($pr->code, Auth::user()->emp_id, $sig->emp_id, 'issued');

            $logEmpMessage = "updated the purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Purchase Request $prNo successfully updated.";
            return redirect(url('procurement/pr?search=' . $prNo))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered updating the Purchase Request $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function delete($id) {
        $pr = PurchaseRequest::where('id', $id)->first();
        $prNo = $pr->pr_no;

        try {
            PurchaseRequest::where('id', $id)->delete();
            Canvass::where('pr_id', $id)->delete();
            Abstracts::where('pr_id', $id)->delete();
            PurchaseOrder::where('pr_id', $id)->delete();
            OrsBurs::where('pr_id', $id)->delete();
            InspectionAcceptance::where('pr_id', $id)->delete();
            DisbursementVoucher::where('pr_id', $id)->delete();
            InventoryStock::where('pr_id', $id)->delete();

            $logEmpMessage = "deleted the purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Purchase Request $prNo successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered deleting the Purchase Request $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }

    }


















    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showItems($id, Request $request) {
        $awardedTo = $request['awarded'];
        $toggle = $request['toggle'];
        $poNo = $request['po_no'];
        $countPO = 0;

        if ($toggle == "po") {
            $countPO = DB::table('tblpo_jo_items')
                         ->where('po_no', $poNo)
                         ->count();

            if ($countPO > 0) {
                $items = DB::table('tblpo_jo_items as po')
                           ->join('tblunit_issue AS unit', 'unit.id','=', 'po.unit_issue')
                           ->where('po.po_no', $poNo);
            } else {
                $items = DB::table('purchase_requests_items AS itm')
                           ->join('tblunit_issue AS unit', 'unit.id','=', 'itm.unit_issue')
                           ->where('itm.pr_id', $id)
                           ->where('itm.awarded_to', $awardedTo);
            }
        } else {
            $items = DB::table('purchase_requests_items AS itm')
                       ->join('tblunit_issue AS unit', 'unit.id','=', 'itm.unit_issue')
                       ->where('itm.pr_id', $id);
        }

        $items = $items->get();

        return view('pages.view-pr-items', ['prItems' => $items,
                                            'toggle' => $toggle,
                                            'countPO' => $countPO]);
    }

    public function showTrackPR($prNo) {
        $mainStatusColor = "";
        $mainStatusSymbol = "-";

        // Purchase Request
        $pr = PurchaseRequest::where('pr_no', $prNo)->first();

        if ($pr) {
            $prApprovedStatusColor = "";
            $prApprovedStatusSymbol = "";

            if (!empty($pr->date_pr_approve)) {
                $mainStatusColor = "green";
                $mainStatusSymbol = '<i class="fas fa-check"></i>';
                $prApprovedStatusColor =  $mainStatusColor;
                $prApprovedStatusSymbol = $mainStatusSymbol;
            } else {
                $mainStatusColor = "blue";
                $mainStatusSymbol = '<i class="fas fa-chevron-right"></i>';
                $prApprovedStatusColor =  "";
                $prApprovedStatusSymbol = "-";
            }

            $prTrackData = (object) ['main_status_color' => $mainStatusColor,
                                    'main_status_symbol' => $mainStatusSymbol,
                                    '_approved_status_color' => $prApprovedStatusColor,
                                    '_approved_status_symbol' => $prApprovedStatusSymbol];
            // -- Reset Variable
            $mainStatusColor = "";
            $mainStatusSymbol = "-";

            // Request for Quotation
            $rfq = Canvass::where('pr_id', $pr->id)->first();
            $rfqCode = isset($rfq->code) ? $rfq->code: '';
            $rfqDocStatus = $this->checkDocStatus($rfqCode);

            $rfqIssuedStatusColor = "";
            $rfqIssuedStatusSymbol = "-";
            $rfqReceivedStatusColor = "";
            $rfqReceivedStatusSymbol = "-";

            if (!empty($rfq) && !empty($rfqDocStatus->date_issued) &&
                !empty($rfqDocStatus->date_received)) {
                $mainStatusColor = "green";
                $mainStatusSymbol = '<i class="fas fa-check"></i>';
            } else if (!empty($rfq) && empty($rfqDocStatus->date_issued) &&
                    empty($rfqDocStatus->date_received)) {
                $mainStatusColor = "blue";
                $mainStatusSymbol = '<i class="fas fa-chevron-right"></i>';
            }

            if (!empty($rfqDocStatus->date_issued)) {
                $rfqIssuedStatusColor = "green";
                $rfqIssuedStatusSymbol = '<i class="fas fa-check"></i>';
            }

            if (!empty($rfqDocStatus->date_received)) {
                $rfqReceivedStatusColor = "green";
                $rfqReceivedStatusSymbol = '<i class="fas fa-check"></i>';
            }

            $rfqTrackData = (object) ['main_status_color' => $mainStatusColor,
                                    'main_status_symbol' => $mainStatusSymbol,
                                    '_issued_status_color' => $rfqIssuedStatusColor,
                                    '_issued_status_symbol' => $rfqIssuedStatusSymbol,
                                    '_received_status_color' => $rfqReceivedStatusColor,
                                    '_received_status_symbol' => $rfqReceivedStatusSymbol];

            // -- Reset Variable
            $mainStatusColor = "";
            $mainStatusSymbol = "-";

            // Abstract of Bids and Quotation
            $abstract = Abstracts::where('pr_id', $pr->id)->first();

            $abstractApprovedStatusColor = "";
            $abstractApprovedStatusSymbol = "-";

            if (!empty($abstract) > 0 && !empty($abstract->date_abstract_approve)) {
                $mainStatusColor = "green";
                $mainStatusSymbol = '<i class="fas fa-check"></i>';
                $abstractApprovedStatusColor = "green";
                $abstractApprovedStatusSymbol = '<i class="fas fa-check"></i>';
            } else if (!empty($abstract) > 0 && empty($abstract->date_abstract_approve)) {
                $mainStatusColor = "blue";
                $mainStatusSymbol = '<i class="fas fa-chevron-right"></i>';
                $abstractApprovedStatusColor = "";
                $abstractApprovedStatusSymbol = '-';
            }

            $abstractTrackData = (object) ['main_status_color' => $mainStatusColor,
                                        'main_status_symbol' => $mainStatusSymbol,
                                        '_approved_status_color' => $abstractApprovedStatusColor,
                                        '_approved_status_symbol' => $abstractApprovedStatusSymbol];

            // -- Reset Variable
            $mainStatusColor = "";
            $mainStatusSymbol = "-";

            // Purchase/Job Order
            $po = DB::table('tblpo_jo')->where('pr_id', $pr->id)
                                       ->get();
            $poCount = $po->count();
            $poCountComplete = 0;

            foreach ($po as $dat) {
                // Individual Purchase/Job Order
                $poCode = isset($dat->code) ? $dat->code: '';
                $poDocStatus = $this->checkDocStatus($poCode);

                $poStatusColor = "blue";
                $poStatusSymbol = '<i class="fas fa-chevron-right"></i>';
                $_poSignedStatusColor = "";
                $_poSignedStatusSymbol = "-";
                $_poApprovedStatusColor = "";
                $_poApprovedStatusSymbol = "-";
                $_poIssuedStatusColor = "";
                $_poIssuedStatusSymbol = "-";
                $_poReceivedStatusColor = "";
                $_poReceivedStatusSymbol = "-";

                if (!empty($dat->date_accountant_signed)) {
                    $_poSignedStatusColor = "green";
                    $_poSignedStatusSymbol = '<i class="fas fa-check"></i>';
                }

                if (!empty($dat->date_po_approved)) {
                    $_poApprovedStatusColor = "green";
                    $_poApprovedStatusSymbol = '<i class="fas fa-check"></i>';
                }

                if (!empty($poDocStatus->date_issued)) {
                    $_poIssuedStatusColor = "green";
                    $_poIssuedStatusSymbol = '<i class="fas fa-check"></i>';
                }

                if (!empty($poDocStatus->date_issued)) {
                    $_poReceivedStatusColor = "green";
                    $_poReceivedStatusSymbol = '<i class="fas fa-check"></i>';
                }

                // Obligation / Budget Utilization and Request Status
                $ors = OrsBurs::where('po_no', $dat->po_no)->first();
                $orsID = isset($ors->id) ? $ors->id: '';
                $orsCode = isset($ors->code) ? $ors->code: '';
                $orsDocStatus = $this->checkDocStatus($orsCode);

                $orsStatusColor = "";
                $orsStatusSymbol = "-";
                $_orsIssuedStatusColor = "";
                $_orsIssuedStatusSymbol = "-";
                $_orsReceivedStatusColor = "";
                $_orsReceivedStatusSymbol = "-";
                $_orsObligatedStatusColor = "";
                $_orsObligatedStatusSymbol = "-";

                if (!empty($ors)) {
                    if (!empty($orsDocStatus->date_issued) && !empty($orsDocStatus->date_received) &&
                        !empty($ors->date_obligated)) {
                        $orsStatusColor = "green";
                        $orsStatusSymbol = '<i class="fas fa-check"></i>';
                    } else {
                        $orsStatusColor = "blue";
                        $orsStatusSymbol = '<i class="fas fa-chevron-right"></i>';
                    }

                    if (!empty($orsDocStatus->date_issued)) {
                        $_orsIssuedStatusColor = "green";
                        $_orsIssuedStatusSymbol = '<i class="fas fa-check"></i>';
                    }

                    if (!empty($orsDocStatus->date_received)) {
                        $_orsReceivedStatusColor = "green";
                        $_orsReceivedStatusSymbol = '<i class="fas fa-check"></i>';
                    }

                    if (!empty($ors->date_obligated)) {
                        $_orsObligatedStatusColor = "green";
                        $_orsObligatedStatusSymbol = '<i class="fas fa-check"></i>';
                    }
                }

                // Inspection and Acceptance Report
                $iar = InspectionAcceptance::where('ors_id', $orsID)->first();
                $inventoryCount = InventoryStock::where('po_no', $dat->po_no)->count();
                $iarCode = isset($iar->code) ? $iar->code: '';
                $iarDocStatus = $this->checkDocStatus($iarCode);

                $iarStatusColor = "";
                $iarStatusSymbol = "-";
                $_iarIssuedStatusColor = "";
                $_iarIssuedStatusSymbol = "-";
                $_iarInspectedStatusColor = "";
                $_iarInspectedStatusSymbol = "-";
                $_iarIssuedInventoryStatusColor = "";
                $_iarIssuedInventoryStatusSymbol = "-";

                if (!empty($iar)) {
                    if (!empty($iarDocStatus->date_issued) && !empty($iarDocStatus->date_received) &&
                        $inventoryCount > 0) {
                        $iarStatusColor = "green";
                        $iarStatusSymbol = '<i class="fas fa-check"></i>';
                    } else {
                        $iarStatusColor = "blue";
                        $iarStatusSymbol = '<i class="fas fa-chevron-right"></i>';
                    }

                    if (!empty($iarDocStatus->date_issued)) {
                        $_iarIssuedStatusColor = "green";
                        $_iarIssuedStatusSymbol = '<i class="fas fa-check"></i>';
                    }

                    if (!empty($iarDocStatus->date_received)) {
                        $_iarInspectedStatusColor = "green";
                        $_iarInspectedStatusSymbol = '<i class="fas fa-check"></i>';
                    }

                    if ($inventoryCount > 0) {
                        $_iarIssuedInventoryStatusColor = "green";
                        $_iarIssuedInventoryStatusSymbol = '<i class="fas fa-check"></i>';
                    }
                }

                // Disbursement Voucher
                $dv = DisbursementVoucher::where('ors_id', $orsID)->first();
                $dvCode = isset($dv->code) ? $dv->code: '';
                $dvDocStatus = $this->checkDocStatus($dvCode);

                $dvStatusColor = "";
                $dvStatusSymbol = "-";
                $_dvIssuedStatusColor = "";
                $_dvIssuedStatusSymbol = "-";
                $_dvReceivedStatusColor = "";
                $_dvReceivedStatusSymbol = "-";
                $_dvDisbursedStatusColor = "";
                $_dvDisbursedStatusSymbol = "-";

                if (!empty($dv)) {
                    if (!empty($dvDocStatus->date_issued) && !empty($dvDocStatus->date_received) &&
                        !empty($dv->date_disbursed)) {
                        $dvStatusColor = "green";
                        $dvStatusSymbol = '<i class="fas fa-check"></i>';
                    } else {
                        $dvStatusColor = "blue";
                        $dvStatusSymbol = '<i class="fas fa-chevron-right"></i>';
                    }

                    if (!empty($dvDocStatus->date_issued)) {
                        $_dvIssuedStatusColor = "green";
                        $_dvIssuedStatusSymbol = '<i class="fas fa-check"></i>';
                    }

                    if (!empty($dvDocStatus->date_received)) {
                        $_dvReceivedStatusColor = "green";
                        $_dvReceivedStatusSymbol = '<i class="fas fa-check"></i>';
                    }

                    if (!empty($dv->date_disbursed)) {
                        $_dvDisbursedStatusColor = "green";
                        $_dvDisbursedStatusSymbol = '<i class="fas fa-check"></i>';
                    }
                }

                // Continuation of individual Purchase/Job Order
                if ($_poApprovedStatusColor == "green" && $_poIssuedStatusColor == "green" &&
                    $_poReceivedStatusColor == "green" && $orsStatusColor == "green" &&
                    $iarStatusColor == "green" && $dvStatusColor == "green") {
                    $poStatusColor = "green";
                    $poStatusSymbol = '<i class="fas fa-check"></i>';
                    $poCountComplete++;
                }

                $dat->po_status = (object) ['main_status_color' => $poStatusColor,
                                            'main_status_symbol' => $poStatusSymbol,
                                            '_signed_status_color' => $_poSignedStatusColor,
                                            '_signed_status_symbol' => $_poSignedStatusSymbol,
                                            '_approved_status_color' => $_poApprovedStatusColor,
                                            '_approved_status_symbol' => $_poApprovedStatusSymbol,
                                            '_issued_status_color' => $_poIssuedStatusColor,
                                            '_issued_status_symbol' => $_poIssuedStatusSymbol,
                                            '_received_status_color' => $_poReceivedStatusColor,
                                            '_received_status_symbol' => $_poReceivedStatusSymbol];
                $dat->ors_status = (object) ['main_status_color' => $orsStatusColor,
                                            'main_status_symbol' => $orsStatusSymbol,
                                            '_issued_status_color' => $_orsIssuedStatusColor,
                                            '_issued_status_symbol' => $_orsIssuedStatusSymbol,
                                            '_received_status_color' => $_orsReceivedStatusColor,
                                            '_received_status_symbol' => $_orsReceivedStatusSymbol,
                                            '_obligated_status_color' => $_orsObligatedStatusColor,
                                            '_obligated_status_symbol' => $_orsObligatedStatusSymbol];
                $dat->iar_status = (object) ['main_status_color' => $iarStatusColor,
                                            'main_status_symbol' => $iarStatusSymbol,
                                            '_issued_status_color' => $_iarIssuedStatusColor,
                                            '_issued_status_symbol' => $_iarIssuedStatusSymbol,
                                            '_inspected_status_color' => $_iarInspectedStatusColor,
                                            '_inspected_status_symbol' => $_iarInspectedStatusSymbol,
                                            '_issued_inventory_status_color' => $_iarIssuedInventoryStatusColor,
                                            '_issued_inventory_status_symbol' => $_iarIssuedInventoryStatusSymbol];
                $dat->dv_status = (object) ['main_status_color' => $dvStatusColor,
                                            'main_status_symbol' => $dvStatusSymbol,
                                            '_issued_status_color' => $_dvIssuedStatusColor,
                                            '_issued_status_symbol' => $_dvIssuedStatusSymbol,
                                            '_received_status_color' => $_dvReceivedStatusColor,
                                            '_received_status_symbol' => $_dvReceivedStatusSymbol,
                                            '_disbursed_status_color' => $_dvDisbursedStatusColor,
                                            '_disbursed_status_symbol' => $_dvDisbursedStatusSymbol];
            }

            if ($poCount == $poCountComplete && $poCount > 0) {
                $mainStatusColor = "green";
                $mainStatusSymbol = '<i class="fas fa-check"></i>';
            } else if ($poCount != $poCountComplete && $poCount > 0) {
                $mainStatusColor = "blue";
                $mainStatusSymbol = '<i class="fas fa-chevron-right"></i>';
            }

            $mainPOTrackData = (object) ['main_status_color' => $mainStatusColor,
                                        'main_status_symbol' => $mainStatusSymbol];

            return view('pages.pr-tracker', ['prNo' => $prNo,
                                            'isPrDisapproved' => !empty($pr->date_pr_disapprove) ? true: false,
                                            'isPrCancelled' => !empty($pr->date_pr_cancel) ? true: false,
                                            'prTrackData' => $prTrackData,
                                            'rfqTrackData' => $rfqTrackData,
                                            'abstractTrackData' => $abstractTrackData,
                                            'mainPOTrackData' => $mainPOTrackData,
                                            'po' => $po]);
        } else {
            return "No data found.";
        }
    }

    public function approve($id) {
        try {
            $pr = PurchaseRequest::where('id', $id)->first();
            $pr->date_pr_approve = Carbon::now();
            $pr->status = 5;
            $pr->save();

            $prNo = $pr->pr_no;
            $canvass = Canvass::where('pr_id', $id)->first();

            if (!$canvass) {
                $code = $this->generateTrackerCode('rfq', $id, 3);
                $canvass = new Canvass;
                $canvass->pr_id = $id;
                $canvass->code = $code;
                $canvass->save();

                $code = $pr->code;
                $this->logTrackerHistory($code, Auth::user()->emp_id, 0, 'received');
            }

            $this->notifyApproved($prNo, $pr->requested_by);

            $logEmpMessage = "approved the purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Purchase Request $prNo is now approved.";
            return redirect(url('procurement/pr?search=' . $prNo))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered while approving the Purchase Request $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }

    }

    public function disapprove($id) {
        try {
            $pr = PurchaseRequest::where('id', $id)->first();
            $pr->date_pr_disapprove = Carbon::now();
            $pr->status = 2;
            $pr->save();

            $prNo = $pr->pr_no;
            $code = $pr->code;
            $this->logTrackerHistory($code, Auth::user()->emp_id, 0, '-');

            $this->notifyDisapproved($prNo, $pr->requested_by);

            $logEmpMessage = "disapproved the purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Purchase request $prNo is now disapproved.";
            return redirect(url('procurement/pr?search=' . $prNo))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered while disapproving the Purchase Request $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function cancel($id) {
        try {
            $pr = PurchaseRequest::where('id', $id)->first();
            $pr->date_pr_cancel = Carbon::now();
            $pr->status = 3;
            $pr->save();

            $prNo = $pr->pr_no;
            $code = $pr->code;
            $this->logTrackerHistory($code, Auth::user()->emp_id, 0, '-');

            $this->notifyCancelled($prNo, $pr->requested_by);

            $logEmpMessage = "cancelled the purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $msg = "Purchase request $prNo is now cancelled.";
            return redirect(url('procurement/pr?search=' . $prNo))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered while disapproving the Purchase Request $prNo.";
            return redirect(url()->previous())->with('failed', $msg);
        }

    }

    private function notifyForApproval($prNo, $requestedBy) {
        $users = User::whereIn('role', [1, 2])
                     ->where('active', 'y')
                     ->get();

        foreach ($users as $user) {
            $userData = User::where('emp_id', $requestedBy)
                            ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                            ->first();
            $msgNotif = "<strong>".$userData->name."</strong> created a new
                         <strong>Purchase Request<br>" . $prNo . "</strong>";
            $data = (object) ['pr_no' => $prNo,
                              'msg' => $msgNotif,
                              'redirect' => 'procurement/pr?search=' . $prNo];
            $user->notify(new PurchaseReqAction($data));
        }
    }

    private function notifyApproved($prNo, $requestedBy) {
        $user = User::where('emp_id', $requestedBy)
                    ->first();
        $msgNotif = "Your <strong>Purchase Request " . $prNo . "</strong> is now <br>
                     <strong>Approved</strong>.";
        $data = (object) ['pr_no' => $prNo,
                          'msg' => $msgNotif,
                          'redirect' => 'procurement/pr?search=' . $prNo];
        $user->notify(new PurchaseReqAction($data));
    }

    private function notifyDisapproved($prNo, $requestedBy) {
        $user = User::where('emp_id', $requestedBy)
                    ->first();
        $msgNotif = "Your <strong>Purchase Request " . $prNo . "</strong> has been <br>
                     <strong>Disapproved</strong>.";
        $data = (object) ['pr_no' => $prNo,
                          'msg' => $msgNotif,
                          'redirect' => 'procurement/pr?search=' . $prNo];
        $user->notify(new PurchaseReqAction($data));
    }

    private function notifyCancelled($prNo, $requestedBy) {
        $user = User::where('emp_id', $requestedBy)
                    ->first();
        $msgNotif = "Your <strong>Purchase Request " . $prNo . "</strong> has been <br>
                     Cancelled.";
        $data = (object) ['pr_no' => $prNo,
                          'msg' => $msgNotif,
                          'redirect' => 'procurement/pr?search=' . $prNo];
        $user->notify(new PurchaseReqAction($data));
    }
}
