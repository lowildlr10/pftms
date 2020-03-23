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
$pdf->Cell(0, 5, "PURCHASE REQUEST", "0", "", "C");
$pdf->Ln(10);

//Table header
$pdf->SetFont('Times', 'BI', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.114, 5, "Fund Cluster:", "", "", "L");
$pdf->SetFont('Times', 'BIU', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 5, "01", "", "", "L");
$pdf->Ln(6);

$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.224, '6', "Office/Section : ", "TLR", "", "L");
$pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.3712, '6', "PR No.: " . $data->pr->pr_no, "TLR", "", "L");
$pdf->Cell(0, '6', "Date: " . $prDate, "TLR", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.224, '7', $data->pr->office, "BLR", "", "C");
$pdf->Cell($pageWidth * 0.3712, '7', "Responsibility Center Code : 19 001 03000 14", "B", "", "L");
$pdf->Cell(0, '7', "", "BLR", "", "L");
$pdf->Ln();

//Table data
$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->htmlTable($data->table_data);

//Table footer
$pdf->MultiCell(0, 5, 'Purpose: ' . $data->pr->purpose, 'BLR', 'L');

$pdf->Cell($pageWidth * 0.138, 5, "", "TLR", "", "L");
$pdf->Cell($pageWidth * 0.362, 5, "Requested by: ", "TLR", "", "L");
$pdf->Cell(0, 5, "Approved by: ", "TLR", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.138, 5, "Signature : ", "LR", "", "L");

$xCoor = $pdf->GetX();
$yCoor = $pdf->GetY();

if (!empty($data->requested_by->signature)) {
    $options = [
        "ssl" => [
        "verify_peer"=>false,
        "verify_peer_name"=>false,
        ],
    ];

    $context = stream_context_create($options);
    session_write_close();   // this is the key
    $img = file_get_contents(url($data->requested_by->signature), false, $context);

    $pdf->Image('@' . $img,
                $xCoor + (($pageWidth * 0.362) / 3) + 3, $yCoor - 2, 16, 0, 'PNG');
}

$pdf->Cell($pageWidth * 0.02, 5, "", "L", "", "L");
$pdf->Cell($pageWidth * 0.322, 5, "", "B", "", "L");
$pdf->Cell($pageWidth * 0.02, 5, "", "R", "", "L");

$xCoor = $pdf->GetX();
$yCoor = $pdf->GetY();

if (!empty($data->approved_by->signature)  && !empty($data->pr->date_pr_approve)) {
    $options = [
        "ssl" => [
        "verify_peer"=>false,
        "verify_peer_name"=>false,
        ],
    ];

    $context = stream_context_create($options);
    session_write_close();   // this is the key
    $img = file_get_contents(url($data->approved_by->signature), false, $context);

    $pdf->Image('@' . $img,
                $xCoor + (($pageWidth * 0.362) / 3) + 5, $yCoor - 2, 16, 0, 'PNG');
}

$pdf->Cell(0, 5, "", "LR", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.138, 5, "Printed Name : ", "LR", "", "L");
$pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.02, 5, "", "L", "", "L");
$pdf->Cell($pageWidth * 0.322, 5, $data->requested_by->name, "B", "", "C");
$pdf->Cell($pageWidth * 0.02, 5, "", "R", "", "L");
$pdf->Cell($pageWidth * 0.04, 5, "", "", "", "L");
$pdf->Cell($pageWidth * 0.322, 5, $data->approved_by->name, "B", "", "C");
$pdf->Cell(0, 5, "", "R", "", "L");
$pdf->Ln();

$pdf->SetFont('Times', '', 10 + ($increaseFontSize * 10));
$pdf->Cell($pageWidth * 0.138, 5, "Designation : ", "LR", "", "L");
$pdf->Cell($pageWidth * 0.02, 5, "", "L", "", "L");
$pdf->Cell($pageWidth * 0.322, 5, $data->requested_by->position, "B", "", "C");
$pdf->Cell($pageWidth * 0.02, 5, "", "R", "", "L");
$pdf->Cell(0, 5, 'Regional Director', "LR", "", "C");
$pdf->Ln();


$pdf->Cell($pageWidth * 0.138, '6', "", "LR", "", "L");
$pdf->Cell($pageWidth * 0.362, '6', "", "LR", "", "C");
$pdf->Cell(0, '6', "", "LR", "", "C");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.138, 5, "", "LR", "", "L");
$pdf->Cell($pageWidth * 0.362, 5, "", "LR", "", "L");
$pdf->SetFont('Times', 'IB', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 5, "Recommended by: ", "LR", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.138, 5, "", "LR", "", "L");
$pdf->Cell($pageWidth * 0.362, 5, "", "LR", "", "C");
$pdf->Cell(0, 5, "", "LR", "", "C");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.138, 5, "", "LR", "", "L");
$pdf->Cell($pageWidth * 0.362, 5, "", "LR", "", "C");
$pdf->SetFont('Times', 'B', 10 + ($increaseFontSize * 10));

$xCoor = $pdf->GetX();
$yCoor = $pdf->GetY();

if (!empty($data->recommended_by->signature) && !empty($data->pr->date_pr_approve)) {
    $options = [
        "ssl" => [
        "verify_peer"=>false,
        "verify_peer_name"=>false,
        ],
    ];

    $context = stream_context_create($options);
    session_write_close();   // this is the key
    $img = file_get_contents(url($data->recommended_by->signature), false, $context);

    $pdf->Image('@' . $img,
                $xCoor + (($pageWidth * 0.362) / 3) + 5, $yCoor - 7, 16, 0, 'PNG');
}

$pdf->Cell($pageWidth * 0.04, 5, "", "L", "", "L");
$pdf->Cell($pageWidth * 0.322, 5, $data->recommended_by->name, "B", "", "C");
$pdf->Cell(0, 5, "", "R", "", "L");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.138, 5, "", "LR", "", "L");
$pdf->Cell($pageWidth * 0.362, 5, "", "LR", "", "C");
$pdf->SetFont('Times', 'BI', 10 + ($increaseFontSize * 10));
$pdf->Cell(0, 5, 'Division Chief / PSTD', "LR", "", "C");
$pdf->Ln();

$pdf->Cell($pageWidth * 0.138, 5, "", "BLR", "", "L");
$pdf->Cell($pageWidth * 0.362, 5, "", "BLR", "", "C");
$pdf->Cell(0, 5, "", "BLR", "", "C");
$pdf->Ln();

/* ------------------------------------- End of Doc ------------------------------------- */
