<?php

namespace App\Plugins\PDFGenerator;

class DocInventoryCustodian extends PDF {
    public function printICS($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->id;

        $data->fund_cluster = $data->fund_cluster ? $data->fund_cluster : '01';
        $data->entity_name = $data->entity_name ? $data->entity_name : 'Department of Science and Technology - CAR';
        $data->received_from_name = strtoupper($data->received_from_name);
        $data->received_by_name = strtoupper($data->received_by_name);

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
        $this->Cell(0, "5", "INVENTORY CUSTODIAN SLIP", "", "", "C");
        $this->Ln(10);

        $this->SetFont('Times', 'B', 11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.9, "5", "Entity Name: $data->entity_name", "", "", "L");
        $this->Ln();
        $this->Cell($pageWidth * 0.65, "5", "Fund Cluster : $data->fund_cluster", "", "", "L");
        $this->Cell(0, "5", "ICS No : " . $data->inventory_no, "", "", "L");
        $this->Ln(10);

        //----Table data
        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->htmlTable($data->table_data);


        $this->SetFont("Times", "B", 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.2, "5", "Purchase Order", "L", "", "L");
        $this->Cell(0, "5", ": " . $data->po_no, "R", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.2, "5", "Date", "L", "", "L");
        $this->Cell(0, "5", ": " . $data->date_po, "R", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.2, "5", "Supplier", "L", "", "L");
        $this->Cell(0, "5", ": " . $data->supplier, "R", "", "L");
        $this->Ln();

        $this->Cell(0, "10", "", "LRB", "", "L");
        $this->Ln();

        $this->SetFont("Times", "", 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.45, "5", "Recieved from: ", "LR", "", "L");
        $this->Cell(0, "5", "Received by:", "R", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
        $this->Cell(0, "5", "", "R", "", "L");
        $this->Ln();

        $this->SetFont("Times", "B", 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.45, "5", $data->received_from_name, "LR", "", "C");
        $this->Cell(0, "5", $data->received_by_name, "R", "", "C");
        $this->Ln();

        $this->SetFont("Times", "", 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.45, "5", "Signature Over Printed Name", "LR", "", "C");
        $this->Cell(0, "5", "Signature Over Printed Name", "R", "", "C");
        $this->Ln();

        $this->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
        $this->Cell(0, "5", "", "R", "", "L");
        $this->Ln();

        $this->SetFont("Times", "B", 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.45, "5", $data->received_from_position, "LR", "", "C");
        $this->Cell(0, "5", $data->received_by_position, "R", "", "C");
        $this->Ln();

        $this->SetFont("Times", "", 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.45, "5", "Position/Office", "LR", "", "C");
        $this->Cell(0, "5", "Position/Office", "R", "", "C");
        $this->Ln();

        $this->Cell($pageWidth * 0.45, "5", "", "LR", "", "L");
        $this->Cell(0, "5", "", "R", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.45, "5", "_________________________________", "LR", "", "C");
        $this->Cell(0, "5", "_________________________________", "R", "", "C");
        $this->Ln();

        $this->Cell($pageWidth * 0.45, "5", "Date", "LRB", "", "C");
        $this->Cell(0, "5", "Date", "RB", "", "C");
        $this->Ln();

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
