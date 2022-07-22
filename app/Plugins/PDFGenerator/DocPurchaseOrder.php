<?php

namespace App\Plugins\PDFGenerator;

class DocPurchaseOrder extends PDF {
    public function printPurchaseOrder($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->po->id;

        $poDate = $data->po->date_po;
        $appName = strtoupper($data->po->sig_approval);
        $deptName = strtoupper($data->po->sig_funds_available);

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
        $this->SetFont('Times','B', 14 + ($fontScale * 14));
        $this->MultiCell(0, 5, "PURCHASE ORDER", '', 'C');

        $this->ln(3);

        $this->SetFont('Times', '', 11 + ($fontScale * 11));

        $_x1 = $this->GetX();
        $_y1 = $this->GetY();

        $_x = $this->GetX();
        $_y = $this->GetY();

        $x1_1 = $_x;
        $x1_2 = $_x + $pageWidth * 0.548;
        $x1_3 = $_x + $pageWidth * 0.905;

        //Table header 1
        $this->MultiCell($pageWidth * 0.0857, '7', "Supplier: ", "TL");
        $this->SetXY($_x + $pageWidth * 0.0857, $_y);
        $this->SetFont('Times', 'B', 11 + ($fontScale * 11));
        $this->MultiCell($pageWidth * 0.462, '6', $data->po->company_name, "T", "L");
        $_x2 = $this->GetX();
        $_y2 = $this->GetY();
        $this->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462), $_y);
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell($pageWidth * 0.1, '7', 'P.O. No. :', "TL");
        $this->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462) + ($pageWidth * 0.1), $_y);
        $this->SetFont('Times', 'B', 11 + ($fontScale * 11));
        $this->MultiCell(0, '7', $data->po->po_no, "TR");

        $_x = $this->GetX();
        $_y = $this->GetY();

        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell($pageWidth * 0.0857, '5', "Address: ", "");
        $this->SetXY($_x + $pageWidth * 0.0857, $_y);
        $this->MultiCell($pageWidth * 0.462, '5', $data->po->address, "", 'L');
        $_yTemp = $this->GetY();
        $this->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462), $_y);
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell($pageWidth * 0.1, '5', 'Date  : ', "");
        $this->SetXY($_x + ($pageWidth * 0.0857) + ($pageWidth * 0.462) + ($pageWidth * 0.1), $_y);
        $this->SetFont('Times', 'B', 11 + ($fontScale * 11));
        $this->MultiCell(0, '5', $poDate, "", "L");

        $_x = $this->GetX();
        $_yTemp += 2;

        $x2_1 = $_x;
        $x2_2 = $_x + $pageWidth * 0.548;
        $x2_3 = $_x + $pageWidth * 0.905;

        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->SetXY($_x, $_yTemp);
        $this->MultiCell('115', '7', "TIN: ________________________________________________", "L");
        $this->SetXY($_x + $pageWidth * 0.548, $_yTemp);
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell('43', '7', 'Mode of Procurement:', 0, 'L');
        $this->SetXY($_x + $pageWidth * 0.752, $_yTemp);
        $this->SetFont('Times', 'B', 11 + ($fontScale * 11));
        $this->MultiCell(0, '7', $data->po->mode_name, 'R', 'L');
        $_yTemp = $this->GetY();

        $this->Line($_x1, $_y1, $x2_1, $_yTemp);
        $this->Line($x1_2 - 0.06, $_y2 - 5, $x2_2 - 0.06, $_yTemp);
        $this->Line($x1_3 - 0.06, $_y2 - 5, $x2_3 - 0.06, $_yTemp);

        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->Cell(0, '7', "Gentlemen:", "RLT");
        $this->ln();
        $this->Cell(0, '11', "                Please furnish this Office the following articles" .
                                " subject to the terms and conditions contained herein:", "RLB");
        $this->ln();

        //Table header 2
        $this->htmlTable($data->po->table_header);

        //Table data
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->htmlTable($data->po->table_data);

        //Table footer
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->MultiCell(0, '5',
                        "\n\t\t\t\t\t In case of failure to make the full delivery within the time " .
                        "specified above, a penalty of one-tenth (1/10) of one \n" .
                        "percent for every day of delay shall be imposed on the undelivered item/s.", 'LR', 'L');
        $this->Cell(0, '5', '','LR');
        $this->Ln();

        $this->Cell($pageWidth * 0.0524,'5',' ','L');
        $this->Cell($pageWidth * 0.229,'5','Conforme:','');
        $this->Cell(0,'5',"Very Truly Yours,",'R','','C');
        $this->Ln();

        $this->Cell(0,'10',' ','LR');

        $this->Ln();

        $this->SetFont('Times', 'B', 11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.0952,'5','','L');
        $this->Cell($pageWidth * 0.424,'5','___________________________________', '','','L');
        $this->Cell(0,'5',"". $appName ."",'R','','C');

        $this->Ln();

        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.0952,'5','','L');
        $this->Cell($pageWidth * 0.438,'5',"\t Signature over Printed Name of Supplier", '','','L');
        $this->SetFont('Times', 'BI', 11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.119,'5',"Regional Director",'','','C');
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->Cell(0,'5',"or Authorized Representative",'R','','C');

        $this->Ln();

        $this->Cell($pageWidth * 0.0952,'5','','L');
        $this->Cell($pageWidth * 0.5,'5',"\t\t\t\t ______________________________",'','','L');
        $this->Cell(0,'5',"",'R','','C');

        $this->Ln();

        $this->Cell($pageWidth * 0.238,'5','','L');
        $this->Cell($pageWidth * 0.45238,'5',"Date",'','','L');
        $this->Cell(0,'5','','R');

        $this->Ln();

        $this->Cell(0,'5','','LR');

        $this->Ln();

        $this->SetFont('Times', 'BI', 10 + ($fontScale * 10));
        $this->MultiCell(0, '5',
                        "Please provide your bank details (account name, ".
                        "account number, business name) to facilitate payment ".
                        "processing after delivery. Landbank is preferred.", 'LRB', 'L');

        $this->Cell($pageWidth * 0.45238,'5','','L');
        $this->Cell(0,'5','','LR');

        $this->Ln();

        $this->SetFont('Times', 'IB', 11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.45238,'5','Fund Cluster : 01','L','','L');
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->Cell(0,'5',"ORS/BURS No. : _____________________________",'LR','','L');

        $this->Ln();

        $this->Cell($pageWidth * 0.45238,'5',"Funds Available : ____________________________",'L','','L');
        $this->Cell(0,'5',"Date of the ORS/BURS : _______________________",'LR','','L');

        $this->Ln();

        $this->Cell($pageWidth * 0.45238,'5','','L');
        $this->SetFont('Times', '', 11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.0857,'5',"Amount : ",'L','','L');
        $this->SetFont('Times','U',11 + ($fontScale * 11));

        if ($data->po->grand_total) {
            $this->Cell(0, '5', "Php " . $data->po->grand_total, 'R', '', 'L');
        } else {
            $this->Cell(0, '5', "Php 0.00", 'R', '', 'L');
        }

        $this->Ln();

        $this->Cell($pageWidth * 0.45238,'5','','L');
        $this->Cell(0,'5','','LR');

        $this->Ln();

        $this->SetFont('Times','BU',11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.45238,'5',"".strtoupper($deptName)."",'L','','C');
        $this->Cell(0,'5','','LR');
        $this->SetFont('Times', '', 11 + ($fontScale * 11));

        $this->Ln();

        $this->SetFont('Times','I',11 + ($fontScale * 11));
        $this->Cell($pageWidth * 0.45238,'5','Signature over Printed Name of Chief','LR','','C');
        $this->Cell(0,'5','','R','L');

        $this->Ln();

        $this->Cell($pageWidth * 0.45238,'5','Accountant/Head of Accounting Division/Unit ','BL','','C');
        $this->Cell(0,'5','','BLR','L');

        $this->Ln();

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
