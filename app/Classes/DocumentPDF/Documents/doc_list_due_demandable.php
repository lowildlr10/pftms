<?php

/* ------------------------------------- Start of Config ------------------------------------- */

//set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Set margins
$pdf->SetMargins(5, 10, 5);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(5);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

//Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5);

//Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//Set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

//Set default font subsetting mode
$pdf->setFontSubsetting(true);

/* ------------------------------------- End of Config ------------------------------------- */

//Add a page
$pdf->AddPage();

/* ------------------------------------- Start of Doc ------------------------------------- */

$pageWidth_inMargin = $pdf->getPageWidth() - 10;

//Title
$pdf->SetFont('Times', 'B', 8 + ($increaseFontSize * 8));
$pdf->Cell(0, 8, strtoupper($docSubject . ' (LDDAP-ADA)'), 0, 0, 'C');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.685, 4, '', 0, 0, 'L');
$pdf->Cell(0, 4, 'NCA No. ' . $ncaNo, 0, 0, 'L');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.685, 4, 'Department : ' . $department, 0, 0, 'L');
$pdf->Cell(0, 4, 'LDDAP-ADA No. ' . $lddapNo, 0, 0, 'L');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.685, 4, 'Entity Name : ' . $entityName, 0, 0, 'L');
$pdf->Cell(0, 4, 'Date : ' . $lddapDate, 0, 0, 'L');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.685, 4, 'Operating Unit : ' . $operatingUnit, 0, 0, 'L');
$pdf->Cell(0, 4, 'Fund Cluster : ' . $fundCluster, 0, 0, 'L');
$pdf->Ln();

$pdf->Cell(0, 10, 'MDS-GSB BRANCH/MDS SUB ACCOUNT NO.: ' . $mdsgsbBranch, 0, 0, 'C');
$pdf->Ln();

