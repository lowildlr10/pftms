<?php

namespace App\Plugins\PDFGenerator;

class DocJobOrder extends PDF {
    public function printJobOrder($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->jo->id;

        $contentWidth = $pageWidth  - 20;
        $joDate = $data->jo->date_po;
        $deptName = strtoupper($data->jo->sig_department);
        $appName = strtoupper($data->jo->sig_approval);
        $fundsName = strtoupper($data->jo->sig_funds_available);

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

        //Add a page
        $this->AddPage();

        /* ------------------------------------- Start of Doc ------------------------------------- */

        //Title
        $this->SetFont('Times', 'B', 14 + ($fontScale * 14));
        $this->Cell(0, '8', "JOB / WORK ORDER", 0, 0, 'C');
        $this->Ln();

        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell($contentWidth * 0.18,'5',"JOB ORDER NO:", 0, 'L');
        $this->SetFont('Times','B', 11 + ($fontScale * 11));
        $this->Cell(0,'5', $data->jo->po_no, 0,'L');

        $this->Ln();

        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell($contentWidth * 0.18, '5', "DATE:", 0, 'L');
        $this->SetFont('Times','', 11 + ($fontScale * 11));
        $this->Cell(0, '5', $joDate, 0, 'L');

        $this->Ln();

        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell($contentWidth * 0.18, '5', "TO:", 0, 'L');
        $this->SetFont('Times','B', 11 + ($fontScale * 11));
        $this->Cell(0, '5', $data->jo->company_name, 0, 'L');

        $this->Ln();

        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell($contentWidth * 0.18, '5', "ADDRESS:", 0, 'L');
        $this->SetFont('Times','', 11 + ($fontScale * 11));
        $this->MultiCell(0, '5', $data->jo->address, 0, 'L');

        $this->Ln(5);

        $this->SetFont('Times','', 11 + ($fontScale * 11));

        $this->MultiCell(0 ,'5',
            "Sir/Madam:\n\nIn connection with the existing regulations," .
            " you are hereby authorized to undertake the indicated job/work below:",
            0, 'L');

        $this->Ln(5);

        //Table data
        $this->htmlTable($data->jo->table_data);

        //Footer data
        $this->SetFont('Times','', 11 + ($fontScale * 11));
        $this->Cell(0, '5',"Completion/Delivery : within the specified date of delivery", 0, 'L');

        $this->Ln();

        $this->SetFont('Times', 'IB', 11 + ($fontScale * 11));
        $this->Cell($contentWidth * 0.16, '5', "Place of Delivery:", 0, 'L');
        $this->SetFont('Times', 'B', 11 + ($fontScale * 11));
        $this->Cell(0, '5', "DOST-CAR ", 0, 'L');

        $this->Ln();

        $this->SetFont('Times', 'IB', 11 + ($fontScale * 11));
        $this->Cell($contentWidth * 0.16,'5', "Date of Delivery:", 0, 'L');
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->Cell($contentWidth * 0.16,'5', $data->jo->date_delivery, 0, 'L');
        $this->SetFont('Times', 'IB', 11 + ($fontScale * 11));
        $this->Cell($contentWidth * 0.40,'5', "Payment Term:", 0, 0, 'R');
        $this->SetFont('Times', 'UB', 11 + ($fontScale * 11));
        $this->Cell(0, '5', "After inspection and acceptance", 0, 0, 'R');

        $this->Ln(10);

        $this->SetFont('Times','', 11 + ($fontScale * 11));
        $this->MultiCell(0, '5',
                "This order is authorized by the DEPARTMENT OF SCIENCE AND TECHNOLOGY, ".
                "Cordillera Administrative Region under DR. NANCY A. BANTOG, Regional ".
                "Director in the amount not to exceed " . $data->jo->amount_words .
                " (Php " . $data->jo->grand_total . "). The cost of this WORK ORDER will ".
                "be charged against DOST-CAR after work has been completed.",
                0, 'C');

        $this->Ln(5);

        $this->SetFont('helvetica','IB', 10 + ($fontScale * 10));
        $this->MultiCell(0, '5',
                " In case of failure to make the full delivery within time specified above, " .
                "a penalty of one-tenth (1/10) of one percent for everyday of delay shall be imposed.",
                0, 'C');

        $this->Ln(10);

        $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
        $this->MultiCell(0, '5',
                        "Please submit your bill together with the original of this ".
                        "JOB/WORK ORDER to expedite payment.",
                        0, 'C');

        $this->Ln(5);

        $this->SetFont('Times','I', 10 + ($fontScale * 10));
        $this->MultiCell($contentWidth,'5', "Very truly yours,", 0, 'L');

        $this->Ln(5);

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell($contentWidth * 0.5,'5', "Requisitioning Office/Dept.:", 0, 'L');
        $this->Cell(0, '5', "APPROVED:", 0, 'L');

        $this->Ln(13);

        $this->SetFont('Times', 'B', 10 + ($fontScale * 10));
        $this->Cell($contentWidth * 0.55, '5', strtoupper($deptName), 0, 'L');
        $this->Cell($contentWidth * 0.45, '5', strtoupper($appName), 0, 0, 'L');

        $this->Ln();
        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell($contentWidth * 0.55, '5', "Authorized Signatory", 0, 'L');
        $this->Cell($contentWidth * 0.45, '5', "Authorized Signatory", 0, 0, 'L');

        $this->Ln(13);

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell(0, '5', "Funds Available:", 0, 'L');

        $this->Ln(13);

        $this->SetFont('Times', 'B', 10 + ($fontScale * 10));
        $this->Cell($contentWidth, '5', strtoupper($fundsName), 0, 'L');
        $this->Ln();
        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell(0, '5', "             Authorized Signatory", 0, 'L');

        $this->Ln(13);

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->MultiCell(0, '5', "JOB/WORK ORDER RECEIVED BY: \n__________________________________", 0, 'L');

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
