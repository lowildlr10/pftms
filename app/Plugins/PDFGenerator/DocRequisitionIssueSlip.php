<?php

namespace App\Plugins\PDFGenerator;

class DocRequisitionIssueSlip extends PDF {
    public function printRIS($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->id;

        $data->fund_cluster = $data->fund_cluster ? $data->fund_cluster : '01';
        $data->responsibility_center = $data->responsibility_center ? $data->responsibility_center : '19 001 03000 14';

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
        $this->Cell(0, "5", "REQUISITION AND ISSUE SLIP", "", "", "C");
        $this->Ln(10);

        $this->SetFont('Times', 'IB', 10 + ($fontScale * 10));
        $this->Cell(0, "5", "Fund Cluster : 01", "", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.55, "2", "", "TLR", "", "L");
        $this->Cell(0, "2", "", "TR", "", "L");
        $this->Ln();

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.55, "7", "Division : " . $data->division, "LR", "", "L");
        $this->SetFont('Times', 'IB', 10 + ($fontScale * 10));
        $this->Cell(0, "7", "Responsibility Center Code : $data->responsibility_center", "R", "", "L");
        $this->Ln();

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.55, "7", "Office : " . $data->office, "LR", "", "L");
        $this->Cell(0, "7", "RIS No : " . $data->inventory_no, "R", "", "L");
        $this->Ln();

        //Table data
        $this->htmlTable($data->table_data);

        //$this->Cell(0, 5, "Purpose: " . $data->purpose, 1, "", "L");
        $this->MultiCell(0, 5, 'Purpose: ' . $data->purpose, 'BLR', 'L');
        //$this->Ln(0);

        $this->Cell($pageWidth * 0.16, "7", "", "LR", "", "L");
        $this->Cell($pageWidth * 0.187, "7", "Requested by:", "RB", "", "L");
        $this->Cell($pageWidth * 0.186, "7", "Approved by:", "RB", "", "L");
        $this->Cell($pageWidth * 0.186, "7", "Issued by:", "RB", "", "L");
        $this->Cell(0, "7", "Received by:", "RB", "", "L");
        $this->Ln();

        //Footer data
        $this->htmlTable($data->footer_data);

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
