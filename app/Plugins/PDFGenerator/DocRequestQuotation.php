<?php

namespace App\Plugins\PDFGenerator;

class DocRequestQuotation extends PDF {
    public function printRequestQuotation($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

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

            $this->Cell($pageWidth * 0.452, 5, "QTN. NO: " . $data->pr->pr_no . "", 0, '', 'L');
            $this->Cell(0, 5, "Date: " . $data->rfq->date_canvass, 0, '', 'R');
            $this->Ln();

            //Title
            $this->MultiCell(0, '5', 'REQUEST FOR BIDS/QUOTATION', 0, 'C');
            $this->Ln();

            $this->SetFont('helvetica', '', 10  + ($fontScale * 10));
            $this->Cell(0, 5, 'Sir/Madam:');
            $this->Ln();
            $this->MultiCell(0, 5,
                            "       This is a request for quotation on items enumerated hereunder.".
                            " If you are interested to and in a position to furnish the same, we shall be ".
                            "glad to have your best prices, terms and conditions of delivery.",
                            0, 'L');
            $this->Ln(5);

            //Table data
            $this->htmlTable($groupNo->table_data);

            //Footer
            $this->Cell($pageWidth * 0.5, 5,"                         Terms of Delivery:");
            $this->Cell($pageWidth * 0.405, 5,"             Terms of Payment:");
            $this->Ln();
            $this->Cell($pageWidth * 0.5, 5,"                          _______ Pick-up");
            $this->Cell($pageWidth * 0.405, 5,"             _______ After Inspection & Acceptance");
            $this->Ln();
            $this->Cell($pageWidth * 0.5, 5,"                          _______On-site Delivery");
            $this->Ln();
            $this->Ln();
            $this->Cell(0, 5,"Deadline for Submission: " . $deadlineDate);
            $this->Ln(8);

            $this->Cell($pageWidth * 0.57,  5, "Very truly yours,");
            $this->Cell($pageWidth * 0.19,  5, "Prices quoted above are");
            $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
            $this->Cell(0,  5, "valid until_______ ", 0);
            $this->SetFont('helvetica','', 10 + ($fontScale * 10));
            $this->Ln();

            $this->Cell($pageWidth * 0.5, 5, '');
            $this->Cell($pageWidth * 0.405, 5, "               Certified Correct:", 0, 0, 'L');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10  + ($fontScale * 10));
            $this->Cell(0, 5,"DEPARTMENT OF SCIENCE AND TECHNOLOGY", 0, 'B', 'L');
            $this->Ln(8);

            $this->Cell($pageWidth * 0.5, 5," ");
            $this->Cell(0, 5,"      ________________________________", 0, 0, 'C');
            $this->Ln();
            $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.5, 5, " ");
            $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.405, 5, "    Name of Firm/Company and Address:", 0, 0, 'C');
            $this->Ln();
            $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.5, 5, " ");
            $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.405, 5, " ");
            $this->Ln();
            $this->Cell($pageWidth * 0.5, 5, " ");
            $this->Cell($pageWidth * 0.405, 5, " ");
            $this->Ln();

            $this->SetFont('helvetica', 'B', 11 + ($fontScale * 11));
            $this->Cell($pageWidth * 0.405, 5, "".$data->sig_rfq->name."",'B','','C');
            $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.5, 5, "");
            $this->Ln();

            $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
            $this->Cell($pageWidth * 0.405, 5, "Property & Supply Officer/ PSTD ", 0, 0, 'C');
            $this->Cell($pageWidth * 0.1645, 5, "",'','','C');
            $this->Cell(0, 5, "Signature over Printed Name of Authorized", 'T', 0, 'L');
            $this->Ln();
            $this->Cell('83', 5, " ", 0, 0, 'C');
            //$this->Cell($pageWidth * 0.452, 5," Representative",0,'','L');
            $this->Ln();


            $this->Cell(0, 5, "IMPORTANT:", 0);
            $this->Ln();
            $this->Cell($pageWidth * 0.75, 5, '          Prices should be in ink and clearly quoted. '.
                                            'When offering substitute/equivalent, specify brand. ', 0);
            $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
            $this->Cell(0, 5, 'Submit quotations');
            $this->Ln();
            $this->Cell(0, 5, 'in a sealed envelope.');
            $this->Ln();
            $this->SetFont('helvetica','', 10 + ($fontScale * 10));
            $this->MultiCell(0, 5, "\n          DOST-CAR office reserves the right to reject any or all bids, ".
                                "to waive any defect therein and accept the offer most ".
                                "advantageous to the DOST-CAR Office.", 0, 'L');
            $this->Ln(5);

            $this->SetFont('helvetica', 'IB', 10 + ($fontScale * 10));

            if ($data->canvassed_by->name) {
                $this->Cell($pageWidth * 0.13, 5, "Canvassed by: ", 0, 0, 'L');
                $this->SetFont('helvetica', 'IU', 10 + ($fontScale * 10));
                $this->Cell(0, 5, ' '.$data->canvassed_by->name.' ', 0, 0, 'L');
            } else {
                $this->Cell(0, 5, "Canvassed by:_______________________", 0, 0, 'L');
            }

        /* ------------------------------------- End of Doc ------------------------------------- */
        }
    }
}
