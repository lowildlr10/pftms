<?php

namespace App\Plugins\PDFGenerator;

class DocLiquidationReport extends PDF {
    public function printLR($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->liq->id;

        $serialNo = $data->liq->serial_no;
        $dateLiquidation = $data->liq->date_liquidation;
        $dateClaimant = $data->liq->date_claimant;
        $dateSupervisor = $data->liq->date_supervisor;
        $dateAccountant = $data->liq->date_accounting;
        $claimant = strtoupper($data->claimant);
        $supervisor = strtoupper($data->supervisor);
        $accounting = strtoupper($data->accounting);

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(15, 15, 15);
        $this->SetHeaderMargin(10);
        $this->SetPrintHeader(false);

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

        //Title header
        $this->Cell($pageWidth * 0.57, 5,'', 'TL', '', 'C');
        $this->Cell($pageWidth * 0, 5, '', 'TLR');
        $this->Ln();

        $this->SetFont('Times', 'B', 12);
        $this->Cell($pageWidth * 0.57, 5, 'LIQUIDATION REPORT', 'L', '', 'C');
        $this->SetFont('Times', '', 12);
        $this->Cell($pageWidth * 0, 5, 'Serial No.: ' . $serialNo, 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.57, 5, 'Period Covered: ' . $data->liq->period_covered, 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, 'Date: ' . $dateLiquidation, 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.57, 5, '', 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, '', 'BLR');
        $this->Ln();

        $this->SetFont('Times', 'B', 12);
        $this->Cell($pageWidth * 0.57, 5, 'Entity Name : ' . $data->liq->entity_name, 'L', '');
        $this->SetFont('Times', '', 12);
        $this->Cell($pageWidth * 0, 5, 'Responsibility Center Code: ', 'LR');
        $this->Ln();

        $this->SetFont('Times', 'B', 12);
        $this->Cell($pageWidth * 0.57, 6, 'Fund Cluster :  ' . $data->liq->fund_cluster, 'BL', '');
        $this->SetFont('Times', '', 12);
        $this->Cell($pageWidth * 0, 6, $data->liq->responsibility_center, 'BLR', '', 'C');
        $this->Ln();

        // Table title
        $this->SetFont('Times', 'B', 11);
        $this->Cell($pageWidth * 0.57, 10, 'PARTICULARS', 'BL', '', 'C');
        $this->Cell($pageWidth * 0, 10, 'AMOUNT', 'BLR', '', 'C');
        $this->Ln();

        // Table body
        $this->SetFont('Times', '', 11);
        $this->htmlTable($data->table_data);

        // Signatories row
        $this->Cell($pageWidth * 0.03, 5, 'A', 'BL', '');
        $this->Cell($pageWidth * 0.255, 5, 'Certified: Correctness of the', 'L', '');
        $this->Cell($pageWidth * 0.03, 5, 'B', 'BL', '');
        $this->Cell($pageWidth * 0.255, 5, 'Certified: Purpose of travel /', 'L', '');
        $this->Cell($pageWidth * 0.03, 5, 'C', 'BL', '');
        $this->Cell($pageWidth * 0, 5, 'Certified: Supporting documents', 'LR', '');
        $this->Ln();

        $this->Cell($pageWidth * 0.03, 5, '', 'L', '');
        $this->Cell($pageWidth * 0.255, 5, 'above data', '', '');
        $this->Cell($pageWidth * 0.03, 5, '', 'L', '');
        $this->Cell($pageWidth * 0.255, 5, 'cash advance duly accomplished', '', '');
        $this->Cell($pageWidth * 0.03, 5, '', 'L', '');
        $this->Cell($pageWidth * 0, 5, 'complete and proper', 'R', '');
        $this->Ln();

        $this->Cell($pageWidth * 0.285, 5, '', 'L');
        $this->Cell($pageWidth * 0.285, 5, '', 'L');
        $this->Cell($pageWidth * 0, 5, '', 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.285, 5, $claimant, 'L', '', 'C');
        $this->Cell($pageWidth * 0.285, 5, $supervisor, 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, $accounting, 'LR', '', 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.285, 5, 'Signature over Printed Name', 'L', '', 'C');
        $this->Cell($pageWidth * 0.285, 5, 'Signature over Printed Name', 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, 'Signature over Printed Name', 'LR', '', 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.285, 5, 'Claimant', 'L', '', 'C');
        $this->Cell($pageWidth * 0.285, 5, 'Immediate Supervisor', 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, 'Head, Accounting Division Unit', 'LR', '', 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.285, 5, '', 'L');
        $this->Cell($pageWidth * 0.285, 5, '', 'L');
        $this->Cell($pageWidth * 0, 5, '', 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.285, 5, '', 'L');
        $this->Cell($pageWidth * 0.285, 5, '', 'L');
        $this->Cell($pageWidth * 0, 5, 'JEV No.:' . $data->liq->jev_no, 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.285, 5, 'Date: ' . $dateClaimant, 'BL');
        $this->Cell($pageWidth * 0.285, 5, 'Date: ' . $dateSupervisor, 'BL');
        $this->Cell($pageWidth * 0, 5, 'Date:' . $dateAccountant, 'BLR');
        $this->Ln();

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}


