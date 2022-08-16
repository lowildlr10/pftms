<?php

namespace App\Plugins\PDFGenerator;

class DocListDueDemandable extends PDF {
    public function printLDDAP($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->lddap->id;

        $docSubject = "List of Due And Demandable Accounts Payable - Advice to Debit Accounts";

        $lddapDate = date('j F Y', strtotime($data->lddap->date_lddap));
        $department = $data->lddap->department;
        $entityName = $data->lddap->entity_name;
        $operatingUnit = $data->lddap->operating_unit;
        $ncaNo = $data->lddap->nca_no;
        $lddapNo = $data->lddap->lddap_ada_no;
        $fundCluster = $data->lddap->fund_cluster;
        $serialNo = $data->lddap->serial_no;
        $mdsgsbBranch = $data->mds_gsb_branch;
        $mdsgsbSubAccount = $data->mds_gsb_sub_account_no;
        $totalAmountWords = $data->lddap->total_amount_words;
        $totalAmount = number_format($data->lddap->total_amount, 2);

        $certCorrect = $data->sig_cert_correct;
        $certCorrectPosition = $data->sig_cert_correct_position;
        $approval1 = $data->sig_approval_1;
        $approval2 = $data->sig_approval_2;
        $approval3 = $data->sig_approval_3;
        $approvalPosition1 = $data->sig_approval_1_position;
        $approvalPosition2 = $data->sig_approval_2_position;
        $approvalPosition3 = $data->sig_approval_3_position;
        $agencyAuth1 = $data->sig_agency_auth_1;
        $agencyAuth2 = $data->sig_agency_auth_2;
        $agencyAuth3 = $data->sig_agency_auth_3;
        $agencyAuth4 = $data->sig_agency_auth_4;

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(5, 10, 5);
        $this->SetHeaderMargin(10);
        $this->SetFooterMargin(10);
        $this->SetPrintHeader(false);
        //$this->SetPrintFooter(false);
        $this->generateInfoOnly = true;

        //Set auto page breaks
        $this->SetAutoPageBreak(TRUE, 25);

        //Set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //Set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $this->setLanguageArray($l);
        }

        //Set default font subsetting mode
        $this->setFontSubsetting(true);

        /* ------------------------------------- End of Config ------------------------------------- */

        //Add a page
        $this->AddPage();

        /* ------------------------------------- Start of Doc ------------------------------------- */

        $pageWidth_inMargin = $this->getPageWidth() - 10;

        //Title
        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell(0, 8, strtoupper($docSubject . ' (LDDAP-ADA)'), 0, 0, 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.685, 4, '', 0, 0, 'L');
        $this->Cell(0, 4, 'NCA No. ' . $ncaNo, 0, 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.685, 4, 'Department : ' . $department, 0, 0, 'L');
        $this->Cell(0, 4, 'LDDAP-ADA No. ' . $lddapNo, 0, 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.685, 4, 'Entity Name : ' . $entityName, 0, 0, 'L');
        $this->Cell(0, 4, 'Date : ' . $lddapDate, 0, 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.685, 4, 'Operating Unit : ' . $operatingUnit, 0, 0, 'L');
        $this->Cell(0, 4, 'Fund Cluster : ' . $fundCluster, 0, 0, 'L');
        $this->Ln(5);

        $this->Cell($pageWidth * 0.7, 7, 'MDS-GSB BRANCH/MDS SUB ACCOUNT NO.: ' . "$mdsgsbBranch / $mdsgsbSubAccount", 0, 0, 'R');


