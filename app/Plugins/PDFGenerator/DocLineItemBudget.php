<?php

namespace App\Plugins\PDFGenerator;

class DocLineItemBudget extends PDF {
    public function printLIB($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->id;

        $originalFontSize = $data->header_count > 7 ? 8 : 9;

        $cy = $data->cy_year;
        $title = $data->title;
        $duration = $data->duration;
        $implementingAgency = $data->implementing_agency;
        $coimplementors = $data->coimplementors;
        $monitoringOffices = $data->monitoring_offices;
        $leader = $data->leader;
        $totalCost = $data->total_cost;
        $preparedBy = strtoupper($data->submitted_by);
        $approvedBy = strtoupper($data->approved_by);
        $preparedByPos = $data->submitted_by_pos;
        $approvedByPos = $data->approved_by_pos;

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(10, 15, 10);
        $this->SetHeaderMargin(10);
        $this->SetPrintHeader(false);
        //$this->SetPrintFooter(false);
        $this->generateInfoOnly = true;

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
        $this->SetFont('helvetica', 'B', $originalFontSize + ($fontScale * 9));
        $this->Cell(0, 4, 'DEPARTMENT OF SCIENCE AND TECHNOLOGY', '', '', 'C');
        $this->Ln();

        $this->Cell(0, 4, 'Cordillera Administrative Region', '', '', 'C');
        $this->Ln();

        $this->Cell(0, 4, 'Project Line-Item-Budget', '', '', 'C');
        $this->Ln();

        $this->Cell(0, 4, "CY $cy", '', '', 'C');
        $this->Ln(9);

        $this->SetFont('helvetica', 'B', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell($data->header_count > 8 ? $pageWidth * 0.15 :
                    $pageWidth * 0.26, 5, 'Project Title', 0, 0, 'L');
        $this->Cell($pageWidth * 0.01, 5, ':', 0, 0, 'C');
        $this->SetFont('helvetica', '', $originalFontSize + ($fontScale * $originalFontSize));
        $this->MultiCell(0, 5, $title, 0, 'L');

        $this->SetFont('helvetica', 'B', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell($data->header_count > 8 ? $pageWidth * 0.15 :
                    $pageWidth * 0.26, 5, 'Period Covered', 0, 0, 'L');
        $this->Cell($pageWidth * 0.01, 5, ':', 0, 0, 'C');
        $this->SetFont('helvetica', '', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell(0, 5, $duration, 0, 0, 'L');
        $this->Ln();

        $this->SetFont('helvetica', 'B', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell($data->header_count > 8 ? $pageWidth * 0.15 :
                    $pageWidth * 0.26, 5, 'Implementing Agency/Office', 0, 0, 'L');
        $this->Cell($pageWidth * 0.01, 5, ':', 0, 0, 'C');
        $this->SetFont('helvetica', '', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell(0, 5, $implementingAgency, 0, 0, 'L');
        $this->Ln();

        $this->SetFont('helvetica', 'B', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell($data->header_count > 8 ? $pageWidth * 0.15 :
                    $pageWidth * 0.26, 5, 'Co-Implementing Agencies/Offices', 0, 0, 'L');
        $this->Cell($pageWidth * 0.01, 5, ':', 0, 0, 'C');
        $this->SetFont('helvetica', '', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell(0, 5, $coimplementors, 0, 0, 'L');
        $this->Ln();

        $this->SetFont('helvetica', 'B', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell($data->header_count > 8 ? $pageWidth * 0.15 :
                    $pageWidth * 0.26, 5, 'Project Leader', 0, 0, 'L');
        $this->Cell($pageWidth * 0.01, 5, ':', 0, 0, 'C');
        $this->SetFont('helvetica', '', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell(0, 5, $leader, 0, 0, 'L');
        $this->Ln();

        $this->SetFont('helvetica', 'B', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell($data->header_count > 8 ? $pageWidth * 0.15 :
                    $pageWidth * 0.26, 5, 'Monitoring Agencies/Offices', 0, 0, 'L');
        $this->Cell($pageWidth * 0.01, 5, ':', 0, 0, 'C');
        $this->SetFont('helvetica', '', $originalFontSize + ($fontScale * $originalFontSize));
        $this->Cell(0, 5, $monitoringOffices, 0, 1, 'L');
        $this->Ln();

        // Table body
        $this->htmlTable($data->table_data);
        $this->Ln(10);

        $this->Cell($pageWidth * 0.34, 4, '    Prepared By:', 0, 0, 'L');
        $this->Cell($pageWidth * 0.34, 4, '    Approved By:', 0, 0, 'L');
        $this->Cell(0, 4, 'Date:', 0, 1, 'L');
        $this->Ln(5);

        $this->SetFont('helvetica', 'B', 9 + ($fontScale * 9));
        $this->Cell($pageWidth * 0.34, 4, $preparedBy, 0, 0, 'C');
        $this->Cell($pageWidth * 0.34, 4, $approvedBy, 0, 0, 'C');
        $this->Cell(0, 4, '', 0, 0, 'C');
        $this->Ln();

        $this->SetFont('helvetica', '', 9 + ($fontScale * 9));
        $this->Cell($pageWidth * 0.34, 4, $preparedByPos, 0, 0, 'C');
        $this->Cell($pageWidth * 0.34, 4, $approvedByPos, 0, 0, 'C');
        $this->Cell(0, 4, '', 0, 0, 'C');

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}


