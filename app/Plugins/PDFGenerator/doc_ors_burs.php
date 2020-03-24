<?php

/* ------------------------------------- Start of Config ------------------------------------- */

//set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Set margins
$pdf->SetMargins(10, 24, 10);
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

//Title header with logo
if (strtolower($data->ors->document_type) == 'ors') {
    $pdf->SetFont('helvetica', 'B', 15 + ($increaseFontSize * 15));
} else {
    $pdf->SetFont('helvetica', 'B', 14 + ($increaseFontSize * 14));
}

$pdf->Cell($pageWidth * 0.5714, 8, strtoupper($docSubject), 'TLR', 0, 'C');
$pdf->Cell(0, 8, '', 'TR');
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
$pdf->Image('@' . $img, $xCoor + 4, $yCoor, 16, 0, 'PNG');
$pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.10476, 4, '', 'L');

if (strtolower($data->ors->document_type) == 'ors') {
    $pdf->SetTextColor(0, 0, 255);
}

$pdf->Cell($pageWidth * 0.466667, 4, 'Republic of the Philippines', 'R');
$pdf->SetFont('helvetica','IB', 10 + ($increaseFontSize * 10));
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 4, "Serial No. \t\t\t\t\t\t\t\t\t: " . $data->ors->serial_no, 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.10476, 4, '', 'L');
$pdf->SetFont('helvetica', 'B', 10 + ($increaseFontSize * 10));

if (strtolower($data->ors->document_type) == 'ors') {
    $pdf->SetTextColor(0, 0, 255);
}

$pdf->Cell($pageWidth * 0.466667, 4, 'DEPARTMENT OF SCIENCE AND TECHNOLOGY', 'R');
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 4, '', 'R');
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9 + ($increaseFontSize * 9));
$pdf->Cell($pageWidth * 0.10476,4, '', 'L');

if (strtolower($data->ors->document_type) == 'ors') {
    $pdf->SetTextColor(0, 0, 255);
}

$pdf->Cell($pageWidth * 0.466667,4, 'Cordillera Administrative Region', 'R');
$pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 4, "Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t: " . $orsDate, 'R');
$pdf->Ln();

$pdf->SetFont('helvetica','', 9 + ($increaseFontSize * 9));
$pdf->Cell($pageWidth * 0.10476,4,'','L');

if (strtolower($data->ors->document_type) == 'ors') {
    $pdf->SetTextColor(0, 0, 255);
}

$pdf->Cell($pageWidth * 0.466667,4,'Km. 6, La Trinidad, Benguet','R');
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 4, '', 'R');
$pdf->Ln();

$pdf->SetFont('helvetica','IB', 11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.57143,6, 'Entity Name','LRB', 0, 'C');
$pdf->SetFont('helvetica','IB', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 6, "Fund Cluster \t\t\t\t: ____________________", 'RB');
$pdf->Ln();

//Header data
$pdf->SetFont('helvetica', '', 9 + ($increaseFontSize * 9));
$pdf->htmlTable($data->header_data);

//Table data
$pdf->htmlTable($data->table_data);

$pdf->Cell($pageWidth * 0.0952, 7, 'A.', 'LRB');
$pdf->Cell($pageWidth * 0.3667, 7, '', 'R');
$pdf->Cell($pageWidth * 0.1095, 7, 'B.', 'RB');
$pdf->Cell(0, 7, '', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.0762, 5, '', 'L');
$pdf->Cell($pageWidth * 0.08095, 5, 'Certified:', '');
$pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.30476, 5, 'Charges to appropriation/alloment ','');
$pdf->Cell($pageWidth * 0.090476, 5, '', 'L');
$pdf->SetFont('helvetica', 'B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.08095, 5, 'Certified:','');
$pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 5, 'Allotment available and obligated','R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.0762, 5, '', 'L');
$pdf->Cell($pageWidth * 0.3857, 5, 'necessary, lawful and under my direct supervision;');
$pdf->Cell($pageWidth * 0.090476, 5, '', 'L');
$pdf->Cell(0, 5, 'for the purpose/adjustment necessary as', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.0762, 5, '', 'L');
$pdf->Cell($pageWidth * 0.3857, 5, 'and supporting documents valid, proper and legal.');
$pdf->Cell($pageWidth * 0.090476, 5, '', 'L');
$pdf->Cell(0, 5, 'indicated above.', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.4619, 7,'','RL');
$pdf->Cell(0, 7,'','R');
$pdf->Ln();

$pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.4619,8, "Signature \t\t\t\t\t\t\t:       ______________________________", 'LR');
$pdf->Cell(0,8,"Signature \t\t\t\t\t\t\t:      ______________________________",'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.4619,'2','','LR');
$pdf->Cell(0,'2','','R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.12857, 5,"Printed Name : ",'L');
$pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.28095, 5, $data->sign1,'B');
$pdf->Cell($pageWidth * 0.05238, 5," ",'R');
$pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.12857, 5,"Printed Name : ",'');
$pdf->SetFont('helvetica','B', 10);
$pdf->Cell($pageWidth * 0.28095, 5, $data->sign2,'B');
$pdf->Cell(0, 5,'','R');
$pdf->Ln();

$pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.4619,4,'','LR');
$pdf->Cell(0,4,'','R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.12857, 5, "Position \t\t\t\t\t\t\t\t\t:   ", 'L');
$pdf->Cell($pageWidth * 0.28095, 5, $data->position1,'B');
$pdf->Cell($pageWidth * 0.05238, 5, '','R');
$pdf->Cell($pageWidth * 0.12857, 5, "Position \t\t\t\t\t\t\t\t\t:   ");
$pdf->Cell($pageWidth * 0.28095, 5, $data->position2, 'B');
$pdf->Cell(0, 5, '', 'R');
$pdf->Ln();

$pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.4619, 5,
           "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Head, ".
           "Requesting Office/Authorized", 'LR');
$pdf->Cell(0, 5,
           "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Head, ".
           "Budget Division/Unit/Authorized", 'R');
$pdf->Ln();

$pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.4619, 3,
           "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
           "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Representative", 'LR');
$pdf->Cell(0, 3,
           "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
           "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Representative",'R');
$pdf->Ln();

$pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.4619, 3, '', 'LR');
$pdf->Cell(0, 3, '', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.12857, 5, "Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t: ", 'L');
$pdf->Cell($pageWidth * 0.28095, 5, $sDate1, 'B');
$pdf->Cell($pageWidth * 0.05238, 5, '','R');
$pdf->Cell($pageWidth * 0.12857, 5, "Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t: ");
$pdf->Cell($pageWidth * 0.28095, 5, $sDate2, 'B');
$pdf->Cell(0, 5, '', 'R');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.4619, 3, '', 'LRB');
$pdf->Cell(0, 3, '', 'RB');
$pdf->Ln();

//----Footer data
$pdf->htmlTable($data->footer_data);

$pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.5857, '10', "Date Received:", '', "L");
$pdf->Cell($pageWidth * 0.32857, '10', "Date Released:", '', "L");

/* ------------------------------------- End of Doc ------------------------------------- */
