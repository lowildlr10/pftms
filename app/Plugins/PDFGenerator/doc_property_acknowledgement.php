<?php

/* ------------------------------------- Start of Config ------------------------------------- */

//set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Set margins
$pdf->SetMargins(10, 35, 10);
$pdf->SetHeaderMargin(10);

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

//Title
$pdf->SetFont('Times', 'B', 14 + ($increaseFontSize * 14));
$pdf->Cell(0, "5", "PROPERTY ACKNOWLEDGMENT RECEIPT", "", "", "C");
$pdf->Ln(10);

$pdf->SetFont('Times', 'B', 11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.65, "5", "Fund Cluster : 01", "", "", "L");
$pdf->Cell(0, "5", "PAR No : " . $data->inventory_no, "", "", "L");
$pdf->Ln(10);

//Table data
$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->htmlTable($data->table_data);

$pdf->SetFont("Times", "B", 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.2, "5", "Purchase Order", "L", "", "L");
$pdf->Cell(0, "5", ": " . $data->po->po_no, "R", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.2, "5", "Date", "L", "", "L");
$pdf->Cell(0, "5", ": " . $poDate, "R", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.2, "5", "Supplier", "L", "", "L");
$pdf->Cell(0, "5", ": " . $data->po->company_name, "R", "", "L");
$pdf->Ln();

$pdf->Cell(0, "10", "", "LRB", "", "L");
$pdf->Ln();

$pdf->SetFont("Times", "", 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.45, "5", "Received by:", "LR", "", "L");
$pdf->Cell(0, "5", "Issued By: ", "R", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
$pdf->Cell(0, "5", "", "R", "", "L");
$pdf->Ln();

$pdf->SetFont("Times", "B", 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.45, "5", $data->received_by->name, "LR", "", "C");
$pdf->Cell(0, "5", $data->issued_by->name, "R", "", "C");
$pdf->Ln();

$pdf->SetFont("Times", "", 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.45, "5", "Signature over Printed Name of End User", "LR", "", "C");
$pdf->Cell(0, "5", "Signature over Printed Name of Supply and/or", "R", "", "C");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.45, "3", "", "LR", "", "L");
$pdf->Cell(0, "3", "Property Custodian", "R", "", "C");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
$pdf->Cell(0, "5", "", "R", "", "C");
$pdf->Ln();

$pdf->SetFont("Times", "B", 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.45, "5", $data->received_by->position, "LR", "", "C");
$pdf->Cell(0, "5", $data->issued_by->position, "R", "", "C");
$pdf->Ln();

$pdf->SetFont("Times", "", 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.45, "5", "Position/Office", "LR", "", "C");
$pdf->Cell(0, "5", "Position/Office", "R", "", "C");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
$pdf->Cell(0, "5", "", "R", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.45, "5", "_________________________________", "LR", "", "C");
$pdf->Cell(0, "5", "_________________________________", "R", "", "C");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.45, "5", "Date", "LRB", "", "C");
$pdf->Cell(0, "5", "Date", "RB", "", "C");
$pdf->Ln();

/* ------------------------------------- End of Doc ------------------------------------- */
