<?php

namespace App\Plugins\PDFGenerator;

use \TCPDF;
use Carbon\Carbon;

class PDF extends TCPDF {
    // variable to store widths and aligns of cells, and line height
    public $headerIcon = true;
    public $headerVerRev = true;
    public $docCode;
    public $docId;
    public $docRev;
    public $docRevDate;
    public $fontScale = 0;
    public $headerFontSize = 9;
    public $footerFontSize = 9;
    public $customPageNo = null;
    public $onlyPageNo = false;
    public $isPageGrouped = false;
    public $generateInfoOnly = false;

    /*
    //Set the array of column alignments
    public function SetAligns($a) {
        $formatedAlign = [];

        foreach ($a as $align) {
            $align = strtolower($align);

            switch ($align) {
                case 'l':
                    $formatedAlign[] = "left";
                    break;
                case 'r':
                    $formatedAlign[] = "right";
                    break;
                case 'c':
                    $formatedAlign[] = "center";
                    break;

                default:
                    $formatedAlign[] = "center";
                    break;
            }
        }

        $this->aligns = $formatedAlign;
    }*/

    public function setDocCode($docCode) {
        $this->docCode = $docCode;
    }

    public function setDocRevision($docRev) {
        $this->docRev = $docRev;
    }

    public function setRevDate($revDate) {
        $this->docRevDate = $revDate;
    }

    public function setHeaderLR($set1, $set2) {
        $this->headerIcon = $set1;
        $this->headerVerRev = $set2;
    }

    public function setFontScale($fontScale) {
        $this->fontScale = $fontScale;
    }

    public function setCustomPageNo($customPageNo) {
        $this->customPageNo = $customPageNo;
    }

    public function setIsPageNoOnly($bool) {
        $this->onlyPageNo = $bool;
    }

    public function setIsPageGrouped($bool) {
        $this->isPageGrouped = $bool;
    }

    private function htmlFontStyle($key) {
        $style = '';
        $charArray = str_split($key);

        if (in_array('', $charArray)) {
            $style = '';
        }

        if (in_array('B', $charArray)) {
            $style .= 'font-weight: bold;';
        }

        if (in_array('I', $charArray)) {
            $style .= 'font-style: italic;';
        }

        if (in_array('U', $charArray)) {
            $style .= 'text-decoration: underline;';
        }

        $style = 'style="'.$style.'"';

        return $style;
    }

