<?php

/* ------------------------------------- Start of Config ------------------------------------- */

//set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Set margins
$pdf->SetMargins(10, 22, 10);
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

//Title header with Logo
$xCoor = $pdf->GetX();
$yCoor = $pdf->GetY();

$pdf->Cell($pageWidth * 0.74762, 1, '', "TL", 0, 'C');
$pdf->Cell(0, 1, '', 'TR');
$pdf->Ln();

$xCoor = $pdf->getX();
$yCoor = $pdf->getY();

$arrContextOptions = [
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
];
$img = file_get_contents(url('images/logo/dostlogo.png'), false,
                         stream_context_create($arrContextOptions));

$pdf->Image('@' . $img, $xCoor + 16, $yCoor, 16, 0, 'PNG');
$pdf->SetFont('Times', 'B', 10);
$pdf->Cell($pageWidth * 0.74762, 5, 'Republic of the Philippines', 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, '', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.74762, 5, 'DEPARTMENT OF SCIENCE AND TECHNOLOGY', 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, '', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.74762, 3, 'Cordillera Administrative Region', 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 3, '', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.74762, 4, '', 'BL', '', 'C');
$pdf->Cell($pageWidth * 0, 4, '', 'BR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.74762, 5,'', 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, "Fund Cluster : 01", 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.74762, 5, "DISBURSEMENT VOUCHER", 'L', '', 'C');
$pdf->Cell($pageWidth * 0, 5, "Date : " . $dvDate, 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.74762, 5, '', 'BL', '', 'C');
$pdf->Cell($pageWidth * 0, 5, "DV No. : " . $data->dv->dv_no, 'BLR');
$pdf->Ln();

$x = $pdf->getX();
$y = $pdf->getY();

$pdf->MultiCell($pageWidth * 0.10476, 3.8, "\nMode of \nPayment\n   ", 1);
$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));

$pdf->SetXY($x + $pageWidth * 0.10476, $y);
$pdf->SetFont('ZapfDingbats', '', 15 + ($increaseFontSize * 15));

// Fill checkbox with check symbol
/*
if ($paymentMode[0] != "0") {
    $pdf->Text($x + ($pageWidth - 20) * 0.13333, $y + 2, 3);
}

if ($paymentMode[1] != "0") {
    $pdf->Text($x + $pageWidth * 0.28095, $y + 2, 3);
}

if ($paymentMode[2] != "0") {
    $pdf->Text($x + $pageWidth * 0.49524, $y + 2, 3);
}

if ($paymentMode[3] != "0") {
    $pdf->Text($x + $pageWidth * 0.6, $y + 2, 3);
}*/

$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 15));
$pdf->Rect($x + $pageWidth * 0.12857, $y + 3, 5, 5);
$pdf->Rect($x + $pageWidth * 0.27619, $y + 3, 5, 5);
$pdf->Rect($x + $pageWidth * 0.49048, $y + 3, 5, 5);
$pdf->Rect($x + $pageWidth * 0.59524, $y + 3, 5, 5);
$pdf->MultiCell(0, 3.8, "\n \t\t\t\t\t\t\t\t\t  MDS Check \t\t\t\t\t\t\t\t\t\t\t\t\t   Commercial Check" .
                              "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    ADA" .
                              "\t\t\t\t\t\t\t\t\t\t\t\t\t      Others (Please specify)
                               \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                              "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                              "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                              "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                              "_____________________\n\n", 1);

//Table data
$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->htmlTable($data->header_data);
$pdf->htmlTable($data->table_data);

//Section A
$pdf->Cell($pageWidth * 0.02391, 5, "A.", 1);
$pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 5,
           "Certified: Expenses/Cash Advance necessary, ".
           "lawful and incurred under my direct supervision.", 'R');
$pdf->Ln();

$pdf->Cell(0, 5, '', 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.2619, 5, '', 'L');
$pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.390476, 5, strtoupper($signatory), '', '', 'C');
$pdf->Cell(0, 5, '', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.2619, 5, '', 'BL');
$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.390476, 5,
           "Printed Name, Designation and Signature of Supervisor", 'B', '', 'C');
$pdf->Cell(0, 5, '', 'BR');
$pdf->Ln();

//Section B
$pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.02381, 5, "B.", 1);
$pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 5, "Accounting Entry:", 'R');
$pdf->Ln();

$pdf->htmlTable($data->footer_data);

$pdf->Cell($pageWidth * 0.466667, 6, '', 'LR');
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->Rect($x + 2, $y + 2, 4, 4);

$pdf->Cell($pageWidth * 0.171429, 6, '', 'LR');
$pdf->Cell($pageWidth * 0.1381, 6, '', 1);
$pdf->Cell(0, 6, '', 1);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.466667, 6, '', 'LR');
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->Rect($x + 2, $y + 2, 4, 4);

$pdf->Cell($pageWidth * 0.171429, 6, '', 'LR');
$pdf->Cell($pageWidth * 0.1381, 6, '', 1);
$pdf->Cell(0, 6, '', 1);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.466667, 6, '', 'LR');
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->Rect($x + 2, $y + 2, 4, 4);

$pdf->Cell($pageWidth * 0.171429, 6, '', 'LR');
$pdf->Cell($pageWidth * 0.1381, 6, '', 1);
$pdf->Cell(0, 6, '', 1);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.466667, 6, '', 'LR');
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->Rect($x + 2, $y + 2, 4, 4);

$pdf->Cell($pageWidth * 0.171429, 6, '', 'LR');
$pdf->Cell($pageWidth * 0.1381, 6, '', 1);
$pdf->Cell(0, 6, '', 1);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.466667, 6, '', 'LR');
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->Rect($x + 2, $y + 2, 4, 4);

