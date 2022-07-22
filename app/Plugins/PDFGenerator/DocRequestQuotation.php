<?php

namespace App\Plugins\PDFGenerator;

class DocRequestQuotation extends PDF {
    public function printRequestQuotation($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->rfq->id;

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(10, 35, 10);
        $this->SetHeaderMargin(10);

        //Set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //Set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //Set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $this->setLanguageArray($l);
        }

        //Set default font subsetting mode
        $this->setFontSubsetting(true);

        /* ------------------------------------- End of Config ------------------------------------- */

        $deadlineDate = "";

        foreach ($data->group_no as $groupNo) {
            $this->AddPage();

        /* ------------------------------------- Start of Doc ------------------------------------- */

            $this->Ln(5);

            $this->SetFont('helvetica', '', 10  + ($fontScale * 10));
            $this->Cell($pageWidth * 0.452, 5, "QTN. NO: " . $data->pr->pr_no . "", 0, '', 'L');
            $this->Cell(0, 5, "Date: " . $data->rfq->date_canvass, 0, '', 'R');
            $this->Ln(10);

            //Title
            $this->MultiCell(0, '5', 'REQUEST FOR BIDS/QUOTATION', 0, 'C');
            $this->Ln();

            $this->Cell(0, 5, 'Sir/Madam:');
            $this->Ln();
            $this->MultiCell(0, 5,
                            "             This is a request for quotation on items enumerated hereunder. If ".
                            "you are interested to and in a position to furnish the same, we shall be glad ".
                            "to have your best prices, terms and conditions of delivery.",
                            0, 'L');
            $this->Ln(5);

            //Table data
            $this->htmlTable($groupNo->table_data);

            //Footer
            $this->SetFont('helvetica', 'BI', 10  + ($fontScale * 10));
            $this->Cell($pageWidth * 0.5, 5, "     Delivery Term (Complete)");
            $this->SetFont('helvetica', '', 10  + ($fontScale * 10));
            $this->Cell($pageWidth * 0.068, 5, "Payment");
            $this->SetFont('helvetica', 'BI', 10  + ($fontScale * 10));
            $this->Cell($pageWidth * 0, 5, "Term");
            $this->Ln();

            $this->SetFont('helvetica', '', 10  + ($fontScale * 10));
            $this->Cell($pageWidth * 0.5, 5, "________ Pick-up");
            $this->Cell($pageWidth * 0.405, 5, "________ After Inspection & Acceptance");
            $this->Ln();

            $this->Cell($pageWidth * 0.205, 5, "________  On-site delivery");
            $this->SetFont('helvetica', 'BI', 10  + ($fontScale * 10));
            $this->Cell(0, 5, "to _____________");
            $this->Ln(8);

            $this->SetFont('helvetica', 'BI', 10  + ($fontScale * 10));
            $this->Cell($pageWidth * 0.09, 5, "Warranty: ",);
            $this->SetFont('helvetica', '', 10  + ($fontScale * 10));
            $this->Cell(0, 5, "___________________________________");
            $this->Ln();

            $this->SetFont('helvetica', 'BI', 10  + ($fontScale * 10));
            $this->Cell($pageWidth * 0.184, 5, "After Sales Service/s: ",);
            $this->SetFont('helvetica', '', 10  + ($fontScale * 10));
            $this->Cell(0, 4, "_________________________");
            $this->Ln();

            $this->Cell(0, 4, "____________________________________________");
            $this->Ln();

            $this->Cell(0, 4, "____________________________________________");
            $this->Ln(12);

            $this->Cell(0, 5,"Deadline for Submission: _____________________" . $deadlineDate);
            $this->Ln(15);

            $this->Cell($pageWidth * 0.55,  5, "Very truly yours,");
            $this->Cell($pageWidth * 0.188,  5, "Prices quoted above are");
            $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.045,  5, "valid", 0);
            $this->SetFont('helvetica','', 10 + ($fontScale * 10));
            $this->Cell(0,  5, "until _________", 0);
            $this->Ln();

            $this->Cell($pageWidth * 0.55, 5, '');
            $this->Cell(0, 5, "and Certified Correct:", 0, 0, 'L');
            $this->Ln();

            $this->SetFont('helvetica', 'B', 10  + ($fontScale * 10));
            $this->Cell(0, 5,"DEPARTMENT OF SCIENCE AND TECHNOLOGY", 0, 'B', 'L');
            $this->Ln(8);

            $this->Cell($pageWidth * 0.55, 5," ");
            $this->Cell(0, 5,"______________________________");
            $this->Ln();


            $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.55, 5, " ");
            $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
            $this->Cell(0, 5, "Name of Firm/Company and Address:", 0, 0);
            $this->Ln(10);

            $this->SetFont('helvetica', 'B', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.39, 5, strtoupper($data->sig_rfq->name), 'B', 0, 'C');
            $this->Cell($pageWidth * 0.16, 5, '');
            $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
            $this->Cell(0, 5, "");
            $this->Ln();

            $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.39, 5, "Property & Supply Officer/ PSTD", 0, 0, 'C');
            $this->Cell($pageWidth * 0.16, 5, '');
            $this->Cell(0, 5, "Signature over Printed Name of Authorized", 'T');
            $this->Ln();

            $this->Cell($pageWidth * 0.55, 5, "");
            $this->Cell(0, 5, "Representative");
            $this->Ln(12);

            $this->Cell(0, 5, "IMPORTANT:", 0);
            $this->Ln();

            $html = '<span style="text-align:justify;">&nbsp;&nbsp;&nbsp;&nbsp;'.
                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prices should be in ink and clearly quoted. '.
                    "When offering substitute/ equivalent, specify brand. <b>Submit quotations in ".
                    "a sealed envelope.</b></span>";
            $this->writeHTML($html, true, 0, true, true);
            $this->Ln();

            $html = '<span style="text-align:justify;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DOST-CAR office reserves the right to reject '.
                    "any or all bids, to waive any defect therein and accept the offer most advantageous ".
                    "to the DOST-CAR Office.</span>";
            $this->writeHTML($html, true, 0, true, true);

            /*
            $this->Cell($pageWidth * 0.77, 5,
                        "           Prices should be in ink and clearly quoted.  ".
                        "When offering substitute/ equivalent, specify brand.", 0, 0, 'J');
            $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
            $this->Cell(0, 5, 'Submit', 0, 0, 'R');
            $this->Ln();

            $this->Cell(0, 5, 'quotations in a sealed envelope.');
            $this->Ln();

            $this->SetFont('helvetica','', 10 + ($fontScale * 10));
            $this->MultiCell(0, 5, "\n          DOST-CAR office reserves the right to reject any or all bids, ".
                                "to waive any defect therein and accept the offer most ".
                                "advantageous to the DOST-CAR Office.", 0, 'L');*/
            $this->Ln(5);

            if ($data->canvassed_by->name) {
                $this->Cell($pageWidth * 0.13, 5, "Canvassed by: ", 0, 0, 'L');
                $this->SetFont('helvetica', 'U', 10 + ($fontScale * 10));
                $this->Cell(0, 5, strtoupper($data->canvassed_by->name), 0, 0, 'L');
            } else {
                $this->Cell(0, 5, "Canvassed by:_______________________", 0, 0, 'L');
            }

        /* ------------------------------------- End of Doc ------------------------------------- */
        }
    }
}
