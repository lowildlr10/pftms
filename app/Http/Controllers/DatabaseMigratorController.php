<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DocumentLog;
use App\DocumentLogHistory;
use Carbon\Carbon;
use DB;

class DatabaseMigratorController extends Controller
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

    public function index() {
        return view('pages.db-migrator');
    }

    public function migrate(Request $request) {
        ini_set('max_execution_time', 600);
        DB::table('tbldocument_logs')->truncate();

        $file = $request->file('file');
        $servername = $request['servername'];
        $username = $request['username'];
        $password = $request['password'];
        $link = $this->connectMySQL($servername, $username, $password);

        // Check connection
        if ($link === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        } else {
            $this->createDB($link);
        }

        $link = $this->connectMySQL($servername, $username, $password, 'temp_dbpis');

        // Check connection
        if ($link === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        } else {
            $this->proccessMigration($file, $link);
        }

        // Close connection
        mysqli_close($link);
    }

    private function connectMySQL($servername, $username, $password, $database = "") {
        /* Attempt MySQL server connection. Assuming you are running MySQL
        server with default setting (user 'root' with no password) */
        $link = mysqli_connect($servername, $username, $password, $database);

        return $link;
    }

    private function createDB($link) {
        // Attempt create database query execution
        $sql = "CREATE DATABASE temp_dbpis";

        if (mysqli_query($link, $sql)) {
            echo "[" . date('H:i:s') . "] : " . "Database created successfully\n";
        } else {
            //echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
            $sql = "DROP DATABASE temp_dbpis";

            if (mysqli_query($link, $sql)) {
                echo "[" . date('H:i:s') . "] : " . "Database deleted successfully\n";
            }

            $this->createDB($link);
        }
    }

    private function proccessMigration($file, $link) {
        // Temporary variable, used to store current query
        $templine = '';

        // Read in entire file
        $fp = fopen($file, 'r');

        while (($line = fgets($fp)) !== false) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $templine .= $line;

            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                if (!mysqli_query($link, $templine)) {
                    print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error($connection) . '<br /><br />');
                }

                // Reset temp variable to empty
                $templine = '';
            }
        }

        fclose($fp);

        echo "[" . date('H:i:s') . "] : " . "Tables imported successfully.\n";
    }

    public function migratePIS_PFMS(Request $request, $type) {
        ini_set('max_execution_time', 600);

        switch ($type) {
            case 'employee':
                DB::table('tblemp_accounts')->truncate();
                $this->employeeModule();
                break;

            case 'signatory':
                DB::table('tblsignatories')->truncate();
                $this->signatoriesModule();
                break;

            case 'supplier-classification':
                DB::table('tblsupplier_classifications')->truncate();
                $this->supplier_classModule();
                break;

            case 'supplier':
                DB::table('tblsuppliers')->truncate();
                $this->supplierModule();
                break;

            case 'unit-issue':
                DB::table('tblunit_issue')->truncate();
                $this->unitIssueModule();
                break;

            case 'pr':
                DB::table('tblpr')->truncate();
                DB::table('tblpr_items')->truncate();
                $this->prModule();
                break;

            case 'rfq':
                DB::table('tblcanvass')->truncate();
                $this->rfqModule();
                break;

            case 'abstract':
                DB::table('tblabstract')->truncate();
                DB::table('tblabstract_items')->truncate();
                $this->abstractModule();
                break;

            case 'po-jo':
                DB::table('tblpo_jo')->truncate();
                DB::table('tblpo_jo_items')->truncate();
                $this->po_joModule();
                break;

            case 'ors-burs':
                DB::table('tblors_burs')->truncate();
                $this->ors_bursModule();
                break;

            case 'iar':
                DB::table('tbliar')->truncate();
                $this->iarModule();
                break;

            case 'dv':
                DB::table('tbldv')->truncate();
                $this->dvModule();
                break;

            case 'stock':
                DB::table('tblinventory_stocks')->truncate();
                DB::table('tblinventory_stocks_issue')->truncate();
                $this->stockModule();
                break;

            case 'temp':
                $servername = $request['servername'];
                $username = $request['username'];
                $password = $request['password'];
                $link = $this->connectMySQL($servername, $username, $password);
                $sql = "DROP DATABASE temp_dbpis";

                if (mysqli_query($link, $sql)) {
                    echo "[" . date('H:i:s') . "] : " . "Database deleted successfully\n";
                }

                break;

            default:
                break;
        }

    }

    private function getEquivalentStatus($_status) {
        $_status = strtolower($_status);
        $status = 0;

        switch ($_status) {
            case 'pending':
                $status = 1;
                break;

            case 'disapproved':
                $status = 2;
                break;

            case 'cancelled':
                $status = 3;
                break;

            case 'closed':
                $status = 4;
                break;

            case 'for_canvass':
                $status = 5;
                break;

            case 'for_po':
                $status = 6;
                break;

            case 'obligated':
                $status = 7;
                break;

            case 'for_delivery':
                $status = 8;
                break;

            case 'for_inspection':
                $status = 9;
                break;

            case 'for_disbursement':
                $status = 10;
                break;

            case 'for_payment':
                $status = 11;
                break;

            case 'recorded':
                $status = 12;
                break;

            case 'issued':
                $status = 13;
                break;

            case 'condemn':
                $status = 14;
                break;

            default:
                $status = 1;
                break;
        }

        return $status;
    }

    //---------------------- Library Module ----------------------

    private function employeeModule() {
        $pisAccount = DB::connection('dbpis')
                        ->table('tblemp_accounts')
                        ->get();

        DB::table('tblemp_accounts')->insert([
            'emp_id' => 'MIS-1',
            'division_id' => 1,
            'province_id' => 3,
            'region_id' => 2,
            'firstname' => 'MIS',
            'middlename' => '-',
            'lastname' => 'Super Admin',
            'gender' => 'male',
            'position' => 'MIS',
            'username' => 'mis',
            'email' => 'dostcar.mis@gmail.com',
            'password' => bcrypt('car007'),
            'address' => 'km6',
            'mobile_no' => '+639000000000',
            'role' => 1
        ]);

        foreach ($pisAccount as $emp) {
            $lastname = preg_replace('/\s+/', '', $emp->lastname);
            $lastname = strtolower($lastname);
            //$password = password_hash($emp->password, PASSWORD_DEFAULT);
            $firstname = preg_replace('/\s+/', '', $emp->firstname);
            $firstname = strtolower($firstname);
            $email = $firstname . $lastname . "@gmail.com";

            switch ($emp->user_type) {
                case 'admin':
                    $emp->user_type = 1;
                    break;

                case 'pstd':
                    $emp->user_type = 5;
                    break;

                case 'staff':
                    $emp->user_type = 6;
                    break;

                default:
                    # code...
                    break;
            }

            DB::table('tblemp_accounts')->insert([
                'emp_id' => $emp->empID,
                'division_id' => $emp->sectionID,
                'province_id' => 4,
                'region_id' => 2,
                'firstname' => $emp->firstname,
                'middlename' => $emp->middlename,
                'lastname' => $emp->lastname,
                'position' => $emp->position,
                'username' => $emp->username,
                'email' => $email,
                'password' => bcrypt($lastname),
                'address' => '',
                'mobile_no' => '0999999999',
                'role' => $emp->user_type,
                'active' => 'y'
            ]);
        }
    }

    private function signatoriesModule() {
        $signatory = [];
        $pisSignatories = DB::connection('dbpis')
                            ->table('tblsignatories as sig')
                            ->orderBy('sig.name')
                            ->get();

        foreach ($pisSignatories as $key => $sig) {
            $name = explode(' ', $sig->name);
            $countName = count($name);
            $firstname = "";
            $lastname = "";

            if ($countName == 2) {
                $firstname = $name[0];
                $lastname = $name[1];
            } else if ($countName == 3) {
                $firstname = $name[0];
                $lastname = $name[2];
            } else if ($countName == 4) {
                $firstname = $name[0] . " " . $name[1];
                $lastname = $name[3];
            } else {
                $firstname = $name[0] . " " . $name[1];
                $lastname = $name[$countName - 1];
            }

            $firstname = strtolower($firstname);
            $lastname = strtolower($lastname);
            $pisEmpAccount = DB::connection('dbpis')
                            ->table('tblemp_accounts as emp')
                            ->where([
                                ['emp.firstname', 'LIKE', '%' . $firstname . '%'],
                                ['emp.lastname', 'LIKE', '%' . $lastname . '%'],
                            ])->first();

            /*
            $likeValues = [$firstname, $lastname];
            $pisEmpAccount = DB::connection('dbpis')
                            ->table('tblemp_accounts as emp')
                            ->where(function ($query) use ($likeValues) {
                                    $query->where('emp.firstname', 'LIKE', '%' . $likeValues[0] . '%')
                                          ->orWhere('emp.lastname', 'LIKE', '%' . $likeValues[1] . '%');
                                })
                            ->first();*/

            if ($pisEmpAccount) {
                $signatory[] = (object)['signatoryID' => $sig->signatoryID,
                                        'empID' => $pisEmpAccount->empID,
                                        'position' => $sig->position,
                                        'p_req' => $sig->p_req,
                                        'rfq' => $sig->rfq,
                                        'abs' => $sig->abs,
                                        'ors' => $sig->ors,
                                        'iar' => $sig->iar,
                                        'dv' => $sig->dv,
                                        'ris' => $sig->ris,
                                        'par' => $sig->par,
                                        'ics' => $sig->ics];
            }
        }

        foreach ($signatory as $sig) {
            DB::table('tblsignatories')->insert([
                'id' => $sig->signatoryID,
                'emp_id' => $sig->empID,
                'position' => $sig->position,
                'p_req' => $sig->p_req,
                'rfq' => $sig->rfq,
                'abs' => $sig->abs,
                'ors' => $sig->ors,
                'iar' => $sig->iar,
                'dv' => $sig->dv,
                'ris' => $sig->ris,
                'par' => $sig->par,
                'ics' => $sig->ics
            ]);
        }
    }

    private function supplier_classModule() {
        $pisClassifications = DB::connection('dbpis')
                                ->table('tblclassifications')
                                ->get();

        foreach ($pisClassifications as $class) {
            DB::table('tblsupplier_classifications')->insert([
                'id' => $class->classID,
                'classification' => $class->classification
            ]);
        }
    }

    private function supplierModule() {
        $pisSuppliers = DB::connection('dbpis')
                          ->table('tblbidders')
                          ->get();

        foreach ($pisSuppliers as $supplier) {
            if (!empty($supplier->establishedDate)) {
                if (strpos($supplier->establishedDate, '/') !== false) {
                    $supplier->establishedDate = DB::Raw("STR_TO_DATE('" . $supplier->establishedDate . "', '%m/%d/%Y')");
                } else if (strpos($supplier->establishedDate, '-') !== false) {
                    $supplier->establishedDate = DB::Raw("STR_TO_DATE('" . $supplier->establishedDate . "', '%m-%d-%Y')");
                } else {
                    $supplier->establishedDate = NULL;
                }
            } else {
                $supplier->establishedDate = NULL;
            }

            if (!empty($supplier->fileDate)) {
                if (strpos($supplier->fileDate, '/') !== false) {
                    $supplier->fileDate = DB::Raw("STR_TO_DATE('" . $supplier->fileDate . "', '%m/%d/%Y')");
                } else if (strpos($supplier->establishedDate, '-') !== false) {
                    $supplier->fileDate = DB::Raw("STR_TO_DATE('" . $supplier->fileDate . "', '%m-%d-%Y')");
                } else {
                    $supplier->fileDate = NULL;
                }
            } else {
                $supplier->fileDate = NULL;
            }

            if (empty($supplier->deliveryVehicleNo)) {
                $supplier->deliveryVehicleNo = 0;
            }

            switch (strtolower($supplier->natureBusiness)) {
                case 'manufacturer':
                    $supplier->natureBusiness = "manufacturer";
                    break;

                case 'trading firm':
                    $supplier->natureBusiness = "trading_firms";
                    break;

                case 'service contractor':
                    $supplier->natureBusiness = "service_contractor";
                    break;

                case 'others: (pls. specify)':
                    $supplier->natureBusiness = "others";
                    break;

                default:
                    $supplier->natureBusiness = "";
                    break;
            }

            switch ($supplier->creditAccomodation) {
                case '91-DAYS AND ABOVE':
                    $supplier->creditAccomodation = "90-days_above";
                    break;

                case '90-DAYS':
                    $supplier->creditAccomodation = "90-days_above";
                    break;

                case '60-DAYS':
                    $supplier->creditAccomodation = "60-days";
                    break;

                case '30-DAYS':
                    $supplier->creditAccomodation = "30-days";
                    break;

                case '90-DAYS AND BELOW':
                    $supplier->creditAccomodation = "below-15-days";
                    break;

                default:
                    $supplier->creditAccomodation = "";
                    break;
            }

            switch ($supplier->attachement) {
                case 'Latest Financial Statement':
                    $supplier->attachement = "1";
                    break;

                case 'DTI/SEC Registration':
                    $supplier->attachement = "2";
                    break;

                case "Valid and Current Mayor's Permit/Municipal License":
                    $supplier->attachement = "3";
                    break;

                case 'VAT Registration Certificate':
                    $supplier->attachement = "4";
                    break;

                case 'Articles of Incorporation, Partnership or Cooperation, Valid joint venture Agreement whichever is applicable':
                    $supplier->attachement = "5";
                    break;

                case 'Certificate of PhilGEPS Registration':
                    $supplier->attachement = "6";
                    break;

                case 'Others, Specify':
                    $supplier->attachement = "7";
                    break;

                default:
                    $supplier->attachement = "";
                    break;
            }

            DB::table('tblsuppliers')->insert([
                'id' => $supplier->bidderID,
                'class_id' => $supplier->classID,
                'company_name' => $supplier->company_name,
                'address' => $supplier->address,
                'contact_person' => $supplier->contact_person,
                'mobile_no' => $supplier->contact_no,
                'date_established' => $supplier->establishedDate,
                'date_file' => $supplier->fileDate,
                'email' => $supplier->emailAddress,
                'url_address' => $supplier->urlAddress,
                'fax_no' => $supplier->faxNo,
                'vat_no' => $supplier->vatNo,
                'name_bank' => $supplier->nameBank,
                'account_name' => $supplier->accountName,
                'account_no' => $supplier->accountNo,
                'nature_business' => $supplier->natureBusiness,
                'nature_business_others' => $supplier->natureBusinessOthers,
                'delivery_vehicle_no' => $supplier->deliveryVehicleNo,
                'product_lines' => $supplier->productLines,
                'credit_accomodation' => $supplier->creditAccomodation,
                'attachment' => $supplier->attachement,
                'attachment_others' => $supplier->attachmentOthers
            ]);
        }
    }

    private function unitIssueModule() {
        $pisUnitIssue = DB::connection('dbpis')
                          ->table('tblunit_issue')
                          ->get();

        foreach ($pisUnitIssue as $unit) {
            DB::table('tblunit_issue')->insert([
                'unit' => $unit->unitName
            ]);
        }
    }

    //---------------------- Main Module ----------------------

    private function prModule() {
        $pisPR = DB::connection('dbpis')
                   ->table('tblpr')
                   ->get();
        $pisPRItems = DB::connection('dbpis')
                   ->table('tblpr_info')
                   ->get();

        foreach ($pisPR as $pr) {
            $prDate = strtotime($pr->prDate);

            if (!empty($pr->prApprovalDate)) {
                $pr->prApprovalDate = DB::Raw("STR_TO_DATE('" . $pr->prApprovalDate . "', '%m/%d/%Y')");
            }

            $pr->prStatus = $this->getEquivalentStatus($pr->prStatus);

            if (!empty($pr->prNo)) {
                DB::table('tblpr')->insert([
                    'id' => $pr->prID,
                    'pr_no' => $pr->prNo,
                    'date_pr' => DB::Raw("STR_TO_DATE('" . $pr->prDate . "', '%m/%d/%Y')"),
                    'date_pr_approve' => $pr->prApprovalDate,
                    'project_id' => 1,
                    'requested_by' => $pr->requestBy,
                    'pr_division_id' => 1,
                    'approved_by' => $pr->signatory,
                    'sig_app' => 53,
                    'sig_funds_available' => 24,
                    'remarks' => $pr->remarks,
                    'purpose' => $pr->purpose,
                    'status' => $pr->prStatus
                ]);

                $this->createTracker('PR', $pr->prID, $pr->prNo);

                if ($pr->prStatus >= 5) {
                    $code = $this->getDocCode($pr->prID, 'PR');
                    $date = DB::Raw("STR_TO_DATE('" . $pr->prDate . "', '%m/%d/%Y')");
                    $this->trackerHistory($code, $pr->requestBy, 0, 'document_generated', $date);
                    $this->trackerHistory($code, $pr->requestBy, 0, 'issued', $date);
                }
            }
        }

        foreach ($pisPRItems as $pr) {
            if (empty($pr->unitIssue) || $pr->unitIssue == 'NULL' || $pr->unitIssue == NULL) {
                $unitID = 0;
            } else {
                $unit = DB::table('tblunit_issue')
                          ->where('unit', 'LIKE', '%'.$pr->unitIssue.'%')
                          ->first();

                $unitID = $unit->id;
            }

            if (empty($pr->groupNo)) {
                $pr->groupNo = 0;
            }

            if ($pr->awardedTo == 'NULL') {
                $pr->awardedTo = NULL;
            }

            if ($unitID == 8) {
                $docType = "JO";
            } else {
                $docType = "PO";
            }

            if (!empty($pr->estimateUnitCost) || !empty($pr->estimateTotalCost)) {
                DB::table('tblpr_items')->insert([
                    'item_id' => $pr->infoID,
                    'pr_id' => $pr->prID,
                    'quantity' => $pr->quantity,
                    'unit_issue' => $unitID,
                    'item_description' => $pr->itemDescription,
                    'est_unit_cost' => $pr->estimateUnitCost,
                    'est_total_cost' => $pr->estimateTotalCost,
                    'awarded_to' => $pr->awardedTo,
                    'awarded_remarks' => $pr->awardedRemarks,
                    'group_no' => $pr->groupNo,
                    'document_type' => $docType
                ]);
            }
        }
    }

    private function rfqModule() {
        $pisPR = DB::connection('dbpis')
                   ->table('tblpr')
                   ->get();

        foreach ($pisPR as $pr) {
            if (!empty($pr->prNo)) {
                if (!empty($pr->canvassDate)) {
                    $pr->canvassDate = DB::Raw("STR_TO_DATE('" . $pr->canvassDate . "', '%m/%d/%Y')");
                } else {
                    $pr->canvassDate = NULL;
                }

                $pr->prStatus = $this->getEquivalentStatus($pr->prStatus);

                if ($pr->prStatus >= 5) {
                    DB::table('tblcanvass')->insert([
                        'pr_id' => $pr->prID,
                        'date_canvass' => $pr->canvassDate,
                        'sig_rfq' => 53
                    ]);

                    $this->createTracker('RFQ', $pr->prID, $pr->prNo);

                    if (!empty($pr->canvassDate)) {
                        $countAbstract = DB::connection('dbpis')
                                           ->table('tblbids_quotations')
                                           ->where('prID', $pr->prID)
                                           ->count();
                        $code = $this->getDocCode($pr->prID, 'RFQ');

                        if (empty($pr->prApprovalDate)) {
                            $pr->prApprovalDate = Carbon::now();
                        } else {
                            $pr->prApprovalDate = DB::Raw("STR_TO_DATE('" . $pr->prApprovalDate . "', '%m/%d/%Y')");
                        }

                        $this->trackerHistory($code, 'D-0903', 0, 'document_generated', $pr->prApprovalDate);
                        $this->trackerHistory($code, 'D-0903', $pr->requestBy, 'issued', $pr->prApprovalDate);

                        if ($countAbstract > 0) {
                            /*
                            if (empty($pr->canvassDate)) {
                                $pr->canvassDate = Carbon::now();
                            }*/

                            $this->trackerHistory($code, 'D-0903', 0, 'received', $pr->prApprovalDate);
                        }
                    }
                }
            }
        }
    }

    private function abstractModule() {
        $pisPR = DB::connection('dbpis')
                   ->table('tblpr')
                   ->get();

        foreach ($pisPR as $pr) {
            if (!empty($pr->prNo)) {
                $countPO = DB::connection('dbpis')
                        ->table('tblpo_jo')
                        ->where('prID', $pr->prID)
                        ->count();

                if ($countPO > 0) {
                    $pisPR_Items = DB::connection('dbpis')
                                     ->table('tblpr_info')
                                     ->where('prID', $pr->prID)
                                     ->get();

                    if (!empty($pr->abstractDate)) {
                        $pr->abstractDate = DB::Raw("STR_TO_DATE('" . $pr->abstractDate . "', '%m/%d/%Y')");
                    } else {
                        $pr->abstractDate = NULL;
                    }

                    if (!empty($pr->abstractApprovalDate)) {
                        $pr->abstractApprovalDate = DB::Raw("STR_TO_DATE('" . $pr->abstractApprovalDate . "', '%m/%d/%Y')");
                    } else {
                        $pr->abstractApprovalDate = NULL;
                    }

                    $pr->prStatus = $this->getEquivalentStatus($pr->prStatus);

                    if (!empty($pr->prNo)) {
                        if ($pr->procurementMode == 'Canvass') {
                            $pr->procurementMode = "Alternative";
                        }

                        $modeProcurement = DB::table('tblmode_procurement')
                                             ->where('mode', 'LIKE', '%'. $pr->procurementMode .'%')
                                             ->first();

                        DB::table('tblabstract')->insert([
                            'pr_id' => $pr->prID,
                            'date_abstract' => $pr->abstractDate,
                            'date_abstract_approve' => $pr->abstractApprovalDate,
                            'mode_procurement_id' => $modeProcurement->id]
                        );

                        $this->createTracker('ABSTRACT', $pr->prID, $pr->prNo);

                        if ($pr->prStatus > 5) {
                            $code = $this->getDocCode($pr->prID, 'ABSTRACT');
                            $countPO = DB::connection('dbpis')
                                         ->table('tblpo_jo')
                                         ->where('prID', $pr->prID)
                                         ->count();

                            if ($countPO > 0) {
                                if (!empty($pr->abstractDate)) {
                                    $date = $pr->abstractDate;
                                }

                                if (!empty($pr->abstractApprovalDate)) {
                                    $date = $pr->abstractApprovalDate;
                                } else {
                                    $date = Carbon::now();
                                }

                                $this->trackerHistory($code, 'D-0903', 0, 'document_generated', $date);
                                $this->trackerHistory($code, 'D-0903', 0, 'issued', $date);
                            }
                        }
                    }

                    foreach ($pisPR_Items as $item) {
                        $abstractItem = DB::connection('dbpis')
                                          ->table('tblbids_quotations')
                                          ->where('infoID', $item->infoID)
                                          ->get();

                        foreach ($abstractItem as $count => $abs) {
                            $abstractID = $item->infoID . '-' . ($count + 1);

                            DB::table('tblabstract_items')->insert(
                                ['abstract_id' => $abstractID,
                                 'pr_id' => $pr->prID,
                                 'supplier_id' => $abs->bidderID,
                                 'pr_item_id' => $abs->infoID,
                                 'remarks' => $abs->remarks,
                                 'unit_cost' => $abs->amount,
                                 'total_cost' => $abs->lamount]
                            );
                        }
                    }
                }
            }
        }
    }

    private function po_joModule() {
        $pisPO_JO = DB::connection('dbpis')
                      ->table('tblpo_jo as po')
                      ->join('tblpr as pr', 'pr.prID', '=', 'po.prID')
                      ->get();

        foreach ($pisPO_JO as $po) {
            $withORSBURS = 'n';
            $countORS = DB::connection('dbpis')
                          ->table('tblors')
                          ->where('poNo', $po->poNo)
                          ->count();

            if ($countORS > 0) {
                $withORSBURS = 'y';
            }

            if (!empty($po->poDate)) {
                $po->poDate = DB::Raw("STR_TO_DATE('" . $po->poDate . "', '%m/%d/%Y')");
            } else {
                $po->poDate = NULL;
            }

            if (!empty($po->poApprovalDate)) {
                $po->poApprovalDate = DB::Raw("STR_TO_DATE('" . $po->poApprovalDate . "', '%m/%d/%Y')");
            } else {
                $po->poApprovalDate = NULL;
            }

            $po->poStatus = $this->getEquivalentStatus($po->poStatus);
            $documentType = [];
            $countPO = DB::connection('dbpis')
                         ->table('tblpr_info')
                         ->where([['prID', $po->prID],
                                  ['awardedTo', $po->awardedTo],
                                  ['unitIssue', '<>', 'J.O.']
                                 ])
                         ->count();
            $countJO = DB::connection('dbpis')
                         ->table('tblpr_info')
                         ->where([['prID', $po->prID],
                                  ['awardedTo', $po->awardedTo],
                                  ['unitIssue', 'J.O.']
                                 ])
                         ->count();

            if ($countPO && !$countJO) {
                $documentType = ['PO'];
            } elseif (!$countPO && $countJO) {
                $documentType = ['JO'];
            } elseif ($countPO && $countJO) {
                $documentType = ['PO', 'JO'];
            }

            foreach ($documentType as $key => $docType) {
                $countDocType = count($documentType);

                if (!empty($po->signatoryDept)) {
                    $empAccount = DB::connection('dbpis')
                                    ->table('tblemp_accounts')
                                    ->where('empID', $po->signatoryDept)
                                    ->first();
                    if (!empty($empAccount)) {
                        $signatoryDept = DB::connection('dbpis')
                                           ->table('tblsignatories')
                                           ->where('name', 'LIKE' , '%'. strtolower($empAccount->firstname) . '%')
                                           ->first();

                        if (!empty($signatoryDept->signatoryID)) {
                            $sigDeptID = $signatoryDept->signatoryID;
                        } else {
                            $sigDeptID = NULL;
                        }
                    } else {
                        $sigDeptID = NULL;
                    }
                } else {
                    $sigDeptID = NULL;
                }

                if ($countDocType > 1) {
                    $poNo = $po->poNo . $key;
                } else {
                    $poNo = $po->poNo;
                }

                DB::table('tblpo_jo')->insert(
                        ['po_no' => $poNo,
                         'pr_id' => $po->prID,
                         'date_po' => $po->poDate,
                         'date_po_approved' => $po->poApprovalDate,
                         //'mode_procurement_id' => $modeProcurement->id,
                         'awarded_to' => $po->awardedTo,
                         'place_delivery' => $po->placeDelivery,
                         'date_delivery' => $po->deliveryDate,
                         'delivery_term' => $po->deliveryTerm,
                         'payment_term' => $po->paymentTerm,
                         'amount_words' => $po->amountWords,
                         'grand_total' => $po->totalAmount,
                         'sig_department' => $sigDeptID,
                         'sig_approval' => $po->signatoryApp,
                         'sig_funds_available' => $po->signatoryFunds,
                         'for_approval' => $po->forApproval,
                         'with_ors_burs' => $withORSBURS,
                         'document_abrv' => $docType,
                         'status' => $po->poStatus]
                    );

                $this->createTracker($docType, $poNo, $poNo);

                if (!empty($po->poDate) && !empty($po->poApprovalDate)) {
                    $code = $this->getDocCode($poNo, $docType);
                    $prRequestBy = DB::connection('dbpis')
                                     ->table('tblpr')
                                     ->where('prID', $po->prID)
                                     ->first();
                    $this->trackerHistory($code, 'D-0903', 0, 'document_generated', $po->poApprovalDate);
                    $this->trackerHistory($code, 'D-0903', $prRequestBy->requestBy, 'issued', $po->poApprovalDate);
                    $this->trackerHistory($code, 'D-0903', 0, 'received', $po->poApprovalDate);
                }

                $poItems = DB::connection('dbpis')
                             ->table('tblpo_jo_items')
                             ->where('poNo', $poNo)
                             ->get();

                foreach ($poItems as $item) {
                    $totalCost = $item->quantity * $item->amount;

                    if (empty($item->unitIssue) || $item->unitIssue == 'NULL' || $item->unitIssue == NULL) {
                        $unitID = 0;
                    } else {
                        $unit = DB::table('tblunit_issue')
                                  ->where('unit', 'LIKE', '%'.$item->unitIssue.'%')
                                  ->first();

                        $unitID = $unit->id;
                    }

                    if (empty($item->infoID)) {
                        $item->infoID = $item->id;
                    }

                    DB::table('tblpo_jo_items')->insert(
                            ['item_id' => $item->id,
                             'po_no' => $item->poNo,
                             'pr_id' => $item->prID,
                             'quantity' => $item->quantity,
                             'unit_issue' => $unitID,
                             'item_description' => $item->itemDescription,
                             'unit_cost' => $item->amount,
                             'total_cost' => $totalCost]
                        );
                }
            }
        }
    }

    private function ors_bursModule() {
        $pisORS_BURS = DB::connection('dbpis')
                         ->table('tblors as ors')
                         ->join('tblpo_jo as po', 'po.poNo', '=', 'ors.poNo')
                         ->get();

        foreach ($pisORS_BURS as $ors) {
            if (!empty($ors->orsDate)) {
                $ors->orsDate = DB::Raw("STR_TO_DATE('" . $ors->orsDate . "', '%m/%d/%Y')");
            } else {
                $ors->orsDate = NULL;
            }

            if (!empty($ors->signatoryReqDate)) {
                $ors->signatoryReqDate = DB::Raw("STR_TO_DATE('" . $ors->signatoryReqDate . "', '%m/%d/%Y')");
            } else {
                $ors->signatoryReqDate = NULL;
            }

            if (!empty($ors->signatoryBudgetDate)) {
                $ors->signatoryBudgetDate = DB::Raw("STR_TO_DATE('" . $ors->signatoryBudgetDate . "', '%m/%d/%Y')");
            } else {
                $ors->signatoryBudgetDate = NULL;
            }

            $ors->poStatus = $this->getEquivalentStatus($ors->poStatus);

            if ($ors->poStatus >= 7) {
                $dateObligated = $ors->orsDate;
            } else {
                $dateObligated = NULL;
            }

            DB::table('tblors_burs')->insert(
                    ['id' => $ors->id,
                     'pr_id' => $ors->prID,
                     'po_no' => $ors->poNo,
                     'pr_id' => $ors->prID,
                     'serial_no' => $ors->orsNo,
                     'date_ors_burs' => $ors->orsDate,
                     'date_obligated' => $dateObligated,
                     'payee' => $ors->payee,
                     'office' => $ors->office,
                     'address' => $ors->address,
                     'responsibility_center' => '19 001 03000 14',
                     'particulars' => $ors->particulars,
                     'mfo_pap' => "3-Regional Office\nA.III.c.1\nA.III.b.1\nA.III.c.2",
                     'uacs_object_code' => $ors->uacsObjectCode,
                     'amount' => $ors->amount,
                     'sig_certified_1' => $ors->signatoryReq,
                     'sig_certified_2' => $ors->signatoryBudget,
                     'sig_accounting' => $ors->signatoryFunds,
                     'sig_agency_head' => $ors->signatoryApp,
                     'date_certified_1' => $ors->signatoryReqDate,
                     'date_certified_2' => $ors->signatoryBudgetDate,
                     'module_class_id' => 3,
                     'document_type' => strtoupper($ors->documentType)]
                );

            $orsData = DB::table('tblors_burs')->where('po_no', $ors->poNo)->first();

            $this->createTracker(strtoupper($ors->documentType), $orsData->id, $ors->poNo);

            if ($ors->particulars == '...' || empty($ors->particulars)) {

            } else {
                $code = $this->getDocCode($orsData->id, strtoupper($ors->documentType));
                $origDateObligated = $dateObligated;

                if (empty($dateObligated)) {
                    $dateObligated = Carbon::now();
                }

                $this->trackerHistory($code, 'D-0903', 0, 'document_generated', $dateObligated);
                $this->trackerHistory($code, 'D-0903', 0, 'issued', $dateObligated);

                if (!empty($origDateObligated)) {
                    $this->trackerHistory($code, 'S-0730', 0, 'received', $origDateObligated);
                }
            }
        }
    }

    private function iarModule() {
        $pisIAR = DB::connection('dbpis')
                    ->table('tbliar as iar')
                    ->join('tblors as ors', 'ors.id', '=', 'iar.orsID')
                    ->get();

        foreach ($pisIAR as $iar) {
            $inventoryCount = DB::connection('dbpis')
                                ->table('tblpo_jo as po')
                                ->join('tbliar as iar', 'iar.iarNo', 'LIKE', DB::RAW('CONCAT("%",po.poNo,"%")'))
                                ->join('tblpo_jo_items as item', 'item.poNo', '=', 'po.poNo')
                                ->join('tblinventory_items as inv', 'inv.poItemID', '=', 'item.id')
                                ->where('iar.iarNo', $iar->iarNo)
                                ->count();

            if (!empty($iar->iarDate)) {
                $iar->iarDate = DB::Raw("STR_TO_DATE('" . $iar->iarDate . "', '%m/%d/%Y')");
            } else {
                $iar->iarDate = NULL;
            }

            if (!empty($iar->invoiceDate)) {
                $iar->invoiceDate = DB::Raw("STR_TO_DATE('" . $iar->invoiceDate . "', '%m/%d/%Y')");
            } else {
                $iar->invoiceDate = NULL;
            }

            DB::table('tbliar')->insert(
                    ['iar_no' => $iar->iarNo,
                     'pr_id' => $iar->prID,
                     'ors_id' => $iar->orsID,
                     'date_iar' => $iar->iarDate,
                     'invoice_no' => $iar->invoiceNo,
                     'date_invoice' => $iar->invoiceDate,
                     'sig_inspection' => $iar->inspectedBy,
                     'sig_supply' => $iar->signatorySupply]
                );

            $this->createTracker('IAR', $iar->iarNo, $iar->poNo);

            if (!empty($iar->iarDate) || $inventoryCount > 0) {
                $code = $this->getDocCode($iar->iarNo, 'IAR');

                if (empty($iar->iarDate)) {
                    $iar->iarDate = Carbon::now();
                }

                $this->trackerHistory($code, 'D-0903', 0, 'document_generated', $iar->iarDate);
                $this->trackerHistory($code, 'D-0903', 0, 'issued', $iar->iarDate);
                $this->trackerHistory($code, 'D-0903', 0, 'received', $iar->iarDate);
            }
        }
    }

    private function dvModule() {
        $pisDV = DB::connection('dbpis')
                        ->table('tbldv as dv')
                        ->select('dv.id', 'dv.prID', 'dv.orsID', 'dv.dvNo', 'dv.dvDate',
                                 'dv.paymentMode', 'dv.particulars', 'po.signatoryFunds',
                                 'po.signatoryApp', 'po.poNo')
                        ->join('tblors as ors', 'ors.id', '=', 'dv.orsID')
                        ->join('tblpo_jo as po', 'po.poNo', '=', 'ors.poNo')
                        ->get();

        foreach ($pisDV as $dv) {
            if (!empty($dv->dvDate)) {
                $dv->dvDate = DB::Raw("STR_TO_DATE('" . $dv->dvDate . "', '%m/%d/%Y')");
            } else {
                $dv->dvDate = NULL;
            }

            DB::table('tbldv')->insert(
                    ['id' => $dv->id,
                     'pr_id' => $dv->prID,
                     'ors_id' => $dv->orsID,
                     'dv_no' => $dv->dvNo,
                     'date_dv' => $dv->dvDate,
                     'payment_mode' => $dv->paymentMode,
                     'particulars' => $dv->particulars,
                     'sig_accounting' => $dv->signatoryFunds,
                     'sig_agency_head' => $dv->signatoryApp,
                     'module_class_id' => 3]
                );

            $dvData = DB::table('tbldv')->where('ors_id', $dv->orsID)->first();

            $this->createTracker('DV', $dvData->id, $dv->poNo);

            if ($dv->particulars == '...' || empty($dv->particulars)) {

            } else {
                $code = $this->getDocCode($dvData->id, 'DV');

                if (empty($dv->dvDate)) {
                    $dv->dvDate = Carbon::now();
                }

                $this->trackerHistory($code, 'D-0903', 0, 'document_generated', $dv->dvDate);
                $this->trackerHistory($code, 'D-0903', 0, 'issued', $dv->dvDate);
                $this->trackerHistory($code, 'C-1108', 0, 'received', $dv->dvDate);
            }
        }
    }

    private function stockModule() {
        $pisStock = DB::connection('dbpis')
                      ->table('tblinventory_items as inv')
                      ->select('inv.*', 'pr.purpose', 'pr.purpose', 'pr.requestBy',
                               'pr.sectionID', 'po.poNo', 'ors.office')
                      ->join('tblpo_jo_items as item', 'item.id', '=', 'inv.poItemID')
                      ->join('tblpo_jo as po', 'po.poNo', '=', 'item.poNo')
                      ->join('tblpr as pr', 'pr.prID', '=', 'po.prID')
                      ->join('tblors as ors', 'ors.poNo', '=', 'po.poNo')
                      //->whereNotNull('pr.sectionID')
                      ->orderBy('inv.id')
                      ->get();

        foreach ($pisStock as $stock) {
            $pisStock_Issue = DB::connection('dbpis')
                                ->table('tblitem_issue')
                                ->where('inventoryID', $stock->id)
                                ->get();

            if ($stock->inventoryClass == 'par') {
                $stock->inventoryClass = 1;
            } else if ($stock->inventoryClass == 'ris') {
                $stock->inventoryClass = 2;
            } else if ($stock->inventoryClass == 'ics') {
                $stock->inventoryClass = 3;
            }

            if (empty($stock->stockAvailable)) {
                $stock->stockAvailable = "y";
            } else {
                if ($stock->stockAvailable == 'yes') {
                    $stock->stockAvailable = "y";
                } else {
                    $stock->stockAvailable = "n";
                }
            }

            $stock->itemStatus = $this->getEquivalentStatus($stock->itemStatus);

            DB::table('tblinventory_stocks')->insert(
                ['id' => $stock->id,
                 'pr_id' => $stock->prID,
                 'po_item_id' => $stock->poItemID,
                 'po_no' => $stock->poNo,
                 'inventory_no' => $stock->inventoryClassNo,
                 'property_no' => $stock->propertyNo,
                 'inventory_class_id' => $stock->inventoryClass,
                 'item_class_id' => $stock->itemClassification,
                 'requested_by' => $stock->requestBy,
                 'office' => $stock->office,
                 'division_id' => $stock->sectionID,
                 'purpose' => $stock->purpose,
                 'stock_available' => $stock->stockAvailable,
                 'est_useful_life' => $stock->estimatedUsefulLife,
                 'status' => $stock->itemStatus,
                 'group_no' => $stock->groupNo]
            );

            foreach ($pisStock_Issue as $issue) {
                if (!empty($issue->issueDate)) {
                    $issue->issueDate = DB::Raw("STR_TO_DATE('" . $issue->issueDate . "', '%m/%d/%Y')");
                } else {
                    $issue->issueDate = NULL;
                }

                DB::table('tblinventory_stocks_issue')->insert(
                    ['pr_id' => $stock->prID,
                     'inventory_id' => $issue->inventoryID,
                     'serial_no' => $issue->serialNo,
                     'quantity' => $issue->quantity,
                     'received_by' => $issue->empID,
                     'issued_by' => $issue->issuedBy,
                     'date_issued' => $issue->issueDate,
                     'issued_remarks' => $issue->issueRemarks,
                     'approved_by' => $issue->approvedBy]
                );
            }
        }
    }

    private function getDocCode($id, $documentType) {
        $code = DB::table('tbldocument_logs')
                  ->select('code')
                  ->where([
                        ['primary_id', $id],
                        ['document_type', $documentType]
                    ])
                  ->first();

        return $code->code;
    }

    private function createTracker($documentType, $primaryID, $appendCode) {
        $code = "";
        $doc = new DocumentLog;
        $code =  $documentType . "-" . $appendCode . "-3-" . date('mdY');

        $doc->code = $code;
        $doc->primary_id = $primaryID;
        $doc->document_type = $documentType;
        $doc->module_class_id = 3;

        $doc->save();
    }

    private function trackerHistory($code, $empFrom, $empTo, $action, $date, $remarks = "") {
        $docHistory = new DocumentLogHistory;
        $docHistory->code = $code;
        $docHistory->date = $date;
        $docHistory->emp_from = $empFrom;
        $docHistory->emp_to = $empTo;
        $docHistory->action = $action;
        $docHistory->remarks = $remarks;
        $docHistory->save();
    }
}