        $this->SetTextColor(0, 139, 255);
        $this->Cell($pageWidth * 0.032, 7, "", 0, 0, 'C');
        $this->SetFont('Helvetica', '', 11 + ($fontScale * 11));
        $cellBorder = [
            'TBLR' => ['width' => 0.1, 'color' => [221, 221, 221], 'solid' => 0, 'cap' => 'butt']
        ];
        $this->Cell($pageWidth * 0.21, 7, $serialNo, $cellBorder, 0, 'C');
        $this->Cell(0, 7, "", 0, 0, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Ln(8);

        $cellBorder = [
            'TBLR' => ['width' => 0.2, 'color' => [0, 0, 0], 'solid' => 0, 'cap' => 'butt']
        ];

        //Table Data
        $this->Cell(0, 4, 'I. LIST OF DUE AND DEMANDABLE ACCOUNTS PAYABLE (LDDAP)', $cellBorder, 0, 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.422, 4, 'CREDITOR', 'BLR', 0, 'C');
        $this->SetFont('Times', 'B', 6 + ($fontScale * 6));
        $this->Cell($pageWidth * 0.086, 4, 'Obligation', 'R', 0, 'C');
        $this->Cell($pageWidth * 0.083, 4, 'ALLOTMENT', 'R', 0, 'C');
        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.245, 4, 'In Pesos', 'BR', 0, 'C');
        $this->Cell(0, 4, '', 'R', 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.234, 0.5, '', 'LR', 0, 'C', false, '', 1, true);
        $this->SetFont('Times', '', 6 + ($fontScale * 6));
        $this->Cell($pageWidth * 0.188, 0.5, '', 'R', 0, 'L', false, '', 1, true);
        $this->SetFont('Times', 'B', 6 + ($fontScale * 6));
        $this->Cell($pageWidth * 0.086, 2, 'Request and', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * 0.083, 2, 'CLASS per', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 0.5, '', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 0.5, '', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 0.5, '', 'R', 0, 'C', false, '', 1, true);
        $this->Cell(0, 0.5, '', 'R', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->Cell($pageWidth * 0.234, 2, '', 'LR', 0, 'C', false, '', 1, true);
        $this->SetFont('Times', '', 6 + ($fontScale * 6));
        $this->Cell($pageWidth * 0.188, 2, 'PREFERRED SERVICING', 'R', 0, 'L', false, '', 1, true);
        $this->SetFont('Times', 'B', 6 + ($fontScale * 6));
        $this->Cell($pageWidth * 0.086, 2, '', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * 0.083, 2, '', 'R', 0, 'C', false, '', 1, true);
        $this->SetFont('Times', '', 7 + ($fontScale * 7));
        $this->Cell($pageWidth * (0.245/3), 2.5, 'GROSS', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 2.5, 'WITHHOLDI', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 2.5, 'NET', 'R', 0, 'C', false, '', 1, true);
        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell(0, 2, 'REMARKS', 'R', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.234, 2, 'NAME', 'LR', 0, 'C', false, '', 1, true);
        $this->SetFont('Times', '', 6 + ($fontScale * 6));
        $this->Cell($pageWidth * 0.188, 2, 'BANK/SAVINGS/CURRENT ACCOUNT', 'R', 0, 'L', false, '', 1, true);
        $this->SetFont('Times', 'B', 6 + ($fontScale * 6));
        $this->Cell($pageWidth * 0.086, 3, 'Status No.', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * 0.083, 3, '(UACS)', 'R', 0, 'C', false, '', 1, true);
        $this->SetFont('Times', '', 7 + ($fontScale * 7));
        $this->Cell($pageWidth * (0.245/3), 3, 'AMOUNT', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 3, 'NG TAX', 'R', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 3, 'AMOUNT', 'R', 0, 'C', false, '', 1, true);
        $this->Cell(0, 2, '', 'R', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->Cell($pageWidth * 0.234, 2, '', 'BLR', 0, 'C', false, '', 1, true);
        $this->SetFont('Times', '', 6 + ($fontScale * 6));
        $this->Cell($pageWidth * 0.188, 2, 'NO.', 'BR', 0, 'L', false, '', 1, true);
        $this->Cell($pageWidth * 0.086, 2, '', 'BR', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * 0.083, 2, '', 'BR', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 2, '', 'BR', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 2, '', 'BR', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth * (0.245/3), 2, '', 'BR', 0, 'C', false, '', 1, true);
        $this->Cell(0, 2, '', 'BR', 0, 'C', false, '', 1, true);
        $this->Ln();

        //Table Content
        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->htmlTable($data->table_data);

        //Footer
        $this->Cell($pageWidth * 0.508, 4, '', 'L', 0, 'L', false, '', 1, true);
        $this->Cell(0, 4, '', 'R', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.508, 3,
                "\t\t\t\t\t I hereby warrant that the above List of Due and Demandable A/Ps",
                'L', 0, 'L', false, '', 1, true);
        $this->Cell(0, 3,
                "\t\t\t\t\t I hereby assume full responsibility for the veracity and accuracy of the",
                'R', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->Cell($pageWidth * 0.508, 3,
                "was prepared in accordance with existing budgeting, accounting and",
                'L', 0, 'L', false, '', 1, true);
        $this->Cell(0, 3,
                "listed claims, and the authencity of the supporting documents as submitted",
                'R', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->Cell($pageWidth * 0.508, 3,
                "auditing rules and regulations.",
                'L', 0, 'L', false, '', 1, true);
        $this->Cell(0, 3,
                "by the claimants.",
                'R', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->Cell($pageWidth * 0.508, 5, '', 'L', 0, 'L', false, '', 1, true);
        $this->Cell(0, 5, '', 'R', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->Cell($pageWidth * 0.508, 3,
                "\t\t\t\t\tCertified Correct:",
                'L', 0, 'L', false, '', 1, true);
        $this->Cell(0, 3,
                "Approved:",
                'R', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->Cell(0, 8, '', 'LR', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.422, 3, $certCorrect, 'L', '0', 'C', false, '', 1, true);
        $this->Cell($pageWidth * 0.086, 3, '', 0, 0, 'C', false, '', 1, true);
        $this->Cell(($pageWidth_inMargin - ($pageWidth * 0.508)) / 2, 3, $approval1,
                '', 0, 'C', false, '', 1, true);
        $this->Cell(($pageWidth_inMargin - ($pageWidth * 0.508)) / 2, 3, $approval2,
                'R', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.422, 3, $certCorrectPosition, 'L', '0', 'C', false, '', 1, true);
        $this->Cell($pageWidth * 0.086, 3, '', 0, 0, 'C', false, '', 1, true);
        $this->Cell(($pageWidth_inMargin - ($pageWidth * 0.508)) / 2, 3, $approvalPosition1,
                '', 0, 'C', false, '', 1, true);
        $this->Cell(($pageWidth_inMargin - ($pageWidth * 0.508)) / 2, 3, $approvalPosition2,
                'R', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->Cell(0, 8, '', 'LR', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.422, 3, '', 'L', '0', 'C', false, '', 1, true);
        $this->Cell($pageWidth * 0.086, 3, '', 0, 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth_inMargin - ($pageWidth * 0.508), 3, $approval3,
                'R', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.422, 3, '', 'L', '0', 'C', false, '', 1, true);
        $this->Cell($pageWidth * 0.086, 3, '', 0, 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth_inMargin - ($pageWidth * 0.508), 3, $approvalPosition3,
                'R', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->Cell($pageWidth_inMargin, 3, '', 'BLR', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell(0, 4, 'II. ADVICE TO DEBIT ACCOUNT (ADA)', 1, 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->Cell(0, 4, 'To: MDS-GSB of the Agency', 'LR', 0, 'l', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell(0, 4, "Please debit MDS Sub-Account Number : $mdsgsbSubAccount", 'LR', 0, 'l', false, '', 1, true);
        $this->Ln();

        $this->Cell(0, 4,
                'Please debit the accounts of the above listed creditors to cover '.
                'payment of accounts payable', 'LR',
                0, 'l', false, '', 1, true);
        $this->Ln();

        $this->Cell(0, 4, '', 'LR', 0, 'l', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.85, 4, "TOTAL AMOUNT: $totalAmountWords", 'L', 0, 'L', false, '', 1, true);
        $this->Cell(0, 4, $totalAmount, 1, 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.85, 4, '(In Words)', 'L', 0, 'C', false, '', 1, true);
        $this->Cell(0, 4, '', 'R', 0, 'L', false, '', 1, true);
        $this->Ln();

        $this->Cell(0, 4, '', 'LR', 0, 'l', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell(0, 4, 'Agency Authorized Signatories', 'LR', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->Cell(0, 8, '', 'LR', 0, 'l', false, '', 1, true);
        $this->Ln();

        $this->Cell($pageWidth_inMargin / 4, 4, $agencyAuth1, 'L', 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth_inMargin / 4, 4, $agencyAuth2, 0, 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth_inMargin / 4, 4, $agencyAuth3, 0, 0, 'C', false, '', 1, true);
        $this->Cell($pageWidth_inMargin / 4, 4, $agencyAuth4, 'R', 0, 'C', false, '', 1, true);
        $this->Ln();

        $this->SetFont('Times', 'I', 8 + ($fontScale * 8));
        $this->Cell(0, 8, '(Erasures shall invalidate this document)', 'BLR', 0, 'C', false, '', 1, true);
        $this->Ln();

        /*
        $this->SetTextColor(0, 139, 255);
        $this->SetFont('Helvetica', '', 11 + ($fontScale * 11));
        $this->SetLineWidth(0.05);
        $this->setXY(158, 35);
        $cellBorder = [
            'TBLR' => ['width' => 0.1, 'color' => [221, 221, 221], 'solid' => 0, 'cap' => 'butt']
        ];
        $this->Cell(45, 7, $serialNo, $cellBorder, 1, 'C');
        $this->SetTextColor(0, 0, 0);*/

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
