<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\RequestQuotation;
use App\Models\AbstractQuotation;
use App\Models\AbstractQuotationItem;
use App\Models\PurchaseJobOrder;
use App\Models\PurchaseJobOrderItem;
use App\Models\ObligationRequestStatus;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\InventoryStock;

use App\Models\DocumentLog as DocLog;
use App\Models\EmpLog;
use App\Models\Signatory;
use App\User;
use App\Models\PaperSize;
use Carbon\Carbon;
use Auth;
use DB;

use \DateTime;
//use App\Classes\PDF;
//use App\Classes\Barcode;
use App\LiquidationReport;
use App\ListDueDemandAccPay;

use App\Plugins\PDFGenerator\DocPurchaseRequest;
use App\Plugins\PDFGenerator\DocRequestQuotation;
use App\Plugins\PDFGenerator\DocAbstractQuotation;
use App\Plugins\PDFGenerator\DocPurchaseOrder;
use App\Plugins\PDFGenerator\DocJobOrder;
use App\Plugins\PDFGenerator\DocObligationRequestStatus;
use App\Plugins\PDFGenerator\DocInspectionAcceptanceReport;
use App\Plugins\PDFGenerator\DocDisbursementVoucher;

class PrintController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function index(Request $request, $key) {
        $instanceDocLog = new DocLog;

        $action = "document_generated";
        $test = $request->test;
        $paperSize = $request->paper_size;
        $documentType = $request->document_type;
        $pageWidth = $this->getPaperSize($paperSize)->width;
        $pageHeight = $this->getPaperSize($paperSize)->height;
        $pageUnit = $this->getPaperSize($paperSize)->unit;
        $fontScale = $request->font_scale / 100;
        $previewToggle = $request->preview_toggle;
        $otherParam = $request->other_param;

