<?php

namespace App\Plugins;

use App\Notifications\Procurement as ProcNotify;
use App\Notifications\AccountManagement as AccountManageNotify;

use App\Models\PurchaseRequest;
use App\Models\RequestQuotation;
use App\Models\AbstractQuotation;
use App\Models\PurchaseJobOrder;
use App\Models\ObligationRequestStatus;
use App\Models\InspectionAcceptance;
use App\Models\DisbursementVoucher;
use App\Models\LiquidationReport;
use App\Models\InventoryStock;
use App\Models\ListDemandPayable;
use App\Models\SummaryLDDAP;

use App\Models\EmpAccount as User;
use Auth;

class Notification {

    // Obligation / Budget Utilization Request and Status
    public function notifyIssuedORS($id, $subModule) {
        $orsData = ObligationRequestStatus::find($id);
        $documentType = $orsData->document_type ? 'Obligation Request and Status' :
                        'Budget Utilization Request and Status';
        $currentUser = Auth::user()->id;
        $currentUserName = Auth::user()->getEmployee($currentUser)->name;

        if ($subModule == 'proc-ors-burs') {
            $module = 'procurement';
            $poNo = $orsData->po_no;
            $prData = PurchaseRequest::find($orsData->pr_id);
            $userID = $prData->requested_by;
            $msgNotif = "<b>$currentUserName</b> submitted the <b>$documentType</b>,
                        which is linked to PO/JO number <b>$poNo</b>, to you.";
        } else {
            $module = 'cash_advance';
            $userID = $orsData->payee;
            $msgNotif = "<b>$currentUserName</b> submitted the <b>$documentType</b> to you.";
        }

        $users = User::where('is_active', 'y')
                     ->get();

        $data = (object) [
            'id' => $id,
            'module' => $module,
            'sub_module' => $subModule,
            'msg' => $msgNotif,
        ];

        foreach ($users as $user) {
            if ($user->hasBudgetRole($user->id)) {
                $user->notify(new ProcNotify($data));
            }
        }
    }

