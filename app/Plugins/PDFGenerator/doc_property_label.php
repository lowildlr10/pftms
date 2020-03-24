<?php

/* ------------------------------------- Start of Config ------------------------------------- */

//set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//Set margins
$pdf->SetMargins(0, 0, 0);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

//Set auto page breaks
$pdf->SetAutoPageBreak(false, 0);

//Set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

//Set default font subsetting mode
$pdf->setFontSubsetting(true);

/* ------------------------------------- End of Config ------------------------------------- */

foreach ($_data as $data) {
    //Add a page
    $pdf->AddPage();

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
    $pdf->Image('@' . $img, 0, 0, $pageWidth, $pageHeight, 'PNG', '', '', false, 300, '', false, false, 0);

    $pdf->ln(13);

    $pdf->SetFont('Times', '', 8 + ($increaseFontSize * 8));
    $pdf->htmlTable($data->data1);
    $pdf->SetFont('Times', '', 7 + ($increaseFontSize * 7));
    $pdf->htmlTable($data->data2);
    $pdf->htmlTable($data->data3);
    $pdf->htmlTable($data->data4);

    // Barcode
    /*
    if ($data->property_no != 'N/A') {
        $pdf->setXY(89.3, 157);

        $type = 'code128';
        //$type = 'code39';
        $black = '000000'; // color in hexa

        $code = $data->id . ':' . $data->received_by; // barcode

        $pdf->StartTransform();
        $pdf->Rotate(90);
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
        $pdf->write1DBarcode($code, 'C39', '', '', $pageHeight, 15, 0.4, $barcodeStyle, 'M');
        $pdf->StopTransform();
    } else {
        $pdf->setXY(89.3, 190);
        $pdf->SetFont('helvetica', '', 13);
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->Cell($pageHeight, 15, '-- No Barcode --', 1, '', 'C');
        $pdf->StopTransform();
    }*/

    $pdf->setXY(89.3, 157);

    $type = 'code128';
    //$type = 'code39';
    $black = '000000'; // color in hexa

    $code = $data->stock_id . '-' . $data->received_by; // barcode

    $pdf->StartTransform();
    $pdf->Rotate(90);
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
    $pdf->write1DBarcode($code, 'C39', '', '', $pageHeight, 15, 0.4, $barcodeStyle, 'M');
    $pdf->StopTransform();

    /* ------------------------------------- End of Doc ------------------------------------- */
}
