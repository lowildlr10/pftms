<?php

namespace App\Plugins\PDFGenerator;

class DocSummaryListDueDemandable extends PDF {
    public function printSummary($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $docSubject = "Summary of LDDAP-ADAs Issued and Invalidated ADA Entries";

        /* ------------------------------------- Start of Config ------------------------------------- */

        //set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Set margins
        $this->SetMargins(5, 10, 5);
        $this->SetHeaderMargin(10);
        $this->SetFooterMargin(5);
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);

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

        //Table Content
        $this->SetFont('Times', '', 8 + ($fontScale * 8));
        $this->htmlTable($data->table_data);

        /* ------------------------------------- End of Doc ------------------------------------- */
    }
}
