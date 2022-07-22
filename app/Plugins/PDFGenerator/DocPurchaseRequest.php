<?php

namespace App\Plugins\PDFGenerator;

class DocPurchaseRequest extends PDF {
    public function printPurchaseRequest($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->pr->id;

        $data->requested_by->name = strtoupper($data->requested_by->name);
        $data->approved_by->name = strtoupper($data->approved_by->name);
        $data->recommended_by->name = strtoupper($data->recommended_by->name);

        $data->pr->purpose = $data->pr->funding ?
                            'Purpose: ' . $data->pr->purpose .
                                "\n(Charged to ".'"'.$data->pr->funding->project_title.'")' :
                            'Purpose: ' . $data->pr->purpose;

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
        $this->Cell(0, 5, "PURCHASE REQUEST", "0", "", "C");
        $this->Ln(10);

        //Table header
        $this->SetFont('Times', 'BI', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.114, 5, "Fund Cluster:", "", "", "L");
        $this->SetFont('Times', 'BIU', 10 + ($fontScale * 10));
        $this->Cell(0, 5, "01", "", "", "L");
        $this->Ln(6);

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.224, '6', "Office/Section : ", "TLR", "", "L");
        $this->SetFont('Times', 'B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.3712, '6', "PR No.: " . $data->pr->pr_no, "TLR", "", "L");
        $this->Cell(0, '6', "Date: " . $data->pr->date_pr, "TLR", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.224, 7, $data->pr->office, "BLR", "", "C");
        $this->Cell($pageWidth * 0.3712, 7, "Responsibility Center Code : 19 001 03000 14", "B", "", "L");
        $this->Cell(0, 7, '', 'BLR', 0, 'L');
        $this->Ln();

        //Table data
        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->htmlTable($data->table_data);
        $this->Ln();

        //Table footer
        $this->MultiCell(0, 5, $data->pr->purpose, 'BLR', 'L');

        if ($data->pr->funding['source_name']) {
            $this->MultiCell(0, 5, 'Charged to: ' . $data->pr->funding['source_name'], 'BLR', 'L');
        }

        $this->Cell($pageWidth * 0.138, 5, "", "TLR", "", "L");
        $this->Cell($pageWidth * 0.362, 5, "Requested by: ", "TLR", "", "L");
        $this->Cell(0, 5, "Approved by: ", "TLR", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.138, 5, "Signature : ", "LR", "", "L");

        $xCoor = $this->GetX();
        $yCoor = $this->GetY();

        try {
            if (!empty($data->requested_by->signature)) {
                $options = [
                    "ssl" => [
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                    ],
                ];

                $context = stream_context_create($options);
                session_write_close();   // this is the key
                $img = file_get_contents(url($data->requested_by->signature), false, $context);

                $this->Image('@' . $img,
                            $xCoor + (($pageWidth * 0.362) / 3) + 3, $yCoor - 2, 16, 0, 'PNG');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        $this->Cell($pageWidth * 0.02, 5, "", "L", "", "L");
        $this->Cell($pageWidth * 0.322, 5, "", "B", "", "L");
        $this->Cell($pageWidth * 0.02, 5, "", "R", "", "L");

        $xCoor = $this->GetX();
        $yCoor = $this->GetY();

        try {
            if (!empty($data->approved_by->signature)  && !empty($data->pr->date_pr_approve)) {
                $options = [
                    "ssl" => [
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                    ],
                ];

                $context = stream_context_create($options);
                session_write_close();   // this is the key
                $img = file_get_contents(url($data->approved_by->signature), false, $context);

                $this->Image('@' . $img,
                            $xCoor + (($pageWidth * 0.362) / 3) + 5, $yCoor - 2, 16, 0, 'PNG');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        $this->Cell(0, 5, "", "LR", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.138, 5, "Printed Name : ", "LR", "", "L");
        $this->SetFont('Times', 'B', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.02, 5, "", "L", "", "L");
        $this->Cell($pageWidth * 0.322, 5, $data->requested_by->name, "B", "", "C");
        $this->Cell($pageWidth * 0.02, 5, "", "R", "", "L");
        $this->Cell($pageWidth * 0.04, 5, "", "", "", "L");
        $this->Cell($pageWidth * 0.322, 5, $data->approved_by->name, "B", "", "C");
        $this->Cell(0, 5, "", "R", "", "L");
        $this->Ln();

        $this->SetFont('Times', '', 10 + ($fontScale * 10));
        $this->Cell($pageWidth * 0.138, 5, "Designation : ", "LR", "", "L");
        $this->Cell($pageWidth * 0.02, 5, "", "L", "", "L");
        $this->Cell($pageWidth * 0.322, 5, $data->requested_by->position, "B", "", "C");
        $this->Cell($pageWidth * 0.02, 5, "", "R", "", "L");
        $this->Cell(0, 5, 'Regional Director', "LR", "", "C");
        $this->Ln();


        $this->Cell($pageWidth * 0.138, '6', "", "LR", "", "L");
        $this->Cell($pageWidth * 0.362, '6', "", "LR", "", "C");
        $this->Cell(0, '6', "", "LR", "", "C");
        $this->Ln();

        $this->Cell($pageWidth * 0.138, 5, "", "LR", "", "L");
        $this->Cell($pageWidth * 0.362, 5, "", "LR", "", "L");
        $this->SetFont('Times', 'IB', 10 + ($fontScale * 10));
        $this->Cell(0, 5, "Recommended by: ", "LR", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.138, 5, "", "LR", "", "L");
        $this->Cell($pageWidth * 0.362, 5, "", "LR", "", "C");
        $this->Cell(0, 5, "", "LR", "", "C");
        $this->Ln();

        $this->Cell($pageWidth * 0.138, 5, "", "LR", "", "L");
        $this->Cell($pageWidth * 0.362, 5, "", "LR", "", "C");
        $this->SetFont('Times', 'B', 10 + ($fontScale * 10));

        $xCoor = $this->GetX();
        $yCoor = $this->GetY();

        try {
            if (!empty($data->recommended_by->signature) && !empty($data->pr->date_pr_approve)) {
                $options = [
                    "ssl" => [
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                    ],
                ];

                $context = stream_context_create($options);
                session_write_close();   // this is the key
                $img = file_get_contents(url($data->recommended_by->signature), false, $context);

                $this->Image('@' . $img,
                            $xCoor + (($pageWidth * 0.362) / 3) + 5, $yCoor - 7, 16, 0, 'PNG');
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        $this->Cell($pageWidth * 0.04, 5, "", "L", "", "L");
        $this->Cell($pageWidth * 0.322, 5, $data->recommended_by->name, "B", "", "C");
        $this->Cell(0, 5, "", "R", "", "L");
        $this->Ln();

        $this->Cell($pageWidth * 0.138, 5, "", "LR", "", "L");
        $this->Cell($pageWidth * 0.362, 5, "", "LR", "", "C");
        $this->SetFont('Times', 'BI', 10 + ($fontScale * 10));
        $this->Cell(0, 5, 'Division Chief / PSTD', "LR", "", "C");
        $this->Ln();

        $this->Cell($pageWidth * 0.138, 5, "", "BLR", "", "L");
        $this->Cell($pageWidth * 0.362, 5, "", "BLR", "", "C");
        $this->Cell(0, 5, "", "BLR", "", "C");
        $this->Ln();

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