    public function htmlTable($data) {
        $html = '<table>';

        foreach ($data as $dat) {
            if ($dat['type'] == 'row-title') {
                $aligns = $dat['aligns'];
                $widths = $dat['widths'];
                $html .= '<thead>';

                foreach ($dat['data'] as $row) {
                    $html .= '<tr>';

                    foreach ($row as $key => $val) {
                        $html .= '<th width="'.$widths[$key].'%">'.$val.'</th>';
                    }

                    $html .= '</tr>';
                }

                $html .= '</thead>';
            } else if ($dat['type'] == 'row-data' || $dat['type'] == 'other') {
                $aligns = $dat['aligns'];
                $widths = $dat['widths'];
                $withColSpan = false;
                $html .= '<tbody>';

                if (isset($dat['col-span']) && $dat['col-span']) {
                    $withColSpan = true;
                    $columnDataKeys = [];

                    foreach ($dat['col-span-key'] as $cKey) {
                        $arrKey = explode('-', $cKey);

                        if (count($arrKey) > 1) {
                            $_columnDataKeys = [];

                            for ($i = $arrKey[0]; $i <= $arrKey[1]; $i++) {
                                $_columnDataKeys[] = (int)$i;
                            }

                            $columnDataKeys[] = $_columnDataKeys;
                        } else {
                            $columnDataKeys[] = [(int)$arrKey[0]];
                        }
                    }
                }

                foreach ($dat['data'] as $row) {
                    $html .= '<tr>';

                    if ($withColSpan) {
                        foreach ($columnDataKeys as $cKey) {
                            $widthPercent = 0;
                            $colSpanCount = count($cKey);

                            if ($colSpanCount > 0) {
                                foreach ($cKey as $index => $_cK) {
                                    if ($index == 0) {
                                        $val = $row[$_cK];
                                        $align = $aligns[$_cK];
                                        $fontStyle = $this->htmlFontStyle(trim($dat['font-styles'][$_cK]));
                                    }

                                    $widthPercent += floatval($dat['widths'][$_cK]);
                                }
                            }

                            $html .= '<td '.$fontStyle.' width="'.$widthPercent.'%" colspan="'.
                                     $colSpanCount.'" align="'. $align .'">'.$val.'</td>';
                        }
                    } else {
                        foreach ($row as $key => $val) {
                            $fontStyle = $this->htmlFontStyle(trim($dat['font-styles'][$key]));
                            $html .= '<td '.$fontStyle.' width="'.$widths[$key].'%" align="'.
                                     $aligns[$key] .'">'.$val.'</td>';
                        }
                    }

                    $html .= '</tr>';
                }

                $html .= '</tbody>';
            }
        }

        $html .= '</table>
        <style>
            body {margin: 0 !important}
            table {border-collapse:collapse; padding: 2px;}
            th,td {border: 1px solid #000; }
            tr>th {font-weight: bold; text-align: center; vertical-align: middle;}
        </style>';

        $tidyConfig = [
            'output-html' => true,
            'indent' => false,
            'indent-attributes' => false,
            'wrap' => 0,
            'vertical-space' => false,
        ];
        $tidy = tidy_parse_string($html, $tidyConfig);
        $html = $tidy->html();
        $html = trim(str_replace("\r", '', $html));
        $html = trim(str_replace("\n", '', $html));

        $this->writeHTML($html, false, false, false, false);
    }

    public function header() {
        $curOrientation = $this->CurOrientation;
        $xCoor = $this->GetX();
        $yCoor = $this->GetY();
        $pageWidth = $this->w;

        $aliasNumberPage = $this->getAliasNumPage();
        $aliasNbPages = $this->getAliasNbPages();
        $pageNo = !$this->customPageNo ?
                  "Page $aliasNumberPage of $aliasNbPages" :
                  $this->customPageNo;

        if ($curOrientation == 'P') {
            $mulltiplier1 = 0.09;;
            $mulltiplier2 = 0.67;
        } else if ($curOrientation == 'L') {
            $mulltiplier1 = 0.06;
            $mulltiplier2 = 0.77;
        }

        if ($this->headerIcon && $this->headerVerRev) {
            $this->SetXY($xCoor, $yCoor);
            //$this->Image(url('images/logo/dostlogo.png'), $xCoor + 1, $yCoor, 16, 0);
            $arrContextOptions = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ];
            $img = file_get_contents(url('images/logo/dostlogo.png'), false,
                                     stream_context_create($arrContextOptions));
            $this->Image('@'.$img, $xCoor + 1, $yCoor, 16, 0);

            $this->SetFont('helvetica', '', 9);
            $this->Cell($pageWidth * $mulltiplier1, 4, '');
            $this->Cell($pageWidth * $mulltiplier2, 4, 'Republic of the Philippines');
            $this->SetFont('helvetica', 'B', 9);
            $this->Cell(0, 4, "\t".$this->docCode, 'LRT');
            $this->Ln();

            $this->SetFont('helvetica', 'B', 9);
            $this->Cell($pageWidth * $mulltiplier1, 4, '');
            $this->Cell($pageWidth * $mulltiplier2, 4, 'DEPARTMENT OF SCIENCE AND TECHNOLOGY');
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 4, "\t".$this->docRev, 'LR');
            $this->Ln();

            $this->SetFont('helvetica', '', 9);
            $this->Cell($pageWidth * $mulltiplier1, 4, '');
            $this->Cell($pageWidth * $mulltiplier2, 4, 'Cordillera Administrative Region');
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 4, "\t".$pageNo, 'LR');
            $this->Ln();

