<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use DateTime;
use App\Models\ItemUnitIssue;
use App\Models\Signatory;
use App\Models\InventoryStockIssue;
use App\Models\InventoryStockIssueItem;
use App\Models\DocumentLog as DocLog;
use Response;
use Auth;

class VoucherLogController extends Controller
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
    public function index($toggle)
    {
        //$paperSizes = PaperSize::all();

        return view('modules.voucher-tracking.index', [//'paperSizes' => $paperSizes,
            'toggle' => $toggle,
            'type' => ''
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $toggle)
    {
        ini_set('max_execution_time', 600);
        $dateFrom = $request['date_from'];
        $dateTo = $request['date_to'];
        $search = trim($request['search']);

        switch ($toggle) {
            case 'pr-rfq':
                $data = $this->generatePR_RFQ($dateFrom, $dateTo, $search);
                $prTooltip = "Date & time of PR approved to date & time of RFQ issued.";
                $rfqTooltip = "Date & time of RFQ issued to date & time of RFQ received.";
                return view('modules.voucher-tracking.pr-rfq.index', [
                    'prRfqData' => $data,
                    'search' => $search,
                    'prTooltip' => $prTooltip,
                    'rfqTooltip' => $rfqTooltip,
                    'module' => $toggle
                ]);
                break;

            case 'rfq-abstract':
                $data = $this->generateRFQ_Abstract($dateFrom, $dateTo, $search);
                $rfqabstractTooltip = "Date & time of RFQ received to date & time of Abstract approved for PO/JO.";
                return view('modules.voucher-tracking.rfq-abstract.index', [
                    'rfqAbsData' => $data,
                    'search' => $search,
                    'rfqabstractTooltip' => $rfqabstractTooltip,
                    'module' => $toggle
                ]);
                break;

            case 'abstract-po':
                $data = $this->generateAbstract_PO($dateFrom, $dateTo, $search);
                $abstractTooltip = "Date & time of Abstract approved for PO/JO to date & time of PO/JO approved.";
                $poTooltip = "Date & time of PO/JO approved to date & time of PO/JO received.";
                return view('modules.voucher-tracking.abstract-po-jo.index', [
                    'absPoData' => $data,
                    'search' => $search,
                    'abstractTooltip' => $abstractTooltip,
                    'poTooltip' => $poTooltip,
                    'module' => $toggle
                ]);
                break;

            case 'po-ors':
                $data = $this->generatePO_ORS($dateFrom, $dateTo, $search);
                $poTooltip = "Date & time of PO/JO issued to date & time of PO/JO received.";
                $orsTooltip = "Date & time of ORS/BURS received by Budget Officer to date & time of ORS/BURS obligated.";
                return view('modules.voucher-tracking.po-jo-ors-burs.index', [
                    'poOrsData' => $data,
                    'search' => $search,
                    'poTooltip' => $poTooltip,
                    'orsTooltip' => $orsTooltip,
                    'module' => $toggle
                ]);
                break;

            case 'po-iar':
                $data = $this->generatePO_IAR($dateFrom, $dateTo, $search);
                $poTooltip = "Date & time of PO/JO issued to date & time of PO/JO received.";
                $iarTooltip = "Date & time of PO/JO received to date & time of IAR inspected.";
                return view('modules.voucher-tracking.po-jo-iar.index', [
                    'poIarData' => $data,
                    'search' => $search,
                    'poTooltip' => $poTooltip,
                    'iarTooltip' => $iarTooltip,
                    'module' => $toggle
                ]);
                break;

            case 'iar-stock':
                $data = $this->generateIAR_STOCK($dateFrom, $dateTo, $search);
                $iarTooltip = "Date & time of IAR inspected to date & time of Inventory Stock created.";
                $stockTooltip = "Date & time of Inventory Stock created to date & time of
                                 Inventory Stock issued.";
                return view('modules.voucher-tracking.iar-stocks.index', [
                    'iarStockData' => $data,
                    'search' => $search,
                    'iarTooltip' => $iarTooltip,
                    'stockTooltip' => $stockTooltip,
                    'module' => $toggle
                ]);
                break;

            case 'iar-dv':
                $data = $this->generateIAR_DV($dateFrom, $dateTo, $search);
                $iarTooltip = "Date & time of IAR inspected to date & time of DV issued.";
                $dvTooltip = "Date & time of DV issued to date & time of DV received.";
                return view('modules.voucher-tracking.iar-dv.index', [
                    'iarDvData' => $data,
                    'search' => $search,
                    'iarTooltip' => $iarTooltip,
                    'dvTooltip' => $dvTooltip,
                    'module' => $toggle
                ]);
                break;

            case 'ors-dv':
                $data = $this->generateORS_DV($dateFrom, $dateTo, $search);
                $orsTooltip = "Date & time of ORS/BURS obligated to date & time of DV received.";
                $dvTooltip = "Date & time of DV received to date & time of DV disbursed.";
                return view('modules.voucher-tracking.ors-burs-dv.index', [
                    'orsDvData' => $data,
                    'search' => $search,
                    'orsTooltip' => $orsTooltip,
                    'dvTooltip' => $dvTooltip,
                    'module' => $toggle
                ]);
                break;

            default:
                return '<h5 class="red-text m-3 mb-5 p-3 text-center">No available data</h5>';
                break;
        }
    }

    public function search(Request $request) {
        $keyword = trim($request->vtrack_search);
        $modules = (object) [
            'pr-rfq' => 'PR to RFQ',
            'rfq-abstract' => 'RFQ to Abstract',
            'abstract-po' => 'Abstract to PO/JO',
            'po-ors' => 'PO/JO to ORS/BURS',
            'po-iar' => 'PO/JO to IAR',
            'iar-stock' => 'IAR to PAR/RIS/ICS',
            'iar-dv' => 'IAR to DV',
            'ors-dv' => 'ORS/BURS to DV',
            'dv-lddap' => 'DV to LDDAP',
            'lddap-summary' => 'LDDAP to Summary',
            'summary-bank' => 'Summary to Bank',
        ];
        $counter = 1;

        return view('modules.voucher-tracking.search', compact(
            'keyword', 'modules', 'counter'
        ));
    }

    public function getSearch(Request $request) {
        ini_set('max_execution_time', 1800);
        $dateFrom = '2016-01-01';
        $dateTo = date('Y-m-d');
        $keyword = $request->keyword;
        $module = $request->module;

        switch ($module) {
            case 'pr-rfq':
                $prRfqData = $this->generatePR_RFQ($dateFrom, $dateTo, $keyword, true);
                $prTooltip = "Date & time of PR approved to date & time of RFQ issued.";
                $rfqTooltip = "Date & time of RFQ issued to date & time of RFQ received.";

                return view('modules.voucher-tracking.pr-rfq.index', [
                    'prRfqData' => $prRfqData,
                    'search' => $keyword,
                    'prTooltip' => $prTooltip,
                    'rfqTooltip' => $rfqTooltip,
                    'module' => $module
                ]);
                break;

            case 'rfq-abstract':
                $data = $this->generateRFQ_Abstract($dateFrom, $dateTo, $keyword, true);
                $rfqabstractTooltip = "Date & time of RFQ received to date & time of Abstract approved for PO/JO.";
                return view('modules.voucher-tracking.rfq-abstract.index', [
                    'rfqAbsData' => $data,
                    'search' => $keyword,
                    'rfqabstractTooltip' => $rfqabstractTooltip,
                    'module' => $module
                ]);
                break;

            case 'abstract-po':
                $data = $this->generateAbstract_PO($dateFrom, $dateTo, $keyword, true);
                $abstractTooltip = "Date & time of Abstract approved for PO/JO to date & time of PO/JO approved.";
                $poTooltip = "Date & time of PO/JO approved to date & time of PO/JO received.";
                return view('modules.voucher-tracking.abstract-po-jo.index', [
                    'absPoData' => $data,
                    'search' => $keyword,
                    'abstractTooltip' => $abstractTooltip,
                    'poTooltip' => $poTooltip,
                    'module' => $module
                ]);
                break;

            case 'po-ors':
                $data = $this->generatePO_ORS($dateFrom, $dateTo, $keyword, true);
                $poTooltip = "Date & time of PO/JO issued to date & time of PO/JO received.";
                $orsTooltip = "Date & time of ORS/BURS received by Budget Officer to date & time of ORS/BURS obligated.";
                return view('modules.voucher-tracking.po-jo-ors-burs.index', [
                    'poOrsData' => $data,
                    'search' => $keyword,
                    'poTooltip' => $poTooltip,
                    'orsTooltip' => $orsTooltip,
                    'module' => $module
                ]);
                break;

            case 'po-iar':
                $data = $this->generatePO_IAR($dateFrom, $dateTo, $keyword, true);
                $poTooltip = "Date & time of PO/JO issued to date & time of PO/JO received.";
                $iarTooltip = "Date & time of PO/JO received to date & time of IAR inspected.";
                return view('modules.voucher-tracking.po-jo-iar.index', [
                    'poIarData' => $data,
                    'search' => $keyword,
                    'poTooltip' => $poTooltip,
                    'iarTooltip' => $iarTooltip,
                    'module' => $module
                ]);
                break;

            case 'iar-stock':
                $data = $this->generateIAR_STOCK($dateFrom, $dateTo, $keyword, true);
                $iarTooltip = "Date & time of IAR inspected to date & time of Inventory Stock created.";
                $stockTooltip = "Date & time of Inventory Stock created to date & time of
                                 Inventory Stock issued.";
                return view('modules.voucher-tracking.iar-stocks.index', [
                    'iarStockData' => $data,
                    'search' => $keyword,
                    'iarTooltip' => $iarTooltip,
                    'stockTooltip' => $stockTooltip,
                    'module' => $module
                ]);
                break;

            case 'iar-dv':
                $data = $this->generateIAR_DV($dateFrom, $dateTo, $keyword, true);
                $iarTooltip = "Date & time of IAR inspected to date & time of DV issued.";
                $dvTooltip = "Date & time of DV issued to date & time of DV received.";
                return view('modules.voucher-tracking.iar-dv.index', [
                    'iarDvData' => $data,
                    'search' => $keyword,
                    'iarTooltip' => $iarTooltip,
                    'dvTooltip' => $dvTooltip,
                    'module' => $module
                ]);
                break;

            case 'ors-dv':
                $data = $this->generateORS_DV($dateFrom, $dateTo, $keyword, true);
                $orsTooltip = "Date & time of ORS/BURS obligated to date & time of DV received.";
                $dvTooltip = "Date & time of DV received to date & time of DV disbursed.";
                return view('modules.voucher-tracking.ors-burs-dv.index', [
                    'orsDvData' => $data,
                    'search' => $keyword,
                    'orsTooltip' => $orsTooltip,
                    'dvTooltip' => $dvTooltip,
                    'module' => $module
                ]);
                break;

            default:
                return '<h5 class="red-text m-3 mb-5 p-3 text-center">No available data</h5>';
                break;
        }
    }

    private function computeDateRange($dateFrom, $dateTo, $debugValue = "") {
        $rangeValue = "N/a";

        if (!empty($dateFrom)) {
            if (empty($dateTo)) {
                $dateTo = date('Y-m-d H:i:s');
            }

            $datetime1 = new DateTime($dateFrom);
            $datetime2 = new DateTime($dateTo);
            $interval = $datetime1->diff($datetime2);

            $year = (int) $interval->format("%y");
            $month = (int) $interval->format("%m");
            $day = (int) $interval->format("%d");
            $hour = $interval->format("%H:%I");

            // For Debugging
            /*
            if ($debugValue == 0) {
                dd($dateFrom);
            }
            */

            if ($year == 0) {
                $year = NULL;
            } else {
                if ($year > 1) {
                    $year = $year . "yrs ";
                } else {
                    $year = $year . "yr ";
                }

            }

            if ($month == 0) {
                $month = NULL;
            } else {
                if ($month > 1) {
                    $month = $month . "mos ";
                } else {
                    $month = $month . "mo ";
                }
            }

            if ($day == 0) {
                $day = NULL;
            } else {
                if ($day > 1) {
                    $day = $day . "days ";
                } else {
                    $day = $day . "day ";
                }
            }

            if ((int) $hour > 1) {
                $hour = $hour . "hrs";
            } else {
                $hour = $hour . "hr";
            }

            //$days = $interval->format("%m mo/s %d day/s &  %H:%I hr/s");

            if (!$interval->invert) {
                $rangeValue = $year . $month . $day . $hour;
            } else {
                $rangeValue = "DateTime(From) should be lesser than DateTime(To)";
            }

        }

        return $rangeValue;
    }

    private function getEmpRoles() {
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();
        $roleHasAdministrator = Auth::user()->hasOrdinaryRole();
        $roleHasRD = Auth::user()->hasRdRole();
        $roleHasARD = Auth::user()->hasArdRole();
        $roleHasPSTD = Auth::user()->hasPstdRole();
        $roleHasPlanning = Auth::user()->hasPlanningRole();
        $roleHasProjectStaff = Auth::user()->hasProjectStaffRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAccountant = Auth::user()->hasAccountantRole();
        $roleHasPropertySupply = Auth::user()->hasPropertySupplyRole();
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();

        return (object) [
            'is_developer' => $roleHasDeveloper,
            'is_adminstrator' => $roleHasAdministrator,
            'is_rd' => $roleHasRD,
            'is_ard' => $roleHasARD,
            'is_pstd' => $roleHasPSTD,
            'is_planning' => $roleHasPlanning,
            'is_proj_staff' => $roleHasProjectStaff,
            'is_budget' => $roleHasBudget,
            'is_accountant' => $roleHasAccountant,
            'is_dpso' => $roleHasPropertySupply,
            'is_ordinary' => $roleHasOrdinary,
        ];
    }

    private function generatePR_RFQ($dateFrom, $dateTo, $search, $isSearchAll = false) {
        $itemCountPerPage = $isSearchAll ? 20 : 100;
        $roles = $this->getEmpRoles();

        $projects = DB::table('funding_projects')
                      ->select('id')
                      ->where('project_title', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $accounts = DB::table('emp_accounts')
                      ->select('id')
                      ->where('firstname', 'like', "%$search%")
                      ->orWhere('middlename', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $divisions = DB::table('emp_divisions')
                       ->select('id')
                       ->where('division_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();

        $instanceDocLog = new DocLog;
        $data = DB::table('purchase_requests as pr')
                  ->select('pr.id as pr_code', 'rfq.id as rfq_code',
                           'pr.date_pr_approved', 'pr.pr_no', 'pr.id')
                  ->leftJoin('request_quotations as rfq', 'rfq.pr_id', '=', 'pr.id')
                  ->whereBetween(DB::raw('DATE(pr.created_at)'), array($dateFrom, $dateTo))
                  ->whereNull('pr.deleted_at');

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search, $projects, $accounts, $divisions) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_disapproved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('pr.funding_source', $projects)
                      ->orWhereIn('pr.requested_by', $accounts)
                      ->orWhereIn('pr.approved_by', $accounts)
                      ->orWhereIn('pr.sig_app', $accounts)
                      ->orWhereIn('pr.sig_funds_available', $accounts)
                      ->orWhereIn('pr.recommended_by', $accounts)
                      ->orWhereIn('pr.division', $divisions)
                      ->orWhere('pr.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('rfq.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('rfq.date_canvass', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('rfq.sig_rfq', $accounts)
                      ->orWhereIn('rfq.canvassed_by', $accounts);
            });
        }

        if ($roles->is_ordinary) {
            $data = $data->where('pr.requested_by', Auth::user()->id);
        }

        /*
        if ($roles->is_developer || $roles->is_adminstrator || $roles->is_rd ||
            $roles->is_ard || $roles->is_planning || $roles->is_dpso ||
            $roles->is_budget || $roles->is_accountant) {
            //$data = $data->data;
        } else {

        }*/

        $data = $data->orderByRaw('LENGTH(pr.pr_no)', 'desc')
                     ->orderBy('pr.pr_no', 'desc')
                     ->whereNull('pr.deleted_at')
                     ->paginate($itemCountPerPage);

        foreach ($data as $dat) {
            $prDocHistory =  $instanceDocLog->checkDocHistory($dat->pr_code);
            $rfqDocHistory =  $instanceDocLog->checkDocHistory($dat->rfq_code);
            $prDocStatus = $instanceDocLog->checkDocStatus($dat->pr_code);
            $rfqDocStatus = $instanceDocLog->checkDocStatus($dat->rfq_code);

            $dat->pr_document_history = $prDocHistory;
            $dat->rfq_document_history = $rfqDocHistory;
            $dat->pr_document_status = $prDocStatus;
            $dat->rfq_document_status = $rfqDocStatus;

            $dat->pr_range_count = $this->computeDateRange($dat->date_pr_approved,
                                                           $rfqDocStatus->date_issued);
            $dat->rfq_range_count = $this->computeDateRange($rfqDocStatus->date_issued,
                                                            $rfqDocStatus->date_received);
        }

        return $data;
    }

    private function generateRFQ_Abstract($dateFrom, $dateTo, $search, $isSearchAll = false) {
        $itemCountPerPage = $isSearchAll ? 20 : 100;
        $roles = $this->getEmpRoles();

        $projects = DB::table('funding_projects')
                      ->select('id')
                      ->where('project_title', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $accounts = DB::table('emp_accounts')
                      ->select('id')
                      ->where('firstname', 'like', "%$search%")
                      ->orWhere('middlename', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $divisions = DB::table('emp_divisions')
                       ->select('id')
                       ->where('division_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();
        $procModes = DB::table('procurement_modes')
                       ->select('id')
                       ->where('mode_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();

        $instanceDocLog = new DocLog;
        $data = DB::table('request_quotations as rfq')
                  ->select('rfq.id as rfq_code', 'abstract.id as abstract_code',
                           'abstract.date_abstract_approved', 'rfq.pr_id', 'pr.pr_no')
                  ->join('purchase_requests as pr', 'pr.id', '=', 'rfq.pr_id')
                  ->leftJoin('abstract_quotations as abstract', 'abstract.pr_id', '=', 'rfq.pr_id')
                  ->whereBetween(DB::raw('DATE(rfq.created_at)'), array($dateFrom, $dateTo))
                  ->whereNull('rfq.deleted_at');

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search, $projects, $accounts, $divisions, $procModes) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_disapproved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('pr.funding_source', $projects)
                      ->orWhereIn('pr.requested_by', $accounts)
                      ->orWhereIn('pr.approved_by', $accounts)
                      ->orWhereIn('pr.sig_app', $accounts)
                      ->orWhereIn('pr.sig_funds_available', $accounts)
                      ->orWhereIn('pr.recommended_by', $accounts)
                      ->orWhereIn('pr.division', $divisions)
                      ->orWhere('pr.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('rfq.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('rfq.date_canvass', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('rfq.sig_rfq', $accounts)
                      ->orWhereIn('rfq.canvassed_by', $accounts)
                      ->orWhere('abstract.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.date_abstract', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.date_abstract_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.date_abstract', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('abstract.mode_procurement', $procModes)
                      ->orWhereIn('abstract.sig_chairperson', $accounts)
                      ->orWhereIn('abstract.sig_vice_chairperson', $accounts)
                      ->orWhereIn('abstract.sig_first_member', $accounts)
                      ->orWhereIn('abstract.sig_second_member', $accounts)
                      ->orWhereIn('abstract.sig_third_member', $accounts)
                      ->orWhereIn('abstract.sig_end_user', $accounts);
            });
        }

        $data = $data->orderByRaw('LENGTH(rfq.id)', 'desc')
                     ->orderBy('rfq.id', 'desc')
                     ->paginate($itemCountPerPage);

        foreach ($data as $ctr => $dat) {
            $rfqDocStatus = $instanceDocLog->checkDocStatus($dat->rfq_code);
            $abstractDocStatus = $instanceDocLog->checkDocStatus($dat->abstract_code);

            $dat->rfq_document_status = $rfqDocStatus;
            $dat->abstract_document_status = $abstractDocStatus;

            $dat->abstract_range_count = $this->computeDateRange($rfqDocStatus->date_received,
                                                                 $dat->date_abstract_approved);
        }

        return $data;
    }

    private function generateAbstract_PO($dateFrom, $dateTo, $search, $isSearchAll = false) {
        $itemCountPerPage = $isSearchAll ? 20 : 100;
        $roles = $this->getEmpRoles();

        $projects = DB::table('funding_projects')
                      ->select('id')
                      ->where('project_title', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $accounts = DB::table('emp_accounts')
                      ->select('id')
                      ->where('firstname', 'like', "%$search%")
                      ->orWhere('middlename', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $divisions = DB::table('emp_divisions')
                       ->select('id')
                       ->where('division_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();
        $procModes = DB::table('procurement_modes')
                       ->select('id')
                       ->where('mode_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();
        $suppliers = DB::table('suppliers')
                       ->select('id')
                       ->where('company_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();

        $instanceDocLog = new DocLog;
        $data = DB::table('abstract_quotations as abstract')
                  ->select('abstract.id as abstract_code', 'po.id as po_code', 'pr.pr_no',
                           'abstract.date_abstract_approved', 'po.date_po_approved')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'abstract.pr_id')
                  ->leftJoin('purchase_job_orders as po', 'po.pr_id', '=', 'abstract.pr_id')
                  ->whereBetween(DB::raw('DATE(abstract.created_at)'), array($dateFrom, $dateTo))
                  ->whereNull('abstract.deleted_at');

        if (!empty($search)) {
            $data = $data->where(function ($query)
                    use ($search, $projects, $accounts, $divisions, $procModes, $suppliers) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_disapproved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('pr.funding_source', $projects)
                      ->orWhereIn('pr.requested_by', $accounts)
                      ->orWhereIn('pr.approved_by', $accounts)
                      ->orWhereIn('pr.sig_app', $accounts)
                      ->orWhereIn('pr.sig_funds_available', $accounts)
                      ->orWhereIn('pr.recommended_by', $accounts)
                      ->orWhereIn('pr.division', $divisions)
                      ->orWhere('pr.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.date_abstract', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.date_abstract_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.date_abstract', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('abstract.mode_procurement', $procModes)
                      ->orWhereIn('abstract.sig_chairperson', $accounts)
                      ->orWhereIn('abstract.sig_vice_chairperson', $accounts)
                      ->orWhereIn('abstract.sig_first_member', $accounts)
                      ->orWhereIn('abstract.sig_second_member', $accounts)
                      ->orWhereIn('abstract.sig_third_member', $accounts)
                      ->orWhereIn('abstract.sig_end_user', $accounts)
                      ->orWhere('po.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.po_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_po', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_po_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.place_delivery', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_delivery', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.delivery_term', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.payment_term', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.amount_words', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.grand_total', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.fund_cluster', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_accountant_signed', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.document_type', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('po.awarded_to', $suppliers)
                      ->orWhereIn('po.sig_department', $accounts)
                      ->orWhereIn('po.sig_approval', $accounts)
                      ->orWhereIn('po.sig_funds_available', $accounts);
            });
        }

        $data = $data->orderByRaw('LENGTH(abstract.id)', 'desc')
                     ->orderBy('abstract.id', 'desc')
                     ->paginate($itemCountPerPage);

        foreach ($data as $dat) {
            $poDocHistory =  $instanceDocLog->checkDocHistory($dat->po_code);
            $poDocStatus = $instanceDocLog->checkDocStatus($dat->po_code);

            $dat->po_document_history = $poDocHistory;
            $dat->po_document_status = $poDocStatus;

            $dat->abs_range_count = $this->computeDateRange($dat->date_abstract_approved,
                                                            $dat->date_po_approved);
            $dat->po_range_count = $this->computeDateRange($dat->date_po_approved,
                                                           $poDocStatus->date_received);
        }

        return $data;
    }

    private function generatePO_ORS($dateFrom, $dateTo, $search, $isSearchAll = false) {
        $itemCountPerPage = $isSearchAll ? 20 : 100;
        $roles = $this->getEmpRoles();

        $projects = DB::table('funding_projects')
                      ->select('id')
                      ->where('project_title', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $accounts = DB::table('emp_accounts')
                      ->select('id')
                      ->where('firstname', 'like', "%$search%")
                      ->orWhere('middlename', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $divisions = DB::table('emp_divisions')
                       ->select('id')
                       ->where('division_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();
        $suppliers = DB::table('suppliers')
                       ->select('id')
                       ->where('company_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();

        $instanceDocLog = new DocLog;
        $data = DB::table('purchase_job_orders as po')
                  ->select('po.id as po_code', 'po.po_no', 'ors.id as ors_code',
                           'po.document_type', 'po.date_po_approved', 'ors.date_obligated',
                           'po.created_at as po_created_at')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                  ->leftJoin('obligation_request_status as ors', 'ors.po_no', '=', 'po.po_no')
                  ->whereBetween(DB::raw('DATE(po.created_at)'), array($dateFrom, $dateTo))
                  ->whereNull('po.deleted_at');

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search, $projects, $accounts, $divisions, $suppliers) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_disapproved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('pr.funding_source', $projects)
                      ->orWhereIn('pr.requested_by', $accounts)
                      ->orWhereIn('pr.approved_by', $accounts)
                      ->orWhereIn('pr.sig_app', $accounts)
                      ->orWhereIn('pr.sig_funds_available', $accounts)
                      ->orWhereIn('pr.recommended_by', $accounts)
                      ->orWhereIn('pr.division', $divisions)
                      ->orWhere('pr.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.po_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_po', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_po_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.place_delivery', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_delivery', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.delivery_term', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.payment_term', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.amount_words', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.grand_total', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.fund_cluster', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_accountant_signed', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.document_type', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('po.awarded_to', $suppliers)
                      ->orWhereIn('po.sig_department', $accounts)
                      ->orWhereIn('po.sig_approval', $accounts)
                      ->orWhereIn('po.sig_funds_available', $accounts)
                      ->orWhere('ors.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.transaction_type', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.document_type', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.fund_cluster', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.serial_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.date_ors_burs', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.date_obligated', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.date_released', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.address', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.responsibility_center', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.particulars', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.mfo_pap', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.uacs_object_code', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.prior_year', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.continuing', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.current', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.amount', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('ors.payee', $accounts)
                      ->orWhereIn('ors.payee', $suppliers)
                      ->orWhereIn('ors.sig_certified_1', $accounts)
                      ->orWhereIn('ors.sig_certified_2', $accounts)
                      ->orWhereIn('ors.sig_accounting', $accounts)
                      ->orWhereIn('ors.sig_agency_head', $accounts)
                      ->orWhereIn('ors.obligated_by', $accounts)
                      ->orWhereIn('ors.funding_source', $projects);
            });
        }

        $data = $data->orderByRaw('LENGTH(po.id)', 'desc')
                     ->orderBy('po.id', 'desc')
                     ->paginate($itemCountPerPage);

        foreach ($data as $dat) {
            $poDocHistory =  $instanceDocLog->checkDocHistory($dat->po_code);
            $poDocStatus = $instanceDocLog->checkDocStatus($dat->po_code);
            $orsDocHistory =  $instanceDocLog->checkDocHistory($dat->ors_code);
            $orsDocStatus = $instanceDocLog->checkDocStatus($dat->ors_code);

            $dat->po_document_history = $poDocHistory;
            $dat->po_document_status = $poDocStatus;
            $dat->ors_document_history = $orsDocHistory;
            $dat->ors_document_status = $orsDocStatus;

            $dat->po_range_count = $this->computeDateRange($poDocStatus->date_issued,
                                                           $poDocStatus->date_received);
            $dat->ors_range_count = $this->computeDateRange($orsDocStatus->date_received,
                                                            $dat->date_obligated);
        }

        //dd(['UNDER DEVELOPMENT', $data]);

        return $data;
    }

    private function generatePO_IAR($dateFrom, $dateTo, $search, $isSearchAll = false) {
        $itemCountPerPage = $isSearchAll ? 20 : 100;
        $roles = $this->getEmpRoles();

        $projects = DB::table('funding_projects')
                      ->select('id')
                      ->where('project_title', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $accounts = DB::table('emp_accounts')
                      ->select('id')
                      ->where('firstname', 'like', "%$search%")
                      ->orWhere('middlename', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $divisions = DB::table('emp_divisions')
                       ->select('id')
                       ->where('division_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();
        $suppliers = DB::table('suppliers')
                       ->select('id')
                       ->where('company_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();

        $instanceDocLog = new DocLog;
        $data = DB::table('purchase_job_orders as po')
                  ->select('po.id as po_code', 'iar.id as iar_code', 'po.po_no',
                           'po.document_type', 'po.date_po_approved')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                  ->leftJoin('inspection_acceptance_reports as iar', 'iar.iar_no', 'LIKE', DB::Raw("CONCAT('%', po.po_no, '%')"))
                  ->whereBetween(DB::raw('DATE(po.created_at)'), array($dateFrom, $dateTo))
                  ->whereNull('po.deleted_at');

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search, $projects, $accounts, $divisions, $suppliers) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_disapproved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('pr.funding_source', $projects)
                      ->orWhereIn('pr.requested_by', $accounts)
                      ->orWhereIn('pr.approved_by', $accounts)
                      ->orWhereIn('pr.sig_app', $accounts)
                      ->orWhereIn('pr.sig_funds_available', $accounts)
                      ->orWhereIn('pr.recommended_by', $accounts)
                      ->orWhereIn('pr.division', $divisions)
                      ->orWhere('pr.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.po_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_po', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_po_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.place_delivery', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_delivery', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.delivery_term', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.payment_term', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.amount_words', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.grand_total', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.fund_cluster', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.date_accountant_signed', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.document_type', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('po.awarded_to', $suppliers)
                      ->orWhereIn('po.sig_department', $accounts)
                      ->orWhereIn('po.sig_approval', $accounts)
                      ->orWhereIn('po.sig_funds_available', $accounts)
                      ->orWhere('iar.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.iar_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.ors_id', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_iar', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.invoice_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_invoice', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_inspected', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.inspection_remarks', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_received', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.acceptance_remarks', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.specify_quantity', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.remarks_recommendation', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('iar.sig_inspection', $accounts)
                      ->orWhereIn('iar.sig_supply', $accounts);
            });
        }

        $data = $data->orderByRaw('LENGTH(po.id)', 'desc')
                     ->orderBy('po.id', 'desc')
                     ->paginate($itemCountPerPage);

        foreach ($data as $dat) {
            $poDocHistory =  $instanceDocLog->checkDocHistory($dat->po_code);
            $poDocStatus = $instanceDocLog->checkDocStatus($dat->po_code);
            $iarDocHistory =  $instanceDocLog->checkDocHistory($dat->iar_code);
            $iarDocStatus = $instanceDocLog->checkDocStatus($dat->iar_code);

            $dat->po_document_history = $poDocHistory;
            $dat->po_document_status = $poDocStatus;
            $dat->iar_document_history = $iarDocHistory;
            $dat->iar_document_status = $iarDocStatus;

            $dat->po_range_count = $this->computeDateRange($poDocStatus->date_issued,
                                                           $poDocStatus->date_received);
            $dat->iar_range_count = $this->computeDateRange($poDocStatus->date_received,
                                                            $iarDocStatus->date_received);
        }

        //dd(['UNDER DEVELOPMENT', $data]);

        return $data;
    }

    private function generateIAR_STOCK($dateFrom, $dateTo, $search, $isSearchAll = false) {
        $itemCountPerPage = $isSearchAll ? 20 : 100;
        $roles = $this->getEmpRoles();

        $projects = DB::table('funding_projects')
                      ->select('id')
                      ->where('project_title', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $accounts = DB::table('emp_accounts')
                      ->select('id')
                      ->where('firstname', 'like', "%$search%")
                      ->orWhere('middlename', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $divisions = DB::table('emp_divisions')
                       ->select('id')
                       ->where('division_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();
        $suppliers = DB::table('suppliers')
                       ->select('id')
                       ->where('company_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();
        $invClasses = DB::table('inventory_stock_classifications')
                       ->select('id')
                       ->where('classification_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();

        $instanceDocLog = new DocLog;
        $signatory = new Signatory;
        $data = DB::table('inspection_acceptance_reports as iar')
                  ->select('iar.id as iar_code', 'inv.id as inv_code',
                           'inv.created_at as inv_created_at', 'inv.inventory_no',
                           'inv.id as inv_id', 'inv.po_no',
                           'invclass.classification_name as inv_classification')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'iar.pr_id')
                  ->leftJoin('inventory_stocks as inv', 'iar.po_id', '=', 'inv.po_id')
                  ->leftJoin('item_classifications as invclass',
                             'invclass.id', '=', 'inv.inventory_classification')
                  ->whereBetween(DB::raw('DATE(iar.created_at)'), array($dateFrom, $dateTo))
                  ->whereNull('iar.deleted_at');

        if (!empty($search)) {
            $data = $data->where(function ($query)
                    use ($search, $projects, $accounts, $divisions, $suppliers, $invClasses) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_disapproved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('pr.funding_source', $projects)
                      ->orWhereIn('pr.requested_by', $accounts)
                      ->orWhereIn('pr.approved_by', $accounts)
                      ->orWhereIn('pr.sig_app', $accounts)
                      ->orWhereIn('pr.sig_funds_available', $accounts)
                      ->orWhereIn('pr.recommended_by', $accounts)
                      ->orWhereIn('pr.division', $divisions)
                      ->orWhere('pr.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.iar_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.ors_id', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_iar', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.invoice_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_invoice', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_inspected', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.inspection_remarks', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_received', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.acceptance_remarks', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.specify_quantity', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.remarks_recommendation', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('iar.sig_inspection', $accounts)
                      ->orWhereIn('iar.sig_supply', $accounts)
                      ->orWhere('inv.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('inv.entity_name', 'LIKE', '%' . $search . '%')
                      ->orWhere('inv.fund_cluster', 'LIKE', '%' . $search . '%')
                      ->orWhere('inv.inventory_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('inv.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('inv.responsibility_center', 'LIKE', '%' . $search . '%')
                      ->orWhere('inv.date_po', 'LIKE', '%' . $search . '%')
                      ->orWhere('inv.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('inv.division', $divisions)
                      ->orWhereIn('inv.supplier', $suppliers)
                      ->orWhereIn('inv.inventory_classification', $invClasses);
            });
        }

        $data = $data->orderByRaw('LENGTH(iar.id)', 'desc')
                     ->orderBy('iar.id', 'desc')
                     ->distinct()
                     ->paginate($itemCountPerPage);

        foreach ($data as $dat) {
            // IAR
            $iarDocHistory =  $instanceDocLog->checkDocHistory($dat->iar_code);
            $iarDocStatus = $instanceDocLog->checkDocStatus($dat->iar_code);

            $dat->iar_document_history = $iarDocHistory;
            $dat->iar_document_status = $iarDocStatus;

            $dat->iar_range_count = $this->computeDateRange($iarDocStatus->date_received,
                                                            $dat->inv_created_at);

            // Inventory Stock
            $issuedStock = InventoryStockIssueItem::with(['invstockitems', 'invstockissue'])
                                              ->where('inv_stock_id', $dat->inv_id)->get();
            $invDocStatusList = [];
            $invRangeCountList = [];

            foreach ($issuedStock as $stock) {
                $invStockDat = InventoryStockIssue::find($stock->inv_stock_issue_id);

                $issuedBy = $signatory->getSignatory($invStockDat->sig_received_from ?
                            $invStockDat->sig_received_from :
                            $invStockDat->sig_issued_by)->name;
                $issuedTo = Auth::user()->getEmployee(
                                $invStockDat->sig_received_by
                            )->name;
                $issuedTo .= '<br><em><small class="grey-text">Item Barcode:<br>'.$stock->id.'</small></em>';
                $dateIssued = $stock->date_issued;
                $oldDateInvCreated = strtotime($dat->inv_created_at);

                $invDocStatusList[] = (object) ['issued_by' => $issuedBy,
                                                'issued_to' => $issuedTo,
                                                'date_issued' => $dateIssued,
                                                'quantity' => $stock->quantity];
                $invRangeCountList[] = $this->computeDateRange(date('Y-m-d', $oldDateInvCreated),
                                                               $dateIssued);
            }

            $dat->inv_document_status = $invDocStatusList;
            $dat->inv_range_count = $invRangeCountList;
        }

        //dd(['UNDER DEVELOPMENT', $data]);

        return $data;
    }

    private function generateIAR_DV($dateFrom, $dateTo, $search, $isSearchAll = false) {
        $itemCountPerPage = $isSearchAll ? 20 : 100;
        $roles = $this->getEmpRoles();

        $projects = DB::table('funding_projects')
                      ->select('id')
                      ->where('project_title', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $accounts = DB::table('emp_accounts')
                      ->select('id')
                      ->where('firstname', 'like', "%$search%")
                      ->orWhere('middlename', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $divisions = DB::table('emp_divisions')
                       ->select('id')
                       ->where('division_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();

        $instanceDocLog = new DocLog;
        $data = DB::table('inspection_acceptance_reports as iar')
                  ->select('iar.id as iar_code', 'iar.iar_no', 'dv.id as dv_code',
                           'dv.date_disbursed')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'iar.pr_id')
                  ->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'iar.ors_id')
                  ->whereBetween(DB::raw('DATE(iar.created_at)'), array($dateFrom, $dateTo))
                  ->whereNull('iar.deleted_at');

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search, $projects, $accounts, $divisions) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_approved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_disapproved', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.date_pr_cancelled', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('pr.funding_source', $projects)
                      ->orWhereIn('pr.requested_by', $accounts)
                      ->orWhereIn('pr.approved_by', $accounts)
                      ->orWhereIn('pr.sig_app', $accounts)
                      ->orWhereIn('pr.sig_funds_available', $accounts)
                      ->orWhereIn('pr.recommended_by', $accounts)
                      ->orWhereIn('pr.division', $divisions)
                      ->orWhere('pr.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.iar_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.ors_id', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_iar', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.invoice_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_invoice', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_inspected', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.inspection_remarks', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.date_received', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.acceptance_remarks', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.specify_quantity', 'LIKE', '%' . $search . '%')
                      ->orWhere('iar.remarks_recommendation', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('iar.sig_inspection', $accounts)
                      ->orWhereIn('iar.sig_supply', $accounts)
                      ->orWhere('dv.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.dv_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.transaction_type', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.address', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.date_dv', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.date_disbursed', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.date_for_payment', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.fund_cluster', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.other_payment', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.particulars', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.responsibility_center', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.mfo_pap', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.prior_year', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.continuing', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.current', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.amount', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.uacs_object_code', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('dv.sig_certified', $accounts)
                      ->orWhereIn('dv.sig_accounting', $accounts)
                      ->orWhereIn('dv.sig_agency_head', $accounts)
                      ->orWhereIn('dv.disbursed_by', $accounts)
                      ->orWhereIn('dv.for_payment_by', $accounts)
                      ->orWhereIn('dv.funding_source', $projects);
            });
        }

        $data = $data->orderByRaw('LENGTH(iar.id)', 'desc')
                     ->orderBy('iar.id', 'desc')
                     ->paginate($itemCountPerPage);

        foreach ($data as $dat) {
            $iarDocHistory =  $instanceDocLog->checkDocHistory($dat->iar_code);
            $iarDocStatus = $instanceDocLog->checkDocStatus($dat->iar_code);
            $dvDocHistory =  $instanceDocLog->checkDocHistory($dat->dv_code);
            $dvDocStatus = $instanceDocLog->checkDocStatus($dat->dv_code);

            $dat->iar_document_history = $iarDocHistory;
            $dat->iar_document_status = $iarDocStatus;
            $dat->dv_document_history = $dvDocHistory;
            $dat->dv_document_status = $dvDocStatus;

            $dat->iar_range_count = $this->computeDateRange($iarDocStatus->date_received,
                                                            $dvDocStatus->date_issued);
            $dat->dv_range_count = $this->computeDateRange($dvDocStatus->date_issued,
                                                           $dvDocStatus->date_received);
        }

        //dd(['UNDER DEVELOPMENT', $data]);

        return $data;
    }

    private function generateORS_DV($dateFrom, $dateTo, $search, $isSearchAll = false) {
        $itemCountPerPage = $isSearchAll ? 20 : 100;
        $roles = $this->getEmpRoles();

        $projects = DB::table('funding_projects')
                      ->select('id')
                      ->where('project_title', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $accounts = DB::table('emp_accounts')
                      ->select('id')
                      ->where('firstname', 'like', "%$search%")
                      ->orWhere('middlename', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->pluck('id')
                      ->toArray();
        $suppliers = DB::table('suppliers')
                       ->select('id')
                       ->where('company_name', 'like', "%$search%")
                       ->pluck('id')
                       ->toArray();

        $instanceDocLog = new DocLog;
        $data = DB::table('obligation_request_status as ors')
                  ->select('ors.id as ors_code', 'dv.id as dv_code', 'ors.document_type as doc_type',
                            DB::raw('CONCAT(obligated_by.firstname, " ", obligated_by.lastname) AS obligated_by'),
                            DB::raw('CONCAT(disbursed_by.firstname, " ", disbursed_by.lastname) AS disbursed_by'),
                            'ors.date_obligated', 'ors.serial_no', 'dv.dv_no', 'dv.date_disbursed', 'dv.id as dv_id',
                            'ors.id as ors_id')
                  ->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'ors.id')
                  ->leftJoin('emp_accounts as obligated_by', 'obligated_by.id', '=', 'ors.obligated_by')
                  ->leftJoin('emp_accounts as disbursed_by', 'disbursed_by.id', '=', 'dv.disbursed_by')
                  ->whereBetween(DB::raw('DATE(ors.created_at)'), array($dateFrom, $dateTo))
                  ->whereNull('ors.deleted_at');

        if (!empty($search)) {
            $data = $data->where(function ($query)  use ($search, $projects, $accounts, $suppliers) {
                $query->where('ors.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.po_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.transaction_type', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.document_type', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.fund_cluster', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.serial_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.date_ors_burs', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.date_obligated', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.date_released', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.address', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.responsibility_center', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.particulars', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.mfo_pap', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.uacs_object_code', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.prior_year', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.continuing', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.current', 'LIKE', '%' . $search . '%')
                      ->orWhere('ors.amount', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('ors.payee', $accounts)
                      ->orWhereIn('ors.payee', $suppliers)
                      ->orWhereIn('ors.sig_certified_1', $accounts)
                      ->orWhereIn('ors.sig_certified_2', $accounts)
                      ->orWhereIn('ors.sig_accounting', $accounts)
                      ->orWhereIn('ors.sig_agency_head', $accounts)
                      ->orWhereIn('ors.obligated_by', $accounts)
                      ->orWhereIn('ors.funding_source', $projects)
                      ->orWhere('dv.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.dv_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.transaction_type', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.address', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.date_dv', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.date_disbursed', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.date_for_payment', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.fund_cluster', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.other_payment', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.particulars', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.responsibility_center', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.mfo_pap', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.prior_year', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.continuing', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.current', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.amount', 'LIKE', '%' . $search . '%')
                      ->orWhere('dv.uacs_object_code', 'LIKE', '%' . $search . '%')
                      ->orWhereIn('dv.sig_certified', $accounts)
                      ->orWhereIn('dv.sig_accounting', $accounts)
                      ->orWhereIn('dv.sig_agency_head', $accounts)
                      ->orWhereIn('dv.disbursed_by', $accounts)
                      ->orWhereIn('dv.for_payment_by', $accounts)
                      ->orWhereIn('dv.funding_source', $projects);
            });
        }

        $data = $data->orderByRaw('LENGTH(ors.id)', 'desc')
                     ->orderBy('ors.id', 'desc')
                     ->paginate($itemCountPerPage);

        foreach ($data as $dat) {
            $orsDocHistory =  $instanceDocLog->checkDocHistory($dat->ors_code);
            $dvDocHistory =  $instanceDocLog->checkDocHistory($dat->dv_code);
            $orsDocStatus = $instanceDocLog->checkDocStatus($dat->ors_code);
            $dvDocStatus = $instanceDocLog->checkDocStatus($dat->dv_code);

            $dat->ors_document_history = $orsDocHistory;
            $dat->dv_document_history = $dvDocHistory;
            $dat->ors_document_status = $orsDocStatus;
            $dat->dv_document_status = $dvDocStatus;
            $dat->ors_range_count = $this->computeDateRange($dat->date_obligated, $dvDocStatus->date_received);
            $dat->dv_range_count = $this->computeDateRange($dvDocStatus->date_received, $dat->date_disbursed);
        }

        return $data;
    }
}
