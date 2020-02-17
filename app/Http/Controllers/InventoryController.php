<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseRequest;
use App\InventoryClassification;
use App\ItemClassification;
use App\InventoryStock;
use App\StockIssue;
use App\PaperSize;
use DB;

class InventoryController extends Controller
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
        $pageLimit = 20;
        $search = trim($request['search']);
        $paperSizes = PaperSize::all();
        $inventoryClass = InventoryClassification::all();
        $inventoryList = DB::table('tblinventory_stocks as inv')
                           ->select('inv.inventory_no', 'created_at')
                           ->join('tblpr_status as status', 'status.id', '=', 'inv.status')
                           ->join('tblinventory_classification as class', 'class.id', '=', 'inv.inventory_class_id');

        if (!empty($search)) {
            $inventoryList = $inventoryList->where(function ($query)  use ($search) {
                                   $query->where('inv.inventory_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('inv.po_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('status.status', 'LIKE', '%' . $search . '%')
                                         ->orWhere('class.classification', 'LIKE', '%' . $search . '%');
                               });
        }

        $inventoryList = $inventoryList//->orderBy('inv.inventory_no', 'desc')
                                       ->orderBy('inv.created_at', 'desc')
                                       ->distinct()

                                       ->paginate($pageLimit, ['inv.inventory_no']);

        // $inventoryList = $inventoryList->orderBy('inv.created_at', 'desc')
        //                                ->paginate($pageLimit, 'inv.inventory_no');

        foreach ($inventoryList as $key => $list) {
            $quantity = 0;
            $invClassAbrv = "";
            $_inventoryList = DB::table('tblinventory_stocks as inv')
                                ->select('inv.inventory_no', 'inv.pr_id', 'inv.id', 'status.status',
                                         'class.classification', 'class.id as class_id')
                                ->join('tblpr_status AS status', 'status.id', '=', 'inv.status')
                                ->join('tblinventory_classification AS class', 'class.id', '=', 'inv.inventory_class_id')
                                ->where('inv.inventory_no', $list->inventory_no)
                                ->first();
            $pr = PurchaseRequest::where('id', $_inventoryList->pr_id)->first();
            $items = array();
            $poItem = DB::table('tblpo_jo_items as po')
                        ->select('po.*', 'inv.id as inventory_id')
                        ->join('tblinventory_stocks as inv', 'inv.po_item_id', '=', 'po.item_id')
                        ->join('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                        ->where('inv.inventory_no', $list->inventory_no)
                        ->orderBy('po.item_id')
                        ->get();

            if ($_inventoryList->class_id == 1) {
                $invClassAbrv = "par";
            } else if ($_inventoryList->class_id == 2) {
                $invClassAbrv = "ris";
            } else if ($_inventoryList->class_id == 3) {
                $invClassAbrv = "ics";
            }

            foreach ($poItem as $item) {
                $quantity = $item->quantity;
                $stockIssue = DB::table('tblinventory_stocks_issue as stocks')
                                ->join('tblinventory_stocks as inv', 'inv.id', '=', 'stocks.inventory_id')
                                ->where('inv.po_item_id', $item->item_id)
                                ->get();

                foreach ($stockIssue as $stock) {
                    $quantity -= $stock->quantity;
                }

                $items[] = (object)['inventory_id' => $item->inventory_id,
                                    'current_quantity' => $quantity,
                                    'original_quantity' => $item->quantity,
                                    'item_description' => $item->item_description,
                                    'excluded' => $item->excluded,
                                    'inventory_classification' => $invClassAbrv];
            }

            $list->po_item = $items;
            $list->pr_deleted_at = $pr->deleted_at;
            $list->status = $_inventoryList->status;
            $list->classification = $_inventoryList->classification;
            $list->classification_abrv = $invClassAbrv;
        }

        return view('pages.stocks', ['search' => $search,
                                     'list' => $inventoryList,
                                     'pageLimit' => $pageLimit,
                                     'paperSizes' => $paperSizes,
                                     'classifications' => $inventoryClass]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($poNo)
    {
        $items = DB::table('tblpo_jo_items as po')
                   ->select('po.*', 'inv.*', 'inv.id as inventory_id', 'unit.unit')
                   ->join('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                   ->leftJoin('tblinventory_stocks as inv', 'inv.po_item_id', '=', 'po.item_id')
                   ->where([['po.po_no', $poNo],
                            ['po.excluded', 'n']])
                   ->orderByRaw('LENGTH(po.item_id)')
                   ->orderBy('po.item_id')
                   ->get();
        $inventoryClassification = InventoryClassification::all();
        $itemClassification = ItemClassification::all();

        return view('pages.create-edit-inventory-items', ['items' => $items,
                                                          'inventoryClassification' => $inventoryClassification,
                                                          'itemClassification' => $itemClassification,
                                                          'poNo' => $poNo]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $poNo)
    {
        $pr = DB::table('tblpr as pr')
                ->select('pr.pr_division_id', 'pr.requested_by', 'pr.purpose','ors.office')
                ->join('tblors_burs as ors', 'ors.pr_id', '=', 'pr.id')
                ->where('ors.po_no', $poNo)
                ->first();
        $po = DB::table('tblpo_jo_items')->where('po_no', $poNo)->first();
        $itemID = $request['item_id'];
        $inventoryClassification = $request['inventory_classification'];
        $itemClassification = $request['item_classification'];
        $groupNo = $request['group_no'];
        $inventoryNo = "";

        foreach ($inventoryClassification as $key => $class) {
            if ($class == 1) {
                $inventoryNo = "PAR-" . $poNo . "-" . $groupNo[$key];
            } else if ($class == 2) {
                $inventoryNo = "RIS-" . $poNo . "-" . $groupNo[$key];
            } else if ($class == 3) {
                $inventoryNo = "ICS-" . $poNo . "-" . $groupNo[$key];
            }

            $inventory = new InventoryStock;
            $inventory->pr_id = $po->pr_id;
            $inventory->po_item_id = $itemID[$key];
            $inventory->po_no = $poNo;
            $inventory->inventory_no = $inventoryNo;
            $inventory->inventory_class_id = $class;
            $inventory->item_class_id = $itemClassification[$key];
            $inventory->requested_by = $pr->requested_by;
            $inventory->office = $pr->office;
            $inventory->division_id = $pr->pr_division_id;
            $inventory->purpose = $pr->purpose;
            $inventory->group_no = $groupNo[$key];
            $inventory->code = $this->generateTrackerCode('STOCK', $inventoryNo, 5);
            $inventory->save();
        }

        return redirect(url('inventory/stocks?search='.$poNo))
              ->with('success', "Created the inventory stocks for IAR-". $poNo);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $key)
    {
        $classification = $request['classification'];
        $type = $request['type'];
        $issuers = array();
        $inventoryNo = "";
        $poNo = "";
        $supplier = "";
        $poDate = "";
        $quantity = 0;
        $issuedBy = 0;
        $receivedBy = 0;
        $approvedBy = 0;
        $stocks;
        $po;
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.ris_sign_type', 'sig.par_sign_type', 'sig.active',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
                                   'sig.ics_sign_type')
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where([['sig.ris', 'y'],
                                  ['sig.active', 'y']])
                         ->orWhere('sig.par', 'y')
                         ->orWhere('sig.ics', 'y')
                         ->orderBy('emp.firstname')
                         ->get();
        $employees = DB::table('tblemp_accounts')
                       ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                               'position', 'emp_id')
                       ->orderBy('firstname')
                       ->get();

        if ($type == 'all') {
            $stocks = DB::table('tblinventory_stocks as inv')
                        ->select('po.*', 'inv.*', 'unit.unit', 'inv.id as inventory_id')
                        ->join('tblpo_jo_items as po', 'po.item_id', '=', 'inv.po_item_id')
                        ->leftJoin('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                        ->leftJoin('tblinventory_stocks_issue as stocks', 'stocks.inventory_id', '=', 'inv.id')
                        ->where('inv.inventory_no', $key)
                        ->orderByRaw('LENGTH(po.item_id)')
                        ->orderBy('po.item_id')
                        ->distinct()
                        ->get();
            $po = DB::table('tblpo_jo as po')
                    ->join('tblinventory_stocks as inv', 'inv.po_no', '=', 'po.po_no')
                    ->join('tblsuppliers as bid', 'bid.id', '=', 'po.awarded_to')
                    ->leftJoin('tbldivision as div', 'div.id', '=', 'inv.division_id')
                    ->leftJoin('tblinventory_stocks_issue as stocks', 'stocks.inventory_id', '=', 'inv.id')
                    ->where('inv.inventory_no', $key)
                    ->first();

            $inventoryNo = $po->inventory_no;
            $poNo = $po->po_no;
            $supplier = $po->company_name;
            $poDate = $po->date_po;
            $issuedBy = $po->issued_by;
            $receivedBy = $po->received_by;
            $approvedBy = $po->approved_by;
            $division = $po->division;
            $office = $po->office;
        } else if ($type == 'this') {
            $stocks = DB::table('tblinventory_stocks as inv')
                        ->select('po.*', 'inv.*', 'unit.unit', 'inv.id as inventory_id')
                        ->join('tblpo_jo_items as po', 'po.item_id', '=', 'inv.po_item_id')
                        ->join('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                        ->leftJoin('tblinventory_stocks_issue as stocks', 'stocks.inventory_id', '=', 'inv.id')
                        ->where('inv.id', $key)
                        ->orderByRaw('LENGTH(po.item_id)')
                        ->orderBy('po.item_id')
                        ->distinct()
                        ->get();
            $po = DB::table('tblpo_jo as po')
                    ->join('tblinventory_stocks as inv', 'inv.po_no', '=', 'po.po_no')
                    ->join('tblsuppliers as bid', 'bid.id', '=', 'po.awarded_to')
                    ->leftJoin('tbldivision as div', 'div.id', '=', 'inv.division_id')
                    ->leftJoin('tblinventory_stocks_issue as stocks', 'stocks.inventory_id', '=', 'inv.id')
                    ->where('inv.id', $key)
                    ->first();

            $inventoryNo = $po->inventory_no;
            $poNo = $po->po_no;
            $supplier = $po->company_name;
            $poDate = $po->date_po;
            $issuedBy = $po->issued_by;
            $receivedBy = $po->received_by;
            $approvedBy = $po->approved_by;
            $division = $po->division;
            $office = $po->office;
        }

        $issuedTo = DB::table('tblinventory_stocks_issue as issued')
                      ->select('emp.emp_id', 'emp.firstname')
                      ->join('tblinventory_stocks as inv', 'inv.id', '=', 'issued.inventory_id')
                      ->join('tblemp_accounts as emp', 'emp.emp_id', '=', 'issued.received_by')
                      ->where('inv.inventory_no', $inventoryNo)
                      ->orderBy('emp.firstname')
                      ->distinct()
                      ->get();

        foreach ($stocks as $stock) {
            $quantity = $stock->quantity;
            $stockIssue = DB::table('tblinventory_stocks_issue as stocks')
                            ->join('tblinventory_stocks as inv', 'inv.id', '=', 'stocks.inventory_id')
                            ->where('inv.po_item_id', $stock->item_id)
                            ->get();

            foreach ($stockIssue as $_stock) {
                $quantity -= $_stock->quantity;
            }

            $stock->current_quantity = $quantity;
        }

        foreach ($issuedTo as $emp) {
            $issuers[] = $emp->emp_id;
        }

        return view('pages.edit-stock', ['key' => $key,
                                         'classification' => $classification,
                                         'type' => $type,
                                         'stocks' => $stocks,
                                         'inventoryNo' => $inventoryNo,
                                         'poNo' => $poNo,
                                         'supplier' => $supplier,
                                         'poDate' => $poDate,
                                         'issuedBy' => $issuedBy,
                                         'receivedBy' => $receivedBy,
                                         'approvedBy' => $approvedBy,
                                         'office' => $office,
                                         'division' => $division,
                                         'signatories' => $signatories,
                                         'employees' => $employees,
                                         'issuers' => $issuers]);
    }

    public function showCreate($classification) {
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.ris_sign_type', 'sig.par_sign_type',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
                                   'sig.ics_sign_type')
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where('sig.ris', 'y')
                         ->orWhere('sig.par', 'y')
                         ->orWhere('sig.ics', 'y')
                         ->orderBy('emp.firstname')
                         ->get();
        $employees = DB::table('tblemp_accounts')
                       ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                               'position', 'emp_id')
                       ->orderBy('firstname')
                       ->get();

        return view('pages.create-stock', ['classification' => $classification,
                                           'signatories' => $signatories,
                                           'employees' => $employees]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showIssuedTo(Request $request, $inventoryNo)
    {
        $classificationAbrv = $request['classification'];
        $issuedTo = DB::table('tblinventory_stocks_issue as issued')
                      ->select('emp.emp_id', 'emp.firstname', 'emp.middlename', 'emp.lastname')
                      ->join('tblinventory_stocks as inv', 'inv.id', '=', 'issued.inventory_id')
                      ->join('tblemp_accounts as emp', 'emp.emp_id', '=', 'issued.received_by')
                      ->where('inv.inventory_no', $inventoryNo)
                      ->orderBy('emp.firstname')
                      ->distinct()
                      ->get();

        return view('pages.view-stock-issued-to', ['issuedTo' => $issuedTo,
                                                   'inventoryNo' => $inventoryNo,
                                                   'classificationAbrv' => $classificationAbrv]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $inventoryNo)
    {
        $type = "all";
        $issuedBy = 0;
        $approvedBy = 0;
        $quantity = 0;
        $receivedBy = $request['received_by'];
        $classification = $request['classification'];
        $signatories = DB::table('tblsignatories AS sig')
                         ->select('sig.id', 'sig.position', 'sig.ris_sign_type', 'sig.par_sign_type',
                                   DB::raw('CONCAT(emp.firstname, " ", emp.lastname) AS name'),
                                   'sig.ics_sign_type')
                         ->join('tblemp_accounts AS emp', 'emp.emp_id', '=', 'sig.emp_id')
                         ->where('sig.ris', 'y')
                         ->orWhere('sig.par', 'y')
                         ->orWhere('sig.ics', 'y')
                         ->orderBy('emp.firstname')
                         ->get();
        $employees = DB::table('tblemp_accounts')
                       ->select(DB::raw('CONCAT(firstname, " ", lastname) AS name'),
                               'position', 'emp_id')
                       ->orderBy('firstname')
                       ->get();
        $stocks = DB::table('tblinventory_stocks_issue as stocks')
                    ->select('stocks.quantity', 'unit.unit', 'po.item_description',
                             'stocks.date_issued', 'po.total_cost', 'inv.property_no',
                             'stocks.issued_by', 'po.stock_no', 'po.quantity as po_qnty',
                             'inv.stock_available', 'stocks.issued_remarks', 'stocks.approved_by',
                             'inv.est_useful_life', 'po.unit_cost', 'po.item_id', 'inv.id as inventory_id',
                             'stocks.received_by', 'stocks.serial_no')
                    ->join('tblinventory_stocks as inv', 'inv.id', '=', 'stocks.inventory_id')
                    ->join('tblpo_jo_items as po', 'po.item_id', '=', 'inv.po_item_id')
                    ->leftJoin('tblunit_issue as unit', 'unit.id', '=', 'po.unit_issue')
                    ->where('inv.inventory_no', $inventoryNo)
                    ->where('stocks.received_by', $receivedBy)
                    ->orderBy('inv.id')
                    ->distinct()
                    ->get();
        $po = DB::table('tblpo_jo as po')
                ->select('po.po_no', 'po.date_po', 'bid.company_name', 'div.division',
                         'inv.office', 'inv.requested_by', 'inv.purpose')
                ->join('tblinventory_stocks as inv', 'inv.po_no', '=', 'po.po_no')
                ->join('tblsuppliers as bid', 'bid.id', '=', 'po.awarded_to')
                ->leftJoin('tbldivision as div', 'div.id', '=', 'inv.division_id')
                ->where('inv.inventory_no', $inventoryNo)
                ->first();

        foreach ($stocks as $stock) {
            $issuedBy = $stock->issued_by;
            $receivedBy = $stock->received_by;
            $approvedBy = $stock->approved_by;
            $quantity = $stock->po_qnty;
            $stockIssue = DB::table('tblinventory_stocks_issue as stocks')
                            ->join('tblinventory_stocks as inv', 'inv.id', '=', 'stocks.inventory_id')
                            ->where('inv.po_item_id', $stock->item_id)
                            ->get();

            foreach ($stockIssue as $_stock) {
                $quantity -= $_stock->quantity;
            }

            $stock->current_quantity = $quantity;
        }

        return view('pages.edit-stock-issued', ['classification' => $classification,
                                                'type' => $type,
                                                'stocks' => $stocks,
                                                'inventoryNo' => $inventoryNo,
                                                'poNo' => $po->po_no,
                                                'supplier' => $po->company_name,
                                                'poDate' => $po->date_po,
                                                'issuedBy' => $issuedBy,
                                                'receivedBy' => $receivedBy,
                                                'approvedBy' => $approvedBy,
                                                'office' => $po->office,
                                                'division' => $po->division,
                                                'signatories' => $signatories,
                                                'employees' => $employees]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $inventoryNo) {
        $classification = $request['classification'];

        $arr_InventoryID = $request['inventory_id'];
        $inventoryNo = $request['inventory_no'];
        $issuedBy = $request['issued_by'];
        $receivedBy = $request['received_by'];
        $arr_Quantity = $request['quantity'];
        $arr_PropertyNo = $request['property_no'];
        $countItem = 0;

        if (!empty($arr_InventoryID)) {
            if ($classification == 'par') {
                $arr_SerialNo = $request['serial_no'];
                $arr_DateIssued = $request['date_issued'];

                foreach ($arr_InventoryID as $counter => $invID) {
                    $inventory = InventoryStock::where('id', $invID)->first();
                    $inventory->inventory_no = $inventoryNo;
                    $inventory->property_no = $arr_PropertyNo[$counter];
                    $inventory->save();

                    DB::table('tblinventory_stocks as inv')
                      ->join('tblinventory_stocks_issue as stocks', 'stocks.inventory_id', '=', 'inv.id')
                      ->where('inv.inventory_no', $inventoryNo)
                      ->where('stocks.inventory_id', $invID)
                      ->where('stocks.received_by', $receivedBy)
                      ->update(['stocks.serial_no' => $arr_SerialNo[$counter],
                                'stocks.quantity' => $arr_Quantity[$counter],
                                'stocks.issued_by' => $issuedBy,
                                'stocks.date_issued' => $arr_DateIssued[$counter],
                               ]);

                    $po = DB::table('tblpo_jo_items as po')
                            ->join('tblinventory_stocks as inv', 'inv.po_item_id', '=', 'po.item_id')
                            ->where('inv.id', $invID)
                            ->update(['po.stock_no' => $arr_PropertyNo[$counter]]);

                    $countItem++;
                }
            } else if ($classification == 'ris') {
                $issuedRemarks = $request['issued_remarks'];
                $approvedBy = $request['approved_by'];

                foreach ($arr_InventoryID as $counter => $invID) {
                    $inventory = InventoryStock::where('id', $invID)->first();
                    $inventory->inventory_no = $inventoryNo;
                    $inventory->property_no = $arr_PropertyNo[$counter];
                    $inventory->save();

                    DB::table('tblinventory_stocks as inv')
                      ->join('tblinventory_stocks_issue as stocks', 'stocks.inventory_id', '=', 'inv.id')
                      ->where('inv.inventory_no', $inventoryNo)
                      ->where('stocks.inventory_id', $invID)
                      ->where('stocks.received_by', $receivedBy)
                      ->update(['stocks.quantity' => $arr_Quantity[$counter],
                                'stocks.issued_by' => $issuedBy,
                                'stocks.issued_remarks' => $issuedRemarks[$counter]
                               ]);

                    $po = DB::table('tblpo_jo_items as po')
                            ->join('tblinventory_stocks as inv', 'inv.po_item_id', '=', 'po.item_id')
                            ->where('inv.id', $invID)
                            ->update(['po.stock_no' => $arr_PropertyNo[$counter]]);

                    $countItem++;
                }
            } else if ($classification == 'ics') {
                $arr_EstUsefulLife = $request['est_useful_life'];
                $arr_SerialNo = $request['serial_no'];
                $arr_DateIssued = $request['date_issued'];

                foreach ($arr_InventoryID as $counter => $invID) {
                    $inventory = InventoryStock::where('id', $invID)->first();
                    $inventory->inventory_no = $inventoryNo;
                    $inventory->property_no = $arr_PropertyNo[$counter];
                    $inventory->est_useful_life = $arr_EstUsefulLife[$counter];
                    $inventory->save();

                    DB::table('tblinventory_stocks as inv')
                      ->join('tblinventory_stocks_issue as stocks', 'stocks.inventory_id', '=', 'inv.id')
                      ->where('inv.inventory_no', $inventoryNo)
                      ->where('stocks.inventory_id', $invID)
                      ->where('stocks.received_by', $receivedBy)
                      ->update(['stocks.serial_no' => $arr_SerialNo[$counter],
                                'stocks.quantity' => $arr_Quantity[$counter],
                                'stocks.issued_by' => $issuedBy,
                                'stocks.date_issued' => $arr_DateIssued[$counter],
                               ]);

                    $po = DB::table('tblpo_jo_items as po')
                            ->join('tblinventory_stocks as inv', 'inv.po_item_id', '=', 'po.item_id')
                            ->where('inv.id', $invID)
                            ->update(['po.stock_no' => $arr_PropertyNo[$counter]]);

                    $countItem++;
                }
            }
        }

        return redirect(url('inventory/stocks?search='.$inventoryNo))
                ->with('success', "Updated inventory no. $inventoryNo to " . $receivedBy . ".");
    }

    public function updateSerialNo(Request $request, $invID) {
        $receivedBy = $request['received_by'];
        $serialNo = $request['serial_no'];
        $stockIssue = StockIssue::where('inventory_id', $invID)
                                ->where('received_by', $receivedBy)
                                ->first();

        $stockIssue->serial_no = $serialNo;
        $stockIssue->save();
    }

    public function issueStocks(Request $request, $key)
    {
        $classification = $request['classification'];
        $type = $request['type'];
        $arr_InventoryID = $request['inventory_id'];
        $inventoryNo = $request['inventory_no'];
        $issuedBy = $request['issued_by'];
        $receivedBy = $request['received_by'];
        $arr_Quantity = $request['quantity'];
        $arr_PropertyNo = $request['property_no'];
        $countItem = 0;

        //InventoryStock
        //StockIssue

        if (!empty($arr_InventoryID)) {
            if ($classification == 'par') {
                $arr_SerialNo = $request['serial_no'];
                $arr_DateIssued = $request['date_issued'];

                foreach ($arr_InventoryID as $counter => $invID) {
                    $inventory = InventoryStock::where('id', $invID)->first();
                    $inventory->inventory_no = $inventoryNo;
                    $inventory->property_no = $arr_PropertyNo[$counter];
                    $inventory->save();

                    $issue = new StockIssue;
                    $issue->pr_id = $inventory->pr_id;
                    $issue->inventory_id = $invID;
                    $issue->serial_no = $arr_SerialNo[$counter];
                    $issue->quantity = $arr_Quantity[$counter];
                    $issue->received_by = $receivedBy;
                    $issue->issued_by = $issuedBy;
                    $issue->date_issued = $arr_DateIssued[$counter];
                    $issue->save();

                    $po = DB::table('tblpo_jo_items as po')
                            ->join('tblinventory_stocks as inv', 'inv.po_item_id', '=', 'po.item_id')
                            ->where('inv.id', $invID)
                            ->update(['po.stock_no' => $arr_PropertyNo[$counter]]);

                    $countItem++;
                }
            } else if ($classification == 'ris') {
                $issuedRemarks = $request['issued_remarks'];
                $approvedBy = $request['approved_by'];

                foreach ($arr_InventoryID as $counter => $invID) {
                    $inventory = InventoryStock::where('id', $invID)->first();
                    $inventory->inventory_no = $inventoryNo;
                    $inventory->property_no = $arr_PropertyNo[$counter];
                    $inventory->save();

                    $issue = new StockIssue;
                    $issue->pr_id = $inventory->pr_id;
                    $issue->inventory_id = $invID;
                    $issue->quantity = $arr_Quantity[$counter];
                    $issue->received_by = $receivedBy;
                    $issue->issued_by = $issuedBy;
                    $issue->issued_remarks = $issuedRemarks[$counter];
                    $issue->approved_by = $approvedBy;
                    $issue->date_issued = date('Y-m-d H:i:s');
                    $issue->save();

                    $po = DB::table('tblpo_jo_items as po')
                            ->join('tblinventory_stocks as inv', 'inv.po_item_id', '=', 'po.item_id')
                            ->where('inv.id', $invID)
                            ->update(['po.stock_no' => $arr_PropertyNo[$counter]]);

                    $countItem++;
                }
            } else if ($classification == 'ics') {
                $arr_EstUsefulLife = $request['est_useful_life'];
                $arr_SerialNo = $request['serial_no'];
                $arr_DateIssued = $request['date_issued'];

                foreach ($arr_InventoryID as $counter => $invID) {
                    $inventory = InventoryStock::where('id', $invID)->first();
                    $inventory->inventory_no = $inventoryNo;
                    $inventory->property_no = $arr_PropertyNo[$counter];
                    $inventory->est_useful_life = $arr_EstUsefulLife[$counter];
                    $inventory->save();

                    $issue = new StockIssue;
                    $issue->pr_id = $inventory->pr_id;
                    $issue->inventory_id = $invID;
                    $issue->serial_no = $arr_SerialNo[$counter];
                    $issue->quantity = $arr_Quantity[$counter];
                    $issue->received_by = $receivedBy;
                    $issue->issued_by = $issuedBy;
                    $issue->date_issued = $arr_DateIssued[$counter];
                    $issue->save();

                    $po = DB::table('tblpo_jo_items as po')
                            ->join('tblinventory_stocks as inv', 'inv.po_item_id', '=', 'po.item_id')
                            ->where('inv.id', $invID)
                            ->update(['po.stock_no' => $arr_PropertyNo[$counter]]);

                    $countItem++;
                }
            }
        } else {
            if ($type == 'all') {
                $inventory = InventoryStock::where('inventory_no', $key)->first();
                $inventory->inventory_no = $inventoryNo;
                $inventory->save();
            } else if ($type == 'this') {
                $inventory = InventoryStock::where('id', $key)->first();
                $inventory->inventory_no = $inventoryNo;
                $inventory->save();
            }
        }

        return redirect(url('inventory/stocks?search='.$inventoryNo))
                ->with('success', "Issued $countItem item/s from inventory no. $inventoryNo to " . $receivedBy . ".");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStocks(Request $request, $poNo)
    {
        $itemID = $request['item_id'];
        $inventoryID = $request['inventory_id'];
        $inventoryClassification = $request['inventory_classification'];
        $itemClassification = $request['item_classification'];
        $groupNo = $request['group_no'];
        $inventoryNo = "";

        foreach ($inventoryID as $key => $id) {
            if ($inventoryClassification[$key] == 1) {
                $inventoryNo = "PAR-" . $poNo . "-" . $groupNo[$key];
            } else if ($inventoryClassification[$key] == 2) {
                $inventoryNo = "RIS-" . $poNo . "-" . $groupNo[$key];
            } else if ($inventoryClassification[$key] == 3) {
                $inventoryNo = "ICS-" . $poNo . "-" . $groupNo[$key];
            }

            $inventory = InventoryStock::where('id', $id)->first();

            if ($inventory) {
                $inventory->inventory_no = $inventoryNo;
                $inventory->inventory_class_id = $inventoryClassification[$key];
                $inventory->item_class_id = $itemClassification[$key];
                $inventory->group_no = $groupNo[$key];
            } else {
                $pr = DB::table('tblpr as pr')
                        ->select('pr.pr_division_id', 'pr.requested_by', 'pr.purpose','ors.office')
                        ->join('tblors_burs as ors', 'ors.pr_id', '=', 'pr.id')
                        ->where('ors.po_no', $poNo)
                        ->first();
                $po = DB::table('tblpo_jo_items')->where('po_no', $poNo)->first();

                $inventory = new InventoryStock;
                $inventory->pr_id = $po->pr_id;
                $inventory->po_item_id = $itemID[$key];
                $inventory->po_no = $poNo;
                $inventory->inventory_no = $inventoryNo;
                $inventory->inventory_class_id = $inventoryClassification[$key];
                $inventory->item_class_id = $itemClassification[$key];
                $inventory->requested_by = $pr->requested_by;
                $inventory->office = $pr->office;
                $inventory->division_id = $pr->pr_division_id;
                $inventory->purpose = $pr->purpose;
                $inventory->group_no = $groupNo[$key];
                $inventory->code = $this->generateTrackerCode('STOCK', $inventoryNo, 5);
            }

            $inventory->save();
        }

        return redirect(url('inventory/stocks?search='.$poNo))->with('success', "Updated the inventory stocks for IAR-". $poNo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $inventoryNo)
    {
        $receivedBy = $request['received_by'];

        $inventory = InventoryStock::where('inventory_no', $inventoryNo)
                                   ->orderBy('id')
                                   ->get();

        foreach ($inventory as $inv) {
            DB::table('tblinventory_stocks_issue')
              ->where('inventory_id', $inv->id)
              ->where('received_by', $receivedBy)
              ->delete();
        }

        return redirect(url('inventory/stocks?search='.$inventoryNo))
               ->with('success', "Successfully deleted the issued stocks for $receivedBy [ $inventoryNo ]");
    }

    public function setIssued($inventoryNo) {
        return redirect(url('inventory/stocks?search='.$inventoryNo))
               ->with('warning', "Under Development");
    }

    private function generateTrackerCode($modAbbr, $pKey, $modClass) {
        $modAbbr = strtoupper($modAbbr);
        $pKey = strtoupper($pKey);

        return $modAbbr . "-" . $pKey . "-" . $modClass . "-" . date('mdY');
    }
}
