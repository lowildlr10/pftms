<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PurchaseJobOrder;
use App\Models\FundingProject;
use App\Models\FundingBudget;
use App\Models\FundingAllotment;
use App\Models\FundingLedger;
use App\Models\FundingLedgerItem;
use App\Models\FundingLedgerAllotment;
use App\Models\FundingBudgetRealignment;
use App\Models\FundingAllotmentRealignment;
use App\Models\RegAllotment;
use App\Models\RegAllotmentItem;
use App\Models\ObligationRequestStatus;
use App\Models\DisbursementVoucher;
use App\Models\PurchaseRequest;
use App\Models\AllotmentClass;
use App\Models\PaperSize;
use App\Models\EmpAccount as User;
use App\Models\EmpUnit;
use App\Models\Supplier;
use App\Models\MooeAccountTitle;
use App\Models\CustomPayee;
use App\Models\MfoPap;

use Carbon\Carbon;
use Auth;
use DB;
use Session;
use Redirect;

class RegAllotmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $keyword = trim($request->keyword);
        $status = $request->status;

        // Get module access
        $module = 'report_dvledger';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $fundRAOD = $this->getIndexData($request);

        if ($status == 'success') {
            if (!Session::has('success')) {
                $msg = "Registry '$keyword' successfully created.";
                return redirect()->route('report-raod')
                          ->with('success', $msg);
            } else {
                $request->session()->forget('success');
                return redirect()->route('report-raod');
            }
        }

        return view('modules.report.registry-allotment.index', [
            'list' => $fundRAOD,
            'keyword' => $keyword,
            'paperSizes' => $paperSizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
        ]);
    }

    private function getIndexData($request) {
        $keyword = trim($request->keyword);

        // User groups
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

        $fundRAOD = new RegAllotment;

        if (!empty($keyword)) {
            $fundRAOD = $fundRAOD->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('period_ending', 'like', "%$keyword%")
                    ->orWhere('entity_name', 'like', "%$keyword%")
                    ->orWhere('fund_cluster', 'like', "%$keyword%")
                    ->orWhere('legal_basis', 'like', "%$keyword%")
                    ->orWhere('mfo_pap', 'like', "%$keyword%")
                    ->orWhere('sheet_no', 'like', "%$keyword%");
            });
        }

        $fundRAOD = $fundRAOD->sortable(['period_ending' => 'desc'])
                             ->paginate(50);

        foreach ($fundRAOD as $raod) {
            $mfoPAPs = [];
            $_periodEnding = strtotime($raod->period_ending);
            $periodEnding = date('F Y', $_periodEnding);
            $raod->period_ending_month = $periodEnding;
            $raod->voucher_count = RegAllotmentItem::where('reg_allotment_id', $raod->id)->count();

            foreach (unserialize($raod->mfo_pap) as $pap) {
                $mfoPapDat = DB::table('mfo_pap')->where('id', $pap)->first();
                $mfoPAPs[] = $mfoPapDat->code;
            }

            $raod->mfo_pap = implode(', ', $mfoPAPs);
        }

        return $fundRAOD;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        $mfoPAPs = MfoPap::orderBy('code')->get();
        return view('modules.report.registry-allotment.create', compact('mfoPAPs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $mfoPAPs = explode(',', $request->mfo_pap);
        sort($mfoPAPs);
        $request->mfo_pap = serialize($mfoPAPs);

        $instanceRegAllot = new RegAllotment([
            'period_ending' => $request->period_ending,
            'entity_name' => $request->entity_name,
            'fund_cluster' => $request->fund_cluster,
            'legal_basis' => $request->legal_basis,
            'mfo_pap' => $request->mfo_pap,
            'sheet_no' => $request->sheet_no,
        ]);
        $instanceRegAllot->save();

        return $instanceRegAllot->id;
    }

    public function storeItems(Request $request, $regID) {
        $orsID =  $request->ors_id;
        $uacsCode = $request->uacs_object_code ?
                    serialize(explode(',', $request->uacs_object_code)) :
                    serialize([]);

        $instanceRegAllot = new RegAllotmentItem([
            'reg_allotment_id' => $regID,
            'ors_id' => $orsID,
            'order_no' => $request->order_no,
            'date_received' => $request->date_received,
            'date_obligated' => $request->date_obligated,
            'date_released' => $request->date_released,
            'payee' => $request->payee,
            'particulars' => $request->particulars,
            'serial_number' => $request->serial_number,
            'uacs_object_code' => $uacsCode,
            'allotments' => $request->allotments,
            'obligations' => $request->obligations,
            'unobligated_allot' => $request->unobligated_allot,
            'disbursement' => $request->disbursement,
            'due_demandable' => $request->due_demandable,
            'not_due_demandable' => $request->not_due_demandable,
            'is_excluded' => $request->is_excluded,
        ]);
        $instanceRegAllot->save();

        $instanceORS = ObligationRequestStatus::find($orsID);
        $instanceORS->date_obligated = $request->date_obligated;
        $instanceORS->date_released = $request->date_released;
        $instanceORS->payee = $request->payee;
        $instanceORS->particulars = $request->particulars;
        $instanceORS->serial_no = $request->serial_number;
        $instanceORS->uacs_object_code = $uacsCode;
        $instanceORS->save();

        $instancePO = PurchaseJobOrder::where('po_no', $instanceORS->po_no)->first();

        if ($instancePO) {
            $instancePO->awarded_by;
            $instancePO->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request) {
        $regAllotIDs = $request->ids ? explode(";", trim($request->ids)) : [];
        $regAllotIDs = array_filter($regAllotIDs);

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
        usort($dates, ['App\Http\Controllers\RegAllotmentController', 'dateCompare']);

        foreach ($mfoPapGrps as $mfoGrpKey => $papGrp) {
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

            foreach ($dates as $dateCtr => $regDat) {
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
                        'TOTAL FOR THE MONTH OF ',
                        strtoupper($_periodEnding),
                        '', '', '',
                        $totalAllot,
                        $totalOblig,
                        $totalUnoblig,
                        $totalDisb,
                        $totalDue,
                        $totalNotDue,
                    ];

                    $data[$datKey]->table_data[] = (object) [
                        'body' => $itemTableData,
                        'footer' => $footerTableData,
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
        }

        return view('modules.report.registry-allotment.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $employees = User::orderBy('firstname')->get();
        $suppliers = Supplier::orderBy('company_name')->get();
        $customPayees = CustomPayee::orderBy('payee_name')->get();
        $mfoPAPs = MfoPap::orderBy('code')->get();
        $uacsObjects = MooeAccountTitle::orderBy('uacs_code')->get();
        $regDat = RegAllotment::find($id);
        $periodEnding = $regDat->period_ending;
        $entityName = $regDat->entity_name;
        $fundCluster = $regDat->fund_cluster;
        $legalBasis = $regDat->legal_basis;
        $mfoPAP = unserialize($regDat->mfo_pap);
        $sheetNo = $regDat->sheet_no;
        $regItems = DB::table('obligation_request_status as ors')
                      ->select(
                            //'reg.id',
                            //'reg.date_received',
                            //'reg.date_obligated',
                            //'reg.date_released',
                            //'reg.payee',
                            //'reg.particulars',
                            //'reg.serial_number',
                            //'reg.uacs_object_code',
                            //'reg.allotments',
                            //'reg.obligations',
                            //'reg.unobligated_allot',
                            //'reg.disbursement',
                            //'reg.due_demandable',
                            //'reg.not_due_demandable',
                            'ors.id as ors_id',
                            'ors.serial_no as ors_serial_no',
                            'ors.date_obligated as ors_date_obligated',
                            'ors.date_released as ors_date_released',
                            'ors.payee as ors_payee',
                            'ors.particulars as ors_particulars',
                            'ors.serial_no as ors_serial_number',
                            'ors.mfo_pap as mfo_pap',
                            'ors.uacs_object_code as ors_uacs_object_code',
                            'ors.amount as ors_amount',
                            'dv.amount as dv_amount'
                        )/*->leftJoin('funding_reg_allotment_items as reg',
                                 'reg.ors_id', '=', 'ors.id')*/
                      ->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'ors.id')
                      ->where('ors.date_obligated', 'like', "%$periodEnding%");

        $regItems = $regItems->where(function($qry) use ($mfoPAP) {
            foreach ($mfoPAP as $papCtr => $pap) {
                if ($papCtr == 0) {
                    $qry->where('ors.mfo_pap', 'like', "%$pap%");
                } else {
                    $qry->orWhere('ors.mfo_pap', 'like', "%$pap%");
                }
            }
        });

        $regItems = $regItems//->orderBy('reg.order_no')
                             ->orderBy('ors.date_obligated')
                             ->get();



        foreach ($regItems as $reg) {
            $reg->raod = RegAllotmentItem::where('ors_id', $reg->ors_id)->first();
            if (!$reg->raod) {
                $logs = DB::table('document_logs')
                        ->where([['doc_id', $reg->ors_id], ['action', 'received']])
                        ->orderBy('created_at', 'desc')
                        ->first();
                $reg->date_received = $logs->created_at;
            }
        }

        return view('modules.report.registry-allotment.update', compact(
            'id', 'periodEnding', 'entityName', 'fundCluster',
            'legalBasis', 'mfoPAP', 'sheetNo', 'regItems',
            'employees', 'suppliers', 'uacsObjects', 'mfoPAPs',
            'customPayees'
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
        $mfoPAPs = explode(',', $request->mfo_pap);
        sort($mfoPAPs);
        $request->mfo_pap = serialize($mfoPAPs);

        $instanceRegAllot = RegAllotment::find($id);
        $instanceRegAllot->period_ending = $request->period_ending;
        $instanceRegAllot->entity_name = $request->entity_name;
        $instanceRegAllot->fund_cluster = $request->fund_cluster;
        $instanceRegAllot->legal_basis = $request->legal_basis;
        $instanceRegAllot->mfo_pap = $request->mfo_pap;
        $instanceRegAllot->sheet_no = $request->sheet_no;
        $instanceRegAllot->save();

        $raodItems = DB::table('funding_reg_allotment_items')
                       ->where('reg_allotment_id', $id)
                       ->get();

        foreach ($raodItems as $item) {
            RegAllotmentItem::destroy($item->id);
        }

        return $id;
    }

    /**
     * Soft deletes the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id) {
        $isDestroy = $request->destroy;
        $routeName = 'report-raod';

        if ($isDestroy) {
            $response = $this->destroy($request, $id);

            if ($response->alert_type == 'success') {
                return redirect()->route($routeName)
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route($routeName)
                                 ->with($response->alert_type, $response->msg);
            }
        } else {
            try {
                $instanceRAOD = RegAllotment::find($id);
                $documentType = 'Registry of Allotments, Obligations and Disbursement';
                $instanceRAOD->delete();

                $msg = "$documentType '$id' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName)
                                 ->with('success', $msg);
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName)
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
            $raodItems = DB::table('funding_reg_allotment_items')
                             ->where('reg_allotment_id', $id)
                             ->get();

            foreach ($raodItems as $item) {
                RegAllotmentItem::destroy($item->id);
            }

            $instanceRAOD = RegAllotment::find($id);
            $instanceRAOD->forceDelete();

            $documentType = 'Registry of Allotments, Obligations and Disbursement';
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

    public function getVouchers(Request $request) {
        $periodEnding = $request->period_ending;
        $mfoPAPs = $request->mfo_pap ? $request->mfo_pap : [];
        $employees = User::orderBy('firstname')->get();
        $suppliers = Supplier::orderBy('company_name')->get();
        $customPayees = CustomPayee::orderBy('payee_name')->get();
        $uacsObjects = MooeAccountTitle::orderBy('uacs_code')->get();

        if (count($mfoPAPs) > 0) {
            $vouchers = DB::table('obligation_request_status as ors')
                        ->select(
                            'ors.id as ors_id', 'dv.id as dv_id', 'ors.serial_no as serial_no',
                            'ors.date_obligated as date_obligated', 'ors.payee as payee',
                            'ors.uacs_object_code as uacs_object', 'ors.amount as obligation',
                            'ors.continuing as continuing', 'ors.current as current',
                            'ors.particulars', 'ors.mfo_pap', 'dv.amount as disbursement'
                        )->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'ors.id')
                        ->where('ors.date_obligated', 'like', "%$periodEnding%");

            $vouchers = $vouchers->where(function($qry) use ($mfoPAPs) {
                foreach ($mfoPAPs as $papCtr => $pap) {
                    if ($papCtr == 0) {
                        $qry->where('ors.mfo_pap', 'like', "%$pap%");
                    } else {
                        $qry->orWhere('ors.mfo_pap', 'like', "%$pap%");
                    }
                }
            });

            $vouchers = $vouchers->orderBy('ors.date_obligated')
                                ->get();

            foreach ($vouchers as $ors) {
                $logs = DB::table('document_logs')
                        ->where([['doc_id', $ors->ors_id], ['action', 'received']])
                        ->orderBy('created_at', 'desc')
                        ->first();
                $ors->log_date_received = $logs->created_at;
            }
        } else {
            $vouchers = [];
        }

        return view('modules.report.registry-allotment.vouchers-list', compact(
            'employees', 'suppliers', 'vouchers', 'uacsObjects', 'customPayees'
        ));
    }

    public function getPayees(Request $request) {
        $keyword = trim($request->search);

        $payees = [];
        $empPayees = User::select('id', 'firstname', 'lastname');
        $supplierPayees = Supplier::select('id', 'company_name');

        if ($keyword) {
            $empPayees = $empPayees->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('firstname', 'like', "%$keyword%")
                    ->orWhere('lastname', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('firstname', 'like', "%$tag%")
                            ->orWhere('lastname', 'like', "%$tag%");
                    }
                }
            });

            $supplierPayees = $supplierPayees->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('company_name', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('company_name', 'like', "%$tag%");
                    }
                }
            });
        }

        $empPayees = $empPayees->orderBy('firstname')->get();
        $supplierPayees = $supplierPayees->orderBy('company_name')->get();

        foreach ($empPayees as $emp) {
            $payees[] = (object) [
                'id' => $emp->id,
                'name' => $emp->firstname.' '.$emp->lastname
            ];
        }

        foreach ($supplierPayees as $bid) {
            $payees[] = (object) [
                'id' => $bid->id,
                'name' => $bid->company_name
            ];
        }

        return response()->json($payees);
    }

    public function getUacsObject(Request $request) {
        $keyword = trim($request->search);

        $mooes = [];
        $mooeTitles = MooeAccountTitle::select('id', 'uacs_code', 'account_title',
                                               'order_no');

        if ($keyword) {
            $mooeTitles = $mooeTitles->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('uacs_code', 'like', "%$keyword%")
                    ->orWhere('account_title', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('uacs_code', 'like', "%$tag%")
                            ->orWhere('account_title', 'like', "%$tag%");
                    }
                }
            });
        }

        $mooeTitles = $mooeTitles->orderBy('order_no')->get();

        foreach ($mooeTitles as $mooe) {
            $mooes[] = (object) [
                'id' => $mooe->id,
                'name' => $mooe->account_title,
                'uacs_code' => $mooe->uacs_code,
            ];
        }

        return response()->json($mooes);
    }

    private static function dateCompare($date1, $date2) {
        $date1 = strtotime($date1['date']);
        $date2 = strtotime($date2['date']);

        return $date1 - $date2;
    }
}
