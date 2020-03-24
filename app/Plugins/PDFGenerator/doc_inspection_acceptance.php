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

$pdf->SetFont('Times', 'B', 14 + ($increaseFontSize * 14));
$pdf->Cell(0, 5, "INSPECTION AND ACCEPTANCE REPORT", '', '', 'C');
$pdf->Ln();

$pdf->Ln(5);

$pdf->SetFont('Times', 'IB', 11 + ($increaseFontSize * 11));
$pdf->Cell(0, 5, "Fund Cluster : 01", '', '', 'L');
$pdf->Ln();

$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($contentWidth * 0.147, 7, 'Supplier: ', 'TL', 'L', 0, 0, '', '', true);
$pdf->MultiCell($contentWidth * 0.47, 7, $data->iar->company_name, 'TR', 'L', 0, 0, '', '', true);
$pdf->MultiCell($contentWidth * 0.126, 7, 'IAR No.:', 'T', 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 7, $data->iar->iar_no, 'TR', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->MultiCell($contentWidth * 0.617, 3, '', 'LR', 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 3, '', 'LR', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($contentWidth * 0.147, 7, 'PO No./Date : ', 'L', 'L', 0, 0, '', '', true);
$pdf->MultiCell($contentWidth * 0.47, 7, $poDate, 'R', 'L', 0, 0, '', '', true);
$pdf->SetFont('Times', 'IB', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($contentWidth * 0.126, 7, 'Date :', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('Times', '', 11);
$pdf->MultiCell(0, 7, $iarDate, 'R', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($contentWidth * 0.263, 7, 'Requisitioning Office/Dept. :', 'L', 'L', 0, 0, '', '', true);
$pdf->MultiCell($contentWidth * 0.354, 7, $data->iar->division, 'R', 'L', 0, 0, '', '', true);
$pdf->MultiCell($contentWidth * 0.126, 7, 'Invoice No. :', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 7, $data->iar->invoice_no, 'R', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('Times', 'IB', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($contentWidth * 0.263, 7, 'Responsibility Center Code :', 'L', 'L', 0, 0, '', '', true);
$pdf->MultiCell($contentWidth * 0.354, 7, $data->iar->responsibility_center, 'R', 'L', 0, 0, '', '', true);
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($contentWidth * 0.126, 7, 'Date :', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 7, $invoiceDate, 'R', 'L', 0, 0, '', '', true);
$pdf->Ln();

//Table data
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->htmlTable($data->table_data);

//Table footer
$pdf->htmlTable($data->footer_data);

$pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
$pdf->Cell($contentWidth * 0.5, 7, 'Date Inspected : ______________________________', "TLR");
$pdf->Cell(0, 7, 'Date Received : ____________________________', "TLR");
$pdf->ln();

$pdf->Cell($contentWidth * 0.5, 5, '', 'LR');
$pdf->Cell(0, 5, 'PO/JO #: ', 'LR');
$pdf->ln();

$pdf->Cell($contentWidth * 0.0105, 7, '', 'LR');
$pdf->Cell($contentWidth * 0.0421, 7, '', "TBLR");
$pdf->Cell($contentWidth * 0.4474, 7, "\tInspected, verified and found in order as to", 'LR');
$pdf->Cell(0, 7, '', 'LR');
$pdf->ln();

$pdf->Cell($contentWidth * 0.0105, 7, '', 'L');
$pdf->Cell($contentWidth * 0.0421, 7, '', '');
$pdf->Cell($contentWidth * 0.4474, 7, "\tquantity and specifications", 'R');
$pdf->Cell($contentWidth * 0.0368, 7, '', 'LR');
$pdf->Cell($contentWidth * 0.0421, 7, '', "TBLR");
$pdf->Cell(0, 7, "\tComplete", 'LR');
$pdf->ln();

$pdf->Cell($contentWidth * 0.5, 5, '', 'LR');
$pdf->Cell(0, 5, '', 'LR');
$pdf->ln();

$pdf->Cell($contentWidth * 0.0105, 7, '', 'LR');
$pdf->Cell($contentWidth * 0.0421, 7, '', "TBLR");
$pdf->Cell($contentWidth * 0.4474, 7, "\tNot in conformity as to quantity and ", 'R');
$pdf->Cell($contentWidth * 0.0368, 7, '', 'LR');
$pdf->Cell($contentWidth * 0.0421, 7, '', "TBLR");
$pdf->Cell(0, 7, "\tPartial  (pls. specify quantity)", 'LR');
$pdf->ln();

$pdf->Cell($contentWidth * 0.0105, 7, '', 'L');
$pdf->Cell($contentWidth * 0.0421, 7, '', '');
$pdf->Cell($contentWidth * 0.4474, 7, "\tspecifications", 'R');
$pdf->Cell(0, 7, '', 'LR');
$pdf->ln();

$pdf->SetFont('Times','', 12 + ($increaseFontSize * 12));
$pdf->Cell($contentWidth * 0.5, 3, '', 'LR', '', 'C');
$pdf->Cell(0, 3, '', 'LR', '', 'C');
$pdf->ln();

$pdf->SetFont('Times','B', 12 + ($increaseFontSize * 12));
$pdf->Cell($contentWidth * 0.5, 5, strtoupper($sign1), 'LR', '', 'C');
$pdf->Cell(0, 5, strtoupper($sign2), 'LR', '', 'C');
$pdf->ln();

$pdf->SetFont('Times','', 12 + ($increaseFontSize * 12));
$pdf->Cell($contentWidth * 0.5, 8, "Inspection Officer/Inspection Committee", 'BLR', '', 'C');
$pdf->Cell(0, 8, "Supply and/or Property Custodian", 'BLR', '', 'C');
$pdf->ln();

$pdf->SetFont('Times','', 11 + ($increaseFontSize * 11));
$pdf->Cell($contentWidth * 0.5, 8, 'Remarks/Recommendation: ', 'LR', '', 'L');
$pdf->Cell(0, 8, '', 'LR');
$pdf->ln();

$pdf->SetFont('Times','', 12 + ($increaseFontSize * 12));
$pdf->Cell($contentWidth * 0.5, 5, '___________________________________________ ', 'LR', '', 'L');
$pdf->Cell(0, 5, '', 'LR');
$pdf->ln();

$pdf->Cell($contentWidth * 0.5, 6, '', 'BLR');
$pdf->Cell(0, 6, '', 'BLR');
$pdf->ln();

/* ------------------------------------- End of Doc ------------------------------------- */
