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

class ObligationRequestStatusController extends Controller
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

    public function indexProc(Request $request) {
        $data = $this->getIndexData($request, 'procurement');

        // Get module access
        $module = 'proc_ors_burs';
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedObligate = Auth::user()->getModuleAccess($module, 'obligate');
        $isAllowedPO = Auth::user()->getModuleAccess('proc_po_jo', 'is_allowed');

        return view('modules.procurement.ors-burs.index', [
            'list' => $data->ors_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedObligate' => $isAllowedObligate,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedPO' => $isAllowedPO,
        ]);
    }

    public function indexCA(Request $request) {
        $data = $this->getIndexData($request, 'cashadvance');

        // Get module access
        $module = 'ca_ors_burs';
        $isAllowedCreate = Auth::user()->getModuleAccess($module, 'create');
        $isAllowedUpdate = Auth::user()->getModuleAccess($module, 'update');
        $isAllowedIssue = Auth::user()->getModuleAccess($module, 'issue');
        $isAllowedIssueBack = Auth::user()->getModuleAccess($module, 'issue_back');
        $isAllowedReceive = Auth::user()->getModuleAccess($module, 'receive');
        $isAllowedReceiveBack = Auth::user()->getModuleAccess($module, 'receive_back');
        $isAllowedObligate = Auth::user()->getModuleAccess($module, 'obligate');
        $isAllowedDV = Auth::user()->getModuleAccess('ca_dv', 'obligate');

        return view('modules.procurement.ors-burs.index', [
            'list' => $data->ors_data,
            'keyword' => $data->keyword,
            'paperSizes' => $data->paper_sizes,
            'isAllowedCreate' => $isAllowedCreate,
            'isAllowedUpdate' => $isAllowedUpdate,
            'isAllowedObligate' => $isAllowedObligate,
            'isAllowedIssue' => $isAllowedIssue,
            'isAllowedIssueBack'=> $isAllowedIssueBack,
            'isAllowedReceive' => $isAllowedReceive,
            'isAllowedReceiveBack'=> $isAllowedReceiveBack,
            'isAllowedPO' => $isAllowedPO,
        ]);


        /*
        $isOrdinaryUser = true;
        $pageLimit = 50;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $orsList = DB::table('tblors_burs as ors')
                     ->select('ors.*', DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
                              'ors.id as ors_id', 'ors.date_ors_burs')
                     ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'ors.payee')
                     ->where('ors.module_class_id', 2)
                     ->whereNull('ors.deleted_at');

        if (!empty($search)) {
            $orsList = $orsList->where(function ($query)  use ($search) {
                                   $query->where('ors.payee', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.id', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.date_ors_burs', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.particulars', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                         ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                         ->orWhere('ors.code', 'LIKE', '%' . $search . '%');
                               });
        }

        if (Auth::user()->role != 1 && Auth::user()->role != 4) {
            $orsList = $orsList->where('ors.payee', Auth::user()->emp_id);
            $isOrdinaryUser = true;
        } else {
            $isOrdinaryUser = false;
        }

        $orsList = $orsList->orderBy('ors.id', 'desc')
                           ->paginate($pageLimit);

        foreach ($orsList as $list) {
            $dvCount = DisbursementVoucher::where('ors_id', $list->id)->count();
            $list->dv_count = $dvCount;
            $list->document_status = $this->checkDocStatus($list->code);

            if ($dvCount > 0) {
                $dv = DB::table('tbldv')->where('ors_id', $list->id)->first();
                $list->dv_id = $dv->id;
            }

            if (!$isOrdinaryUser) {
                if (!empty($list->document_status->date_issued) &&
                    $list->payee != Auth::user()->emp_id) {
                    $list->display_menu = true;
                } else {
                    $list->display_menu = false;
                }

                if ($list->payee == Auth::user()->emp_id) {
                    $list->display_menu = true;
                }
            } else {
                $list->display_menu = true;
            }
        }

        return view('pages.ors-burs', ['search' => $search,
                                       'list' => $orsList,
                                       'pageLimit' => $pageLimit,
                                       'paperSizes' => $paperSizes,
                                       'type' => 'cashadvance',
                                       'colSpan' => 5]);*/
    }

    private function getIndexData($request, $type) {
        $keyword = trim($request->keyword);
        $instanceDocLog = new DocLog;

        // User groups
        $roleHasOrdinary = Auth::user()->hasOrdinaryRole();
        $empDivisionAccess = !$roleHasOrdinary ? Auth::user()->getDivisionAccess() :
                             [Auth::user()->division];

        // Main data
        $paperSizes = PaperSize::orderBy('paper_type')->get();
        $orsData = PurchaseJobOrder::with('bidpayee')->whereHas('pr', function($query)
                                             use($empDivisionAccess) {
            $query->whereIn('division', $empDivisionAccess);
        })->whereHas('ors', function($query) {
            $query->whereNull('deleted_at');
        });

        if (!empty($keyword)) {
            $orsData = $orsData->where(function($qry) use ($keyword) {
                $qry->where('id', 'like', "%$keyword%")
                    ->orWhere('po_no', 'like', "%$keyword%")
                    ->orWhere('date_po', 'like', "%$keyword%")
                    ->orWhere('grand_total', 'like', "%$keyword%")
                    ->orWhere('document_type', 'like', "%$keyword%")
                    ->orWhereHas('stat', function($query) use ($keyword) {
                        $query->where('status_name', 'like', "%$keyword%");
                    })->orWhereHas('bidpayee', function($query) use ($keyword) {
                        $query->where('company_name', 'like', "%$keyword%")
                              ->orWhere('address', 'like', "%$keyword%");
                    })->orWhereHas('poitems', function($query) use ($keyword) {
                        $query->where('item_description', 'like', "%$keyword%");
                    })->orWhereHas('ors', function($query) use ($keyword) {
                        $query->where('id', 'like', "%$keyword%")
                              ->orWhere('particulars', 'like', "%$keyword%")
                              ->orWhere('document_type', 'like', "%$keyword%")
                              ->orWhere('transaction_type', 'like', "%$keyword%")
                              ->orWhere('serial_no', 'like', "%$keyword%")
                              ->orWhere('date_ors_burs', 'like', "%$keyword%")
                              ->orWhere('date_obligated', 'like', "%$keyword%")
                              ->orWhere('responsibility_center', 'like', "%$keyword%")
                              ->orWhere('uacs_object_code', 'like', "%$keyword%")
                              ->orWhere('amount', 'like', "%$keyword%")
                              ->orWhere('office', 'like', "%$keyword%")
                              ->orWhere('address', 'like', "%$keyword%")
                              ->orWhere('fund_cluster', 'like', "%$keyword%");
                    });
            });
        }

        $orsData = $orsData->sortable(['po_no' => 'desc'])->paginate(15);

        foreach ($orsData as $orsDat) {
            $orsDat->doc_status = $instanceDocLog->checkDocStatus($orsDat->ors['id']);
        }

        return (object) [
            'keyword' => $keyword,
            'ors_data' => $orsData,
            'paper_sizes' => $paperSizes
        ];
    }

    /**
     * Store a newly created resource from PO/JO in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  uuid $poID
     * @return \Illuminate\Http\Response
     */
    public function storeORSFromPO(Request $request, $poID) {
        $orsDocumentType = $request->ors_document_type;

        try {
            $instancePO = PurchaseJobOrder::find($poID);
            $poNo = $instancePO->po_no;
            $prID = $instancePO->pr_id;
            $documentType = $instancePO->document_type;
            $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
            $grandTotal = $instancePO->grand_total;
            $countORS = ObligationRequestStatus::where('po_no', $poNo)->count();

            if ($countORS == 0 && $grandTotal > 0) {
                $instanceORS = new ObligationRequestStatus;
                $instanceORS->pr_id = $prID;
                $instanceORS->po_no = $poNo;
                $instanceORS->responsibility_center = "19 001 03000 14";
                $instanceORS->particulars = "To obligate...";
                $instanceORS->mfo_pap = "3-Regional Office\nA.III.c.1\nA.III.b.1\nA.III.c.2";
                $instanceORS->payee = $instancePO->awarded_to;
                $instanceORS->amount = $instancePO->grand_total;
                $instanceORS->module_class = 3;
                $instanceORS->save();

                $instancePO->for_approval = 'y';
                $instancePO->with_ors_burs = 'y';
                $instancePO->save();

                $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
                $msg = "$documentType '$poNo' successfully created the ORS/BURS document.";
                Auth::user()->log($request, $msg);
                return redirect()->route('proc-ors-burs', ['keyword' => $poNo])
                                 ->with('success', $msg);
            } else {
                $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
                $msg = "$documentType '$poNo' should have a grand total greater than 0 and
                        no existing ORS/BURS document.";
                Auth::user()->log($request, $msg);
                return redirect()->route('po-jo', ['keyword' => $poNo])
                                 ->with('warning', $msg);
            }

            if ($ountORS > 0) {
                ObligationRequestStatus::where('po_no', $poNo)->restore();
            }
        } catch (\Throwable $th) {
            $instanceORS = PurchaseJobOrder::find($poID);
            $poNo = $instanceORS->po_no;

            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect()->route('po-jo', ['keyword' => $poNo])
                             ->with('failed', $msg);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showEdit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
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

            return redirect(url()->previous())
                                 ->with($response->alert_type, $response->msg);
        } else {
            try {
                $instanceORS = ObligationRequestStatus::find($id);
                $documentType = $instanceORS->document_type;
                $documentType = $documentType == 'ors' ? 'Obligation & Request Status' :
                                                 'Budget Utilization & Request Status';
                $instanceORS->delete();

                $msg = "$documentType '$id' successfully deleted.";
                Auth::user()->log($request, $msg);
                return redirect(url()->previous())
                                     ->with('success', $msg);
            } catch (\Throwable $th) {
                $msg = "Unknown error has occured. Please try again.";
                Auth::user()->log($request, $msg);
                return redirect(url()->previous())
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
            $instanceORS = PurchaseJobOrder::find($id);
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'po' ? 'Purchase Order' : 'Job Order';
            $instanceORS->forceDelete();

            $msg = "$documentType '$id' permanently deleted.";
            Auth::user()->log($request, $msg);

            return (object) [
                'msg' => $msg,
                'alert_type' => 'success',
                'pr_id' => $prID
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
        return view('modules.procurement.ors-burs.issue', [
            'id' => $id
        ]);
    }

    public function issue(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            $isDocGenerated = $instanceDocLog->checkDocGenerated($id);
            $docStatus = $instanceDocLog->checkDocStatus($id);

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            if (empty($docStatus->date_issued)) {
                if ($isDocGenerated) {
                    $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued", $remarks);

                    $instanceORS->notifyIssued($id, Auth::user()->id);

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
        return view('modules.procurement.ors-burs.receive', [
            'id' => $id
        ]);
    }

    public function receive(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "received", $remarks);
            $instanceORS->notifyReceived($id, Auth::user()->id);

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
        return view('modules.procurement.ors-burs.issue-back', [
            'id' => $id
        ]);
    }

    public function issueBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            $instanceDocLog->logDocument($id, Auth::user()->id, NULL, "issued_back", $remarks);
            $instanceORS->notifyIssuedBack($id, Auth::user()->id);

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
        return view('modules.procurement.ors-burs.receive-back', [
            'id' => $id
        ]);
    }

    public function receiveBack(Request $request, $id) {
        $remarks = $request->remarks;

        try {
            $instanceDocLog = new DocLog;
            $instanceORS = ObligationRequestStatus::find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

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

    public function showObligate($id) {
        $instanceORS = ObligationRequestStatus::find($id);
        $serialNo = $instanceORS->serial_no;
        return view('modules.procurement.ors-burs.obligate', [
            'id' => $id,
            'serialNo' => $serialNo
        ]);
    }

    public function obligate(Request $request, $id) {
        $serialNo = $request->serial_no;

        try {
            $instanceDocLog = new DocLog;
            $instanceORS = ObligationRequestStatus::with('po')->find($id);
            $moduleClass = $instanceORS->module_class;
            $documentType = $instanceORS->document_type;
            $documentType = $documentType == 'ors' ? 'Obligation Request & Status' :
                                             'Budget Utilization Request & Status';

            if ($moduleClass == 3) {
                $routeName = 'proc-ors-burs';
                $instancePO = PurchaseJobOrder::find($instanceORS->po->id);
                $instancePO->status = 7;
                $instancePO->save();
            } else if ($moduleClass == 2) {
                $routeName = 'ca-ors-burs';
            }

            $instanceORS->date_obligated = Carbon::now();
            $instanceORS->obligated_by = Auth::user()->id;
            $instanceORS->serial_no = $serialNo;
            $instanceORS->save();

            $instanceORS->notifyObligated($id, Auth::user()->id);

            $msg = "$documentType with a serial number of '$serialNo'
                    is successfully obligated.";
            Auth::user()->log($request, $msg);
            return redirect()->route($routeName, ['keyword' => $id])
                             ->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            Auth::user()->log($request, $msg);
            return redirect(url()->previous())->with('failed', $msg);
        }
    }
}
