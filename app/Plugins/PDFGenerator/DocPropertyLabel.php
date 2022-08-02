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

        foreach ($data->label_data as $ctr => $dat) {
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
            $this->Image('@' . $img, 0, 5, $pageWidth + 16.5, $pageHeight - 5, 'PNG', '', '', false, 300, '', false, false, 0);

            $this->ln(19);

            $this->SetFont('Times', '', 8 + ($fontScale * 8));
            $this->htmlTable($dat->data1);
            $this->SetFont('Times', '', 7.5 + ($fontScale * 8));
            $this->htmlTable($dat->data2);
            $this->htmlTable($dat->data3);
            $this->htmlTable($dat->data4);

            $code = $dat->stock_id[$ctr]; // barcode

            $style = array(
                'border' => false,
                'vpadding' => 1,
                'hpadding' => 1,
                'fgcolor' => [0, 0, 0],
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );
            $this->write2DBarcode($code, 'QRCODE,H', 86, 50.6, 12, 12, $style, 'N');


            $type = 'code128';
            //$type = 'code39';
            $black = '000000'; // color in hexa

            //$this->setXY(88.9, 200);
            //$this->setXY(0.8, 0);
            //$this->StartTransform();
            //$this->Rotate(90);
            $barcodeStyle = [
                'position' => 'S',
                'align' => 'C',
                'stretch' => true,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'hpadding' => 0,
                'vpadding' => 10,
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false,
                'text' => false,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 4
            ];
            $this->write1DBarcode($code, 'C39', 0.4, -2.2, 97.9, 9.4, 0.2, $barcodeStyle, 'M');
            //$this->StopTransform();

            /* ------------------------------------- End of Doc ------------------------------------- */
        }
    }
}
