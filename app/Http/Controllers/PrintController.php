<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\RequestQuotation;
use App\Models\AbstractQuotation;
use App\Models\AbstractQuotationItem;
use App\Models\PurchaseJobOrder;
use App\Models\PurchaseJobOrderItem;
use App\Models\ObligationRequestStatus as OrsBurs;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\InventoryStock;
use App\Models\InventoryStockItem;
use App\Models\InventoryStockIssue;
use App\Models\InventoryStockIssueItem;

use App\Models\DocumentLog as DocLog;
use App\Models\EmpLog;
use App\Models\Signatory;
use App\Models\EmpAccount as User;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\ItemUnitIssue;
use App\Models\EmpDivision;
use App\Models\MdsGsb;
use App\Models\AgencyLGU;
use App\Models\MonitoringOffice;
use App\Models\AllotmentClass;
use App\Models\FundingProject;
use App\Models\MooeAccountTitle;
use App\Models\CustomePayee;
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
use App\Plugins\PDFGenerator\DocLiquidationReport;
use App\Plugins\PDFGenerator\DocListDueDemandable;
use App\Plugins\PDFGenerator\DocSummaryListDueDemandable;
use App\Plugins\PDFGenerator\DocPropertyAcknowledgement;
use App\Plugins\PDFGenerator\DocInventoryCustodian;
use App\Plugins\PDFGenerator\DocRequisitionIssueSlip;
use App\Plugins\PDFGenerator\DocPropertyLabel;
use App\Plugins\PDFGenerator\DocLineItemBudget;
use App\Plugins\PDFGenerator\DocLineItemBudgetRealignment;
use App\Plugins\PDFGenerator\DocRegistryAllotmentsORSDV;

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
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Liquidation Report '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateLR(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'pay_lddap':
                $data = $this->getDataLDDAP($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the List of Due and Demandable Accounts Payable '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateLDDAP(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'pay_summary':
                $data = $this->getDataSummary($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Summary of LDDAP-ADAs Issued and Invalidated ADA Entries '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateSummary(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'inv_par':
                $data = $this->getDataPAR($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Property Acknowledgement Report '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generatePAR(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'inv_ris':
                $data = $this->getDataRIS($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Requisition and Issue Slip '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateRIS(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'inv_ics':
                $data = $this->getDataICS($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Inventory Custodian Slip '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateICS(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'inv_label':
                $data = $this->getDataPropertyLabel($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Property Label '$key' tag.";
                    Auth::user()->log($request, $msg);
                } else {
                    $pageHeight = 53.27;
                    $pageWidth = 103.76125;
                    $this->generatePropertyLabel(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'fund_lib':
                $data = $this->getDataLIB($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Line Item Budgets '$key' document.";
                    Auth::user()->log($request, $msg);
                } else {
                    $this->generateLIB(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'fund_lib_realignment':
                $data = $this->getDataRealignmentLIB($key);
                $data->doc_type = $documentType;

                if ($test == 'true') {
                    $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                    $msg = "Generated the Line Item Budgets '$key' document.";
                    //Auth::user()->log($request, $msg);
                } else {
                    $this->generateLIBRealignment(
                        $data,
                        $fontScale,
                        $pageHeight,
                        $pageWidth,
                        $pageUnit,
                        $previewToggle
                    );
                }
                break;

            case 'report_raod':
                if ($key != 'id') {
                    $data = $this->getDataRAOD($key);
                    $data->doc_type = $documentType;

                    if ($test == 'true') {
                        $instanceDocLog->logDocument($key, Auth::user()->id, NULL, $action);
                        $msg = "Generated the Reggistry of Allotments, Obligations and Disbursement Report '$key' document.";
                        //Auth::user()->log($request, $msg);
                    } else {
                        $this->generateRAOD(
                            $data,
                            $fontScale,
                            $pageHeight,
                            $pageWidth,
                            $pageUnit,
                            $previewToggle
                        );
                    }
                } else {
                    exit();
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

    private function groupAllotments($allotments, $isRealignment = false, $currRealignData = NULL) {
        $groupedAllotments = [];
        $allotClassData = DB::table('allotment_classes')
                            ->orderBy('order_no')
                            ->get();

        if ($isRealignment) {
            $realignOrder = $currRealignData->realignment_order;
            $budgetID = $currRealignData->budget_id;
            $currRealignID = $currRealignData->id;

            $currRealignAllotments = DB::table('funding_allotment_realignments')
                                       ->where('budget_realign_id', $currRealignID)
                                       ->orderBy('order_no')
                                       ->get();

            $_allotments = [];

            foreach ($currRealignAllotments as $realignAllotCtr =>  $realignAllot) {
                $cpCoimplementers = unserialize($realignAllot->coimplementers);

                foreach ($cpCoimplementers as $cpCoimplementer) {
                    $cpCoimplementer['coimplementor_budget'] = 0;
                }

                $_allotments[$realignAllotCtr] = (object) [
                    'allotment_class' => $realignAllot->allotment_class,
                    'allotment_name' => $realignAllot->allotment_name,
                    "realignment_$realignOrder" => (object) [
                        'allotment_cost' => $realignAllot->realigned_allotment_cost,
                        'coimplementers' => unserialize($realignAllot->coimplementers),
                    ],
                    'justification' => $realignAllot->justification,
                ];

                if ($realignAllot->allotment_id) {
                    $allotmentData = DB::table('funding_allotments')
                                       ->where('id', $realignAllot->allotment_id)
                                       ->first();
                    $_allotments[$realignAllotCtr]->allotment_cost = $allotmentData->allotment_cost;
                    $_allotments[$realignAllotCtr]->coimplementers = unserialize($allotmentData->coimplementers);
                } else {
                    $_allotments[$realignAllotCtr]->allotment_cost = 0;
                    $_allotments[$realignAllotCtr]->coimplementers = $cpCoimplementers;
                }

                for ($realignOrderCtr = 1; $realignOrderCtr < $realignOrder; $realignOrderCtr++) {
                    $realignIndex = "realignment_$realignOrderCtr";

                    $budgetRealignData = DB::table('funding_budget_realignments')
                                        ->where([
                                            ['budget_id', $budgetID],
                                            ['realignment_order', $realignOrderCtr]
                                        ])->first();
                    $realignID = $budgetRealignData->id;
                    $realignAllotmentData = DB::table('funding_allotment_realignments')
                                            ->where('budget_realign_id', $realignID)
                                            ->orderBy('order_no')
                                            ->get();
                    $hasRealignAllot = false;

                    foreach ($realignAllotmentData as $rAllotCtr => $rAllot) {
                        if (strtolower(trim($realignAllot->allotment_name)) == strtolower(trim($rAllot->allotment_name)) ||
                            ($realignAllot->allotment_id == $rAllot->allotment_id &&
                            !empty($realignAllot->allotment_id) && !empty($realignAllot->allotment_id))) {
                            $_allotments[$realignAllotCtr]->{$realignIndex} = (object) [
                                'allotment_cost' => $rAllot->realigned_allotment_cost,
                                'coimplementers' => unserialize($rAllot->coimplementers),
                            ];

                            $hasRealignAllot = true;
                            break;
                        }
                    }

                    if (!$hasRealignAllot) {
                        $_allotments[$realignAllotCtr]->{$realignIndex} = (object) [
                            'allotment_cost' => 0,
                            'coimplementers' => $cpCoimplementers,
                        ];
                    }
                }
            }

            $allotments = $_allotments;
        }

        foreach ($allotClassData as $class) {
            foreach ($allotments as $itmCtr => $item) {
                if ($class->id == $item->allotment_class) {
                    $keyClass = preg_replace("/\s+/", "-", $class->class_name);

                    if (count(explode('::', $item->allotment_name)) > 1) {
                        $keyAllotment = preg_replace(
                            "/\s+/", "-", explode('::', $item->allotment_name)[0]
                        );
                        $groupedAllotments[$keyClass][$keyAllotment][] = $item;
                    } else {
                        $groupedAllotments[$keyClass][$itmCtr + 1] = $item;
                    }
                }
            }
        }

        return $groupedAllotments;
    }

    private function getAgencyName($id) {
        $agencyData = AgencyLGU::find($id);
        $agencyName = $agencyData->agency_name;
        return $agencyName;
    }

    private function getMonitoringOfficeName($id) {
        $officeData = MonitoringOffice::find($id);
        $officeName = $officeData->office_name;
        return $officeName;
    }

    function convertToOrdinal($number) {
        $suffix = [
            'th',
            'st',
            'nd',
            'rd',
            'th',
            'th',
            'th',
            'th',
            'th',
            'th'
        ];

        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number. 'th';
        } else {
            return $number. $suffix[$number % 10];
        }
    }


    private static function dateCompare($date1, $date2) {
        $date1 = strtotime($date1['date']);
        $date2 = strtotime($date2['date']);

        return $date1 - $date2;
    }

    private function getDataRAOD($_regAllotIDs) {
        $regAllotIDs = array_filter(explode(';', $_regAllotIDs));
        $mfoPapGrps = [];
        $dates = [];
        $data = [];

        foreach ($regAllotIDs as $regAllotID) {
            $regAllotData = DB::table('funding_reg_allotments')
                                ->where('id', $regAllotID)
                                ->first();
            $mfoPapGrps[] = implode(',', unserialize($regAllotData->mfo_pap));

            $dates[] = [
                'id' => $regAllotID,
                'date' => $regAllotData->period_ending . '-01'
            ];
        }

        $mfoPapGrps = array_unique($mfoPapGrps);
        usort($dates, ['App\Http\Controllers\PrintController', 'dateCompare']);

        foreach ($mfoPapGrps as $papGrp) {
            $mfoPAPs = [];
            $periodEnding = [];
            $entityName = '';
            $fundCluster = '';
            $legalBasis = '';
            $mfoPAP = '';
            $sheetNo = '1';

            $currMonth = '';
            $currTotalAllot = 0;
            $currTotalOblig = 0;
            $currTotalUnoblig = 0;
            $currTotalDisb = 0;
            $currTotalDue = 0;
            $currTotalNotDue = 0;

            $suppliers = DB::table('suppliers')->get();

            $_mfoPAPs = explode(',', $papGrp);

            foreach ($_mfoPAPs as $grpKey => $pap) {
                $mfoPapDat = DB::table('mfo_pap')
                                ->where('id', $pap)
                                ->first();

                if ($mfoPapDat) {
                    $mfoPAPs[] = $mfoPapDat->code;
                }
            }

            $mfoPAP = implode(', ', $mfoPAPs);
            $data[] = (object) [
                'mfo_pap' => $mfoPAP
            ];
            $datKey = count($data) - 1;

            foreach ($dates as $regDat) {
                $itemTableData = [];
                $footerTableData = [];

                $regAllotData = DB::table('funding_reg_allotments')
                                  ->where('id', $regDat['id'])
                                  ->first();

                if (implode(',', unserialize($regAllotData->mfo_pap)) == $papGrp) {
                    $itemTableData = [];
                    $footerTableData = [];
                    $regAllotmentItems = DB::table('funding_reg_allotment_items')
                                           ->where([['reg_allotment_id', $regDat['id']], ['is_excluded', 'n']])
                                           ->orderBy('order_no')
                                           ->get();

                    $data[$datKey]->id[] = $regDat['id'];
                    $periodEnding[] = date_format(date_create($regAllotData->period_ending), 'F Y');
                    $entityName = $regAllotData->entity_name;
                    $fundCluster = $regAllotData->fund_cluster;
                    $legalBasis = $regAllotData->legal_basis;
                    $sheetNo = $regAllotData->sheet_no;

                    $_periodEnding = date_format(date_create($regAllotData->period_ending), 'F Y');
                    $currMonth = strtoupper($_periodEnding);

                    $multiplier = 1;

                    $totalAllot = 0;
                    $totalOblig = 0;
                    $totalUnoblig = 0;
                    $totalDisb = 0;
                    $totalDue = 0;
                    $totalNotDue = 0;

                    foreach ($regAllotmentItems as $ctr => $item) {
                        $payee = Auth::user()->getEmployee($item->payee)->name;
                        $uacsCodes = unserialize($item->uacs_object_code);
                        $uacsObjects = [];

                        if (strpos($item->particulars, "\n") !== FALSE) {
                            $searchStr = ["\r\n", "\n", "\r"];
                            $item->particulars = str_replace($searchStr, '<br>', $item->particulars);
                        }

                        if (!$payee) {
                            foreach ($suppliers as $key => $bid) {
                                if ($bid->id == $item->payee) {
                                    $payee = $bid->company_name;
                                    break;
                                }
                            }
                        }

                        foreach ($uacsCodes as $uacs) {
                            $mooeTitleDat = DB::table('mooe_account_titles')
                                            ->where('id', $uacs)
                                            ->first();

                            if ($mooeTitleDat) {
                                $uacsObjects[] = $mooeTitleDat->uacs_code;
                            }
                        }

                        $uacsObjects = implode(', ', $uacsObjects);

                        $totalAllot += $item->allotments;
                        $totalOblig += $item->obligations;
                        $totalUnoblig += $item->unobligated_allot;
                        $totalDisb += $item->disbursement;
                        $totalDue += $item->due_demandable;
                        $totalNotDue += $item->not_due_demandable;

                        $currTotalAllot += $item->allotments;
                        $currTotalOblig += $item->obligations;
                        $currTotalUnoblig += $item->unobligated_allot;
                        $currTotalDisb += $item->disbursement;
                        $currTotalDue += $item->due_demandable;
                        $currTotalNotDue += $item->not_due_demandable;

                        $item->allotments = number_format($item->allotments, 2);
                        $item->obligations = number_format($item->obligations, 2);
                        $item->unobligated_allot = number_format($item->unobligated_allot, 2);
                        $item->disbursement = number_format($item->disbursement, 2);
                        $item->due_demandable = number_format($item->due_demandable, 2);
                        $item->not_due_demandable = number_format($item->not_due_demandable, 2);

                        $itemTableData[] = [
                            $item->date_received,
                            $item->date_obligated,
                            $item->date_released,
                            $payee,
                            $item->particulars,
                            $item->serial_number,
                            $uacsObjects,
                            $item->allotments,
                            $item->obligations,
                            $item->unobligated_allot,
                            $item->disbursement,
                            $item->due_demandable,
                            $item->not_due_demandable,
                        ];
                    }

                    $totalAllot = number_format($totalAllot, 2);
                    $totalOblig = number_format($totalOblig, 2);
                    $totalUnoblig = number_format($totalUnoblig, 2);
                    $totalDisb = number_format($totalDisb, 2);
                    $totalDue = number_format($totalDue, 2);
                    $totalNotDue = number_format($totalNotDue, 2);

                    $footerTableData[] = [
                        'TOTAL FOR THE MONTH OF '.strtoupper($_periodEnding),
                        '', '', '', '', '', '',
                        $totalAllot,
                        $totalOblig,
                        $totalUnoblig,
                        $totalDisb,
                        $totalDue,
                        $totalNotDue,
                    ];

                    $data[$datKey]->table_data[] = (object) [
                        'table_data' => [
                            [
                                'aligns' => [
                                    'C', 'C', 'C', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'
                                ],
                                'widths' => [
                                    5 * $multiplier, //date_received
                                    5 * $multiplier, //date_obligated
                                    5 * $multiplier, //date_released
                                    9 * $multiplier, //payee
                                    14 * $multiplier, //particulars
                                    7 * $multiplier, //serial_number
                                    7 * $multiplier, //uacs_objects
                                    7 * $multiplier, //allotments
                                    7 * $multiplier, //obligations
                                    7 * $multiplier, //unobligated_allot
                                    7 * $multiplier, //disbursement
                                    10 * $multiplier, //due_demandable
                                    10 * $multiplier, //not_due_demandable
                                ],
                                'font-styles' => [
                                    '', '', '', '', '', '', '', '', '', '', '', '', ''
                                ],
                                'type' => 'row-data',
                                'data' => $itemTableData,
                            ],
                        ],
                        'table_footer' => [
                            [
                                'col-span' => true,
                                'col-span-key' => ['0-3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                                'aligns' => [
                                    'L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'
                                ],
                                'widths' => [
                                    5 * $multiplier, //date_received
                                    5 * $multiplier, //date_obligated
                                    5 * $multiplier, //date_released
                                    9 * $multiplier, //payee
                                    14 * $multiplier, //particulars
                                    7 * $multiplier, //serial_number
                                    7 * $multiplier, //uacs_objects
                                    7 * $multiplier, //allotments
                                    7 * $multiplier, //obligations
                                    7 * $multiplier, //unobligated_allot
                                    7 * $multiplier, //disbursement
                                    10 * $multiplier, //due_demandable
                                    10 * $multiplier, //not_due_demandable
                                ],
                                'font-styles' => [
                                    'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'
                                ],
                                'type' => 'other',
                                'data' => $footerTableData
                            ]
                        ],
                        'month' => strtoupper($_periodEnding)
                    ];
                }
            }

            $currTotalAllot = number_format($currTotalAllot, 2);
            $currTotalOblig = number_format($currTotalOblig, 2);
            $currTotalUnoblig = number_format($currTotalUnoblig, 2);
            $currTotalDisb = number_format($currTotalDisb, 2);
            $currTotalDue = number_format($currTotalDue, 2);
            $currTotalNotDue = number_format($currTotalNotDue, 2);

            $data[$datKey]->period_ending = implode(', ', $periodEnding);
            $data[$datKey]->entity_name = $entityName;
            $data[$datKey]->fund_cluster = $fundCluster;
            $data[$datKey]->legal_basis = $legalBasis;
            $data[$datKey]->sheet_no = $sheetNo;
            $data[$datKey]->current_month = $currMonth;
            $data[$datKey]->total_allotment = $currTotalAllot;
            $data[$datKey]->total_obligation = $currTotalOblig;
            $data[$datKey]->total_unobligated = $currTotalUnoblig;
            $data[$datKey]->total_disbursement = $currTotalDisb;
            $data[$datKey]->total_due = $currTotalDue;
            $data[$datKey]->total_not_due = $currTotalNotDue;

            /*
            foreach ($dates as $regDat) {
                $itemTableData = [];
                $footerTableData = [];
                $mfoPAPs = [];
                $suppliers = DB::table('suppliers')->get();

                $regAllotData = DB::table('funding_reg_allotments')
                                    ->where('id', $regDat['id'])
                                    ->first();
                $regAllotmentItems = DB::table('funding_reg_allotment_items')
                                    ->where('reg_allotment_id', $regDat['id'])
                                    ->orderBy('order_no')
                                    ->get();

                $periodEnding[] = date_format(date_create($regAllotData->period_ending), 'F Y');
                $entityName = $regAllotData->entity_name;
                $fundCluster = $regAllotData->fund_cluster;
                $legalBasis = $regAllotData->legal_basis;
                $_mfoPAPs = $regAllotData->mfo_pap ? unserialize($regAllotData->mfo_pap) : [];
                $sheetNo = $regAllotData->sheet_no;

                $_periodEnding = date_format(date_create($regAllotData->period_ending), 'F Y');
                $currMonth = strtoupper($_periodEnding);

                $multiplier = 1;

                $totalAllot = 0;
                $totalOblig = 0;
                $totalUnoblig = 0;
                $totalDisb = 0;
                $totalDue = 0;
                $totalNotDue = 0;

                foreach ($_mfoPAPs as $pap) {
                    $mfoPapDat = DB::table('mfo_pap')
                                    ->where('id', $pap)
                                    ->first();

                    if ($mfoPapDat) {
                        $mfoPAPs[] = $mfoPapDat->code;
                    }
                }

                $mfoPAP = implode(', ', $mfoPAPs);

                foreach ($regAllotmentItems as $ctr => $item) {
                    $payee = Auth::user()->getEmployee($item->payee)->name;
                    $uacsCodes = unserialize($item->uacs_object_code);
                    $uacsObjects = [];

                    if (strpos($item->particulars, "\n") !== FALSE) {
                        $searchStr = ["\r\n", "\n", "\r"];
                        $item->particulars = str_replace($searchStr, '<br>', $item->particulars);
                    }

                    if (!$payee) {
                        foreach ($suppliers as $key => $bid) {
                            if ($bid->id == $item->payee) {
                                $payee = $bid->company_name;
                                break;
                            }
                        }
                    }

                    foreach ($uacsCodes as $uacs) {
                        $mooeTitleDat = DB::table('mooe_account_titles')
                                        ->where('id', $uacs)
                                        ->first();

                        if ($mooeTitleDat) {
                            $uacsObjects[] = $mooeTitleDat->uacs_code;
                        }
                    }

                    $uacsObjects = implode(', ', $uacsObjects);

                    $totalAllot += $item->allotments;
                    $totalOblig += $item->obligations;
                    $totalUnoblig += $item->unobligated_allot;
                    $totalDisb += $item->disbursement;
                    $totalDue += $item->due_demandable;
                    $totalNotDue += $item->not_due_demandable;

                    $currTotalAllot += $item->allotments;
                    $currTotalOblig += $item->obligations;
                    $currTotalUnoblig += $item->unobligated_allot;
                    $currTotalDisb += $item->disbursement;
                    $currTotalDue += $item->due_demandable;
                    $currTotalNotDue += $item->not_due_demandable;

                    $item->allotments = number_format($item->allotments, 2);
                    $item->obligations = number_format($item->obligations, 2);
                    $item->unobligated_allot = number_format($item->unobligated_allot, 2);
                    $item->disbursement = number_format($item->disbursement, 2);
                    $item->due_demandable = number_format($item->due_demandable, 2);
                    $item->not_due_demandable = number_format($item->not_due_demandable, 2);

                    $itemTableData[] = [
                        $item->date_received,
                        $item->date_obligated,
                        $item->date_released,
                        $payee,
                        $item->particulars,
                        $item->serial_number,
                        $uacsObjects,
                        $item->allotments,
                        $item->obligations,
                        $item->unobligated_allot,
                        $item->disbursement,
                        $item->due_demandable,
                        $item->not_due_demandable,
                    ];
                }

                $totalAllot = number_format($totalAllot, 2);
                $totalOblig = number_format($totalOblig, 2);
                $totalUnoblig = number_format($totalUnoblig, 2);
                $totalDisb = number_format($totalDisb, 2);
                $totalDue = number_format($totalDue, 2);
                $totalNotDue = number_format($totalNotDue, 2);

                $footerTableData[] = [
                    'TOTAL FOR THE MONTH OF '.strtoupper($_periodEnding),
                    '', '', '', '', '', '',
                    $totalAllot,
                    $totalOblig,
                    $totalUnoblig,
                    $totalDisb,
                    $totalDue,
                    $totalNotDue,
                ];

                $data[] = (object) [
                    'table_data' => [
                        [
                            'aligns' => [
                                'C', 'C', 'C', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'
                            ],
                            'widths' => [
                                5 * $multiplier, //date_received
                                5 * $multiplier, //date_obligated
                                5 * $multiplier, //date_released
                                9 * $multiplier, //payee
                                14 * $multiplier, //particulars
                                7 * $multiplier, //serial_number
                                7 * $multiplier, //uacs_objects
                                7 * $multiplier, //allotments
                                7 * $multiplier, //obligations
                                7 * $multiplier, //unobligated_allot
                                7 * $multiplier, //disbursement
                                10 * $multiplier, //due_demandable
                                10 * $multiplier, //not_due_demandable
                            ],
                            'font-styles' => [
                                '', '', '', '', '', '', '', '', '', '', '', '', ''
                            ],
                            'type' => 'row-data',
                            'data' => $itemTableData,
                        ],
                    ],
                    'table_footer' => [
                        [
                            'col-span' => true,
                            'col-span-key' => ['0-3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                            'aligns' => [
                                'L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'
                            ],
                            'widths' => [
                                5 * $multiplier, //date_received
                                5 * $multiplier, //date_obligated
                                5 * $multiplier, //date_released
                                9 * $multiplier, //payee
                                14 * $multiplier, //particulars
                                7 * $multiplier, //serial_number
                                7 * $multiplier, //uacs_objects
                                7 * $multiplier, //allotments
                                7 * $multiplier, //obligations
                                7 * $multiplier, //unobligated_allot
                                7 * $multiplier, //disbursement
                                10 * $multiplier, //due_demandable
                                10 * $multiplier, //not_due_demandable
                            ],
                            'font-styles' => [
                                'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B'
                            ],
                            'type' => 'other',
                            'data' => $footerTableData
                        ]
                    ],
                    'month' => strtoupper($_periodEnding)
                ];
            }

            $periodEnding = implode(', ', $periodEnding);
            $currTotalAllot = number_format($currTotalAllot, 2);
            $currTotalOblig = number_format($currTotalOblig, 2);
            $currTotalUnoblig = number_format($currTotalUnoblig, 2);
            $currTotalDisb = number_format($currTotalDisb, 2);
            $currTotalDue = number_format($currTotalDue, 2);
            $currTotalNotDue = number_format($currTotalNotDue, 2);*/
        }

        return (object)[
            'id' => $_regAllotIDs,
            'data' => $data
        ];
    }

    private function getDataRealignmentLIB($budRealignID) {
        $budgetRealignedData = DB::table('funding_budget_realignments')
                                 ->where('id', $budRealignID)
                                 ->orderBy('realignment_order', 'desc')
                                 ->first();
        $budgetID = $budgetRealignedData->budget_id;
        $realignOrder = $budgetRealignedData->realignment_order;

        $budgetRealigns = DB::table('funding_budgets as bud')
                            ->join('funding_budget_realignments as r_bud',
                                'r_bud.budget_id', '=', 'bud.id')
                            ->where([
                                ['bud.id', $budgetID],
                                ['r_bud.realignment_order', '<=', $realignOrder]
                            ])->orderBy('r_bud.realignment_order')->get();
        $allotments = DB::table('funding_allotments as allot')
                        ->where('budget_id', $budgetID)
                        ->orderBy('order_no')
                        ->get();

        $projID = $budgetRealignedData->project_id;
        $projData = FundingProject::find($projID);
        $cyYearFrom = date_format(date_create($projData->date_from), 'Y');
        $cyYearTo = date_format(date_create($projData->date_to), 'Y');
        $currDateFrom = date_format(date_create($projData->date_from), 'F n, Y');
        $currDateTo = date_format(date_create($projData->date_to), 'F n, Y');

        $cyYear = $cyYearFrom == $cyYearTo ? $cyYearTo : "$cyYearFrom - $cyYearTo";
        $projTitle = $projData->project_title;
        $currDuration = "$currDateFrom - $currDateTo";
        $implAgency = $this->getAgencyName($projData->implementing_agency);
        $impBudget = $projData->implementing_project_cost;
        $__coimplAgencies = unserialize($projData->comimplementing_agency_lgus);
        $_coimplAgencies = [];

        foreach ($__coimplAgencies as $agency) {
            $_coimplAgencies[] = $this->getAgencyName($agency["comimplementing_agency_lgu"]);
        }

        $coimplAgencies = implode(',', $_coimplAgencies);
        $__monitOffices = unserialize($projData->monitoring_offices);
        $_monitOffices = [];

        foreach ($__monitOffices as $monitOffice) {
            $_monitOffices[] = $this->getMonitoringOfficeName($monitOffice);
        }

        $monitOffices = implode(',', $_monitOffices);
        $projectLeader = $projData->project_leader;
        $projectCost = $projData->project_cost;

        $instanceSignatory = new Signatory;
        $submittedBy = Auth::user()->getEmployee($budgetRealignedData->sig_submitted_by)->name;
        $submittedByPos = Auth::user()->getEmployee($budgetRealignedData->sig_submitted_by)->position;
        $approvedBy = $instanceSignatory->getSignatory($budgetRealignedData->sig_approved_by)->name;
        $approvedByPos = $instanceSignatory->getSignatory($budgetRealignedData->sig_approved_by)->position;

        $groupedAllotments = $this->groupAllotments($allotments, true, $budgetRealignedData);

        $multiplier = 1;

        $headerCount = 1;
        $headerCountRoman = 'I';
        $tableHeader = ['PARTICULARS', $implAgency];

        for ($orderNo = 1; $orderNo <= $realignOrder; $orderNo++) {
            $ordinalOrderNo = $this->convertToOrdinal($orderNo);
            $tableHeader[] = "$implAgency<br>($ordinalOrderNo Realignment)";
        }

        foreach ($_coimplAgencies as $coimplementor) {
            $tableHeader[] = $coimplementor;

            for ($orderNo = 1; $orderNo <= $realignOrder; $orderNo++) {
                $ordinalOrderNo = $this->convertToOrdinal($orderNo);
                $tableHeader[] = "$coimplementor<br>($ordinalOrderNo Realignment)";
            }
        }

        $tableHeader[] = "JUSTIFICATION";

        $coimplementerCount = count($_coimplAgencies);
        $tableHeaderCount = count($tableHeader);
        $firstColWidth = $tableHeaderCount > 7 ? $multiplier * 15 :
                         $multiplier * (100 / $tableHeaderCount);
        $otherColWidth = $tableHeaderCount > 7 ? $multiplier * (85 / ($tableHeaderCount - 1)) :
                         $multiplier * (100 / $tableHeaderCount);
        $fontStyles = [];
        $aligns = [];
        $widths = [];
        $colSpanKeys = [];

        for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
            $fontStyles[] = "B";
            $aligns[] = "C";

            if ($tblHeadCtr == 0) {
                $widths[] = $firstColWidth;
            } else {
                $widths[] = $otherColWidth;
            }
        }

        $data = [
            [
                'aligns' => $aligns,
                'widths' => $widths,
                'font-styles' => $fontStyles,
                'type' => 'row-title',
                'data' => [$tableHeader]
            ]
        ];

        $grandTotal = ['GRAND TOTAL', 0];

        for ($grandTotalCtr = 1; $grandTotalCtr < $tableHeaderCount - 2; $grandTotalCtr++) {
            $grandTotal[] = 0;
        }

        $grandTotal[] = '';

        foreach ($groupedAllotments as $className => $classItems) {
            $subTotal = ['Sub-Total', 0];
            $row = [];

            for ($subTotalCtr = 1; $subTotalCtr < $tableHeaderCount - 2; $subTotalCtr++) {
                $subTotal[] = 0;
            }

            $subTotal[] = '';

            switch ($headerCount) {
                case 1:
                    $headerCountRoman = 'I';
                    break;
                case 2:
                    $headerCountRoman = 'II';
                    break;
                case 3:
                    $headerCountRoman = 'III';
                    break;
                case 4:
                    $headerCountRoman = 'IV';
                    break;
                default:
                    break;
            }

            $row[] = "$headerCountRoman. ".str_replace('-', ' ', $className);

            for ($rowCount = 0; $rowCount < $tableHeaderCount - 1; $rowCount++) {
                $row[] = "";
            }

            $fontStyles = [];
            $aligns = [];
            $widths = [];
            $colSpanKeys = [];

            for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                $fontStyles[] = "B";

                if ($tblHeadCtr == 0) {
                    $aligns[] = "L";
                    $widths[] = $firstColWidth;
                    $colSpanKeys[] = "0";
                } else if ($tblHeadCtr == 1) {
                    $aligns[] = "L";
                    $widths[] = $otherColWidth;
                    $colSpanKeys[] = "1-".($tableHeaderCount - 1);
                } else {
                    $aligns[] = "L";
                    $widths[] = $otherColWidth;
                }
            }

            $data[] = [
                'col-span' => true,
                'col-span-key' => $colSpanKeys,
                'aligns' => $aligns,
                'widths' => $widths,
                'font-styles' => $fontStyles,
                'type' => 'row-data',
                'data' => [$row]
            ];

            foreach ($classItems as $ctr => $item) {
                if (is_int($ctr)) {
                    $allotCoimplementors = $item->coimplementers;

                    $row = [];
                    $row[] = " $item->allotment_name";

                    $row[] = $item->allotment_cost ? number_format($item->allotment_cost, 2) : '-';
                    $subTotal[count($row) - 1] += $item->allotment_cost;
                    $grandTotal[count($row) - 1] += $item->allotment_cost;

                    for ($realignOrderCtr = 1; $realignOrderCtr <= $realignOrder; $realignOrderCtr++) {
                        $realignIndex = "realignment_$realignOrderCtr";

                        $row[] = $item->{$realignIndex}->allotment_cost ?
                                 number_format($item->{$realignIndex}->allotment_cost, 2) :
                                 '-';
                        $subTotal[count($row) - 1] += $item->{$realignIndex}->allotment_cost;
                        $grandTotal[count($row) - 1] += $item->{$realignIndex}->allotment_cost;
                    }

                    foreach ($item->coimplementers as $coimpCtr => $coimplementer) {
                        $row[] = $coimplementer['coimplementor_budget'] ?
                                 number_format($coimplementer['coimplementor_budget'], 2) :
                                 '-';
                        $subTotal[count($row) - 1] += $coimplementer['coimplementor_budget'];
                        $grandTotal[count($row) - 1] += $coimplementer['coimplementor_budget'];

                        for ($realignOrderCtr = 1; $realignOrderCtr <= $realignOrder; $realignOrderCtr++) {
                            $realignIndex = "realignment_$realignOrderCtr";

                            $row[] = $item->{$realignIndex}->coimplementers[$coimpCtr]['coimplementor_budget'] ?
                                 number_format($item->{$realignIndex}->coimplementers[$coimpCtr]['coimplementor_budget'], 2) :
                                 '-';
                            $subTotal[count($row) - 1] +=
                                $item->{$realignIndex}->coimplementers[$coimpCtr]['coimplementor_budget'];
                            $grandTotal[count($row) - 1] +=
                                $item->{$realignIndex}->coimplementers[$coimpCtr]['coimplementor_budget'];
                        }
                    }

                    $row[] = $item->justification;

                    $fontStyles = [];
                    $aligns = [];
                    $widths = [];
                    $colSpanKeys = [];

                    for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                        $colSpanKeys[] = "$tblHeadCtr";

                        if ($tblHeadCtr == 0) {
                            $aligns[] = "L";
                            $fontStyles[] = "";
                            $widths[] = $firstColWidth;

                        } else {
                            $aligns[] = "R";
                            $fontStyles[] = "";
                            $widths[] = $otherColWidth;
                        }
                    }

                    $data[] = [
                        'aligns' => $aligns,
                        'widths' => $widths,
                        'font-styles' => $fontStyles,
                        'type' => 'row-data',
                        'data' => [$row]
                    ];

                    $row = [];
                } else {
                    $row = [' '.str_replace('-', ' ', $ctr)];

                    for ($rowCount = 1; $rowCount < $tableHeaderCount; $rowCount++) {
                        $row[] = '';
                    }

                    $fontStyles = [];
                    $aligns = [];
                    $widths = [];

                    for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                        if ($tblHeadCtr == 0) {
                            $fontStyles[] = "B";
                            $aligns[] = "L";
                            $widths[] = $firstColWidth;
                        } else if ($tblHeadCtr == 1 || $tblHeadCtr == 2) {
                            $fontStyles[] = "";
                            $aligns[] = "R";
                            $widths[] = $otherColWidth;

                        } else {
                            $fontStyles[] = "B";
                            $aligns[] = "L";
                            $widths[] = $otherColWidth;
                        }
                    }

                    $data[] = [
                        'aligns' => $aligns,
                        'widths' => $widths,
                        'font-styles' => $fontStyles,
                        'type' => 'row-data',
                        'data' => [$row]
                    ];

                    $row = [];

                    foreach ($item as $itm) {
                        $allotCoimplementors = $itm->coimplementers;

                        $row = [];
                        $row[] = '  '.explode('::', $itm->allotment_name)[1];

                        $row[] = $itm->allotment_cost ? number_format($itm->allotment_cost, 2) : '-';
                        $subTotal[count($row) - 1] += $itm->allotment_cost;
                        $grandTotal[count($row) - 1] += $itm->allotment_cost;

                        for ($realignOrderCtr = 1; $realignOrderCtr <= $realignOrder; $realignOrderCtr++) {
                            $realignIndex = "realignment_$realignOrderCtr";

                            $row[] = $itm->{$realignIndex}->allotment_cost ?
                                    number_format($itm->{$realignIndex}->allotment_cost, 2) :
                                    '-';
                            $subTotal[count($row) - 1] += $itm->{$realignIndex}->allotment_cost;
                            $grandTotal[count($row) - 1] += $itm->{$realignIndex}->allotment_cost;
                        }

                        foreach ($itm->coimplementers as $coimpCtr => $coimplementer) {
                            $row[] = $coimplementer['coimplementor_budget'] ?
                                    number_format($coimplementer['coimplementor_budget'], 2) :
                                    '-';
                            $subTotal[count($row) - 1] += $coimplementer['coimplementor_budget'];
                            $grandTotal[count($row) - 1] += $coimplementer['coimplementor_budget'];

                            for ($realignOrderCtr = 1; $realignOrderCtr <= $realignOrder; $realignOrderCtr++) {
                                $realignIndex = "realignment_$realignOrderCtr";

                                $row[] = $itm->{$realignIndex}->coimplementers[$coimpCtr]['coimplementor_budget'] ?
                                    number_format($itm->{$realignIndex}->coimplementers[$coimpCtr]['coimplementor_budget'], 2) :
                                    '-';
                                $subTotal[count($row) - 1] +=
                                    $itm->{$realignIndex}->coimplementers[$coimpCtr]['coimplementor_budget'];
                                $grandTotal[count($row) - 1] +=
                                    $itm->{$realignIndex}->coimplementers[$coimpCtr]['coimplementor_budget'];
                            }
                        }

                        $row[] = $itm->justification;

                        $fontStyles = [];
                        $aligns = [];
                        $widths = [];
                        $colSpanKeys = [];

                        for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                            $colSpanKeys[] = "$tblHeadCtr";

                            if ($tblHeadCtr == 0) {
                                $fontStyles[] = "";
                                $aligns[] = "L";
                                $widths[] = $firstColWidth;
                            } else {
                                $fontStyles[] = "";
                                $aligns[] = "R";
                                $widths[] = $otherColWidth;
                            }
                        }

                        $data[] = [
                            'aligns' => $aligns,
                            'widths' => $widths,
                            'font-styles' => $fontStyles,
                            'type' => 'row-data',
                            'data' => [$row]
                        ];

                        $row = [];
                    }
                }
            }

            $fontStyles = [];
            $aligns = [];
            $widths = [];
            $colSpanKeys = [];

            for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                $fontStyles[] = "BI";
                $aligns[] = "R";

                if ($tblHeadCtr == 0) {
                    $widths[] = $firstColWidth;
                } else {
                    $widths[] = $otherColWidth;
                }
            }

            for ($subTotalIndex = 1; $subTotalIndex < count($subTotal) - 1; $subTotalIndex++) {
                $subTotal[$subTotalIndex] = $subTotal[$subTotalIndex] ?
                                            number_format($subTotal[$subTotalIndex], 2) :
                                            '-';
            }

            $data[] = [
                'aligns' => $aligns,
                'widths' => $widths,
                'font-styles' => $fontStyles,
                'type' => 'row-data',
                'data' => [$subTotal]
            ];

            $subTotal = [];
            $headerCount++;
        }

        $fontStyles = [];
        $aligns = [];
        $widths = [];
        $colSpanKeys = [];

        for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
            $colSpanKeys[] = "$tblHeadCtr";

            if ($tblHeadCtr == 0) {
                $fontStyles[] = "B";
                $aligns[] = "C";
                $widths[] = $firstColWidth;
            } else {
                $fontStyles[] = "B";
                $aligns[] = "R";
                $widths[] = $otherColWidth;
            }
        }

        for ($grandTotalIndex = 1; $grandTotalIndex < count($grandTotal) - 1; $grandTotalIndex++) {
            if ($grandTotalIndex == count($grandTotal) - 1) {
                $grandTotal[$grandTotalIndex] = "";
            } else {
                $grandTotal[$grandTotalIndex] = $grandTotal[$grandTotalIndex] ?
                                                number_format($grandTotal[$grandTotalIndex], 2) :
                                                '-';
            }
        }

        $data[] = [
            'col-span' => true,
            'col-span-key' => $colSpanKeys,
            'aligns' => $aligns,
            'widths' => $widths,
            'font-styles' => $fontStyles,
            'type' => 'row-data',
            'data' => [$grandTotal]
        ];

        return (object)[
            'id' => $budRealignID,
            'header_count' => $tableHeaderCount,
            'table_data' => $data,
            'cy_year' => $cyYear,
            'title' => $projTitle,
            'duration' => $currDuration,
            'implementing_agency' => $implAgency,
            'coimplementors' => $coimplAgencies,
            'monitoring_offices' => $monitOffices,
            'leader' => $projectLeader,
            'total_cost' => $projectCost,
            'submitted_by' => $submittedBy,
            'submitted_by_pos' => $submittedByPos,
            'approved_by' => $approvedBy,
            'approved_by_pos' => $approvedByPos,
        ];
    }

    private function getDataLIB($budgetID) {
        $libData = DB::table('funding_budgets')
                     ->where('id', $budgetID)
                     ->first();
        $allotments = DB::table('funding_allotments')
                        ->where('budget_id', $budgetID)
                        ->orderBy('order_no')
                        ->get();

        $projID = $libData->project_id;
        $projData = FundingProject::find($projID);
        $cyYearFrom = date_format(date_create($projData->date_from), 'Y');
        $cyYearTo = date_format(date_create($projData->date_to), 'Y');
        $currDateFrom = date_format(date_create($projData->date_from), 'F n, Y');
        $currDateTo = date_format(date_create($projData->date_to), 'F n, Y');

        $cyYear = $cyYearFrom == $cyYearTo ? $cyYearTo : "$cyYearFrom - $cyYearTo";
        $projTitle = $projData->project_title;
        $currDuration = "$currDateFrom - $currDateTo";
        $implAgency = $this->getAgencyName($projData->implementing_agency);
        $impBudget = $projData->implementing_project_cost;
        $__coimplAgencies = unserialize($projData->comimplementing_agency_lgus);
        $_coimplAgencies = [];

        foreach ($__coimplAgencies as $agency) {
            $_coimplAgencies[] = $this->getAgencyName($agency["comimplementing_agency_lgu"]);
        }

        $coimplAgencies = implode(',', $_coimplAgencies);
        $__monitOffices = unserialize($projData->monitoring_offices);
        $_monitOffices = [];

        foreach ($__monitOffices as $monitOffice) {
            $_monitOffices[] = $this->getMonitoringOfficeName($monitOffice);
        }

        $monitOffices = implode(',', $_monitOffices);
        $projectLeader = $projData->project_leader;
        $projectCost = $projData->project_cost;

        $instanceSignatory = new Signatory;
        $submittedBy = Auth::user()->getEmployee($libData->sig_submitted_by)->name;
        $submittedByPos = Auth::user()->getEmployee($libData->sig_submitted_by)->position;
        $approvedBy = $instanceSignatory->getSignatory($libData->sig_approved_by)->name;
        $approvedByPos = $instanceSignatory->getSignatory($libData->sig_approved_by)->position;

        $groupedAllotments = $this->groupAllotments($allotments);

        $multiplier = 1;

        $headerCount = 1;
        $headerCountRoman = 'I';
        $tableHeader = ['PARTICULARS', $implAgency];

        foreach ($_coimplAgencies as $coimplementor) {
            $tableHeader[] = $coimplementor;
        }

        $tableHeaderCount = count($tableHeader);
        $firstColWidth = $tableHeaderCount > 7 ? $multiplier * 15 :
                         $multiplier * (100 / $tableHeaderCount);
        $otherColWidth = $tableHeaderCount > 7 ? $multiplier * (85 / ($tableHeaderCount - 1)) :
                         $multiplier * (100 / $tableHeaderCount);
        $fontStyles = [];
        $aligns = [];
        $widths = [];

        for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
            $fontStyles[] = "B";
            $aligns[] = "C";

            if ($tblHeadCtr == 0) {
                $widths[] = $firstColWidth;
            } else {
                $widths[] = $otherColWidth;
            }
        }

        $data = [
            [
                'aligns' => $aligns,
                'widths' => $widths,
                'font-styles' => $fontStyles,
                'type' => 'row-title',
                'data' => [$tableHeader]
            ]
        ];

        $grandTotal = ['GRAND TOTAL', 0];

        for ($grandTotalCtr = 2; $grandTotalCtr < $tableHeaderCount; $grandTotalCtr++) {
            $grandTotal[] = 0;
        }

        foreach ($groupedAllotments as $className => $classItems) {
            $subTotal = ['Sub-Total', 0];
            $row = [];

            for ($subTotalCtr = 2; $subTotalCtr < $tableHeaderCount; $subTotalCtr++) {
                $subTotal[] = 0;
            }

            switch ($headerCount) {
                case 1:
                    $headerCountRoman = 'I';
                    break;
                case 2:
                    $headerCountRoman = 'II';
                    break;
                case 3:
                    $headerCountRoman = 'III';
                    break;
                case 4:
                    $headerCountRoman = 'IV';
                    break;
                default:
                    break;
            }

            $row[] = "$headerCountRoman.".str_replace('-', ' ', $className);

            for ($rowCount = 0; $rowCount < $tableHeaderCount - 1; $rowCount++) {
                $row[] = "";
            }

            $fontStyles = [];
            $aligns = [];
            $widths = [];
            $colSpanKeys = [];

            for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                $fontStyles[] = "B";

                if ($tblHeadCtr == 0) {
                    $aligns[] = "L";
                    $widths[] = $firstColWidth;
                    $colSpanKeys[] = "0";
                } else if ($tblHeadCtr == 1) {
                    $aligns[] = "L";
                    $widths[] = $otherColWidth;
                    $colSpanKeys[] = "1-".($tableHeaderCount - 1);
                } else {
                    $aligns[] = "L";
                    $widths[] = $otherColWidth;
                }
            }

            $data[] = [
                'col-span' => true,
                'col-span-key' => $colSpanKeys,
                'aligns' => $aligns,
                'widths' => $widths,
                'font-styles' => $fontStyles,
                'type' => 'row-data',
                'data' => [$row]
            ];

            foreach ($classItems as $ctr => $item) {
                if (is_int($ctr)) {
                    $allotCoimplementors = unserialize($item->coimplementers);

                    $row = [];
                    $row[] = " $item->allotment_name";
                    $row[] = $item->allotment_cost ?
                             number_format($item->allotment_cost, 2) :
                             '-';

                    $subTotal[count($row) - 1] += $item->allotment_cost;
                    $grandTotal[count($row) - 1] += $item->allotment_cost;

                    foreach ($allotCoimplementors as $coimpCtr => $coimp) {
                        $row[] = $coimp['coimplementor_budget'] ?
                                 number_format($coimp['coimplementor_budget'], 2) :
                                 '-';
                        $subTotal[count($row) - 1] += $coimp['coimplementor_budget'];
                        $grandTotal[count($row) - 1] += $coimp['coimplementor_budget'];
                    }

                    $fontStyles = [];
                    $aligns = [];
                    $widths = [];
                    $colSpanKeys = [];

                    for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                        $colSpanKeys[] = "$tblHeadCtr";

                        if ($tblHeadCtr == 0) {
                            $fontStyles[] = "";
                            $aligns[] = "L";
                            $widths[] = $firstColWidth;
                        } else if ($tblHeadCtr == 1 || $tblHeadCtr == 1) {
                            $fontStyles[] = "";
                            $aligns[] = "R";
                            $widths[] = $otherColWidth;
                        } else {
                            $fontStyles[] = "";
                            $aligns[] = "R";
                            $widths[] = $otherColWidth;
                        }
                    }

                    $data[] = [
                        'col-span' => true,
                        'col-span-key' => $colSpanKeys,
                        'aligns' => $aligns,
                        'widths' => $widths,
                        'font-styles' => $fontStyles,
                        'type' => 'row-data',
                        'data' => [$row]
                    ];

                    $row = [];
                } else {
                    $row = [' '.str_replace('-', ' ', $ctr)];

                    for ($rowCount = 0; $rowCount < $tableHeaderCount - 1; $rowCount++) {
                        $row[] = "";
                    }

                    $fontStyles = [];
                    $aligns = [];
                    $widths = [];
                    $colSpanKeys = [];

                    for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                        $colSpanKeys[] = "$tblHeadCtr";

                        if ($tblHeadCtr == 0) {
                            $fontStyles[] = "B";
                            $aligns[] = "L";
                            $widths[] = $firstColWidth;
                        } else if ($tblHeadCtr == 1 || $tblHeadCtr == 2) {
                            $fontStyles[] = "";
                            $aligns[] = "L";
                            $widths[] = $otherColWidth;
                        } else {
                            $fontStyles[] = "B";
                            $aligns[] = "L";
                            $widths[] = $otherColWidth;
                        }
                    }

                    $data[] = [
                        'col-span' => true,
                        'col-span-key' => $colSpanKeys,
                        'aligns' => $aligns,
                        'widths' => $widths,
                        'font-styles' => $fontStyles,
                        'type' => 'row-data',
                        'data' => [$row]
                    ];

                    $row = [];

                    foreach ($item as $itm) {
                        $allotCoimplementors = unserialize($itm->coimplementers);

                        $row = [];
                        $row[] = '  '.explode('::', $itm->allotment_name)[1];
                        $row[] = $itm->allotment_cost ?
                                 number_format($itm->allotment_cost, 2) :
                                 '-';
                        $subTotal[count($row) - 1] += $itm->allotment_cost;
                        $grandTotal[count($row) - 1] += $itm->allotment_cost;

                        foreach ($allotCoimplementors as $coimpCtr => $coimp) {
                            $row[] = $coimp['coimplementor_budget'] ?
                                     number_format($coimp['coimplementor_budget'], 2) :
                                     '-';
                            $subTotal[count($row) - 1] += $coimp['coimplementor_budget'];
                            $grandTotal[count($row) - 1] += $coimp['coimplementor_budget'];
                        }

                        $fontStyles = [];
                        $aligns = [];
                        $widths = [];
                        $colSpanKeys = [];

                        for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                            $colSpanKeys[] = "$tblHeadCtr";

                            if ($tblHeadCtr == 0) {
                                $fontStyles[] = "";
                                $aligns[] = "L";
                                $widths[] = $firstColWidth;
                            } else if ($tblHeadCtr == 1 || $tblHeadCtr == 2) {
                                $fontStyles[] = "";
                                $aligns[] = "R";
                                $widths[] = $otherColWidth;
                            } else {
                                $fontStyles[] = "";
                                $aligns[] = "R";
                                $widths[] = $otherColWidth;
                            }
                        }

                        $data[] = [
                            'col-span' => true,
                            'col-span-key' => $colSpanKeys,
                            'aligns' => $aligns,
                            'widths' => $widths,
                            'font-styles' => $fontStyles,
                            'type' => 'row-data',
                            'data' => [$row]
                        ];

                        $row = [];
                    }
                }
            }

            $fontStyles = [];
            $aligns = [];
            $widths = [];
            $colSpanKeys = [];

            for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
                $colSpanKeys[] = "$tblHeadCtr";

                $fontStyles[] = "BI";
                $aligns[] = "R";

                if ($tblHeadCtr == 0) {
                    $widths[] = $firstColWidth;
                } else {
                    $widths[] = $otherColWidth;
                }
            }

            for ($subTotalIndex = 1; $subTotalIndex < count($subTotal); $subTotalIndex++) {
                $subTotal[$subTotalIndex] = $subTotal[$subTotalIndex] ?
                                            number_format($subTotal[$subTotalIndex], 2) :
                                            '-';
            }

            $data[] = [
                'col-span' => true,
                'col-span-key' => $colSpanKeys,
                'aligns' => $aligns,
                'widths' => $widths,
                'font-styles' => $fontStyles,
                'type' => 'row-data',
                'data' => [$subTotal]
            ];

            $subTotal = [];
            $headerCount++;
        }

        $fontStyles = [];
        $aligns = [];
        $widths = [];
        $colSpanKeys = [];

        for ($tblHeadCtr = 0; $tblHeadCtr < $tableHeaderCount; $tblHeadCtr++) {
            $colSpanKeys[] = "$tblHeadCtr";

            if ($tblHeadCtr == 0) {
                $fontStyles[] = "B";
                $aligns[] = "C";
                $widths[] = $firstColWidth;
            } else {
                $fontStyles[] = "B";
                $aligns[] = "R";
                $widths[] = $otherColWidth;
            }
        }

        for ($grandTotalIndex = 1; $grandTotalIndex < count($grandTotal); $grandTotalIndex++) {
            $grandTotal[$grandTotalIndex] = $grandTotal[$grandTotalIndex] ?
                                            number_format($grandTotal[$grandTotalIndex], 2) :
                                            '-';
        }

        $data[] = [
            'col-span' => true,
            'col-span-key' => $colSpanKeys,
            'aligns' => $aligns,
            'widths' => $widths,
            'font-styles' => $fontStyles,
            'type' => 'row-data',
            'data' => [$grandTotal]
        ];

        return (object)[
            'id' => $budgetID,
            'header_count' => $tableHeaderCount,
            'table_data' => $data,
            'cy_year' => $cyYear,
            'title' => $projTitle,
            'duration' => $currDuration,
            'implementing_agency' => $implAgency,
            'coimplementors' => $coimplAgencies,
            'monitoring_offices' => $monitOffices,
            'leader' => $projectLeader,
            'total_cost' => $projectCost,
            'submitted_by' => $submittedBy,
            'submitted_by_pos' => $submittedByPos,
            'approved_by' => $approvedBy,
            'approved_by_pos' => $approvedByPos,
            'budget_id' => $budgetID,
        ];
    }

    private function getDataPropertyLabel($invStockIssueID) {
        $stockID = [];
        $invStockIssueData = InventoryStockIssue::find($invStockIssueID);
        $invID = $invStockIssueData->inv_stock_id;
        $inventoryStockID = $invStockIssueData->inv_stock_id;
        $invStockIssueItemData = InventoryStockIssueItem::with('invstockitems')
                                                    ->where('inv_stock_issue_id', $invStockIssueID)
                                                    ->where('excluded', 'n')
                                                    ->get();

        $invDat = InventoryStock::find($invID);
        $invNo = $invDat->inventory_no;

        $data = (object) [
            'inv_stock_issue' => $invStockIssueData,
            'inv_stock_issue_item' => $invStockIssueItemData
        ];

        $instanceSignatory = new Signatory;
        $sigIssuedBy = $invStockIssueData->sig_received_from ?
                       $invStockIssueData->sig_received_from :
                       $invStockIssueData->sig_issued_by;
        $sigReceivedBy = $invStockIssueData->sig_received_by;
        $certifiedBy = $instanceSignatory->getSignatory($sigIssuedBy)->name;
        $issuedTo = Auth::user()->getEmployee($sigReceivedBy)->name;
        $issuedToEmpID = Auth::user()->getEmployee($sigReceivedBy)->emp_id;
        $finalData = [];
        $multiplier = 1.13;

        foreach ($invStockIssueItemData as $item) {
            $invstockitem = InventoryStockItem::where('id', $item->inv_stock_item_id)
                                              ->first();
            $propertyNos = unserialize($item->prop_stock_no);
            $dateAcquired = $item->date_issued;
            $description = (strlen($invstockitem->description) > 350) ?
                            substr($invstockitem->description, 0, 350).'...' :
                            $invstockitem->description;

            if (!empty($dateAcquired)) {
                $dateAcquired = new DateTime($dateAcquired);
                $dateAcquired = $dateAcquired->format('F j, Y');
            }

            foreach ($propertyNos as $propertyNo) {
                $stockID[] = $item->id;

                if (empty($propertyNo)) {
                    $propertyNo = 'N/A';
                }

                $data1 = [
                    [
                        'aligns' => ['L', 'L'],
                        'widths' => [$multiplier * 18,
                                    $multiplier * 67],
                        'font-styles' => ['', ''],
                        'type' => 'row-data',
                        'data' => [["&nbsp;Property No.:", $propertyNo]]
                    ]
                ];
                $data2 = [
                    [
                        'aligns' => ['L', 'L'],
                        'widths' => [$multiplier * 18,
                                    $multiplier * 67],
                        'font-styles' => ['', ''],
                        'type' => 'row-data',
                        'data' => [["&nbsp;Description:", $description]]
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
                        'data' => [["&nbsp;Date Acquired:", $dateAcquired, 'Issued To:', $issuedTo]]
                    ]
                ];
                $data4 = [
                    [
                        'aligns' => ['L', 'L'],
                        'widths' => [$multiplier * 18,
                                    $multiplier * 67],
                        'font-styles' => ['', 'B'],
                        'type' => 'row-data',
                        'data' => [["&nbsp;Certified By:", $certifiedBy],
                                ["&nbsp;Verified By:", '____________________________________________']]
                    ]
                ];

                $finalData[] = (object) [
                    'data1' => $data1,
                    'data2' => $data2,
                    'data3' => $data3,
                    'data4' => $data4,
                    'property_no' => $propertyNo,
                    'stock_id' => $stockID,
                    //'stock_id' => $invStockIssueID,
                    'received_by' => $issuedTo
                ];
            }
        }

        return (object) [
            'inventory_no' => $invNo,
            'emp_id' => $issuedToEmpID,
            'label_data' => $finalData
        ];
    }

    private function getDataICS($invStockIssueID) {
        $invStockIssueData = InventoryStockIssue::with('invstocks')
                                                ->find($invStockIssueID);
        $inventoryStockID = $invStockIssueData->inv_stock_id;
        $invStockIssueItemData = InventoryStockIssueItem::with('invstockitems')
                                                    ->where('inv_stock_issue_id', $invStockIssueID)
                                                    ->where('excluded', 'n')
                                                    ->get();
        $invStockData = InventoryStock::find($inventoryStockID);

        $entityName = $invStockData->entity_name;
        $fundCluster = $invStockData->fund_cluster;
        $inventoryNo = $invStockData->inventory_no;
        $poNo = $invStockData->po_no;
        $datePO = $invStockData->date_po;
        $supplierData = Supplier::find($invStockData->supplier);
        $supplier = $supplierData->company_name;

        $instanceSignatory = new Signatory;
        $sigReceivedFrom = $invStockIssueData->sig_received_from;
        $sigReceivedBy = $invStockIssueData->sig_received_by;
        $sigReceivedFromName = $instanceSignatory->getSignatory($sigReceivedFrom)->name;
        $sigReceivedFromPosition = $instanceSignatory->getSignatory($sigReceivedFrom)->ics_designation;
        $sigReceivedByName = Auth::user()->getEmployee($sigReceivedBy)->name;
        $sigReceivedByPosition = Auth::user()->getEmployee($sigReceivedBy)->position;

        $multiplier = 100 / 90;
        $tableData = [];

        foreach ($invStockIssueItemData as $item) {
            $invstockitem = InventoryStockItem::where('id', $item->inv_stock_item_id)
                                              ->first();
            $unitData = ItemUnitIssue::find($invstockitem->unit_issue);
            $propertyNo = implode(', ', unserialize($item->prop_stock_no));
            $unitName = $unitData->unit_name;

            if (!empty($item->date_issued)) {
                $item->date_issued = new DateTime($item->date_issued);
                $item->date_issued = $item->date_issued->format('F j, Y');
            }

            if (strpos($invstockitem->description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->description = str_replace($searchStr, '<br>', $item->description);
            }

            $tableData[] = [
                $item->quantity,
                $unitName,
                number_format($invstockitem->amount/$item->quantity, 2),
                number_format($invstockitem->amount, 2),
                $invstockitem->description,
                $item->date_issued,
                $propertyNo,
                $item->est_useful_life
            ];
        }

        for ($i = 0; $i <= 3; $i++) {
            $tableData[] = ['', '', '', '', '', '', '', ''];
        }

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

        return (object)[
            'id' => $invStockIssueID,
            'fund_cluster' => $fundCluster,
            'entity_name' => $entityName,
            'inventory_no' => $inventoryNo,
            'po_no' => $poNo,
            'date_po' => $datePO,
            'supplier' => $supplier,
            'table_data' => $data,
            'received_from_name' => $sigReceivedFromName,
            'received_from_position' => $sigReceivedFromPosition,
            'received_by_name' => $sigReceivedByName,
            'received_by_position' => $sigReceivedByPosition,
        ];
    }

    private function getDataRIS($invStockIssueID) {
        $invStockIssueData = InventoryStockIssue::with('invstocks')
                                                ->find($invStockIssueID);
        $inventoryStockID = $invStockIssueData->inv_stock_id;
        $invStockIssueItemData = InventoryStockIssueItem::with('invstockitems')
                                                    ->where('inv_stock_issue_id', $invStockIssueID)
                                                    ->where('excluded', 'n')
                                                    ->get();
        $invStockData = InventoryStock::find($inventoryStockID);

        $fundCluster = $invStockData->fund_cluster;
        $divisionData = EmpDivision::find($invStockData->division);
        $division = $divisionData->division_name;
        $office = $invStockData->office;
        $purpose = $invStockData->purpose;
        $inventoryNo = $invStockData->inventory_no;
        $responsibilityCenter = $invStockData->responsibility_center;

        $instanceSignatory = new Signatory;
        $sigIssuedBy = $invStockIssueData->sig_issued_by;
        $sigReceivedBy = $invStockIssueData->sig_received_by;
        $sigRequestedBy = $invStockIssueData->sig_requested_by;
        $sigApprovedBy = $invStockIssueData->sig_approved_by;
        $sigApprovedByName = $instanceSignatory->getSignatory($sigApprovedBy)->name;
        $sigApprovedByPosition = $instanceSignatory->getSignatory($sigApprovedBy)->ris_designation;
        $sigIssuedByName = $instanceSignatory->getSignatory($sigIssuedBy)->name;
        $sigIssuedByPosition = $instanceSignatory->getSignatory($sigIssuedBy)->ris_designation;
        $sigRequestedByName = Auth::user()->getEmployee($sigRequestedBy)->name;
        $sigRequestedByPosition = Auth::user()->getEmployee($sigRequestedBy)->position;
        $sigReceivedByName = Auth::user()->getEmployee($sigReceivedBy)->name;
        $sigReceivedByPosition = Auth::user()->getEmployee($sigReceivedBy)->position;

        $multiplier = 100 / 90;
        $tableData = [];

        foreach ($invStockIssueItemData as $item) {
            $invstockitem = InventoryStockItem::where('id', $item->inv_stock_item_id)
                                              ->first();
            $unitData = ItemUnitIssue::find($invstockitem->unit_issue);
            $propertyNo = implode(', ', unserialize($item->prop_stock_no));
            $unitName = $unitData->unit_name;
            $yes = "";
            $no = "";

            if (strpos($invstockitem->description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->description = str_replace($searchStr, '<br>', $item->description);
            }

            if (strpos($item->remarks, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->remarks = str_replace($searchStr, '<br>', $item->remarks);
            }

            if ($item->stock_available == 'y') {
                $yes = "x";
            } else {
                $no = "x";
            }

            $tableData[] = [
                $propertyNo,
                $unitName,
                $invstockitem->description,
                $invstockitem->quantity,
                $yes,
                $no,
                $item->quantity,
                $item->remarks
            ];
        }

        for ($i = 0; $i <= 3; $i++) {
            $tableData[] = ['', '', '', '', '', '', '', ''];
        }

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
                           ['Printed Name:', $sigRequestedByName, $sigApprovedByName,
                            $sigIssuedByName, $sigReceivedByName],
                           ['Designations:', $sigRequestedByPosition, $sigApprovedByPosition,
                            $sigIssuedByPosition, $sigReceivedByPosition],
                           ['Date:', '', '', '', '']]
            ]
        ];

        return (object)[
            'id' => $invStockIssueID,
            'fund_cluster' => $fundCluster,
            'inventory_no' => $inventoryNo,
            'office' => $office,
            'purpose' => $purpose,
            'division' => $division,
            'responsibility_center' => $responsibilityCenter,
            'table_data' => $data,
            'footer_data' => $dataFooter
        ];
    }

    private function getDataPAR($invStockIssueID) {
        $invStockIssueData = InventoryStockIssue::with('invstocks')
                                                ->find($invStockIssueID);
        $inventoryStockID = $invStockIssueData->inv_stock_id;
        $invStockIssueItemData = InventoryStockIssueItem::with('invstockitems')
                                                    ->where('inv_stock_issue_id', $invStockIssueID)
                                                    ->where('excluded', 'n')
                                                    ->get();
        $invStockData = InventoryStock::find($inventoryStockID);

        $fundCluster = $invStockData->fund_cluster;
        $inventoryNo = $invStockData->inventory_no;
        $poNo = $invStockData->po_no;
        $datePO = $invStockData->date_po;
        $supplierData = Supplier::find($invStockData->supplier);
        $supplier = $supplierData->company_name;

        $instanceSignatory = new Signatory;
        $sigIssuedBy = $invStockIssueData->sig_issued_by;
        $sigReceivedBy = $invStockIssueData->sig_received_by;
        $sigIssuedByName = $instanceSignatory->getSignatory($sigIssuedBy)->name;
        $sigIssuedByPosition = $instanceSignatory->getSignatory($sigIssuedBy)->par_designation;
        $sigReceivedByName = Auth::user()->getEmployee($sigReceivedBy)->name;
        $sigReceivedByPosition = Auth::user()->getEmployee($sigReceivedBy)->position;

        $multiplier = 100 / 90;
        $tableData = [];

        foreach ($invStockIssueItemData as $item) {
            $invstockitem = InventoryStockItem::where('id', $item->inv_stock_item_id)
                                              ->first();
            $unitData = ItemUnitIssue::find($invstockitem->unit_issue);
            $propertyNo = implode(', ', unserialize($item->prop_stock_no));
            $unitName = $unitData->unit_name;

            if (!empty($item->date_issued)) {
                $item->date_issued = new DateTime($item->date_issued);
                $item->date_issued = $item->date_issued->format('F j, Y');
            }

            if (strpos($invstockitem->description, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->description = str_replace($searchStr, '<br>', $item->description);
            }

            $tableData[] = [
                $item->quantity,
                $unitName,
                $invstockitem->description,
                $propertyNo,
                $item->date_issued,
                number_format($invstockitem->amount, 2)
            ];
        }

        for ($i = 0; $i <= 3; $i++) {
            $tableData[] = ['', '', '', '', '', ''];
        }

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

        return (object)[
            'id' => $invStockIssueID,
            'fund_cluster' => $fundCluster,
            'inventory_no' => $inventoryNo,
            'po_no' => $poNo,
            'date_po' => $datePO,
            'supplier' => $supplier,
            'table_data' => $data,
            'issued_by_name' => $sigIssuedByName,
            'issued_by_position' => $sigIssuedByPosition,
            'received_by_name' => $sigReceivedByName,
            'received_by_position' => $sigReceivedByPosition,
        ];
    }

    private function getDataSummary($id) {
        $summary = DB::table('summary_lddaps')
                     ->where('id', $id)
                     ->first();
        $mdsGSB = MdsGsb::find($summary->mds_gsb_id);
        $summaryItems = DB::table('summary_lddap_items')
                          ->where('sliiae_id', $id)
                          ->orderBy('item_no')
                          ->get();

        $itemTableData = [];
        $allotmentPS = 0;
        $allotmentMOOE = 0;
        $allotmentCO = 0;
        $allotmentFE = 0;
        $multiplier = 9 / 10;

        foreach ($summaryItems as $ctr => $item) {
            $lddap = DB::table('list_demand_payables')
                       ->where('id', $item->lddap_id)
                       ->first();

            if (strpos($item->allotment_remarks, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->allotment_ps_remarks = str_replace($searchStr, '<br>', $item->allotment_ps_remarks);
            }

            $allotmentPS += $item->allotment_ps;
            $allotmentMOOE += $item->allotment_mooe;
            $allotmentCO += $item->allotment_co;
            $allotmentFE += $item->allotment_fe;

            $item->total = $item->total ? number_format($item->total, 2) : '';
            $item->allotment_ps = $item->allotment_ps ? number_format($item->allotment_ps, 2) : '';
            $item->allotment_mooe = $item->allotment_mooe ? number_format($item->allotment_mooe, 2) : '';
            $item->allotment_co = $item->allotment_co ? number_format($item->allotment_co, 2) : '';
            $item->allotment_fe = $item->allotment_fe ? number_format($item->allotment_fe, 2) : '';

            $itemTableData[] = [
                ($ctr + 1) . '. ' . $lddap->lddap_ada_no,
                $item->date_issue,
                $item->total,
                $item->allotment_ps,
                $item->allotment_mooe,
                $item->allotment_co,
                $item->allotment_fe,
                $item->allotment_remarks,
            ];
        }

        if (count($itemTableData) < 15) {
            $itemDataCount = count($itemTableData);

            for ($i = $itemDataCount; $i <= 14; $i++) {
                $itemTableData[] = ['', '', '', '', '', '', '', ''];
            }
        }

        $totalAmount = number_format($summary->total_amount, 2);
        $allotmentPS = $allotmentPS ? number_format($allotmentPS, 2) : '';
        $allotmentMOOE = $allotmentMOOE ? number_format($allotmentMOOE, 2) : '';
        $allotmentCO = $allotmentCO ? number_format($allotmentCO, 2) : '';
        $allotmentFE = $allotmentFE ? number_format($allotmentFE, 2) : '';

        $itemTableData[] = [
            'Total', '',
            $totalAmount,
            $allotmentPS,
            $allotmentMOOE,
            $allotmentCO,
            $allotmentFE,
            '',
        ];

        $instanceSignatory = new Signatory;
        $certCorrect = $instanceSignatory->getSignatory($summary->sig_cert_correct)->name;
        $certCorrectPosition = $instanceSignatory->getSignatory($summary->sig_cert_correct)->summary_designation;
        $approvedBy = $instanceSignatory->getSignatory($summary->sig_approved_by)->name;
        $approvedByPosition = $instanceSignatory->getSignatory($summary->sig_approved_by)->summary_designation;
        $deliveredBy = $instanceSignatory->getSignatory($summary->sig_delivered_by)->name;
        $deliveredByPosition = $instanceSignatory->getSignatory($summary->sig_delivered_by)->summary_designation;

        $data = [
            [
                'aligns' => ['C', 'R', 'R', 'R', 'R', 'R', 'R', 'L', 'L', 'L', 'L'],
                'widths' => [
                    20.3 * $multiplier,
                    10 * $multiplier,
                    10 * $multiplier,
                    10 * $multiplier,
                    10 * $multiplier,
                    10 * $multiplier,
                    10 * $multiplier,
                    30.8 * $multiplier,
                ],
                'font-styles' => ['', '', '', '', '', '', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $itemTableData
            ]
        ];

        return (object)[
            'summary' => $summary,
            'mds_account_no' => $mdsGSB->sub_account_no,
            'table_data' => $data,
            'sig_cert_correct' => strtoupper($certCorrect),
            'sig_cert_correct_position' => $certCorrectPosition,
            'sig_approved_by' => strtoupper($approvedBy),
            'sig_approved_by_position' => $approvedByPosition,
            'sig_delivered_by' => strtoupper($deliveredBy),
            'sig_delivered_by_position' => strtoupper($deliveredByPosition),
        ];
    }

    private function getDataLDDAP($id) {
        $lddap = DB::table('list_demand_payables')
                   ->where('id', $id)
                   ->first();
        $mdsGSB = MdsGsb::find($lddap->mds_gsb_accnt_no);
        $lddapItems = DB::table('list_demand_payable_items')
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

            /*
            if (strpos($item->ors_no, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->ors_no = str_replace($searchStr, '<br>', $item->ors_no);
            }*/

            $item->ors_no = unserialize($item->ors_no);
            $item->allot_class_uacs = unserialize($item->allot_class_uacs);
            $orsNos = [];
            $mooeTitles = [];

            foreach ($item->ors_no as $orsID) {
                $orsData = OrsBurs::find($orsID);
                $orsNos[] = $orsData->serial_no;
            }

            $orsNo = implode(', ', $orsNos);

            foreach ($item->allot_class_uacs as $allotClass) {
                $mooeAccountData = MooeAccountTitle::find($allotClass);
                $mooeTitles[] = $mooeAccountData->uacs_code;
            }

            $mooeTitle = implode(', ', $mooeTitles);

            /*
            if (strpos($item->allot_class_uacs, "\n") !== FALSE) {
                $searchStr = ["\r\n", "\n", "\r"];
                $item->allot_class_uacs = str_replace($searchStr, '<br>', $item->allot_class_uacs);
            }*/

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
                                       $orsNo, $mooeTitle,
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
                                     $orsNo, $mooeTitle,
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

        $instanceSignatory = new Signatory;
        $certCorrect = $instanceSignatory->getSignatory($lddap->sig_cert_correct)->name;
        $certCorrectPosition = $instanceSignatory->getSignatory($lddap->sig_cert_correct)->lddap_designation;
        $approval1 = $instanceSignatory->getSignatory($lddap->sig_approval_1)->name;
        $approval2 = $instanceSignatory->getSignatory($lddap->sig_approval_2)->name;
        $approval3 = $instanceSignatory->getSignatory($lddap->sig_approval_3)->name;
        $approvalPosition1 = $instanceSignatory->getSignatory($lddap->sig_approval_1)->lddap_designation;
        $approvalPosition2 = $instanceSignatory->getSignatory($lddap->sig_approval_2)->lddap_designation;
        $approvalPosition3 = $instanceSignatory->getSignatory($lddap->sig_approval_3)->lddap_designation;
        $agencyAuth1 = $instanceSignatory->getSignatory($lddap->sig_agency_auth_1)->name;
        $agencyAuth2 = $instanceSignatory->getSignatory($lddap->sig_agency_auth_2)->name;
        $agencyAuth3 = $instanceSignatory->getSignatory($lddap->sig_agency_auth_3)->name;
        $agencyAuth4 = $instanceSignatory->getSignatory($lddap->sig_agency_auth_4)->name;

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

        return (object)[
            'lddap' => $lddap,
            'mds_gsb_branch' => $mdsGSB->branch,
            'mds_gsb_sub_account_no' => $mdsGSB->sub_account_no,
            'table_data' => $data,
            'sig_cert_correct' => strtoupper($certCorrect),
            'sig_cert_correct_position' => $certCorrectPosition,
            'sig_approval_1' => strtoupper($approval1),
            'sig_approval_2' => strtoupper($approval2),
            'sig_approval_3' => strtoupper($approval3),
            'sig_approval_1_position' => $approvalPosition1,
            'sig_approval_2_position' => $approvalPosition2,
            'sig_approval_3_position' => $approvalPosition3,
            'sig_agency_auth_1' => strtoupper($agencyAuth1),
            'sig_agency_auth_2' => strtoupper($agencyAuth2),
            'sig_agency_auth_3' => strtoupper($agencyAuth3),
            'sig_agency_auth_4' => strtoupper($agencyAuth4),
        ];
    }

    private function getDataLiquidation($id) {
        $liq = DB::table('liquidation_reports as liq')
                 ->select('liq.*', 'emp.firstname', 'emp.middlename', 'emp.lastname',
                          'bid.company_name', 'pay.payee_name', 'dv.dv_no')
                 ->leftJoin('emp_accounts as emp', 'emp.id', '=', 'liq.sig_claimant')
                 ->leftJoin('suppliers as bid', 'bid.id', '=', 'liq.sig_claimant')
                 ->leftJoin('custom_payees as pay', 'pay.id', '=', 'liq.sig_claimant')
                 ->join('disbursement_vouchers as dv', 'dv.id', '=', 'liq.dv_id')
                 ->where('liq.id', $id)
                 ->first();
        $liq->dv_no = !empty($liq->dv_no) ? $liq->dv_no: '_______';
        $liq->dv_dtd = !empty($liq->dv_dtd) ? $liq->dv_dtd: '_______';
        $liq->or_no = !empty($liq->or_no) ? $liq->or_no: '_______';
        $liq->or_dtd = !empty($liq->or_dtd) ? $liq->or_dtd: '_______';
        $multiplier = 100 / 90;

        $instanceSignatory = new Signatory;
        //$claimant = Auth::user()->getEmployee($liq->sig_claimant)->name;
        $supervisor = $instanceSignatory->getSignatory($liq->sig_supervisor)->name;
        $accounting = $instanceSignatory->getSignatory($liq->sig_accounting)->name;

        if (strlen($liq->particulars) <= 73) {
            $liq->particulars = $liq->particulars . '<br><br><br><br>';
        } else if (strlen($liq->particulars) > 73 && strlen($liq->particulars) <= 200) {
            $liq->particulars = $liq->particulars . '<br><br>';
        }

        if ($liq->firstname) {
            if (!empty($liq->middlename)) {
                $claimant = $liq->firstname . " " . $liq->middlename[0] . ". " . $liq->lastname;
            } else {
                $claimant = $liq->firstname . " " . $liq->lastname;
            }
        } else if ($liq->company_name) {
            $claimant = $liq->company_name;
        } else {
            $claimant = $liq->payee_name;
        }

        $liq->amount = number_format($liq->amount, 2);
        $liq->total_amount = number_format($liq->total_amount, 2);
        $liq->amount_cash_adv = $liq->amount_cash_adv ? number_format($liq->amount_cash_adv, 2) : '-';
        $liq->amount_refunded = $liq->amount_refunded ? number_format($liq->amount_refunded, 2) : '-';
        $liq->amount_reimbursed = $liq->amount_reimbursed ? number_format($liq->amount_reimbursed, 2) : '-';


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

        return (object)[
            'liq' => $liq,
            'table_data' => $data,
            'claimant' => $claimant,
            'supervisor' => $supervisor,
            'accounting' => $accounting
        ];
    }

    private function getDataDV($id, $type) {
        $payee = "";

        if ($type == 'procurement') {
            $dv = DB::table('disbursement_vouchers as dv')
                    ->select('dv.id as dv_id', 'dv.*', 'bid.company_name', 'bid.vat_no as tin')
                    ->join('obligation_request_status as ors', 'ors.id', '=', 'dv.ors_id')
                    ->join('suppliers as bid', 'bid.id', '=', 'dv.payee')
                    ->where('dv.id', $id)
                    ->first();

            $payee = $dv->company_name;
        } else if ($type == 'cashadvance') {
            $dv = DB::table('disbursement_vouchers as dv')
                    ->select(
                        'dv.id as dv_id', 'dv.*', 'emp.emp_id',
                        'emp.firstname', 'emp.middlename', 'emp.lastname',
                        'bid.company_name', 'pay.payee_name'
                    )
                    ->leftJoin('emp_accounts as emp', 'emp.id', '=', 'dv.payee')
                    ->leftJoin('suppliers as bid', 'bid.id', '=', 'dv.payee')
                    ->leftJoin('custom_payees as pay', 'pay.id', '=', 'dv.payee')
                    ->where('dv.id', $id)
                    ->first();

            if ($dv->firstname) {
                if (!empty($dv->middlename)) {
                    $payee = $dv->firstname . " " . $dv->middlename[0] . ". " . $dv->lastname;
                } else {
                    $payee = $dv->firstname . " " . $dv->lastname;
                }
            } else if ($dv->company_name) {
                $payee = $dv->company_name;
            } else {
                $payee = $dv->payee_name;
            }
        }

        $multiplier = 100 / 91.4296;

        if (strpos($dv->particulars, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $dv->particulars = str_replace($searchStr, '<br>', $dv->particulars);
        }

        if (strpos($dv->responsibility_center, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $dv->responsibility_center = str_replace($searchStr, '<br>', $dv->responsibility_center);
        }

        if (strlen($dv->particulars) < 300) {
            $dv->particulars .= '<br><br><br><br><br>';
        }

        /*
        if (strpos($dv->mfo_pap, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $dv->mfo_pap = str_replace($searchStr, '<br>', $dv->mfo_pap);
        } else {
            $dv->mfo_pap = '<br><br><br><br>';
        }*/

        $instanceSignatory = new Signatory;
        $sign1 = $instanceSignatory->getSignatory($dv->sig_certified)->name;
        $sign2 = $instanceSignatory->getSignatory($dv->sig_accounting)->name;
        $sign3 = $instanceSignatory->getSignatory($dv->sig_agency_head)->name;
        $position1 = $instanceSignatory->getSignatory($dv->sig_certified)->dv_designation;
        $position2 = $instanceSignatory->getSignatory($dv->sig_accounting)->dv_designation;
        $position3 = $instanceSignatory->getSignatory($dv->sig_agency_head)->dv_designation;
        $mfoPAPs = [];
        $uacsObjects = [];
        $_mfoPAPs = $dv->mfo_pap ? unserialize($dv->mfo_pap) : [];
        $uacsCodes = $dv->uacs_object_code ? unserialize($dv->uacs_object_code) : [];

        foreach ($_mfoPAPs as $pap) {
            $mfoPapDat = DB::table('mfo_pap')
                              ->where('id', $pap)
                              ->first();

            if ($mfoPapDat) {
                $mfoPAPs[] = $mfoPapDat->code;
            }
        }

        foreach ($uacsCodes as $uacs) {
            $mooeTitleDat = DB::table('mooe_account_titles')
                              ->where('id', $uacs)
                              ->first();

            if ($mooeTitleDat) {
                $uacsObjects[] = $mooeTitleDat->uacs_code;
            }
        }

        $mfoPAP = implode('<br>', $mfoPAPs);

        if (count($mfoPAPs) <= 3) {
            $mfoPAP .= '<br><br>';
        }

        $uacsObjects = implode(', ', $uacsObjects);
        $amount = number_format($dv->amount, 2);

        $tableData[] = [$dv->particulars,
                        $dv->responsibility_center,
                        $mfoPAP, $amount];

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

            if ($item->iar_excluded == 'n') {
                $tableData[] = [$item->stock_no,
                                $item->item_description,
                                $item->unit_name, $item->quantity];
            }
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
                     ->select(
                        'ors.*', 'emp.firstname', 'emp.middlename', 'emp.lastname',
                        'bid.company_name', 'pay.payee_name'
                     )
                     ->leftJoin('emp_accounts as emp', 'emp.id', '=', 'ors.payee')
                     ->leftJoin('suppliers as bid', 'bid.id', '=', 'ors.payee')
                     ->leftJoin('custom_payees as pay', 'pay.id', '=', 'ors.payee')
                     ->where([['ors.id', $id], ['ors.module_class', 2]])
                     ->first();
            $payee = "";

            if ($ors->firstname) {
                if (!empty($ors->middlename)) {
                    $payee = $ors->firstname . " " . $ors->middlename[0] . ". " . $ors->lastname;
                } else {
                    $payee = $ors->firstname . " " . $ors->lastname;
                }
            } else if ($ors->company_name) {
                $payee = $ors->company_name;
            } else {
                $payee = $ors->payee_name;
            }
        }

        $instanceUacsItems = DB::table('ors_burs_uacs_items as uacs')
                                ->select(
                                    'uacs.description', 'uacs.amount', 'mooe.uacs_code'
                                )
                                ->join('mooe_account_titles as mooe', 'mooe.id', '=', 'uacs.uacs_id')
                                ->where('uacs.ors_id', $id)
                                ->orderBy('mooe.uacs_code')
                                ->get();

        $instanceSignatory = new Signatory;
        $sign1 = $instanceSignatory->getSignatory($ors->sig_certified_1)->name;
        $sign2 = $instanceSignatory->getSignatory($ors->sig_certified_2)->name;
        $position1 = $instanceSignatory->getSignatory($ors->sig_certified_1)->ors_designation;
        $position2 = $instanceSignatory->getSignatory($ors->sig_certified_2)->ors_designation;
        $sDate1 = $ors->date_certified_1;
        $sDate2 = $ors->date_certified_2;
        $mfoPAPs = [];
        $uacsObjects = [];

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
        $_mfoPAPs = $ors->mfo_pap ? unserialize($ors->mfo_pap) : [];
        $uacsCodes = $ors->uacs_object_code ? unserialize($ors->uacs_object_code) : [];

        if (strpos($ors->responsibility_center, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $ors->responsibility_center = str_replace($searchStr, '<br>', $ors->responsibility_center);
        }

        if (strpos($ors->particulars, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $ors->particulars = str_replace($searchStr, '<br>', $ors->particulars);
        }

        if (strlen($ors->particulars) < 300) {
            $ors->particulars .= '<br><br><br><br><br><br>';
        }

        /*
        if (strpos($ors->mfo_pap, "\n") !== FALSE) {
            $searchStr = ["\r\n", "\n", "\r"];
            $ors->mfo_pap = str_replace($searchStr, '<br>', $ors->mfo_pap);
        }*/

        foreach ($_mfoPAPs as $pap) {
            $mfoPapDat = DB::table('mfo_pap')
                              ->where('id', $pap)
                              ->first();

            if ($mfoPapDat) {
                $mfoPAPs[] = $mfoPapDat->code;
            }
        }

        foreach ($uacsCodes as $uacs) {
            $mooeTitleDat = DB::table('mooe_account_titles')
                              ->where('id', $uacs)
                              ->first();

            if ($mooeTitleDat) {
                $uacsObjects[] = $mooeTitleDat->uacs_code;
            }
        }

        $mfoPAP = implode('<br>', $mfoPAPs);
        $uacsObjects = implode(', ', $uacsObjects);
        $_uacsObjects = '';

        foreach ($instanceUacsItems as $uacsCtr => $uacsItem) {
            $uacsItem->amount = number_format($uacsItem->amount, 2);
            $_uacsObjects .= "$uacsItem->description ($uacsItem->uacs_code) = $uacsItem->amount";

            if ($uacsCtr == count($instanceUacsItems)) {
                $_uacsObjects .=  "";
            } else {
                $_uacsObjects .= "<br><br>";
            }
        }

        $tableData[] = [$ors->responsibility_center,
                        $ors->particulars,
                        $mfoPAP,
                        $_uacsObjects, $itemAmount];

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
                ->leftJoin('procurement_modes as mode', 'mode.id', '=', 'abs.mode_procurement')
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
                      ->leftJoin('procurement_modes as mode', 'mode.id', '=', 'abstract.mode_procurement')
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
        $canvassedBy = Auth::user()->getEmployee($instanceRFQ->canvassed_by);
        $groupNumbers = $this->getItemGroup($prID);

        $headers = [
            'ITEM NO.', 'QTY', 'UNIT', 'ARTICLES/PARTICULARS',
            'Approved Budget for the Contract (unit)', 'UNIT PRICE'
        ];

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
                                 $multiplier * 7.62, $multiplier * 49.3,
                                 $multiplier * 13.2, $multiplier * 8.9],
                    'font-styles' => ['', '', '', '', '', ''],
                    'type' => 'row-title',
                    'data' => [$headers]
                ], [
                    'aligns' => ['C','C','C','L','R','R'],
                    'widths' => [$multiplier * 7.14, $multiplier * 6.19,
                                 $multiplier * 7.62, $multiplier * 49.3,
                                 $multiplier * 13.2, $multiplier * 8.9],
                    'font-styles' => ['', '', '', '', '', ''],
                    'type' => 'row-data',
                    'data' => $tableData
                ], count($tableData) == 0 ? [
                    'aligns' => ['C','C','C','C','R','R'],
                    'widths' => [$multiplier * 7.14, $multiplier * 6.19,
                                 $multiplier * 7.62, $multiplier * 49.3,
                                 $multiplier * 13.2, $multiplier * 8.9],
                    'font-styles' => ['', '', '', '', '', ''],
                    'type' => 'other',
                    'data' => [['', '', '', '', '', '']]
                ] : null
            ];

            $groupNo->table_data = (object)$data;
        }

        return (object)['pr' => $instancePR,
                        'group_no' => $groupNumbers,
                        'rfq' => $instanceRFQ,
                        'sig_rfq' => $sigRFQ,
                        'canvassed_by' => $canvassedBy];
    }

    private function getDataPR($id) {
        $tableData = [];
        $total = 0;
        $instancePR = PurchaseRequest::with('funding')->find($id);
        $instanceSignatory = new Signatory;
        $prItems = DB::table('purchase_request_items as item')
                     ->join('item_unit_issues as unit', 'unit.id', '=', 'item.unit_issue')
                     ->where('item.pr_id', $id)
                     ->orderBy('item.item_no')
                     ->get();
        $requestedBy = Auth::user()->getEmployee($instancePR->requested_by);
        $approvedBy = $instanceSignatory->getSignatory($instancePR->approved_by);
        $recommendedBy = $instanceSignatory->getSignatory($instancePR->recommended_by);

        $headers = [
            'Stock/Property No.', 'Unit', 'Item Description',
            'Quantity', 'Unit Cost', 'Total Cost'
        ];

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

        $footers = [
            ['', '', '', '', '', ''],
            ['', '', '', '', 'Total', number_format($total, 2)]
        ];

        $multiplier = 100 / 91.52;
        //$multiplier = 1;
        $total = number_format($total, 2);

        $data = [
            [
                'aligns' => ['C', 'C', 'C', 'C', 'C', 'C'],
                'widths' => [$multiplier * 13.8, $multiplier * 8.85,
                             $multiplier * 28.05, $multiplier * 9.52,
                             $multiplier * 15.65, $multiplier * 15.65],
                'font-styles' => ['B', 'B', 'B', 'B', 'B', 'B'],
                'type' => 'row-title',
                'data' => [$headers],
            ], [
                'aligns' => ['C', 'C', 'L', 'C', 'R', 'R'],
                'widths' => [$multiplier * 13.8, $multiplier * 8.85,
                             $multiplier * 28.05, $multiplier * 9.52,
                             $multiplier * 15.65, $multiplier * 15.65],
                'font-styles' => ['', '', '', '', '', ''],
                'type' => 'row-data',
                'data' => $tableData
            ], [
                'aligns' => ['C', 'C', 'C', 'C', 'L', 'R'],
                'widths' => [$multiplier * 13.8, $multiplier * 8.85,
                             $multiplier * 28.05, $multiplier * 9.52,
                             $multiplier * 15.65, $multiplier * 15.65],
                'font-styles' => ['', '', '', '', 'B', 'B'],
                'type' => 'other',
                'data' => $footers
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
        } else if ($previewToggle == 'download-image') {
            $imageBlob = $pdf->Output($docTitle, 'S');

            $imInstance = new \Imagick();
            $imInstance->setResolution(300, 300);
            $imInstance->readImageBlob($imageBlob);
            $imInstance->setImageFormat('png');

            if (count($imInstance) > 1) {
                $zip = new \ZipArchive();
                $path = "public/temp/$docTitle";
                $zipfile = storage_path("app/$path")."/$docTitle.zip";

                if (!Storage::exists($path)) {
                    Storage::makeDirectory($path);
                }

                $zip->open($zipfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                foreach ($imInstance as $i => $img) {
                    $filename = "$docTitle-$i.png";
                    $img->setResolution(300, 300);
                    $img->setImageFormat('png');
                    $img->writeImage(storage_path("app/$path")."/$filename");
                    $zip->addFile(storage_path("app/$path/$filename"), $filename);
                }

                $zip->close();

                header('Content-Type: application/zip');
                header('Content-disposition: attachment; filename="'.$docTitle.'.zip"');
                header('Content-Length: ' . filesize($zipfile));
                readfile($zipfile);

                Storage::deleteDirectory($path);
            } else {
                header('Content-type: image/png');
                header('Content-Disposition: attachment; filename="' . $docTitle . '.png"');
                echo $imInstance;
            }
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
        $pdf->setCustomPageNo('Page 2 of 2');

        $docCode = "FM-FAS-PUR F06";
        //$docRev = "Revision 1";
        //$docRevDate = "02-28-18";
        $docRev = "Revision 2";
        $docRevDate = "10-16-2020";
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
        $pdf = new DocDisbursementVoucher('P', $pageUnit, $pageSize);
        $pdf->setHeaderLR(false, true);
        $pdf->setFontScale($fontScale);

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

    private function generateLR($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocLiquidationReport('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = "lr_" . $data->liq->id;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Liquidation Report";
        $docKeywords = "LR, lr, liquidation, report, liquidation report";

        if (empty($data->liq->serial_no)) {
            $data->liq->serial_no = '______________';
        }

        if (!empty($data->liq->date_liquidation)) {
            $data->liq->date_liquidation = new DateTime($data->liq->date_liquidation);
            $data->liq->date_liquidation = $data->liq->date_liquidation->format('F j, Y');
        }

        if (!empty($data->liq->date_claimant)) {
            $data->liq->date_claimant = new DateTime($data->liq->date_claimant);
            $data->liq->date_claimant = $data->liq->date_claimant->format('F j, Y');
        } else {
            $data->liq->date_claimant = ' ______________________';
        }

        if (!empty($data->liq->date_supervisor)) {
            $data->liq->date_supervisor = new DateTime($data->liq->date_supervisor);
            $data->liq->date_supervisor = $data->liq->date_supervisor->format('F j, Y');
        } else {
            $data->liq->date_supervisor = ' ______________________';
        }

        if (!empty($data->liq->date_accounting)) {
            $data->liq->date_accounting = new DateTime($data->liq->date_accounting);
            $data->liq->date_accounting = $data->liq->date_accounting->format('F j, Y');
        } else {
            $data->liq->date_accounting = ' ______________________';
        }

        if (empty($data->liq->jev_no)) {
            $data->liq->jev_no = ' ___________________';
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printLR($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateLDDAP($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocListDueDemandable('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = "lddap_".$data->lddap->id /*$data->ddap->lddap_id*/;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "List of Due And Demandable Accounts Payable - Advice to Debit Accounts";
        $docKeywords = "LDDAP, lddap, List, Due, Demandable, Accounts, Payable,
                        Advice, Debit, Accounts";

        if (!empty($data->lddap->lddap_date)) {
            $data->lddap->lddap_date = new DateTime($data->lddap->lddap_date);
            $data->lddap->lddap_date = $data->lddap->lddap_date->format('j F Y');
        }

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printLDDAP($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateSummary($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocSummaryListDueDemandable('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = "summary_".$data->summary->sliiae_no /*$data->ddap->lddap_id*/;
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Summary of LDDAP-ADAs Issued and Invalidated ADA Entries";
        $docKeywords = "LDDAP, lddap, List, Due, Demandable, Accounts, Payable,
                        Advice, Debit, Accounts, summary, Summary, sliiae, SLIIAE,
                        of, Issued, Invalidated, ADA, ada, Entries";

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printSummary($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generatePAR($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocPropertyAcknowledgement('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-PUR F10";
        $docRev = "Revision 1";
        $docRevDate = "02-28-18";
        $docTitle = strtolower($data->inventory_no);
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Property Acknowledgement Receipt";
        $docKeywords = "PAR, par, property, acknowledgement, receipt, property acknowledgement receipt";

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printPAR($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateRIS($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocRequisitionIssueSlip('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

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
        $pdf->printRIS($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateICS($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocInventoryCustodian('P', $pageUnit, $pageSize);
        $pdf->setFontScale($fontScale);

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
        $pdf->printICS($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generatePropertyLabel($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        $pageSize = [$pageWidth - 5, $pageHeight + 10];
        $pdf = new DocPropertyLabel('L', $pageUnit, $pageSize);
        $pdf->setHeaderLR(false, false);
        $pdf->setFontScale($fontScale);

        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = 'proptag-'.$data->inventory_no.'-'.$data->emp_id;
        $docTitle = strtoupper($docTitle);
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Property Label";
        $docKeywords = "property, label, property label";

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printPropertyLabel($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateLIB($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocLineItemBudget(
            $data->header_count > 8 ? 'L' : 'P',
            $pageUnit, $pageSize
        );
        $pdf->setHeaderLR(false, false);
        $pdf->setFontScale($fontScale);

        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = 'lib_cy_'.str_replace(' - ', '_', $data->cy_year);
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Line-Item-Budget";
        $docKeywords = "LIB, lib, Line, line, LINE, Item, item, ITEM, Budget, budget, BUDGET, Line-Item-Budget";

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printLIB($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateLIBRealignment($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocLineItemBudgetRealignment(
            $data->header_count > 8 ? 'L' : 'P',
            $pageUnit, $pageSize
        );
        $pdf->setHeaderLR(false, false);
        $pdf->setFontScale($fontScale);

        $docCode = "";
        $docRev = "";
        $docRevDate = "";
        $docTitle = 'lib_realignment_cy_'.str_replace(' - ', '_', $data->cy_year);
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Line-Item-Budget";
        $docKeywords = "LIB, lib, Line, line, LINE, Item, item, ITEM, Budget, budget, BUDGET, Line-Item-Budget";

        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        $pdf->printRealignmentLIB($data);

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }

    private function generateRAOD($data, $fontScale, $pageHeight, $pageWidth, $pageUnit, $previewToggle) {
        //Initiated variables
        $pageSize = [$pageWidth, $pageHeight];
        $pdf = new DocRegistryAllotmentsORSDV(
            'L', $pageUnit, $pageSize
        );

        $pdf->setHeaderLR(false, true);
        $pdf->setIsPageGrouped(true);
        $pdf->setFontScale($fontScale);

        $docCode = "FM-FAS-BUD F01";
        $docRev = "Revision 1";
        $docRevDate = "03-09-15";
        $docTitle = 'raod_report';
        $docCreator = "DOST-CAR";
        $docAuthor = "DOST-CAR";
        $docSubject = "Registry of Allotments, Obligations and Disbursements";
        $docKeywords = "RAOD, raod, Registry, registry, Allotments, allotments, Obligations, obligations, Disbursement. disbursement";

        $currentTotalPages = 0;


        //Set document information
        $this->setDocumentInfo($pdf, $docCode, $docRev, $docRevDate, $docTitle,
                               $docCreator, $docAuthor, $docSubject, $docKeywords);

        //Main document generation code file
        foreach ($data->data as $dat) {
            $pdf->printRAOD($dat);
            $currentTotalPages = $currentTotalPages == 0 ? $pdf->getNumPages() :
                                 $pdf->getNumPages() - $currentTotalPages;

            foreach ($dat->id as $id) {
                DB::table('funding_reg_allotments')
                ->where('id', $id)
                ->update([
                    'sheet_no' => $currentTotalPages
                ]);
            }
        }

        //Print the document
        $this->printDocument($pdf, $docTitle, $previewToggle);
    }
}
