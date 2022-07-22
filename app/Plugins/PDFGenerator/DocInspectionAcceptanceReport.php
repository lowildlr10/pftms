<?php

namespace App\Plugins\PDFGenerator;

class DocInspectionAcceptanceReport extends PDF {
    public function printIAR($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->iar->id;

        $poDate = $data->iar->date_po;
        $iarDate = $data->iar->date_iar;
        $invoiceDate = $data->iar->date_invoice;

        $contentWidth = $pageWidth  - 20;
        $sign1 = strtoupper($data->iar->sig_inspection);
        $sign2 = strtoupper($data->iar->sig_supply);

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

        $this->SetFont('Times', 'B', 14 + ($fontScale * 14));
        $this->Cell(0, 5, "INSPECTION AND ACCEPTANCE REPORT", '', '', 'C');
        $this->Ln();

        $this->Ln(5);

        $this->SetFont('Times', 'IB', 11 + ($fontScale * 11));
        $this->Cell(0, 5, "Fund Cluster : 01", '', '', 'L');
        $this->Ln();

        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell($contentWidth * 0.147, 7, 'Supplier: ', 'TL', 'L', 0, 0, '', '', true);
        $this->MultiCell($contentWidth * 0.47, 7, $data->iar->company_name, 'TR', 'L', 0, 0, '', '', true);
        $this->MultiCell($contentWidth * 0.126, 7, 'IAR No.:', 'T', 'L', 0, 0, '', '', true);
        $this->MultiCell(0, 7, $data->iar->iar_no, 'TR', 'L', 0, 0, '', '', true);
        $this->Ln();

        $this->MultiCell($contentWidth * 0.617, 3, '', 'LR', 'L', 0, 0, '', '', true);
        $this->MultiCell(0, 3, '', 'LR', 'L', 0, 0, '', '', true);
        $this->Ln();

        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell($contentWidth * 0.147, 7, 'PO No./Date : ', 'L', 'L', 0, 0, '', '', true);
        $this->MultiCell($contentWidth * 0.47, 7, $poDate, 'R', 'L', 0, 0, '', '', true);
        $this->SetFont('Times', 'IB', 11 + ($fontScale * 11));
        $this->MultiCell($contentWidth * 0.126, 7, 'Date :', '', 'L', 0, 0, '', '', true);
        $this->SetFont('Times', '', 11);
        $this->MultiCell(0, 7, $iarDate, 'R', 'L', 0, 0, '', '', true);
        $this->Ln();

        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell($contentWidth * 0.263, 7, 'Requisitioning Office/Dept. :', 'L', 'L', 0, 0, '', '', true);
        $this->MultiCell($contentWidth * 0.354, 7, $data->iar->division_name, 'R', 'L', 0, 0, '', '', true);
        $this->MultiCell($contentWidth * 0.126, 7, 'Invoice No. :', '', 'L', 0, 0, '', '', true);
        $this->MultiCell(0, 7, $data->iar->invoice_no, 'R', 'L', 0, 0, '', '', true);
        $this->Ln();

        $this->SetFont('Times', 'IB', 11 + ($fontScale * 11));
        $this->MultiCell($contentWidth * 0.263, 7, 'Responsibility Center Code :', 'L', 'L', 0, 0, '', '', true);
        $this->MultiCell($contentWidth * 0.354, 7, $data->iar->responsibility_center, 'R', 'L', 0, 0, '', '', true);
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell($contentWidth * 0.126, 7, 'Date :', '', 'L', 0, 0, '', '', true);
        $this->MultiCell(0, 7, $invoiceDate, 'R', 'L', 0, 0, '', '', true);
        $this->Ln();

        //Table data
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->htmlTable($data->table_data);

        //Table footer
        $this->htmlTable($data->footer_data);

        $this->SetFont('Times','', 11 + ($fontScale * 11));
        $this->Cell($contentWidth * 0.5, 7, 'Date Inspected : ______________________________', "TLR");
        $this->Cell(0, 7, 'Date Received : ____________________________', "TLR");
        $this->ln();

        $this->Cell($contentWidth * 0.5, 5, '', 'LR');
        $this->Cell(0, 5, 'PO/JO #: ', 'LR');
        $this->ln();

        $this->Cell($contentWidth * 0.0105, 7, '', 'LR');
        $this->Cell($contentWidth * 0.0421, 7, '', "TBLR");
        $this->Cell($contentWidth * 0.4474, 7, "\tInspected, verified and found in order as to", 'LR');
        $this->Cell(0, 7, '', 'LR');
        $this->ln();

        $this->Cell($contentWidth * 0.0105, 7, '', 'L');
        $this->Cell($contentWidth * 0.0421, 7, '', '');
        $this->Cell($contentWidth * 0.4474, 7, "\tquantity and specifications", 'R');
        $this->Cell($contentWidth * 0.0368, 7, '', 'LR');
        $this->Cell($contentWidth * 0.0421, 7, '', "TBLR");
        $this->Cell(0, 7, "\tComplete", 'LR');
        $this->ln();

        $this->Cell($contentWidth * 0.5, 5, '', 'LR');
        $this->Cell(0, 5, '', 'LR');
        $this->ln();

        $this->Cell($contentWidth * 0.0105, 7, '', 'LR');
        $this->Cell($contentWidth * 0.0421, 7, '', "TBLR");
        $this->Cell($contentWidth * 0.4474, 7, "\tNot in conformity as to quantity and ", 'R');
        $this->Cell($contentWidth * 0.0368, 7, '', 'LR');
        $this->Cell($contentWidth * 0.0421, 7, '', "TBLR");
        $this->Cell(0, 7, "\tPartial  (pls. specify quantity)", 'LR');
        $this->ln();

        $this->Cell($contentWidth * 0.0105, 7, '', 'L');
        $this->Cell($contentWidth * 0.0421, 7, '', '');
        $this->Cell($contentWidth * 0.4474, 7, "\tspecifications", 'R');
        $this->Cell(0, 7, '', 'LR');
        $this->ln();

        $this->SetFont('Times','', 12 + ($fontScale * 12));
        $this->Cell($contentWidth * 0.5, 3, '', 'LR', '', 'C');
        $this->Cell(0, 3, '', 'LR', '', 'C');
        $this->ln();

        $this->SetFont('Times','B', 12 + ($fontScale * 12));
        $this->Cell($contentWidth * 0.5, 5, strtoupper($sign1), 'LR', '', 'C');
        $this->Cell(0, 5, strtoupper($sign2), 'LR', '', 'C');
        $this->ln();

        $this->SetFont('Times','', 12 + ($fontScale * 12));
        $this->Cell($contentWidth * 0.5, 8, "Inspection Officer/Inspection Committee", 'BLR', '', 'C');
        $this->Cell(0, 8, "Supply and/or Property Custodian", 'BLR', '', 'C');
        $this->ln();

        $this->SetFont('Times','', 11 + ($fontScale * 11));
        $this->Cell($contentWidth * 0.5, 8, 'Remarks/Recommendation: ', 'LR', '', 'L');
        $this->Cell(0, 8, '', 'LR');
        $this->ln();

        $this->SetFont('Times','', 12 + ($fontScale * 12));
        $this->Cell($contentWidth * 0.5, 5, '___________________________________________ ', 'LR', '', 'L');
        $this->Cell(0, 5, '', 'LR');
        $this->ln();

        $this->Cell($contentWidth * 0.5, 6, '', 'BLR');
        $this->Cell(0, 6, '', 'BLR');
        $this->ln();

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