//Table Data
$pdf->Cell(0, 4, 'I. LIST OF DUE AND DEMANDABLE ACCOUNTS PAYABLE (LDDAP)', 1, 0, 'C');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.422, 4, 'CREDITOR', 'BLR', 0, 'C');
$pdf->SetFont('Times', 'B', 6 + ($increaseFontSize * 6));
$pdf->Cell($pageWidth * 0.086, 4, 'Obligation', 'R', 0, 'C');
$pdf->Cell($pageWidth * 0.083, 4, 'ALLOTMENT', 'R', 0, 'C');
$pdf->SetFont('Times', 'B', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.245, 4, 'In Pesos', 'BR', 0, 'C');
$pdf->Cell(0, 4, '', 'R', 0, 'L');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.234, 0.5, '', 'LR', 0, 'C', false, '', 1, true);
$pdf->SetFont('Times', '', 6 + ($increaseFontSize * 6));
$pdf->Cell($pageWidth * 0.188, 0.5, '', 'R', 0, 'L', false, '', 1, true);
$pdf->SetFont('Times', 'B', 6 + ($increaseFontSize * 6));
$pdf->Cell($pageWidth * 0.086, 2, 'Request and', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * 0.083, 2, 'CLASS per', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 0.5, '', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 0.5, '', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 0.5, '', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell(0, 0.5, '', 'R', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.234, 2, '', 'LR', 0, 'C', false, '', 1, true);
$pdf->SetFont('Times', '', 6 + ($increaseFontSize * 6));
$pdf->Cell($pageWidth * 0.188, 2, 'PREFERRED SERVICING', 'R', 0, 'L', false, '', 1, true);
$pdf->SetFont('Times', 'B', 6 + ($increaseFontSize * 6));
$pdf->Cell($pageWidth * 0.086, 2, '', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * 0.083, 2, '', 'R', 0, 'C', false, '', 1, true);
$pdf->SetFont('Times', '', 7 + ($increaseFontSize * 7));
$pdf->Cell($pageWidth * (0.245/3), 2.5, 'GROSS', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 2.5, 'WITHHOLDI', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 2.5, 'NET', 'R', 0, 'C', false, '', 1, true);
$pdf->SetFont('Times', 'B', 8 + ($increaseFontSize * 8));
$pdf->Cell(0, 2, 'REMARKS', 'R', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.234, 2, 'NAME', 'LR', 0, 'C', false, '', 1, true);
$pdf->SetFont('Times', '', 6 + ($increaseFontSize * 6));
$pdf->Cell($pageWidth * 0.188, 2, 'BANK/SAVINGS/CURRENT ACCOUNT', 'R', 0, 'L', false, '', 1, true);
$pdf->SetFont('Times', 'B', 6 + ($increaseFontSize * 6));
$pdf->Cell($pageWidth * 0.086, 3, 'Status No.', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * 0.083, 3, '(UACS)', 'R', 0, 'C', false, '', 1, true);
$pdf->SetFont('Times', '', 7 + ($increaseFontSize * 7));
$pdf->Cell($pageWidth * (0.245/3), 3, 'AMOUNT', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 3, 'NG TAX', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 3, 'AMOUNT', 'R', 0, 'C', false, '', 1, true);
$pdf->Cell(0, 2, '', 'R', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.234, 2, '', 'BLR', 0, 'C', false, '', 1, true);
$pdf->SetFont('Times', '', 6 + ($increaseFontSize * 6));
$pdf->Cell($pageWidth * 0.188, 2, 'NO.', 'BR', 0, 'L', false, '', 1, true);
$pdf->Cell($pageWidth * 0.086, 2, '', 'BR', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * 0.083, 2, '', 'BR', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 2, '', 'BR', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 2, '', 'BR', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth * (0.245/3), 2, '', 'BR', 0, 'C', false, '', 1, true);
$pdf->Cell(0, 2, '', 'BR', 0, 'C', false, '', 1, true);
$pdf->Ln();

//Table Content
$pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
$pdf->htmlTable($data->table_data);

//Footer
$pdf->Cell($pageWidth * 0.508, 4, '', 'L', 0, 'L', false, '', 1, true);
$pdf->Cell(0, 4, '', 'R', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.508, 3,
           "\t\t\t\t\t I hereby warrant that the above List of Due and Demandable A/Ps",
           'L', 0, 'L', false, '', 1, true);
$pdf->Cell(0, 3,
           "\t\t\t\t\t I hereby assume full responsibility for the veracity and accuracy of the",
           'R', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.508, 3,
           "was prepared in accordance with existing budgeting, accounting and",
           'L', 0, 'L', false, '', 1, true);
$pdf->Cell(0, 3,
           "listed claims, and the authencity of the supporting documents as submitted",
           'R', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.508, 3,
           "auditing rules and regulations.",
           'L', 0, 'L', false, '', 1, true);
$pdf->Cell(0, 3,
           "by the claimants.",
           'R', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.508, 5, '', 'L', 0, 'L', false, '', 1, true);
$pdf->Cell(0, 5, '', 'R', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.508, 3,
           "\t\t\t\t\tCertified Correct:",
           'L', 0, 'L', false, '', 1, true);
$pdf->Cell(0, 3,
           "Approved:",
           'R', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->Cell(0, 8, '', 'LR', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', 'B', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.422, 3, $certCorrect, 'L', '0', 'C', false, '', 1, true);
$pdf->Cell($pageWidth * 0.086, 3, '', 0, 0, 'C', false, '', 1, true);
$pdf->Cell(($pageWidth_inMargin - ($pageWidth * 0.508)) / 2, 3, $approval1,
           '', 0, 'C', false, '', 1, true);
$pdf->Cell(($pageWidth_inMargin - ($pageWidth * 0.508)) / 2, 3, $approval2,
           'R', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.422, 3, $certCorrectPosition, 'L', '0', 'C', false, '', 1, true);
$pdf->Cell($pageWidth * 0.086, 3, '', 0, 0, 'C', false, '', 1, true);
$pdf->Cell(($pageWidth_inMargin - ($pageWidth * 0.508)) / 2, 3, $approvalPosition1,
           '', 0, 'C', false, '', 1, true);
$pdf->Cell(($pageWidth_inMargin - ($pageWidth * 0.508)) / 2, 3, $approvalPosition2,
           'R', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->Cell(0, 8, '', 'LR', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', 'B', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.422, 3, '', 'L', '0', 'C', false, '', 1, true);
$pdf->Cell($pageWidth * 0.086, 3, '', 0, 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth_inMargin - ($pageWidth * 0.508), 3, $approval3,
           'R', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.422, 3, '', 'L', '0', 'C', false, '', 1, true);
$pdf->Cell($pageWidth * 0.086, 3, '', 0, 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth_inMargin - ($pageWidth * 0.508), 3, $approvalPosition3,
           'R', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->Cell($pageWidth_inMargin, 3, '', 'BLR', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', 'B', 8 + ($increaseFontSize * 8));
$pdf->Cell(0, 4, 'II. ADVICE TO DEBIT ACCOUNT (ADA)', 1, 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->Cell(0, 4, 'To: MDS-GSB of the Agency', 'LR', 0, 'l', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
$pdf->Cell(0, 4, "Please debit MDS Sub-Account Number : $mdsgsbBranch", 'LR', 0, 'l', false, '', 1, true);
$pdf->Ln();

$pdf->Cell(0, 4,
           'Please debit the accounts of the above listed creditors to cover '.
           'payment of accounts payable', 'LR',
           0, 'l', false, '', 1, true);
$pdf->Ln();

$pdf->Cell(0, 4, '', 'LR', 0, 'l', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', 'B', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.85, 4, "TOTAL AMOUNT: $totalAmountWords", 'L', 0, 'L', false, '', 1, true);
$pdf->Cell(0, 4, $totalAmount, 1, 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
$pdf->Cell($pageWidth * 0.85, 4, '(In Words)', 'L', 0, 'C', false, '', 1, true);
$pdf->Cell(0, 4, '', 'R', 0, 'L', false, '', 1, true);
$pdf->Ln();

$pdf->Cell(0, 4, '', 'LR', 0, 'l', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', 'B', 8 + ($increaseFontSize * 8));
$pdf->Cell(0, 4, 'Agency Authorized Signatories', 'LR', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->Cell(0, 8, '', 'LR', 0, 'l', false, '', 1, true);
$pdf->Ln();

$pdf->Cell($pageWidth_inMargin / 4, 4, $agencyAuth1, 'L', 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth_inMargin / 4, 4, $agencyAuth2, 0, 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth_inMargin / 4, 4, $agencyAuth3, 0, 0, 'C', false, '', 1, true);
$pdf->Cell($pageWidth_inMargin / 4, 4, $agencyAuth4, 'R', 0, 'C', false, '', 1, true);
$pdf->Ln();

$pdf->SetFont('Times', 'I', 8 + ($increaseFontSize * 8));
$pdf->Cell(0, 8, '(Erasures shall invalidate this document)', 'BLR', 0, 'C', false, '', 1, true);
$pdf->Ln();

/* ------------------------------------- End of Doc ------------------------------------- */
