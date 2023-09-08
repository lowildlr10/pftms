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

/*===================== ACCOUNT MANAGEMENT ROUTES =====================*/

// Profile Module
Route::get('profile/registration', 'AccountController@showCreateProfile')
     ->name('profile-registration');
Route::post('profile/register', 'AccountController@storeProfile')
     ->name('profile-store');
Route::post('profile/get-province/{region_id}', 'AccountController@getProvince');

// Registration Routes...
Route::get('register', 'AccountController@showCreateProfile')
     ->name('register');
Route::post('register', 'AccountController@storeProfile');

Route::middleware(['web', 'auth'])->group(function () {

    /*===================== REPORT ROUTES =====================*/

    // Under Development

    /*===================== VOUCHER TRACKING ROUTES =====================*/

    Route::get('voucher-tracking/{toggle}', 'VoucherLogController@index');
    Route::get('voucher-tracking/generate-table/{toggle}', 'VoucherLogController@show');
    Route::post('voucher-tracking/search', 'VoucherLogController@search')
         ->name('voucher-tracking-search');
    Route::get('v-track/get-search', 'VoucherLogController@getSearch');

    /*===================== ACCOUNT MANAGEMENT ROUTES =====================*/

    // Profile Module
    Route::get('profile', 'AccountController@indexProfile')
         ->name('profile');
    Route::get('profile/edit', 'AccountController@showEditProfile')
         ->name('profile-show-edit');
    Route::post('profile/update', 'AccountController@updateProfile')
         ->name('profile-update');
    Route::post('profile/delete', 'AccountController@deleteProfile')
         ->name('profile-delete');
    Route::post('profile/destroy', 'AccountController@destroyProfile')
         ->name('profile-destroy');

    /*===================== OTHER ROUTES =====================*/

    // Search all
    Route::post('search', 'HomeController@indexSearchAll')->name('search-all');

    // Dashboard Module
    Route::get('/', 'HomeController@index')->name('dashboard');
    Route::get('/show-dashboard/{dashboardID}', 'HomeController@showDashboard')->name('get-dashboard');

    // Document Printing Module
    Route::any('print/{key}', 'PrintController@index')->name('doc-print');

    // Document Attachment Moduule
    Route::get('attachment/get/{parentID}', 'AttachmentController@showAttachment')->name('doc-attach');
    Route::post('attachment/store', 'AttachmentController@store')->name('doc-attach-store');
    Route::post('attachment/update/{id}', 'AttachmentController@update')->name('doc-attach-update');
    Route::post('attachment/destroy/{id}', 'AttachmentController@destroy')->name('doc-attach-destroy');

    // Notification Module
    Route::get('notification/mark-as-read/{notifID}', 'NotificationController@makeAsRead');
    Route::get('notification/display', 'NotificationController@displayNotifications');
    Route::get('notification/show-all', 'NotificationController@showAllNotifications');
});

