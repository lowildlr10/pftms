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

$deadlineDate = "";

foreach ($data->group_no as $groupNo) {
    $pdf->AddPage();

/* ------------------------------------- Start of Doc ------------------------------------- */

    $pdf->Cell($pageWidth * 0.452, 5, "QTN. NO: " . $data->pr->pr_no . "", 0, '', 'L');
    $pdf->Cell(0, 5, "Date: " . $rfqDate, 0, '', 'R');
    $pdf->Ln();

    //Title
    $pdf->MultiCell(0, '5', 'REQUEST FOR BIDS/QUOTATION', 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('helvetica', '', 10  + ($increaseFontSize * 10));
    $pdf->Cell(0, 5, 'Sir/Madam:');
    $pdf->Ln();
    $pdf->MultiCell(0, 5,
                    "       This is a request for quotation on items enumerated hereunder.".
                    " If you are interested to and in a position to furnish the same, we shall be ".
                    "glad to have your best prices, terms and conditions of delivery.",
                    0, 'L');
    $pdf->Ln(5);

    //Table data
    $pdf->htmlTable($groupNo->table_data);

    //Footer
    $pdf->Cell($pageWidth * 0.5, 5,"                         Terms of Delivery:");
    $pdf->Cell($pageWidth * 0.405, 5,"             Terms of Payment:");
    $pdf->Ln();
    $pdf->Cell($pageWidth * 0.5, 5,"                          _______ Pick-up");
    $pdf->Cell($pageWidth * 0.405, 5,"             _______ After Inspection & Acceptance");
    $pdf->Ln();
    $pdf->Cell($pageWidth * 0.5, 5,"                          _______On-site Delivery");
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(0, 5,"Deadline for Submission: " . $deadlineDate);
    $pdf->Ln(8);

    $pdf->Cell($pageWidth * 0.57,  5, "Very truly yours,");
    $pdf->Cell($pageWidth * 0.19,  5, "Prices quoted above are");
    $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0,  5, "valid until_______ ", 0);
    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->Ln();

    $pdf->Cell($pageWidth * 0.5, 5, '');
    $pdf->Cell($pageWidth * 0.405, 5, "               Certified Correct:", 0, 0, 'L');
    $pdf->Ln();
    $pdf->SetFont('helvetica', 'B', 10  + ($increaseFontSize * 10));
    $pdf->Cell(0, 5,"DEPARTMENT OF SCIENCE AND TECHNOLOGY", 0, 'B', 'L');
    $pdf->Ln(8);

    $pdf->Cell($pageWidth * 0.5, 5," ");
    $pdf->Cell(0, 5,"      ________________________________", 0, 0, 'C');
    $pdf->Ln();
    $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.5, 5, " ");
    $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.405, 5, "    Name of Firm/Company and Address:", 0, 0, 'C');
    $pdf->Ln();
    $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.5, 5, " ");
    $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.405, 5, " ");
    $pdf->Ln();
    $pdf->Cell($pageWidth * 0.5, 5, " ");
    $pdf->Cell($pageWidth * 0.405, 5, " ");
    $pdf->Ln();

    $pdf->SetFont('helvetica', 'B', 11 + ($increaseFontSize * 11));
    $pdf->Cell($pageWidth * 0.405, 5, "".$data->sig_rfq->name."",'B','','C');
    $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.5, 5, "");
    $pdf->Ln();

    $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
    $pdf->Cell($pageWidth * 0.405, 5, "Property & Supply Officer/ PSTD ", 0, 0, 'C');
    $pdf->Cell($pageWidth * 0.1645, 5, "",'','','C');
    $pdf->Cell(0, 5, "Signature over Printed Name of Authorized", 'T', 0, 'L');
    $pdf->Ln();
    $pdf->Cell('83', 5, " ", 0, 0, 'C');
    //$pdf->Cell($pageWidth * 0.452, 5," Representative",0,'','L');
    $pdf->Ln();


    $pdf->Cell(0, 5, "IMPORTANT:", 0);
    $pdf->Ln();
    $pdf->Cell($pageWidth * 0.75, 5, '          Prices should be in ink and clearly quoted. '.
                                       'When offering substitute/equivalent, specify brand. ', 0);
    $pdf->SetFont('helvetica','B', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, 5, 'Submit quotations');
    $pdf->Ln();
    $pdf->Cell(0, 5, 'in a sealed envelope.');
    $pdf->Ln();
    $pdf->SetFont('helvetica','', 10 + ($increaseFontSize * 10));
    $pdf->MultiCell(0, 5, "\n          DOST-CAR office reserves the right to reject any or all bids, ".
                          "to waive any defect therein and accept the offer most ".
                          "advantageous to the DOST-CAR Office.", 0, 'L');
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', 'IB', 10 + ($increaseFontSize * 10));
    $pdf->Cell(0, 5, "Canvassed by:_______________________", 0, 0, 'L');

 /* ------------------------------------- End of Doc ------------------------------------- */

}