$pdf->Cell($pageWidth * 0.171429, 6, '', 'LR');
$pdf->Cell($pageWidth * 0.1381, 6, '', 1);
$pdf->Cell(0, 6, '', 1);
$pdf->Ln();

$pdf->Cell($pageWidth * 0.466667, "2", '', 'BLR');
$pdf->Cell($pageWidth * 0.171429, "2", '', 'BLR');
$pdf->Cell($pageWidth * 0.1381, "2", '', 1);
$pdf->Cell(0, "2", '', 1);
$pdf->Ln();

//Section C & D
$pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.02381, 5, "C.", 1);
$pdf->Cell($pageWidth * 0.4429, 5, "Certified:", 1);
$pdf->Cell($pageWidth * 0.02381, 5, "D.", 1);
$pdf->Cell(0, 5, "Approved for Payment", 1);
$pdf->Ln();

$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.02381, 6, '', 'L');
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->Rect($x, $y + 1, 8, 4);

$pdf->Cell($pageWidth * 0.0381, 6, '', '');
$pdf->Cell($pageWidth * 0.40476, 6, " Cash available", 'R');
$pdf->Cell(0, 6, '', 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.02381, 6, '', 'L');
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->Rect($x, $y + 1, 8, 4);

$pdf->Cell($pageWidth * 0.0381, 6, '', '');
$pdf->Cell($pageWidth * 0.40476, 6, " Subject to Authority to Debit Account (when applicable)", 'R');
$pdf->Cell(0, 6, '', 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.02381, 6, '', 'L');
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->Rect($x, $y + 1, 8, 4);

$pdf->Cell($pageWidth * 0.0381, 6, '', '');
$pdf->Cell($pageWidth * 0.40476, 6, " Supporting documents complete and amount claimed", 'R');
$pdf->Cell(0, 6, '', 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.02381, 4, '', 'BL');
$pdf->Cell($pageWidth * 0.0381, 4, '', 'B');
$pdf->Cell($pageWidth * 0.40476, 4, "  proper", 'BR');
$pdf->Cell(0, 4, '', 'BLR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.10476, 10, "Signature", 'BLR', '', 'C');
$pdf->Cell($pageWidth * 0.3619, 10, '', 'BLR');
$pdf->Cell($pageWidth * 0.10476, 10, "Signature", 'BLR', '', 'C');
$pdf->Cell(0, 10, '', 'BLR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.10476, 5, "Printed Name", 'BLR', '', 'C');
$pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.3619, 5, strtoupper($sign1), 'BLR', '', 'C');
$pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.10476, 5, "Printed Name", 'BLR', '', 'C');
$pdf->SetFont('Times','B', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 5, strtoupper($sign2), 'BLR', '', 'C');
$pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
$pdf->Ln();

$pdf->Cell($pageWidth * 0.10476, 10, '', 'BLR', '', 'C');
$pdf->Cell($pageWidth * 0.3619, 10, "Head, Accounting Unit/Authorized Representative", 'BLR', '', 'C');
$pdf->Cell($pageWidth * 0.10476, 10, '', 'BLR', '', 'C');
$pdf->Cell(0, 10, "Agency Head/Authorized Representative", 'BLR', '', 'C');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.10476, 5, "Date", 'BLR', '', 'C');
$pdf->Cell($pageWidth * 0.3619, 5, '', 'BLR', '', 'C');
$pdf->Cell($pageWidth * 0.10476, 5, "Date", 'BLR', '', 'C');
$pdf->Cell(0, 5, '', 'BLR', '', 'C');
$pdf->Ln();

//Section E
$pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.02381, 5, "E.", 'LB');
$pdf->Cell($pageWidth * 0.65, 5, "Receipt of Payment", 'LB');
$pdf->SetFont('Times','', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 5, "JEV  No.", 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.105, 5, "Check/   ADA ", 'L', '', 'C');
$pdf->Cell($pageWidth * 0.14, 5, '', 'L');
$pdf->Cell($pageWidth * 0.09, 5, '', 'L');
$pdf->Cell($pageWidth * 0.09, 5, "Date:", 'L');
$pdf->Cell($pageWidth * 0.24881, 5, "Bank Name & Account Number:", 'L');
$pdf->Cell(0, 5, '', 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.105, 5, "No. :", 'L', '', 'C');
$pdf->Cell($pageWidth * 0.14, 5, '', 'L');
$pdf->Cell($pageWidth * 0.09, 5, '', 'L');
$pdf->Cell($pageWidth * 0.09, 5, '', 'L');
$pdf->Cell($pageWidth * 0.24881, 5, '', 'L');
$pdf->Cell(0, 5, '', 'LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.105, 10, "Signature:", 'TL', '', 'C');
$pdf->Cell($pageWidth * 0.14, 5, '', 'TL');
$pdf->Cell($pageWidth * 0.09, 5, '', 'TL');
$pdf->Cell($pageWidth * 0.09, 5, "Date:", 'TL');
$pdf->Cell($pageWidth * 0.24881, 5, "Printed Name:", 'TL');
$pdf->Cell(0, 5, "Date", 'TLR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.105, 5, '', 'L');
$pdf->Cell($pageWidth * 0.14, 5, '', 'L');
$pdf->Cell($pageWidth * 0.09, 5, '', 'L');
$pdf->Cell($pageWidth * 0.09, 5, '', 'L');
$pdf->Cell($pageWidth * 0.24881, 5, '', 'L');
$pdf->Cell(0, 5, '', " LR");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.67381, 5, "Official Receipt No. & Date/Other Documents", 'TBL');
$pdf->Cell(0, 5, '', 'BLR');
$pdf->Ln();

/* ------------------------------------- End of Doc ------------------------------------- */