Route::middleware(['web', 'auth', 'moduleaccess'])->group(function () {

    /*===================== CASH ADVANCE, REIMBURSEMENT, & LIQUIDATION ROUTES =====================*/

    Route::any('cadv-reim-liquidation/ors-burs', [
        'uses' => 'ObligationRequestStatusController@indexCA',
        'module' => 'ca_ors_burs',
        'access' => 'is_allowed'
    ])->name('ca-ors-burs');
    Route::get('cadv-reim-liquidation/ors-burs/show-create', [
        'uses' => 'ObligationRequestStatusController@showCreate',
        'module' => 'ca_ors_burs',
        'access' => 'create'
    ])->name('ca-ors-burs-show-create');
    Route::post('cadv-reim-liquidation/ors-burs/store', [
        'uses' => 'ObligationRequestStatusController@store',
        'module' => 'ca_ors_burs',
        'access' => 'create'
    ])->name('ca-ors-burs-store');
    Route::get('cadv-reim-liquidation/ors-burs/show-edit/{id}', [
        'uses' => 'ObligationRequestStatusController@showEdit',
        'module' => 'ca_ors_burs',
        'access' => 'update'
    ])->name('ca-ors-burs-show-edit');
    Route::post('cadv-reim-liquidation/ors-burs/update/{id}', [
        'uses' => 'ObligationRequestStatusController@update',
        'module' => 'ca_ors_burs',
        'access' => 'update'
    ])->name('ca-ors-burs-update');
    Route::post('cadv-reim-liquidation/ors-burs/delete/{id}', [
        'uses' => 'ObligationRequestStatusController@delete',
        'module' => 'ca_ors_burs',
        'access' => 'delete'
    ])->name('ca-ors-burs-delete');
    Route::post('cadv-reim-liquidation/ors-burs/destroy/{id}', [
        'uses' => 'ObligationRequestStatusController@destroy',
        'module' => 'ca_ors_burs',
        'access' => 'destroy'
    ])->name('ca-ors-burs-destroy');
    Route::get('cadv-reim-liquidation/ors-burs/show-issue/{id}', [
        'uses' => 'ObligationRequestStatusController@showIssue',
        'module' => 'ca_ors_burs',
        'access' => 'issue'
    ])->name('ca-ors-burs-show-issue');
    Route::post('cadv-reim-liquidation/ors-burs/issue/{id}', [
        'uses' => 'ObligationRequestStatusController@issue',
        'module' => 'ca_ors_burs',
        'access' => 'issue'
    ])->name('ca-ors-burs-issue');
    Route::get('cadv-reim-liquidation/ors-burs/show-receive/{id}', [
        'uses' => 'ObligationRequestStatusController@showReceive',
        'module' => 'ca_ors_burs',
        'access' => 'receive'
    ])->name('ca-ors-burs-show-receive');
    Route::post('cadv-reim-liquidation/ors-burs/receive/{id}', [
        'uses' => 'ObligationRequestStatusController@receive',
        'module' => 'ca_ors_burs',
        'access' => 'receive'
    ])->name('ca-ors-burs-receive');
    Route::get('cadv-reim-liquidation/ors-burs/show-issue-back/{id}', [
        'uses' => 'ObligationRequestStatusController@showIssueback',
        'module' => 'ca_ors_burs',
        'access' => 'issue_back'
    ])->name('ca-ors-burs-show-issue-back');
    Route::post('cadv-reim-liquidation/ors-burs/issue-back/{id}', [
        'uses' => 'ObligationRequestStatusController@issueBack',
        'module' => 'ca_ors_burs',
        'access' => 'issue_back'
    ])->name('ca-ors-burs-issue-back');
    Route::get('cadv-reim-liquidation/ors-burs/show-receive-back/{id}', [
        'uses' => 'ObligationRequestStatusController@showReceiveBack',
        'module' => 'ca_ors_burs',
        'access' => 'receive_back'
    ])->name('ca-ors-burs-show-receive-back');
    Route::post('cadv-reim-liquidation/ors-burs/receive-back/{id}', [
        'uses' => 'ObligationRequestStatusController@receiveBack',
        'module' => 'ca_ors_burs',
        'access' => 'receive_back'
    ])->name('ca-ors-burs-receive-back');
    Route::get('cadv-reim-liquidation/ors-burs/show-obligate/{id}', [
        'uses' => 'ObligationRequestStatusController@showObligate',
        'module' => 'ca_ors_burs',
        'access' => 'obligate'
    ])->name('ca-ors-burs-show-obligate');
    Route::post('cadv-reim-liquidation/ors-burs/obligate/{id}', [
        'uses' => 'ObligationRequestStatusController@obligate',
        'module' => 'ca_ors_burs',
        'access' => 'obligate'
    ])->name('ca-ors-burs-obligate');
    Route::get('cadv-reim-liquidation/ors-burs/show-remarks/{id}', [
        'uses' => 'ObligationRequestStatusController@showLogRemarks',
        'module' => 'ca_ors_burs',
        'access' => 'is_allowed'
    ])->name('ca-ors-burs-show-remarks');
    Route::post('cadv-reim-liquidation/ors-burs/create-remarks/{id}', [
        'uses' => 'ObligationRequestStatusController@logRemarks',
        'module' => 'ca_ors_burs',
        'access' => 'is_allowed'
    ])->name('ca-ors-burs-store-remarks');
    Route::get('cadv-reim-liquidation/ors-burs/show-uacs-items/{id}', [
        'uses' => 'ObligationRequestStatusController@showUacsItems',
        'module' => 'ca_ors_burs',
        'access' => 'is_allowed'
    ])->name('ca-ors-burs-show-uacs-items');
    Route::post('cadv-reim-liquidation/ors-burs/update-uacs-items/{id}', [
        'uses' => 'ObligationRequestStatusController@updateUacsItems',
        'module' => 'ca_ors_burs',
        'access' => 'is_allowed'
    ])->name('ca-ors-burs-update-uacs-items');
    Route::any('cadv-reim-liquidation/ors-burs/get-custom-payees', [
        'uses' => 'CollectionController@getCustomPayees',
        'module' => 'ca_ors_burs',
        'access' => 'is_allowed'
    ]);

    // Disbursement Voucher
    Route::any('cadv-reim-liquidation/dv', [
        'uses' => 'DisbursementVoucherController@indexCA',
        'module' => 'ca_dv',
        'access' => 'is_allowed'
    ])->name('ca-dv');
    Route::get('cadv-reim-liquidation/dv/show-create', [
        'uses' => 'DisbursementVoucherController@showCreate',
        'module' => 'ca_dv',
        'access' => 'create'
    ])->name('ca-dv-show-create');
    Route::get('cadv-reim-liquidation/dv/show-create-from-ors/{orsID}', [
        'uses' => 'DisbursementVoucherController@showCreateFromORS',
        'module' => 'ca_dv',
        'access' => 'create'
    ])->name('ca-dv-show-create-ors');
    Route::post('cadv-reim-liquidation/dv/store', [
        'uses' => 'DisbursementVoucherController@store',
        'module' => 'ca_dv',
        'access' => 'create'
    ])->name('ca-dv-store');
    Route::get('cadv-reim-liquidation/dv/show-edit/{id}', [
        'uses' => 'DisbursementVoucherController@showEdit',
        'module' => 'ca_dv',
        'access' => 'update'
    ])->name('ca-dv-show-edit');
    Route::post('cadv-reim-liquidation/dv/update/{id}', [
        'uses' => 'DisbursementVoucherController@update',
        'module' => 'ca_dv',
        'access' => 'update'
    ])->name('ca-dv-update');
    Route::post('cadv-reim-liquidation/dv/delete/{id}', [
        'uses' => 'DisbursementVoucherController@delete',
        'module' => 'ca_dv',
        'access' => 'delete'
    ])->name('ca-dv-delete');
    Route::post('cadv-reim-liquidation/dv/destroy/{id}', [
        'uses' => 'DisbursementVoucherController@destroy',
        'module' => 'ca_dv',
        'access' => 'destroy'
    ])->name('ca-dv-destroy');
    Route::get('cadv-reim-liquidation/dv/show-issue/{id}', [
        'uses' => 'DisbursementVoucherController@showIssue',
        'module' => 'ca_dv',
        'access' => 'issue'
    ])->name('ca-dv-show-issue');
    Route::post('cadv-reim-liquidation/dv/issue/{id}', [
        'uses' => 'DisbursementVoucherController@issue',
        'module' => 'ca_dv',
        'access' => 'issue'
    ])->name('ca-dv-issue');
    Route::get('cadv-reim-liquidation/dv/show-receive/{id}', [
        'uses' => 'DisbursementVoucherController@showReceive',
        'module' => 'ca_dv',
        'access' => 'receive'
    ])->name('ca-dv-show-receive');
    Route::post('cadv-reim-liquidation/dv/receive/{id}', [
        'uses' => 'DisbursementVoucherController@receive',
        'module' => 'ca_dv',
        'access' => 'receive'
    ])->name('ca-dv-receive');
    Route::get('cadv-reim-liquidation/dv/show-issue-back/{id}', [
        'uses' => 'DisbursementVoucherController@showIssueback',
        'module' => 'ca_dv',
        'access' => 'issue_back'
    ])->name('ca-dv-show-issue-back');
    Route::post('cadv-reim-liquidation/dv/issue-back/{id}', [
        'uses' => 'DisbursementVoucherController@issueBack',
        'module' => 'ca_dv',
        'access' => 'issue_back'
    ])->name('ca-dv-issue-back');
    Route::get('cadv-reim-liquidation/dv/show-receive-back/{id}', [
        'uses' => 'DisbursementVoucherController@showReceiveBack',
        'module' => 'ca_dv',
        'access' => 'receive_back'
    ])->name('ca-dv-show-receive-back');
    Route::post('cadv-reim-liquidation/dv/receive-back/{id}', [
        'uses' => 'DisbursementVoucherController@receiveBack',
        'module' => 'ca_dv',
        'access' => 'receive_back'
    ])->name('ca-dv-receive-back');
    Route::get('cadv-reim-liquidation/dv/show-payment/{id}', [
        'uses' => 'DisbursementVoucherController@showPayment',
        'module' => 'ca_dv',
        'access' => 'payment'
    ])->name('ca-dv-show-payment');
    Route::post('cadv-reim-liquidation/dv/payment/{id}', [
        'uses' => 'DisbursementVoucherController@payment',
        'module' => 'ca_dv',
        'access' => 'payment'
    ])->name('ca-dv-payment');
    Route::get('cadv-reim-liquidation/dv/show-disburse/{id}', [
        'uses' => 'DisbursementVoucherController@showDisburse',
        'module' => 'ca_dv',
        'access' => 'disburse'
    ])->name('ca-dv-show-disburse');
    Route::post('cadv-reim-liquidation/dv/disburse/{id}', [
        'uses' => 'DisbursementVoucherController@disburse',
        'module' => 'ca_dv',
        'access' => 'disburse'
    ])->name('ca-dv-disburse');
    Route::get('cadv-reim-liquidation/dv/show-remarks/{id}', [
        'uses' => 'DisbursementVoucherController@showLogRemarks',
        'module' => 'ca_dv',
        'access' => 'is_allowed'
    ])->name('ca-dv-show-remarks');
    Route::post('cadv-reim-liquidation/dv/create-remarks/{id}', [
        'uses' => 'DisbursementVoucherController@logRemarks',
        'module' => 'ca_dv',
        'access' => 'is_allowed'
    ])->name('ca-dv-store-remarks');
    Route::get('cadv-reim-liquidation/dv/show-uacs-items/{id}', [
        'uses' => 'DisbursementVoucherController@showUacsItems',
        'module' => 'ca_dv',
        'access' => 'is_allowed'
    ])->name('ca-dv-show-uacs-items');
    Route::post('cadv-reim-liquidation/dv/update-uacs-items/{id}', [
        'uses' => 'DisbursementVoucherController@updateUacsItems',
        'module' => 'ca_dv',
        'access' => 'is_allowed'
    ])->name('ca-dv-update-uacs-items');
    Route::post('cadv-reim-liquidation/dv/get-custom-payees', [
        'uses' => 'CollectionController@getCustomPayees',
        'module' => 'ca_dv',
        'access' => 'is_allowed'
    ]);

    // Liquidation Report
    Route::any('cadv-reim-liquidation/liquidation', [
        'uses' => 'LiquidationController@indexCA',
        'module' => 'ca_lr',
        'access' => 'is_allowed'
    ])->name('ca-lr');
    Route::get('cadv-reim-liquidation/liquidation/show-create', [
        'uses' => 'LiquidationController@showCreate',
        'module' => 'ca_lr',
        'access' => 'create'
    ])->name('ca-lr-show-create');
    Route::get('cadv-reim-liquidation/liquidation/show-create-from-dv/{dvID}', [
        'uses' => 'LiquidationController@showCreateFromDV',
        'module' => 'ca_lr',
        'access' => 'create'
    ])->name('ca-dv-show-create-lr');
    Route::post('cadv-reim-liquidation/liquidation/store', [
        'uses' => 'LiquidationController@store',
        'module' => 'ca_lr',
        'access' => 'create'
    ])->name('ca-lr-store');
    Route::get('cadv-reim-liquidation/liquidation/show-edit/{id}', [
        'uses' => 'LiquidationController@showEdit',
        'module' => 'ca_lr',
        'access' => 'update'
    ])->name('ca-lr-show-edit');
    Route::post('cadv-reim-liquidation/liquidation/update/{id}', [
        'uses' => 'LiquidationController@update',
        'module' => 'ca_lr',
        'access' => 'update'
    ])->name('ca-lr-update');
    Route::post('cadv-reim-liquidation/liquidation/delete/{id}', [
        'uses' => 'LiquidationController@delete',
        'module' => 'ca_lr',
        'access' => 'delete'
    ])->name('ca-lr-delete');
    Route::post('cadv-reim-liquidation/liquidation/destroy/{id}', [
        'uses' => 'LiquidationController@destroy',
        'module' => 'ca_lr',
        'access' => 'destroy'
    ])->name('ca-lr-destroy');
    Route::get('cadv-reim-liquidation/liquidation/show-issue/{id}', [
        'uses' => 'LiquidationController@showIssue',
        'module' => 'ca_lr',
        'access' => 'issue'
    ])->name('ca-lr-show-issue');
    Route::post('cadv-reim-liquidation/liquidation/issue/{id}', [
        'uses' => 'LiquidationController@issue',
        'module' => 'ca_lr',
        'access' => 'issue'
    ])->name('ca-lr-issue');
    Route::get('cadv-reim-liquidation/liquidation/show-receive/{id}', [
        'uses' => 'LiquidationController@showReceive',
        'module' => 'ca_lr',
        'access' => 'receive'
    ])->name('ca-lr-show-receive');
    Route::post('cadv-reim-liquidation/liquidation/receive/{id}', [
        'uses' => 'LiquidationController@receive',
        'module' => 'ca_lr',
        'access' => 'receive'
    ])->name('ca-lr-receive');
    Route::get('cadv-reim-liquidation/liquidation/show-issue-back/{id}', [
        'uses' => 'LiquidationController@showIssueback',
        'module' => 'ca_lr',
        'access' => 'issue_back'
    ])->name('ca-lr-show-issue-back');
    Route::post('cadv-reim-liquidation/liquidation/issue-back/{id}', [
        'uses' => 'LiquidationController@issueBack',
        'module' => 'ca_lr',
        'access' => 'issue_back'
    ])->name('ca-lr-issue-back');
    Route::get('cadv-reim-liquidation/liquidation/show-receive-back/{id}', [
        'uses' => 'LiquidationController@showReceiveBack',
        'module' => 'ca_lr',
        'access' => 'receive_back'
    ])->name('ca-lr-show-receive-back');
    Route::post('cadv-reim-liquidation/liquidation/receive-back/{id}', [
        'uses' => 'LiquidationController@receiveBack',
        'module' => 'ca_lr',
        'access' => 'receive_back'
    ])->name('ca-lr-receive-back');
    Route::get('cadv-reim-liquidation/liquidation/show-liquidate/{id}', [
        'uses' => 'LiquidationController@showLiquidate',
        'module' => 'ca_lr',
        'access' => 'liquidate'
    ])->name('ca-lr-show-liquidate');
    Route::post('cadv-reim-liquidation/liquidation/liquidate/{id}', [
        'uses' => 'LiquidationController@liquidate',
        'module' => 'ca_lr',
        'access' => 'liquidate'
    ])->name('ca-lr-liquidate');
    Route::get('cadv-reim-liquidation/liquidation/show-remarks/{id}', [
        'uses' => 'LiquidationController@showLogRemarks',
        'module' => 'ca_lr',
        'access' => 'is_allowed'
    ])->name('ca-lr-show-remarks');
    Route::post('cadv-reim-liquidation/liquidation/create-remarks/{id}', [
        'uses' => 'LiquidationController@logRemarks',
        'module' => 'ca_lr',
        'access' => 'is_allowed'
    ])->name('ca-lr-store-remarks');
    Route::post('cadv-reim-liquidation/liquidation/get-custom-claimants', [
        'uses' => 'CollectionController@getCustomPayees',
        'module' => 'ca_lr',
        'access' => 'is_allowed'
    ]);

    /*===================== PROCUREMENT ROUTES =====================*/

    // Purchase Request Module
    Route::any('procurement/pr', [
        'uses' => 'PurchaseRequestController@index',
        'module' => 'proc_pr',
        'access' => 'is_allowed'
    ])->name('pr');
    Route::post('procurement/pr/s/{keyword}', [
        'uses' => 'PurchaseRequestController@index',
        'module' => 'proc_pr',
        'access' => 'is_allowed'
    ])->name('pr-search');
    Route::get('procurement/pr/show-create', [
        'uses' => 'PurchaseRequestController@showCreate',
        'module' => 'proc_pr',
        'access' => 'create'
    ])->name('pr-show-create');
    Route::post('procurement/pr/store', [
        'uses' => 'PurchaseRequestController@store',
        'module' => 'proc_pr',
        'access' => 'create'
    ])->name('pr-store');
    Route::get('procurement/pr/show-items/{id}', [
        'uses' => 'PurchaseRequestController@showItems',
        'module' => 'proc_pr',
        'access' => 'is_allowed'
    ])->name('pr-show-items');
    Route::get('procurement/pr/show-edit/{id}', [
        'uses' => 'PurchaseRequestController@showEdit',
        'module' => 'proc_pr',
        'access' => 'update'
    ])->name('pr-show-edit');
    Route::post('procurement/pr/update/{id}', [
        'uses' => 'PurchaseRequestController@update',
        'module' => 'proc_pr',
        'access' => 'update'
    ])->name('pr-update');
    Route::post('procurement/pr/delete/{id}', [
        'uses' => 'PurchaseRequestController@delete',
        'module' => 'proc_pr',
        'access' => 'delete'
    ])->name('pr-delete');
    Route::post('procurement/pr/approve/{id}', [
        'uses' => 'PurchaseRequestController@approve',
        'module' => 'proc_pr',
        'access' => 'approve'
    ])->name('pr-approve');
    Route::post('procurement/pr/disapprove/{id}', [
        'uses' => 'PurchaseRequestController@disapprove',
        'module' => 'proc_pr',
        'access' => 'disapprove'
    ])->name('pr-disapprove');
    Route::post('procurement/pr/cancel/{id}', [
        'uses' => 'PurchaseRequestController@cancel',
        'module' => 'proc_pr',
        'access' => 'cancel'
    ])->name('pr-cancel');
    Route::post('procurement/pr/uncancel/{id}', [
        'uses' => 'PurchaseRequestController@uncancel',
        'module' => 'proc_pr',
        'access' => 'cancel'
    ])->name('pr-uncancel');
    Route::get('procurement/pr/tracker/{prNo}', [
        'uses' => 'PurchaseRequestController@showTrackPR',
        'module' => 'proc_pr',
        'access' => 'is_allowed'
    ])->name('pr-tracker');

    // Request for Quotation Module
    Route::any('procurement/rfq', [
        'uses' => 'RequestQuotationController@index',
        'module' => 'proc_rfq',
        'access' => 'is_allowed'
    ])->name('rfq');
    Route::get('procurement/rfq/show-edit/{id}', [
        'uses' => 'RequestQuotationController@showEdit',
        'module' => 'proc_rfq',
        'access' => 'update'
    ])->name('rfq-show-edit');
    Route::post('procurement/rfq/update/{id}', [
        'uses' => 'RequestQuotationController@update',
        'module' => 'proc_rfq',
        'access' => 'update'
    ])->name('rfq-update');
    Route::get('procurement/rfq/show-issue/{id}', [
        'uses' => 'RequestQuotationController@showIssue',
        'module' => 'proc_rfq',
        'access' => 'issue'
    ])->name('rfq-show-issue');
    Route::post('procurement/rfq/issue/{id}', [
        'uses' => 'RequestQuotationController@issue',
        'module' => 'proc_rfq',
        'access' => 'issue'
    ])->name('rfq-issue');
    Route::get('procurement/rfq/show-receive/{id}', [
        'uses' => 'RequestQuotationController@showReceive',
        'module' => 'proc_rfq',
        'access' => 'receive'
    ])->name('rfq-show-receive');
    Route::post('procurement/rfq/receive/{id}', [
        'uses' => 'RequestQuotationController@receive',
        'module' => 'proc_rfq',
        'access' => 'receive'
    ])->name('rfq-receive');

    // Abstract of Quotation Module
    Route::any('procurement/abstract', [
        'uses' => 'AbstractQuotationController@index',
        'module' => 'proc_abstract',
        'access' => 'is_allowed'
    ])->name('abstract');
    Route::get('procurement/abstract/item-segment/{id}', [
        'uses' => 'AbstractQuotationController@showItemSegment',
        'module' => 'proc_abstract',
        'access' => 'is_allowed'
    ])->name('abstract-segment');
    Route::get('procurement/abstract/show-create/{id}', [
        'uses' => 'AbstractQuotationController@showCreate',
        'module' => 'proc_abstract',
        'access' => 'create'
    ])->name('abstract-show-create');
    Route::post('procurement/abstract/store/{id}', [
        'uses' => 'AbstractQuotationController@store',
        'module' => 'proc_abstract',
        'access' => 'create'
    ])->name('abstract-store');
    Route::post('procurement/abstract/store-items/{id}', [
        'uses' => 'AbstractQuotationController@storeItems',
        'module' => 'proc_abstract',
        'access' => 'create'
    ])->name('abstract-store-items');
    Route::get('procurement/abstract/show-edit/{id}', [
        'uses' => 'AbstractQuotationController@showEdit',
        'module' => 'proc_abstract',
        'access' => 'update'
    ])->name('abstract-show-edit');
    Route::post('procurement/abstract/update/{id}', [
        'uses' => 'AbstractQuotationController@update',
        'module' => 'proc_abstract',
        'access' => 'update'
    ])->name('abstract-update');
    Route::post('procurement/abstract/update-items/{id}', [
        'uses' => 'AbstractQuotationController@updateItems',
        'module' => 'proc_abstract',
        'access' => 'update'
    ])->name('abstract-update-items');
    Route::post('procurement/abstract/delete-items/{id}', [
        'uses' => 'AbstractQuotationController@deleteItems',
        'module' => 'proc_abstract',
        'access' => 'delete'
    ])->name('abstract-delete-items');
    Route::post('procurement/abstract/approve/{id}', [
        'uses' => 'AbstractQuotationController@approveForPO',
        'module' => 'proc_abstract',
        'access' => 'approve_po_jo'
    ])->name('abstract-approve');

    // Purchase and Job Order Module
    Route::any('procurement/po-jo', [
        'uses' => 'PurchaseJobOrderController@index',
        'module' => 'proc_po_jo',
        'access' => 'is_allowed'
    ])->name('po-jo');
    Route::get('procurement/po-jo/show-create/{prID}', [
        'uses' => 'PurchaseJobOrderController@showCreate',
        'module' => 'proc_po_jo',
        'access' => 'create'
    ])->name('po-jo-show-create');
    Route::post('procurement/po-jo/store/{prID}', [
        'uses' => 'PurchaseJobOrderController@store',
        'module' => 'proc_po_jo',
        'access' => 'create'
    ])->name('po-jo-store');
    Route::get('procurement/po-jo/show-edit/{id}', [
        'uses' => 'PurchaseJobOrderController@showEdit',
        'module' => 'proc_po_jo',
        'access' => 'update'
    ])->name('po-jo-show-edit');
    Route::post('procurement/po-jo/update/{id}', [
        'uses' => 'PurchaseJobOrderController@update',
        'module' => 'proc_po_jo',
        'access' => 'update'
    ])->name('po-jo-update');
    Route::post('procurement/po-jo/delete/{id}', [
        'uses' => 'PurchaseJobOrderController@delete',
        'module' => 'proc_po_jo',
        'access' => 'delete'
    ])->name('po-jo-delete');
    Route::post('procurement/po-jo/destroy/{id}', [
        'uses' => 'PurchaseJobOrderController@destroy',
        'module' => 'proc_po_jo',
        'access' => 'destroy'
    ])->name('po-jo-destroy');
    Route::post('procurement/po-jo/accountant-signed/{id}', [
        'uses' => 'PurchaseJobOrderController@accountantSigned',
        'module' => 'proc_po_jo',
        'access' => 'signed'
    ])->name('po-jo-accountant-signed');
    Route::post('procurement/po-jo/approve/{id}', [
        'uses' => 'PurchaseJobOrderController@approve',
        'module' => 'proc_po_jo',
        'access' => 'approve'
    ])->name('po-jo-approve');
    Route::get('procurement/po-jo/show-issue/{id}', [
        'uses' => 'PurchaseJobOrderController@showIssue',
        'module' => 'proc_po_jo',
        'access' => 'issue'
    ])->name('po-jo-show-issue');
    Route::post('procurement/po-jo/issue/{id}', [
        'uses' => 'PurchaseJobOrderController@issue',
        'module' => 'proc_po_jo',
        'access' => 'issue'
    ])->name('po-jo-issue');
    Route::get('procurement/po-jo/show-receive/{id}', [
        'uses' => 'PurchaseJobOrderController@showReceive',
        'module' => 'proc_po_jo',
        'access' => 'receive'
    ])->name('po-jo-show-receive');
    Route::post('procurement/po-jo/receive/{id}', [
        'uses' => 'PurchaseJobOrderController@receive',
        'module' => 'proc_po_jo',
        'access' => 'receive'
    ])->name('po-jo-receive');
    Route::post('procurement/po-jo/cancel/{id}', [
        'uses' => 'PurchaseJobOrderController@cancel',
        'module' => 'proc_po_jo',
        'access' => 'cancel'
    ])->name('po-jo-cancel');
    Route::post('procurement/po-jo/uncancel/{id}', [
        'uses' => 'PurchaseJobOrderController@uncancel',
        'module' => 'proc_po_jo',
        'access' => 'uncancel'
    ])->name('po-jo-uncancel');
    Route::post('procurement/po-jo/restore/{id}', [
        'uses' => 'PurchaseJobOrderController@restore',
        'module' => 'proc_po_jo',
        'access' => 'uncancel'
    ])->name('po-jo-restore');
    Route::post('procurement/po-jo/delivery/{id}', [
        'uses' => 'PurchaseJobOrderController@delivery',
        'module' => 'proc_po_jo',
        'access' => 'delivery'
    ])->name('po-jo-delivery');
    Route::post('procurement/po-jo/inspection/{id}', [
        'uses' => 'PurchaseJobOrderController@inspection',
        'module' => 'proc_po_jo',
        'access' => 'inspection'
    ])->name('po-jo-inspection');

    // Obligation and Request Status/BURS Module
    Route::any('procurement/ors-burs', [
        'uses' => 'ObligationRequestStatusController@indexProc',
        'module' => 'proc_ors_burs',
        'access' => 'is_allowed'
    ])->name('proc-ors-burs');
    Route::post('procurement/ors-burs/create-ors-burs/{poID}', [
        'uses' => 'ObligationRequestStatusController@storeORSFromPO',
        'module' => 'proc_ors_burs',
        'access' => 'create'
    ])->name('po-jo-create-ors-burs');
    Route::get('procurement/ors-burs/show-edit/{id}', [
        'uses' => 'ObligationRequestStatusController@showEdit',
        'module' => 'proc_ors_burs',
        'access' => 'update'
    ])->name('proc-ors-burs-show-edit');
    Route::post('procurement/ors-burs/update/{id}', [
        'uses' => 'ObligationRequestStatusController@update',
        'module' => 'proc_ors_burs',
        'access' => 'update'
    ])->name('proc-ors-burs-update');
    Route::get('procurement/ors-burs/show-issue/{id}', [
        'uses' => 'ObligationRequestStatusController@showIssue',
        'module' => 'proc_ors_burs',
        'access' => 'issue'
    ])->name('proc-ors-burs-show-issue');
    Route::post('procurement/ors-burs/issue/{id}', [
        'uses' => 'ObligationRequestStatusController@issue',
        'module' => 'proc_ors_burs',
        'access' => 'issue'
    ])->name('proc-ors-burs-issue');
    Route::get('procurement/ors-burs/show-receive/{id}', [
        'uses' => 'ObligationRequestStatusController@showReceive',
        'module' => 'proc_ors_burs',
        'access' => 'receive'
    ])->name('proc-ors-burs-show-receive');
    Route::post('procurement/ors-burs/receive/{id}', [
        'uses' => 'ObligationRequestStatusController@receive',
        'module' => 'proc_ors_burs',
        'access' => 'receive'
    ])->name('proc-ors-burs-receive');
    Route::get('procurement/ors-burs/show-issue-back/{id}', [
        'uses' => 'ObligationRequestStatusController@showIssueback',
        'module' => 'proc_ors_burs',
        'access' => 'issue_back'
    ])->name('proc-ors-burs-show-issue-back');
    Route::post('procurement/ors-burs/issue-back/{id}', [
        'uses' => 'ObligationRequestStatusController@issueBack',
        'module' => 'proc_ors_burs',
        'access' => 'issue_back'
    ])->name('proc-ors-burs-issue-back');
    Route::get('procurement/ors-burs/show-receive-back/{id}', [
        'uses' => 'ObligationRequestStatusController@showReceiveBack',
        'module' => 'proc_ors_burs',
        'access' => 'receive_back'
    ])->name('proc-ors-burs-show-receive-back');
    Route::post('procurement/ors-burs/receive-back/{id}', [
        'uses' => 'ObligationRequestStatusController@receiveBack',
        'module' => 'proc_ors_burs',
        'access' => 'receive_back'
    ])->name('proc-ors-burs-receive-back');
    Route::get('procurement/ors-burs/show-obligate/{id}', [
        'uses' => 'ObligationRequestStatusController@showObligate',
        'module' => 'proc_ors_burs',
        'access' => 'obligate'
    ])->name('proc-ors-burs-show-obligate');
    Route::post('procurement/ors-burs/obligate/{id}', [
        'uses' => 'ObligationRequestStatusController@obligate',
        'module' => 'proc_ors_burs',
        'access' => 'obligate'
    ])->name('proc-ors-burs-obligate');
    Route::get('procurement/ors-burs/show-remarks/{id}', [
        'uses' => 'ObligationRequestStatusController@showLogRemarks',
        'module' => 'proc_ors_burs',
        'access' => 'is_allowed'
    ])->name('proc-ors-burs-show-remarks');
    Route::post('procurement/ors-burs/create-remarks/{id}', [
        'uses' => 'ObligationRequestStatusController@logRemarks',
        'module' => 'proc_ors_burs',
        'access' => 'is_allowed'
    ])->name('proc-ors-burs-store-remarks');
    Route::get('procurement/ors-burs/show-uacs-items/{id}', [
        'uses' => 'ObligationRequestStatusController@showUacsItems',
        'module' => 'proc_ors_burs',
        'access' => 'is_allowed'
    ])->name('proc-ors-burs-show-uacs-items');
    Route::post('procurement/ors-burs/update-uacs-items/{id}', [
        'uses' => 'ObligationRequestStatusController@updateUacsItems',
        'module' => 'proc_ors_burs',
        'access' => 'is_allowed'
    ])->name('proc-ors-burs-update-uacs-items');

    // Inpection and Acceptance Report Module
    Route::any('procurement/iar', [
        'uses' => 'InspectionAcceptanceController@index',
        'module' => 'proc_iar',
        'access' => 'is_allowed'
    ])->name('iar');
    Route::get('procurement/iar/show-edit/{id}', [
        'uses' => 'InspectionAcceptanceController@showEdit',
        'module' => 'proc_iar',
        'access' => 'update'
    ])->name('iar-show-edit');
    Route::post('procurement/iar/update/{id}', [
        'uses' => 'InspectionAcceptanceController@update',
        'module' => 'proc_iar',
        'access' => 'update'
    ])->name('iar-update');
    Route::get('procurement/iar/show-issue/{id}', [
        'uses' => 'InspectionAcceptanceController@showIssue',
        'module' => 'proc_iar',
        'access' => 'issue'
    ])->name('iar-show-issue');
    Route::post('procurement/iar/issue/{id}', [
        'uses' => 'InspectionAcceptanceController@issue',
        'module' => 'proc_iar',
        'access' => 'issue'
    ])->name('iar-issue');
    Route::get('procurement/iar/show-inspect/{id}', [
        'uses' => 'InspectionAcceptanceController@showInspect',
        'module' => 'proc_iar',
        'access' => 'inspect'
    ])->name('iar-show-inspect');
    Route::post('procurement/iar/inspect/{id}', [
        'uses' => 'InspectionAcceptanceController@inspect',
        'module' => 'proc_iar',
        'access' => 'inspect'
    ])->name('iar-inspect');

    // Disbursement Voucher Module
    Route::any('procurement/dv', [
        'uses' => 'DisbursementVoucherController@indexProc',
        'module' => 'proc_dv',
        'access' => 'is_allowed'
    ])->name('proc-dv');
    Route::get('procurement/dv/show-edit/{id}', [
        'uses' => 'DisbursementVoucherController@showEdit',
        'module' => 'proc_dv',
        'access' => 'update'
    ])->name('proc-dv-show-edit');
    Route::post('procurement/dv/update/{id}', [
        'uses' => 'DisbursementVoucherController@update',
        'module' => 'proc_dv',
        'access' => 'update'
    ])->name('proc-dv-update');
    Route::get('procurement/dv/show-issue/{id}', [
        'uses' => 'DisbursementVoucherController@showIssue',
        'module' => 'proc_dv',
        'access' => 'issue'
    ])->name('proc-dv-show-issue');
    Route::post('procurement/dv/issue/{id}', [
        'uses' => 'DisbursementVoucherController@issue',
        'module' => 'proc_dv',
        'access' => 'issue'
    ])->name('proc-dv-issue');
    Route::get('procurement/dv/show-receive/{id}', [
        'uses' => 'DisbursementVoucherController@showReceive',
        'module' => 'proc_dv',
        'access' => 'receive'
    ])->name('proc-dv-show-receive');
    Route::post('procurement/dv/receive/{id}', [
        'uses' => 'DisbursementVoucherController@receive',
        'module' => 'proc_dv',
        'access' => 'receive'
    ])->name('proc-dv-receive');
    Route::get('procurement/dv/show-issue-back/{id}', [
        'uses' => 'DisbursementVoucherController@showIssueback',
        'module' => 'proc_dv',
        'access' => 'issue_back'
    ])->name('proc-dv-show-issue-back');
    Route::post('procurement/dv/issue-back/{id}', [
        'uses' => 'DisbursementVoucherController@issueBack',
        'module' => 'proc_dv',
        'access' => 'issue_back'
    ])->name('proc-dv-issue-back');
    Route::get('procurement/dv/show-receive-back/{id}', [
        'uses' => 'DisbursementVoucherController@showReceiveBack',
        'module' => 'proc_dv',
        'access' => 'receive_back'
    ])->name('proc-dv-show-receive-back');
    Route::post('procurement/dv/receive-back/{id}', [
        'uses' => 'DisbursementVoucherController@receiveBack',
        'module' => 'proc_dv',
        'access' => 'receive_back'
    ])->name('proc-dv-receive-back');
    Route::get('procurement/dv/show-payment/{id}', [
        'uses' => 'DisbursementVoucherController@showPayment',
        'module' => 'proc_dv',
        'access' => 'payment'
    ])->name('proc-dv-show-payment');
    Route::post('procurement/dv/payment/{id}', [
        'uses' => 'DisbursementVoucherController@payment',
        'module' => 'proc_dv',
        'access' => 'payment'
    ])->name('proc-dv-payment');
    Route::get('procurement/dv/show-disburse/{id}', [
        'uses' => 'DisbursementVoucherController@showDisburse',
        'module' => 'proc_dv',
        'access' => 'disburse'
    ])->name('proc-dv-show-disburse');
    Route::post('procurement/dv/disburse/{id}', [
        'uses' => 'DisbursementVoucherController@disburse',
        'module' => 'proc_dv',
        'access' => 'disburse'
    ])->name('proc-dv-disburse');
    Route::get('procurement/dv/show-remarks/{id}', [
        'uses' => 'DisbursementVoucherController@showLogRemarks',
        'module' => 'proc_dv',
        'access' => 'is_allowed'
    ])->name('proc-dv-show-remarks');
    Route::post('procurement/dv/create-remarks/{id}', [
        'uses' => 'DisbursementVoucherController@logRemarks',
        'module' => 'proc_dv',
        'access' => 'is_allowed'
    ])->name('proc-dv-store-remarks');
    Route::get('procurement/dv/show-uacs-items/{id}', [
        'uses' => 'DisbursementVoucherController@showUacsItems',
        'module' => 'proc_dv',
        'access' => 'is_allowed'
    ])->name('proc-dv-show-uacs-items');
    Route::post('procurement/dv/update-uacs-items/{id}', [
        'uses' => 'DisbursementVoucherController@updateUacsItems',
        'module' => 'proc_dv',
        'access' => 'is_allowed'
    ])->name('proc-dv-update-uacs-items');

    /*===================== INVENTORY ROUTES =====================*/

    // All Items Procured
    Route::any('inventory/stocks', [
        'uses' => 'InventoryStockController@index',
        'module' => 'inv_stocks',
        'access' => 'is_allowed'
    ])->name('stocks');
    Route::get('inventory/stocks/show-create-from-iar/{poID}', [
        'uses' => 'InventoryStockController@showCreateFromIAR',
        'module' => 'inv_stocks',
        'access' => 'create'
    ])->name('stocks-show-create-iar');
    Route::get('inventory/stocks/show-create/{classificationID}/{classification}', [
        'uses' => 'InventoryStockController@showCreate',
        'module' => 'inv_stocks',
        'access' => 'create'
    ])->name('stocks-show-create');
    Route::post('inventory/stocks/store/{classificationID}/{classification}', [
        'uses' => 'InventoryStockController@store',
        'module' => 'inv_stocks',
        'access' => 'create'
    ])->name('stocks-store');
    Route::post('inventory/stocks/store-iar/{poID}', [
        'uses' => 'InventoryStockController@storeFromIAR',
        'module' => 'inv_stocks',
        'access' => 'create'
    ])->name('stocks-store-iar');
    Route::get('inventory/stocks/show-edit-from-iar/{poID}', [
        'uses' => 'InventoryStockController@showEditFromIAR',
        'module' => 'inv_stocks',
        'access' => 'update'
    ])->name('stocks-show-edit-iar');
    Route::get('inventory/stocks/show-edit/{id}/{classification}', [
        'uses' => 'InventoryStockController@showEdit',
        'module' => 'inv_stocks',
        'access' => 'update'
    ])->name('stocks-show-edit');
    Route::post('inventory/stocks/update/{id}', [
        'uses' => 'InventoryStockController@update',
        'module' => 'inv_stocks',
        'access' => 'update'
    ])->name('stocks-update');
    Route::post('inventory/stocks/update-iar/{poID}', [
        'uses' => 'InventoryStockController@updateFromIAR',
        'module' => 'inv_stocks',
        'access' => 'update'
    ])->name('stocks-update-iar');
    Route::get('inventory/stocks/show-create-issue-item/{invStockID}/{invStockItemID}/{classification}/{type}', [
        'uses' => 'InventoryStockController@showCreateIssueItem',
        'module' => 'inv_stocks',
        'access' => 'issue'
    ])->name('stocks-show-create-issue-item');
    Route::post('inventory/stocks/store-issue-item/{invStockID}/{classification}', [
        'uses' => 'InventoryStockController@storeIssueItem',
        'module' => 'inv_stocks',
        'access' => 'issue'
    ])->name('stocks-store-issue-item');
    Route::get('inventory/stocks/show-update-issue-item/{invStockIssueID}/{classification}', [
        'uses' => 'InventoryStockController@showUpdateIssueItem',
        'module' => 'inv_stocks',
        'access' => 'update'
    ])->name('stocks-show-update-issue-item');
    Route::post('inventory/stocks/update-issue-item/{invStockID}/{classification}', [
        'uses' => 'InventoryStockController@updateIssueItem',
        'module' => 'inv_stocks',
        'access' => 'update'
    ])->name('stocks-update-issue-item');
    Route::get('inventory/stocks/show-recipients/{id}', [
        'uses' => 'InventoryStockController@showRecipients',
        'module' => 'inv_stocks',
        'access' => 'is_allowed'
    ])->name('stocks-show-recipients');
    Route::post('inventory/stocks/delete-issue/{invStockIssueID}', [
        'uses' => 'InventoryStockController@deleteIssue',
        'module' => 'inv_stocks',
        'access' => 'delete'
    ])->name('stocks-delete-issue');
    Route::post('inventory/stocks/destroy-issue/{invStockIssueID}', [
        'uses' => 'InventoryStockController@destroyIssue',
        'module' => 'inv_stocks',
        'access' => 'destroy'
    ])->name('stocks-destroy-issue');
    Route::post('inventory/stocks/issue/{id}', [
        'uses' => 'InventoryStockController@issue',
        'module' => 'inv_stocks',
        'access' => 'issue'
    ])->name('stocks-issue');
    Route::post('inventory/stocks/delete/{id}', [
        'uses' => 'InventoryStockController@delete',
        'module' => 'inv_stocks',
        'access' => 'delete'
    ])->name('stocks-delete');

    // Per Person
    Route::get('inventory/summary-per-person', [
        'uses' => 'InventoryStockController@indexPerPerson',
        'module' => 'inv_stocks',
        'access' => 'is_allowed'
    ])->name('inv-summary-per-person');
    Route::any('inventory/summary-per-person/{empID}', [
        'uses' => 'InventoryStockController@indexListStocks',
        'module' => 'inv_stocks',
        'access' => 'is_allowed'
    ])->name('inv-summary-per-person-view');

    /*===================== PAYMENT ROUTES =====================*/

    // List of Due and Demandable Accounts Payable Module
    Route::any('payment/lddap', [
        'uses' => 'LDDAPController@index',
        'module' => 'pay_lddap',
        'access' => 'is_allowed'
    ])->name('lddap');
    Route::get('payment/lddap/show-create', [
        'uses' => 'LDDAPController@showCreate',
        'module' => 'pay_lddap',
        'access' => 'create'
    ])->name('lddap-show-create');
    Route::get('payment/lddap/show-edit/{id}', [
        'uses' => 'LDDAPController@showEdit',
        'module' => 'pay_lddap',
        'access' => 'update'
    ])->name('lddap-show-edit');
    Route::post('payment/lddap/store', [
        'uses' => 'LDDAPController@store',
        'module' => 'pay_lddap',
        'access' => 'create'
    ])->name('lddap-store');
    Route::post('payment/lddap/update/{id}', [
        'uses' => 'LDDAPController@update',
        'module' => 'pay_lddap',
        'access' => 'update'
    ])->name('lddap-update');
    Route::post('payment/lddap/delete/{id}', [
        'uses' => 'LDDAPController@delete',
        'module' => 'pay_lddap',
        'access' => 'delete'
    ])->name('lddap-delete');
    Route::post('payment/lddap/for-approval/{id}', [
        'uses' => 'LDDAPController@forApproval',
        'module' => 'pay_lddap',
        'access' => 'approval'
    ])->name('lddap-for-approval');
    Route::post('payment/lddap/approve/{id}', [
        'uses' => 'LDDAPController@approve',
        'module' => 'pay_lddap',
        'access' => 'approve'
    ])->name('lddap-approve');
    Route::post('payment/lddap/summary/{id}', [
        'uses' => 'LDDAPController@summary',
        'module' => 'pay_lddap',
        'access' => 'summary'
    ])->name('lddap-summary');
    Route::post('payment/lddap/get-mds-gsb', [
        'uses' => 'LDDAPController@getListMDSGSB',
        'module' => 'pay_lddap',
        'access' => 'is_allowed'
    ]);
    Route::post('payment/lddap/get-ors-burs', [
        'uses' => 'LDDAPController@getListORSBURS',
        'module' => 'pay_lddap',
        'access' => 'is_allowed'
    ]);
    Route::post('payment/lddap/get-mooe-title', [
        'uses' => 'LDDAPController@getListTitleMOOE',
        'module' => 'pay_lddap',
        'access' => 'is_allowed'
    ]);
    Route::post('payment/lddap/get-ors-burs-details', [
        'uses' => 'LDDAPController@getOrsBursDetails',
        'module' => 'pay_lddap',
        'access' => 'is_allowed'
    ]);

    // Summary of LDDAP Module
    Route::any('payment/summary', [
        'uses' => 'SummaryLDDAPController@index',
        'module' => 'pay_summary',
        'access' => 'is_allowed'
    ])->name('summary');
    Route::get('payment/summary/show-create', [
        'uses' => 'SummaryLDDAPController@showCreate',
        'module' => 'pay_summary',
        'access' => 'create'
    ])->name('summary-show-create');
    Route::get('payment/summary/show-edit/{id}', [
        'uses' => 'SummaryLDDAPController@showEdit',
        'module' => 'pay_summary',
        'access' => 'update'
    ])->name('summary-show-edit');
    Route::post('payment/summary/store', [
        'uses' => 'SummaryLDDAPController@store',
        'module' => 'pay_summary',
        'access' => 'create'
    ])->name('summary-store');
    Route::post('payment/summary/update/{id}', [
        'uses' => 'SummaryLDDAPController@update',
        'module' => 'pay_summary',
        'access' => 'update'
    ])->name('summary-update');
    Route::post('payment/summary/delete/{id}', [
        'uses' => 'SummaryLDDAPController@delete',
        'module' => 'pay_summary',
        'access' => 'delete'
    ])->name('summary-delete');
    Route::post('payment/summary/for-approval/{id}', [
        'uses' => 'SummaryLDDAPController@forApproval',
        'module' => 'pay_summary',
        'access' => 'approval'
    ])->name('summary-for-approval');
    Route::post('payment/summary/approve/{id}', [
        'uses' => 'SummaryLDDAPController@approve',
        'module' => 'pay_summary',
        'access' => 'approve'
    ])->name('summary-approve');
    Route::post('payment/summary/submission/{id}', [
        'uses' => 'SummaryLDDAPController@submissionBank',
        'module' => 'pay_summary',
        'access' => 'submission'
    ])->name('summary-submission');
    Route::post('payment/summary/get-lddap', [
        'uses' => 'SummaryLDDAPController@getListLDDAP',
        'module' => 'pay_summary',
        'access' => 'is_allowed'
    ]);

    /*===================== FUND UTILIZATION ROUTES =====================*/

    // Fund Utilization Module
    Route::any('fund-utilization/project-lib', [
        'uses' => 'LineItemBudgetController@index',
        'module' => 'fund_lib',
        'access' => 'is_allowed'
    ])->name('fund-project-lib');
    Route::get('fund-utilization/project-lib/show-print/{id}', [
        'uses' => 'LineItemBudgetController@showPrint',
        'module' => 'fund_lib',
        'access' => 'is_allowed'
    ])->name('fund-project-lib-show-print');
    Route::get('fund-utilization/project-lib/show-create', [
        'uses' => 'LineItemBudgetController@showCreate',
        'module' => 'fund_lib',
        'access' => 'create'
    ])->name('fund-project-lib-show-create');
    Route::get('fund-utilization/project-lib/show-create-realignment/{id}/{type}', [
        'uses' => 'LineItemBudgetController@showCreateEditRealignment',
        'module' => 'fund_librealign',
        'access' => 'create'
    ])->name('fund-project-lib-show-create-realignment');
    Route::post('fund-utilization/project-lib/store-realignment/{id}', [
        'uses' => 'LineItemBudgetController@storeRealignment',
        'module' => 'fund_librealign',
        'access' => 'create'
    ])->name('fund-project-lib-store-realignment');
    Route::post('fund-utilization/project-lib/store', [
        'uses' => 'LineItemBudgetController@store',
        'module' => 'fund_lib',
        'access' => 'create'
    ])->name('fund-project-lib-store');
    Route::get('fund-utilization/project-lib/show-edit/{id}', [
        'uses' => 'LineItemBudgetController@showEdit',
        'module' => 'fund_lib',
        'access' => 'update'
    ])->name('fund-project-lib-show-edit');
    Route::get('fund-utilization/project-lib/show-edit-realignment/{id}/{type}', [
        'uses' => 'LineItemBudgetController@showCreateEditRealignment',
        'module' => 'fund_librealign',
        'access' => 'update'
    ])->name('fund-project-lib-show-edit-realignment');
    Route::post('fund-utilization/project-lib/update/{id}', [
        'uses' => 'LineItemBudgetController@update',
        'module' => 'fund_lib',
        'access' => 'update'
    ])->name('fund-project-lib-update');
    Route::post('fund-utilization/project-lib/update-realignment/{id}', [
        'uses' => 'LineItemBudgetController@updateRealignment',
        'module' => 'fund_librealign',
        'access' => 'create'
    ])->name('fund-project-lib-update-realignment');
    Route::post('fund-utilization/project-lib/delete/{id}', [
        'uses' => 'LineItemBudgetController@delete',
        'module' => 'fund_lib',
        'access' => 'delete'
    ])->name('fund-project-lib-delete');
    Route::post('fund-utilization/project-lib/destroy-realignment/{id}', [
        'uses' => 'LineItemBudgetController@destroyRealignment',
        'module' => 'fund_librealign',
        'access' => 'delete'
    ])->name('fund-project-lib-destroy-realignment');
    Route::post('fund-utilization/project-lib/get-allot-class', [
        'uses' => 'LineItemBudgetController@getListAllotmentClass',
        'module' => 'fund_lib',
        'access' => 'is_allowed'
    ]);
    Route::post('fund-utilization/project-lib/get-account-title', [
        'uses' => 'LineItemBudgetController@getListAccountTitle',
        'module' => 'fund_lib',
        'access' => 'is_allowed'
    ]);
    Route::post('fund-utilization/project-lib/get-uacs-object', [
        'uses' => 'RegAllotmentController@getUacsObject',
        'module' => 'fund_lib',
        'access' => 'is_allowed'
    ]);
    Route::post('fund-utilization/project-lib/approve/{id}/{isRealignment}', [
        'uses' => 'LineItemBudgetController@approve',
        'module' => 'fund_lib',
        'access' => 'is_allowed'
    ])->name('fund-project-lib-approve');
    Route::post('fund-utilization/project-lib/disapprove/{id}/{isRealignment}', [
        'uses' => 'LineItemBudgetController@disapprove',
        'module' => 'fund_lib',
        'access' => 'is_allowed'
    ])->name('fund-project-lib-disapprove');

    /*===================== REPORTS ROUTES =====================*/

    // Obligation Ledger Module
    Route::any('report/ledger/obligation', [
        'uses' => 'LedgerController@indexObligation',
        'module' => 'report_orsledger',
        'access' => 'is_allowed'
    ])->name('report-obligation-ledger');
    Route::get('report/ledger/obligation/show/{id}/{for}/{type}', [
        'uses' => 'LedgerController@showLedger',
        'module' => 'report_orsledger',
        'access' => 'is_allowed'
    ])->name('report-obligation-ledger-show');
    Route::get('report/ledger/obligation/show-create/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@showCreate',
        'module' => 'report_orsledger',
        'access' => 'create'
    ])->name('report-obligation-ledger-show-create');
    Route::post('report/ledger/obligation/store/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@store',
        'module' => 'report_orsledger',
        'access' => 'create'
    ])->name('report-obligation-ledger-store');
    Route::post('report/ledger/obligation/store-items/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@storeItems',
        'module' => 'report_orsledger',
        'access' => 'create'
    ])->name('report-obligation-ledger-store-items');
    Route::get('report/ledger/obligation/show-edit/{id}/{for}/{type}', [
        'uses' => 'LedgerController@showEdit',
        'module' => 'report_orsledger',
        'access' => 'update'
    ])->name('report-obligation-ledger-show-edit');
    Route::post('report/ledger/obligation/update/{id}/{for}/{type}', [
        'uses' => 'LedgerController@update',
        'module' => 'report_orsledger',
        'access' => 'update'
    ])->name('report-obligation-ledger-update');
    Route::post('report/ledger/obligation/update-items/{id}/{for}/{type}', [
        'uses' => 'LedgerController@updateItems',
        'module' => 'report_orsledger',
        'access' => 'update'
    ])->name('report-obligation-ledger-update-items');
    Route::post('report/ledger/obligation/delete/{id}/{for}', [
        'uses' => 'LedgerController@indexObligation',
        'module' => 'report_orsledger',
        'access' => 'delete'
    ])->name('report-obligation-ledger-delete');
    Route::post('report/ledger/obligation/get-payee', [
        'uses' => 'LedgerController@getPayees',
        'module' => 'report_orsledger',
        'access' => 'is_allowed'
    ]);
    Route::post('report/ledger/obligation/get-unit', [
        'uses' => 'LedgerController@getUnits',
        'module' => 'report_orsledger',
        'access' => 'is_allowed'
    ]);
    Route::post('report/ledger/obligation/get-mooe-title', [
        'uses' => 'LedgerController@getMooeTitles',
        'module' => 'report_orsledger',
        'access' => 'is_allowed'
    ]);
    Route::post('report/ledger/obligation/import', [
        'uses' => 'LedgerController@import',
        'module' => 'report_orsledger',
        'access' => 'is_allowed'
    ])->name('report-obligation-ledger-import');

    // Disbursement Ledger Module
    Route::any('report/ledger/disbursement', [
        'uses' => 'LedgerController@indexDisbursement',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ])->name('report-disbursement-ledger');
    Route::get('report/ledger/disbursement/show/{id}/{for}/{type}', [
        'uses' => 'LedgerController@showLedger',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ])->name('report-disbursement-ledger-show');
    Route::get('report/ledger/disbursement/show-create/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@showCreate',
        'module' => 'report_dvledger',
        'access' => 'create'
    ])->name('report-disbursement-ledger-show-create');
    Route::post('report/ledger/disbursement/store/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@store',
        'module' => 'report_dvledger',
        'access' => 'create'
    ])->name('report-disbursement-ledger-store');
    Route::post('report/ledger/disbursement/store-items/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@storeItems',
        'module' => 'report_dvledger',
        'access' => 'create'
    ])->name('report-obligation-ledger-store-items');
    Route::get('report/ledger/disbursement/show-edit/{id}/{for}/{type}', [
        'uses' => 'LedgerController@showEdit',
        'module' => 'report_dvledger',
        'access' => 'update'
    ])->name('report-disbursement-ledger-show-edit');
    Route::post('report/ledger/disbursement/update/{id}/{for}/{type}', [
        'uses' => 'LedgerController@update',
        'module' => 'report_dvledger',
        'access' => 'update'
    ])->name('report-disbursement-ledger-update');
    Route::post('report/ledger/disbursement/update-items/{id}/{for}/{type}', [
        'uses' => 'LedgerController@updateItems',
        'module' => 'report_dvledger',
        'access' => 'update'
    ])->name('report-disbursement-ledger-update-items');
    Route::post('report/ledger/disbursement/delete/{id}/{for}', [
        'uses' => 'LedgerController@delete',
        'module' => 'report_dvledger',
        'access' => 'delete'
    ])->name('report-disbursement-ledger-delete');
    Route::post('report/ledger/disbursement/get-payee', [
        'uses' => 'LedgerController@getPayees',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ]);
    Route::post('report/ledger/disbursement/get-unit', [
        'uses' => 'LedgerController@getUnits',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ]);
    Route::post('report/ledger/disbursement/get-mooe-title', [
        'uses' => 'LedgerController@getMooeTitles',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ]);

    // Registry of Allotments, Obligations and Disbursements Module
    Route::any('report/registry-allot-obli-disb', [
        'uses' => 'RegAllotmentController@index',
        'module' => 'report_raod',
        'access' => 'is_allowed'
    ])->name('report-raod');
    Route::get('report/registry-allot-obli-disb/show', [
        'uses' => 'RegAllotmentController@show',
        'module' => 'report_raod',
        'access' => 'create'
    ])->name('report-raod-show');
    Route::get('report/registry-allot-obli-disb/show-create', [
        'uses' => 'RegAllotmentController@showCreate',
        'module' => 'report_raod',
        'access' => 'create'
    ])->name('report-raod-show-create');
    Route::post('report/registry-allot-obli-disb/store', [
        'uses' => 'RegAllotmentController@store',
        'module' => 'report_raod',
        'access' => 'create'
    ])->name('report-raod-store');
    Route::post('report/registry-allot-obli-disb/store-items/{regID}', [
        'uses' => 'RegAllotmentController@storeItems',
        'module' => 'report_raod',
        'access' => 'create'
    ])->name('report-raod-store-items');
    Route::get('report/registry-allot-obli-disb/show-edit/{id}', [
        'uses' => 'RegAllotmentController@showEdit',
        'module' => 'report_raod',
        'access' => 'update'
    ])->name('report-raod-show-edit');
    Route::post('report/registry-allot-obli-disb/update/{id}', [
        'uses' => 'RegAllotmentController@update',
        'module' => 'report_raod',
        'access' => 'update'
    ])->name('report-raod-update');
    Route::post('report/registry-allot-obli-disb/update-items/{regID}', [
        'uses' => 'RegAllotmentController@updateItems',
        'module' => 'report_raod',
        'access' => 'create'
    ])->name('report-raod-update-items');
    Route::post('report/registry-allot-obli-disb/delete/{id}', [
        'uses' => 'RegAllotmentController@delete',
        'module' => 'report_raod',
        'access' => 'delete'
    ])->name('report-raod-delete');
    Route::post('report/registry-allot-obli-disb/get-vouchers', [
        'uses' => 'RegAllotmentController@getVouchers',
        'module' => 'report_raod',
        'access' => 'is_allowed'
    ]);
    Route::post('report/registry-allot-obli-disb/get-payee', [
        'uses' => 'CollectionController@getCustomPayees',
        'module' => 'report_raod',
        'access' => 'is_allowed'
    ]);
    Route::post('report/registry-allot-obli-disb/get-uacs-object', [
        'uses' => 'RegAllotmentController@getUacsObject',
        'module' => 'report_raod',
        'access' => 'is_allowed'
    ]);
    /*
    Route::get('report/ledger/disbursement/show/{id}/{for}/{type}', [
        'uses' => 'LedgerController@showLedger',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ])->name('report-disbursement-ledger-show');
    Route::get('report/ledger/disbursement/show-create/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@showCreate',
        'module' => 'report_dvledger',
        'access' => 'create'
    ])->name('report-disbursement-ledger-show-create');
    Route::post('report/ledger/disbursement/store/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@store',
        'module' => 'report_dvledger',
        'access' => 'create'
    ])->name('report-disbursement-ledger-store');
    Route::post('report/ledger/disbursement/store-items/{project_id}/{for}/{type}', [
        'uses' => 'LedgerController@storeItems',
        'module' => 'report_dvledger',
        'access' => 'create'
    ])->name('report-obligation-ledger-store-items');
    Route::get('report/ledger/disbursement/show-edit/{id}/{for}/{type}', [
        'uses' => 'LedgerController@showEdit',
        'module' => 'report_dvledger',
        'access' => 'update'
    ])->name('report-disbursement-ledger-show-edit');
    Route::post('report/ledger/disbursement/update/{id}/{for}/{type}', [
        'uses' => 'LedgerController@update',
        'module' => 'report_dvledger',
        'access' => 'update'
    ])->name('report-disbursement-ledger-update');
    Route::post('report/ledger/disbursement/update-items/{id}/{for}/{type}', [
        'uses' => 'LedgerController@updateItems',
        'module' => 'report_dvledger',
        'access' => 'update'
    ])->name('report-disbursement-ledger-update-items');
    Route::post('report/ledger/disbursement/delete/{id}/{for}', [
        'uses' => 'LedgerController@delete',
        'module' => 'report_dvledger',
        'access' => 'delete'
    ])->name('report-disbursement-ledger-delete');
    Route::post('report/ledger/disbursement/get-payee', [
        'uses' => 'LedgerController@getPayees',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ]);
    Route::post('report/ledger/disbursement/get-unit', [
        'uses' => 'LedgerController@getUnits',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ]);
    Route::post('report/ledger/disbursement/get-mooe-title', [
        'uses' => 'LedgerController@getMooeTitles',
        'module' => 'report_dvledger',
        'access' => 'is_allowed'
    ]);*/

    // Line-Item Budget Module
    Route::any('reports/project-lib', [
        'uses' => 'LineItemBudgetController@indexReport',
        'module' => 'report_lib',
        'access' => 'is_allowed'
    ])->name('report-project-lib');


    /*===================== SYSTEM LIBRARIES ROUTES =====================*/

    // Agencies amd LGUs Module
    Route::any('libraries/agency-lgu', [
        'uses' => 'LibraryController@indexAgencyLGU',
        'module' => 'lib_agency_lgu',
        'access' => 'is_allowed'
    ])->name('agency-lgu');
    Route::get('libraries/agency-lgu/show-create', [
        'uses' => 'LibraryController@showCreateAgencyLGU',
        'module' => 'lib_agency_lgu',
        'access' => 'create'
    ])->name('agency-lgu-show-create');
    Route::post('libraries/agency-lgu/store', [
        'uses' => 'LibraryController@storeAgencyLGU',
        'module' => 'lib_agency_lgu',
        'access' => 'create'
    ])->name('agency-lgu-store');
    Route::get('libraries/agency-lgu/show-edit/{id}', [
        'uses' => 'LibraryController@showEditAgencyLGU',
        'module' => 'lib_agency_lgu',
        'access' => 'update'
    ])->name('agency-lgu-show-edit');
    Route::post('libraries/agency-lgu/update/{id}', [
        'uses' => 'LibraryController@updateAgencyLGU',
        'module' => 'lib_agency_lgu',
        'access' => 'update'
    ])->name('agency-lgu-update');
    Route::post('libraries/agency-lgu/delete/{id}', [
        'uses' => 'LibraryController@deleteAgencyLGU',
        'module' => 'lib_agency_lgu',
        'access' => 'delete'
    ])->name('agency-lgu-delete');
    Route::post('libraries/agency-lgu/destroy/{id}', [
        'uses' => 'LibraryController@destroyAgencyLGU',
        'module' => 'lib_agency_lgu',
        'access' => 'destroy'
    ])->name('agency-lgu-destroy');
    Route::post('libraries/agency-lgu/get-agencies-lgus', [
        'uses' => 'LibraryController@getListAgencyLGU',
        'module' => 'lib_agency_lgu',
        'access' => 'is_allowed'
    ]);

    // Industry/Sectors Module
    Route::any('libraries/industry-sector', [
        'uses' => 'LibraryController@indexIndustrySector',
        'module' => 'lib_industry',
        'access' => 'is_allowed'
    ])->name('industry-sector');
    Route::get('libraries/industry-sector/show-create', [
        'uses' => 'LibraryController@showCreateIndustrySector',
        'module' => 'lib_industry',
        'access' => 'create'
    ])->name('industry-sector-show-create');
    Route::post('libraries/industry-sector/store', [
        'uses' => 'LibraryController@storeIndustrySector',
        'module' => 'lib_industry',
        'access' => 'create'
    ])->name('industry-sector-store');
    Route::get('libraries/industry-sector/show-edit/{id}', [
        'uses' => 'LibraryController@showEditIndustrySector',
        'module' => 'lib_industry',
        'access' => 'update'
    ])->name('industry-sector-show-edit');
    Route::post('libraries/industry-sector/update/{id}', [
        'uses' => 'LibraryController@updateIndustrySector',
        'module' => 'lib_industry',
        'access' => 'update'
    ])->name('industry-sector-update');
    Route::post('libraries/industry-sector/delete/{id}', [
        'uses' => 'LibraryController@deleteIndustrySector',
        'module' => 'lib_industry',
        'access' => 'delete'
    ])->name('industry-sector-delete');
    Route::post('libraries/industry-sector/destroy/{id}', [
        'uses' => 'LibraryController@destroyIndustrySector',
        'module' => 'lib_industry',
        'access' => 'destroy'
    ])->name('industry-sector-destroy');
    Route::post('libraries/industry-sector/get-industry-sector', [
        'uses' => 'LibraryController@getListIndustrySector',
        'module' => 'lib_industry',
        'access' => 'is_allowed'
    ]);

    // Item Classification Module
    Route::any('libraries/item-classification', [
        'uses' => 'LibraryController@indexItemClassification',
        'module' => 'lib_item_class',
        'access' => 'is_allowed'
    ])->name('item-classification');
    Route::get('libraries/item-classification/show-create', [
        'uses' => 'LibraryController@showCreateItemClassification',
        'module' => 'lib_item_class',
        'access' => 'create'
    ])->name('item-classification-show-create');
    Route::post('libraries/item-classification/store', [
        'uses' => 'LibraryController@storeItemClassification',
        'module' => 'lib_item_class',
        'access' => 'create'
    ])->name('item-classification-store');
    Route::get('libraries/item-classification/show-edit/{id}', [
        'uses' => 'LibraryController@showEditItemClassification',
        'module' => 'lib_item_class',
        'access' => 'update'
    ])->name('item-classification-show-edit');
    Route::post('libraries/item-classification/update/{id}', [
        'uses' => 'LibraryController@updateItemClassification',
        'module' => 'lib_item_class',
        'access' => 'update'
    ])->name('item-classification-update');
    Route::post('libraries/item-classification/delete/{id}', [
        'uses' => 'LibraryController@deleteItemClassification',
        'module' => 'lib_item_class',
        'access' => 'delete'
    ])->name('item-classification-delete');
    Route::post('libraries/item-classification/destroy/{id}', [
        'uses' => 'LibraryController@destroyItemClassification',
        'module' => 'lib_item_class',
        'access' => 'destroy'
    ])->name('item-classification-destroy');

    // MFO/PAP
    Route::any('libraries/mfo-pap', [
        'uses' => 'LibraryController@indexMfoPap',
        'module' => 'lib_item_class',
        'access' => 'is_allowed'
    ])->name('mfo-pap');
    Route::get('libraries/mfo-pap/show-create', [
        'uses' => 'LibraryController@showCreateMfoPap',
        'module' => 'lib_item_class',
        'access' => 'create'
    ])->name('mfo-pap-show-create');
    Route::post('libraries/mfo-pap/store', [
        'uses' => 'LibraryController@storeMfoPap',
        'module' => 'lib_item_class',
        'access' => 'create'
    ])->name('mfo-pap-store');
    Route::get('libraries/mfo-pap/show-edit/{id}', [
        'uses' => 'LibraryController@showEditMfoPap',
        'module' => 'lib_item_class',
        'access' => 'update'
    ])->name('mfo-pap-show-edit');
    Route::post('libraries/mfo-pap/update/{id}', [
        'uses' => 'LibraryController@updateMfoPap',
        'module' => 'lib_item_class',
        'access' => 'update'
    ])->name('mfo-pap-update');
    Route::post('libraries/mfo-pap/delete/{id}', [
        'uses' => 'LibraryController@deleteMfoPap',
        'module' => 'lib_item_class',
        'access' => 'delete'
    ])->name('mfo-pap-delete');
    Route::post('libraries/mfo-pap/destroy/{id}', [
        'uses' => 'LibraryController@destroyMfoPap',
        'module' => 'lib_item_class',
        'access' => 'destroy'
    ])->name('mfo-pap-destroy');

    // Monitoring Office Module
    Route::any('libraries/monitoring-office', [
        'uses' => 'LibraryController@indexMonitoringOffice',
        'module' => 'lib_monit_office',
        'access' => 'is_allowed'
    ])->name('monitoring-office');
    Route::get('libraries/monitoring-office/show-create', [
        'uses' => 'LibraryController@showCreateMonitoringOffice',
        'module' => 'lib_monit_office',
        'access' => 'create'
    ])->name('monitoring-office-show-create');
    Route::post('libraries/monitoring-office/store', [
        'uses' => 'LibraryController@storeMonitoringOffice',
        'module' => 'lib_monit_office',
        'access' => 'create'
    ])->name('monitoring-office-store');
    Route::get('libraries/monitoring-office/show-edit/{id}', [
        'uses' => 'LibraryController@showEditMonitoringOffice',
        'module' => 'lib_monit_office',
        'access' => 'update'
    ])->name('monitoring-office-show-edit');
    Route::post('libraries/monitoring-office/update/{id}', [
        'uses' => 'LibraryController@updateMonitoringOffice',
        'module' => 'lib_monit_office',
        'access' => 'update'
    ])->name('monitoring-office-update');
    Route::post('libraries/monitoring-office/delete/{id}', [
        'uses' => 'LibraryController@deleteMonitoringOffice',
        'module' => 'lib_monit_office',
        'access' => 'delete'
    ])->name('monitoring-office-delete');
    Route::post('libraries/monitoring-office/destroy/{id}', [
        'uses' => 'LibraryController@destroyMonitoringOffice',
        'module' => 'lib_monit_office',
        'access' => 'destroy'
    ])->name('monitoring-office-destroy');
    Route::post('libraries/monitoring-office/get-monitoring-office', [
        'uses' => 'LibraryController@getListMonitoringOffice',
        'module' => 'lib_monit_office',
        'access' => 'is_allowed'
    ]);

    // Project
    Route::any('libraries/project', [
        'uses' => 'LibraryController@indexProject',
        'module' => 'lib_funding',
        'access' => 'is_allowed'
    ])->name('project');
    Route::get('libraries/project/show-create', [
        'uses' => 'LibraryController@showCreateProject',
        'module' => 'lib_funding',
        'access' => 'create'
    ])->name('project-show-create');
    Route::post('libraries/project/store', [
        'uses' => 'LibraryController@storeProject',
        'module' => 'lib_funding',
        'access' => 'create'
    ])->name('project-store');
    Route::get('libraries/project/show-edit/{id}', [
        'uses' => 'LibraryController@showEditProject',
        'module' => 'lib_funding',
        'access' => 'update'
    ])->name('project-show-edit');
    Route::post('libraries/project/update/{id}', [
        'uses' => 'LibraryController@updateProject',
        'module' => 'lib_funding',
        'access' => 'update'
    ])->name('project-update');
    Route::post('libraries/project/delete/{id}', [
        'uses' => 'LibraryController@deleteProject',
        'module' => 'lib_funding',
        'access' => 'delete'
    ])->name('project-delete');
    Route::post('libraries/project/destroy/{id}', [
        'uses' => 'LibraryController@destroyProject',
        'module' => 'lib_funding',
        'access' => 'destroy'
    ])->name('project-destroy');

    // Signatory Module
    Route::any('libraries/signatory', [
        'uses' => 'LibraryController@indexSignatory',
        'module' => 'lib_signatory',
        'access' => 'is_allowed'
    ])->name('signatory');
    Route::get('libraries/signatory/show-create', [
        'uses' => 'LibraryController@showCreateSignatory',
        'module' => 'lib_signatory',
        'access' => 'create'
    ])->name('signatory-show-create');
    Route::post('libraries/signatory/store', [
        'uses' => 'LibraryController@storeSignatory',
        'module' => 'lib_signatory',
        'access' => 'create'
    ])->name('signatory-store');
    Route::get('libraries/signatory/show-edit/{id}', [
        'uses' => 'LibraryController@showEditSignatory',
        'module' => 'lib_signatory',
        'access' => 'update'
    ])->name('signatory-show-edit');
    Route::post('libraries/signatory/update/{id}', [
        'uses' => 'LibraryController@updateSignatory',
        'module' => 'lib_signatory',
        'access' => 'update'
    ])->name('signatory-update');
    Route::post('libraries/signatory/delete/{id}', [
        'uses' => 'LibraryController@deleteSignatory',
        'module' => 'lib_signatory',
        'access' => 'delete'
    ])->name('signatory-delete');
    Route::post('libraries/signatory/destroy/{id}', [
        'uses' => 'LibraryController@destroySignatory',
        'module' => 'lib_signatory',
        'access' => 'destroy'
    ])->name('signatory-destroy');

    // Supplier Classification Module
    Route::any('libraries/supplier-classification', [
        'uses' => 'LibraryController@indexSupplierClassification',
        'module' => 'lib_sup_class',
        'access' => 'is_allowed'
    ])->name('supplier-classification');
    Route::get('libraries/supplier-classification/show-create', [
        'uses' => 'LibraryController@showCreateSupplierClassification',
        'module' => 'lib_sup_class',
        'access' => 'create'
    ])->name('supplier-classification-show-create');
    Route::post('libraries/supplier-classification/store', [
        'uses' => 'LibraryController@storeSupplierClassification',
        'module' => 'lib_sup_class',
        'access' => 'create'
    ])->name('supplier-classification-store');
    Route::get('libraries/supplier-classification/show-edit/{id}', [
        'uses' => 'LibraryController@showEditSupplierClassification',
        'module' => 'lib_sup_class',
        'access' => 'update'
    ])->name('supplier-classification-show-edit');
    Route::post('libraries/supplier-classification/update/{id}', [
        'uses' => 'LibraryController@updateSupplierClassification',
        'module' => 'lib_sup_class',
        'access' => 'update'
    ])->name('supplier-classification-update');
    Route::post('libraries/supplier-classification/delete/{id}', [
        'uses' => 'LibraryController@deleteSupplierClassification',
        'module' => 'lib_sup_class',
        'access' => 'delete'
    ])->name('supplier-classification-delete');
    Route::post('libraries/supplier-classification/destroy/{id}', [
        'uses' => 'LibraryController@destroySupplierClassification',
        'module' => 'lib_sup_class',
        'access' => 'destroy'
    ])->name('supplier-classification-destroy');

    // Supplier Module
    Route::any('libraries/supplier', [
        'uses' => 'LibraryController@indexSupplier',
        'module' => 'lib_supplier',
        'access' => 'is_allowed'
    ])->name('supplier');
    Route::get('libraries/supplier/show-create', [
        'uses' => 'LibraryController@showCreateSupplier',
        'module' => 'lib_supplier',
        'access' => 'create'
    ])->name('supplier-show-create');
    Route::post('libraries/supplier/store', [
        'uses' => 'LibraryController@storeSupplier',
        'module' => 'lib_supplier',
        'access' => 'create'
    ])->name('supplier-store');
    Route::get('libraries/supplier/show-edit/{id}', [
        'uses' => 'LibraryController@showEditSupplier',
        'module' => 'lib_supplier',
        'access' => 'update'
    ])->name('supplier-show-edit');
    Route::post('libraries/supplier/update/{id}', [
        'uses' => 'LibraryController@updateSupplier',
        'module' => 'lib_supplier',
        'access' => 'update'
    ])->name('supplier-update');
    Route::post('libraries/supplier/delete/{id}', [
        'uses' => 'LibraryController@deleteSupplier',
        'module' => 'lib_supplier',
        'access' => 'delete'
    ])->name('supplier-delete');
    Route::post('libraries/supplier/destroy/{id}', [
        'uses' => 'LibraryController@destroySupplier',
        'module' => 'lib_supplier',
        'access' => 'destroy'
    ])->name('supplier-destroy');

    // UACS Object Classifications Module
    Route::any('libraries/uacs-classification', [
        'uses' => 'LibraryController@indexUacsClassification',
        'module' => 'lib_unit_issue',
        'access' => 'is_allowed'
    ])->name('uacs-classification');
    Route::get('libraries/uacs-classification/show-create', [
        'uses' => 'LibraryController@showCreateUacsClassification',
        'module' => 'lib_unit_issue',
        'access' => 'create'
    ])->name('uacs-classification-show-create');
    Route::post('libraries/uacs-classification/store', [
        'uses' => 'LibraryController@storeUacsClassification',
        'module' => 'lib_unit_issue',
        'access' => 'create'
    ])->name('uacs-classification-store');
    Route::get('libraries/uacs-classification/show-edit/{id}', [
        'uses' => 'LibraryController@showEditUacsClassification',
        'module' => 'lib_unit_issue',
        'access' => 'update'
    ])->name('uacs-classification-show-edit');
    Route::post('libraries/uacs-classification/update/{id}', [
        'uses' => 'LibraryController@updateUacsClassification',
        'module' => 'lib_unit_issue',
        'access' => 'update'
    ])->name('uacs-classification-update');
    Route::post('libraries/uacs-classification/delete/{id}', [
        'uses' => 'LibraryController@deleteUacsClassification',
        'module' => 'lib_unit_issue',
        'access' => 'delete'
    ])->name('uacs-classification-delete');
    Route::post('libraries/uacs-classification/destroy/{id}', [
        'uses' => 'LibraryController@destroyUacsClassification',
        'module' => 'lib_unit_issue',
        'access' => 'destroy'
    ])->name('uacs-classification-destroy');

    // UACS Object Codes Module
    Route::any('libraries/uacs-object-code', [
        'uses' => 'LibraryController@indexUacsObjCode',
        'module' => 'lib_unit_issue',
        'access' => 'is_allowed'
    ])->name('uacs-object-code');
    Route::get('libraries/uacs-object-code/show-create', [
        'uses' => 'LibraryController@showCreateUacsObjCode',
        'module' => 'lib_unit_issue',
        'access' => 'create'
    ])->name('uacs-object-code-show-create');
    Route::post('libraries/uacs-object-code/store', [
        'uses' => 'LibraryController@storeUacsObjCode',
        'module' => 'lib_unit_issue',
        'access' => 'create'
    ])->name('uacs-object-code-store');
    Route::get('libraries/uacs-object-code/show-edit/{id}', [
        'uses' => 'LibraryController@showEditUacsObjCode',
        'module' => 'lib_unit_issue',
        'access' => 'update'
    ])->name('uacs-object-code-show-edit');
    Route::post('libraries/uacs-object-code/update/{id}', [
        'uses' => 'LibraryController@updateUacsObjCode',
        'module' => 'lib_unit_issue',
        'access' => 'update'
    ])->name('uacs-object-code-update');
    Route::post('libraries/uacs-object-code/delete/{id}', [
        'uses' => 'LibraryController@deleteUacsObjCode',
        'module' => 'lib_unit_issue',
        'access' => 'delete'
    ])->name('uacs-object-code-delete');
    Route::post('libraries/uacs-object-code/destroy/{id}', [
        'uses' => 'LibraryController@destroyUacsObjCode',
        'module' => 'lib_unit_issue',
        'access' => 'destroy'
    ])->name('uacs-object-code-destroy');

    // Item Unit Issue Module
    Route::any('libraries/item-unit-issue', [
        'uses' => 'LibraryController@indexUnitissue',
        'module' => 'lib_unit_issue',
        'access' => 'is_allowed'
    ])->name('item-unit-issue');
    Route::get('libraries/item-unit-issue/show-create', [
        'uses' => 'LibraryController@showCreateUnitissue',
        'module' => 'lib_unit_issue',
        'access' => 'create'
    ])->name('item-unit-issue-show-create');
    Route::post('libraries/item-unit-issue/store', [
        'uses' => 'LibraryController@storeUnitissue',
        'module' => 'lib_unit_issue',
        'access' => 'create'
    ])->name('item-unit-issue-store');
    Route::get('libraries/item-unit-issue/show-edit/{id}', [
        'uses' => 'LibraryController@showEditUnitissue',
        'module' => 'lib_unit_issue',
        'access' => 'update'
    ])->name('item-unit-issue-show-edit');
    Route::post('libraries/item-unit-issue/update/{id}', [
        'uses' => 'LibraryController@updateUnitissue',
        'module' => 'lib_unit_issue',
        'access' => 'update'
    ])->name('item-unit-issue-update');
    Route::post('libraries/item-unit-issue/delete/{id}', [
        'uses' => 'LibraryController@deleteUnitissue',
        'module' => 'lib_unit_issue',
        'access' => 'delete'
    ])->name('item-unit-issue-delete');
    Route::post('libraries/item-unit-issue/destroy/{id}', [
        'uses' => 'LibraryController@destroyUnitissue',
        'module' => 'lib_unit_issue',
        'access' => 'destroy'
    ])->name('item-unit-issue-destroy');

    // Procurement Mode Module
    Route::any('libraries/procurement-mode', [
        'uses' => 'LibraryController@indexProcurementMode',
        'module' => 'lib_proc_mode',
        'access' => 'is_allowed'
    ])->name('procurement-mode');
    Route::get('libraries/procurement-mode/show-create', [
        'uses' => 'LibraryController@showCreateProcurementMode',
        'module' => 'lib_proc_mode',
        'access' => 'create'
    ])->name('procurement-mode-show-create');
    Route::post('libraries/procurement-mode/store', [
        'uses' => 'LibraryController@storeProcurementMode',
        'module' => 'lib_proc_mode',
        'access' => 'create'
    ])->name('procurement-mode-store');
    Route::get('libraries/procurement-mode/show-edit/{id}', [
        'uses' => 'LibraryController@showEditProcurementMode',
        'module' => 'lib_proc_mode',
        'access' => 'update'
    ])->name('procurement-mode-show-edit');
    Route::post('libraries/procurement-mode/update/{id}', [
        'uses' => 'LibraryController@updateProcurementMode',
        'module' => 'lib_proc_mode',
        'access' => 'update'
    ])->name('procurement-mode-update');
    Route::post('libraries/procurement-mode/delete/{id}', [
        'uses' => 'LibraryController@deleteProcurementMode',
        'module' => 'lib_proc_mode',
        'access' => 'delete'
    ])->name('procurement-mode-delete');
    Route::post('libraries/procurement-mode/destroy/{id}', [
        'uses' => 'LibraryController@destroyProcurementMode',
        'module' => 'lib_proc_mode',
        'access' => 'destroy'
    ])->name('procurement-mode-destroy');

    // Inventory Stock Classification Module
    Route::any('libraries/inventory-classification', [
        'uses' => 'LibraryController@indexInventoryClassification',
        'module' => 'lib_inv_class',
        'access' => 'is_allowed'
    ])->name('inventory-classification');
    Route::get('libraries/inventory-classification/show-create', [
        'uses' => 'LibraryController@showCreateInventoryClassification',
        'module' => 'lib_inv_class',
        'access' => 'create'
    ])->name('inventory-classification-show-create');
    Route::post('libraries/inventory-classification/store', [
        'uses' => 'LibraryController@storeInventoryClassification',
        'module' => 'lib_inv_class',
        'access' => 'create'
    ])->name('inventory-classification-store');
    Route::get('libraries/inventory-classification/show-edit/{id}', [
        'uses' => 'LibraryController@showEditInventoryClassification',
        'module' => 'lib_inv_class',
        'access' => 'update'
    ])->name('inventory-classification-show-edit');
    Route::post('libraries/inventory-classification/update/{id}', [
        'uses' => 'LibraryController@updateInventoryClassification',
        'module' => 'lib_inv_class',
        'access' => 'update'
    ])->name('inventory-classification-update');
    Route::post('libraries/inventory-classification/delete/{id}', [
        'uses' => 'LibraryController@deleteInventoryClassification',
        'module' => 'lib_inv_class',
        'access' => 'delete'
    ])->name('inventory-classification-delete');
    Route::post('libraries/inventory-classification/destroy/{id}', [
        'uses' => 'LibraryController@destroyInventoryClassification',
        'module' => 'lib_inv_class',
        'access' => 'destroy'
    ])->name('inventory-classification-destroy');

    // Paper Size Module
    Route::any('libraries/paper-size', [
        'uses' => 'LibraryController@indexPaperSize',
        'module' => 'lib_paper_size',
        'access' => 'is_allowed'
    ])->name('paper-size');
    Route::get('libraries/paper-size/show-create', [
        'uses' => 'LibraryController@showCreatePaperSize',
        'module' => 'lib_paper_size',
        'access' => 'create'
    ])->name('paper-size-show-create');
    Route::post('libraries/paper-size/store', [
        'uses' => 'LibraryController@storePaperSize',
        'module' => 'lib_paper_size',
        'access' => 'create'
    ])->name('paper-size-store');
    Route::get('libraries/paper-size/show-edit/{id}', [
        'uses' => 'LibraryController@showEditPaperSize',
        'module' => 'lib_paper_size',
        'access' => 'update'
    ])->name('paper-size-show-edit');
    Route::post('libraries/paper-size/update/{id}', [
        'uses' => 'LibraryController@updatePaperSize',
        'module' => 'lib_paper_size',
        'access' => 'update'
    ])->name('paper-size-update');
    Route::post('libraries/paper-size/delete/{id}', [
        'uses' => 'LibraryController@deletePaperSize',
        'module' => 'lib_paper_size',
        'access' => 'delete'
    ])->name('paper-size-delete');
    Route::post('libraries/paper-size/destroy/{id}', [
        'uses' => 'LibraryController@destroyPaperSize',
        'uses' => 'AccountController@indexDivision',
        'module' => 'lib_paper_size',
        'access' => 'destroy'
    ])->name('paper-size-destroy');

    /*===================== ACCOUNT MANAGEMENT ROUTES =====================*/

    // Employee Division Module
    Route::any('account-management/emp-division', [
        'uses' => 'AccountController@indexDivision',
        'module' => 'acc_division',
        'access' => 'is_allowed'
    ])->name('emp-division');
    Route::get('account-management/emp-division/show-create', [
        'uses' => 'AccountController@showCreateDivision',
        'module' => 'acc_division',
        'access' => 'create'
    ])->name('emp-division-show-create');
    Route::post('account-management/emp-division/store', [
        'uses' => 'AccountController@storeDivision',
        'module' => 'acc_division',
        'access' => 'create'
    ])->name('emp-division-store');
    Route::get('account-management/emp-division/show-edit/{id}', [
        'uses' => 'AccountController@showEditDivision',
        'module' => 'acc_division',
        'access' => 'update'
    ])->name('emp-division-show-edit');
    Route::post('account-management/emp-division/update/{id}', [
        'uses' => 'AccountController@updateDivision',
        'module' => 'acc_division',
        'access' => 'update'
    ])->name('emp-division-update');
    Route::post('account-management/emp-division/delete/{id}', [
        'uses' => 'AccountController@deleteDivision',
        'module' => 'acc_division',
        'access' => 'delete'
    ])->name('emp-division-delete');
    Route::post('account-management/emp-division/destroy/{id}', [
        'uses' => 'AccountController@destroyDivision',
        'module' => 'acc_division',
        'access' => 'destroy'
    ])->name('emp-division-destroy');

    // Employee Unit Module
    Route::any('account-management/emp-unit', [
        'uses' => 'AccountController@indexUnit',
        'module' => 'acc_division',
        'access' => 'is_allowed'
    ])->name('emp-unit');
    Route::get('account-management/emp-unit/show-create', [
        'uses' => 'AccountController@showCreateUnit',
        'module' => 'acc_division',
        'access' => 'create'
    ])->name('emp-unit-show-create');
    Route::post('account-management/emp-unit/store', [
        'uses' => 'AccountController@storeUnit',
        'module' => 'acc_division',
        'access' => 'create'
    ])->name('emp-unit-store');
    Route::get('account-management/emp-unit/show-edit/{id}', [
        'uses' => 'AccountController@showEditUnit',
        'module' => 'acc_division',
        'access' => 'update'
    ])->name('emp-unit-show-edit');
    Route::post('account-management/emp-unit/update/{id}', [
        'uses' => 'AccountController@updateUnit',
        'module' => 'acc_division',
        'access' => 'update'
    ])->name('emp-unit-update');
    Route::post('account-management/emp-unit/delete/{id}', [
        'uses' => 'AccountController@deleteUnit',
        'module' => 'acc_division',
        'access' => 'delete'
    ])->name('emp-unit-delete');
    Route::post('account-management/emp-unit/destroy/{id}', [
        'uses' => 'AccountController@destroyUnit',
        'module' => 'acc_division',
        'access' => 'destroy'
    ])->name('emp-unit-destroy');

    // Employee Role Module
    Route::any('account-management/emp-role', [
        'uses' => 'AccountController@indexRole',
        'module' => 'acc_role',
        'access' => 'is_allowed'
    ])->name('emp-role');
    Route::get('account-management/emp-role/show-create', [
        'uses' => 'AccountController@showCreateRole',
        'module' => 'acc_role',
        'access' => 'create'
    ])->name('emp-role-show-create');
    Route::post('account-management/emp-role/store', [
        'uses' => 'AccountController@storeRole',
        'module' => 'acc_role',
        'access' => 'create'
    ])->name('emp-role-store');
    Route::get('account-management/emp-role/show-edit/{id}', [
        'uses' => 'AccountController@showEditRole',
        'module' => 'acc_role',
        'access' => 'update'
    ])->name('emp-role-show-edit');
    Route::post('account-management/emp-role/update/{id}', [
        'uses' => 'AccountController@updateRole',
        'module' => 'acc_role',
        'access' => 'update'
    ])->name('emp-role-update');
    Route::post('account-management/emp-role/delete/{id}', [
        'uses' => 'AccountController@deleteRole',
        'module' => 'acc_role',
        'access' => 'delete'
    ])->name('emp-role-delete');
    Route::post('account-management/emp-role/destroy/{id}', [
        'uses' => 'AccountController@destroyRole',
        'module' => 'acc_role',
        'access' => 'destroy'
    ])->name('emp-role-destroy');

    // Employee Account Module
    Route::any('account-management/emp-account', [
        'uses' => 'AccountController@indexAccount',
        'module' => 'acc_account',
        'access' => 'is_allowed'
    ])->name('emp-account');
    Route::get('account-management/emp-account/show-create', [
        'uses' => 'AccountController@showCreateAccount',
        'module' => 'acc_account',
        'access' => 'create'
    ])->name('emp-account-show-create');
    Route::post('account-management/emp-account/store', [
        'uses' => 'AccountController@storeAccount',
        'module' => 'acc_account',
        'access' => 'create'
    ])->name('emp-account-store');
    Route::get('account-management/emp-account/show-edit/{id}', [
        'uses' => 'AccountController@showEditAccount',
        'module' => 'acc_account',
        'access' => 'update'
    ])->name('emp-account-show-edit');
    Route::post('account-management/emp-account/update/{id}', [
        'uses' => 'AccountController@updateAccount',
        'module' => 'acc_account',
        'access' => 'update'
    ])->name('emp-account-update');
    Route::post('account-management/emp-account/delete/{id}', [
        'uses' => 'AccountController@deleteAccount',
        'module' => 'acc_account',
        'access' => 'delete'
    ])->name('emp-account-delete');
    Route::post('account-management/emp-account/destroy/{id}', [
        'uses' => 'AccountController@destroyAccount',
        'module' => 'acc_account',
        'access' => 'destroy'
    ])->name('emp-account-destroy');

    // Employee Group Module
    Route::any('account-management/emp-group', [
        'uses' => 'AccountController@indexGroup',
        'module' => 'acc_group',
        'access' => 'is_allowed'
    ])->name('emp-group');
    Route::get('account-management/emp-group/show-create', [
        'uses' => 'AccountController@showCreateGroup',
        'module' => 'acc_group',
        'access' => 'create'
    ])->name('emp-group-show-create');
    Route::post('account-management/emp-group/store', [
        'uses' => 'AccountController@storeGroup',
        'module' => 'acc_group',
        'access' => 'create'
    ])->name('emp-group-store');
    Route::get('account-management/emp-group/show-edit/{id}', [
        'uses' => 'AccountController@showEditGroup',
        'module' => 'acc_group',
        'access' => 'update'
    ])->name('emp-group-show-edit');
    Route::post('account-management/emp-group/update/{id}', [
        'uses' => 'AccountController@updateGroup',
        'module' => 'acc_group',
        'access' => 'update'
    ])->name('emp-group-update');
    Route::post('account-management/emp-group/delete/{id}', [
        'uses' => 'AccountController@deleteGroup',
        'module' => 'acc_group',
        'access' => 'delete'
    ])->name('emp-group-delete');
    Route::post('account-management/emp-group/destroy/{id}', [
        'uses' => 'AccountController@destroyGroup',
        'module' => 'acc_group',
        'access' => 'destroy'
    ])->name('emp-group-destroy');

    // Employee Logs Module
    Route::any('account-management/emp-log', [
        'uses' => 'AccountController@indexLogs',
        'module' => 'acc_user_log',
        'access' => 'is_allowed'
    ])->name('emp-log');
    Route::post('account-management/emp-log/destroy/{id}', [
        'uses' => 'AccountController@destroyLogs',
        'module' => 'acc_user_log',
        'access' => 'destroy'
    ])->name('emp-log-destroy');

    /*===================== PLACES ROUTES =====================*/

    // Region Module
    Route::any('places/region', [
        'uses' => 'PlaceController@indexRegion',
        'module' => 'place_region',
        'access' => 'is_allowed'
    ])->name('region');
    Route::get('places/region/show-create', [
        'uses' => 'PlaceController@showCreateRegion',
        'module' => 'place_region',
        'access' => 'create'
    ])->name('region-show-create');
    Route::post('places/region/store', [
        'uses' => 'PlaceController@storeRegion',
        'module' => 'place_region',
        'access' => 'create'
    ])->name('region-store');
    Route::get('places/region/show-edit/{id}', [
        'uses' => 'PlaceController@showEditRegion',
        'module' => 'place_region',
        'access' => 'update'
    ])->name('region-show-edit');
    Route::post('places/region/update/{id}', [
        'uses' => 'PlaceController@updateRegion',
        'module' => 'place_region',
        'access' => 'update'
    ])->name('region-update');
    Route::post('places/region/delete/{id}', [
        'uses' => 'PlaceController@deleteRegion',
        'module' => 'place_region',
        'access' => 'delete'
    ])->name('region-delete');
    Route::post('places/region/destroy/{id}', [
        'uses' => 'PlaceController@destroyRegion',
        'module' => 'place_region',
        'access' => 'destroy'
    ])->name('region-destroy');

    // Province Module
    Route::any('places/province', [
        'uses' => 'PlaceController@indexProvince',
        'module' => 'place_province',
        'access' => 'is_allowed'
    ])->name('province');
    Route::get('places/province/show-create', [
        'uses' => 'PlaceController@showCreateProvince',
        'module' => 'place_province',
        'access' => 'create'
    ])->name('province-show-create');
    Route::post('places/province/store', [
        'uses' => 'PlaceController@storeProvince',
        'module' => 'place_province',
        'access' => 'create'
    ])->name('province-store');
    Route::get('places/province/show-edit/{id}', [
        'uses' => 'PlaceController@showEditProvince',
        'module' => 'place_province',
        'access' => 'update'
    ])->name('province-show-edit');
    Route::post('places/province/update/{id}', [
        'uses' => 'PlaceController@updateProvince',
        'module' => 'place_province',
        'access' => 'update'
    ])->name('province-update');
    Route::post('places/province/delete/{id}', [
        'uses' => 'PlaceController@deleteProvince',
        'module' => 'place_province',
        'access' => 'delete'
    ])->name('province-delete');
    Route::post('places/province/destroy/{id}', [
        'uses' => 'PlaceController@destroyProvince',
        'module' => 'place_province',
        'access' => 'destory'
    ])->name('province-destroy');

    // Municipality Module
    Route::any('places/municipality', [
        'uses' => 'PlaceController@indexMunicipality',
        'module' => 'place_municipality',
        'access' => 'is_allowed'
    ])->name('municipality');
    Route::get('places/municipality/show-create', [
        'uses' => 'PlaceController@showCreateMunicipality',
        'module' => 'place_municipality',
        'access' => 'create'
    ])->name('municipality-show-create');
    Route::post('places/municipality/store', [
        'uses' => 'PlaceController@storeMunicipality',
        'module' => 'place_municipality',
        'access' => 'create'
    ])->name('municipality-store');
    Route::get('places/municipality/show-edit/{id}', [
        'uses' => 'PlaceController@showEditMunicipality',
        'module' => 'place_municipality',
        'access' => 'update'
    ])->name('municipality-show-edit');
    Route::post('places/municipality/update/{id}', [
        'uses' => 'PlaceController@updateMunicipality',
        'module' => 'place_municipality',
        'access' => 'update'
    ])->name('municipality-update');
    Route::post('places/municipality/delete/{id}', [
        'uses' => 'PlaceController@deleteMunicipality',
        'module' => 'place_municipality',
        'access' => 'delete'
    ])->name('municipality-delete');
    Route::post('places/municipality/destroy/{id}', [
        'uses' => 'PlaceController@destroyMunicipality',
        'module' => 'place_municipality',
        'access' => 'destory'
    ])->name('municipality-destroy');
});

// PAR,
Route::get('/par','ParController@index')->middleware('auth');
Route::get('/ris','RisController@index')->middleware('auth');
Route::get('/ics','IcsController@index')->middleware('auth');
Route::get('/parrisics','PARRISICSController@index')->middleware('auth');