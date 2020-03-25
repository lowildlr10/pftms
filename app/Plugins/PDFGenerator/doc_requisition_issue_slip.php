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
$pdf->Cell(0, "5", "REQUISITION AND ISSUE SLIP", "", "", "C");
$pdf->Ln(10);

$pdf->SetFont('Times', 'IB', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, "5", "Fund Cluster : 01", "", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.55, "2", "", "TLR", "", "L");
$pdf->Cell(0, "2", "", "TR", "", "L");
$pdf->Ln();

$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.55, "7", "Division : " . $data->po->division, "LR", "", "L");
$pdf->SetFont('Times', 'IB', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, "7", "Responsibility Center Code : 19 001 03000 14", "R", "", "L");
$pdf->Ln();

$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.55, "7", "Office : " . $data->po->office, "LR", "", "L");
$pdf->Cell(0, "7", "RIS No : " . $data->inventory_no, "R", "", "L");
$pdf->Ln();

//Table data
$pdf->htmlTable($data->table_data);

$pdf->Cell(0, 5, "Purpose: " . $data->po->purpose, 1, "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.16, "7", "", "LR", "", "L");
$pdf->Cell($pageWidth * 0.187, "7", "Requested by:", "RB", "", "L");
$pdf->Cell($pageWidth * 0.186, "7", "Approved by:", "RB", "", "L");
$pdf->Cell($pageWidth * 0.186, "7", "Issued by:", "RB", "", "L");
$pdf->Cell(0, "7", "Received by:", "RB", "", "L");
$pdf->Ln();

//Footer data
$pdf->htmlTable($data->footer_data);

/* ------------------------------------- End of Doc ------------------------------------- */