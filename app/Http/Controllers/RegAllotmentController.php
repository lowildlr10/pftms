<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $roleHasBudget = Auth::user()->hasBudgetRole();
        $roleHasAdministrator = Auth::user()->hasAdministratorRole();
        $roleHasDeveloper = Auth::user()->hasDeveloperRole();

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
            $_periodEnding = strtotime($raod->period_ending);
            $periodEnding = date('F Y', $_periodEnding);
            $raod->period_ending_month = $periodEnding;
            $raod->voucher_count = RegAllotmentItem::where('reg_allotment_id', $raod->id)->count();
        }

        return $fundRAOD;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreate() {
        return view('modules.report.registry-allotment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
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
        $instanceRegAllot = new RegAllotmentItem([
            'reg_allotment_id' => $regID,
            'order_no' => $request->order_no,
            'date_received' => $request->date_received,
            'date_obligated' => $request->date_obligated,
            'date_released' => $request->date_released,
            'payee' => $request->payee,
            'particulars' => $request->particulars,
            'serial_number' => $request->serial_number,
            'uacs_object_code' => $request->uacs_object_code ?
                                  serialize(explode(',', $request->uacs_object_code)) :
                                  serialize([]),
            'allotments' => $request->allotments,
            'obligations' => $request->obligations,
            'unobligated_allot' => $request->unobligated_allot,
            'disbursement' => $request->disbursement,
            'due_demandable' => $request->due_demandable,
            'not_due_demandable' => $request->not_due_demandable,
        ]);
        $instanceRegAllot->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
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
        $uacsObjects = MooeAccountTitle::orderBy('uacs_code')->get();
        $regDat = RegAllotment::find($id);
        $periodEnding = $regDat->period_ending;
        $entityName = $regDat->entity_name;
        $fundCluster = $regDat->fund_cluster;
        $legalBasis = $regDat->legal_basis;
        $mfoPAP = $regDat->mfo_pap;
        $sheetNo = $regDat->sheet_no;
        $regItems = DB::table('obligation_request_status as ors')
                      ->select(
                            'reg.id',
                            'reg.date_received',
                            'reg.date_obligated',
                            'reg.date_released',
                            'reg.payee',
                            'reg.particulars',
                            'reg.serial_number',
                            'reg.uacs_object_code',
                            'reg.allotments',
                            'reg.obligations',
                            'reg.unobligated_allot',
                            'reg.disbursement',
                            'reg.due_demandable',
                            'reg.not_due_demandable',
                            'ors.id as ors_id',
                            'ors.serial_no as ors_serial_no',
                            'ors.date_obligated as ors_date_obligated',
                            'ors.payee as ors_payee',
                            'ors.particulars as ors_particulars',
                            'ors.serial_no as ors_serial_number',
                            'ors.uacs_object_code as ors_uacs_object_code',
                            'ors.amount as ors_amount',
                            'dv.amount as dv_amount'
                        )->leftJoin('funding_reg_allotment_items as reg',
                                 'reg.serial_number', '=', 'ors.serial_no')
                      ->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'ors.id')
                      ->where('ors.date_obligated', 'like', "%$periodEnding%")
                      ->orderBy('reg.order_no')
                      ->get();

        foreach ($regItems as $reg) {
            if (!$reg->id) {
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
            'employees', 'suppliers', 'uacsObjects'
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
        $employees = User::orderBy('firstname')->get();
        $suppliers = Supplier::orderBy('company_name')->get();
        $uacsObjects = MooeAccountTitle::orderBy('uacs_code')->get();
        $vouchers = DB::table('obligation_request_status as ors')
                      ->select(
                          'ors.id as ors_id', 'dv.id as dv_id', 'ors.serial_no as serial_no',
                          'ors.date_obligated as date_obligated', 'ors.payee as payee',
                          'ors.uacs_object_code as uacs_object', 'ors.amount as obligation',
                          'ors.continuing as continuing', 'ors.current as current',
                          'ors.particulars', 'dv.amount as disbursement'
                      )->leftJoin('disbursement_vouchers as dv', 'dv.ors_id', '=', 'ors.id')
                      ->where('ors.date_obligated', 'like', "%$periodEnding%")
                      ->orderBy('ors.date_obligated')
                      ->get();

        foreach ($vouchers as $ors) {
            $logs = DB::table('document_logs')
                      ->where([['doc_id', $ors->ors_id], ['action', 'received']])
                      ->orderBy('created_at', 'desc')
                      ->first();
            $ors->log_date_received = $logs->created_at;
        }

        return view('modules.report.registry-allotment.vouchers-list', compact(
            'employees', 'suppliers', 'vouchers', 'uacsObjects'
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
}
