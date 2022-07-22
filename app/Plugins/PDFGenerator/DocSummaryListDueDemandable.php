<?php

namespace App\Plugins\PDFGenerator;

class DocSummaryListDueDemandable extends PDF {
    public function printSummary($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->summary->id;

        $mdsNo = $data->mds_account_no;
        $department = $data->summary->department;
        $department = $data->summary->department;
        $entityName = $data->summary->entity_name;
        $operatingUnit = $data->summary->operating_unit;
        $fundCluster = $data->summary->fund_cluster;
        $sliiaeNo = $data->summary->sliiae_no;
        $sliiaeDate = date('F j, Y', strtotime($data->summary->date_sliiae));
        $to = $data->summary->to;
        $bankName = $data->summary->bank_name;
        $bankAddress = $data->summary->bank_address;
        $totalAmount = number_format($data->summary->total_amount, 2);
        $totalAmountWords = strtoupper($data->summary->total_amount_words);
        $countLDDAP = $data->summary->lddap_no_pcs;

        $certCorrect = $data->sig_cert_correct;
        $certCorrectPosition = $data->sig_cert_correct_position;
        $approvedBy = $data->sig_approved_by;
        $approvedByPosition = $data->sig_approved_by_position;
        $deliveredBy = $data->sig_delivered_by;
        $deliveredByPosition = $data->sig_delivered_by_position;

        $docSubject = "Summary of LDDAP-ADAs Issued and Invalidated ADA Entries";

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(5, 10, 5);
        $this->SetHeaderMargin(10);
        $this->SetFooterMargin(5);
        $this->SetPrintHeader(false);
        //$this->SetPrintFooter(false);
        $this->generateInfoOnly = true;

        //Set auto page breaks
        $this->SetAutoPageBreak(TRUE, 5);

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

        $pageWidth_inMargin = $this->getPageWidth() - 10;

        //Title
        $this->Cell(0, 5, '', 'TLR', 0, 'C');
        $this->Ln();

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell(0, 4, strtoupper($docSubject), 'LR', 0, 'C');
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell(0, 3, "For MDS Account Number: $mdsNo", 'LR', 0, 'C');
        $this->Ln();

        $this->Cell(0, 5, '', 'LR', 0, 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.685, 4, "Department: $department", 'L', 0, 'L');
        $this->Cell(0, 4, "Fund Cluster: $fundCluster", 'R', 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.685, 4, "Entity Name: $entityName", 'L', 0, 'L');
        $this->Cell(0, 4, "SLIIAE No.: $sliiaeNo", 'R', 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.685, 4, "Operating Unit: $operatingUnit", 'L', 0, 'L');
        $this->Cell(0, 4, "Date: $sliiaeDate", 'R', 0, 'L');
        $this->Ln();

        $this->Cell(0, 0, '', 'LR', 0, 'C');
        $this->Ln();

        $this->Cell(0, 4, "To: $to", 'LR', 0, 'L');
        $this->Ln();

        $this->MultiCell(0, 4, "$bankName", 'LR', 'L');

        $this->Cell(0, 4, "$bankAddress", 'LR', 0, 'L');
        $this->Ln();

        $this->Cell(0, 0, '', 'LR', 0, 'C');
        $this->Ln();

        //Table Header
        $this->SetFont('Times', 'B', 7 + ($fontScale * 7));
        $this->Cell($pageWidth * 0.174, 4, '', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.086, 4, '', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.4282, 4, 'Amount', 'TLR', 0, 'C');
        $this->Cell(0, 4, 'For GSB Use Only', 'TLR', 0, 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.174, 4, 'LDDAP-ADA No.', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.086, 4, 'Date of Issue', 'LR', 0, 'C');
        $this->SetFont('Times', '', 7 + ($fontScale * 7));
        $this->Cell($pageWidth * 0.08564, 4, '', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.34256, 4, 'Allotment/Object Class', 'TLR', 0, 'C');
        $this->Cell(0, 4, '', 'TLR', 0, 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.174, 4, '', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.086, 4, '', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'Total', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'PS', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'MOOE', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'CO', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'FE', 'TLR', 0, 'C');
        $this->Cell(0, 4, 'Remarks', 'LR', 0, 'C');
        $this->Ln();

        //Table Content
        $this->SetFont('Times', '', 7 + ($fontScale * 7));
        $this->htmlTable($data->table_data);

        $this->Cell($pageWidth * 0.26, 4, "No. of pcs of LDDAP-ADA : $countLDDAP", 'LR', 0, 'C');
        $this->Cell(0, 4, "Total Amount : $totalAmount", 'R', 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.26, 4, '', 'LR', 0, 'R');
        $this->MultiCell(0, 4, "Amount in Words: $totalAmountWords", 'TLR', 'L');

        $this->SetFillColor(128,128,128);
        $this->Cell(0, 8, '', 1, 0, 'C', 1);
        $this->Ln();

        $this->Cell($pageWidth * 0.174, 4, '', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.086, 4, '', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, '', 'LR', 0, 'C');
        $this->Cell(0, 4, 'OF WHICH INVALIDATED ENTRIES OF PREVIOUSLY ISSUED LDDAP-ADAs', 'LR', 0, 'C');
        $this->Ln();

        $this->SetFont('Times', 'B', 7 + ($fontScale * 7));
        $this->Cell($pageWidth * 0.174, 4, 'LDDAP-ADA No.', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.086, 4, 'Amount', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'Date Issued', 'LR', 0, 'C');
        $this->Cell(0, 4, 'Allotment/Object Class', 'TLR', 0, 'C');
        $this->Ln();

        $this->SetFont('Times', '', 7 + ($fontScale * 7));
        $this->Cell($pageWidth * 0.174, 4, '', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.086, 4, '', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, '', 'LR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'PS', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'MOOE', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'CO', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'FE', 'TLR', 0, 'C');
        $this->Cell($pageWidth * 0.08564, 4, 'TOTAL', 'TLR', 0, 'C');
        $this->Cell(0, 4, 'Remarks', 'TLR', 0, 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.174, 9, '', 1, 0, 'C');
        $this->Cell($pageWidth * 0.086, 9, '', 1, 0, 'C');
        $this->Cell($pageWidth * 0.08564, 9, '', 1, 0, 'C');
        $this->Cell($pageWidth * 0.08564, 9, '', 1, 0, 'C');
        $this->Cell($pageWidth * 0.08564, 9, '', 1, 0, 'C');
        $this->Cell($pageWidth * 0.08564, 9, '', 1, 0, 'C');
        $this->Cell($pageWidth * 0.08564, 9, '', 1, 0, 'C');
        $this->Cell($pageWidth * 0.08564, 9, '', 1, 0, 'C');
        $this->Cell(0, 9, '', 1, 0, 'C');
        $this->Ln();

        $this->Cell(0, 4, '', 'LR', 0, 'C');
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.685, 4, 'Certified Correct by:', 'L', 0, 'L');
        $this->Cell(0, 4, 'Approved by:', 'R', 0, 'L');
        $this->Ln();

        $this->Cell(0, 6, '', 'LR', 0, 'C');
        $this->Ln();

        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.35, 4, $certCorrect, 'L', 0, 'C');
        $this->Cell($pageWidth * 0.25, 4, '', 0, 0, 'L');
        $this->Cell(0, 4, $approvedBy, 'R', 0, 'C');
        $this->Ln();

        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.35, 4, $certCorrectPosition, 'L', 0, 'C');
        $this->Cell($pageWidth * 0.25, 4, '', 0, 0, 'L');
        $this->Cell(0, 4, $approvedByPosition, 'R', 0, 'C');
        $this->Ln();

        $this->Cell(0, 15, '', 'BLR', 0, 'C');
        $this->Ln();

        $this->Cell(0, 4, '', 'LR', 0, 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.26, 4, 'TRANSMITTAL INFORMATION', 'L', 0, 'L');
        $this->Cell($pageWidth * 0.34, 4, '', 0, 0, 'L');
        $this->Cell(0, 4, '', 'R', 0, 'C');
        $this->Ln();

        $this->Cell(0, 4, '', 'LR', 0, 'C');
        $this->Ln();


        $this->Cell($pageWidth * 0.685, 4, "\t\t\t\t\t\t\t\t\t\t\t\tDelivered by:", 'L', 0, 'L');
        $this->Cell(0, 4, 'Received by:', 'R', 0, 'L');
        $this->Ln();

        $this->Cell(0, 6, '', 'LR', 0, 'C');
        $this->Ln();

        $this->SetFont('Times', 'B', 8 + ($fontScale * 8));
        $this->Cell($pageWidth * 0.35, 4, $deliveredBy, 'L', 0, 'C');
        $this->Cell($pageWidth * 0.34, 4, '', 0, 0, 'L');
        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->Cell(0, 4, '(Signature)', 'R', 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.35, 4, $deliveredByPosition, 'L', 0, 'C');
        $this->Cell($pageWidth * 0.34, 4, '', 0, 0, 'L');
        $this->Cell(0, 4, '(Name in Print)', 'R', 0, 'L');
        $this->Ln();

        $this->Cell($pageWidth * 0.35, 4, '', 'L', 0, 'C');
        $this->Cell($pageWidth * 0.34, 4, '', 0, 0, 'L');
        $this->Cell(0, 4, 'Designation', 'R', 0, 'L');
        $this->Ln();

        $this->Cell(0, 4, '', 'BLR', 0, 'C');
        $this->Ln();

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
