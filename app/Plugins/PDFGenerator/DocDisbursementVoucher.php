<?php

namespace App\Plugins\PDFGenerator;

class DocDisbursementVoucher extends PDF {
    public function printDV($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->dv->id;

        $dvDate = $data->dv->date_dv;
        $signatory = strtoupper($data->sign1);
        $sign1 = strtoupper($data->sign2);
        $sign2 = strtoupper($data->sign3);
        $paymentMode = explode("-", $data->dv->payment_mode);

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(10, 22, 10);
        $this->SetHeaderMargin(10);

        //Set auto page breaks
        //$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->SetAutoPageBreak(TRUE, 15);

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

        //Title header with Logo
        $xCoor = $this->GetX();
        $yCoor = $this->GetY();

        $this->Cell($pageWidth * 0.71, 1, '', "TL", 0, 'C');
        $this->Cell(0, 1, '', 'TR');
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

        $this->Image('@' . $img, $xCoor + 14, $yCoor, 14, 0, 'PNG');
        $this->SetFont('Times', 'B', 10);
        $this->Cell($pageWidth * 0.71, 5, 'Republic of the Philippines', 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, '', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.71, 5, 'DEPARTMENT OF SCIENCE AND TECHNOLOGY', 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, '', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.71, 3, 'Cordillera Administrative Region', 'L', '', 'C');
        $this->Cell($pageWidth * 0, 3, '', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.71, 4, '', 'BL', '', 'C');
        $this->Cell($pageWidth * 0, 4, '', 'BR');
        $this->Ln();

        $this->Cell($pageWidth * 0.71, 5,'', 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, "Fund Cluster : 01", 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.71, 5, "DISBURSEMENT VOUCHER", 'L', '', 'C');
        $this->Cell($pageWidth * 0, 5, "Date : " . $dvDate, 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.71, 5, '', 'BL', '', 'C');
        $this->Cell($pageWidth * 0, 5, "DV No. : " . $data->dv->dv_no, 'BLR');
        $this->Ln();

        $x = $this->getX();
        $y = $this->getY();

        $this->MultiCell($pageWidth * 0.10476, 3.8, "\nMode of \nPayment\n   ", 1);
        $this->SetFont('Times', '', 10 + ($fontScale * 10));

        $this->SetXY($x + $pageWidth * 0.10476, $y);
        $this->SetFont('ZapfDingbats', '', 15 + ($fontScale * 15));

        /*
        // Fill checkbox with check symbol
        if ($paymentMode[0] != "0") {
            $this->Text($x + ($pageWidth - 20) * 0.13333, $y + 2, 3);
        }

        if ($paymentMode[1] != "0") {
            $this->Text($x + $pageWidth * 0.28095, $y + 2, 3);
        }

        if ($paymentMode[2] != "0") {
            $this->Text($x + $pageWidth * 0.49524, $y + 2, 3);
        }

        if ($paymentMode[3] != "0") {
            $this->Text($x + $pageWidth * 0.6, $y + 2, 3);
        }*/

        $this->SetFont('Times', '', 10 + ($fontScale * 15));
        $this->Rect($x + $pageWidth * 0.12857, $y + 3, 5, 5);
        $this->Rect($x + $pageWidth * 0.27619, $y + 3, 5, 5);
        $this->Rect($x + $pageWidth * 0.49048, $y + 3, 5, 5);
        $this->Rect($x + $pageWidth * 0.59524, $y + 3, 5, 5);
        $this->MultiCell(0, 3.8, "\n \t\t\t\t\t\t\t\t\t  MDS Check \t\t\t\t\t\t\t\t\t\t\t\t\t   Commercial Check" .
                                    "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t    ADA" .
                                    "\t\t\t\t\t\t\t\t\t\t\t\t\t      Others (Please specify)
                                    \t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                    "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                    "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                    "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t".
                                    "_____________________\n\n", 1);

        //Table data
        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->htmlTable($data->header_data);
        $this->htmlTable($data->table_data);

        //Section A
        $this->Cell($pageWidth * 0.02391, 5, "A.", 1);
        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell(0, 5,
                "Certified: Expenses/Cash Advance necessary, ".
                "lawful and incurred under my direct supervision.", 'R');
        $this->Ln();

        $this->Cell(0, 5, '', 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.2619, 5, '', 'L');
        $this->SetFont('Times', 'B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.390476, 5, strtoupper($signatory), '', '', 'C');
        $this->Cell(0, 5, '', 'R');
        $this->Ln();

        $this->Cell($pageWidth * 0.2619, 5, '', 'BL');
        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.390476, 5,
                "Printed Name, Designation and Signature of Supervisor", 'B', '', 'C');
        $this->Cell(0, 5, '', 'BR');
        $this->Ln();

        //Section B
        $this->SetFont('Times','B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.02381, 5, "B.", 1);
        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell(0, 5, "Accounting Entry:", 'R');
        $this->Ln();

        $this->htmlTable($data->footer_data);

        $this->Cell($pageWidth * 0.466667, 6, '', 'LR');
        $x = $this->getX();
        $y = $this->getY();
        $this->Rect($x + 2, $y + 2, 4, 4);

        $this->Cell($pageWidth * 0.171429, 6, '', 'LR');
        $this->Cell($pageWidth * 0.1381, 6, '', 1);
        $this->Cell(0, 6, '', 1);
        $this->Ln();

        $this->Cell($pageWidth * 0.466667, 6, '', 'LR');
        $x = $this->getX();
        $y = $this->getY();
        $this->Rect($x + 2, $y + 2, 4, 4);

        $this->Cell($pageWidth * 0.171429, 6, '', 'LR');
        $this->Cell($pageWidth * 0.1381, 6, '', 1);
        $this->Cell(0, 6, '', 1);
        $this->Ln();

        $this->Cell($pageWidth * 0.466667, 6, '', 'LR');
        $x = $this->getX();
        $y = $this->getY();
        $this->Rect($x + 2, $y + 2, 4, 4);

        $this->Cell($pageWidth * 0.171429, 6, '', 'LR');
        $this->Cell($pageWidth * 0.1381, 6, '', 1);
        $this->Cell(0, 6, '', 1);
        $this->Ln();

        $this->Cell($pageWidth * 0.466667, 6, '', 'LR');
        $x = $this->getX();
        $y = $this->getY();
        $this->Rect($x + 2, $y + 2, 4, 4);

        $this->Cell($pageWidth * 0.171429, 6, '', 'LR');
        $this->Cell($pageWidth * 0.1381, 6, '', 1);
        $this->Cell(0, 6, '', 1);
        $this->Ln();

        $this->Cell($pageWidth * 0.466667, 6, '', 'LR');
        $x = $this->getX();
        $y = $this->getY();
        $this->Rect($x + 2, $y + 2, 4, 4);

        $this->Cell($pageWidth * 0.171429, 6, '', 'LR');
        $this->Cell($pageWidth * 0.1381, 6, '', 1);
        $this->Cell(0, 6, '', 1);
        $this->Ln();

        $this->Cell($pageWidth * 0.466667, "2", '', 'BLR');
        $this->Cell($pageWidth * 0.171429, "2", '', 'BLR');
        $this->Cell($pageWidth * 0.1381, "2", '', 1);
        $this->Cell(0, "2", '', 1);
        $this->Ln();

        //Section C & D
        $this->SetFont('Times', 'B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.02381, 5, "C.", 1);
        $this->Cell($pageWidth * 0.4429, 5, "Certified:", 1);
        $this->Cell($pageWidth * 0.02381, 5, "D.", 1);
        $this->Cell(0, 5, "Approved for Payment", 1);
        $this->Ln();

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.02381, 6, '', 'L');
        $x = $this->getX();
        $y = $this->getY();
        $this->Rect($x, $y + 1, 8, 4);

        $this->Cell($pageWidth * 0.0381, 6, '', '');
        $this->Cell($pageWidth * 0.40476, 6, " Cash available", 'R');
        $this->Cell(0, 6, '', 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.02381, 6, '', 'L');
        $x = $this->getX();
        $y = $this->getY();
        $this->Rect($x, $y + 1, 8, 4);

        $this->Cell($pageWidth * 0.0381, 6, '', '');
        $this->Cell($pageWidth * 0.40476, 6, " Subject to Authority to Debit Account (when applicable)", 'R');
        $this->Cell(0, 6, '', 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.02381, 6, '', 'L');
        $x = $this->getX();
        $y = $this->getY();
        $this->Rect($x, $y + 1, 8, 4);

        $this->Cell($pageWidth * 0.0381, 6, '', '');
        $this->Cell($pageWidth * 0.40476, 6, " Supporting documents complete and amount claimed", 'R');
        $this->Cell(0, 6, '', 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.02381, 4, '', 'BL');
        $this->Cell($pageWidth * 0.0381, 4, '', 'B');
        $this->Cell($pageWidth * 0.40476, 4, "  proper", 'BR');
        $this->Cell(0, 4, '', 'BLR');
        $this->Ln();

        $this->Cell($pageWidth * 0.10476, 10, "Signature", 'BLR', '', 'C');
        $this->Cell($pageWidth * 0.3619, 10, '', 'BLR');
        $this->Cell($pageWidth * 0.10476, 10, "Signature", 'BLR', '', 'C');
        $this->Cell(0, 10, '', 'BLR');
        $this->Ln();

        $this->Cell($pageWidth * 0.10476, 5, "Printed Name", 'BLR', '', 'C');
        $this->SetFont('Times','B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.3619, 5, strtoupper($sign1), 'BLR', '', 'C');
        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.10476, 5, "Printed Name", 'BLR', '', 'C');
        $this->SetFont('Times','B', 10 + ($fontScale * 10));
        $this->Cell(0, 5, strtoupper($sign2), 'BLR', '', 'C');
        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Ln();

        $this->Cell($pageWidth * 0.10476, 10, '', 'BLR', '', 'C');
        $this->Cell($pageWidth * 0.3619, 10, "Head, Accounting Unit/Authorized Representative", 'BLR', '', 'C');
        $this->Cell($pageWidth * 0.10476, 10, '', 'BLR', '', 'C');
        $this->Cell(0, 10, "Agency Head/Authorized Representative", 'BLR', '', 'C');
        $this->Ln();

        $this->Cell($pageWidth * 0.10476, 5, "Date", 'BLR', '', 'C');
        $this->Cell($pageWidth * 0.3619, 5, $data->dv->date_accounting, 'BLR', '', 'C');
        $this->Cell($pageWidth * 0.10476, 5, "Date", 'BLR', '', 'C');
        $this->Cell(0, 5, '', 'BLR', $data->dv->date_agency_head, 'C');
        $this->Ln();

        //Section E
        $this->SetFont('Times', 'B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.02381, 5, "E.", 'LB');
        $this->Cell($pageWidth * 0.65, 5, "Receipt of Payment", 'LB');
        $this->SetFont('Times','', 10 + ($fontScale * 10));
        $this->Cell(0, 5, "JEV  No.", 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.105, 5, "Check/   ADA ", 'L', '', 'C');
        $this->Cell($pageWidth * 0.14, 5, '', 'L');
        $this->Cell($pageWidth * 0.09, 5, '', 'L');
        $this->Cell($pageWidth * 0.09, 5, "Date:", 'L');
        $this->Cell($pageWidth * 0.24881, 5, "Bank Name & Account Number:", 'L');
        $this->Cell(0, 5, '', 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.105, 5, "No. :", 'L', '', 'C');
        $this->Cell($pageWidth * 0.14, 5, '', 'L');
        $this->Cell($pageWidth * 0.09, 5, '', 'L');
        $this->Cell($pageWidth * 0.09, 5, '', 'L');
        $this->Cell($pageWidth * 0.24881, 5, '', 'L');
        $this->Cell(0, 5, '', 'LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.105, 10, "Signature:", 'TL', '', 'C');
        $this->Cell($pageWidth * 0.14, 5, '', 'TL');
        $this->Cell($pageWidth * 0.09, 5, '', 'TL');
        $this->Cell($pageWidth * 0.09, 5, "Date:", 'TL');
        $this->Cell($pageWidth * 0.24881, 5, "Printed Name:", 'TL');
        $this->Cell(0, 5, "Date", 'TLR');
        $this->Ln();

        $this->Cell($pageWidth * 0.105, 5, '', 'L');
        $this->Cell($pageWidth * 0.14, 5, '', 'L');
        $this->Cell($pageWidth * 0.09, 5, '', 'L');
        $this->Cell($pageWidth * 0.09, 5, '', 'L');
        $this->Cell($pageWidth * 0.24881, 5, '', 'L');
        $this->Cell(0, 5, '', " LR");
        $this->Ln();

        $this->Cell($pageWidth * 0.67381, 5, "Official Receipt No. & Date/Other Documents", 'TBL');
        $this->Cell(0, 5, '', 'BLR');
        $this->Ln();

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
