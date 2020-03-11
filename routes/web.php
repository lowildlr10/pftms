<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middlewareGroups' => ['web']], function () {

    // Registration Routes...
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    // Dashboard
    Route::get('/', 'HomeController@index')->name('dashboard');

    /*===================== CASH ADVANCE, REIMBURSEMENT, & LIQUIDATION ROUTES =====================*/

    // Obligation and Request Status/BURS
    Route::any('cadv-reim-liquidation/ors-burs', 'OrsBursController@indexCashAdvLiquidation');
    Route::get('cadv-reim-liquidation/ors-burs/create', 'OrsBursController@showCreate');
    Route::post('cadv-reim-liquidation/ors-burs/store', 'OrsBursController@store');
    Route::get('cadv-reim-liquidation/ors-burs/edit/{key}', 'OrsBursController@showEdit');
    Route::post('cadv-reim-liquidation/ors-burs/update/{key}', 'OrsBursController@update');
    Route::get('cadv-reim-liquidation/ors-burs/show-issue/{key}', 'OrsBursController@showIssuedTo');
    Route::post('cadv-reim-liquidation/ors-burs/create-dv/{id}', 'OrsBursController@createDV');
    Route::post('cadv-reim-liquidation/ors-burs/delete/{id}', 'OrsBursController@delete');
    Route::post('cadv-reim-liquidation/ors-burs/issue/{id}', 'OrsBursController@issue');
    Route::post('cadv-reim-liquidation/ors-burs/receive/{id}', 'OrsBursController@receive');
    Route::post('cadv-reim-liquidation/ors-burs/obligate/{id}', [
        'uses' => 'OrsBursController@obligate',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Budget Officer']
    ]);

    // Disbursement Voucher
    Route::any('cadv-reim-liquidation/dv', 'DisbursementVoucherController@indexCashAdvLiquidation');
    Route::get('cadv-reim-liquidation/dv/create', 'DisbursementVoucherController@showCreate');
    Route::post('cadv-reim-liquidation/dv/store', 'DisbursementVoucherController@store');
    Route::get('cadv-reim-liquidation/dv/edit/{key}', 'DisbursementVoucherController@showEdit');
    Route::post('cadv-reim-liquidation/dv/update/{key}', 'DisbursementVoucherController@update');
    Route::get('cadv-reim-liquidation/dv/show/{poNo}', 'DisbursementVoucherController@show');
    Route::get('cadv-reim-liquidation/dv/show-issue/{id}', 'DisbursementVoucherController@showIssuedTo');
    Route::post('cadv-reim-liquidation/dv/update/{poNo}', 'DisbursementVoucherController@update');
    Route::post('cadv-reim-liquidation/dv/issue/{id}', 'DisbursementVoucherController@issue');
    Route::post('cadv-reim-liquidation/dv/receive/{id}', 'DisbursementVoucherController@receive');
    Route::post('cadv-reim-liquidation/ors-burs/create-liquidation/{id}',
                'DisbursementVoucherController@createLiquidation');
    Route::post('cadv-reim-liquidation/dv/payment/{id}', [
        'uses' => 'DisbursementVoucherController@payment',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);

    // Liquidation Report
    Route::any('cadv-reim-liquidation/liquidation', 'LiquidationController@indexCashAdvLiquidation');
    //Route::any('cadv-reim-liquidation/liquidation/create', 'LiquidationController@showCreate');
    //Route::post('cadv-reim-liquidation/liquidation/store', 'LiquidationController@store');
    Route::any('cadv-reim-liquidation/liquidation/edit/{id}', 'LiquidationController@showEdit');
    Route::post('cadv-reim-liquidation/liquidation/update/{id}', 'LiquidationController@update');
    Route::get('cadv-reim-liquidation/liquidation/show-issue/{id}', 'LiquidationController@showIssuedTo');
    Route::post('cadv-reim-liquidation/liquidation/issue/{id}', 'LiquidationController@issue');
    Route::post('cadv-reim-liquidation/liquidation/receive/{id}', 'LiquidationController@receive');
    Route::post('cadv-reim-liquidation/liquidation/liquidate/{id}', [
        'uses' => 'LiquidationController@liquidate',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);

    /*===================== PROCUREMENT ROUTES =====================*/

    // Purchase Request Module
    Route::any('procurement/pr', [
        'uses' => 'PurchaseRequestController@index',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'is_allowed'
    ])->name('pr');
    Route::get('procurement/pr/show-create', [
        'uses' => 'PurchaseRequestController@showCreate',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'create'
    ])->name('pr-show-create');
    Route::post('procurement/pr/store', [
        'uses' => 'PurchaseRequestController@store',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'create'
    ])->name('pr-store');
    Route::get('procurement/pr/show-items/{id}', [
        'uses' => 'PurchaseRequestController@showItems',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'read'
    ])->name('pr-show-items');
    Route::get('procurement/pr/show-edit/{id}', [
        'uses' => 'PurchaseRequestController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'update'
    ])->name('pr-show-edit');
    Route::post('procurement/pr/update/{id}', [
        'uses' => 'PurchaseRequestController@update',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'update'
    ])->name('pr-update');
    Route::post('procurement/pr/delete/{id}', [
        'uses' => 'PurchaseRequestController@delete',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'delete'
    ]);
    Route::post('procurement/pr/approve/{id}', [
        'uses' => 'PurchaseRequestController@approve',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'approve'
    ])->name('pr-approve');
    Route::post('procurement/pr/disapprove/{id}', [
        'uses' => 'PurchaseRequestController@disapprove',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'disapprove'
    ])->name('pr-disapprove');
    Route::post('procurement/pr/cancel/{id}', [
        'uses' => 'PurchaseRequestController@cancel',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'cancel'
    ])->name('pr-cancel');
    Route::get('procurement/pr/tracker/{prNo}', [
        'uses' => 'PurchaseRequestController@showTrackPR',
        'middleware' => 'moduleaccess',
        'module' => 'proc_pr',
        'access' => 'is_allowed'
    ])->name('pr-tracker');

    // Request for Quotation Module
    Route::any('procurement/rfq', [
        'uses' => 'CanvassController@index',
        'middleware' => 'moduleaccess',
        'module' => 'proc_rfq',
        'access' => 'is_allowed'
    ])->name('rfq');
    Route::get('procurement/rfq/show-edit/{id}', [
        'uses' => 'CanvassController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'proc_rfq',
        'access' => 'update'
    ])->name('rfq-show-edit');
    Route::post('procurement/rfq/update/{id}', [
        'uses' => 'CanvassController@update',
        'middleware' => 'moduleaccess',
        'module' => 'proc_rfq',
        'access' => 'update'
    ])->name('rfq-update');
    Route::get('procurement/rfq/show-issue/{id}', [
        'uses' => 'CanvassController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_rfq',
        'access' => 'issue'
    ])->name('rfq-show-issue');
    Route::post('procurement/rfq/issue/{id}', [
        'uses' => 'CanvassController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_rfq',
        'access' => 'issue'
    ])->name('rfq-issue');
    Route::post('procurement/rfq/receive/{id}', [
        'uses' => 'CanvassController@receive',
        'middleware' => 'moduleaccess',
        'module' => 'proc_rfq',
        'access' => 'receive'
    ])->name('rfq-receive');

    // Abstract of Quotation Module
    Route::any('procurement/abstract', [
        'uses' => 'AbstractController@index',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'is_allowed'
    ])->name('abstract');
    Route::get('procurement/abstract/segment/{id}', [
        'uses' => 'AbstractController@getSegment',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'is_allowed'
    ])->name('abstract-segment');
    Route::get('procurement/abstract/show-create/{id}', [
        'uses' => 'AbstractController@showCreate',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'create'
    ])->name('abstract-show-create');
    Route::post('procurement/abstract/store/{id}', [
        'uses' => 'AbstractController@store',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'create'
    ])->name('abstract-store');
    Route::post('procurement/abstract/store-items/{id}', [
        'uses' => 'AbstractController@storeItems',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'create'
    ])->name('abstract-store-items');
    Route::get('procurement/abstract/show-edit/{id}', [
        'uses' => 'AbstractController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'update'
    ])->name('abstract-show-edit');
    Route::post('procurement/abstract/store-update/{id}', [
        'uses' => 'AbstractController@storeUpdate',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'update'
    ])->name('abstract-store');
    Route::post('procurement/abstract/update-items/{id}', [
        'uses' => 'AbstractController@updateItems',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'update'
    ])->name('abstract-update');
    Route::post('procurement/abstract/delete/{id}', [
        'uses' => 'AbstractController@delete',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'delete'
    ])->name('abstract-delete');
    Route::post('procurement/abstract/approve/{id}', [
        'uses' => 'AbstractController@approve',
        'middleware' => 'moduleaccess',
        'module' => 'proc_abstract',
        'access' => 'approve'
    ])->name('abstract-approve');

    // Purchase and Job Order Module
    Route::any('procurement/po-jo', [
        'uses' => 'PurchaseJobOrderController@index',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'is_allowed'
    ])->name('po-jo');
    Route::get('procurement/po-jo/show-edit/{id}', [
        'uses' => 'PurchaseJobOrderController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'update'
    ])->name('po-jo-show-edit');
    Route::post('procurement/po-jo/update/{id}', [
        'uses' => 'PurchaseJobOrderController@update',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'update'
    ])->name('po-jo-update');
    Route::post('procurement/po-jo/accountant-signed/{id}', [
        'uses' => 'PurchaseJobOrderController@accountantSigned',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'accountant_signed'
    ])->name('po-jo-accountant-signed');
    Route::post('procurement/po-jo/approve/{id}', [
        'uses' => 'PurchaseJobOrderController@approve',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'approve'
    ])->name('po-jo-approve');
    Route::get('procurement/po-jo/show-issue/{id}', [
        'uses' => 'PurchaseJobOrderController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'issue'
    ])->name('po-jo-show-issue');
    Route::post('procurement/po-jo/issue/{id}', [
        'uses' => 'PurchaseJobOrderController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'issue'
    ])->name('po-jo-issue');
    Route::post('procurement/po-jo/receive/{id}', [
        'uses' => 'PurchaseJobOrderController@receive',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'receive'
    ])->name('po-jo-receive');
    Route::post('procurement/po-jo/cancel/{id}', [
        'uses' => 'PurchaseJobOrderController@cancel',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'cancel'
    ])->name('po-jo-cancel');
    Route::post('procurement/po-jo/uncancel/{id}', [
        'uses' => 'PurchaseJobOrderController@unCancel',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'uncancel'
    ])->name('po-jo-uncancel');
    Route::post('procurement/ors-burs/create-ors-burs/{id}', [
        'uses' => 'PurchaseJobOrderController@createORS_BURS',
        'middleware' => 'moduleaccess',
        'module' => 'proc_ors_burs',
        'access' => 'create'
    ])->name('po-jo-create-ors-burs');
    Route::post('procurement/po-jo/delivery/{id}', [
        'uses' => 'PurchaseJobOrderController@delivery',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'delivery'
    ])->name('po-jo-delivery');
    Route::post('procurement/po-jo/inspection/{id}', [
        'uses' => 'PurchaseJobOrderController@inspection',
        'middleware' => 'moduleaccess',
        'module' => 'proc_po_jo',
        'access' => 'inspection'
    ])->name('po-jo-inspection');

    // Obligation and Request Status/BURS Module
    Route::any('procurement/ors-burs', [
        'uses' => 'OrsBursController@index',
        'middleware' => 'moduleaccess',
        'module' => 'proc_ors_burs',
        'access' => 'is_allowed'
    ])->name('proc-ors-burs');
    Route::get('procurement/ors-burs/show-edit/{id}', [
        'uses' => 'OrsBursController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'proc_ors_burs',
        'access' => 'update'
    ])->name('proc-ors-burs-show-edit');
    Route::post('procurement/ors-burs/update/{id}', [
        'uses' => 'OrsBursController@update',
        'middleware' => 'moduleaccess',
        'module' => 'proc_ors_burs',
        'access' => 'update'
    ])->name('proc-ors-burs-update');
    Route::get('procurement/ors-burs/show-issue/{id}', [
        'uses' => 'OrsBursController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_ors_burs',
        'access' => 'issue'
    ])->name('proc-ors-burs-show-issue');
    Route::post('procurement/ors-burs/issue/{id}', [
        'uses' => 'OrsBursController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_ors_burs',
        'access' => 'issue'
    ])->name('proc-ors-burs-issue');
    Route::post('procurement/ors-burs/receive/{id}', [
        'uses' => 'OrsBursController@receive',
        'middleware' => 'moduleaccess',
        'module' => 'proc_ors_burs',
        'access' => 'receive'
    ])->name('proc-ors-burs-receive');
    Route::post('procurement/ors-burs/obligate/{id}', [
        'uses' => 'OrsBursController@obligate',
        'middleware' => 'moduleaccess',
        'module' => 'proc_ors_burs',
        'access' => 'obligate'
    ])->name('proc-ors-burs-obligate');

    // Inpection and Acceptance Report Module
    Route::any('procurement/iar', [
        'uses' => 'InspectionAcceptanceController@index',
        'middleware' => 'moduleaccess',
        'module' => 'proc_iar',
        'access' => 'is_allowed'
    ])->name('iar');
    Route::get('procurement/iar/show-edit/{poNo}', [
        'uses' => 'InspectionAcceptanceController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'proc_iar',
        'access' => 'update'
    ])->name('iar-show-edit');
    Route::post('procurement/iar/update/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@update',
        'middleware' => 'moduleaccess',
        'module' => 'proc_iar',
        'access' => 'update'
    ])->name('iar-update');
    Route::get('procurement/iar/show-issue/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_iar',
        'access' => 'issue'
    ])->name('iar-show-issue');
    Route::post('procurement/iar/issue/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_iar',
        'access' => 'issue'
    ])->name('iar-issue');
    Route::post('procurement/iar/inspect/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@inspect',
        'middleware' => 'moduleaccess',
        'module' => 'proc_iar',
        'access' => 'inspect'
    ])->name('iar-inspect');

    // Disbursement Voucher Module
    Route::any('procurement/dv', [
        'uses' => 'DisbursementVoucherController@index',
        'middleware' => 'moduleaccess',
        'module' => 'proc_dv',
        'access' => 'is_allowed'
    ])->name('proc-dv');
    Route::get('procurement/dv/show-edit/{id}', [
        'uses' => 'DisbursementVoucherController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'proc_dv',
        'access' => 'update'
    ])->name('proc-dv-show-edit');
    Route::post('procurement/dv/update/{id}', [
        'uses' => 'DisbursementVoucherController@update',
        'middleware' => 'moduleaccess',
        'module' => 'proc_dv',
        'access' => 'update'
    ])->name('proc-dv-update');
    Route::get('procurement/dv/show-issue/{id}', [
        'uses' => 'DisbursementVoucherController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_dv',
        'access' => 'issue'
    ])->name('proc-dv-show-issue');
    Route::post('procurement/dv/issue/{id}', [
        'uses' => 'DisbursementVoucherController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'proc_dv',
        'access' => 'issue'
    ])->name('proc-dv-issue');
    Route::post('procurement/dv/receive/{id}', [
        'uses' => 'DisbursementVoucherController@receive',
        'middleware' => 'moduleaccess',
        'module' => 'proc_dv',
        'access' => 'receive'
    ])->name('proc-dv-receive');
    Route::post('procurement/dv/payment/{id}', [
        'uses' => 'DisbursementVoucherController@payment',
        'middleware' => 'moduleaccess',
        'module' => 'proc_dv',
        'access' => 'disburse'
    ])->name('proc-dv-payment');

    /*===================== PAYMENT ROUTES =====================*/

    Route::any('payment/lddap', [
        'uses' => 'PaymentController@indexLDDAP',
        'middleware' => 'moduleaccess',
        'module' => 'pay_lddap',
        'access' => 'is_allowed'
    ])->name('lddap');
    Route::get('payment/lddap/show-create', [
        'uses' => 'PaymentController@showCreate',
        'middleware' => 'moduleaccess',
        'module' => 'pay_lddap',
        'access' => 'create'
    ])->name('lddap-show-create');
    Route::get('payment/lddap/show-edit/{id}', [
        'uses' => 'PaymentController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'pay_lddap',
        'access' => 'update'
    ])->name('lddap-show-edit');
    Route::post('payment/lddap/store', [
        'uses' => 'PaymentController@store',
        'middleware' => 'moduleaccess',
        'module' => 'pay_lddap',
        'access' => 'create'
    ])->name('lddap-store');
    Route::post('payment/lddap/update/{id}', [
        'uses' => 'PaymentController@update',
        'middleware' => 'moduleaccess',
        'module' => 'pay_lddap',
        'access' => 'update'
    ])->name('lddap-update');
    Route::post('payment/lddap/delete/{id}', [
        'uses' => 'PaymentController@delete',
        'middleware' => 'moduleaccess',
        'module' => 'pay_lddap',
        'access' => 'delete'
    ])->name('lddap-delete');
    Route::post('payment/lddap/for-approval/{id}', [
        'uses' => 'PaymentController@forApproval',
        'middleware' => 'moduleaccess',
        'module' => 'pay_lddap',
        'access' => 'approval'
    ])->name('lddap-for-approval');
    Route::post('payment/lddap/approve/{id}', [
        'uses' => 'PaymentController@approve',
        'middleware' => 'moduleaccess',
        'module' => 'pay_lddap',
        'access' => 'approve'
    ])->name('lddap-approve');

    /*===================== INVENTORY ROUTES =====================*/

    // Stocks
    Route::any('inventory/stocks', [
        'uses' => 'InventoryController@index',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory');
    Route::get('inventory/stocks/create/{poNo}', [
        'uses' => 'InventoryController@create',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-create');
    Route::get('inventory/stocks/show/{key}', [
        'uses' => 'InventoryController@show',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-show');
    Route::get('inventory/stocks/show-create/{classification}', [
        'uses' => 'InventoryController@showCreate',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-show-create');
    Route::get('inventory/stocks/edit/{inventoryNo}', [
        'uses' => 'InventoryController@edit',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-edit');
    Route::get('inventory/stocks/issued/{inventoryNo}', [
        'uses' => 'InventoryController@showIssuedTo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-issued');
    Route::post('inventory/stocks/store/{inventoryNo}', [
        'uses' => 'InventoryController@store',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-store');
    Route::post('inventory/stocks/issue-stocks/{key}', [
        'uses' => 'InventoryController@issueStocks',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-issue-stocks');
    Route::post('inventory/stocks/update/{inventoryNo}', [
        'uses' => 'InventoryController@update',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-update');
    Route::post('inventory/stocks/update-stocks/{inventoryNo}', [
        'uses' => 'InventoryController@updateStocks',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-update-stocks');
    Route::post('inventory/stocks/update-serial-no/{inventoryNo}', [
        'uses' => 'InventoryController@updateSerialNo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-update-serial-no');
    Route::post('inventory/stocks/delete/{inventoryNo}', [
        'uses' => 'InventoryController@delete',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-delete');
    Route::post('inventory/stocks/set-issued/{inventoryNo}', [
        'uses' => 'InventoryController@setIssued',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ])->name('inventory-set-issued');

    /*===================== REPORTS =====================*/

    /*===================== VOUCHER TRACKING =====================*/

    // ORS/BURS

    Route::get('voucher-tracking/{toggle}', 'VoucherLogController@index');
    Route::get('voucher-tracking/generate-table/{toggle}', 'VoucherLogController@show');
    /*
    Route::get('voucher-tracking/{toggle}', [
        'uses' => 'VoucherLogController@index',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant', 'Budget Officer']
    ]);
    Route::get('voucher-tracking/generate-table/{toggle}', [
        'uses' => 'VoucherLogController@show',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant', 'Budget Officer']
    ]);*/

    /*===================== LIBRARIES =====================*/

    // Item Classification Module
    Route::any('libraries/item-classification', [
        'uses' => 'LibraryController@indexItemClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_item_class',
        'access' => 'is_allowed'
    ])->name('item-classification');
    Route::get('libraries/item-classification/show-create', [
        'uses' => 'LibraryController@showCreateItemClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_item_class',
        'access' => 'create'
    ])->name('item-classification-show-create');
    Route::post('libraries/item-classification/store', [
        'uses' => 'LibraryController@storeItemClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_item_class',
        'access' => 'create'
    ])->name('item-classification-store');
    Route::get('libraries/item-classification/show-edit/{id}', [
        'uses' => 'LibraryController@showEditItemClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_item_class',
        'access' => 'update'
    ])->name('item-classification-show-edit');
    Route::post('libraries/item-classification/update/{id}', [
        'uses' => 'LibraryController@updateItemClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_item_class',
        'access' => 'update'
    ])->name('item-classification-update');
    Route::post('libraries/item-classification/delete/{id}', [
        'uses' => 'LibraryController@deleteItemClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_item_class',
        'access' => 'delete'
    ])->name('item-classification-delete');
    Route::post('libraries/item-classification/destroy/{id}', [
        'uses' => 'LibraryController@destroyItemClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_item_class',
        'access' => 'destroy'
    ])->name('item-classification-destroy');

    // Funding Source Module
    Route::any('libraries/funding-source', [
        'uses' => 'LibraryController@indexFundingSource',
        'middleware' => 'moduleaccess',
        'module' => 'lib_funding',
        'access' => 'is_allowed'
    ])->name('funding-source');
    Route::get('libraries/funding-source/show-create', [
        'uses' => 'LibraryController@showCreateFundingSource',
        'middleware' => 'moduleaccess',
        'module' => 'lib_funding',
        'access' => 'create'
    ])->name('funding-source-show-create');
    Route::post('libraries/funding-source/store', [
        'uses' => 'LibraryController@storeFundingSource',
        'middleware' => 'moduleaccess',
        'module' => 'lib_funding',
        'access' => 'create'
    ])->name('funding-source-store');
    Route::get('libraries/funding-source/show-edit/{id}', [
        'uses' => 'LibraryController@showEditFundingSource',
        'middleware' => 'moduleaccess',
        'module' => 'lib_funding',
        'access' => 'update'
    ])->name('funding-source-show-edit');
    Route::post('libraries/funding-source/update/{id}', [
        'uses' => 'LibraryController@updateFundingSource',
        'middleware' => 'moduleaccess',
        'module' => 'lib_funding',
        'access' => 'update'
    ])->name('funding-source-update');
    Route::post('libraries/funding-source/delete/{id}', [
        'uses' => 'LibraryController@deleteFundingSource',
        'middleware' => 'moduleaccess',
        'module' => 'lib_funding',
        'access' => 'delete'
    ])->name('funding-source-delete');
    Route::post('libraries/funding-source/destroy/{id}', [
        'uses' => 'LibraryController@destroyFundingSource',
        'middleware' => 'moduleaccess',
        'module' => 'lib_funding',
        'access' => 'destroy'
    ])->name('funding-source-destroy');

    // Signatory Module
    Route::any('libraries/signatory', [
        'uses' => 'LibraryController@indexSignatory',
        'middleware' => 'moduleaccess',
        'module' => 'lib_signatory',
        'access' => 'is_allowed'
    ])->name('signatory');
    Route::get('libraries/signatory/show-create', [
        'uses' => 'LibraryController@showCreateSignatory',
        'middleware' => 'moduleaccess',
        'module' => 'lib_signatory',
        'access' => 'create'
    ])->name('signatory-show-create');
    Route::post('libraries/signatory/store', [
        'uses' => 'LibraryController@storeSignatory',
        'middleware' => 'moduleaccess',
        'module' => 'lib_signatory',
        'access' => 'create'
    ])->name('signatory-store');
    Route::get('libraries/signatory/show-edit/{id}', [
        'uses' => 'LibraryController@showEditSignatory',
        'middleware' => 'moduleaccess',
        'module' => 'lib_signatory',
        'access' => 'update'
    ])->name('signatory-show-edit');
    Route::post('libraries/signatory/update/{id}', [
        'uses' => 'LibraryController@updateSignatory',
        'middleware' => 'moduleaccess',
        'module' => 'lib_signatory',
        'access' => 'update'
    ])->name('signatory-update');
    Route::post('libraries/signatory/delete/{id}', [
        'uses' => 'LibraryController@deleteSignatory',
        'middleware' => 'moduleaccess',
        'module' => 'lib_signatory',
        'access' => 'delete'
    ])->name('signatory-delete');
    Route::post('libraries/signatory/destroy/{id}', [
        'uses' => 'LibraryController@destroySignatory',
        'middleware' => 'moduleaccess',
        'module' => 'lib_signatory',
        'access' => 'destroy'
    ])->name('signatory-destroy');

    // Supplier Classification Module
    Route::any('libraries/supplier-classification', [
        'uses' => 'LibraryController@indexSupplierClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_sup_class',
        'access' => 'is_allowed'
    ])->name('supplier-classification');
    Route::get('libraries/supplier-classification/show-create', [
        'uses' => 'LibraryController@showCreateSupplierClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_sup_class',
        'access' => 'create'
    ])->name('supplier-classification-show-create');
    Route::post('libraries/supplier-classification/store', [
        'uses' => 'LibraryController@storeSupplierClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_sup_class',
        'access' => 'create'
    ])->name('supplier-classification-store');
    Route::get('libraries/supplier-classification/show-edit/{id}', [
        'uses' => 'LibraryController@showEditSupplierClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_sup_class',
        'access' => 'update'
    ])->name('supplier-classification-show-edit');
    Route::post('libraries/supplier-classification/update/{id}', [
        'uses' => 'LibraryController@updateSupplierClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_sup_class',
        'access' => 'update'
    ])->name('supplier-classification-update');
    Route::post('libraries/supplier-classification/delete/{id}', [
        'uses' => 'LibraryController@deleteSupplierClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_sup_class',
        'access' => 'delete'
    ])->name('supplier-classification-delete');
    Route::post('libraries/supplier-classification/destroy/{id}', [
        'uses' => 'LibraryController@destroySupplierClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_sup_class',
        'access' => 'destroy'
    ])->name('supplier-classification-destroy');

    // Supplier Module
    Route::any('libraries/supplier', [
        'uses' => 'LibraryController@indexSupplier',
        'middleware' => 'moduleaccess',
        'module' => 'lib_supplier',
        'access' => 'is_allowed'
    ])->name('supplier');
    Route::get('libraries/supplier/show-create', [
        'uses' => 'LibraryController@showCreateSupplier',
        'middleware' => 'moduleaccess',
        'module' => 'lib_supplier',
        'access' => 'create'
    ])->name('supplier-show-create');
    Route::post('libraries/supplier/store', [
        'uses' => 'LibraryController@storeSupplier',
        'middleware' => 'moduleaccess',
        'module' => 'lib_supplier',
        'access' => 'create'
    ])->name('supplier-store');
    Route::get('libraries/supplier/show-edit/{id}', [
        'uses' => 'LibraryController@showEditSupplier',
        'middleware' => 'moduleaccess',
        'module' => 'lib_supplier',
        'access' => 'update'
    ])->name('supplier-show-edit');
    Route::post('libraries/supplier/update/{id}', [
        'uses' => 'LibraryController@updateSupplier',
        'middleware' => 'moduleaccess',
        'module' => 'lib_supplier',
        'access' => 'update'
    ])->name('supplier-update');
    Route::post('libraries/supplier/delete/{id}', [
        'uses' => 'LibraryController@deleteSupplier',
        'middleware' => 'moduleaccess',
        'module' => 'lib_supplier',
        'access' => 'delete'
    ])->name('supplier-delete');
    Route::post('libraries/supplier/destroy/{id}', [
        'uses' => 'LibraryController@destroySupplier',
        'middleware' => 'moduleaccess',
        'module' => 'lib_supplier',
        'access' => 'destroy'
    ])->name('supplier-destroy');

    // Item Unit Issue Module
    Route::any('libraries/item-unit-issue', [
        'uses' => 'LibraryController@indexUnitissue',
        'middleware' => 'moduleaccess',
        'module' => 'lib_unit_issue',
        'access' => 'is_allowed'
    ])->name('item-unit-issue');
    Route::get('libraries/item-unit-issue/show-create', [
        'uses' => 'LibraryController@showCreateUnitissue',
        'middleware' => 'moduleaccess',
        'module' => 'lib_unit_issue',
        'access' => 'create'
    ])->name('item-unit-issue-show-create');
    Route::post('libraries/item-unit-issue/store', [
        'uses' => 'LibraryController@storeUnitissue',
        'middleware' => 'moduleaccess',
        'module' => 'lib_unit_issue',
        'access' => 'create'
    ])->name('item-unit-issue-store');
    Route::get('libraries/item-unit-issue/show-edit/{id}', [
        'uses' => 'LibraryController@showEditUnitissue',
        'middleware' => 'moduleaccess',
        'module' => 'lib_unit_issue',
        'access' => 'update'
    ])->name('item-unit-issue-show-edit');
    Route::post('libraries/item-unit-issue/update/{id}', [
        'uses' => 'LibraryController@updateUnitissue',
        'middleware' => 'moduleaccess',
        'module' => 'lib_unit_issue',
        'access' => 'update'
    ])->name('item-unit-issue-update');
    Route::post('libraries/item-unit-issue/delete/{id}', [
        'uses' => 'LibraryController@deleteUnitissue',
        'middleware' => 'moduleaccess',
        'module' => 'lib_unit_issue',
        'access' => 'delete'
    ])->name('item-unit-issue-delete');
    Route::post('libraries/item-unit-issue/destroy/{id}', [
        'uses' => 'LibraryController@destroyUnitissue',
        'middleware' => 'moduleaccess',
        'module' => 'lib_unit_issue',
        'access' => 'destroy'
    ])->name('item-unit-issue-destroy');

    // Procurement Mode Module
    Route::any('libraries/procurement-mode', [
        'uses' => 'LibraryController@indexProcurementMode',
        'middleware' => 'moduleaccess',
        'module' => 'lib_proc_mode',
        'access' => 'is_allowed'
    ])->name('procurement-mode');
    Route::get('libraries/procurement-mode/show-create', [
        'uses' => 'LibraryController@showCreateProcurementMode',
        'middleware' => 'moduleaccess',
        'module' => 'lib_proc_mode',
        'access' => 'create'
    ])->name('procurement-mode-show-create');
    Route::post('libraries/procurement-mode/store', [
        'uses' => 'LibraryController@storeProcurementMode',
        'middleware' => 'moduleaccess',
        'module' => 'lib_proc_mode',
        'access' => 'create'
    ])->name('procurement-mode-store');
    Route::get('libraries/procurement-mode/show-edit/{id}', [
        'uses' => 'LibraryController@showEditProcurementMode',
        'middleware' => 'moduleaccess',
        'module' => 'lib_proc_mode',
        'access' => 'update'
    ])->name('procurement-mode-show-edit');
    Route::post('libraries/procurement-mode/update/{id}', [
        'uses' => 'LibraryController@updateProcurementMode',
        'middleware' => 'moduleaccess',
        'module' => 'lib_proc_mode',
        'access' => 'update'
    ])->name('procurement-mode-update');
    Route::post('libraries/procurement-mode/delete/{id}', [
        'uses' => 'LibraryController@deleteProcurementMode',
        'middleware' => 'moduleaccess',
        'module' => 'lib_proc_mode',
        'access' => 'delete'
    ])->name('procurement-mode-delete');
    Route::post('libraries/procurement-mode/destroy/{id}', [
        'uses' => 'LibraryController@destroyProcurementMode',
        'middleware' => 'moduleaccess',
        'module' => 'lib_proc_mode',
        'access' => 'destroy'
    ])->name('procurement-mode-destroy');

    // Inventory Stock Classification Module
    Route::any('libraries/inventory-classification', [
        'uses' => 'LibraryController@indexInventoryClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_inv_class',
        'access' => 'is_allowed'
    ])->name('inventory-classification');
    Route::get('libraries/inventory-classification/show-create', [
        'uses' => 'LibraryController@showCreateInventoryClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_inv_class',
        'access' => 'create'
    ])->name('inventory-classification-show-create');
    Route::post('libraries/inventory-classification/store', [
        'uses' => 'LibraryController@storeInventoryClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_inv_class',
        'access' => 'create'
    ])->name('inventory-classification-store');
    Route::get('libraries/inventory-classification/show-edit/{id}', [
        'uses' => 'LibraryController@showEditInventoryClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_inv_class',
        'access' => 'update'
    ])->name('inventory-classification-show-edit');
    Route::post('libraries/inventory-classification/update/{id}', [
        'uses' => 'LibraryController@updateInventoryClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_inv_class',
        'access' => 'update'
    ])->name('inventory-classification-update');
    Route::post('libraries/inventory-classification/delete/{id}', [
        'uses' => 'LibraryController@deleteInventoryClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_inv_class',
        'access' => 'delete'
    ])->name('inventory-classification-delete');
    Route::post('libraries/inventory-classification/destroy/{id}', [
        'uses' => 'LibraryController@destroyInventoryClassification',
        'middleware' => 'moduleaccess',
        'module' => 'lib_inv_class',
        'access' => 'destroy'
    ])->name('inventory-classification-destroy');

    // Paper Size Module
    Route::any('libraries/paper-size', [
        'uses' => 'LibraryController@indexPaperSize',
        'middleware' => 'moduleaccess',
        'module' => 'lib_paper_size',
        'access' => 'is_allowed'
    ])->name('paper-size');
    Route::get('libraries/paper-size/show-create', [
        'uses' => 'LibraryController@showCreatePaperSize',
        'middleware' => 'moduleaccess',
        'module' => 'lib_paper_size',
        'access' => 'create'
    ])->name('paper-size-show-create');
    Route::post('libraries/paper-size/store', [
        'uses' => 'LibraryController@storePaperSize',
        'middleware' => 'moduleaccess',
        'module' => 'lib_paper_size',
        'access' => 'create'
    ])->name('paper-size-store');
    Route::get('libraries/paper-size/show-edit/{id}', [
        'uses' => 'LibraryController@showEditPaperSize',
        'middleware' => 'moduleaccess',
        'module' => 'lib_paper_size',
        'access' => 'update'
    ])->name('paper-size-show-edit');
    Route::post('libraries/paper-size/update/{id}', [
        'uses' => 'LibraryController@updatePaperSize',
        'middleware' => 'moduleaccess',
        'module' => 'lib_paper_size',
        'access' => 'update'
    ])->name('paper-size-update');
    Route::post('libraries/paper-size/delete/{id}', [
        'uses' => 'LibraryController@deletePaperSize',
        'middleware' => 'moduleaccess',
        'module' => 'lib_paper_size',
        'access' => 'delete'
    ])->name('paper-size-delete');
    Route::post('libraries/paper-size/destroy/{id}', [
        'uses' => 'LibraryController@destroyPaperSize',
        'uses' => 'AccountController@indexDivision',
        'middleware' => 'moduleaccess',
        'module' => 'lib_paper_size',
        'access' => 'destroy'
    ])->name('paper-size-destroy');

    /*===================== ACCOUNT MANAGEMENT =====================*/

    // Employee Division Module
    Route::any('account-management/emp-division', [
        'uses' => 'AccountController@indexDivision',
        'middleware' => 'moduleaccess',
        'module' => 'acc_division',
        'access' => 'is_allowed'
    ])->name('emp-division');
    Route::get('account-management/emp-division/show-create', [
        'uses' => 'AccountController@showCreateDivision',
        'middleware' => 'moduleaccess',
        'module' => 'acc_division',
        'access' => 'create'
    ])->name('emp-division-show-create');
    Route::post('account-management/emp-division/store', [
        'uses' => 'AccountController@storeDivision',
        'middleware' => 'moduleaccess',
        'module' => 'acc_division',
        'access' => 'create'
    ])->name('emp-division-store');
    Route::get('account-management/emp-division/show-edit/{id}', [
        'uses' => 'AccountController@showEditDivision',
        'middleware' => 'moduleaccess',
        'module' => 'acc_division',
        'access' => 'update'
    ])->name('emp-division-show-edit');
    Route::post('account-management/emp-division/update/{id}', [
        'uses' => 'AccountController@updateDivision',
        'middleware' => 'moduleaccess',
        'module' => 'acc_division',
        'access' => 'update'
    ])->name('emp-division-update');
    Route::post('account-management/emp-division/delete/{id}', [
        'uses' => 'AccountController@deleteDivision',
        'middleware' => 'moduleaccess',
        'module' => 'acc_division',
        'access' => 'delete'
    ])->name('emp-division-delete');
    Route::post('account-management/emp-division/destroy/{id}', [
        'uses' => 'AccountController@destroyDivision',
        'middleware' => 'moduleaccess',
        'module' => 'acc_division',
        'access' => 'destroy'
    ])->name('emp-division-destroy');

    // Employee Role Module
    Route::any('account-management/emp-role', [
        'uses' => 'AccountController@indexRole',
        'middleware' => 'moduleaccess',
        'module' => 'acc_role',
        'access' => 'is_allowed'
    ])->name('emp-role');
    Route::get('account-management/emp-role/show-create', [
        'uses' => 'AccountController@showCreateRole',
        'middleware' => 'moduleaccess',
        'module' => 'acc_role',
        'access' => 'create'
    ])->name('emp-role-show-create');
    Route::post('account-management/emp-role/store', [
        'uses' => 'AccountController@storeRole',
        'middleware' => 'moduleaccess',
        'module' => 'acc_role',
        'access' => 'create'
    ])->name('emp-role-store');
    Route::get('account-management/emp-role/show-edit/{id}', [
        'uses' => 'AccountController@showEditRole',
        'middleware' => 'moduleaccess',
        'module' => 'acc_role',
        'access' => 'update'
    ])->name('emp-role-show-edit');
    Route::post('account-management/emp-role/update/{id}', [
        'uses' => 'AccountController@updateRole',
        'middleware' => 'moduleaccess',
        'module' => 'acc_role',
        'access' => 'update'
    ])->name('emp-role-update');
    Route::post('account-management/emp-role/delete/{id}', [
        'uses' => 'AccountController@deleteRole',
        'middleware' => 'moduleaccess',
        'module' => 'acc_role',
        'access' => 'delete'
    ])->name('emp-role-delete');
    Route::post('account-management/emp-role/destroy/{id}', [
        'uses' => 'AccountController@destroyRole',
        'middleware' => 'moduleaccess',
        'module' => 'acc_role',
        'access' => 'destroy'
    ])->name('emp-role-destroy');

    // Employee Account Module
    Route::any('account-management/emp-account', [
        'uses' => 'AccountController@indexAccount',
        'middleware' => 'moduleaccess',
        'module' => 'acc_account',
        'access' => 'is_allowed'
    ])->name('emp-account');
    Route::get('account-management/emp-account/show-create', [
        'uses' => 'AccountController@showCreateAccount',
        'middleware' => 'moduleaccess',
        'module' => 'acc_account',
        'access' => 'create'
    ])->name('emp-account-show-create');
    Route::post('account-management/emp-account/store', [
        'uses' => 'AccountController@storeAccount',
        'middleware' => 'moduleaccess',
        'module' => 'acc_account',
        'access' => 'create'
    ])->name('emp-account-store');
    Route::get('account-management/emp-account/show-edit/{id}', [
        'uses' => 'AccountController@showEditAccount',
        'middleware' => 'moduleaccess',
        'module' => 'acc_account',
        'access' => 'update'
    ])->name('emp-account-show-edit');
    Route::post('account-management/emp-account/update/{id}', [
        'uses' => 'AccountController@updateAccount',
        'middleware' => 'moduleaccess',
        'module' => 'acc_account',
        'access' => 'update'
    ])->name('emp-account-update');
    Route::post('account-management/emp-account/delete/{id}', [
        'uses' => 'AccountController@deleteAccount',
        'middleware' => 'moduleaccess',
        'module' => 'acc_account',
        'access' => 'delete'
    ])->name('emp-account-delete');
    Route::post('account-management/emp-account/destroy/{id}', [
        'uses' => 'AccountController@destroyAccount',
        'middleware' => 'moduleaccess',
        'module' => 'acc_account',
        'access' => 'destroy'
    ])->name('emp-account-destroy');

    // Employee Group Module
    Route::any('account-management/emp-group', [
        'uses' => 'AccountController@indexGroup',
        'middleware' => 'moduleaccess',
        'module' => 'acc_group',
        'access' => 'is_allowed'
    ])->name('emp-group');
    Route::get('account-management/emp-group/show-create', [
        'uses' => 'AccountController@showCreateGroup',
        'middleware' => 'moduleaccess',
        'module' => 'acc_group',
        'access' => 'create'
    ])->name('emp-group-show-create');
    Route::post('account-management/emp-group/store', [
        'uses' => 'AccountController@storeGroup',
        'middleware' => 'moduleaccess',
        'module' => 'acc_group',
        'access' => 'create'
    ])->name('emp-group-store');
    Route::get('account-management/emp-group/show-edit/{id}', [
        'uses' => 'AccountController@showEditGroup',
        'middleware' => 'moduleaccess',
        'module' => 'acc_group',
        'access' => 'update'
    ])->name('emp-group-show-edit');
    Route::post('account-management/emp-group/update/{id}', [
        'uses' => 'AccountController@updateGroup',
        'middleware' => 'moduleaccess',
        'module' => 'acc_group',
        'access' => 'update'
    ])->name('emp-group-update');
    Route::post('account-management/emp-group/delete/{id}', [
        'uses' => 'AccountController@deleteGroup',
        'middleware' => 'moduleaccess',
        'module' => 'acc_group',
        'access' => 'delete'
    ])->name('emp-group-delete');
    Route::post('account-management/emp-group/destroy/{id}', [
        'uses' => 'AccountController@destroyGroup',
        'middleware' => 'moduleaccess',
        'module' => 'acc_group',
        'access' => 'destroy'
    ])->name('emp-group-destroy');

    // Employee Logs Module
    Route::any('account-management/emp-log', [
        'uses' => 'AccountController@indexLogs',
        'middleware' => 'moduleaccess',
        'module' => 'acc_user_log',
        'access' => 'is_allowed'
    ])->name('emp-log');
    Route::post('account-management/emp-log/destroy/{id}', [
        'uses' => 'AccountController@destroyLogs',
        'middleware' => 'moduleaccess',
        'module' => 'acc_user_log',
        'access' => 'destroy'
    ])->name('emp-log-destroy');

    // Profile
    Route::get('profile', 'AccountController@indexProfile')
         ->name('profile');
    Route::get('profile/registration', 'AccountController@showCreateProfile')
         ->name('profile-registration');
    Route::post('profile/register', 'AccountController@storeProfile')
         ->name('profile-store');
    Route::get('profile/edit', 'AccountController@showEditProfile')
         ->name('profile-show-edit');
    Route::post('profile/update', 'AccountController@updateProfile')
         ->name('profile-update');
    Route::post('profile/destroy', 'AccountController@destroyProfile')
         ->name('profile-destroy');

    /*===================== PLACES =====================*/

    // Region Module
    Route::any('places/region', [
        'uses' => 'PlaceController@indexRegion',
        'middleware' => 'moduleaccess',
        'module' => 'place_region',
        'access' => 'is_allowed'
    ])->name('region');
    Route::get('places/region/show-create', [
        'uses' => 'PlaceController@showCreateRegion',
        'middleware' => 'moduleaccess',
        'module' => 'place_region',
        'access' => 'create'
    ])->name('region-show-create');
    Route::post('places/region/store', [
        'uses' => 'PlaceController@storeRegion',
        'middleware' => 'moduleaccess',
        'module' => 'place_region',
        'access' => 'create'
    ])->name('region-store');
    Route::get('places/region/show-edit/{id}', [
        'uses' => 'PlaceController@showEditRegion',
        'middleware' => 'moduleaccess',
        'module' => 'place_region',
        'access' => 'update'
    ])->name('region-show-edit');
    Route::post('places/region/update/{id}', [
        'uses' => 'PlaceController@updateRegion',
        'middleware' => 'moduleaccess',
        'module' => 'place_region',
        'access' => 'update'
    ])->name('region-update');
    Route::post('places/region/delete/{id}', [
        'uses' => 'PlaceController@deleteRegion',
        'middleware' => 'moduleaccess',
        'module' => 'place_region',
        'access' => 'delete'
    ])->name('region-delete');
    Route::post('places/region/destroy/{id}', [
        'uses' => 'PlaceController@destroyRegion',
        'middleware' => 'moduleaccess',
        'module' => 'place_region',
        'access' => 'destroy'
    ])->name('region-destroy');

    // Province Module
    Route::any('places/province', [
        'uses' => 'PlaceController@indexProvince',
        'middleware' => 'moduleaccess',
        'module' => 'place_province',
        'access' => 'is_allowed'
    ])->name('province');
    Route::get('places/province/show-create', [
        'uses' => 'PlaceController@showCreateProvince',
        'middleware' => 'moduleaccess',
        'module' => 'place_province',
        'access' => 'create'
    ])->name('province-show-create');
    Route::post('places/province/store', [
        'uses' => 'PlaceController@storeProvince',
        'middleware' => 'moduleaccess',
        'module' => 'place_province',
        'access' => 'create'
    ])->name('province-store');
    Route::get('places/province/show-edit/{id}', [
        'uses' => 'PlaceController@showEditProvince',
        'middleware' => 'moduleaccess',
        'module' => 'place_province',
        'access' => 'update'
    ])->name('province-show-edit');
    Route::post('places/province/update/{id}', [
        'uses' => 'PlaceController@updateProvince',
        'middleware' => 'moduleaccess',
        'module' => 'place_province',
        'access' => 'update'
    ])->name('province-update');
    Route::post('places/province/delete/{id}', [
        'uses' => 'PlaceController@deleteProvince',
        'middleware' => 'moduleaccess',
        'module' => 'place_province',
        'access' => 'delete'
    ])->name('province-delete');
    Route::post('places/province/destroy/{id}', [
        'uses' => 'PlaceController@destroyProvince',
        'middleware' => 'moduleaccess',
        'module' => 'place_province',
        'access' => 'destory'
    ])->name('province-destroy');

    /*===================== OTHERS =====================*/

    // Printing of Documents
    Route::any('print/{key}', 'PrintController@index');

    // PIS to PFMS Database Migrator
    Route::get('migrator', [
        'uses' => 'DatabaseMigratorController@index',

    ]);
    Route::post('migrator/temp-pis-import', [
        'uses' => 'DatabaseMigratorController@migrate',

    ]);
    Route::any('migrator/migrate-data-modules/{type}', [
        'uses' => 'DatabaseMigratorController@migratePIS_PFMS',

    ]);

    //Route::any('procurement/pr/tableupdate', 'PurchaseRequestController@tableUpdate');

    /*===================== ATTACHMENT ROUTES =====================*/

    Route::get('attachment/get/{parentID}', 'AttachmentController@showAttachment');
    Route::post('attachment/store', 'AttachmentController@store');
    Route::post('attachment/update/{id}', 'AttachmentController@update');
    Route::post('attachment/delete/{id}', 'AttachmentController@delete');

    /*===================== NOTIFICATION ROUTES =====================*/
    Route::get('notification/mark-as-read/{notifID}', 'NotificationController@makeAsRead');
    Route::get('notification/display', 'NotificationController@displayNotifications');
    Route::get('notification/show-all', 'NotificationController@showAllNotifications');
});
