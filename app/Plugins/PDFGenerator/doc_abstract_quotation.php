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

foreach ($data->abstract_items as $abstract) {
    $bidderCount = $abstract->bidder_count;
    $totalWidthDisplay = $pageHeight - 20;
    $totalWidth1 = $totalWidthDisplay * 0.83;
    $totalWidth2 = $totalWidthDisplay * 0.17;
    $bidderTotalWidth = $totalWidth1 * 0.71;

    if ($bidderCount != 0) {
        $bidderWidth = $bidderTotalWidth / $bidderCount;
    } else {
        $bidderWidth = $bidderTotalWidth / 3;
    }  

    if ($bidderCount > 0) {
        $pdf->AddPage();

/* ------------------------------------- Start of Doc ------------------------------------- */

        // TABLE TITLE
        $pdf->SetFont('helvetica', 'B', 10 + ($increaseFontSize * 10));
        $pdf->Cell($pageHeight * 0.948, 5, 'ABSTRACT OF QUOTATION', "", "",'C');
        $pdf->Ln(10);

        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Row group
        $pdf->SetFont('helvetica', 'BI', 9 + ($increaseFontSize * 9));
        $pdf->MultiCell($totalWidth1 / 2, 5.25, "Purchase Request No.: $prNo \nPMO/End-User : $endUser", "LTB", "L", "");
        $pdf->SetXY($x + ($totalWidth1 / 2), $y);
        $pdf->MultiCell($totalWidth1 / 2, 5.25, "Date Prepared: $abstractDate " . 
                                             "\n" .
                                             "Mode of Procurement : $modeProcurement ", "RTB", "R", "");
        $pdf->SetXY($x + $totalWidth1, $y);
        $pdf->SetFont('helvetica', 'BI', 8 + ($increaseFontSize * 8));
        $pdf->setCellHeightRatio(0.95);
        $pdf->MultiCell(0, 3.5, "based on the canvasses submitted,\n WE, the members of the " . 
                                            "Bids and\n Awards Committee (BAC) ", "TR", "C", "");

        // Row group
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell($totalWidth1 * 0.04, 4, '', 'LR', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.13, 4, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
        $pdf->SetFont('helvetica', 'BI', 8 + ($increaseFontSize * 8));
        $pdf->Cell($bidderTotalWidth, 3.6, "BIDDER'S QUOTATION AND OFFER", 'RB', '', 'C');
        $pdf->SetFont('helvetica', 'BI', 9 + ($increaseFontSize * 9));
        $pdf->MultiCell(0, 3.5, "RECOMMEND the following", "R", "C", "");


        // Row group
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'ITEM', 'LR', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'QTY', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'UNIT', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.13, 3.6, 'P A R T I C U L A R S', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'ABC', 'R', '', 'C');

        for ($bidCount = 1; $bidCount <= $bidderCount; $bidCount++) { 
            $pdf->Cell($bidderWidth, 3.6, '', 'R', '', 'C');
        }

        $pdf->SetFont('helvetica', 'BI', 9 + ($increaseFontSize * 9));
        $pdf->MultiCell(0, 3.5, "items to be AWARDED as", "R", "C", "");

        // Row group
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'NO.', 'LR', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.13, 3.6, '', 'R', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '(Unit', 'R', '', 'C');

        foreach ($abstract->suppliers as $list) {
            $strLength = strlen($list->company_name);
            $bidderLists[] = array('', $list->company_name);

            if ($bidderCount == 3) {
                if ($strLength > 30) {
                    $pdf->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 30) . 
                               '...', 'RB', '', 'C');
                } else {
                    $pdf->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                }
            } else if ($bidderCount == 4) {
                if ($strLength > 20) {
                    $pdf->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 20) . 
                               '...', 'RB', '', 'C');
                } else {
                    $pdf->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                }
            } else if ($bidderCount >= 5) {
                if ($strLength > 15) {
                    $pdf->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 15) . 
                               '...', 'RB', '', 'C');
                } else {
                    $pdf->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                }
            }
        }

        $pdf->SetFont('helvetica', 'BI', 9 + ($increaseFontSize * 9));
        $pdf->MultiCell(0, 3.5, "follows:", "RB", "C", "");

        // Row group
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'LRB', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'RB', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, '', 'RB', '', 'C');
        $pdf->Cell($totalWidth1 * 0.13, 3.6, '', 'RB', '', 'C');
        $pdf->Cell($totalWidth1 * 0.04, 3.6, 'Cost)', 'RB', '', 'C');

        for ($bidCount = 1; $bidCount <= $bidderCount; $bidCount++) { 
            if ($bidderCount == 3) {
                $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
            } else if ($bidderCount == 4) {
                $pdf->SetFont('helvetica', '', 7 + ($increaseFontSize * 7));
            } else if ($bidderCount >= 5) {
                $pdf->SetFont('helvetica', '', 5.5 + ($increaseFontSize * 5.5));
            }

            $pdf->Cell($bidderWidth * 0.25, 3.6, 'Unit Cost', 'RB', '', 'C');
            $pdf->Cell($bidderWidth * 0.25, 3.6, 'Total Cost', 'RB', '', 'C');

            if ($bidderCount == 3) {
                $pdf->SetFont('helvetica', 'BI', 8 + ($increaseFontSize * 8));
            } else if ($bidderCount == 4) {
                $pdf->SetFont('helvetica', 'BI', 7 + ($increaseFontSize * 7));
            } else if ($bidderCount >= 5) {
                $pdf->SetFont('helvetica', 'BI', 5.5 + ($increaseFontSize * 5.5));
            }
            
            $pdf->Cell($bidderWidth * 0.5, 3.6, 'Specification', 'RB', '', 'C');
        }

        $pdf->Cell(0, 3.6, '', 'RB', '', 'C');
        $pdf->Ln();

        //Table data
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->htmlTable($abstract->table_data);

        $pdf->Ln(2.5);
        $pdf->SetFont('helvetica', '', 8 + ($increaseFontSize * 8));
        $pdf->Cell(0, 0, "We hereby certify that we have witnessed the opening of bids/quotations and that the prices/quotations contained herein are the true and correct.");
        $pdf->Ln(5);

        // Recommendation
        $pdf->SetFont('helvetica', 'BI', 9 + ($increaseFontSize * 9));
        $pdf->Cell($totalWidth1 + $totalWidth2, 5, "Recommendation:", '', 0, 'L', 0);
        $pdf->Ln(5);

        $pdf->Cell(0, 2, "", 'B', 1, 'L', 0);
        $pdf->Cell(0, 2, "", 'B', 1, 'L', 0);
        $pdf->Cell(0, 2, "", 'B', 1, 'L', 0);
        $pdf->Cell(0, 2, "", 'B', 1, 'L', 0);
        $pdf->Ln(5);

        // Bids and Committee awardee
        $pdf->SetFont('helvetica', 'B', 10 + ($increaseFontSize * 10));
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(5); // LINE BREAK

        $pdf->Cell($totalWidthDisplay * 0.83, 5, "BIDS AND AWARDS COMITTEE:", '', 0, 'L', 0);
        $pdf->SetFont('helvetica', '', 10 + ($increaseFontSize * 10));
        $pdf->Cell($totalWidthDisplay * 0.32, 5, "", '', 0, 'L', 0);
        $pdf->Ln(); // LINE BREAK
        $pdf->SetFont('helvetica', 'B', 9 + ($increaseFontSize * 9));
        $pdf->Cell(0, 8, " ", '', 0, 'L', 0);
        $pdf->Ln(); // LINE BREAK


        $signatoryIDs = [];

        foreach ($abstractSigs as $absSigCtr => $absSig) {
            if (!empty($absSig)) {
                $signatoryIDs[] = $absSigCtr;
            }
        }

        $signatoryCount = count($signatoryIDs);
        $columWidth = ($totalWidthDisplay - 15) / $signatoryCount;
        $columWidthSpace = 15 / 12;

        foreach ($abstractSigs as $absSigCtr => $absSig) {
            if (!empty($absSig)) {
                $pdf->Cell($columWidthSpace, 5);
                $pdf->Cell($columWidth, 5, $absSig, 'B', 0, 'C');
                $pdf->Cell($columWidthSpace, 5);
            }
        }

        $pdf->SetFont('helvetica', '', 9 + ($increaseFontSize * 9));
        $pdf->Ln(); // LINE BREAK

        foreach ($sigPosition as $titleCtr => $title) {
            if (in_array($titleCtr, $signatoryIDs)) {
                $pdf->Cell($columWidthSpace, 5);
                $pdf->Cell($columWidth, 5, $title, 0, 0, 'C');
                $pdf->Cell($columWidthSpace, 5);
            }
        }
    }
    
 /* ------------------------------------- End of Doc ------------------------------------- */

}