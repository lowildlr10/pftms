<?php

namespace App\Plugins\PDFGenerator;

class DocObligationRequestStatus extends PDF {
    public function printORSBURS($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->ors->id;

        $orsDate = $data->ors->date_ors_burs;
        $sDate2 = $data->sDate1;
        $sDate1 = $data->sDate2;
        $data->sign1 = strtoupper($data->sign1);
        $data->sign2 = strtoupper($data->sign2);

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(10, 24, 10);
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

        //Title header with logo
        if ($data->ors->document_type == 'ors') {
            $docSubject = "Obligation Request and Status";
            $this->SetFont('helvetica', 'B', 15 + ($fontScale * 15));
        } else {
            $docSubject = "Budget Utilization Request and Status";
            $this->SetFont('helvetica', 'B', 14 + ($fontScale * 14));
        }

        $this->Cell($pageWidth * 0.5714, 8, strtoupper($docSubject), 'TLR', 0, 'C');
        $this->Cell(0, 8, '', 'TR');
        $this->Ln();

        $xCoor = $this->getX();
        $yCoor = $this->getY();

        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $img = file_get_contents(url('images/logo/dostlogo.png'), false,
                                stream_context_create($arrContextOptions));
        $this->Image('@' . $img, $xCoor + 4, $yCoor, 16, 0, 'PNG');
        $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.10476, 4, '', 'L');

        if (strtolower($data->ors->document_type) == 'ors') {
            $this->SetTextColor(0, 0, 255);
        }

