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
        'module' => 'procurement_pr',
        'access' => 'is_allowed'
    ])->name('pr-index');
    Route::get('procurement/pr/show-create', [
        'uses' => 'PurchaseRequestController@showCreate',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'create'
    ])->name('pr-show-create');
    Route::post('procurement/pr/store', [
        'uses' => 'PurchaseRequestController@store',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'create'
    ])->name('pr-store');
    Route::get('procurement/pr/show-items/{id}', [
        'uses' => 'PurchaseRequestController@showItems',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'read'
    ])->name('pr-show-items');
    Route::get('procurement/pr/show-edit/{id}', [
        'uses' => 'PurchaseRequestController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'update'
    ])->name('pr-show-edit');
    Route::post('procurement/pr/update/{id}', [
        'uses' => 'PurchaseRequestController@update',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'update'
    ])->name('pr-update');
    Route::post('procurement/pr/delete/{id}', [
        'uses' => 'PurchaseRequestController@delete',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'delete'
    ]);
    Route::post('procurement/pr/approve/{id}', [
        'uses' => 'PurchaseRequestController@approve',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'approve'
    ])->name('pr-approve');
    Route::post('procurement/pr/disapprove/{id}', [
        'uses' => 'PurchaseRequestController@disapprove',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'disapprove'
    ])->name('pr-disapprove');
    Route::post('procurement/pr/cancel/{id}', [
        'uses' => 'PurchaseRequestController@cancel',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'cancel'
    ])->name('pr-cancel');
    Route::get('procurement/pr/tracker/{prNo}', [
        'uses' => 'PurchaseRequestController@showTrackPR',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_pr',
        'access' => 'is_allowed'
    ])->name('pr-tracker');

    // Request for Quotation Module
    Route::any('procurement/rfq', [
        'uses' => 'CanvassController@index',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_rfq',
        'access' => 'is_allowed'
    ]);
    Route::get('procurement/rfq/show-edit/{id}', [
        'uses' => 'CanvassController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_rfq',
        'access' => 'update'
    ]);
    Route::post('procurement/rfq/update/{id}', [
        'uses' => 'CanvassController@update',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_rfq',
        'access' => 'update'
    ]);
    Route::get('procurement/rfq/show-issue/{id}', [
        'uses' => 'CanvassController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_rfq',
        'access' => 'issue'
    ]);
    Route::post('procurement/rfq/issue/{id}', [
        'uses' => 'CanvassController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_rfq',
        'access' => 'issue'
    ]);
    Route::post('procurement/rfq/receive/{id}', [
        'uses' => 'CanvassController@receive',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_rfq',
        'access' => 'receive'
    ]);

    // Abstract of Quotation Module
    Route::any('procurement/abstract', [
        'uses' => 'AbstractController@index',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'is_allowed'
    ]);
    Route::get('procurement/abstract/segment/{id}', [
        'uses' => 'AbstractController@getSegment',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'is_allowed'
    ]);
    Route::get('procurement/abstract/show-create/{id}', [
        'uses' => 'AbstractController@showCreate',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'create'
    ]);
    Route::post('procurement/abstract/store/{id}', [
        'uses' => 'AbstractController@store',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'create'
    ]);
    Route::post('procurement/abstract/store-items/{id}', [
        'uses' => 'AbstractController@storeItems',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'create'
    ]);
    Route::get('procurement/abstract/show-edit/{id}', [
        'uses' => 'AbstractController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'update'
    ]);
    Route::post('procurement/abstract/store-update/{id}', [
        'uses' => 'AbstractController@storeUpdate',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'update'
    ]);
    Route::post('procurement/abstract/update-items/{id}', [
        'uses' => 'AbstractController@updateItems',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'update'
    ]);
    Route::post('procurement/abstract/delete/{id}', [
        'uses' => 'AbstractController@delete',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'delete'
    ]);
    Route::post('procurement/abstract/approve/{id}', [
        'uses' => 'AbstractController@approve',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_abstract',
        'access' => 'approve'
    ]);

    // Purchase and Job Order Module
    Route::any('procurement/po-jo', [
        'uses' => 'PurchaseJobOrderController@index',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'is_allowed'
    ]);
    Route::get('procurement/po-jo/show-edit/{id}', [
        'uses' => 'PurchaseJobOrderController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'update'
    ]);
    Route::post('procurement/po-jo/update/{id}', [
        'uses' => 'PurchaseJobOrderController@update',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'update'
    ]);
    Route::post('procurement/po-jo/accountant-signed/{id}', [
        'uses' => 'PurchaseJobOrderController@accountantSigned',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'accountant_signed'
    ]);
    Route::post('procurement/po-jo/approve/{id}', [
        'uses' => 'PurchaseJobOrderController@approve',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'approve'
    ]);
    Route::get('procurement/po-jo/show-issue/{id}', [
        'uses' => 'PurchaseJobOrderController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'issue'
    ]);
    Route::post('procurement/po-jo/issue/{id}', [
        'uses' => 'PurchaseJobOrderController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'issue'
    ]);
    Route::post('procurement/po-jo/receive/{id}', [
        'uses' => 'PurchaseJobOrderController@receive',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'receive'
    ]);
    Route::post('procurement/po-jo/cancel/{id}', [
        'uses' => 'PurchaseJobOrderController@cancel',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'cancel'
    ]);
    Route::post('procurement/po-jo/uncancel/{id}', [
        'uses' => 'PurchaseJobOrderController@unCancel',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'uncancel'
    ]);
    Route::post('procurement/po-jo/create-ors-burs/{id}', [
        'uses' => 'PurchaseJobOrderController@createORS_BURS',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'create_ors_burs'
    ]);
    Route::post('procurement/po-jo/delivery/{id}', [
        'uses' => 'PurchaseJobOrderController@delivery',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'for_delivery'
    ]);
    Route::post('procurement/po-jo/inspection/{id}', [
        'uses' => 'PurchaseJobOrderController@inspection',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_po_jo',
        'access' => 'to_inspection'
    ]);

    // Obligation and Request Status/BURS Module
    Route::any('procurement/ors-burs', [
        'uses' => 'OrsBursController@index',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_ors_burs',
        'access' => 'is_allowed'
    ]);
    Route::get('procurement/ors-burs/show-edit/{id}', [
        'uses' => 'OrsBursController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_ors_burs',
        'access' => 'update'
    ]);
    Route::post('procurement/ors-burs/update/{id}', [
        'uses' => 'OrsBursController@update',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_ors_burs',
        'access' => 'update'
    ]);
    Route::get('procurement/ors-burs/show-issue/{id}', [
        'uses' => 'OrsBursController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_ors_burs',
        'access' => 'issue'
    ]);
    Route::post('procurement/ors-burs/issue/{id}', [
        'uses' => 'OrsBursController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_ors_burs',
        'access' => 'issue'
    ]);
    Route::post('procurement/ors-burs/receive/{id}', [
        'uses' => 'OrsBursController@receive',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_ors_burs',
        'access' => 'receive'
    ]);
    Route::post('procurement/ors-burs/obligate/{id}', [
        'uses' => 'OrsBursController@obligate',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_ors_burs',
        'access' => 'obligate'
    ]);

    // Inpection and Acceptance Report Module
    Route::any('procurement/iar', [
        'uses' => 'InspectionAcceptanceController@index',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_iar',
        'access' => 'is_allowed'
    ]);
    Route::get('procurement/iar/show-edit/{poNo}', [
        'uses' => 'InspectionAcceptanceController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_iar',
        'access' => 'update'
    ]);
    Route::post('procurement/iar/update/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@update',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_iar',
        'access' => 'update'
    ]);
    Route::get('procurement/iar/show-issue/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_iar',
        'access' => 'issue'
    ]);
    Route::post('procurement/iar/issue/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_iar',
        'access' => 'issue'
    ]);
    Route::post('procurement/iar/inspect/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@inspect',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_iar',
        'access' => 'inspect'
    ]);

    // Disbursement Voucher Module
    Route::any('procurement/dv', [
        'uses' => 'DisbursementVoucherController@index',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_dv',
        'access' => 'is_allowed'
    ]);
    Route::get('procurement/dv/show-edit/{id}', [
        'uses' => 'DisbursementVoucherController@showEdit',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_dv',
        'access' => 'update'
    ]);
    Route::post('procurement/dv/update/{id}', [
        'uses' => 'DisbursementVoucherController@update',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_dv',
        'access' => 'update'
    ]);
    Route::get('procurement/dv/show-issue/{id}', [
        'uses' => 'DisbursementVoucherController@showIssue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_dv',
        'access' => 'issue'
    ]);
    Route::post('procurement/dv/issue/{id}', [
        'uses' => 'DisbursementVoucherController@issue',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_dv',
        'access' => 'issue'
    ]);
    Route::post('procurement/dv/receive/{id}', [
        'uses' => 'DisbursementVoucherController@receive',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_dv',
        'access' => 'receive'
    ]);
    Route::post('procurement/dv/payment/{id}', [
        'uses' => 'DisbursementVoucherController@payment',
        'middleware' => 'moduleaccess',
        'module' => 'procurement_dv',
        'access' => 'disburse'
    ]);

    /*===================== PAYMENT ROUTES =====================*/

    Route::any('payment/lddap', [
        'uses' => 'PaymentController@indexLDDAP',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);
    Route::get('payment/lddap/create', [
        'uses' => 'PaymentController@showCreate',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);
    Route::get('payment/lddap/edit/{lddapID}', [
        'uses' => 'PaymentController@showEdit',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);
    Route::post('payment/lddap/store', [
        'uses' => 'PaymentController@store',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);
    Route::post('payment/lddap/update/{lddapID}', [
        'uses' => 'PaymentController@update',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);
    Route::post('payment/lddap/delete/{lddapID}', [
        'uses' => 'PaymentController@delete',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);
    Route::post('payment/lddap/for-approval/{lddapID}', [
        'uses' => 'PaymentController@forApproval',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);
    Route::post('payment/lddap/approve/{lddapID}', [
        'uses' => 'PaymentController@approve',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Accountant']
    ]);

    /*===================== INVENTORY ROUTES =====================*/

    // Stocks
    Route::any('inventory/stocks', [
        'uses' => 'InventoryController@index',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('inventory/stocks/create/{poNo}', [
        'uses' => 'InventoryController@create',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('inventory/stocks/show/{key}', [
        'uses' => 'InventoryController@show',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('inventory/stocks/show-create/{classification}', [
        'uses' => 'InventoryController@showCreate',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('inventory/stocks/edit/{inventoryNo}', [
        'uses' => 'InventoryController@edit',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('inventory/stocks/issued/{inventoryNo}', [
        'uses' => 'InventoryController@showIssuedTo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('inventory/stocks/store/{inventoryNo}', [
        'uses' => 'InventoryController@store',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('inventory/stocks/issue-stocks/{key}', [
        'uses' => 'InventoryController@issueStocks',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('inventory/stocks/update/{inventoryNo}', [
        'uses' => 'InventoryController@update',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('inventory/stocks/update-stocks/{inventoryNo}', [
        'uses' => 'InventoryController@updateStocks',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('inventory/stocks/update-serial-no/{inventoryNo}', [
        'uses' => 'InventoryController@updateSerialNo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('inventory/stocks/delete/{inventoryNo}', [
        'uses' => 'InventoryController@delete',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('inventory/stocks/set-issued/{inventoryNo}', [
        'uses' => 'InventoryController@setIssued',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

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

    // Divisions
    Route::any('libraries/divisions', [
        'uses' => 'LibrariesController@indexDivisions',
        'middleware' => 'roles',
        'roles' => ['Developer']
    ]);

    // Item Classifications
    Route::any('libraries/item-classification', [
        'uses' => 'LibrariesController@indexItemClass',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Modes of Procurement
    Route::any('libraries/modes-procurement', [
        'uses' => 'LibrariesController@indexModesProcurement',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Procurement Status
    Route::any('libraries/status', [
        'uses' => 'LibrariesController@indexStatus',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Projects
    Route::any('libraries/projects', [
        'uses' => 'LibrariesController@indexProjects',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Signatories
    Route::any('libraries/signatories', [
        'uses' => 'LibrariesController@indexSignatories',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD', 'Accountant']
    ]);

    // Supplier Classifications
    Route::any('libraries/supplier-classification', [
        'uses' => 'LibrariesController@indexSupplierClass',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Suppliers
    Route::any('libraries/suppliers', [
        'uses' => 'LibrariesController@indexSuppliers',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Unit of Issues
    Route::any('libraries/unit-issue', 'LibrariesController@indexUnitIssues');

    // User Accounts
    Route::any('libraries/accounts', [
        'uses' => 'LibrariesController@indexEmployees',
        'middleware' => 'roles',
        'roles' => ['Developer']
    ]);

    // User Groups
    Route::any('libraries/user-groups', [
        'uses' => 'LibrariesController@indexUserGroups',
        'middleware' => 'roles',
        'roles' => ['Developer']
    ]);

    // Create
    Route::get('libraries/create/{type}', [
        'uses' => 'LibrariesController@create',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD', 'Accountant']
    ]);

    // Edit
    Route::get('libraries/edit/{type}', [
        'uses' => 'LibrariesController@edit',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD', 'Accountant']
    ]);

    // Store
    Route::post('libraries/store/{type}', [
        'uses' => 'LibrariesController@store',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD', 'Accountant']
    ]);

    // Update
    Route::post('libraries/update/{type}', [
        'uses' => 'LibrariesController@update',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD', 'Accountant']
    ]);

    // Delete
    Route::post('libraries/delete/{type}', [
        'uses' => 'LibrariesController@delete',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    /*===================== OTHERS =====================*/

    // Profile
    Route::get('profile', 'ProfileController@index');
    Route::get('profile/create', 'ProfileController@create');
    Route::get('profile/edit/{empID}', 'ProfileController@edit');
    Route::post('profile/update/{empID}', 'ProfileController@update');
    Route::post('profile/store', [
        'uses' => 'ProfileController@store',
        'middleware' => 'roles',
        'roles' => ['Developer']
    ]);
    Route::post('profile/delete/{empID}', [
        'uses' => 'ProfileController@delete',
        'middleware' => 'roles',
        'roles' => ['Developer']
    ]);

    // Printing of Documents
    Route::any('print/{key}', 'PrintController@index');

    // PIS to PFMS Database Migrator
    Route::get('migrator', [
        'uses' => 'DatabaseMigratorController@index',
        'middleware' => 'roles',
        'roles' => ['Developer']
    ]);
    Route::post('migrator/temp-pis-import', [
        'uses' => 'DatabaseMigratorController@migrate',
        'middleware' => 'roles',
        'roles' => ['Developer']
    ]);
    Route::any('migrator/migrate-data-modules/{type}', [
        'uses' => 'DatabaseMigratorController@migratePIS_PFMS',
        'middleware' => 'roles',
        'roles' => ['Developer']
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
