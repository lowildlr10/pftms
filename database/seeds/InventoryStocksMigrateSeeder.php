<?php

use Illuminate\Database\Seeder;
use App\Models\InventoryStock;
use App\Models\InventoryStockItem;
use App\Models\InventoryStockIssue;
use App\Models\InventoryStockIssueItem;
use App\User;
use App\Models\Signatory;

class InventoryStocksMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invsData = DB::connection('mysql-old-pftms')
                     ->table('tblinventory_stocks')
                     ->get();
        $dataCount = $invsData->count();

        foreach ($invsData as $ctr => $inv) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Inventory Stocks: [ $percentage% ] migrated.\n";

            $entityName = 'Department of Science and Technology - CAR';
            $fundCluster = '01';
            $responsibilityCenter = '19 001 03000 14';
            $invID = $inv->id;
            $_prID = $inv->pr_id;
            $_poItemID = $inv->po_item_id;
            $poNo = $inv->po_no;
            $inventoryNo = trim($inv->inventory_no);
            $inventoryNo = substr($inventoryNo, 0, strlen($inventoryNo) - 2);
            $propertyNo = $inv->property_no;
            $inventoryClassID = $inv->inventory_class_id;
            $itemClassID = $inv->item_class_id;
            $_sigRequestedBy = $inv->requested_by;
            $office = $inv->office;
            $_division = $inv->division_id;
            $purpose = $inv->purpose;
            $stockAvailable = $inv->stock_available;
            $estUsefulLife = $inv->est_useful_life;
            $status = $inv->status;
            $createdAt = $inv->created_at;
            $updatedAt = $inv->updated_at;
            $deletedAt = $inv->deleted_at;

            $invOldIssuesData = DB::connection('mysql-old-pftms')
                                  ->table('tblinventory_stocks_issue')
                                  ->where('inventory_id', $invID)
                                  ->get();
            $poData = DB::table('purchase_job_orders')->where('po_no', $poNo)->first();

            if ($poData) {
                $poID = $poData->id;
                $prID = $poData->pr_id;
                $datePO = $poData->date_po;

                // Fetch supplier library
                $supplier = $poData->awarded_to;

                // Fetch division library
                $empDivOldData = DB::connection('mysql-old-pftms')
                                ->table('tbldivision')
                                ->where('id', $_division)
                                ->first();
                $empDivData = $empDivOldData ?
                              DB::table('emp_divisions')
                                ->where('division_name', 'like', '%'.$empDivOldData->division.'%')
                                ->first() : NULL;
                $division = $empDivData ? $empDivData->id : NULL;

                // Fetch inventory classification library
                $invClassOldData = DB::connection('mysql-old-pftms')
                                     ->table('tblinventory_classification')
                                     ->where('id', $inventoryClassID)->first();
                $invClassData = DB::table('inventory_stock_classifications')
                                  ->where('classification_name', 'like', '%'.$invClassOldData->classification.'%')
                                  ->first();
                $inventoryClassification = $invClassData->id;

                if (strpos(strtolower($invClassData->classification_name), 'ris') !== false) {
                    $inventoryType = 'ris';
                }

                if (strpos(strtolower($invClassData->classification_name), 'ics') !== false) {
                    $inventoryType = 'ics';
                }

                if (strpos(strtolower($invClassData->classification_name), 'par') !== false) {
                    $inventoryType = 'par';
                }

                // Fetch item classification library
                $itemClassOldData = DB::connection('mysql-old-pftms')
                                     ->table('tblitem_classifications')
                                     ->where('id', $itemClassID)->first();
                $itemClassData = $itemClassOldData ?
                                 DB::table('item_classifications')
                                  ->where('classification_name', 'like', '%'.$itemClassOldData->classification.'%')
                                  ->first() : NULL;
                $itemClassification = $itemClassData ? $itemClassData->id : NULL;

                // Fetch signatories and end-user
                $sigRequestedByData = DB::table('emp_accounts')->where('emp_id', $_sigRequestedBy)->first();
                $sigRequestedBy = isset($sigRequestedByData->id) && $sigRequestedByData->id ? $sigRequestedByData->id : NULL;

                //$instanceInvStock = InventoryStock::where('inventory_no', $inventoryNo)->first();
                $instanceInvStock = DB::table('inventory_stocks')->where('inventory_no', $inventoryNo)->first();

                if (!$instanceInvStock) {
                    $instanceInvStock = new InventoryStock;
                    $instanceInvStock->pr_id = $prID;
                    $instanceInvStock->po_id = $poID;
                    $instanceInvStock->inventory_no = $inventoryNo;
                    $instanceInvStock->entity_name = $entityName;
                    $instanceInvStock->fund_cluster = $fundCluster;
                    $instanceInvStock->division = $division;
                    $instanceInvStock->office = $office;
                    $instanceInvStock->responsibility_center = $responsibilityCenter;
                    $instanceInvStock->po_no = $poNo;
                    $instanceInvStock->date_po = $datePO;
                    $instanceInvStock->supplier = $supplier;
                    $instanceInvStock->purpose = $purpose;
                    $instanceInvStock->inventory_classification = $inventoryClassification;
                    $instanceInvStock->status = $status;
                    $instanceInvStock->deleted_at = $deletedAt;
                    $instanceInvStock->created_at = $createdAt;
                    $instanceInvStock->updated_at = $updatedAt;
                    $instanceInvStock->save();
                }

                if (!$instanceInvStock) {
                    $invStockData = DB::table('inventory_stocks')
                                      ->where('inventory_no', $inventoryNo)
                                      ->first();
                    $invStockID = $instanceInvStock->id;
                } else {
                    $invStockID = $instanceInvStock->id;
                }


                $_poItemData = DB::connection('mysql-old-pftms')
                                 ->table('tblpo_jo_items')
                                 ->where('item_id', $_poItemID)
                                 ->first();
                $poItemData = DB::table('purchase_job_order_items')
                                ->where([['po_no', $poNo], ['item_description', $_poItemData->item_description]])
                                ->first();

                if ($poItemData) {
                    $orsData = DB::table('obligation_request_status')
                                ->where('po_no', $poNo)
                                ->first();

                    if ($orsData) {
                        $poItemID = $poItemData->id;
                        $poItemNo = $poItemData->item_no;
                        $quantity = $poItemData->quantity;
                        $description = $poItemData->item_description;
                        $unitIssue = $poItemData->unit_issue;
                        $amount = $orsData->amount;

                        $instanceInvStockItem = new InventoryStockItem;
                        $instanceInvStockItem->inv_stock_id = $invStockID;
                        $instanceInvStockItem->pr_id = $prID;
                        $instanceInvStockItem->po_id = $poID;
                        $instanceInvStockItem->po_item_id = $poItemID;
                        $instanceInvStockItem->item_no = $poItemNo;
                        $instanceInvStockItem->item_classification = $itemClassification;
                        $instanceInvStockItem->unit_issue = $unitIssue;
                        $instanceInvStockItem->description = $description;
                        $instanceInvStockItem->quantity = $quantity;
                        $instanceInvStockItem->amount = $amount;
                        $instanceInvStockItem->save();

                        $invStockItem = InventoryStockItem::where('po_item_id', $poItemID)->first();
                        $invStockItemID = $invStockItem->id;

                        foreach ($invOldIssuesData as $item) {
                            $quantity = $item->quantity;
                            $_sigReceivedBy = $item->received_by;
                            $_sigIssuedBy = $item->issued_by;
                            $_sigApprovedBy = $item->approved_by;
                            $dataIssued = $item->date_issued;
                            $serialNo = $item->serial_no;
                            $remarks = $item->issued_remarks;
                            $createdAt = $item->created_at;
                            $updatedAt = $item->updated_at;

                            $__sigIssuedByData = DB::connection('mysql-old-pftms')
                                                    ->table('tblsignatories')
                                                    ->where('id', $_sigIssuedBy)
                                                    ->first();
                            $_sigIssuedByData = $__sigIssuedByData ?
                                                User::where('emp_id', $__sigIssuedByData->emp_id)->first() :
                                                NULL;
                            $sigIssuedByData = $_sigIssuedByData ?
                                                Signatory::where('emp_id', $_sigIssuedByData->id)->first() :
                                                NULL;

                            $__sigApprovedByData = DB::connection('mysql-old-pftms')
                                                    ->table('tblsignatories')
                                                    ->where('id', $_sigApprovedBy)
                                                    ->first();
                            $_sigApprovedByData = $__sigApprovedByData ?
                                                User::where('emp_id', $__sigApprovedByData->emp_id)->first() :
                                                NULL;
                            $sigApprovedByData = $_sigApprovedByData ?
                                                Signatory::where('emp_id', $_sigApprovedByData->id)->first() :
                                                NULL;

                            $sigReceivedByData = DB::table('emp_accounts')->where('emp_id', $_sigReceivedBy)->first();

                            $sigApprovedBy = isset($sigApprovedByData->id) && $sigApprovedByData->id ? $sigApprovedByData->id : NULL;
                            $sigIssuedBy = isset($sigIssuedByData->id) && $sigIssuedByData->id ? $sigIssuedByData->id : NULL;
                            $sigReceivedBy = isset($sigReceivedByData->id) && $sigReceivedByData->id ? $sigReceivedByData->id : NULL;

                            $instanceInvStockIssue = InventoryStockIssue::where([
                                ['inv_stock_id', $invStockID], ['sig_received_by', $sigReceivedBy]
                            ])->first();

                            if (!$instanceInvStockIssue) {
                                $instanceInvStockIssue = new InventoryStockIssue;
                                $instanceInvStockIssue->inv_stock_id = $invStockID;
                                $instanceInvStockIssue->pr_id = $prID;
                                $instanceInvStockIssue->po_id = $poID;
                            }

                            $instanceInvStockIssue->sig_requested_by = $sigRequestedBy;
                            $instanceInvStockIssue->sig_approved_by = $sigApprovedBy;
                            $instanceInvStockIssue->sig_received_by = $sigReceivedBy;

                            if ($inventoryType == 'ics') {
                                $instanceInvStockIssue->sig_issued_by = NULL;
                                $instanceInvStockIssue->sig_received_from = $sigIssuedBy;
                            } else {
                                $instanceInvStockIssue->sig_issued_by = $sigIssuedBy;
                                $instanceInvStockIssue->sig_received_from = NULL;
                            }

                            $instanceInvStockIssue->save();

                            if (!$instanceInvStockIssue) {
                                $instanceInvStockIssue = InventoryStockIssue::where([
                                    ['inv_stock_id', $invStockID], ['sig_received_by', $sigReceivedBy]
                                ])->first();
                                $invStockIssueID = $instanceInvStockIssue->id;
                            } else {
                                $invStockIssueID = $instanceInvStockIssue->id;
                            }

                            $_propStockNo = $propertyNo ? $propertyNo : $serialNo;
                            $propStockNos = serialize(preg_split("@(\s*and\s*)?[/\s,&]+@", $_propStockNo));


                            $instanceInvStockIssueItem = new InventoryStockIssueItem;
                            $instanceInvStockIssueItem->inv_stock_id = $invStockID;
                            $instanceInvStockIssueItem->inv_stock_item_id = $invStockItemID;
                            $instanceInvStockIssueItem->inv_stock_issue_id = $invStockIssueID;
                            $instanceInvStockIssueItem->date_issued = $dataIssued;
                            $instanceInvStockIssueItem->prop_stock_no = $propStockNos;
                            $instanceInvStockIssueItem->quantity = $quantity;
                            $instanceInvStockIssueItem->est_useful_life = $estUsefulLife;
                            $instanceInvStockIssueItem->stock_available = $stockAvailable;
                            $instanceInvStockIssueItem->remarks = $remarks;
                            $instanceInvStockIssueItem->save();
                        }
                    }
                }
            }
        }
    }
}
