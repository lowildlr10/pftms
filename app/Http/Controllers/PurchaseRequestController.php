<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseRequest;
use App\Canvass;
use App\Abstracts;
use App\PurchaseOrder;
use App\OrsBurs;
use App\InspectionAcceptance;
use App\DisbursementVoucher;
use App\InventoryStock;

use App\User;
use App\Division;
use App\UnitIssue;
use App\Projects;
use App\EmployeeLog;
use App\DocumentLogHistory;
use App\PaperSize;
use Carbon\Carbon;
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
    public function index(Request $request)
    {
        $pageLimit = 25;
        $search = trim($request['search']);
        $filter = $request['filter'];
        $paperSizes = PaperSize::all();

        $prList = $this->getMainData($pageLimit, $search, $filter);

        return view('pages.pr', ['search' => $search,
                                 'filter' => $filter,
                                 'list' => $prList,
                                 'pageLimit' => $pageLimit,
                                 'paperSizes' => $paperSizes]);
    }

    private function getMainData($pageLimit = 50, $search = "", $filter = "") {
        $data = [];
        $prData = DB::table('tblpr AS pr')
                  ->select('pr.*', 'status.status','proj.project', 'status.id AS sID',
                            DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                  ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'pr.requested_by')
                  ->join('tblpr_status AS status', 'status.id', '=', 'pr.status')
                  ->leftJoin('tblprojects AS proj', 'proj.id', '=', 'pr.project_id')
                  ->whereNull('pr.deleted_at');

        if (!empty($search)) {
            $prData = $prData->where(function ($query) use ($search) {
                              $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                    ->orWhere('pr.date_pr', 'LIKE', '%' . $search . '%')
                                    ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                    ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                    ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                    ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%');
                          });
        }

        if (!empty($filter) && $filter != 0) {
            $prData = $prData->where('status.id', '=', $filter);
        }

        if (Auth::user()->role == 3 || Auth::user()->role == 4 || Auth::user()->role == 6) {
            $prData = $prData->where('requested_by', Auth::user()->emp_id);
        }

        if (Auth::user()->role == 5) {
            $prData = $prData->where('emp.division_id', Auth::user()->division_id);
        }

        $prData = $prData->orderBy('pr.id', 'desc')
                         ->paginate($pageLimit);

        return $prData;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $itemNo = 0;
        $toggle = "create";
        $unitIssue = UnitIssue::all();
        $projects = Projects::all();
        $division = Division::all();
        $approvedBy = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.pr_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.p_req', 'y'],
                                  ['sig.active', 'y']])
                         ->orderBy('emp.firstname')
                         ->get();

        if (Auth::user()->role == 1 || Auth::user()->role == 2 || Auth::user()->role == 5) {
            $requestedBy = User::orderBy('firstname')->get();
        } else {
            $requestedBy = User::where('emp_id', Auth::user()->emp_id)->get();
        }

        return view('pages.create-edit-pr', ['requestedBy' => $requestedBy,
                                             'approvedBy' => $approvedBy,
                                             'unitIssue' => $unitIssue,
                                             'projects' => $projects,
                                             'divisions' => $division,
                                             'itemNo' => $itemNo,
                                             'toggle' => $toggle,
                                             'pr' => (object)['status' => 1]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
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
                $items = DB::table('tblpr_items AS itm')
                           ->join('tblunit_issue AS unit', 'unit.id','=', 'itm.unit_issue')
                           ->where('itm.pr_id', $id)
                           ->where('itm.awarded_to', $awardedTo);
            }
        } else {
            $items = DB::table('tblpr_items AS itm')
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $itemNo = 0;
        $toggle = "edit";
        $unitIssue = UnitIssue::all();
        $projects = Projects::all();
        $division = Division::all();
        $approvedBy = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.pr_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'))
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->orderBy('emp.firstname')
                         ->where([['sig.p_req', 'y'],
                                  ['sig.active', 'y']])
                         ->get();
        $pr = DB::table('tblpr')
                ->where('id', $id)
                ->first();
        $prItems = DB::table('tblpr_items')
                     ->where('pr_id', $id)
                     ->orderByRaw('LENGTH(item_id)')
                     ->orderBy('item_id')
                     ->get();

        if (Auth::user()->role == 1 || Auth::user()->role == 2 || Auth::user()->role == 5) {
            $requestedBy = User::orderBy('firstname')->get();
        } else {
            $requestedBy = User::where('emp_id', Auth::user()->emp_id)->get();
        }

        return view('pages.create-edit-pr', ['requestedBy' => $requestedBy,
                                             'approvedBy' => $approvedBy,
                                             'unitIssue' => $unitIssue,
                                             'projects' => $projects,
                                             'divisions' => $division,
                                             'itemNo' => $itemNo,
                                             'id' => $id,
                                             'toggle' => $toggle,
                                             'pr' => $pr,
                                             'prItems' => $prItems]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pr = new PurchaseRequest;

        // To tblpr
        $prDate = $request['date_pr'];
        $prNo = $request['pr_no'];
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

        // To tblpr_items
        $unitIssue = $request['unit'];
        $itemDescription = $request['item_description'];
        $quantity = $request['quantity'];
        $unitCost = $request['unit_cost'];
        $totalCost = $request['total_cost'];

        // Auto Generate pr_no if empty
        $prSequence = DB::table('tblpr')->select('id', 'pr_no')
                                         ->orderBy('id')
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

        try {
            // Saving data to tblpr
            $pr->date_pr = $prDate;
            $pr->pr_no = $prNo;
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
            $pr->code = $this->generateTrackerCode('pr', $prNo, 3);
            $pr->save();

            // Saving pr items to tblpr_items
            $getPR_ID = DB::table('tblpr')
                          ->select('id')
                          ->where('pr_no', '=', $prNo)
                          ->first();
            $prID = $getPR_ID->id;

            foreach ($unitIssue as $arrayKey => $unit) {
                $itemID = $prID . "-" . ($arrayKey + 1);
                $unit = $unitIssue[$arrayKey];
                $description = $itemDescription[$arrayKey];
                $qnty = $quantity[$arrayKey];
                $unCost = $unitCost[$arrayKey];
                $totCost =  $qnty * $unCost;

                DB::table('tblpr_items')->insert(
                    ['item_id' => $itemID,
                     'pr_id' => $prID,
                     'quantity' => $qnty,
                     'unit_issue' => $unit,
                     'item_description' => $description,
                     'est_unit_cost' => $unCost,
                     'est_total_cost' => $totCost]
                );
            }

            $this->notifyForApproval($prNo, $requestedBy);

            $logEmpMessage = "created a new purchase request $prNo.";
            $this->logEmployeeHistory($logEmpMessage);

            $this->logTrackerHistory($pr->code, Auth::user()->emp_id, 0, 'issued');

            $msg = "New Purchase Request $prNo successfully added.";
            return redirect(url('procurement/pr?search=' . $prNo))->with('success', $msg);
        } catch (Exception $e) {
            $msg = "There is an error encountered adding the new Purchase Request $prNo.";
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
                        DB::table('tblpr_items')->where('pr_id', $id)->delete();
                    }

                    DB::table('tblpr_items')->insert(
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

                    DB::table('tblpr_items')
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

            $sig = DB::table('tblpr as pr')
                     ->join('tblsignatories as sig', 'sig.id', '=', 'pr.sig_app')
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

    private function getEmployeeName($empID) {
        $employee = DB::table('tblemp_accounts')
                      ->where('emp_id', $empID)
                      ->first();
        $fullname = "";

        if ($employee) {
            if (!empty($employee->middlename)) {
                $fullname = $employee->firstname . " " . $employee->middlename[0] . ". " .
                            $employee->lastname;
            } else {
                $fullname = $employee->firstname . " " . $employee->lastname;
            }

            $fullname = strtoupper($fullname);
        }

        return $fullname;
    }

    private function checkDocStatus($code) {
        $logs = DB::table('tbldocument_logs_history')
                 ->where('code', $code)
                 ->orderBy('created_at', 'desc')
                 ->get();
        $currentStatus = (object) ["issued_by" => NULL,
                                    "issued_to" => NULL,
                                    "date_issued" => NULL,
                                    "received_by" => NULL,
                                    "date_received" => NULL,
                                    "issued_back_by" => NULL,
                                    "date_issued_back" => NULL,
                                    "received_back_by" => NULL,
                                    "date_received_back" => NULL,
                                    "issued_remarks" => NULL,
                                    "issued_back_remarks" => NULL,
                                    "issued_remarks" => NULL,
                                    "issued_back_remarks" => NULL];

        if (count($logs) > 0) {
            foreach ($logs as $log) {
                if ($log->action != "-") {
                    switch ($log->action) {
                        case 'issued':
                            $currentStatus->issued_remarks = $log->remarks;
                            $currentStatus->issued_by = $this->getEmployeeName($log->emp_from);
                            $currentStatus->issued_to = $this->getEmployeeName($log->emp_to);
                            $currentStatus->date_issued = $log->date;
                            $currentStatus->remarks = $log->remarks;
                            break;

                        case 'received':
                            $currentStatus->received_by = $this->getEmployeeName($log->emp_from);
                            $currentStatus->date_received = $log->date;
                            break;

                        case 'issued_back':
                            $currentStatus->issued_back_remarks = $log->remarks;
                            $currentStatus->issued_back_by = $this->getEmployeeName($log->emp_from);
                            $currentStatus->date_issued_back = $log->date;
                            $currentStatus->remarks = $log->remarks;
                            break;

                        case 'received_back':
                            $currentStatus->received_back_by = $this->getEmployeeName($log->emp_from);
                            $currentStatus->date_received_back = $log->date;
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


    // For debugging purposes
    public function tableUpdate() {
        $po = DB::table('tblpo_jo')->get();

        foreach ($po as $dat) {
            $poNo = $dat->po_no;
            $dateCleared = $dat->date_po_approved;
            if (!empty($dateCleared)) {
                DB::table('tblpo_jo')
                  ->where('po_no', $poNo)
                  ->update(['date_accountant_signed' => $dateCleared]);
            }
        }

        /*
        $pr = PurchaseRequest::all();
        $canvass = Canvass::all();
        $abstract = Abstracts::all();
        $po = DB::table('tblpo_jo')->get();
        $ors = OrsBurs::all();
        $iar = DB::table('tbliar')->get();
        $dv = DisbursementVoucher::all();
        $inv = InventoryStock::all();

        $tableArray = ['PR' => $pr,
                       'RFQ' => $canvass,
                       'ABSTRACT' => $abstract,
                       'PO-JO' => $po,
                       'ORS-BURS' => $ors,
                       'IAR' => $iar,
                       'DV' => $dv,
                       'STOCK' => $inv];

        foreach ($tableArray as $key => $table) {
            foreach ($table as $data) {
                switch ($key) {
                    case 'PR':
                        $primaryID = $data->id;
                        $documentType = $key;
                        break;

                    case 'RFQ':
                        $primaryID = $data->pr_id;
                        $documentType = $key;
                        break;

                    case 'ABSTRACT':
                        $primaryID = $data->pr_id;
                        $documentType = $key;
                        break;

                    case 'PO-JO':
                        $primaryID = $data->po_no;
                        $documentType = $data->document_abrv;
                        $data = PurchaseOrder::where('po_no', $primaryID)
                                             ->first();
                        break;

                    case 'ORS-BURS':
                        $primaryID = $data->id;
                        $documentType = $data->document_type;
                        break;

                    case 'IAR':
                        $primaryID = $data->iar_no;
                        $documentType = $key;
                        $data = InspectionAcceptance::where('iar_no', $primaryID)
                                                    ->first();
                        break;

                    case 'DV':
                        $primaryID = $data->id;
                        $documentType = $key;
                        break;

                    case 'STOCK':
                        $primaryID = $data->id;
                        $documentType = $key;
                        break;

                    default:
                        # code...
                        break;
                }

                $doc = DB::table('tbldocument_logs')
                         ->select('code', 'created_at')
                         ->where('primary_id', $primaryID)
                         ->where('document_type', $documentType)
                         ->first();

                if ($doc) {
                    if (empty($data->created_at)) {
                        $data->created_at = $doc->created_at;
                    }

                    if ($key != "STOCK") {
                        $data->code = $doc->code;
                        $data->save();
                    }
                }

                if ($key == 'STOCK') {
                    if (empty($data->code)) {
                        $data->code = $this->generateTrackerCode($key, $data->inventory_no, 5);
                        $data->save();
                    }
                }
             }
        }*/
    }
}
