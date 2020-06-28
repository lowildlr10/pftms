<?php

namespace App\Plugins\PDFGenerator;

class DocPropertyLabel extends PDF {
    public function printPropertyLabel($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(0, 0, 0);
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);

        //Set auto page breaks
        $this->SetAutoPageBreak(false, 0);

        //Set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $this->setLanguageArray($l);
        }

        //Set default font subsetting mode
        $this->setFontSubsetting(true);

        /* ------------------------------------- End of Config ------------------------------------- */

        foreach ($data->label_data as $dat) {
            //Add a page
            $this->AddPage();

            /* ------------------------------------- Start of Doc ------------------------------------- */

            //Content
            $img_file = url('images/label.png');
            $arrContextOptions = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ];
            $img = file_get_contents($img_file, false,
                                    stream_context_create($arrContextOptions));
            $this->Image('@' . $img, 0, 0, $pageWidth, $pageHeight, 'PNG', '', '', false, 300, '', false, false, 0);

            $this->ln(13);

            $this->SetFont('Times', '', 8 + ($fontScale * 8));
            $this->htmlTable($dat->data1);
            $this->SetFont('Times', '', 7 + ($fontScale * 7));
            $this->htmlTable($dat->data2);
            $this->htmlTable($dat->data3);
            $this->htmlTable($dat->data4);

            // Barcode
            /*
            if ($data->property_no != 'N/A') {
                $this->setXY(89.3, 157);

                $type = 'code128';
                //$type = 'code39';
                $black = '000000'; // color in hexa

                $code = $data->id . ':' . $data->received_by; // barcode

                $this->StartTransform();
                $this->Rotate(90);
                $barcodeStyle = ['position' => 'S',
                                'align' => 'C',
                                'stretch' => true,
                                'fitwidth' => false,
                                'cellfitalign' => '',
                                'border' => true,
                                'hpadding' => 2,
                                'vpadding' => 3,
                                'fgcolor' => array(0, 0, 0),
                                'bgcolor' => false,
                                'text' => false,
                                'font' => 'helvetica',
                                'fontsize' => 8,
                                'stretchtext' => 4];
                $this->write1DBarcode($code, 'C39', '', '', $pageHeight, 15, 0.4, $barcodeStyle, 'M');
                $this->StopTransform();
            } else {
                $this->setXY(89.3, 190);
                $this->SetFont('helvetica', '', 13);
                $this->StartTransform();
                $this->Rotate(90);
                $this->Cell($pageHeight, 15, '-- No Barcode --', 1, '', 'C');
                $this->StopTransform();
            }*/

            $this->setXY(89.3, 157);

            $type = 'code128';
            //$type = 'code39';
            $black = '000000'; // color in hexa

            $code = $dat->stock_id . '-' . $dat->received_by; // barcode

            $this->StartTransform();
            $this->Rotate(90);
            $barcodeStyle = ['position' => 'S',
                            'align' => 'C',
                            'stretch' => true,
                            'fitwidth' => false,
                            'cellfitalign' => '',
                            'border' => true,
                            'hpadding' => 2,
                            'vpadding' => 3,
                            'fgcolor' => array(0, 0, 0),
                            'bgcolor' => false,
                            'text' => false,
                            'font' => 'helvetica',
                            'fontsize' => 8,
                            'stretchtext' => 4];
            $this->write1DBarcode($code, 'C39', '', '', $pageHeight, 15, 0.4, $barcodeStyle, 'M');
            $this->StopTransform();

            /* ------------------------------------- End of Doc ------------------------------------- */
        }
    }
}
