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

        return view('pages.voucher-logs', [//'paperSizes' => $paperSizes,
                                           'toggle' => $toggle,
                                           'type' => '']);
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
        $data;

        switch ($toggle) {
            case 'pr-rfq':
                $data = $this->generatePR_RFQ($dateFrom, $dateTo, $search);
                $prTooltip = "Date & time of PR approved to date & time of RFQ issued.";
                $rfqTooltip = "Date & time of RFQ issued to date & time of RFQ received.";
                return view('modules.voucher-tracking.pr-rfq.index', ['data' => $data,
                                                  'search' => $search,
                                                  'prTooltip' => $prTooltip,
                                                  'rfqTooltip' => $rfqTooltip]);
                break;

            case 'rfq-abstract':
                $data = $this->generateRFQ_Abstract($dateFrom, $dateTo, $search);
                $rfqabstractTooltip = "Date & time of RFQ received to date & time of Abstract approved for PO/JO.";
                return view('modules.voucher-tracking.rfq-abstract.index', ['data' => $data,
                                                        'search' => $search,
                                                        'rfqabstractTooltip' => $rfqabstractTooltip]);
                break;

            case 'abstract-po':
                $data = $this->generateAbstract_PO($dateFrom, $dateTo, $search);
                $abstractTooltip = "Date & time of Abstract approved for PO/JO to date & time of PO/JO approved.";
                $poTooltip = "Date & time of PO/JO approved to date & time of PO/JO received.";
                return view('modules.voucher-tracking.abstract-po-jo.index', ['data' => $data,
                                                       'search' => $search,
                                                       'abstractTooltip' => $abstractTooltip,
                                                       'poTooltip' => $poTooltip]);
                break;

            case 'po-ors':
                $data = $this->generatePO_ORS($dateFrom, $dateTo, $search);
                $poTooltip = "Date & time of PO/JO issued to date & time of PO/JO received.";
                $orsTooltip = "Date & time of ORS/BURS received by Budget Officer to date & time of ORS/BURS obligated.";
                return view('modules.voucher-tracking.po-jo-ors-burs.index', ['data' => $data,
                                                  'search' => $search,
                                                  'poTooltip' => $poTooltip,
                                                  'orsTooltip' => $orsTooltip]);
                break;

            case 'po-iar':
                $data = $this->generatePO_IAR($dateFrom, $dateTo, $search);
                $poTooltip = "Date & time of PO/JO issued to date & time of PO/JO received.";
                $iarTooltip = "Date & time of PO/JO received to date & time of IAR inspected.";
                return view('modules.voucher-tracking.po-jo-iar.index', ['data' => $data,
                                                  'search' => $search,
                                                  'poTooltip' => $poTooltip,
                                                  'iarTooltip' => $iarTooltip]);
                break;

            case 'iar-stock':
                $data = $this->generateIAR_STOCK($dateFrom, $dateTo, $search);
                $iarTooltip = "Date & time of IAR inspected to date & time of Inventory Stock created.";
                $stockTooltip = "Date & time of Inventory Stock created to date & time of
                                 Inventory Stock issued.";
                return view('modules.voucher-tracking.iar-stocks.index', ['data' => $data,
                                                     'search' => $search,
                                                     'iarTooltip' => $iarTooltip,
                                                     'stockTooltip' => $stockTooltip]);
                break;

            case 'iar-dv':
                $data = $this->generateIAR_DV($dateFrom, $dateTo, $search);
                $iarTooltip = "Date & time of IAR inspected to date & time of DV issued.";
                $dvTooltip = "Date & time of DV issued to date & time of DV received.";
                return view('modules.voucher-tracking.iar-dv.index', ['data' => $data,
                                                  'search' => $search,
                                                  'iarTooltip' => $iarTooltip,
                                                  'dvTooltip' => $dvTooltip]);
                break;

            case 'ors-dv':
                $data = $this->generateORS_DV($dateFrom, $dateTo, $search);
                $orsTooltip = "Date & time of ORS/BURS obligated to date & time of DV received.";
                $dvTooltip = "Date & time of DV received to date & time of DV disbursed.";
                return view('modules.voucher-tracking.ors-burs-dv.index', ['data' => $data,
                                                  'search' => $search,
                                                  'orsTooltip' => $orsTooltip,
                                                  'dvTooltip' => $dvTooltip]);
                break;

            default:
                # code...
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

    private function generatePR_RFQ($dateFrom, $dateTo, $search) {
        $instanceDocLog = new DocLog;
        $data = DB::table('purchase_requests as pr')
                  ->select('pr.id as pr_code', 'rfq.id as rfq_code',
                           'pr.date_pr_approved', 'pr.pr_no', 'pr.id')
                  ->leftJoin('request_quotations as rfq', 'rfq.pr_id', '=', 'pr.id')
                  ->whereBetween(DB::raw('DATE(pr.created_at)'), array($dateFrom, $dateTo));

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('rfq.id', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $data->orderByRaw('LENGTH(pr.pr_no)', 'desc')
                     ->orderBy('pr.pr_no', 'desc')
                     ->whereNull('pr.deleted_at')
                     ->paginate(100);

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

    private function generateRFQ_Abstract($dateFrom, $dateTo, $search) {
        $instanceDocLog = new DocLog;
        $data = DB::table('request_quotations as rfq')
                  ->select('rfq.id as rfq_code', 'abstract.id as abstract_code',
                           'abstract.date_abstract_approved', 'rfq.pr_id', 'pr.pr_no')
                  ->join('purchase_requests as pr', 'pr.id', '=', 'rfq.pr_id')
                  ->leftJoin('abstract_quotations as abstract', 'abstract.pr_id', '=', 'rfq.pr_id')
                  ->whereBetween(DB::raw('DATE(rfq.created_at)'), array($dateFrom, $dateTo));

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search) {
                $query->where('rfq.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.id', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $data->orderByRaw('LENGTH(rfq.id)', 'desc')
                     ->orderBy('rfq.id', 'desc')
                     ->paginate(100);

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

    private function generateAbstract_PO($dateFrom, $dateTo, $search) {
        $instanceDocLog = new DocLog;
        $data = DB::table('abstract_quotations as abstract')
                  ->select('abstract.id as abstract_code', 'po.id as po_code', 'pr.pr_no',
                           'abstract.date_abstract_approved', 'po.date_po_approved')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'abstract.pr_id')
                  ->leftJoin('purchase_job_orders as po', 'po.pr_id', '=', 'abstract.pr_id')
                  ->whereBetween(DB::raw('DATE(abstract.created_at)'), array($dateFrom, $dateTo));

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search) {
                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                      ->orWhere('abstract.id', 'LIKE', '%' . $search . '%')
                      ->orWhere('po.id', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $data->orderByRaw('LENGTH(abstract.id)', 'desc')
                     ->orderBy('abstract.id', 'desc')
                     ->paginate(100);

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

    private function generatePO_ORS($dateFrom, $dateTo, $search) {
        $instanceDocLog = new DocLog;
        $data = DB::table('purchase_job_orders as po')
                  ->select('po.id as po_code', 'po.po_no', 'ors.id as ors_code',
                           'po.document_type', 'po.date_po_approved', 'ors.date_obligated',
                           'po.created_at as po_created_at')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                  ->leftJoin('obligation_request_status as ors', 'ors.po_no', '=', 'po.po_no')
                  ->whereBetween(DB::raw('DATE(po.created_at)'), array($dateFrom, $dateTo));

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search) {
                                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                                      ->orWhere('po.po_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('po.id', 'LIKE', '%' . $search . '%')
                                      ->orWhere('ors.id', 'LIKE', '%' . $search . '%');
                            });
        }

        $data = $data->orderByRaw('LENGTH(po.id)', 'desc')
                     ->orderBy('po.id', 'desc')
                     ->paginate(100);

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

    private function generatePO_IAR($dateFrom, $dateTo, $search) {
        $instanceDocLog = new DocLog;
        $data = DB::table('purchase_job_orders as po')
                  ->select('po.id as po_code', 'iar.id as iar_code', 'po.po_no',
                           'po.document_type', 'po.date_po_approved')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'po.pr_id')
                  ->leftJoin('inspection_acceptance_reports as iar', 'iar.iar_no', 'LIKE', DB::Raw("CONCAT('%', po.po_no, '%')"))
                  ->whereBetween(DB::raw('DATE(po.created_at)'), array($dateFrom, $dateTo));

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search) {
                                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                                      ->orWhere('po.po_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('po.id', 'LIKE', '%' . $search . '%')
                                      ->orWhere('iar.id', 'LIKE', '%' . $search . '%');
                            });
        }

        $data = $data->orderByRaw('LENGTH(po.id)', 'desc')
                     ->orderBy('po.id', 'desc')
                     ->paginate(100);

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

    private function generateIAR_STOCK($dateFrom, $dateTo, $search) {
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
                  ->whereBetween(DB::raw('DATE(iar.created_at)'), array($dateFrom, $dateTo));

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search) {
                                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                                      ->orWhere('iar.id', 'LIKE', '%' . $search . '%')
                                      ->orWhere('inv.id', 'LIKE', '%' . $search . '%');
                            });
        }

        $data = $data->orderByRaw('LENGTH(iar.id)', 'desc')
                     ->orderBy('iar.id', 'desc')
                     ->distinct()
                     ->paginate(100);

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
                $issuedBy = $signatory->getSignatory($stock->invstockissue->sig_received_from ?
                            $stock->invstockissue->sig_received_from :
                            $stock->invstockissue->sig_issued_by)->name;
                $issuedTo = Auth::user()->getEmployee(
                                $stock->invstockissue->sig_received_by
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

    private function generateIAR_DV($dateFrom, $dateTo, $search) {
        $instanceDocLog = new DocLog;
        $data = DB::table('inspection_acceptance_reports as iar')
                  ->select('iar.id as iar_code', 'iar.iar_no', 'dv.id as dv_code',
                           'dv.date_disbursed')
                  ->leftJoin('purchase_requests as pr', 'pr.id', '=', 'iar.pr_id')
                  ->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'iar.ors_id')
                  ->whereBetween(DB::raw('DATE(iar.created_at)'), array($dateFrom, $dateTo));

        if (!empty($search)) {
            $data = $data->where(function ($query) use ($search) {
                                $query->where('pr.pr_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('pr.purpose', 'LIKE', '%' . $search . '%')
                                      ->orWhere('pr.office', 'LIKE', '%' . $search . '%')
                                      ->orWhere('iar.id', 'LIKE', '%' . $search . '%')
                                      ->orWhere('dv.id', 'LIKE', '%' . $search . '%');
                            });
        }

        $data = $data->orderByRaw('LENGTH(iar.id)', 'desc')
                     ->orderBy('iar.id', 'desc')
                     ->paginate(100);

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

    private function generateORS_DV($dateFrom, $dateTo, $search) {
        $instanceDocLog = new DocLog;
        $data = DB::table('obligation_request_status as ors')
                  ->select('ors.id as ors_code', 'dv.id as dv_code', 'ors.document_type as doc_type',
                            DB::raw('CONCAT(obligated_by.firstname, " ", obligated_by.lastname) AS obligated_by'),
                            'ors.date_obligated', 'ors.serial_no', 'dv.dv_no', 'dv.date_disbursed', 'dv.id as dv_id',
                            'ors.id as ors_id')
                  ->leftJoin('emp_accounts as obligated_by', 'obligated_by.emp_id', '=', 'ors.obligated_by')
                  ->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'ors.id')
                  ->whereBetween(DB::raw('DATE(ors.created_at)'), array($dateFrom, $dateTo));

        if (!empty($search)) {
            $data = $data->where(function ($query)  use ($search) {
                                $query->where('obligated_by.firstname', 'LIKE', '%' . $search . '%')
                                      ->orWhere('obligated_by.middlename', 'LIKE', '%' . $search . '%')
                                      ->orWhere('obligated_by.lastname', 'LIKE', '%' . $search . '%')
                                      ->orWhere('ors.serial_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('ors.po_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('ors.particulars', 'LIKE', '%' . $search . '%')
                                      ->orWhere('dv.dv_no', 'LIKE', '%' . $search . '%')
                                      ->orWhere('dv.particulars', 'LIKE', '%' . $search . '%')
                                      ->orWhere('ors.id', 'LIKE', '%' . $search . '%')
                                      ->orWhere('dv.id', 'LIKE', '%' . $search . '%');
                            });
        }

        $data = $data->orderByRaw('LENGTH(ors.id)', 'desc')
                     ->orderBy('ors.id', 'desc')
                     ->paginate(100);

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

    private function getEmployeeName($empID) {
        $employee = DB::table('emp_accounts')
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

    private function getSignatoryName($id) {
        $signatory = DB::table('signatories as sig')
                       ->select('sig.id as sig_id', 'emp.firstname', 'emp.middlename',
                                'emp.lastname')
                       ->join('emp_accounts as emp', 'emp.emp_id', '=', 'sig.emp_id')
                       ->where('sig.id', $id)
                       ->first();

        $fullname = "";

        if ($signatory) {
            if (!empty($signatory->middlename)) {
                $fullname = $signatory->firstname . " " . $signatory->middlename[0] . ". " .
                            $signatory->lastname;
            } else {
                $fullname = $signatory->firstname . " " . $signatory->lastname;
            }

            $fullname = strtoupper($fullname);
        }

        return $fullname;
    }
}
