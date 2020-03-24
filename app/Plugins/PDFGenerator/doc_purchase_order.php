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
$pdf->SetFont('Times','B', 14 + ($increaseFontSize * 14));
$pdf->MultiCell(0, 5, "PURCHASE ORDER", '', 'C');

$pdf->ln(3);

$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));

$_x1 = $pdf->GetX();
$_y1 = $pdf->GetY();

$_x = $pdf->GetX();
$_y = $pdf->GetY();

$x1_1 = $_x;
$x1_2 = $_x + $pageWidth * 0.548;
$x1_3 = $_x + $pageWidth * 0.905;

//Table header 1
$pdf->MultiCell($pageWidth * 0.0857, '7', "Supplier: ", "TL");
$pdf->SetXY($_x + $pageWidth * 0.0857, $_y);
$pdf->SetFont('Times', 'B', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($pageWidth * 0.462, '6', $data->po->company_name, "T", "L");
$_x2 = $pdf->GetX();
$_y2 = $pdf->GetY();
$pdf->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462), $_y);
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($pageWidth * 0.1, '7', 'P.O. No. :', "TL");
$pdf->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462) + ($pageWidth * 0.1), $_y);
$pdf->SetFont('Times', 'B', 11 + ($increaseFontSize * 11));
$pdf->MultiCell(0, '7', $data->po->po_no, "TR");

$_x = $pdf->GetX();
$_y = $pdf->GetY();

$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($pageWidth * 0.0857, '5', "Address: ", "");
$pdf->SetXY($_x + $pageWidth * 0.0857, $_y);
$pdf->MultiCell($pageWidth * 0.462, '5', $data->po->address, "", 'L');
$_yTemp = $pdf->GetY();
$pdf->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462), $_y);
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell($pageWidth * 0.1, '5', 'Date  : ', "");
$pdf->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462) + ($pageWidth * 0.1), $_y);
$pdf->SetFont('Times', 'B', 11 + ($increaseFontSize * 11));
$pdf->MultiCell(0, '5', $poDate, "", "L");

$_x = $pdf->GetX();
$_yTemp += 2;

$x2_1 = $_x;
$x2_2 = $_x + $pageWidth * 0.548;
$x2_3 = $_x + $pageWidth * 0.905;

$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->SetXY($_x, $_yTemp);
$pdf->MultiCell('115', '7', "TIN: ________________________________________________", "L");
$pdf->SetXY($_x + $pageWidth * 0.548, $_yTemp);
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell('43', '7', 'Mode of Procurement:', 0, 'L');
$pdf->SetXY($_x + $pageWidth * 0.752, $_yTemp);
$pdf->SetFont('Times', 'B', 11 + ($increaseFontSize * 11));
$pdf->MultiCell(0, '7', $data->po->mode, 0, 'L');

$pdf->Line($_x1, $_y1, $x2_1, $_yTemp);
$pdf->Line($x1_2 - 0.06, $_y2 - 5, $x2_2 - 0.06, $_yTemp + 7);
$pdf->Line($x1_3 - 0.06, $_y2 - 5, $x2_3 - 0.06, $_yTemp + 7);

$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->Cell(0, '7', "Gentlemen:", "RLT");
$pdf->ln();
$pdf->Cell(0, '11', "                Please furnish this Office the following articles" .
                        " subject to the terms and conditions contained herein:", "RLB");
$pdf->ln();

//Table header 2
$pdf->htmlTable($data->po->table_header);

//Table data
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->htmlTable($data->po->table_data);

//Table footer
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->MultiCell(0, '5',
                "\n\t\t\t\t\t In case of failure to make the full delivery within the time " .
                "specified above, a penalty of one-tenth (1/10) of one \n" .
                "percent for every day of delay shall be imposed on the undelivered item/s.", 'LR', 'L');
$pdf->Cell(0, '5', '','LR');
$pdf->Ln();

$pdf->Cell($pageWidth * 0.0524,'5',' ','L');
$pdf->Cell($pageWidth * 0.229,'5','Conforme:','');
$pdf->Cell(0,'5',"Very Truly Yours,",'R','','C');
$pdf->Ln();

$pdf->Cell(0,'10',' ','LR');

$pdf->Ln();

$pdf->SetFont('Times', 'B', 11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.0952,'5','','L');
$pdf->Cell($pageWidth * 0.424,'5','___________________________________', '','','L');
$pdf->Cell(0,'5',"". $appName ."",'R','','C');

$pdf->Ln();

$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.0952,'5','','L');
$pdf->Cell($pageWidth * 0.438,'5',"\t Signature over Printed Name of Supplier", '','','L');
$pdf->SetFont('Times', 'BI', 11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.119,'5',"Regional Director",'','','C');
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->Cell(0,'5',"or Authorized Representative",'R','','C');

$pdf->Ln();

$pdf->Cell($pageWidth * 0.0952,'5','','L');
$pdf->Cell($pageWidth * 0.5,'5',"\t\t\t\t ______________________________",'','','L');
$pdf->Cell(0,'5',"",'R','','C');

$pdf->Ln();

$pdf->Cell($pageWidth * 0.238,'5','','L');
$pdf->Cell($pageWidth * 0.45238,'5',"Date",'','','L');
$pdf->Cell(0,'5','','R');

$pdf->Ln();

$pdf->Cell(0,'5','','LR');

$pdf->Ln();

$pdf->SetFont('Times', 'BI', 10 + ($increaseFontSize * 10));
$pdf->MultiCell(0, '5',
                "Please provide your bank details (account name, ".
                "account number, business name) to facilitate payment ".
                "processing after delivery. Landbank is preferred.", 'LRB', 'L');

$pdf->Cell($pageWidth * 0.45238,'5','','L');
$pdf->Cell(0,'5','','LR');

$pdf->Ln();

$pdf->SetFont('Times', 'IB', 11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.45238,'5','Fund Cluster : 01','L','','L');
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->Cell(0,'5',"ORS/BURS No. : _____________________________",'LR','','L');

$pdf->Ln();

$pdf->Cell($pageWidth * 0.45238,'5',"Funds Available : ____________________________",'L','','L');
$pdf->Cell(0,'5',"Date of the ORS/BURS : _______________________",'LR','','L');

$pdf->Ln();

$pdf->Cell($pageWidth * 0.45238,'5','','L');
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.0857,'5',"Amount : ",'L','','L');
$pdf->SetFont('Times','U',11 + ($increaseFontSize * 11));

if ($data->po->grand_total) {
    $pdf->Cell(0, '5', "Php " . $data->po->grand_total, 'R', '', 'L');
} else {
    $pdf->Cell(0, '5', "Php 0.00", 'R', '', 'L');
}

$pdf->Ln();

$pdf->Cell($pageWidth * 0.45238,'5','','L');
$pdf->Cell(0,'5','','LR');

$pdf->Ln();

$pdf->SetFont('Times','BU',11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.45238,'5',"".strtoupper($deptName)."",'L','','C');
$pdf->Cell(0,'5','','LR');
$pdf->SetFont('Times', '', 11 + ($increaseFontSize * 11));

$pdf->Ln();

$pdf->SetFont('Times','I',11 + ($increaseFontSize * 11));
$pdf->Cell($pageWidth * 0.45238,'5','Signature over Printed Name of Chief','LR','','C');
$pdf->Cell(0,'5','','R','L');

$pdf->Ln();

$pdf->Cell($pageWidth * 0.45238,'5','Accountant/Head of Accounting Division/Unit ','BL','','C');
$pdf->Cell(0,'5','','BLR','L');

$pdf->Ln();

/* ------------------------------------- End of Doc ------------------------------------- */
