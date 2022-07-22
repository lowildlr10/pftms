<?php

namespace App\Plugins\PDFGenerator;

class DocAbstractQuotation extends PDF {
    public function printAbstractQuotation($data) {
        $pageHeight = $this->h;
        $pageWidth = $this->w;
        $fontScale = $this->fontScale;

        $this->docId = $data->abstract->id;

        $prNo = $data->pr->pr_no;
        $abstractDate = $data->abstract->date_abstract;

        $chairperson = strtoupper($data->sig_chairperson->name);
        $viceChairperson = strtoupper($data->sig_vice_chairperson->name);
        $member1 = strtoupper($data->sig_first_member->name);
        $member2 = strtoupper($data->sig_second_member->name);
        $member3 = strtoupper($data->sig_third_member->name);
        $endUser = strtoupper($data->sig_end_user->name);
        $modeProcurement =  $data->abstract->mode_name ?  $data->abstract->mode_name : '________________';

        $abstractSigs = [$chairperson, $viceChairperson, $member1,
                         $member2, $member3, $endUser];
        $sigPosition = ["Chairperson", "Vice Chairperson", "Member",
                        "Member", "Member", "End-user"];

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

        foreach ($data->abstract_items as $abstract) {
            $bidderCount = $abstract->bidder_count;
            $totalWidthDisplay = $pageWidth - 20;
            $totalWidth1 = $totalWidthDisplay * 0.83;
            $totalWidth2 = $totalWidthDisplay * 0.17;
            $bidderTotalWidth = $totalWidth1 * 0.71;

            if ($bidderCount != 0) {
                $bidderWidth = $bidderTotalWidth / $bidderCount;
            } else {
                $bidderWidth = $bidderTotalWidth / 3;
            }

            if ($bidderCount > 0) {
                $this->AddPage();

        /* ------------------------------------- Start of Doc ------------------------------------- */

                // TABLE TITLE
                $this->SetFont('helvetica', 'B', 10 + ($fontScale * 10));
                $this->Cell($pageWidth * 0.948, 5, 'ABSTRACT OF QUOTATION', "", "",'C');
                $this->Ln(10);

                $x = $this->GetX();
                $y = $this->GetY();

                // Row group
                $this->SetFont('helvetica', 'BI', 9 + ($fontScale * 9));
                $this->MultiCell($totalWidth1 / 2, 5.25, "Purchase Request No.: $prNo \nPMO/End-User : $endUser", "LTB", "L", "");
                $this->SetXY($x + ($totalWidth1 / 2), $y);
                $this->MultiCell($totalWidth1 / 2, 5.25, "Date Prepared: $abstractDate " .
                                                    "\n" .
                                                    "Mode of Procurement : $modeProcurement ", "RTB", "R", "");
                $this->SetXY($x + $totalWidth1, $y);
                $this->SetFont('helvetica', 'BI', 8 + ($fontScale * 8));
                $this->setCellHeightRatio(0.95);
                $this->MultiCell(0, 3.5, "based on the canvasses submitted,\n WE, the members of the " .
                                                    "Bids and\n Awards Committee (BAC) ", "TR", "C", "");

                // Row group
                $this->SetFont('helvetica', '', 8 + ($fontScale * 8));
                $this->Cell($totalWidth1 * 0.04, 4, '', 'LR', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.13, 4, '', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 4, '', 'R', '', 'C');
                $this->SetFont('helvetica', 'BI', 8 + ($fontScale * 8));
                $this->Cell($bidderTotalWidth, 3.6, "BIDDER'S QUOTATION AND OFFER", 'RB', '', 'C');
                $this->SetFont('helvetica', 'BI', 9 + ($fontScale * 9));
                $this->MultiCell(0, 3.5, "RECOMMEND the following", "R", "C", "");


                // Row group
                $this->SetFont('helvetica', '', 8 + ($fontScale * 8));
                $this->Cell($totalWidth1 * 0.04, 3.6, 'ITEM', 'LR', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, 'QTY', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, 'UNIT', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.13, 3.6, 'P A R T I C U L A R S', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, 'ABC', 'R', '', 'C');

                for ($bidCount = 1; $bidCount <= $bidderCount; $bidCount++) {
                    $this->Cell($bidderWidth, 3.6, '', 'R', '', 'C');
                }

                $this->SetFont('helvetica', 'BI', 9 + ($fontScale * 9));
                $this->MultiCell(0, 3.5, "items to be AWARDED as", "R", "C", "");

                // Row group
                $this->SetFont('helvetica', '', 8 + ($fontScale * 8));
                $this->Cell($totalWidth1 * 0.04, 3.6, 'NO.', 'LR', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, '', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, '', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.13, 3.6, '', 'R', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, '(Unit', 'R', '', 'C');

                foreach ($abstract->suppliers as $list) {
                    $strLength = strlen($list->company_name);
                    $bidderLists[] = array('', $list->company_name);

                    if ($bidderCount == 3) {
                        if ($strLength > 30) {
                            $this->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 30) .
                                    '...', 'RB', '', 'C');
                        } else {
                            $this->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                        }
                    } else if ($bidderCount == 4) {
                        if ($strLength > 20) {
                            $this->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 20) .
                                    '...', 'RB', '', 'C');
                        } else {
                            $this->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                        }
                    } else if ($bidderCount >= 5) {
                        if ($strLength > 15) {
                            $this->Cell($bidderWidth, 3.6, substr(strtoupper($list->company_name), 0, 15) .
                                    '...', 'RB', '', 'C');
                        } else {
                            $this->Cell($bidderWidth, 3.6, strtoupper($list->company_name), 'RB', '', 'C');
                        }
                    }
                }

                $this->SetFont('helvetica', 'BI', 9 + ($fontScale * 9));
                $this->MultiCell(0, 3.5, "follows:", "RB", "C", "");

                // Row group
                $this->SetFont('helvetica', '', 8 + ($fontScale * 8));
                $this->Cell($totalWidth1 * 0.04, 3.6, '', 'LRB', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, '', 'RB', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, '', 'RB', '', 'C');
                $this->Cell($totalWidth1 * 0.13, 3.6, '', 'RB', '', 'C');
                $this->Cell($totalWidth1 * 0.04, 3.6, 'Cost)', 'RB', '', 'C');

                for ($bidCount = 1; $bidCount <= $bidderCount; $bidCount++) {
                    if ($bidderCount == 3) {
                        $this->SetFont('helvetica', '', 8 + ($fontScale * 8));
                    } else if ($bidderCount == 4) {
                        $this->SetFont('helvetica', '', 7 + ($fontScale * 7));
                    } else if ($bidderCount >= 5) {
                        $this->SetFont('helvetica', '', 5.5 + ($fontScale * 5.5));
                    }

                    $this->Cell($bidderWidth * 0.25, 3.6, 'Unit Cost', 'RB', '', 'C');
                    $this->Cell($bidderWidth * 0.25, 3.6, 'Total Cost', 'RB', '', 'C');

                    if ($bidderCount == 3) {
                        $this->SetFont('helvetica', 'BI', 8 + ($fontScale * 8));
                    } else if ($bidderCount == 4) {
                        $this->SetFont('helvetica', 'BI', 7 + ($fontScale * 7));
                    } else if ($bidderCount >= 5) {
                        $this->SetFont('helvetica', 'BI', 5.5 + ($fontScale * 5.5));
                    }

                    $this->Cell($bidderWidth * 0.5, 3.6, 'Specification', 'RB', '', 'C');
                }

                $this->Cell(0, 3.6, '', 'RB', '', 'C');
                $this->Ln();

                //Table data
                $this->SetFont('helvetica', '', 8 + ($fontScale * 8));
                $this->htmlTable($abstract->table_data);

                $this->Ln(2.5);
                $this->SetFont('helvetica', '', 8 + ($fontScale * 8));
                $this->Cell(0, 0, "We hereby certify that we have witnessed the opening of bids/quotations and that the prices/quotations contained herein are the true and correct.");
                $this->Ln(5);

                // Recommendation
                $this->SetFont('helvetica', 'BI', 9 + ($fontScale * 9));
                $this->Cell($totalWidth1 + $totalWidth2, 5, "Recommendation:", '', 0, 'L', 0);
                $this->Ln(5);

                $this->Cell(0, 2, "", 'B', 1, 'L', 0);
                $this->Cell(0, 2, "", 'B', 1, 'L', 0);
                $this->Cell(0, 2, "", 'B', 1, 'L', 0);
                $this->Cell(0, 2, "", 'B', 1, 'L', 0);
                $this->Ln(5);

                // Bids and Committee awardee
                $this->SetFont('helvetica', 'B', 10 + ($fontScale * 10));
                $this->SetTextColor(0, 0, 0);
                $this->Ln(5); // LINE BREAK

                $this->Cell($totalWidthDisplay * 0.83, 5, "BIDS AND AWARDS COMITTEE:", '', 0, 'L', 0);
                $this->SetFont('helvetica', '', 10 + ($fontScale * 10));
                $this->Cell($totalWidthDisplay * 0.32, 5, "", '', 0, 'L', 0);
                $this->Ln(); // LINE BREAK
                $this->SetFont('helvetica', 'B', 9 + ($fontScale * 9));
                $this->Cell(0, 8, " ", '', 0, 'L', 0);
                $this->Ln(); // LINE BREAK


                $signatoryIDs = [];

                foreach ($abstractSigs as $absSigCtr => $absSig) {
                    if (!empty($absSig)) {
                        $signatoryIDs[] = $absSigCtr;
                    }
                }

                $signatoryCount = count($signatoryIDs);
                $columWidth = ($totalWidthDisplay - 15) / $signatoryCount;
                $columWidthSpace = 15 / 12;

                foreach ($abstractSigs as $absSigCtr => $absSig) {
                    if (!empty($absSig)) {
                        $this->Cell($columWidthSpace, 5);
                        $this->Cell($columWidth, 5, $absSig, 'B', 0, 'C');
                        $this->Cell($columWidthSpace, 5);
                    }
                }

                $this->SetFont('helvetica', '', 9 + ($fontScale * 9));
                $this->Ln(); // LINE BREAK

                foreach ($sigPosition as $titleCtr => $title) {
                    if (in_array($titleCtr, $signatoryIDs)) {
                        $this->Cell($columWidthSpace, 5);
                        $this->Cell($columWidth, 5, $title, 0, 0, 'C');
                        $this->Cell($columWidthSpace, 5);
                    }
                }
            }

        /* ------------------------------------- End of Doc ------------------------------------- */
        }
    }
}
