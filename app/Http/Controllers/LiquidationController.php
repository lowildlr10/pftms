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
use App\Models\LiquidationReport;
use App\Models\InventoryStock;
use App\Models\ListDemandPayable;

use App\Models\EmpAccount as User;
use App\Models\DocumentLog as DocLog;
use App\Models\PaperSize;
use App\Models\Supplier;
use App\Models\Signatory;
use App\Models\ItemUnitIssue;
use App\Models\CustomPayee;
use Carbon\Carbon;
use Auth;
use DB;


class LiquidationController extends Controller
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
    public function indexCA(Request $request) {
        $data = $this->getIndexData($request, 'cashadvance');

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();

        // Get module access
        $module = 'ca_lr';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedDelete = Auth::user()->getModuleAccess($module, 'delete');
        $isAllowedDestroy = Auth::user()->getModuleAccess($module, 'destroy');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedLiquidate = Auth::user()->getModuleAccess($module, 'liquidate');
        $isAllowedDV = Auth::user()->getModuleAccess('ca_dv', 'is_allowed');
        $isAllowedORS = Auth::user()->getModuleAccess('ca_ors_burs', 'is_allowed');

        return view('modules.voucher.liquidation.index', [
            'list' => $data->lr_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedDelete' => $isAllowedDelete,
            'isAllowedDestroy' => $isAllowedDestroy,
            'isAllowedLiquidate' => $isAllowedLiquidate,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedORS' => $isAllowedORS,
            'isAllowedDV' => $isAllowedDV,
            'roleHasOrdinary' => $roleHasOrdinary
        ]);
    }

    private function getIndexData($request, $type) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

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

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();

        if ($type == 'cashadvance') {
            $lrData = LiquidationReport::with('dv')->whereNull('deleted_at');

            if ($roleHasDeveloper || $roleHasBudget || $roleHasAccountant || $roleHasRD ||
                $roleHasARD) {
            } else {
                 $lrData = $lrData->where('sig_claimant', Auth::user()->id)
                                  ->orWhere('created_by', Auth::user()->id);
            }

            if (!empty($keyword)) {
                $lrData = $lrData->where(function($qry) use ($keyword) {
                    $qry->where('id', 'like', "%$keyword%")
                        ->orWhere('period_covered', 'like', "%$keyword%")
                        ->orWhere('entity_name', 'like', "%$keyword%")
                        ->orWhere('serial_no', 'like', "%$keyword%")
                        ->orWhere('fund_cluster', 'like', "%$keyword%")
                        ->orWhere('date_liquidation', 'like', "%$keyword%")
                        ->orWhere('responsibility_center', 'like', "%$keyword%")
                        ->orWhere('particulars', 'like', "%$keyword%")
                        ->orWhere('amount', 'like', "%$keyword%")
                        ->orWhere('date_liquidated', 'like', "%$keyword%")
                        ->orWhere('jev_no', 'like', "%$keyword%")
                        ->orWhere('dv_dtd', 'like', "%$keyword%")
                        ->orWhere('or_no', 'like', "%$keyword%")
                        ->orWhere('or_dtd', 'like', "%$keyword%")
                        ->orWhere('dv_id', 'like', "%$keyword%")
                        ->orWhereHas('empclaimant', function($query) use ($keyword) {
                            $query->where('firstname', 'like', "%$keyword%")
                                  ->orWhere('middlename', 'like', "%$keyword%")
                                  ->orWhere('lastname', 'like', "%$keyword%")
                                  ->orWhere('position', 'like', "%$keyword%");
                        })->orWhereHas('bidclaimant', function($query) use ($keyword) {
                            $query->where('company_name', 'like', "%$keyword%");
                        })->orWhereHas('customclaimant', function($query) use ($keyword) {
                            $query->where('payee_name', 'like', "%$keyword%");
                        })->orWhereHas('dv', function($query) use ($keyword) {
                            $query->where('id', 'like', "%$keyword%")
                                  ->orWhere('ors_id', 'like', "%$keyword%")
                                  ->orWhere('particulars', 'like', "%$keyword%")
                                  ->orWhere('transaction_type', 'like', "%$keyword%")
                                  ->orWhere('dv_no', 'like', "%$keyword%")
                                  ->orWhere('date_dv', 'like', "%$keyword%")
                                  ->orWhere('date_disbursed', 'like', "%$keyword%")
                                  ->orWhere('responsibility_center', 'like', "%$keyword%")
                                  ->orWhere('mfo_pap', 'like', "%$keyword%")
                                  ->orWhere('amount', 'like', "%$keyword%")
                                  ->orWhere('address', 'like', "%$keyword%")
                                  ->orWhere('fund_cluster', 'like', "%$keyword%");
                        });
                });
            }

            $lrData = $lrData->sortable(['created_at' => 'desc'])->paginate(15);

            foreach ($lrData as $lrDat) {
                $instanceDV = DisbursementVoucher::find($lrDat->dv_id);
                $lrDat->doc_status = $instanceDocLog->checkDocStatus($lrDat->id);
                $lrDat->has_dv = DisbursementVoucher::where('id', $lrDat->dv_id)->count();
                $lrDat->has_ors = ObligationRequestStatus::where('id', $instanceDV->ors_id)->count();
            }
        }

        return (object) [
            'keyword' => $keyword,
            'lr_data' => $lrData,
            'paper_sizes' => $paperSizes
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateFromDV($dvID) {
        $claimants = [];

        $dvList = DisbursementVoucher::all();
        $dvData = DisbursementVoucher::find($dvID);
        $particulars = str_replace("To payment", "To liquidate", $dvData->particulars);
        $amount = $dvData->amount;
        $dvNo = $dvData->dv_no;
        $dvDate = $dvData->date_dv;
        $claimant = $dvData->payee;

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        /*
        $claimants = $roleHasOrdinary ?
                User::where('id', Auth::user()->id)
                    ->orderBy('firstname')
                    ->get() :
                User::where('is_active', 'y')
                    ->whereIn('division', $empDivisionAccess)
                    ->orderBy('firstname')->get();
        */

        $claimants[] = User::select(
            DB::raw("CONCAT(firstname, ' ', lastname, ' [ ', position, ' ]') as name"),
            'id'
        )->orderBy('firstname')->get();
        $claimants[] = Supplier::select(
            DB::raw("CONCAT(company_name, ' ', ' [ Registered Supplier ]') as company_name"),
            'id'
        )->orderBy('company_name')->get();
        $claimants[] = CustomPayee::select(
            DB::raw("CONCAT(payee_name, ' [ Manually Added ]') as payee_name"),
            'id'
        )->orderBy('payee_name')->get();

        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.voucher.liquidation.create', compact(
            'signatories', 'claimants', 'claimant', 'particulars',
            'dvNo', 'amount', 'dvList', 'dvID', 'dvDate'
        ));
    }

    public function showCreate() {
        $dvList = DisbursementVoucher::all();
        $amount = 0.00;
        $dvID = NULL;
        $dvNo = NULL;
        $dvDate = NULL;
        $claimant = NULL;

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];
        $claimants = $roleHasOrdinary ?
                User::where('id', Auth::user()->id)
                    ->orderBy('firstname')
                    ->get() :
                User::where('is_active', 'y')
                    ->whereIn('division', $empDivisionAccess)
                    ->orderBy('firstname')->get();

        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.voucher.liquidation.create', compact(
            'signatories', 'claimants', 'claimant',
            'dvNo', 'amount', 'dvList', 'dvID', 'dvDate'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $periodCover = $request->period_cover;
        $serialNo = $request->serial_no;
        $dateLiquidation = !empty($request->date_liquidation) ? $request->date_liquidation : NULL;
        $entityName = $request->entity_name;
        $fundCluster = $request->fund_cluster;
        $responsibilityCenter = $request->responsibility_center;
        $particulars = $request->particulars;
        $amount = $request->amount;
        $totalAmount = $request->total_amount;
        $dvID = $request->dv_id;
        $dvDTD = !empty($request->dv_dtd) ? $request->dv_dtd : NULL;
        $amountCashAdv = $request->amount_cash_adv;
        $orNO = $request->or_no;
        $orDTD = !empty($request->or_dtd) ? $request->or_dtd : NULL;
        $amountRefunded = $request->amount_refunded;
        $amountReimbursed = $request->amount_reimbursed;
        $sigClaimant = $request->sig_claimant;
        $dateClaimant = !empty($request->date_claimant) ? $request->date_claimant : NULL;
        $sigSupervisor = $request->sig_supervisor;
        $dateSupervisor = !empty($request->date_supervisor) ? $request->date_supervisor : NULL;
        $sigAccounting = $request->sig_accounting;
        $jevNo = $request->jev_no;
        $dateAccounting = !empty($request->date_accounting) ? $request->date_accounting : NULL;

        $routeName = 'ca-lr';
        $documentType = 'Liquidation Report';

        try {
            $instanceDV = DisbursementVoucher::find($dvID);

            if ($instanceDV && !empty($instanceDV->dv_no)) {
                $empData = User::where('id', $sigClaimant)->count();
                $supplierData = Supplier::where('id', $sigClaimant)->count();
                $customPayeeData = CustomPayee::where('id', $sigClaimant)
                                            ->orWhere('payee_name', $sigClaimant)
                                            ->count();

                if (!$empData && !$supplierData && !$customPayeeData) {
                    $instancePayee = CustomPayee::create([
                        'payee_name' => $sigClaimant
                    ]);

                    $sigClaimant = $instancePayee->id->string;
                }

                $instanceLR = new LiquidationReport;
                $instanceLR->period_covered = $periodCover;
                $instanceLR->serial_no = $serialNo;
                $instanceLR->date_liquidation = $dateLiquidation;
                $instanceLR->entity_name = $entityName;
                $instanceLR->fund_cluster = $fundCluster;
                $instanceLR->responsibility_center = $responsibilityCenter;
                $instanceLR->particulars = $particulars;
                $instanceLR->amount = $amount;
                $instanceLR->total_amount = $totalAmount;
                $instanceLR->dv_id = $dvID;
                $instanceLR->dv_dtd = $dvDTD;
                $instanceLR->amount_cash_adv = $amountCashAdv;
                $instanceLR->or_no = $orNO;
                $instanceLR->or_dtd = $orDTD;
                $instanceLR->amount_refunded = $amountRefunded;
                $instanceLR->amount_reimbursed = $amountReimbursed;
                $instanceLR->sig_claimant = $sigClaimant;
                $instanceLR->date_claimant = $dateClaimant;
                $instanceLR->sig_supervisor = $sigSupervisor;
                $instanceLR->date_supervisor = $dateSupervisor;
                $instanceLR->sig_accounting = $sigAccounting;
                $instanceLR->jev_no = $jevNo;
                $instanceLR->date_accounting = $dateAccounting;
                $instanceLR->created_by = Auth::user()->id;
                $instanceLR->save();

                $msg = "$documentType successfully created.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName, ['keyword' => $dvID])
                                 ->with('success', $msg);
            } else {
                $msg = "DV number should not be empty.";
                Auth::user()->log($request, $msg);
                return redirect(url()->previous())
                                     ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        $claimants = [];

        $dvList = DisbursementVoucher::all();
        $liquidationData = LiquidationReport::with('dv')->find($id);
        $dateDV = $liquidationData->dv['date_dv'];
        $periodCover = $liquidationData->period_covered;
        $serialNo = $liquidationData->serial_no;
        $dateLiquidation = $liquidationData->date_liquidation;
        $entityName = $liquidationData->entity_name;
        $fundCluster = $liquidationData->fund_cluster;
        $responsibilityCenter = $liquidationData->responsibility_center;
        $particulars = $liquidationData->particulars;
        $amount = $liquidationData->amount;
        $totalAmount = $liquidationData->total_amount;
        $dvID = $liquidationData->dv_id;
        $dvDTD = $liquidationData->dv_dtd ? $liquidationData->dv_dtd : $dateDV;
        $amountCashAdv = $liquidationData->amount_cash_adv;
        $orNo = $liquidationData->or_no;
        $orDTD = $liquidationData->or_dtd;
        $amountRefunded = $liquidationData->amount_refunded;
        $amountReimbursed = $liquidationData->amount_reimbursed;
        $sigClaimant = $liquidationData->sig_claimant;
        $dateClaimant = $liquidationData->date_claimant;
        $sigSupervisor = $liquidationData->sig_supervisor;
        $dateSupervisor = $liquidationData->date_supervisor;
        $sigAccounting = $liquidationData->sig_accounting;
        $jevNo = $liquidationData->jev_no;
        $dateAccounting = $liquidationData->date_accounting;

        $claimants[] = User::select(
            DB::raw("CONCAT(firstname, ' ', lastname, ' [ ', position, ' ]') as name"),
            'id'
        )->orderBy('firstname')->get();
        $claimants[] = Supplier::select(
            DB::raw("CONCAT(company_name, ' ', ' [ Registered Supplier ]') as company_name"),
            'id'
        )->orderBy('company_name')->get();
        $claimants[] = CustomPayee::select(
            DB::raw("CONCAT(payee_name, ' [ Manually Added ]') as payee_name"),
            'id'
        )->orderBy('payee_name')->get();

        $signatories = Signatory::addSelect([
            'name' => User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                          ->whereColumn('id', 'signatories.emp_id')
                          ->limit(1)
        ])->where('is_active', 'y')->get();

        //$claimants = User::orderBy('firstname')->get();

        foreach ($signatories as $sig) {
            $sig->module = json_decode($sig->module);
        }

        return view('modules.voucher.liquidation.update', compact(
            'signatories', 'claimants', 'amount', 'dvList',
            'periodCover', 'serialNo', 'dateLiquidation', 'entityName',
            'fundCluster', 'responsibilityCenter', 'particulars', 'amount',
            'totalAmount', 'dvID', 'dvDTD', 'amountCashAdv',
            'orNo', 'orDTD', 'amountRefunded', 'amountReimbursed',
            'dateClaimant', 'dateClaimant', 'sigSupervisor', 'dateSupervisor',
            'sigAccounting', 'jevNo', 'dateAccounting', 'id', 'sigClaimant'
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
        $periodCover = $request->period_covered;
        $serialNo = $request->serial_no;
        $dateLiquidation = !empty($request->date_liquidation) ? $request->date_liquidation : NULL;
        $entityName = $request->entity_name;
        $fundCluster = $request->fund_cluster;
        $responsibilityCenter = $request->responsibility_center;
        $particulars = $request->particulars;
        $amount = $request->amount;
        $totalAmount = $request->total_amount;
        $dvID = $request->dv_id;
        $dvDTD = !empty($request->dv_dtd) ? $request->dv_dtd : NULL;
        $amountCashAdv = $request->amount_cash_adv;
        $orNO = $request->or_no;
        $orDTD = !empty($request->or_dtd) ? $request->or_dtd : NULL;
        $amountRefunded = $request->amount_refunded;
        $amountReimbursed = $request->amount_reimbursed;
        $sigClaimant = $request->sig_claimant;
        $dateClaimant = !empty($request->date_claimant) ? $request->date_claimant : NULL;
        $sigSupervisor = $request->sig_supervisor;
        $dateSupervisor = !empty($request->date_supervisor) ? $request->date_supervisor : NULL;
        $sigAccounting = $request->sig_accounting;
        $jevNo = $request->jev_no;
        $dateAccounting = !empty($request->date_accounting) ? $request->date_accounting : NULL;

        $routeName = 'ca-lr';
        $documentType = 'Liquidation Report';

        try {
            $empData = User::where('id', $sigClaimant)->count();
            $supplierData = Supplier::where('id', $sigClaimant)->count();
            $customPayeeData = CustomPayee::where('id', $sigClaimant)
                                         //->orWhere('payee_name', $sigClaimant)
                                         ->count();

            if (!$empData && !$supplierData && !$customPayeeData) {
                $instancePayee = CustomPayee::create([
                    'payee_name' => $sigClaimant
                ]);

                $sigClaimant = $instancePayee->id->string;
            }

            $instanceLR = LiquidationReport::find($id);
            $instanceLR->period_covered = $periodCover;
            $instanceLR->serial_no = $serialNo;
            $instanceLR->date_liquidation = $dateLiquidation;
            $instanceLR->entity_name = $entityName;
            $instanceLR->fund_cluster = $fundCluster;
            $instanceLR->responsibility_center = $responsibilityCenter;
            $instanceLR->particulars = $particulars;
            $instanceLR->amount = $amount;
            $instanceLR->total_amount = $totalAmount;
            $instanceLR->dv_id = $dvID;
            $instanceLR->dv_dtd = $dvDTD;
            $instanceLR->amount_cash_adv = $amountCashAdv;
            $instanceLR->or_no = $orNO;
            $instanceLR->or_dtd = $orDTD;
            $instanceLR->amount_refunded = $amountRefunded;
            $instanceLR->amount_reimbursed = $amountReimbursed;
            $instanceLR->sig_claimant = $sigClaimant;
            $instanceLR->date_claimant = $dateClaimant;
            $instanceLR->sig_supervisor = $sigSupervisor;
            $instanceLR->date_supervisor = $dateSupervisor;
            $instanceLR->sig_accounting = $sigAccounting;
            $instanceLR->jev_no = $jevNo;
            $instanceLR->date_accounting = $dateAccounting;
            $instanceLR->save();

            $msg = "$documentType '$id' successfully updated.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $dvID])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())
                                 ->with('failed', $msg);
        }
    }

    /**
     * Soft deletes the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id) {
        $isDestroy = $request->destroy;

        if ($isDestroy) {
            $response = $this->destroy($request, $id);

            if ($response->alert_type == 'success') {
                return redirect()->route('ca-dv', ['keyword' => $response->id])
                                 ->with($response->alert_type, $response->msg);
            } else {
                return redirect()->route('ca-dv')
                                 ->with($response->alert_type, $response->msg);
            }
        } else {
            try {
                $instanceLR = LiquidationReport::find($id);
                //$instanceORS = ObligationRequestStatus::where('id', $instanceLR->ors_id)->first();
                $documentType = 'Liquidation Report';
                $instanceLR->delete();

                /*
                if ($instanceORS) {
                    $instanceORS->delete();
                }*/

                $msg = "$documentType '$id' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect()->route('ca-dv', ['keyword' => $id])
                                 ->with('success', $msg);
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect()->route('ca-dv')
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
            $instanceLR = LiquidationReport::find($id);
            //$instanceORS = ObligationRequestStatus::where('id', $instanceLR->ors_id)->first();
            $documentType = 'Liquidation Report';
            $instanceLR->forceDelete();

            /*
            if ($instanceORS) {
                $instanceORS->forceDelete();
            }*/

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

    public function showIssue($id) {
        return view('modules.voucher.liquidation.issue', [
            'id' => $id
        ]);
    }

    public function issue(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceLR = LiquidationReport::find($id);
            $documentType = 'Liquidation Report';

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);
            $docStatus = $instanceDocLog->checkDocStatus($id);

            $routeName = 'ca-lr';

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued", $remarks);

                    //$instanceDV->notifyIssued($id, Auth::user()->id);

                    $msg = "$documentType '$id' successfully issued to accounting unit.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route($routeName, ['keyword' => $id])
                                     ->with('success', $msg);
                } else {
                    $msg = "Document for $documentType '$id' should be generated first.";
                    Auth::user()->log($request, $msg);
                    return redirect()->route($routeName, ['keyword' => $id])
                                     ->with('warning', $msg);
                }
            } else {
                $msg = "$documentType '$id' already issued.";
                Auth::user()->log($request, $msg);
                return redirect()->route($routeName, ['keyword' => $id])
                                 ->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showReceive($id) {
        return view('modules.voucher.liquidation.receive', [
            'id' => $id
        ]);
    }

    public function receive(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceLR = LiquidationReport::find($id);
            $documentType = 'Liquidation Report';

            $routeName = 'ca-lr';

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received", $remarks);
            //$instanceLR->notifyReceived($id, Auth::user()->id);

            $msg = "$documentType '$id' successfully received.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showIssueBack($id) {
        return view('modules.voucher.liquidation.issue-back', [
            'id' => $id
        ]);
    }

    public function issueBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceLR = LiquidationReport::find($id);
            $documentType = 'Liquidation Report';

            $routeName = 'ca-lr';

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued_back", $remarks);
            //$instanceLR->notifyIssuedBack($id, Auth::user()->id);

            $msg = "$documentType '$id' successfully issued back.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showReceiveBack($id) {
        return view('modules.voucher.liquidation.receive-back', [
            'id' => $id
        ]);
    }

    public function receiveBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceLR = LiquidationReport::find($id);
            $documentType = 'Liquidation Report';

            $routeName = 'ca-lr';

            $instanceDocLog->logDocument($id, NULL, NULL, "-", NULL);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received_back", $remarks);

            $msg = "$documentType '$id' successfully received back.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showLiquidate($id) {
        $instanceDV = LiquidationReport::find($id);
        $serialNo = $instanceDV->serial_no;
        return view('modules.voucher.liquidation.liquidate', [
            'id' => $id,
            'serialNo' => $serialNo
        ]);
    }

    public function liquidate(Request $request, $id) {
        $serialNo = $request->serial_no;

        try {
            $instanceDocLog = new DocLog;
            $instanceLR = LiquidationReport::find($id);
            $documentType = 'Liquidation Report';

            $routeName = 'ca-lr';

            $instanceLR->date_liquidated = Carbon::now();
            $instanceLR->liquidated_by = Auth::user()->id;
            $instanceLR->serial_no = $serialNo;
            $instanceLR->save();

            //$instanceLR->notifyPayment($id, Auth::user()->id);

            $msg = "$documentType with a serial number of '$serialNo'
                    is successfully set to 'Liquidated'.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function showLogRemarks($id) {
        $instanceDocLog = DocLog::where('doc_id', $id)
                                ->whereNotNull('remarks')
                                ->orderBy('logged_at', 'desc')
                                ->get();
        return view('modules.procurement.dv.remarks', [
            'id' => $id,
            'docRemarks' => $instanceDocLog
        ]);
    }

    public function logRemarks(Request $request, $id) {
        $message = $request->message;

        if (!empty($message)) {
            $instanceLR = LiquidationReport::find($id);
            $instanceDocLog = new DocLog;
            //$instanceLR->notifyMessage($id, Auth::user()->id, $message);
            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "message", $message);
            return 'Sent!';
        }
    }
}