    public function notifyReceivedORS($id, $subModule) {
        $orsData = ObligationRequestStatus::find($id);
        $documentType = $orsData->document_type ? 'Obligation Request and Status' :
                        'Budget Utilization Request and Status';

        if ($subModule == 'proc-ors-burs') {
            $module = 'procurement';
            $poNo = $orsData->po_no;
            $prData = PurchaseRequest::find($orsData->pr_id);
            $userID = $prData->requested_by;
            $msgNotif = "<b>$documentType</b>, which is linked to PO/JO number <b>$poNo</b>,
                        has been <b>Received</b> by the Budget Officer.";
            $data = (object) [
                'id' => $id,
                'module' => $module,
                'sub_module' => $subModule,
                'msg' => $msgNotif,
            ];

            $users = User::where('is_active', 'y')
                         ->get();

            foreach ($users as $_user) {
                if ($_user->hasPropertySupplyRole($_user->id)) {
                    $_user->notify(new ProcNotify($data));
                }
            }

            $msgNotif = "Your <b>$documentType</b>, which is linked to PO/JO number <b>$poNo</b>,
                        has been <b>Received</b> by the Budget Officer.";
        } else {
            $module = 'cash_advance';
            $userID = $orsData->payee;
            $msgNotif = "Your <b>$documentType</b> with an ID of <b>$id</b>
                        has been <b>Received</b> by the Budget Officer.";
        }

        $user = User::find($userID);
        $data = (object) [
            'id' => $id,
            'module' => $module,
            'sub_module' => $subModule,
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyIssuedBackORS($id, $subModule) {
        $orsData = ObligationRequestStatus::find($id);
        $documentType = $orsData->document_type ? 'Obligation Request and Status' :
                        'Budget Utilization Request and Status';

        if ($subModule == 'proc-ors-burs') {
            $module = 'procurement';
            $poNo = $orsData->po_no;
            $prData = PurchaseRequest::find($orsData->pr_id);
            $userID = $prData->requested_by;
            $msgNotif = "Your <b>$documentType</b> with a serial number of <b>$serialNo</b>
                        linked to PO/JO number <b>$poNo</b> is now <b>Obligated</b>.";
        } else {
            $module = 'cash_advance';
            $userID = $orsData->payee;
            $msgNotif = "Your <b>$documentType</b> with a serial number of <b>$serialNo</b>
                        is now <b>Obligated</b>.";
        }

        $user = User::find($userID);

        $data = (object) [
            'id' => $id,
            'module' => $module,
            'sub_module' => $subModule,
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyReceivedBackORS($id, $subModule) {
        $orsData = ObligationRequestStatus::find($id);
        $documentType = $orsData->document_type ? 'Obligation Request and Status' :
                        'Budget Utilization Request and Status';

        if ($subModule == 'proc-ors-burs') {
            $module = 'procurement';
            $poNo = $orsData->po_no;
            $prData = PurchaseRequest::find($orsData->pr_id);
            $userID = $prData->requested_by;
            $msgNotif = "Your <b>$documentType</b> with a serial number of <b>$serialNo</b>,
                        which is linked to PO/JO number <b>$poNo</b>, is now <b>Obligated</b>.";
        } else {
            $module = 'cash_advance';
            $userID = $orsData->payee;
            $msgNotif = "Your <b>$documentType</b> with a serial number of <b>$serialNo</b>
                        is now <b>Obligated</b>.";
        }

        $user = User::find($userID);

        $data = (object) [
            'id' => $id,
            'module' => $module,
            'sub_module' => $subModule,
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyObligatedORS($id, $subModule) {
        $orsData = ObligationRequestStatus::find($id);
        $documentType = $orsData->document_type ? 'Obligation Request and Status' :
                        'Budget Utilization Request and Status';
        $serialNo = $orsData->serial_no;

        if ($subModule == 'proc-ors-burs') {
            $module = 'procurement';
            $poNo = $orsData->po_no;
            $prData = PurchaseRequest::find($orsData->pr_id);
            $userID = $prData->requested_by;
            $msgNotif = "Your <b>$documentType</b> with a serial number of <b>$serialNo</b>
                        linked to PO/JO number <b>$poNo</b> is now <b>Obligated</b>.";
        } else {
            $module = 'cash_advance';
            $userID = $orsData->created_by;
            $msgNotif = "Your <b>$documentType</b> with a serial number of <b>$serialNo</b>
                        is now <b>Obligated</b>.";
        }

        $user = User::find($userID);

        $data = (object) [
            'id' => $id,
            'module' => $module,
            'sub_module' => $subModule,
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    // Disbursement Voucher
    public function notifyIssuedDV($id, $subModule) {
        $dvData = DisbursementVoucher::find($id);

        if ($subModule == 'proc-dv') {

        } else {

        }
    }

    public function notifyReceivedDV($id, $subModule) {
        $dvData = DisbursementVoucher::find($id);

        if ($subModule == 'proc-dv') {

        } else {

        }
    }

    public function notifyIssuedBackDV($id, $subModule) {
        $dvData = DisbursementVoucher::find($id);

        if ($subModule == 'proc-dv') {

        } else {

        }
    }

    public function notifyReceivedBackDV($id, $subModule) {
        $dvData = DisbursementVoucher::find($id);

        if ($subModule == 'proc-dv') {

        } else {

        }
    }

    public function notifyToPaymentDV($id, $subModule) {
        $dvData = DisbursementVoucher::find($id);

        if ($subModule == 'proc-dv') {

        } else {

        }
    }

    /**
     * ACCOUNT MANAGEMENT METHODS
     */
    // User Registration
    public function notifyAccountRegistered($empID) {
        $users = User::where('is_active', 'y')
                     ->get();
        $registeredUser = User::where('emp_id', $empID)->first();
        $id = $registeredUser->id;
        $userFullName = $registeredUser->firstname .
                        (!empty($registeredUser->middlename) ? ' '.$registeredUser->middlename[0].'. ' : ' ') .
                        $registeredUser->lastname;

        $msgNotif =  "Pending account approval for <b>$userFullName</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'account_management',
            'sub_module' => 'emp-account',
            'action' => 'register',
            'msg' => $msgNotif,
        ];

        foreach ($users as $user) {
            if ($user->hasDeveloperRole($user->id) || $user->hasAdministratorRole($user->id)) {
                $user->notify(new AccountManageNotify($data));
            }
        }
    }

    /**
     * PROCUREMENT NOTIFICATION METHODS
     */

    // Purchase Request
    public function notifyForApprovalPR($id) {
        $users = User::where('is_active', 'y')
                     ->get();
        $prData = PurchaseRequest::find($id);
        $prNo = $prData->pr_no;
        $requestedBy = $prData->requested_by;
        $userData = User::find($requestedBy);
        $requestorName = $userData->firstname .
                         (!empty($userData->middlename) ? ' '.$userData->middlename[0].'. ' : ' ') .
                         $userData->lastname;

        $msgNotif =  "<b>$requestorName</b> created a new <b>Purchase Request</b> with a PR number of <b>$prNo</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'pr',
            'msg' => $msgNotif,
        ];

        foreach ($users as $user) {
            if (!$user->hasOrdinaryRole($user->id)) {
                $user->notify(new ProcNotify($data));
            }
        }
    }

    public function notifyApprovedPR($id) {
        $prData = PurchaseRequest::find($id);
        $prNo = $prData->pr_no;
        $requestedBy = $prData->requested_by;
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>Purchase Request</b> with a PR number of <b>$prNo</b> is now approved.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'pr',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyDisapprovedPR($id) {
        $prData = PurchaseRequest::find($id);
        $prNo = $prData->pr_no;
        $requestedBy = $prData->requested_by;
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>Purchase Request</b> with a PR number of <b>$prNo</b> has been disapproved.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'pr',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyCancelledPR($id) {
        $prData = PurchaseRequest::find($id);
        $prNo = $prData->pr_no;
        $requestedBy = $prData->requested_by;
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>Purchase Request</b> with a PR number of <b>$prNo</b> has been cancelled.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'pr',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyRestoredPR($id) {
        $prData = PurchaseRequest::find($id);
        $prNo = $prData->pr_no;
        $requestedBy = $prData->requested_by;
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>Purchase Request</b> with a PR number of <b>$prNo</b> is now restored.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'pr',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    // Request for Quotation
    public function notifyIssuedRFQ($id, $responsiblePerson) {
        $rfqData = RequestQuotation::find($id);
        $prID = $rfqData->pr_id;
        $prData = PurchaseRequest::find($prID);
        $prNo = $prData->pr_no;
        $requestedBy = $prData->requested_by;

        if ($responsiblePerson == $requestedBy) {
            $user = User::find($requestedBy);
            $msgNotif = "<b>Request for Quotation</b> with a quotation number of <b>$prNo</b>
                        is now issued to you.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'rfq',
                'msg' => $msgNotif,
            ];
            $user->notify(new ProcNotify($data));
        } else {
            $user = User::find($responsiblePerson);
            $msgNotif = "<b>Request for Quotation</b> with a quotation number of <b>$prNo</b>
                        is now issued to you.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'rfq',
                'msg' => $msgNotif,
            ];
            $user->notify(new ProcNotify($data));

            $user = User::find($requestedBy);
            $responsiblePersonName = $user->getEmployee($responsiblePerson)->name;
            $msgNotif = "Your <b>Request for Quotation</b> with a quotation number of <b>$prNo</b>
                        has been issued to <b>$responsiblePersonName</b>.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'rfq',
                'msg' => $msgNotif,
            ];
            $user->notify(new ProcNotify($data));
        }
    }

    public function notifyReceivedRFQ($id, $receivedBy, $responsiblePerson) {
        $rfqData = RequestQuotation::find($id);
        $prID = $rfqData->pr_id;
        $prData = PurchaseRequest::find($prID);
        $prNo = $prData->pr_no;
        $requestedBy = $prData->requested_by;

        $user = new User;
        $receivedByName = $user->getEmployee($receivedBy)->name;
        $requestedByName = $user->getEmployee($requestedBy)->name;

        if ($responsiblePerson == $requestedBy) {
            $user = User::find($requestedBy);
            $msgNotif = "Your <b>Request for Quotation</b> with a quotation number of <b>$prNo</b>
                        has been received by <b>$receivedByName</b>, and it is now ready for
                        <b>Abstract for Quotation</b>.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'rfq',
                'msg' => $msgNotif,
            ];
            $user->notify(new ProcNotify($data));
        } else {
            $user = User::find($responsiblePerson);
            $msgNotif = "<b>Request for Quotation</b> with a quotation number of <b>$prNo</b> of
                        <b>$requestedByName</b> has been received by <b>$receivedByName</b>, and it
                        is now ready for <b>Abstract for Quotation</b>.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'rfq',
                'msg' => $msgNotif,
            ];
            $user->notify(new ProcNotify($data));

            $user = User::find($requestedBy);
            $msgNotif = "Your <b>Request for Quotation</b> with a quotation number of <b>$prNo</b>
                        has been received by <b>$receivedByName</b>, and it is now ready for
                        <b>Abstract for Quotation</b>.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'rfq',
                'msg' => $msgNotif,
            ];
            $user->notify(new ProcNotify($data));
        }
    }

    // Abstract of Quotations
    public function notifyApprovedForPOAbstract($id) {
        $currentUser = Auth::user()->id;
        $absData = AbstractQuotation::find($id);
        $prID = $absData->pr_id;
        $prData = PurchaseRequest::find($prID);
        $prNo = $prData->pr_no;
        $requestedBy = $prData->requested_by;
        $user = User::find($requestedBy);
        $currentUseryName = $user->getEmployee($currentUser)->name;
        $requestedByName = $user->getEmployee($requestedBy)->name;
        $msgNotif = "Your <b>Abstract of Quotation</b> with a quotation number of <b>$prNo</b>
                    has been set to <b>Approved for PO/JO</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'abstract',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    // Purchase/Job Order
    public function notifyAccountantSignedPO($id) {
        $poData = PurchaseJobOrder::find($id);
        $prData = PurchaseRequest::find($poData->pr_id);
        $requestedBy = $prData->requested_by;
        $poNo = $poData->po_no;
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                    has been set to <b>Cleared/Signed by the Accountant</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'po-jo',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyApprovedPO($id) {
        $poData = PurchaseJobOrder::find($id);
        $prData = PurchaseRequest::find($poData->pr_id);
        $requestedBy = $prData->requested_by;
        $poNo = $poData->po_no;
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                    is now <b>Approved</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'po-jo',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyIssuedPO($id, $responsiblePerson) {
        $poData = PurchaseJobOrder::find($id);
        $prData = PurchaseRequest::find($poData->pr_id);
        $requestedBy = $prData->requested_by;
        $poNo = $poData->po_no;
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);
        $user1 = User::find($responsiblePerson);
        $currentUser = Auth::user()->id;
        $requestorName = $user->getEmployee($requestedBy)->name;
        $responsiblePersonName = $user->getEmployee($responsiblePerson)->name;

        if ($responsiblePerson == $currentUser) {
            $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                        has been issued to you.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'po-jo',
                'msg' => $msgNotif,
            ];
            $user->notify(new ProcNotify($data));
        } else {
            // Requestor
            $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                        has been issued to <b>$responsiblePersonName</b>.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'po-jo',
                'msg' => $msgNotif,
            ];
            $user->notify(new ProcNotify($data));

            // Issuee
            $msgNotif = "<b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                        of <b>$requestorName</b> has been issued to you.";
            $data = (object) [
                'id' => $id,
                'module' => 'procurement',
                'sub_module' => 'po-jo',
                'msg' => $msgNotif,
            ];
            $user1->notify(new ProcNotify($data));
        }
    }

    public function notifyReceivedPO($id) {
        $poData = PurchaseJobOrder::find($id);
        $prData = PurchaseRequest::find($poData->pr_id);
        $requestedBy = $prData->requested_by;
        $poNo = $poData->po_no;
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);
        $currentUser = Auth::user()->id;
        $currentUserName = $user->getEmployee($currentUser)->name;

        $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                    has been received by <b>$currentUserName</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'po-jo',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyDeliveredPO($id) {
        $poData = PurchaseJobOrder::find($id);
        $prData = PurchaseRequest::find($poData->pr_id);
        $requestedBy = $prData->requested_by;
        $poNo = $poData->po_no;
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                    is now <b>For Delivery</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'po-jo',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyInspectionPO($id) {
        $poData = PurchaseJobOrder::find($id);
        $prData = PurchaseRequest::find($poData->pr_id);
        $requestedBy = $prData->requested_by;
        $poNo = $poData->po_no;
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                    is now <b>For Inspection</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'po-jo',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyCancelledPO($id) {
        $poData = PurchaseJobOrder::find($id);
        $prData = PurchaseRequest::find($poData->pr_id);
        $requestedBy = $prData->requested_by;
        $poNo = $poData->po_no;
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                    has been <b>Cancelled</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'po-jo',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyRestoredPO($id) {
        $poData = PurchaseJobOrder::find($id);
        $prData = PurchaseRequest::find($poData->pr_id);
        $requestedBy = $prData->requested_by;
        $poNo = $poData->po_no;
        $documentTypeAbbrv = strtoupper($poData->document_type);
        $documentType = $poData->document_type == 'po' ? 'Purchase Order' : 'Job Order';
        $user = User::find($requestedBy);

        $msgNotif = "Your <b>$documentType</b> with a $documentTypeAbbrv number of <b>$poNo</b>
                    is now <b>Restored</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'po-jo',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    // Obligation / Budget Utilization Request and Status
    public function notifyCreatedORS($poNo) {
        $orsData = ObligationRequestStatus::where('po_no', $poNo)->first();
        $id = $orsData->id;
        $prData = PurchaseRequest::find($orsData->pr_id);
        $requestedBy = $prData->requested_by;
        $user = User::find($requestedBy);

        $msgNotif = "<b>Obligation Request and Status</b> document for PO/JO number <b>$poNo</b>
                    is now created.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'proc-ors-burs',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    // Inspection and Acceptance Report
    public function notifyIssuedIAR($id, $responsiblePerson) {
        $iarData = InspectionAcceptance::find($id);
        $prID = $iarData->pr_id;
        $prData = PurchaseRequest::find($prID);
        $iarNo = $iarData->iar_no;
        $requestedBy = $prData->requested_by;

        $user = User::find($requestedBy);
        $responsiblePersonName = $user->getEmployee($responsiblePerson)->name;
        $msgNotif = "The <b>Inspection and Acceptance Report</b> with an IAR number of <b>$iarNo</b>
                    has been issued to <b>$responsiblePersonName</b> for inspection.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'iar',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyInspectIAR($id) {
        $iarData = InspectionAcceptance::find($id);
        $prID = $iarData->pr_id;
        $prData = PurchaseRequest::find($prID);
        $poID = $iarData->po_id;
        $requestedBy = $prData->requested_by;
        $poData = PurchaseJobOrder::find($poID);
        $poNo = $poData->po_no;
        $documentType = $poData->document_type == 'po' ?
                        'Purchase Order' : 'Job Order';
        $documentTypeAbbr = strtoupper($poData->document_type);

        $user = User::find($requestedBy);
        $msgNotif = "The items in your <b>$documentType</b> with a $documentTypeAbbr number of
                    <b>$poNo</b> has been <b>Inspected</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'procurement',
            'sub_module' => 'iar',
            'msg' => $msgNotif,
        ];
        $user->notify(new ProcNotify($data));
    }

    public function notifyApproveLDDAP($id, $_approvedBy) {
        $lddapData = ListDemandPayable::find($id);
        $lddapNo = $lddapData->lddap_ada_no;
        $approvedBy = User::find($_approvedBy);
        $approvedByName = Auth::user()->getEmployee($_approvedBy)->name;

        $msgNotif = "The <b>List of Due and Demandable Accounts Payable</b> with a LDDAP number of <b>$lddapNo</b>
                    has been <b>Approved</b> by <b>$approvedByName</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'payment',
            'sub_module' => 'lddap',
            'msg' => $msgNotif,
        ];
        $_user = User::all();

        foreach ($_user as $user) {
            $userID = $user->id;

            $hasAccountantRole = $user->hasAccountantRole($userID);

            if ($hasAccountantRole) {
                $user->notify(new ProcNotify($data));
            }
        }
    }

    public function notifySummaryLDDAP($id) {
        $lddapData = ListDemandPayable::find($id);
        $lddapNo = $lddapData->lddap_ada_no;

        $msgNotif = "The <b>List of Due and Demandable Accounts Payable</b> with a LDDAP number of <b>$lddapNo</b>
                    is now ready for <b>Summary</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'payment',
            'sub_module' => 'lddap',
            'msg' => $msgNotif,
        ];
        $_user = User::all();

        foreach ($_user as $user) {
            $userID = $user->id;

            $hasCashierRole = $user->hasCashierRole($userID);

            if ($hasCashierRole) {
                $user->notify(new ProcNotify($data));
            }
        }
    }

    public function notifyApproveSummary($id, $_approvedBy) {
        $summaryData = SummaryLDDAP::find($id);
        $sliiaeNo = $summaryData->sliiae_no;
        $approvedBy = User::find($_approvedBy);
        $approvedByName = Auth::user()->getEmployee($_approvedBy)->name;

        $msgNotif = "The <b>Summary of LDDAP-ADAs Issued and Invalidated ADA Entries</b>
                    with a SLIIAE number of <b>$sliiaeNo</b>
                    has been <b>Approved</b> by <b>$approvedByName</b>.";
        $data = (object) [
            'id' => $id,
            'module' => 'payment',
            'sub_module' => 'summary',
            'msg' => $msgNotif,
        ];
        $_user = User::all();

        foreach ($_user as $user) {
            $userID = $user->id;

            $hasCashierRole = $user->hasCashierRole($userID);

            if ($hasCashierRole) {
                $user->notify(new ProcNotify($data));
            }
        }
    }
}