        $this->Cell($pageWidth * 0.466667, 4, 'Republic of the Philippines', 'R');
        $this->SetFont('helvetica','IB', 10 + ($fontScale * 10));
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 4, "Serial No. \t\t\t\t\t\t\t\t\t: " . $data->ors->serial_no, 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.10476, 4, '', 'L');
        $this->SetFont('helvetica', 'B', 10 + ($fontScale * 10));

        if (strtolower($data->ors->document_type) == 'ors') {
            $this->SetTextColor(0, 0, 255);
        }

        $this->Cell($pageWidth * 0.466667, 4, 'DEPARTMENT OF SCIENCE AND TECHNOLOGY', 'R');
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 4, '', 'R');
        $this->Ln();

        $this->SetFont('helvetica', '', 9 + ($fontScale * 9));
        $this->Cell($pageWidth * 0.10476,4, '', 'L');

        if (strtolower($data->ors->document_type) == 'ors') {
            $this->SetTextColor(0, 0, 255);
        }

        $this->Cell($pageWidth * 0.466667,4, 'Cordillera Administrative Region', 'R');
        $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 4, "Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t: " . $orsDate, 'R');
        $this->Ln();

        $this->SetFont('helvetica','', 9 + ($fontScale * 9));
        $this->Cell($pageWidth * 0.10476,4,'','L');

        if (strtolower($data->ors->document_type) == 'ors') {
            $this->SetTextColor(0, 0, 255);
        }

        $this->Cell($pageWidth * 0.466667,4,'Km. 6, La Trinidad, Benguet','R');
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 4, '', 'R');
        $this->Ln();

        $this->SetFont('helvetica','IB', 11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.57143,6, 'Entity Name','LRB', 0, 'C');
        $this->SetFont('helvetica','IB', 10 + ($fontScale * 10));
        $this->Cell(0, 6, "Fund Cluster \t\t\t\t: ____________________", 'RB');
        $this->Ln();

        //Header data
        $this->SetFont('helvetica', '', 9 + ($fontScale * 9));
        $this->htmlTable($data->header_data);

        //Table data
        $this->htmlTable($data->table_data);

        $this->Cell($pageWidth * 0.0952, 7, 'A.', 'LRB');
        $this->Cell($pageWidth * 0.3667, 7, '', 'R');
        $this->Cell($pageWidth * 0.1095, 7, 'B.', 'RB');
        $this->Cell(0, 7, '', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.0762, 5, '', 'L');
        $this->Cell($pageWidth * 0.08095, 5, 'Certified:', '');
        $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.30476, 5, 'Charges to appropriation/alloment ','');
        $this->Cell($pageWidth * 0.090476, 5, '', 'L');
        $this->SetFont('helvetica', 'B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.08095, 5, 'Certified:','');
        $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
        $this->Cell(0, 5, 'Allotment available and obligated','R');
        $this->Ln();

        $this->Cell($pageWidth * 0.0762, 5, '', 'L');
        $this->Cell($pageWidth * 0.3857, 5, 'necessary, lawful and under my direct supervision;');
        $this->Cell($pageWidth * 0.090476, 5, '', 'L');
        $this->Cell(0, 5, 'for the purpose/adjustment necessary as', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.0762, 5, '', 'L');
        $this->Cell($pageWidth * 0.3857, 5, 'and supporting documents valid, proper and legal.');
        $this->Cell($pageWidth * 0.090476, 5, '', 'L');
        $this->Cell(0, 5, 'indicated above.', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.4619, 7,'','RL');
        $this->Cell(0, 7,'','R');
        $this->Ln();

        $this->SetFont('helvetica','', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.4619,8, "Signature \t\t\t\t\t\t\t:       ______________________________", 'LR');
        $this->Cell(0,8,"Signature \t\t\t\t\t\t\t:      ______________________________",'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.4619,'2','','LR');
        $this->Cell(0,'2','','R');
        $this->Ln();

        $this->Cell($pageWidth * 0.12857, 5,"Printed Name : ",'L');
        $this->SetFont('helvetica','B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.28095, 5, $data->sign1,'B');
        $this->Cell($pageWidth * 0.05238, 5," ",'R');
        $this->SetFont('helvetica','', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.12857, 5,"Printed Name : ",'');
        $this->SetFont('helvetica','B', 10);
        $this->Cell($pageWidth * 0.28095, 5, $data->sign2,'B');
        $this->Cell(0, 5,'','R');
        $this->Ln();

        $this->SetFont('helvetica','', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.4619,4,'','LR');
        $this->Cell(0,4,'','R');
        $this->Ln();

        $this->Cell($pageWidth * 0.12857, 5, "Position \t\t\t\t\t\t\t\t\t:   ", 'L');
        $this->Cell($pageWidth * 0.28095, 5, $data->position1,'B');
        $this->Cell($pageWidth * 0.05238, 5, '','R');
        $this->Cell($pageWidth * 0.12857, 5, "Position \t\t\t\t\t\t\t\t\t:   ");
        $this->Cell($pageWidth * 0.28095, 5, $data->position2, 'B');
        $this->Cell(0, 5, '', 'R');
        $this->Ln();

        $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.4619, 5,
                "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Head, ".
                "Requesting Office/Authorized", 'LR');
        $this->Cell(0, 5,
                "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Head, ".
                "Budget Division/Unit/Authorized", 'R');
        $this->Ln();

        $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.4619, 3,
                "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Representative", 'LR');
        $this->Cell(0, 3,
                "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t Representative",'R');
        $this->Ln();

        $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.4619, 3, '', 'LR');
        $this->Cell(0, 3, '', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.12857, 5, "Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t: ", 'L');
        $this->Cell($pageWidth * 0.28095, 5, $sDate1, 'B');
        $this->Cell($pageWidth * 0.05238, 5, '','R');
        $this->Cell($pageWidth * 0.12857, 5, "Date \t\t\t\t\t\t\t\t\t\t\t\t\t\t: ");
        $this->Cell($pageWidth * 0.28095, 5, $sDate2, 'B');
        $this->Cell(0, 5, '', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.4619, 3, '', 'LRB');
        $this->Cell(0, 3, '', 'RB');
        $this->Ln();

        //----Footer data
        $this->htmlTable($data->footer_data);

        $this->SetFont('helvetica','', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.5857, '10', "Date Received:", '', "L");
        $this->Cell($pageWidth * 0.32857, '10', "Date Released:", '', "L");

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
