<?php

namespace App\Plugins\PDFGenerator;

class DocRegistryAllotmentsORSDV extends PDF {
    public function printRAOD($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = implode(', ', $data->id);

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(7, 30, 7);
        $this->SetHeaderMargin(10);
        $this->SetPrintHeader(true);
        $this->SetPrintFooter(true);
        $this->setIsPageNoOnly(true);

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

        $periodEnding = $data->period_ending;
        $entityName = $data->entity_name;
        $fundCluster = $data->fund_cluster;
        $legalBasis = $data->legal_basis;
        $mfoPAP = $data->mfo_pap;
        $sheetNo = $data->sheet_no;

        $currentMonth = $data->current_month;
        $totalAllot = $data->total_allotment;
        $totalObli = $data->total_obligation;
        $totalUnobli = $data->total_unobligated;
        $totalDisb = $data->total_disbursement;
        $totalDue = $data->total_due;
        $totalNotDue = $data->total_not_due;

        //Add a page
        $this->startPageGroup();
        $this->AddPage();

        /* ------------------------------------- Start of Doc ------------------------------------- */

        //Title header
        $this->SetFont('helvetica', 'BI', 8 + ($fontScale * 8));
        $this->Cell(0, 4, 'Appendix 9A', '', '', 'R');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8 + ($fontScale * 8));
        $this->Cell(0, 4, 'REGISTRY OF ALLOTMENTS, OBLIGATIONS AND DISBURSEMENTS', '', '', 'C');
        $this->Ln();

        $this->Cell(0, 4, 'PERSONNEL SERVICES/MAINTENANCE AND OTHER OPERATING EXPENSES ', '', '', 'C');
        $this->Ln();

        /*
        $this->Cell($pageWidth * 0.48, 4, 'For the Period Ending ', '', '', 'R');
        $this->SetFont('helvetica', 'BU', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.1, 4, " $periodEnding ", '', '', 'L');
        $this->Cell(0, 4, '', '', '', 'C');
        $this->Ln(8);*/

        $this->MultiCell(0, 4, "For the Period Ending: $periodEnding", '', 'C');
        $this->Ln(8);

        $this->SetFont('helvetica', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.1, 4, 'Entity Name: ', '', '', 'L');
        $this->SetFont('helvetica', 'BU', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.64, 4, $entityName, '', '', 'L');
        $this->SetFont('helvetica', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.11, 4, 'MFO/PAP: ', '', '', 'L');
        $this->SetFont('helvetica', 'BU', 8 + ($fontScale * 8));
        $this->Cell(0, 4, $mfoPAP, '', '', 'L');
        $this->Ln();

        $this->SetFont('helvetica', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.1, 4, 'Fund Cluster: ', '', '', 'L');
        $this->SetFont('helvetica', 'BU', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.64, 4, $fundCluster, '', '', 'L');
        $this->SetFont('helvetica', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.11, 4, 'Sheet No.: ', '', '', 'L');
        $this->SetFont('helvetica', 'BU', 8 + ($fontScale * 8));
        $this->Cell(0, 4, $sheetNo, '', '', 'L');
        $this->Ln();

        $this->SetFont('helvetica', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.1, 4, 'Legal Basis: ', '', '', 'L');
        $this->SetFont('helvetica', 'BU', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.64, 4, $legalBasis, '', '', 'L');
        $this->SetFont('helvetica', 'BI', 8 + ($fontScale * 8));
        $this->Cell(0, 4, 'Current /Cont Allotment', '', '', 'C');
        $this->Ln(8);

        // Table header
        $this->SetFont('helvetica', 'B', 8 + ($fontScale * 8));
        $this->Cell(($pageWidth - 14) * 0.05, 4, '', 'LTR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.05, 4, '', 'LTR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.05, 4, '', 'LTR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.3, 4, 'Reference', '1', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'UACS Object', 'LTR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LTR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LTR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LTR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LTR', '', 'C');
        $this->Cell(0, 4, 'Unpaid Obligations', 'LTR', '', 'C');
        $this->Ln();

        $this->Cell(($pageWidth - 14) * 0.05, 4, 'Date', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.05, 4, 'Date', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.05, 4, 'Date', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.09, 4, 'Payee', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.14, 4, 'Particulars', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'Serial Number', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'Code/', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'Allotments', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'Obligations', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'Unobligated', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'Disbursements', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.1, 4, 'Due and Demandable', 'LTR', '', 'C');
        $this->Cell(0, 4, 'Not Yet Due and', 'LTR', '', 'C');
        $this->Ln();

        $this->Cell(($pageWidth - 14) * 0.05, 4, 'Received', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.05, 4, 'Obligated', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.05, 4, 'Released', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.09, 4, '', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.14, 4, '', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'Expenditure', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, 'Allotments', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.1, 4, '', 'LR', '', 'C');
        $this->Cell(0, 4, 'Demandable', 'LR', '', 'C');
        $this->Ln();

        // Table body
        foreach ($data->table_data as $datCtr => $dat) {
            $this->SetFont('helvetica', '', 8 + ($fontScale * 8));
            $this->htmlTable($dat->table_data);
            $this->htmlTable($dat->table_footer);

            if ($datCtr != count($data->table_data) - 1) {
                $this->Cell(0, 4, '', 'LR', '', 'C');
                $this->Ln();
            }
        }

        $this->SetFont('helvetica', 'B', 8 + ($fontScale * 8));
        $this->Cell(($pageWidth - 14) * 0.24, 4, 'TOTAL AS OF '.$currentMonth, 'LBR', '', 'L');
        $this->Cell(($pageWidth - 14) * 0.14, 4, '', 'LBR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LBR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, '', 'LBR', '', 'C');
        $this->Cell(($pageWidth - 14) * 0.07, 4, $totalAllot, 'LBR', '', 'R');
        $this->Cell(($pageWidth - 14) * 0.07, 4, $totalObli, 'LBR', '', 'R');
        $this->Cell(($pageWidth - 14) * 0.07, 4, $totalUnobli, 'LBR', '', 'R');
        $this->Cell(($pageWidth - 14) * 0.07, 4, $totalDisb, 'LBR', '', 'R');
        $this->Cell(($pageWidth - 14) * 0.1, 4, $totalDue, 'LBR', '', 'R');
        $this->Cell(0, 4, $totalNotDue, 'LBR', '', 'R');
        $this->Ln();

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
