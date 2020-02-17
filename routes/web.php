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

Route::get('/', 'HomeController@index')->name('dashboard');

Route::group(['middlewareGroups' => ['web']], function () {

    // Registration Routes...
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    // Dashboard
    Route::get('main', 'HomeController@index');

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

    // Purchase Request
    Route::any('procurement/pr', 'PurchaseRequestController@index');
    Route::get('procurement/pr/show/{id}', 'PurchaseRequestController@show');
    Route::get('procurement/pr/tracker/{prNo}', 'PurchaseRequestController@showTrackPR');
    Route::get('procurement/pr/create', 'PurchaseRequestController@create');
    Route::get('procurement/pr/edit/{id}', 'PurchaseRequestController@edit');
    Route::post('procurement/pr/save', 'PurchaseRequestController@store');
    Route::post('procurement/pr/update/{id}', 'PurchaseRequestController@update');
    Route::post('procurement/pr/delete/{id}', 'PurchaseRequestController@delete');
    Route::post('procurement/pr/approve/{id}', [
        'uses' => 'PurchaseRequestController@approve',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/pr/disapprove/{id}', [
        'uses' => 'PurchaseRequestController@disapprove',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/pr/cancel/{id}', 'PurchaseRequestController@cancel');

    // Canvass
    Route::any('procurement/rfq', 'CanvassController@index');
    Route::get('procurement/rfq/show/{id}', 'CanvassController@show');
    Route::get('procurement/rfq/show-issue/{id}', [
        'uses' => 'CanvassController@showIssuedTo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/rfq/issue/{id}', [
        'uses' => 'CanvassController@issue',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/rfq/receive/{id}', [
        'uses' => 'CanvassController@receive',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/rfq/update/{id}', 'CanvassController@update');

    // Abstract
    Route::any('procurement/abstract', [
        'uses' => 'AbstractController@index',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('procurement/abstract/create/{id}', [
        'uses' => 'AbstractController@create',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('procurement/abstract/segment/{id}', [
        'uses' => 'AbstractController@getSegment',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('procurement/abstract/show/{id}', [
        'uses' => 'AbstractController@show',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/abstract/store-update/{id}', [
        'uses' => 'AbstractController@storeUpdate',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/abstract/store-update-items/{id}', [
        'uses' => 'AbstractController@storeUpdateItems',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/abstract/delete/{id}', [
        'uses' => 'AbstractController@delete',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/abstract/receive/{id}', [
        'uses' => 'AbstractController@receive',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/abstract/approve/{id}', [
        'uses' => 'AbstractController@approve',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Purchase and Job Order
    Route::any('procurement/po-jo', [
        'uses' => 'PurchaseJobOrderController@index',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('procurement/po-jo/show/{poNo}', [
        'uses' => 'PurchaseJobOrderController@show',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('procurement/po-jo/show-issue/{poNo}', [
        'uses' => 'PurchaseJobOrderController@showIssuedTo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/update/{poNo}', [
        'uses' => 'PurchaseJobOrderController@update',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/accountant-signed/{poNo}', [
        'uses' => 'PurchaseJobOrderController@accountantSigned',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/approve/{poNo}', [
        'uses' => 'PurchaseJobOrderController@approve',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/issue/{poNo}', [
        'uses' => 'PurchaseJobOrderController@issue',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/receive/{poNo}', [
        'uses' => 'PurchaseJobOrderController@receive',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/cancel/{poNo}', [
        'uses' => 'PurchaseJobOrderController@cancel',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/uncancel/{poNo}', [
        'uses' => 'PurchaseJobOrderController@unCancel',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/create-ors-burs/{poNo}', [
        'uses' => 'PurchaseJobOrderController@createORS_BURS',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/delivery/{poNo}', [
        'uses' => 'PurchaseJobOrderController@delivery',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/po-jo/inspection/{poNo}', [
        'uses' => 'PurchaseJobOrderController@inspection',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Obligation and Request Status/BURS
    Route::any('procurement/ors-burs', [
        'uses' => 'OrsBursController@index',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Budget Officer', 'PSTD']
    ]);
    Route::get('procurement/ors-burs/edit/{key}', [
        'uses' => 'OrsBursController@showEdit',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Budget Officer', 'PSTD']
    ]);
    Route::post('procurement/ors-burs/update/{key}', [
        'uses' => 'OrsBursController@update',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Budget Officer', 'PSTD']
    ]);
    Route::get('procurement/ors-burs/show-issue/{id}', [
        'uses' => 'OrsBursController@showIssuedTo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Budget Officer', 'PSTD']
    ]);
    Route::post('procurement/ors-burs/issue/{id}', [
        'uses' => 'OrsBursController@issue',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Budget Officer', 'PSTD']
    ]);
    Route::post('procurement/ors-burs/receive/{id}', [
        'uses' => 'OrsBursController@receive',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Budget Officer', 'PSTD']
    ]);
    Route::post('procurement/ors-burs/obligate/{id}', [
        'uses' => 'OrsBursController@obligate',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Budget Officer', 'Accountant', 'PSTD']
    ]);

    // Inpection and Acceptance Report
    Route::any('procurement/iar', [
        'uses' => 'InspectionAcceptanceController@index',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('procurement/iar/show/{poNo}', [
        'uses' => 'InspectionAcceptanceController@show',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::get('procurement/iar/show-issue/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@showIssuedTo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/iar/update/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@update',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/iar/issue/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@issue',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);
    Route::post('procurement/iar/inspect/{iarNo}', [
        'uses' => 'InspectionAcceptanceController@inspect',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'PSTD']
    ]);

    // Disbursement Voucher
    Route::any('procurement/dv', [
        'uses' => 'DisbursementVoucherController@index',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Accountant', 'PSTD']
    ]);
    Route::get('procurement/dv/edit/{id}', [
        'uses' => 'DisbursementVoucherController@showEdit',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Accountant', 'PSTD']
    ]);
    Route::post('procurement/dv/update/{id}', [
        'uses' => 'DisbursementVoucherController@update',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Accountant', 'PSTD']
    ]);
    Route::get('procurement/dv/show-issue/{id}', [
        'uses' => 'DisbursementVoucherController@showIssuedTo',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Accountant', 'PSTD']
    ]);
    Route::post('procurement/dv/issue/{id}', [
        'uses' => 'DisbursementVoucherController@issue',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Accountant', 'PSTD']
    ]);
    Route::post('procurement/dv/receive/{id}', [
        'uses' => 'DisbursementVoucherController@receive',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Accountant', 'PSTD']
    ]);
    Route::post('procurement/dv/payment/{id}', [
        'uses' => 'DisbursementVoucherController@payment',
        'middleware' => 'roles',
        'roles' => ['Developer', 'Supply & Property Officer', 'Accountant', 'PSTD']
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