            $this->SetFont('helvetica', '', 9);
            $this->Cell($pageWidth * $mulltiplier1, 4, '');
            $this->Cell($pageWidth * $mulltiplier2, 4, 'Km. 6, La Trinidad, Benguet');
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 4, "\t".$this->docRevDate, 'LRB');
            $this->Ln();
        } elseif (!$this->headerIcon && $this->headerVerRev) {
            $this->SetFont('helvetica', '', 9);
            $this->Cell($pageWidth * $mulltiplier1, 4, '');
            $this->Cell($pageWidth * $mulltiplier2, 4, '');
            $this->SetFont('helvetica', 'B', 9);
            $this->Cell(0, 4, "\t".$this->docCode, 'LRT');
            $this->Ln();

            $this->SetFont('helvetica', 'B', 9);
            $this->Cell($pageWidth * $mulltiplier1, 4, '');
            $this->Cell($pageWidth * $mulltiplier2, 4, '');
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 4, "\t".$this->docRev, 'LR');
            $this->Ln();

            $this->SetFont('helvetica', '', 9);
            $this->Cell($pageWidth * $mulltiplier1, 4, '');
            $this->Cell($pageWidth * $mulltiplier2, 4, '');
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 4, "\t".$this->docRevDate, 'LRB');
            $this->Ln();
        }
    }

    public function Footer() {

        if (!$this->generateInfoOnly) {
            // Go to 1.5 cm from bottom
            $this->SetY(-15);
            // Select helvetica italic 8
            $this->SetFont('helvetica', '', $this->footerFontSize + ($this->fontScale * $this->footerFontSize));
            // Print current and total page numbers

            //$this->Cell(0,10,'Page '.$this->pageNo().'/{nb}',0,0,'C');

            if (!$this->onlyPageNo) {
                $this->Cell(0, 4, 'This document shall be deemed uncontrolled unless labelled "CONTROLLED"', 0, 0, 'C');
                $this->ln();

                $this->Cell($this->w - ($this->w * 0.51), 4, 'User should verify latest', 0, 0, 'R');
                $this->SetFont('helvetica', 'B', $this->footerFontSize + ($this->fontScale * $this->footerFontSize));
                $this->Cell(0, 4, ' revision.', 0, 0, 'L');
                $this->ln();
            }

            $this->SetFont('helvetica', 'I', 7);

            if (!$this->isPageGrouped) {
                $pageNo = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
            } else {
                $pageNo = 'Page '.$this->getPageNumGroupAlias().' of '.$this->getPageGroupAlias();
            }

            $this->Cell(0, 3, $pageNo, 0, 0, 'R');

            $this->GenerateInfo();
        } else {
            $this->GenerateInfo();
        }
    }

    public function GenerateInfo() {
        $pageWidth = $this->w;
        $pageHeight = $this->h;
        $code = $this->docId; // qrcode

        if (isset($code) && !empty($code)) {
            $style = array(
                'border' => false,
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => [64, 64, 64],
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );
            $this->write2DBarcode($code, 'QRCODE,H', 0, $pageHeight - 20, 25, 16, $style, 'N');

            $this->SetTextColor(96, 96, 96);
            $this->StartTransform();
            $this->setXY(21, $pageHeight - 19);
            //$this->SetFont('helvetica', 'IB', 6);
            //$this->Write(0, 'Last Printed/Generated:', '', 0, '', true, 0, false, false, 0);
            //$this->setXY(21, $pageHeight - 16);
            //$this->SetFont('helvetica', 'I', 6);
            //$this->Write(0, Carbon::now(), '', 0, '', true, 0, false, false, 0);
            $this->setXY(21, $pageHeight - 11);
            $this->SetFont('helvetica', 'IB', 6);
            $this->Write(0, 'PFTMS Doc ID:', '', 0, '', true, 0, false, false, 0);
            $this->setXY(21, $pageHeight - 8);
            $this->SetFont('helvetica', 'I', 6);
            $this->Write(0, $code, '', 0, '', true, 0, false, false, 0);
            $this->StopTransform();
        }
    }

}