        switch ($documentType) {
            case 'proc_pr':
                $data = $this->getDataPR($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $prNo =  $data->pr->pr_no;
                    $msg = "Generated the Purchase Request '$prNo' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generatePR(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'proc_rfq':
                $data = $this->getDataRFQ($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $prNo =  $data->pr->pr_no;
                    $msg = "Generated the Request for Quotation '$prNo' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateRFQ(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'proc_abstract':
                $data = $this->getDataAbstract($key, $pageHeight);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $prNo =  $data->abstract->pr_no;
                    $msg = "Generated the Abstract of Quotation '$prNo' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateAbstract(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'proc_po':
                $data = $this->getDataPOJO($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $poNo =  $data->po->po_no;
                    $msg = "generated the Purchase Order '$poNo' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generatePO(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'proc_jo':
                $data = $this->getDataPOJO($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $joNo =  $data->jo->po_no;
                    $msg = "Generated the Job Order '$joNo' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateJO(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'proc_ors':
                $data = $this->getDataORSBURS($key, 'procurement');
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Obligation Request and Status '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateORS(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'proc_burs':
                $data = $this->getDataORSBURS($key, 'procurement');
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Budget Utilization Request and Status '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateBURS(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'ca_ors':
                $data = $this->getDataORSBURS($key, 'cashadvance');
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Obligation Request and Status '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateORS(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'ca_burs':
                $data = $this->getDataORSBURS($key, 'cashadvance');
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $code = $this->getDocCode($key, 'ors');
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $docType = ($data->ors->document_type == 'ors') ?
                               'obligation and request status': 'budget utilization and request status';
                    $orsID =  $data->ors->id;
                    $msg = "Ggenerated the $docType $orsID.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateBURS($data, $data->ors->document_type, $fontScale,
                                        $pageHeight, $pageWidth, $previewToggle);
                }
                break;
            case 'proc_iar':
                $data = $this->getDataIAR($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Obligation Request and Status '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateIAR(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'proc_dv':
                $data = $this->getDataDV($key, 'procurement');
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Disbursement Voucher '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateDV(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;
            case 'ca_dv':
                $data = $this->getDataDV($key, 'cashadvance');
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Disbursement Voucher '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateDV(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'ca_lr':
                $data = $this->getDataLiquidation($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $code = $this->getDocCode($key, 'liquidation');
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $liqID =  $data->liq->id;
                    $msg = "generated the disbursement voucher $liqID.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateLiquidation($data, $documentType, $fontScale,
                                               $pageHeight, $pageWidth, $previewToggle);
                }

                break;

                case 'pay_lddap':
                    $data = $this->getDataLDDAP($key);
                    $data->doc_type = $documentType;

                    if ($test == 'true') {
                        //$code = $this->getDocCode($key, 'lddap');
                        //$instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                        //$lddapID =  $data->lddap->id;
                        //$msg = "generated the disbursement voucher $lddapID.";
                        //Auth::user()->log($request, $msg);
                    } else {
                        $this->generateLDDAP($data, $documentType, $fontScale,
                                             $pageHeight, $pageWidth, $previewToggle);
                    }

                    break;

            case 'inv_par':
                $data = $this->getDataPAR($key, $otherParam);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $code = $this->getDocCode($key, 'stock');
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "generated the property acknowledgement report $key.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generatePAR($data, $documentType, $fontScale,
                                       $pageHeight, $pageWidth, $previewToggle);
                }

                break;

            case 'inv_ris':
                $data = $this->getDataRIS($key, $otherParam);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $code = $this->getDocCode($key, 'stock');
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "generated the requisition and issue slip $key.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateRIS($data, $documentType, $fontScale,
                                       $pageHeight, $pageWidth, $previewToggle);
                }

                break;

            case 'inv_ics':
                $data = $this->getDataICS($key, $otherParam);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $code = $this->getDocCode($key, 'stock');
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "generated the inventory custodian slip $key.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateICS($data, $documentType, $fontScale,
                                       $pageHeight, $pageWidth, $previewToggle);
                }

                break;

            case 'inv_label':
                $data = $this->getDataPropertyLabel($key, $otherParam);
                $data->doc_type = $documentType;

                if ($test == 'true') {

                } else {
                    $pageHeight = 53.27;
                    $pageWidth = 103.76125;
                    $this->generatePropertyLabel($data, $documentType, $fontScale,
                                                 $pageHeight, $pageWidth, $previewToggle);
                }

                break;

            default:
                # code...
                break;
        }
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

    private function getDocCode($key, $type) {
        $type = strtolower($type);

        switch ($type) {
            case 'pr':
                $data = PurchaseRequest::where('id', $key)->first();
                $code = $data->code;
                break;

            case 'rfq':
                $data = Canvass::where('pr_id', $key)->first();
                $code = $data->code;
                break;

            case 'abstract':
                $data = Abstracts::where('pr_id', $key)->first();
                $code = $data->code;
                break;

            case 'po':
                $data = PurchaseOrder::where('po_no', $key)->first();
                $code = $data->code;
                break;

            case 'ors':
                $data = OrsBurs::where('id', $key)->first();
                $code = $data->code;
                break;

            case 'iar':
                $data = InspectionAcceptance::where('iar_no', $key)->first();
                $code = $data->code;
                break;

            case 'dv':
                $data = DisbursementVoucher::where('id', $key)->first();
                $code = $data->code;
                break;

            case 'liquidation':
                $data = LiquidationReport::where('id', $key)->first();
                $code = $data->code;
                break;

            case 'stock':
                $data = InventoryStock::where('inventory_no', $key)->first();
                $code = $data->code;
                break;

            case 'lddap':
                $data = ListDueDemandAccPay::where('lddap_id', $key)->first();
                $code = $data->code;
                break;

            default:
                $code = "";
                break;
        }

        return $code;
    }

    private function getPaperSize($id) {
        $paper = PaperSize::find($id);
        $width = !empty($paper->width) ? $paper->width : 0;
        $height = !empty($paper->height) ? $paper->height : 0;
        $unit = !empty($paper->unit) ? $paper->unit : '';
        $isNull = !$paper ? true : false;

        return (object) ['width' => $width,
                         'height' => $height,
                         'unit' => $unit,
                         'isNull' => $isNull];
    }

    private function getBidderCount($groupNo, $prID) {
        $bidderCount = 0;
        $itemID = PurchaseRequestItem::select('id')
                                     ->where([
            ['pr_id', $prID], ['group_no', $groupNo]
        ])->orderBy('item_no')->first();
        $bidderCount = AbstractQuotationItem::where('pr_item_id', $itemID->id)
                                            ->count();
        return $bidderCount;
    }

    private function getItemGroup($id) {
        $groupNumbers = PurchaseRequestItem::select('group_no')
                                           ->where('pr_id', $id)
                                           ->distinct()
                                           ->orderBy('group_no', 'asc')
                                           ->get();
        return $groupNumbers;
    }

    private function getDataPropertyLabel($inventoryID, $empID) {
        $dataLabel = DB::table('inventory_stock_issues as issued')
                  ->join('inventory_stocks as stock', 'stock.id', '=', 'issued.inventory_id')
                  ->join('purchase_job_order_items as po', 'po.item_id', '=', 'stock.po_item_id')
                  ->where('issued.received_by', $empID)
                  //->where('issued.inventory_id', $inventoryID)
                  ->where('stock.inventory_no', $inventoryID)
                  ->get();
        $finalData = [];

        foreach ($dataLabel as $data) {
            $propertyNo = $data->property_no;
            $issuedBy = $this->getSignatory($data->issued_by);
            $receivedBy = $this->getEmployee($empID);
            $acquiredDate = "";

            if (!empty($data->date_issued)) {
                $acquiredDate = new DateTime($data->date_issued);
                $acquiredDate = $acquiredDate->format('F j, Y');
            }

            /*
            if (strpos($data->item_description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $data->item_description = str_replace($searchStr, '<br>', $data->item_description);
            }*/

            $itemDescription = $data->item_description;
            $multiplier = 85 / 85;

            if (empty($data->property_no)) {
                $propertyNo = 'N/A';
            }

            if (!empty($data->serial_no)) {
                $itemDescription = substr($data->item_description, 0, 178) . "... \n[S/N: " . $data->serial_no . "]";
            } else {
                $itemDescription = substr($data->item_description, 0, 185) . "...";
            }

            $data1 = [
                [
                    'aligns' => ['L', 'L'],
                    'widths' => [$multiplier * 18,
                                $multiplier * 67],
                    'font-styles' => ['', ''],
                    'type' => 'row-data',
                    'data' => [[' Property No.:', $propertyNo]]
                ]
            ];
            $data2 = [
                [
                    'aligns' => ['L', 'L'],
                    'widths' => [$multiplier * 18,
                                $multiplier * 67],
                    'font-styles' => ['', ''],
                    'type' => 'row-data',
                    'data' => [[' Description:', $itemDescription]]
                ]
            ];
            $data3 = [
                [
                    'aligns' => ['L', 'L', 'L', 'L'],
                    'widths' => [$multiplier * 18,
                                $multiplier * 21,
                                $multiplier * 12,
                                $multiplier * 34],
                    'font-styles' => ['', 'B', '', 'B'],
                    'type' => 'row-data',
                    'data' => [[' Date Acquired:', $acquiredDate, 'Issued To:', $receivedBy->name]]
                ]
            ];
            $data4 = [
                [
                    'aligns' => ['L', 'L'],
                    'widths' => [$multiplier * 18,
                                $multiplier * 67],
                    'font-styles' => ['', 'B'],
                    'type' => 'row-data',
                    'data' => [[' Certified By:', $issuedBy->name],
                            [' Verified By:', '______________________________________________________']]
                ]
            ];

            $finalData[] = (object)['stock' => $data,
                                    'data1' => $data1,
                                    'data2' => $data2,
                                    'data3' => $data3,
                                    'data4' => $data4,
                                    'property_no' => $propertyNo,
                                    'stock_id' => $data->id,
                                    'received_by' => $receivedBy->name];
        }

        return $finalData;
    }

    private function getDataICS($inventoryNo, $empID) {
        $tableData = [];
        $stockIssue = DB::table('inventory_stock_issues as stocks')
                        ->select('stocks.quantity', 'unit.unit', 'po.unit_cost','po.total_cost',
                                'po.item_description', 'stocks.date_issued', 'inv.property_no',
                                'inv.est_useful_life', 'stocks.issued_by', 'inv.id as inv_id')
                        ->join('inventory_stocks as inv', 'inv.id', '=', 'stocks.inventory_id')
                        ->join('purchase_job_order_items as po', 'po.item_id', '=', 'inv.po_item_id')
                        ->join('item_unit_issues as unit', 'unit.id', '=', 'po.unit_issue')
                        ->where('inv.inventory_no', $inventoryNo)
                        ->where('stocks.received_by', $empID)
                        ->orderBy('inv.id')
                        ->distinct()
                        ->get();
        $po = DB::table('purchase_job_orders as po')
                ->select('po.po_no', 'po.date_po', 'bid.company_name')
                ->join('inventory_stocks as inv', 'inv.po_no', '=', 'po.po_no')
                ->join('suppliers as bid', 'bid.id', '=', 'po.awarded_to')
                ->where('inv.inventory_no', $inventoryNo)
                ->first();

        foreach ($stockIssue as $item) {
            if (!empty($item->date_issued)) {
                $item->date_issued = new DateTime($item->date_issued);
                $item->date_issued = $item->date_issued->format('F j, Y');
            }

            if (strpos($item->item_description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->item_description = str_replace($searchStr, '<br>', $item->item_description);
            }

            $issuedBy = $item->issued_by;
            $tableData[] = [$item->quantity,
                            $item->unit_name,
                            number_format($item->unit_cost, 2),
                            number_format($item->total_cost, 2),
                            $item->item_description,
                            $item->date_issued,
                            $item->property_no,
                            $item->est_useful_life];
        }

        for ($i = 0; $i <= 3; $i++) {
            $tableData[] = ['', '', '', '', '', '', '', ''];
        }

        $issuedBy = $this->getSignatory($issuedBy);
        $receivedBy = $this->getEmployee($empID);
        $multiplier = 100 / 90;
        $data = [
            [
                'col-span' => true,
                'col-span-key' => ['0', '1', '2-3', '4', '5', '6', '7'],
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [$multiplier * 10, $multiplier * 8,
                             $multiplier * 7.5, $multiplier * 7.5,
                             $multiplier * 28, $multiplier * 9,
                             $multiplier * 10, $multiplier * 10],
                'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'],
                'type' => 'other',
                'data' => [['', '', 'Amount', '', '', '', '', '']]
            ], [
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [$multiplier * 10, $multiplier * 8,
                             $multiplier * 7.5, $multiplier * 7.5,
                             $multiplier * 28, $multiplier * 9,
                             $multiplier * 10, $multiplier * 10],
                'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'],
                'type' => 'row-data',
                'data' => [['Quantity', 'Unit', 'Unit Cost', 'Total Cost', 'Description',
                            'Date Acquired', 'Inventory Item No.', 'Estimated Useful Life']]
            ], [
                'aligns' => ['C', 'C', 'C', 'C', 'L', 'C', 'C', 'C'],
                'widths' => [$multiplier * 10, $multiplier * 8,
                             $multiplier * 7.5, $multiplier * 7.5,
                             $multiplier * 28, $multiplier * 9,
                             $multiplier * 10, $multiplier * 10],
                'font-styles' => ['', '', '', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData
            ]
        ];



        return (object)['inventory_no' => $inventoryNo,
                        'po' => $po,
                        'table_data' => $data,
                        'issued_by' => $issuedBy,
                        'received_by' => $receivedBy];
    }

    private function getDataRIS($inventoryNo, $empID) {
        $tableData = [];
        $stockIssue = DB::table('inventory_stock_issues as stocks')
                        ->select('po.stock_no', 'unit.unit', 'po.item_description', 'po.quantity as po_qnty',
                                 'inv.stock_available', 'stocks.quantity', 'stocks.issued_remarks',
                                 'stocks.issued_by', 'stocks.approved_by', 'inv.id as inv_id')
                        ->join('inventory_stocks as inv', 'inv.id', '=', 'stocks.inventory_id')
                        ->join('purchase_job_order_items as po', 'po.item_id', '=', 'inv.po_item_id')
                        ->join('item_unit_issues as unit', 'unit.id', '=', 'po.unit_issue')
                        ->where('inv.inventory_no', $inventoryNo)
                        ->where('stocks.received_by', $empID)
                        ->orderBy('inv.id')
                        ->distinct()
                        ->get();
        $po = DB::table('purchase_job_orders as po')
                ->select('div.division', 'inv.office', 'inv.requested_by', 'inv.purpose')
                ->join('inventory_stocks as inv', 'inv.po_no', '=', 'po.po_no')
                ->join('suppliers as bid', 'bid.id', '=', 'po.awarded_to')
                ->join('emp_divisions as div', 'div.id', '=', 'inv.division_id')
                ->where('inv.inventory_no', $inventoryNo)
                ->first();

        foreach ($stockIssue as $item) {
            $yes = "";
            $no = "";

            if ($item->stock_available == 'y') {
                $yes = "x";
            } else {
                $no = "x";
            }

            if (strpos($item->item_description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->item_description = str_replace($searchStr, '<br>', $item->item_description);
            }

            if (strpos($item->issued_remarks, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->issued_remarks = str_replace($searchStr, '<br>', $item->issued_remarks);
            }

            $approvedBy = $item->approved_by;
            $issuedBy = $item->issued_by;
            $tableData[] = [$item->stock_no,
                            $item->unit_name,
                            $item->item_description,
                            $item->po_qnty,
                            $yes,
                            $no,
                            $item->quantity,
                            $item->issued_remarks];
        }

        for ($i = 0; $i <= 3; $i++) {
            $tableData[] = ['', '', '', '', '', '', '', ''];
        }

        $multiplier = 100 / 90;
        $requestedBy = $this->getEmployee($po->requested_by);
        $approvedBy = $this->getSignatory($approvedBy);
        $issuedBy = $this->getSignatory($issuedBy);
        $receivedBy = $this->getEmployee($empID);

        $data = [
            [
                'aligns' => ['C', 'C', 'C'],
                'widths' => [$multiplier * 48, $multiplier * 14,
                             $multiplier * 28],
                'font-styles' => ['B', 'B', 'B'],
                'type' => 'row-data',
                'data' => [['<br>Requisition', '<br>Stock Available?', '<br>Issue']]
            ], [
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [$multiplier * 10, $multiplier * 6,
                             $multiplier * 23, $multiplier * 9,
                             $multiplier * 7, $multiplier * 7,
                             $multiplier * 9, $multiplier * 19],
                'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'],
                'type' => 'row-data',
                'data' => [['Stock No.', 'Unit', 'Description',
                            'Quantity', 'Yes', 'No',
                            'Quantity', 'Remarks']]
            ], [
                'aligns' => ['C', 'C', 'L', 'C', 'C', 'C', 'C', 'L'],
                'widths' => [$multiplier * 10, $multiplier * 6,
                             $multiplier * 23, $multiplier * 9,
                             $multiplier * 7, $multiplier * 7,
                             $multiplier * 9, $multiplier * 19],
                'font-styles' => ['', '', '', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData
            ]
        ];
        $dataFooter = [
            [
                'aligns' => ['L', 'C', 'C', 'C', 'C'],
                'widths' => [$multiplier * 16, $multiplier * 18.5,
                             $multiplier * 18.5, $multiplier * 18.5,
                             $multiplier * 18.5],
                'font-styles' => ['', '', '', '', ''],
                'type' => 'row-data',
                'data' => [['Signature:<br><br> ', '', '', '', ''],
                           ['Printed Name:', $requestedBy->name, $approvedBy->name,
                            $issuedBy->name, $receivedBy->name],
                           ['Designations:', $requestedBy->position, $approvedBy->position,
                            $issuedBy->position, $receivedBy->position],
                           ['Date:', '', '', '', '']]
            ]
        ];

        return (object)['inventory_no' => $inventoryNo,
                        'table_data' => $data,
                        'footer_data' => $dataFooter,
                        'po' => $po];
    }

    private function getDataPAR($inventoryNo, $empID) {
        $tableData = [];
        $stockIssue = DB::table('inventory_stock_issues as stocks')
                        ->select('stocks.quantity', 'unit.unit', 'po.item_description',
                                 'stocks.date_issued', 'po.total_cost', 'inv.property_no',
                                 'stocks.issued_by', 'inv.id as inv_id')
                        ->join('inventory_stocks as inv', 'inv.id', '=', 'stocks.inventory_id')
                        ->join('purchase_job_order_items as po', 'po.item_id', '=', 'inv.po_item_id')
                        ->join('item_unit_issues as unit', 'unit.id', '=', 'po.unit_issue')
                        ->where('inv.inventory_no', $inventoryNo)
                        ->where('stocks.received_by', $empID)
                        ->orderBy('inv.id')
                        ->distinct()
                        ->get();
        $po = DB::table('purchase_job_orders as po')
                ->select('po.po_no', 'po.date_po', 'bid.company_name')
                ->join('inventory_stocks as inv', 'inv.po_no', '=', 'po.po_no')
                ->join('suppliers as bid', 'bid.id', '=', 'po.awarded_to')
                ->where('inv.inventory_no', $inventoryNo)
                ->first();

        foreach ($stockIssue as $item) {
            if (!empty($item->date_issued)) {
                $item->date_issued = new DateTime($item->date_issued);
                $item->date_issued = $item->date_issued->format('F j, Y');
            }

            if (strpos($item->item_description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->item_description = str_replace($searchStr, '<br>', $item->item_description);
            }

            $issuedBy = $item->issued_by;
            $tableData[] = [$item->quantity,
                            $item->unit_name,
                            $item->item_description,
                            $item->property_no,
                            $item->date_issued,
                            number_format($item->total_cost, 2)];
        }

        for ($i = 0; $i <= 3; $i++) {
            $tableData[] = ['', '', '', '', '', ''];
        }

        $multiplier = 100 / 90;
        $issuedBy = $this->getSignatory($issuedBy);
        $receivedBy = $this->getEmployee($empID);
        $data = [
            [
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [$multiplier * 10, $multiplier * 8,
                             $multiplier * 37, $multiplier * 10,
                             $multiplier * 10, $multiplier * 15],
                'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
                'type' => 'row-title',
                'data' => [["Quantity", "Unit", "Description", "Property Number", "Date Acquired", "Amount"]]
            ], [
                'aligns' => ['C', 'C', 'L', 'C', 'C', 'C'],
                'widths' => [$multiplier * 10, $multiplier * 8,
                             $multiplier * 37, $multiplier * 10,
                             $multiplier * 10, $multiplier * 15],
                'font-styles' => ['', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData
            ]
        ];

        return (object)['inventory_no' => $inventoryNo,
                        'po' => $po,
                        'table_data' => $data,
                        'issued_by' => $issuedBy,
                        'received_by' => $receivedBy];
    }

    private function getDataLDDAP($id) {
        $lddap = DB::table('list_demand_payables')
                   ->where('lddap_id', $id)
                   ->first();
        $lddapItems = DB::table('list_demand_payables_items')
                        ->where('lddap_id', $id)
                        ->get();

        $currentTableData = [];
        $priorTableData = [];
        $currAmountGross = 0;
        $currWithholding = 0;
        $currNetAmount = 0;
        $priorAmountGross = 0;
        $priorWithholding = 0;
        $priorNetAmount = 0;
        $amountGross = 0;
        $withholding = 0;
        $netAmount = 0;
        $multiplier = 90 / 100;

        foreach ($lddapItems as $item) {
            if (strpos($item->creditor_name, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->creditor_name = str_replace($searchStr, '<br>', $item->creditor_name);
            }

            if (strpos($item->creditor_acc_no, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->creditor_acc_no = str_replace($searchStr, '<br>', $item->creditor_acc_no);
            }

            if (strpos($item->ors_no, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->ors_no = str_replace($searchStr, '<br>', $item->ors_no);
            }

            if (strpos($item->allot_class_uacs, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->allot_class_uacs = str_replace($searchStr, '<br>', $item->allot_class_uacs);
            }

            if (strpos($item->remarks, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->remarks = str_replace($searchStr, '<br>', $item->remarks);
            }

            if ($item->category == 'current_year') {
                $currAmountGross += $item->gross_amount;
                $currWithholding += $item->withold_tax;
                $currNetAmount += $item->net_amount;

                $item->gross_amount = number_format($item->gross_amount, 2);
                $item->withold_tax = number_format($item->withold_tax, 2);
                $item->net_amount = number_format($item->net_amount, 2);

                $currentTableData[] = [$item->creditor_name, $item->creditor_acc_no,
                                       $item->ors_no, $item->allot_class_uacs,
                                       $item->gross_amount, $item->withold_tax,
                                       $item->net_amount, $item->remarks];
            } else if ($item->category == 'prior_year') {
                $priorAmountGross += $item->gross_amount;
                $priorWithholding += $item->withold_tax;
                $priorNetAmount += $item->net_amount;

                $item->gross_amount = number_format($item->gross_amount, 2);
                $item->withold_tax = number_format($item->withold_tax, 2);
                $item->net_amount = number_format($item->net_amount, 2);

                $priorTableData[] = [$item->creditor_name, $item->creditor_acc_no,
                                     $item->ors_no, $item->allot_class_uacs,
                                     $item->gross_amount, $item->withold_tax,
                                     $item->net_amount, $item->remarks];
            }
        }

        if (count($currentTableData) < 9) {
            $currentDataCount = count($currentTableData);

            for ($i = $currentDataCount; $i <= 8; $i++) {
                $currentTableData[] = ['', '', '', '', '', '', '-', ''];
            }
        }

        if (count($priorTableData) < 5) {
            $priorDataCount = count($priorTableData);

            for ($i = $priorDataCount; $i <= 5; $i++) {
                $priorTableData[] = ['', '', '', '', '', '', '-', ''];
            }
        }

        $amountGross = $currAmountGross + $priorAmountGross;
        $withholding = $currWithholding + $priorWithholding;
        $netAmount = $currNetAmount + $priorNetAmount;
        $currAmountGross = number_format($currAmountGross, 2);
        $currWithholding = number_format($currWithholding, 2);
        $currNetAmount = number_format($currNetAmount, 2);
        $priorAmountGross = $priorAmountGross ?
                            number_format($priorAmountGross, 2) : '-';
        $priorWithholding = $priorWithholding ?
                            number_format($priorWithholding, 2) : '-';
        $priorNetAmount = $priorNetAmount ?
                          number_format($priorNetAmount, 2) : '-';
        $amountGross = number_format($amountGross, 2);
        $withholding = number_format($withholding, 2);
        $netAmount = number_format($netAmount, 2);

        $data = [
            [
                'col-span' => true,
                'col-span-key' => ['0-1', '2', '3', '4', '5', '6', '7'],
                'aligns' => ['L', 'C', 'C', 'C', 'C', 'C', 'C', 'L'],
                'widths' => [27.3 * $multiplier, 21.94 * $multiplier,
                             10.04 * $multiplier, 9.69 * $multiplier,
                             9.5 * $multiplier, 9.55 * $multiplier,
                             9.5 * $multiplier, 13.6 * $multiplier],
                'font-styles' => ['B', '', '', '', '', '', '', 'B'],
                'type' => 'row-data',
                'data' => [['I. Current Year A/Ps', '', '', '', '', '',
                            '', 'FOR MDS-GSB USE ONLY']]
            ], [
                'aligns' => ['L', 'C', 'C', 'C', 'R', 'R', 'R', 'L'],
                'widths' => [27.3 * $multiplier, 21.94 * $multiplier,
                             10.04 * $multiplier, 9.69 * $multiplier,
                             9.5 * $multiplier, 9.55 * $multiplier,
                             9.5 * $multiplier, 13.6 * $multiplier],
                'font-styles' => ['', '', '', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $currentTableData
            ], [
                'col-span' => true,
                'col-span-key' => ['0-1', '2', '3', '4', '5', '6', '7'],
                'aligns' => ['L', 'C', 'C', 'C', 'R', 'R', 'R', 'L'],
                'widths' => [27.3 * $multiplier, 21.94 * $multiplier,
                             10.04 * $multiplier, 9.69 * $multiplier,
                             9.5 * $multiplier, 9.55 * $multiplier,
                             9.5 * $multiplier, 13.6 * $multiplier],
                'font-styles' => ['B', '', '', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => [['Sub-total', '', '', '', $currAmountGross,
                            $currWithholding, $currNetAmount, '']]
            ], [
                'col-span' => true,
                'col-span-key' => ['0-1', '2', '3', '4', '5', '6', '7'],
                'aligns' => ['L', 'C', 'C', 'C', 'C', 'C', 'C', 'L'],
                'widths' => [27.3 * $multiplier, 21.94 * $multiplier,
                             10.04 * $multiplier, 9.69 * $multiplier,
                             9.5 * $multiplier, 9.55 * $multiplier,
                             9.5 * $multiplier, 13.6 * $multiplier],
                'font-styles' => ['B', '', '', '', '', '', '', 'B'],
                'type' => 'row-data',
                'data' => [["II. Prior Year's A/Ps", '', '', '', '', '',
                            '', '']]
            ], [
                'aligns' => ['L', 'C', 'C', 'C', 'R', 'R', 'R', 'L'],
                'widths' => [27.3 * $multiplier, 21.94 * $multiplier,
                             10.04 * $multiplier, 9.69 * $multiplier,
                             9.5 * $multiplier, 9.55 * $multiplier,
                             9.5 * $multiplier, 13.6 * $multiplier],
                'font-styles' => ['', '', '', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $priorTableData
            ], [
                'col-span' => true,
                'col-span-key' => ['0-1', '2', '3', '4', '5', '6', '7'],
                'aligns' => ['L', 'C', 'C', 'C', 'R', 'R', 'R', 'L'],
                'widths' => [27.3 * $multiplier, 21.94 * $multiplier,
                             10.04 * $multiplier, 9.69 * $multiplier,
                             9.5 * $multiplier, 9.55 * $multiplier,
                             9.5 * $multiplier, 13.6 * $multiplier],
                'font-styles' => ['B', '', '', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => [['Sub-total', '', '', '', $priorAmountGross,
                            $priorWithholding, $priorNetAmount, '']]
            ], [
                'col-span' => true,
                'col-span-key' => ['0-1', '2', '3', '4', '5', '6', '7'],
                'aligns' => ['L', 'C', 'C', 'C', 'R', 'R', 'R', 'L'],
                'widths' => [27.3 * $multiplier, 21.94 * $multiplier,
                             10.04 * $multiplier, 9.69 * $multiplier,
                             9.5 * $multiplier, 9.55 * $multiplier,
                             9.5 * $multiplier, 13.6 * $multiplier],
                'font-styles' => ['B', '', '', '', 'B', 'B', 'B', ''],
                'type' => 'row-data',
                'data' => [['TOTAL', '', '', '', $amountGross, $withholding,
                            $netAmount, '']]
            ]
        ];

        return (object)['lddap' => $lddap,
                        'table_data' => $data,
                       ];
    }

    private function getDataLiquidation($id) {
        $liq = DB::table('liquidation_reports as liq')
                 ->select('liq.*', 'emp.firstname', 'emp.middlename', 'emp.lastname')
                 ->join('emp_accounts as emp', 'emp.emp_id', '=', 'liq.sig_claimant')
                 ->where('liq.id', $id)
                 ->first();
        $liq->dv_no = !empty($liq->dv_no) ? $liq->dv_no: '_______';
        $liq->dv_dtd = !empty($liq->dv_dtd) ? $liq->dv_dtd: '_______';
        $liq->or_no = !empty($liq->or_no) ? $liq->or_no: '_______';
        $liq->or_dtd = !empty($liq->or_dtd) ? $liq->or_dtd: '_______';
        $multiplier = 100 / 90;

        if (strlen($liq->particulars) <= 73) {
            $liq->particulars = $liq->particulars . '<br><br>';
        } else if (strlen($liq->particulars) > 73 && strlen($liq->particulars) <= 150) {
            $liq->particulars = $liq->particulars . '<br>';
        }

        if (!empty($liq->middlename)) {
            $claimant = $liq->firstname . " " . $liq->middlename[0] . ". " . $liq->lastname;
        } else {
            $claimant = $liq->firstname . " " . $liq->lastname;
        }

        $tableData[] = [$liq->particulars,
                        $liq->amount];
        $tableData[] = ['TOTAL AMOUNT SPENT',
                        $liq->total_amount];
        $tableData[] = ['AMOUNT OF CASH ADVANCE PER DV NO. ' .
                        $liq->dv_no .
                        ' DTD. ' . $liq->dv_dtd,
                        $liq->amount_cash_adv];
        $tableData[] = ['AMOUNT REFUNDED PER OR NO. ' .
                        $liq->or_no .
                        ' DTD. ' . $liq->or_dtd,
                        $liq->amount_refunded];
        $tableData[] = ['AMOUNT TO BE REIMBURSED',
                        $liq->amount_reimbursed];
        $data = [
            [
                'aligns' => ['L', 'R'],
                'widths' => [59.85 * $multiplier, 30.15 * $multiplier],
                'font-styles' => ['', ''],
                'type' => 'row-data',
                'data' => $tableData
            ]
        ];

        return (object)['liq' => $liq,
                        'table_data' => $data
                    ];
    }

    private function getDataDV($id, $type) {
        if ($type == 'procurement') {

            $dv = DB::table('disbursement_vouchers as dv')
                    ->select('dv.id as dv_id', 'dv.*', 'ors.payee', 'ors.address', 'ors.amount',
                             'ors.sig_certified_1', 'ors.po_no', 'ors.sig_certified_2', 'bid.company_name',
                             'bid.vat_no as tin', 'ors.responsibility_center', 'ors.mfo_pap')
                    ->join('obligation_request_status as ors', 'ors.id', '=', 'dv.ors_id')
                    ->join('suppliers as bid', 'bid.id', '=', 'ors.payee')
                    ->where('dv.id', $id)
                    ->first();
        } else if ($type == 'cashadvance') {
            $dv = DB::table('disbursement_vouchers as dv')
                    ->select('dv.id as dv_id', 'dv.*', 'emp.emp_id')
                    ->join('emp_accounts as emp', 'emp.id', '=', 'dv.payee')
                    ->where('dv.id', $id)
                    ->first();
        }

        $payee = "";
        $multiplier = 100 / 91.4296;

        if (strpos($dv->particulars, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $dv->particulars = str_replace($searchStr, '<br>', $dv->particulars);
        }

        if (strpos($dv->responsibility_center, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $dv->responsibility_center = str_replace($searchStr, '<br>', $dv->responsibility_center);
        }

        if (strpos($dv->mfo_pap, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $dv->mfo_pap = str_replace($searchStr, '<br>', $dv->mfo_pap);
        }

        $instanceSignatory = new Signatory;
        $sign1 = $instanceSignatory->getSignatory($dv->sig_certified)->name;
        $sign2 = $instanceSignatory->getSignatory($dv->sig_accounting)->name;
        $sign3 = $instanceSignatory->getSignatory($dv->sig_agency_head)->name;
        $position1 = $instanceSignatory->getSignatory($dv->sig_certified)->dv_designation;
        $position2 = $instanceSignatory->getSignatory($dv->sig_accounting)->dv_designation;
        $position3 = $instanceSignatory->getSignatory($dv->sig_agency_head)->dv_designation;

        $tableData[] = [$dv->particulars,
                        $dv->responsibility_center,
                        $dv->mfo_pap, ""];
        $amount = number_format($dv->amount, 2);

        if ($dv->module_class == 3) {
            $payee = $dv->company_name;
        } else if ($dv->module_class == 2) {
            $payee = Auth::user()->getEmployee($dv->payee)->name;
        }

        $dataHeader = [
            [
                'aligns' => ['L', 'L', 'L', 'L'],
                'widths' => [10.4762 * $multiplier, 38.095 * $multiplier,
                             26.19 * $multiplier, 16.6684 * $multiplier],
                'font-styles' => ['B', 'B', '', ''],
                'type' => 'other',
                'data' => [['Payee', $payee,
                            'TIN/Employee No.:<br>_____________________________',
                            'ORS/BURS No.:<br>__________________']]
            ], [
                'col-span' => true,
                'col-span-key' => ['0', '1-3'],
                'aligns' => ['L', 'L', 'L', 'L'],
                'widths' => [10.4762 * $multiplier, 80.9534 * $multiplier, '', ''],
                'font-styles' => ['B', 'B', 'B', 'B'],
                'type' => 'other',
                'data' => [["Address", $dv->address]]
            ]
        ];
        $data = [
            [
                'aligns' => ['C', 'C', 'C', 'C'],
                'widths' => [46.667 * $multiplier, 10.952 * $multiplier,
                             17.143 * $multiplier, 16.667 * $multiplier],
                'font-styles' => ['', '', '', ''],
                'type' => 'row-data',
                'data' => [["Particulars", 'Responsibility Center', 'MFO/PAP', 'Amount']]
            ], [
                'aligns' => ['L', 'C', 'C', 'R'],
                'widths' => [46.667 * $multiplier, 10.952 * $multiplier,
                             17.143 * $multiplier, 16.667 * $multiplier],
                'font-styles' => ['', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData
            ], [
                'aligns' => ['C', 'C', 'C', 'R'],
                'widths' => [46.667 * $multiplier, 10.952 * $multiplier,
                             17.143 * $multiplier, 16.667 * $multiplier],
                'font-styles' => ['B', 'B', 'B', 'B'],
                'type' => 'other',
                'data' => [["Amount Due", "", "", $amount]]
            ]
        ];
        $dataFooter = [
            [
                'aligns' => ['C', 'C', 'C', 'C'],
                'widths' => [47.15 * $multiplier, 17.34 * $multiplier,
                             13.95 * $multiplier, 12.9896 * $multiplier],
                'font-styles' => ['', '', '', ''],
                'type' => 'other',
                'data' => [['Account Title', 'UACS Code', 'Debit', 'Credit']]
            ]
        ];

        return (object)['dv' => $dv,
                        'header_data' => $dataHeader,
                        'table_data' => $data,
                        'footer_data' => $dataFooter,
                        'sign1' => $sign1,
                        'sign2' => $sign2,
                        'sign3' => $sign3,
                        'position1' => $position1,
                        'position2' => $position2,
                        'position3' => $position3];
    }

    private function getDataIAR($id) {
        $tableData = [];
        $iar = DB::table('inspection_acceptance_reports as iar')
                 ->select('ors.*', 'iar.*', 'div.division_name', 'bid.company_name', 'po.date_po')
                 ->join('obligation_request_status as ors', 'ors.id', '=', 'iar.ors_id')
                 ->join('purchase_job_orders as po', 'po.po_no', '=', 'ors.po_no')
                 ->join('purchase_requests as pr', 'pr.id', '=', 'iar.pr_id')
                 ->join('emp_divisions as div', 'div.id', '=', 'pr.division')
                 ->join('suppliers as bid', 'bid.id', '=', 'ors.payee')
                 ->where('iar.id', $id)
                 ->first();
        $iarItems = DB::table('purchase_job_order_items as item')
                      ->join('item_unit_issues as unit', 'unit.id', '=', 'item.unit_issue')
                      ->join('obligation_request_status as ors', 'ors.po_no', '=', 'item.po_no')
                      ->join('inspection_acceptance_reports as iar', 'iar.ors_id', '=', 'ors.id')
                      ->where('iar.iar_no', $iar->iar_no)
                      ->where('item.excluded', 'n')
                      ->get();

        foreach ($iarItems as $key => $item) {
            if (strpos($item->item_description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->item_description = str_replace($searchStr, '<br>', $item->item_description);
            }

            $tableData[] = [$item->stock_no,
                            $item->item_description,
                            $item->unit_name, $item->quantity];
        }

        $data = [
            [
                'aligns' => ['C', 'C', 'C', 'C'],
                'widths' => [15.7895, 45.9105, 18.3, 20],
                'font-styles' => ['B', 'B', 'B', 'B'],
                'type' => 'row-title',
                'data' => [["Stock/<br>Property No.", "Description", "Unit", "Quantity"]]
            ], [
                'aligns' => ['C', 'L', 'C', 'C'],
                'widths' => [15.7895, 45.9105, 18.3, 20],
                'font-styles' => ['', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData
            ], [
                'aligns' => ['C', 'L', 'C', 'C'],
                'widths' => [15.7895, 45.9105, 18.3, 20],
                'font-styles' => ['', '', '', ''],
                'type' => 'other',
                'data' => [['', '', '', '']]
            ]
        ];
        $dataFooter = [
            [
                'aligns' => ['C', 'C'],
                'widths' => [50, 50],
                'font-styles' => ['', ''],
                'type' => 'other',
                'data' => [["INSPECTION", "ACCEPTANCE"]]
            ]
        ];

        $instanceSignatory = new Signatory;
        $iar->sig_inspection = $instanceSignatory->getSignatory($iar->sig_inspection)->name;
        $iar->sig_supply = $instanceSignatory->getSignatory($iar->sig_supply)->name;

        return (object)['iar' => $iar,
                        'table_data' => $data,
                        'footer_data' => $dataFooter];
    }

    private function getDataORSBURS($id, $type) {
        if ($type == 'procurement') {
            $ors = DB::table('obligation_request_status as ors')
                     ->select('ors.*', 'bid.company_name', 'ors.id as ors_id')
                     ->join('suppliers as bid', 'bid.id', '=', 'ors.payee')
                     ->where([['ors.id', $id], ['ors.module_class', 3]])
                     ->first();
            $payee = $ors->company_name;
        } else if ($type == 'cashadvance'){
            $ors = DB::table('obligation_request_status as ors')
                     ->select('ors.*', 'emp.firstname', 'emp.middlename', 'emp.lastname')
                     ->join('emp_accounts as emp', 'emp.id', '=', 'ors.payee')
                     ->where([['ors.id', $id], ['ors.module_class', 2]])
                     ->first();
            $payee = "";

            if (!empty($ors->middlename)) {
                $payee = $ors->firstname . " " . $ors->middlename[0] . ". " . $ors->lastname;
            } else {
                $payee = $ors->firstname . " " . $ors->lastname;
            }
        }

        $instanceSignatory = new Signatory;
        $sign1 = $instanceSignatory->getSignatory($ors->sig_certified_1)->name;
        $sign2 = $instanceSignatory->getSignatory($ors->sig_certified_2)->name;
        $position1 = $instanceSignatory->getSignatory($ors->sig_certified_1)->ors_designation;
        $position2 = $instanceSignatory->getSignatory($ors->sig_certified_2)->ors_designation;
        $sDate1 = $ors->date_certified_1;
        $sDate2 = $ors->date_certified_2;

        if ($ors->document_type == 'ors') {
            $statusOf[] = ['C.', 'STATUS OF OBLIGATION', '', '', '', '', '', ''];
            $tableHeaderOnFooter[] = ["Date", "Particulars", "ORS/JEV/Check/ADA/TRA No.",
                                      "Obligation <br><br>(a)", "Payable <br><br>(b)",
                                      "Payment <br><br>(c)", "Not Yet Due <br><br>(a-b)",
                                      "Due and Demandable <br>(b-c)"];
        } else {
            $statusOf[] = ['C.', 'STATUS OF UTILIZATION', '', '', '', '', '', ''];
            $tableHeaderOnFooter[] = ["Date", "Particulars", "BURS/JEV/RCI/RADAI/RTRAI No.",
                                      "Utilization <br><br>(a)", "Payable <br><br>(b)",
                                      "Payment <br><br>(c)", "Not Yet Due <br><br>(a-b)",
                                      "Due and Demandable <br>(b-c)"];
        }

        $multiplier = 100 / 91.427;
        $itemAmount = number_format($ors->amount, 2);

        if (strpos($ors->responsibility_center, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $ors->responsibility_center = str_replace($searchStr, '<br>', $ors->responsibility_center);
        }

        if (strpos($ors->particulars, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $ors->particulars = str_replace($searchStr, '<br>', $ors->particulars);
        }

        if (strpos($ors->mfo_pap, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $ors->mfo_pap = str_replace($searchStr, '<br>', $ors->mfo_pap);
        }

        $tableData[] = [$ors->responsibility_center,
                        $ors->particulars,
                        $ors->mfo_pap,
                        $ors->uacs_object_code, $itemAmount];

        for ($i = 1; $i <= 6 ; $i++) {
            $obligationValue = '';

            if ($i == 1) {
                $obligationValue = $itemAmount;
            } else {
                $obligationValue = '';
            }

           $statObligations[] = ['', '', '', $obligationValue, '', '', '', ''];
        }

        $dataHeader = [
            [
                'aligns' => ['C', 'L'],
                'widths' => [16.19 * $multiplier, 75.24 * $multiplier],
                'font-styles' => ['', 'B'],
                'type' => 'other',
                'data' => [["Payee", strtoupper($payee)],
                           ["Office", $ors->office],
                           ["Address", $ors->address]]
            ]
        ];
        $data = [
            [
                'aligns' => ['C', 'C', 'C', 'C', 'C'],
                'widths' => [16.19 * $multiplier, 30.5 * $multiplier, 11.05 * $multiplier,
                             14.29 * $multiplier, 19.40 * $multiplier],
                'font-styles' => ['', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => [["Responsibility<br>Center", 'Particulars', 'MFO/PAP', 'UACS Object Code', 'Amount']]
            ], [
                'aligns' => ['C', 'L', 'L', 'C', 'R'],
                'widths' => [16.19 * $multiplier, 30.5 * $multiplier, 11.05 * $multiplier,
                             14.29 * $multiplier, 19.40 * $multiplier],
                'font-styles' => ['', '', '', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData
            ], [
                'aligns' => ['C', 'C', 'C', 'C', 'R'],
                'widths' => [16.19 * $multiplier, 30.5 * $multiplier, 11.05 * $multiplier,
                             14.29 * $multiplier, 19.40 * $multiplier],
                'font-styles' => ['', 'B', '', '', 'B'],
                'type' => 'other',
                'data' => [['', 'Total', '', '', $itemAmount]]
            ]
        ];
        $dataFooter = [
            [
                'col-span' => true,
                'col-span-key' => ['0', '1-7'],
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier],
                'font-styles' => ['', '', '', '', '', '', '', ''],
                'type' => 'other',
                'data' => [['', '', '', '', '', '', '', '']]
            ], [
                'col-span' => true,
                'col-span-key' => ['0', '1-7'],
                'aligns' => ['L', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier],
                'font-styles' => ['B', 'B', '', '', '', '', '', ''],
                'type' => 'other',
                'data' => $statusOf
            ], [
                'col-span' => true,
                'col-span-key' => ['0-2', '3-7'],
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier],
                'font-styles' => ['B', '', '', 'B', '', '', '', ''],
                'type' => 'other',
                'data' => [['Reference', '', '', 'Amount', '', '', '', '']]
            ], [
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier],
                'font-styles' => ['', '', '', '', '', '', '', ''],
                'type' => 'other',
                'data' => $tableHeaderOnFooter
            ], [
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [9.524 * $multiplier, 12.857 * $multiplier, 14.286 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier, 10.952 * $multiplier,
                             10.952 * $multiplier, 10.952 * $multiplier],
                'font-styles' => ['', '', '', 'B', '', '', '', ''],
                'type' => 'other',
                'data' => $statObligations
            ]
        ];

        return (object)[
            'ors' => $ors,
            'header_data' => $dataHeader,
            'table_data' => $data,
            'footer_data' => $dataFooter,
            'sign1' => $sign1,
            'sign2' => $sign2,
            'position1' => $position1,
            'position2' => $position2,
            'sDate1' => $sDate1,
            'sDate2' => $sDate2
        ];
    }

    private function getDataPOJO($id) {
        $tableData = [];
        $grandTotal = 0;
        $po = DB::table('purchase_job_orders as po')
                ->select('po.*', 'bid.company_name', 'bid.address', 'mode.mode_name')
                ->join('suppliers as bid', 'bid.id', '=', 'po.awarded_to')
                ->join('abstract_quotations as abs', 'abs.pr_id', '=', 'po.pr_id')
                ->join('procurement_modes as mode', 'mode.id', '=', 'abs.mode_procurement')
                ->where('po.id', $id)
                ->first();
        $documentType = $po->document_type;
        $poNo = $po->po_no;
        $items = DB::table('purchase_job_order_items as item')
                     ->join('item_unit_issues as unit', 'unit.id', '=', 'item.unit_issue')
                     ->where([['item.po_no', $poNo]])
                     ->where('item.excluded', 'n')
                     ->orderBy('item.item_no')
                     ->get();

        if ($documentType == 'po') {
            foreach ($items as $key => $item) {
                if (strpos($item->item_description, "\n") !== FALSE) {
                    $searchStr = ["\r\n", "\n", "\r"];
                    $item->item_description = str_replace($searchStr, '<br>', $item->item_description);
                }

                $tableData[] = [$key + 1,
                                $item->unit_name,
                                $item->item_description,
                                $item->quantity,
                                number_format($item->unit_cost, 2),
                                number_format($item->total_cost, 2)];
                $grandTotal += floatval($item->total_cost);
            }

            $grandTotal = number_format($grandTotal, 2);
            $tableHeader = [
                [
                    'aligns' => ['L', 'L', 'L', 'L'],
                    'widths' => [17, 43.5, 15, 24.5],
                    'font-styles' => ['', 'B', '', 'B'],
                    'type' => 'other',
                    'data' => [['Place of Delivery: ', $po->place_delivery,
                                'Delivery Term: ', $po->delivery_term]]
                ], [
                    'aligns' => ['L', 'L', 'L', 'L'],
                    'widths' => [17, 43.5, 15, 24.5],
                    'font-styles' => ['', '', '', ''],
                    'type' => 'other',
                    'data' => [['Date of Delivery: ', $po->date_delivery,
                                'Payment Term: ', $po->payment_term]]
                ]
            ];
            $data = [
                [
                    'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
                    'widths' => [12, 10, 38.5, 13, 13.25, 13.25],
                    'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
                    'type' => 'row-title',
                    'data' => [["Stock/<br>Property No.",'Unit','Description',
                                'Quantity','Unit Cost','Amount']]
                ], [
                    'aligns' => ['C', 'C', 'L', 'C', 'R', 'R'],
                    'widths' => [12, 10, 38.5, 13, 13.25, 13.25],
                    'font-styles' => ['', '', '', '', '', ''],
                    'type' => 'row-data',
                    'data' => $tableData
                ], [
                    'aligns' => ['L', 'L', 'L', 'L', 'L', 'R'],
                    'widths' => [12, 10, 38.5, 13, 13.25, 13.25],
                    'font-styles' => ['', '', '', '', '', ''],
                    'type' => 'other',
                    'data' => [['', '', '', '', '', ''],
                               ['', '', '', '', '', '']]
                ], [
                    'col-span' => true,
                    'col-span-key' => ['0-1', '2-4', '5'],
                    'aligns' => ['L', 'L', 'L', 'L', 'L', 'R'],
                    'widths' => [12, 10, 38.5, 13, 13.25, 13.25],
                    'font-styles' => ['', '', '', '', '', ''],
                    'type' => 'other',
                    'data' => [['(Total Amount in Words)', '',
                                $po->amount_words, '', '',
                                $grandTotal]]
                ]
            ];

            $po->table_header = $tableHeader;
            $po->table_data = $data;
            $po->grand_total = $grandTotal;

            return (object)[
                'po' => $po,
                'toggle' => $documentType
            ];
        } else {
            foreach ($items as $key => $item) {
                if (strpos($item->item_description, "\n") !== FALSE) {
                    $searchStr = ["\r\n", "\n", "\r"];
                    $item->item_description = str_replace($searchStr, '<br>', $item->item_description);
                }

                $joUnit = $item->quantity . " " . $item->unit_name;
                $tableData[] = [$joUnit,
                                $item->item_description,
                                number_format($item->total_cost, 2)];
                $grandTotal += floatval($item->total_cost);
            }

            $grandTotal = number_format($grandTotal, 2);
            $data = [
                [
                    'aligns' => ['C','C','C'],
                    'widths' => [16.5, 54.2, 29.3],
                    'font-styles' => ['B', 'B', 'B'],
                    'type' => 'row-title',
                    'data' => [['', 'JOB/WORK DESCRIPTION', 'Amount']]
                ], [
                    'aligns' => ['C', 'L', 'R'],
                    'widths' => [16.5, 54.2, 29.3],
                    'font-styles' => ['', '', ''],
                    'type' => 'row-data',
                    'data' => $tableData
                ], [
                    'aligns' => ['C', 'C', 'C'],
                    'widths' => [16.5, 54.2, 29.3],
                    'font-styles' => ['', 'B', ''],
                    'type' => 'other',
                    'data' => [['', '****** Nothing Follows ******', ''],
                               ['', '', '']]
                ], [
                    'col-span' => true,
                    'col-span-key' => ['0-1', '2'],
                    'aligns' => ['C', 'C', 'R'],
                    'widths' => [16.5, 54.2, 29.3],
                    'font-styles' => ['BI', 'BI', 'B'],
                    'type' => 'other',
                    'data' => [['TOTAL AMOUNT', '', $grandTotal]]
                ]
            ];

            $po->table_data = $data;
            $po->grand_total = $grandTotal;

            return (object)[
                'jo' => $po,
                'toggle' => $documentType
            ];
        }
    }

    private function getDataAbstract($id, $pageHeight) {
        $abstract = DB::table('abstract_quotations as abstract')
                      ->join('purchase_requests as pr', 'pr.id', '=', 'abstract.pr_id')
                      ->join('procurement_modes as mode', 'mode.id', '=', 'abstract.mode_procurement')
                      ->where('abstract.id', $id)->first();
        $items = $this->getItemGroup($abstract->pr_id);
        $prData = $this->getDataPR($abstract->pr_id)->pr;

        $instanceSignatory = new Signatory;
        $chairperson = $instanceSignatory->getSignatory($abstract->sig_chairperson);
        $viceChairperson = $instanceSignatory->getSignatory($abstract->sig_vice_chairperson);
        $member1 = $instanceSignatory->getSignatory($abstract->sig_first_member);
        $member2 = $instanceSignatory->getSignatory($abstract->sig_second_member);
        $member3 = $instanceSignatory->getSignatory($abstract->sig_third_member);
        $endUser = Auth::user()->getEmployee($abstract->sig_end_user);

        foreach ($items as $item) {
            $arraySuppliers = [];
            $tableData = [];
            $suppliers = DB::table('abstract_quotation_items as abs')
                           ->select('bid.id', 'bid.company_name')
                           ->join('purchase_request_items as item', 'item.id', '=', 'abs.pr_item_id')
                           ->join('suppliers as bid', 'bid.id', '=', 'abs.supplier')
                           ->where([['item.group_no', $item->group_no],
                                    ['item.pr_id', $abstract->pr_id]])
                           ->orderBy('bid.company_name')
                           ->distinct()
                           ->get();
            $pritems = DB::table('purchase_request_items as item')
                         ->select('bid.company_name', 'item.awarded_remarks', 'item.quantity',
                                  'unit.unit_name', 'item.id as item_id', 'item.est_unit_cost',
                                  'item.item_description')
                         ->leftJoin('suppliers as bid', 'bid.id', '=', 'item.awarded_to')
                         ->join('item_unit_issues as unit', 'unit.id', '=', 'item.unit_issue')
                         ->where([['item.group_no', $item->group_no],
                                  ['item.pr_id', $abstract->pr_id]])
                         ->orderBy('item.item_no')
                         ->get();
            $bidderCount = $this->getBidderCount($item->group_no, $abstract->pr_id);

            foreach ($suppliers as $bid) {
                $arraySuppliers[] = (object)['company_name' => $bid->company_name];
            }

            foreach ($pritems as $ctrItem => $pr) {
                //$arrayAbstractItems = [];
                if (strpos($pr->item_description, "\n") !== FALSE) {
                    $searchStr = ["\r\n", "\n", "\r"];
                    $pr->item_description = str_replace($searchStr, '<br>', $pr->item_description);
                }

                $abstractItems = DB::table('abstract_quotation_items as abs')
                                   ->join('purchase_request_items as item', 'item.id', '=', 'abs.pr_item_id')
                                   ->join('suppliers as bid', 'bid.id', '=', 'abs.supplier')
                                   ->where([['item.pr_id', $abstract->pr_id],
                                            ['item.id', $pr->item_id]])
                                   ->orderBy('bid.company_name')
                                   ->get();
                $_tableData = [$ctrItem + 1,
                               $pr->quantity,
                               $pr->unit_name,
                               $pr->item_description,
                               number_format($pr->est_unit_cost, 2)];

                foreach ($abstractItems as $abs) {
                    $_tableData[] = number_format($abs->unit_cost, 2);
                    $_tableData[] = number_format($abs->total_cost, 2);

                    if (strpos($abs->remarks, "\n") !== FALSE) {
                        $searchStr = ["\r\n", "\n", "\r"];
                        $abs->remarks = str_replace($searchStr, '<br>', $abs->remarks);
                    }

                    if (strpos($abs->specification, "\n") !== FALSE) {
                        $searchStr = ["\r\n", "\n", "\r"];
                        $abs->specification = str_replace($searchStr, '<br>', $abs->specification);
                    }

                    if (!empty($abs->remarks)) {
                        $_tableData[] = $abs->specification . "<br>*Remarks: $abs->remarks";
                    } else {
                        $_tableData[] = $abs->specification;
                    }

                    /*
                    $arrayAbstractItems[] = (object)['unit_cost' => $abs->unit_cost,
                                                     'total_cost' => $abs->total_cost,
                                                     'specification' => $abs->specification,
                                                     'remarks' => $abs->remarks];*/
                }

                if (!empty($pr->awarded_remarks)) {
                    $_tableData[] = $pr->company_name . " ($pr->awarded_remarks)";
                } else {
                    $_tableData[] = $pr->company_name;
                }

                $tableData[] =  $_tableData;

            }

            $totalWidthDisplay = $pageHeight - 20;
            $totalWidth1 = $totalWidthDisplay * 0.83;
            $totalWidth2 = $totalWidthDisplay * 0.17;
            $bidderTotalWidth = $totalWidth1 * 0.71;

            if ($bidderCount != 0) {
                $bidderWidth = $bidderTotalWidth / $bidderCount;
                $bWidth = 58.92 / $bidderCount;
            } else {
                $bidderWidth = $bidderTotalWidth / 3;
                $bWidth = 58.92 / 3;
            }

            $columnWidths = [3.32, 3.32, 3.32, 10.8, 3.32];
            $aligns = ['R', 'R', 'L', 'L', 'R'];
            $fontStyles = ['', '', '', '', ''];

            for ($i = 1; $i <= $bidderCount; $i++) {
                $columnWidths[] = $bWidth * 0.25;
                $columnWidths[] = $bWidth * 0.25;
                $columnWidths[] = $bWidth * 0.5;
                $aligns[] = "C";
                $aligns[] = "C";
                $aligns[] = "L";
                $fontStyles[] = "";
                $fontStyles[] = "";
                $fontStyles[] = "";
            }

            $columnWidths[] = 17;
            $aligns[] = 'C';
            $fontStyles[] = "";

            $data = [
                [
                    'aligns' => $aligns,
                    'widths' => $columnWidths,
                    'font-styles' => $fontStyles,
                    'type' => 'row-data',
                    'data' => $tableData
                ], [
                    'aligns' => $aligns,
                    'widths' => $columnWidths,
                    'font-styles' => $fontStyles,
                    'type' => 'other',
                    'data' => [$fontStyles]
                ]
            ];

            $item->suppliers = (object)$arraySuppliers;
            $item->table_data = $data;
            $item->bidder_count = $bidderCount;
        }

        return (object)['pr' => $prData,
                        'abstract' => $abstract,
                        'abstract_items' => $items,
                        'sig_chairperson' => $chairperson,
                        'sig_vice_chairperson' => $viceChairperson,
                        'sig_first_member' => $member1,
                        'sig_second_member' => $member2,
                        'sig_third_member' => $member3,
                        'sig_end_user' => $endUser];
    }

    private function getDataRFQ($id) {
        $instanceRFQ = RequestQuotation::find($id);
        $prID = $instanceRFQ->pr_id;
        $instancePR = $this->getDataPR($prID)->pr;
        $instanceSignatory = new Signatory;
        $sigRFQ = $instanceSignatory->getSignatory($instanceRFQ->sig_rfq);
        $groupNumbers = $this->getItemGroup($prID);

        foreach ($groupNumbers as $groupNo) {
            $tableData = [];
            $prItems = DB::table('purchase_request_items as item')
                         ->join('item_unit_issues as unit', 'unit.id', '=', 'item.unit_issue')
                         ->where([['item.pr_id', $prID], ['item.group_no', $groupNo->group_no]])
                         ->orderBy('item.item_no')
                         ->get();

            foreach ($prItems as $key => $item) {
                if (strpos($item->item_description, "\n") !== FALSE) {
                    $searchStr = ["\r\n", "\n", "\r"];
                    $item->item_description = str_replace($searchStr, '<br>', $item->item_description);
                }

                $tableData[] = (object)[$key + 1,
                                        $item->quantity,
                                        $item->unit_name,
                                        $item->item_description,
                                        number_format($item->est_unit_cost, 2),
                                        ''];
            }

            $multiplier = 100 / 92.35;
            $data = [
                [
                    'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
                    'widths' => [$multiplier * 7.14, $multiplier * 6.19,
                                 $multiplier * 7.62, $multiplier * 44.3,
                                 $multiplier * 15.2, $multiplier * 11.9],
                    'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
                    'type' => 'row-title',
                    'data' => [['ITEM NO.', 'QTY', 'UNIT', 'ARTICLES/PARTICULARS',
                                'Approved Budget for the Contract', 'UNIT PRICE']]],
                [
                    'aligns' => ['C','C','C','L','R','R'],
                    'widths' => [$multiplier * 7.14, $multiplier * 6.19,
                                 $multiplier * 7.62, $multiplier * 44.3,
                                 $multiplier * 15.2, $multiplier * 11.9],
                    'font-styles' => ['', '', '', '', '', ''],
                    'type' => 'row-data',
                    'data' => $tableData],
                [
                    'aligns' => ['C','C','C','C','R','R'],
                    'widths' => [$multiplier * 7.14, $multiplier * 6.19,
                                 $multiplier * 7.62, $multiplier * 44.3,
                                 $multiplier * 15.2, $multiplier * 11.9],
                    'font-styles' => ['', '', '', '', '', ''],
                    'type' => 'other',
                    'data' => [['', '', '', '', '', '']]
                ]
            ];

            $groupNo->table_data = (object)$data;
        }

        return (object)['pr' => $instancePR,
                        'group_no' => $groupNumbers,
                        'rfq' => $instanceRFQ,
                        'sig_rfq' => $sigRFQ];
    }

    private function getDataPR($id) {
        $tableData = [];
        $total = 0;
        $instancePR = PurchaseRequest::find($id);
        $instanceSignatory = new Signatory;
        $prItems = DB::table('purchase_request_items as item')
                     ->join('item_unit_issues as unit', 'unit.id', '=', 'item.unit_issue')
                     ->where('item.pr_id', $id)
                     ->orderBy('item.item_no')
                     ->get();
        $requestedBy = Auth::user()->getEmployee($instancePR->requested_by);
        $approvedBy = $instanceSignatory->getSignatory($instancePR->approved_by);
        $recommendedBy = $instanceSignatory->getSignatory($instancePR->recommended_by);

        foreach ($prItems as $item) {
            if (strpos($item->item_description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->item_description = str_replace($searchStr, '<br>', $item->item_description);
            }

            $tableData[] = ['',
                            $item->unit_name,
                            $item->item_description,
                            $item->quantity,
                            number_format($item->est_unit_cost, 2),
                            number_format($item->est_total_cost, 2)];
            $total += $item->est_total_cost;
        }

        $multiplier = 100 / 91.52;
        $total = number_format($total, 2);
        $data = [
            [
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [$multiplier * 13.8, $multiplier * 8.85,
                             $multiplier * 28.05, $multiplier * 9.52,
                             $multiplier * 15.65, $multiplier * 15.65],
                'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
                'type' => 'row-title',
                'data' => [['Stock/Property No.', 'Unit', 'Item Description',
                            'Quantity', 'Unit Cost', 'Total Cost']]],
            [
                'aligns' => ['C', 'C', 'L', 'C', 'R', 'R'],
                'widths' => [$multiplier * 13.8, $multiplier * 8.85,
                             $multiplier * 28.05, $multiplier * 9.52,
                             $multiplier * 15.65, $multiplier * 15.65],
                'font-styles' => ['', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData],
            [
                'aligns' => ['C', 'C', 'C', 'C', 'L', 'R'],
                'widths' => [$multiplier * 13.8, $multiplier * 8.85,
                             $multiplier * 28.05, $multiplier * 9.52,
                             $multiplier * 15.65, $multiplier * 15.65],
                'font-styles' => ['', '', '', '', 'B', 'B'],
                'type' => 'other',
                'data' => [['', '', '', '', '', ''],
                           ['', '', '', '', 'Total', $total]]
            ]
        ];

        return (object)['pr' => $instancePR,
                        'table_data' => $data,
                        'requested_by' => $requestedBy,
                        'approved_by' => $approvedBy,
                        'recommended_by' => $recommendedBy];
    }

    private function setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                                     $docCreator = "DOST-CAR", $docAuthor = "DOST-CAR",
                                     $docSubject = "", $docKeywords = "") {
        //Header information
        $pdf->setDocCode($docCode);
        $pdf->setDocRevision($docRev);
        $pdf->setRevDate($docRevDate);

        //Main information
        $pdf->SetTitle($docTitle);
        $pdf->SetCreator($docCreator);
        $pdf->SetAuthor($docAuthor);
        $pdf->SetSubject($docSubject);
        $pdf->SetKeywords($docKeywords);
    }

    private function printDocument($pdf, $docTitle, $previewToggle) {
        if ($previewToggle == 'download') {
            $pdf->Output($docTitle . '.pdf', 'D');
        } else {
            $pdf->Output($docTitle . '.pdf', 'I');
        }
    }

    private function generatePR($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocPurchaseRequest('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-PUR F05";
        $docRev = "Revision 2";
        $docRevDate = "05-24-19";
        $docTitle = "pr_" . $data->pr->pr_no;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Purchase Request";
        $docKeywords = "PR, pr, purchase, request, purchase request";

        if (!empty($data->pr->date_pr)) {
            $data->pr->date_pr = new DateTime($data->pr->date_pr);
            $data->pr->date_pr = $data->pr->date_pr->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printPurchaseRequest($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateRFQ($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocRequestQuotation('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-PUR F06";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = "rfq_" . $data->pr->pr_no;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Request for Quotation";
        $docKeywords = "RFQ, rfq, quotation, request, request for quotation";

        if (!empty($data->rfq->date_canvass)) {
            $data->rfq->date_canvass = new DateTime($data->rfq->date_canvass);
            $data->rfq->date_canvass = $data->rfq->date_canvass->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printRequestQuotation($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateAbstract($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocAbstractQuotation('L', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-PUR F07";
        $docRev = "Revision 2";
        $docRevDate = "11-16-18";
        $docTitle = "abstract_" . $data->pr->pr_no;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Abstract of Quotation";
        $docKeywords = "Abstract, abstract, quotation, abstract of quotation";

        if (!empty($data->abstract->date_abstract)) {
            $data->abstract->date_abstract = new DateTime($data->abstract->date_abstract);
            $data->abstract->date_abstract = $data->abstract->date_abstract->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printAbstractQuotation($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generatePO($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocPurchaseOrder('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-PUR F08";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = "po_" . $data->po->po_no;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Purchase Order";
        $docKeywords = "PO, po, purchase, order, purchase order";

        $instanceSignatory = new Signatory;
        $data->po->sig_approval = $instanceSignatory->getSignatory($data->po->sig_approval)->name;
        $data->po->sig_funds_available = $instanceSignatory->getSignatory($data->po->sig_funds_available)->name;

        if (!empty($data->po->date_po)) {
            $data->po->date_po = new DateTime($data->po->date_po);
            $data->po->date_po = $data->po->date_po->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printPurchaseOrder($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateJO($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocJobOrder('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-PUR F15";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = "jo_" . $data->jo->po_no;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Job Order";
        $docKeywords = "JO, jo, job, order, job order";

        $instanceSignatory = new Signatory;
        $data->jo->sig_department = $instanceSignatory->getSignatory($data->jo->sig_department)->name;
        $data->jo->sig_approval = $instanceSignatory->getSignatory($data->jo->sig_approval)->name;
        $data->jo->sig_funds_available = $instanceSignatory->getSignatory($data->jo->sig_funds_available)->name;

        if (!empty($data->jo->date_po)) {
            $data->jo->date_po = new DateTime($data->jo->date_po);
            $data->jo->date_po = $data->jo->date_po->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printJobOrder($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateORS($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocObligationRequestStatus('P', $pageUnit, $pageSize);
        $pdf->setHeaderLR(false, true);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-BUD F04";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = "ors_" . $data->ors->id;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Obligation Request and Status";
        $docKeywords = "ORS, ors, obligated, request, status, obligation request and status";

        if (!empty($data->ors->date_ors_burs)) {
            $data->ors->date_ors_burs = new DateTime($data->ors->date_ors_burs);
            $data->ors->date_ors_burs = $data->ors->date_ors_burs->format('F j, Y');
        }

        if (!empty($data->sDate1)) {
            $data->sDate1 = new DateTime($data->sDate1);
            $data->sDate1 = $data->sDate1->format('F j, Y');
        }

        if (!empty($data->sDate2)) {
            $data->sDate2 = new DateTime($data->sDate2);
            $data->sDate2 = $data->sDate2->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printORSBURS($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateBURS($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocObligationRequestStatus('P', $pageUnit, $pageSize);
        $pdf->setHeaderLR(false, true);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-BUD F06";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = "burs_" . $data->ors->id;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Budget Utilization Request and Status";
        $docKeywords = "BURS, burs, budget, utilization, request, status, budget utilization request status";

        if (!empty($data->ors->date_ors_burs)) {
            $data->ors->date_ors_burs = new DateTime($data->ors->date_ors_burs);
            $data->ors->date_ors_burs = $data->ors->date_ors_burs->format('F j, Y');
        }

        if (!empty($data->sDate1)) {
            $data->sDate1 = new DateTime($data->sDate1);
            $data->sDate1 = $data->sDate1->format('F j, Y');
        }

        if (!empty($data->sDate2)) {
            $data->sDate2 = new DateTime($data->sDate2);
            $data->sDate2 = $data->sDate2->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printORSBURS($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateIAR($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocInspectionAcceptanceReport('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-PUR F09";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = "iar_" . $data->iar->po_no;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Inspection and Acceptance Report";
        $docKeywords = "IAR, iar, inspection, acceptance, report, inspection and acceptance report";

        if (!empty($data->iar->date_po)) {
            $data->iar->date_po = new DateTime($data->iar->date_po);
            $data->iar->date_po = $data->iar->date_po->format('F j, Y');
        }

        if (!empty($data->iar->date_iar)) {
            $data->iar->date_iar = new DateTime($data->iar->date_iar);
            $data->iar->date_iar = $data->iar->date_iar->format('F j, Y');
        }

        if (!empty($data->iar->date_invoice)) {
            $data->iar->date_invoice = new DateTime($data->iar->date_invoice);
            $data->iar->date_invoice = $data->iar->date_invoice->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printIAR($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateDV($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocDisbursementVoucher('P', 'mm', $pageSize);
        $pdf->setHeaderLR(false, true);
        $docCode = ($data->dv->module_class == 3) ? 'FM-FAS-BUD F12': 'FM-FAS-ACCTG F01';
        $docRev = ($data->dv->module_class == 3) ? 'Revision 1': 'Revision 0';
        $docRevDate = ($data->dv->module_class == 3) ? '02-28-18': '08-31-17';
        $docTitle = "dv_" . $data->dv->id;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Disbursement Voucher";
        $docKeywords = "DV, dv, disbursement, voucher, disbursement voucher";

        if (!empty($data->dv->date_dv)) {
            $data->dv->date_dv = new DateTime($data->dv->date_dv);
            $data->dv->date_dv = $data->dv->date_dv->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printDV($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateLiquidation($data, $documentType, $fontScale, $pageHeight, $pageWidth, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new PDF('P', 'mm', $pageSize);
        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = "lr_" . $data->liq->id;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Liquidation Report";
        $docKeywords = "LR, lr, liquidation, report, liquidation report";

        $serialNo = $data->liq->serial_no;
        $dateLiquidation = $data->liq->date_liquidation;
        $claimant = $this->getEmployee($data->liq->sig_claimant)->name;
        $supervisor = $this->getSignatory($data->liq->sig_supervisor)->name;
        $accounting = $this->getSignatory($data->liq->sig_accounting)->name;
        $dateClaimant = $data->liq->date_claimant;
        $dateSupervisor = $data->liq->date_supervisor;
        $dateAccountant = $data->liq->date_accounting;

        if (empty($serialNo)) {
            $serialNo = '______________';
        }

        if (!empty($dateLiquidation)) {
            $dateLiquidation = new DateTime($dateLiquidation);
            $dateLiquidation = $dateLiquidation->format('F j, Y');
        }

        if (!empty($dateClaimant)) {
            $dateClaimant = new DateTime($dateClaimant);
            $dateClaimant = $dateClaimant->format('F j, Y');
        } else {
            $dateClaimant = ' ______________________';
        }

        if (!empty($dateSupervisor)) {
            $dateSupervisor = new DateTime($dateSupervisor);
            $dateSupervisor = $dateSupervisor->format('F j, Y');
        } else {
            $dateSupervisor = ' ______________________';
        }

        if (!empty($dateAccountant)) {
            $dateAccountant = new DateTime($dateAccountant);
            $dateAccountant = $dateAccountant->format('F j, Y');
        } else {
            $dateAccountant = ' ______________________';
        }

        if (empty($data->liq->jev_no)) {
            $data->liq->jev_no = ' ___________________';
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        include app_path() . "/Classes/DocumentPDF/Documents/doc_liquidation_report.php";

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateLDDAP($data, $documentType, $fontScale, $pageHeight, $pageWidth, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new PDF('P', 'mm', $pageSize);
        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = "lddap_" /*$data->ddap->lddap_id*/;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "List of Due And Demandable Accounts Payable - Advice to Debit Accounts";
        $docKeywords = "LDDAP, lddap, List, Due, Demandable, Accounts, Payable,
                        Advice, Debit, Accounts";

        $department = $data->lddap->department;
        $entityName = $data->lddap->entity_name;
        $operatingUnit = $data->lddap->operating_unit;
        $ncaNo = $data->lddap->nca_no;
        $lddapNo = $data->lddap->lddap_ada_no;
        $fundCluster = $data->lddap->fund_cluster;
        $mdsgsbBranch = $data->lddap->mds_gsb_accnt_no;
        $certCorrect = $this->getSignatory($data->lddap->sig_cert_correct)->name;
        $certCorrectPosition = $this->getSignatory($data->lddap->sig_cert_correct)->position;
        $approval1 = $this->getSignatory($data->lddap->sig_approval_1)->name;
        $approval2 = $this->getSignatory($data->lddap->sig_approval_2)->name;
        $approval3 = $this->getSignatory($data->lddap->sig_approval_3)->name;
        $approvalPosition1 = $this->getSignatory($data->lddap->sig_approval_1)->position;
        $approvalPosition2 = $this->getSignatory($data->lddap->sig_approval_2)->position;
        $approvalPosition3 = $this->getSignatory($data->lddap->sig_approval_3)->position;
        $agencyAuth1 = $this->getSignatory($data->lddap->sig_agency_auth_1)->name;
        $agencyAuth2 = $this->getSignatory($data->lddap->sig_agency_auth_2)->name;
        $agencyAuth3 = $this->getSignatory($data->lddap->sig_agency_auth_3)->name;
        $agencyAuth4 = $this->getSignatory($data->lddap->sig_agency_auth_4)->name;
        $totalAmountWords = $data->lddap->total_amount_words;
        $totalAmount = number_format($data->lddap->total_amount, 2);

        $lddapDate = "";

        if (!empty($data->lddap->lddap_date)) {
            $lddapDate = new DateTime($data->lddap->lddap_date);
            $lddapDate = $lddapDate->format('j F Y');
        }

        /*
        $serialNo = $data->liq->serial_no;
        $dateLiquidation = $data->liq->date_liquidation;
        $claimant = $this->getEmployee($data->liq->sig_claimant)->name;
        $supervisor = $this->getSignatory($data->liq->sig_supervisor)->name;
        $accounting = $this->getSignatory($data->liq->sig_accounting)->name;
        $dateClaimant = $data->liq->date_claimant;
        $dateSupervisor = $data->liq->date_supervisor;
        $dateAccountant = $data->liq->date_accounting;

        if (empty($serialNo)) {
            $serialNo = '______________';
        }

        if (!empty($dateLiquidation)) {
            $dateLiquidation = new DateTime($dateLiquidation);
            $dateLiquidation = $dateLiquidation->format('F j, Y');
        }

        if (!empty($dateClaimant)) {
            $dateClaimant = new DateTime($dateClaimant);
            $dateClaimant = $dateClaimant->format('F j, Y');
        } else {
            $dateClaimant = ' ______________________';
        }

        if (!empty($dateSupervisor)) {
            $dateSupervisor = new DateTime($dateSupervisor);
            $dateSupervisor = $dateSupervisor->format('F j, Y');
        } else {
            $dateSupervisor = ' ______________________';
        }

        if (!empty($dateAccountant)) {
            $dateAccountant = new DateTime($dateAccountant);
            $dateAccountant = $dateAccountant->format('F j, Y');
        } else {
            $dateAccountant = ' ______________________';
        }

        if (empty($data->liq->jev_no)) {
            $data->liq->jev_no = ' ___________________';
        }*/

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        include app_path() . "/Classes/DocumentPDF/Documents/doc_list_due_demandable.php";

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generatePAR($data, $documentType, $fontScale, $pageHeight, $pageWidth, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new PDF('P', 'mm', $pageSize);
        $docCode = "FM-FAS-PUR F10";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = strtolower($data->inventory_no);
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Property Acknowledgement Receipt";
        $docKeywords = "PAR, par, property, acknowledgement, receipt, property acknowledgement receipt";

        $poDate = "";

        if (!empty($data->po->date_po)) {
            $poDate = new DateTime($data->po->date_po);
            $poDate = $poDate->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        include app_path() . "/Classes/DocumentPDF/Documents/doc_property_acknowledgement.php";

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateRIS($data, $documentType, $fontScale, $pageHeight, $pageWidth, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new PDF('P', 'mm', $pageSize);
        $docCode = "FM-FAS-PUR F11";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = strtolower($data->inventory_no);
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Requisition Issue Slip";
        $docKeywords = "RIS, ris, requisition, issue, slip, requisition issue slip";

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        include app_path() . "/Classes/DocumentPDF/Documents/doc_requisition_issue_slip.php";

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateICS($data, $documentType, $fontScale, $pageHeight, $pageWidth, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new PDF('P', 'mm', $pageSize);
        $docCode = "FM-FAS-PUR F16";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = strtolower($data->inventory_no);
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Inventory Custodian Slip";
        $docKeywords = "ICS, ics, inventory, custodian, slip, inventory custodian slip";

        $poDate = "";

        if (!empty($data->po->date_po)) {
            $poDate = new DateTime($data->po->date_po);
            $poDate = $poDate->format('F j, Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        include app_path() . "/Classes/DocumentPDF/Documents/doc_inventory_custodian.php";

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generatePropertyLabel($_data, $docType, $fontScale, $pageHeight, $pageWidth, $previewToggle) {
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new PDF('L', 'mm', $pageSize);
        $pdf->setHeaderLR(false, false);
        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = "property_label";
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Property Label";
        $docKeywords = "property, label, property label";

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        include app_path() . "/Classes/DocumentPDF/Documents/doc_property_label.php";

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }
}
