<?php

/* ------------------------------------- Start of Config ------------------------------------- */

//set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetPrintHeader(false);

//Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

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

//Title header
$pdf->Cell($pageWidth * 0.57, 5,'', 'TL', '', 'C');
$pdf->Cell($pageWidth * 0, 5, '', 'TLR');
$pdf->Ln();

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell($pageWidth * 0.57, 5, 'LIQUIDATION REPORT', 'L', '', 'C');
$pdf->SetFont('Times', '', 12);
$pdf->Cell($pageWidth * 0, 5, 'Serial No.: ' . $serialNo, 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.57, 5, 'Period Covered: ' . $data->liq->period_covered, 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, 'Date: ' . $dateLiquidation, 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.57, 5, '', 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, '', 'BLR');
$pdf->Ln();

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell($pageWidth * 0.57, 5, 'Entity Name : ' . $data->liq->entity_name, 'L', '');
$pdf->SetFont('Times', '', 12);
$pdf->Cell($pageWidth * 0, 5, 'Responsibility Center Code: ', 'LR');
$pdf->Ln();

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell($pageWidth * 0.57, 6, 'Fund Cluster :  ' . $data->liq->fund_cluster, 'BL', '');
$pdf->SetFont('Times', '', 12);
$pdf->Cell($pageWidth * 0, 6, $data->liq->responsibility_center, 'BLR', '', 'C');
$pdf->Ln();

// Table title
$pdf->SetFont('Times', 'B', 11);
$pdf->Cell($pageWidth * 0.57, 10, 'PARTICULARS', 'BL', '', 'C');
$pdf->Cell($pageWidth * 0, 10, 'AMOUNT', 'BLR', '', 'C');
$pdf->Ln();

// Table body
$pdf->SetFont('Times', '', 11);
$pdf->htmlTable($data->table_data);

// Signatories row
$pdf->Cell($pageWidth * 0.03, 5, 'A', 'BL', '');
$pdf->Cell($pageWidth * 0.255, 5, 'Certified: Correctness of the', 'L', '');
$pdf->Cell($pageWidth * 0.03, 5, 'B', 'BL', '');
$pdf->Cell($pageWidth * 0.255, 5, 'Certified: Purpose of travel /', 'L', '');
$pdf->Cell($pageWidth * 0.03, 5, 'C', 'BL', '');
$pdf->Cell($pageWidth * 0, 5, 'Certified: Supporting documents', 'LR', '');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.03, 5, '', 'L', '');
$pdf->Cell($pageWidth * 0.255, 5, 'above data', '', '');
$pdf->Cell($pageWidth * 0.03, 5, '', 'L', '');
$pdf->Cell($pageWidth * 0.255, 5, 'cash advance duly accomplished', '', '');
$pdf->Cell($pageWidth * 0.03, 5, '', 'L', '');
$pdf->Cell($pageWidth * 0, 5, 'complete and proper', 'R', '');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.285, 5, '', 'L');
$pdf->Cell($pageWidth * 0.285, 5, '', 'L');
$pdf->Cell($pageWidth * 0, 5, '', 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.285, 5, $claimant, 'L', '', 'C');
$pdf->Cell($pageWidth * 0.285, 5, $supervisor, 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, $accounting, 'LR', '', 'C');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.285, 5, 'Signature over Printed Name', 'L', '', 'C');
$pdf->Cell($pageWidth * 0.285, 5, 'Signature over Printed Name', 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, 'Signature over Printed Name', 'LR', '', 'C');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.285, 5, 'Claimant', 'L', '', 'C');
$pdf->Cell($pageWidth * 0.285, 5, 'Immediate Supervisor', 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, 'Head, Accounting Division Unit', 'LR', '', 'C');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.285, 5, '', 'L');
$pdf->Cell($pageWidth * 0.285, 5, '', 'L');
$pdf->Cell($pageWidth * 0, 5, '', 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.285, 5, '', 'L');
$pdf->Cell($pageWidth * 0.285, 5, '', 'L');
$pdf->Cell($pageWidth * 0, 5, 'JEV No.:' . $data->liq->jev_no, 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.285, 5, 'Date: ' . $dateClaimant, 'BL');
$pdf->Cell($pageWidth * 0.285, 5, 'Date: ' . $dateSupervisor, 'BL');
$pdf->Cell($pageWidth * 0, 5, 'Date:' . $dateAccountant, 'BLR');
$pdf->Ln();

/* ------------------------------------- End of Doc ------------------------------------- */
