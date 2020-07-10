<?php

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseJobOrder;
use App\Models\PurchaseJobOrderItem;
use App\User;
use App\Models\Signatory;
use App\Models\Supplier;
use App\Models\ItemUnitIssue as Unit;
use App\Models\DocumentLog as DocLog;

class POJOsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posData = DB::connection('mysql-old-pftms')
                     ->table('tblpo_jo')
                     ->get();
        $dataCount = $posData->count();

        foreach ($posData as $ctr => $po) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Purchase and Job Orders: [ $percentage% ] migrated.\n";

            $code = $po->code;
            $prID = $po->pr_id;
            $poNo = $po->po_no;
            $prData = DB::connection('mysql-old-pftms')
                        ->table('tblpr')
                        ->where('id', $prID)
                        ->first();
            $prNo = $prData->pr_no;
            $instancePR = DB::table('purchase_requests')->where('pr_no', $prNo)->first();

            $__supplierData = DB::connection('mysql-old-pftms')
                                ->table('tblsuppliers')
                                ->where('id', $po->awarded_to)
                                ->first();
            $_supplierData = $__supplierData ?
                             Supplier::where('company_name', $__supplierData->company_name)->first() :
                             NULL;
            $supplierData = $_supplierData ? $_supplierData : NULL;

            $__sigDepartmentData = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $po->sig_department)
                                     ->first();
            $_sigDepartmentData = $__sigDepartmentData ?
                                  User::where('emp_id', $__sigDepartmentData->emp_id)->first() :
                                  NULL;
            $sigDepartmentData = $_sigDepartmentData ?
                                 Signatory::where('emp_id', $_sigDepartmentData->id)->first() :
                                 NULL;

            $__sigApprovalData = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $po->sig_approval)
                                     ->first();
            $_sigApprovalData = $__sigApprovalData ?
                                  User::where('emp_id', $__sigApprovalData->emp_id)->first() :
                                  NULL;
            $sigApprovalData = $_sigApprovalData ?
                                 Signatory::where('emp_id', $_sigApprovalData->id)->first() :
                                 NULL;

            $__sigFundsAvailableData = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $po->sig_funds_available)
                                     ->first();
            $_sigFundsAvailableData = $__sigFundsAvailableData ?
                                  User::where('emp_id', $__sigFundsAvailableData->emp_id)->first() :
                                  NULL;
            $sigFundsAvailableData = $_sigFundsAvailableData ?
                                 Signatory::where('emp_id', $_sigFundsAvailableData->id)->first() :
                                 NULL;

            $instancePO = new PurchaseJobOrder;
            $instancePO->po_no = $po->po_no;
            $instancePO->pr_id = $instancePR->id;
            $instancePO->date_po = $po->date_po;
            $instancePO->date_po_approved = $po->date_po_approved;
            $instancePO->date_cancelled = $po->date_cancelled;
            $instancePO->awarded_to = $supplierData ?
                                      $supplierData->id :
                                      NULL;
            $instancePO->place_delivery = $po->place_delivery;
            $instancePO->date_delivery = $po->date_delivery;
            $instancePO->delivery_term = $po->delivery_term;
            $instancePO->payment_term = $po->payment_term;
            $instancePO->amount_words = $po->amount_words;
            $instancePO->grand_total = $po->grand_total;
            $instancePO->sig_department = $sigDepartmentData ?
                                          $sigDepartmentData->id :
                                          NULL;
            $instancePO->sig_approval = $sigApprovalData ?
                                        $sigApprovalData->id :
                                        NULL;
            $instancePO->sig_funds_available = $sigFundsAvailableData ?
                                               $sigFundsAvailableData->id :
                                               NULL;
            $instancePO->date_accountant_signed = $po->date_accountant_signed;
            $instancePO->for_approval = $po->for_approval;
            $instancePO->with_ors_burs = $po->with_ors_burs;
            $instancePO->status = $po->status;
            $instancePO->document_type = $po->document_abrv;
            $instancePO->deleted_at = $po->deleted_at;
            $instancePO->created_at = $po->created_at;
            $instancePO->updated_at = $po->updated_at;
            $instancePO->save();

            $poData = DB::table('purchase_job_orders')
                        ->where('po_no', $poNo)
                        ->first();
            $poID = $poData->id;

            $poItemData = DB::connection('mysql-old-pftms')
                            ->table('tblpo_jo_items')
                            ->where('po_no', $poNo)
                            ->orderByRaw('LENGTH(item_id)')
                            ->orderBy('item_id')
                            ->get();

            if (count($poItemData) > 0) {
                foreach ($poItemData as $ctr => $item) {
                    $prItemOld = DB::connection('mysql-old-pftms')
                                   ->table('tblpr_items')
                                   ->where('item_id', $item->item_id)
                                   ->first();

                    if ($prItemOld) {
                        $instancePRItem = DB::table('purchase_request_items')
                                            ->where('item_description', $prItemOld->item_description)
                                            ->first();
                        $prItemID = $instancePRItem->id;
                        $itemNo = $instancePRItem->item_no;

                        $unitData = DB::connection('mysql-old-pftms')
                                    ->table('tblunit_issue')
                                    ->where('id', $item->unit_issue)
                                    ->first();
                        $unitName = $unitData ? $unitData->unit : NULL;
                        $instanceUnit = !empty($unitName) ?
                                        Unit::where('unit_name', $unitName)->first() :
                                        NULL;

                        $instancePOItem = new PurchaseJobOrderItem;
                        $instancePOItem->po_no = $poNo;
                        $instancePOItem->pr_id = $instancePR->id;
                        $instancePOItem->pr_item_id = $prItemID;
                        $instancePOItem->item_no = $itemNo;
                        $instancePOItem->stock_no = $item->stock_no;
                        $instancePOItem->quantity = $item->quantity;
                        $instancePOItem->unit_issue = $instanceUnit ? $instanceUnit->id : NULL;
                        $instancePOItem->item_description = $item->item_description;
                        $instancePOItem->unit_cost = $item->unit_cost;
                        $instancePOItem->total_cost = $item->total_cost;
                        $instancePOItem->excluded = $item->excluded;
                        $instancePOItem->save();
                    }
                }
            }

            /*

            $prItemsData = DB::connection('mysql-old-pftms')
                             ->table('tblpr_items')
                             ->where('pr_id', $prID)
                             ->orderByRaw('LENGTH(item_id)')
                             ->orderBy('item_id')
                             ->get();

            foreach ($prItemsData as $itemCtr => $prItem) {
                $instancePRItem = DB::table('purchase_request_items')->where([
                    ['item_no', ($itemCtr + 1)], ['pr_id', $instancePR->id]
                ])->first();
                $prItemID = $instancePRItem->id;
                $itemNo = $instancePRItem->item_no;

                $poItemData = DB::connection('mysql-old-pftms')
                                ->table('tblpo_jo_items')
                                ->where('item_id', $prItem->item_id)
                                ->first();

                if ($poItemData) {
                    $unitData = DB::connection('mysql-old-pftms')
                                ->table('tblunit_issue')
                                ->where('id', $poItemData->unit_issue)
                                ->first();
                    $unitName = $unitData ? $unitData->unit : NULL;
                    $instanceUnit = !empty($unitName) ?
                                    Unit::where('unit_name', $unitName)->first() :
                                    NULL;

                    $instancePOItem = new PurchaseJobOrderItem;
                    $instancePOItem->po_no = $poNo;
                    $instancePOItem->pr_id = $instancePR->id;
                    $instancePOItem->pr_item_id = $prItemID;
                    $instancePOItem->item_no = $itemNo;
                    $instancePOItem->stock_no = $poItemData->stock_no;
                    $instancePOItem->quantity = $poItemData->quantity;
                    $instancePOItem->unit_issue = $instanceUnit ? $instanceUnit->id : NULL;
                    $instancePOItem->item_description = $poItemData->item_description;
                    $instancePOItem->unit_cost = $poItemData->unit_cost;
                    $instancePOItem->total_cost = $poItemData->total_cost;
                    $instancePOItem->excluded = $poItemData->excluded;
                    $instancePOItem->save();
                }
            }*/

            $docLogData = DB::connection('mysql-old-pftms')
                            ->table('tbldocument_logs_history')
                            ->where('code', $code)
                            ->get();

            foreach ($docLogData as $log) {
                $empFromData = User::where('emp_id', $log->emp_from)->first();
                $empToData = User::where('emp_id', $log->emp_to)->first();

                $instanceDocLog = new DocLog;
                $instanceDocLog->doc_id = $poID;
                $instanceDocLog->logged_at = $log->date;
                $instanceDocLog->emp_from = $empFromData ? $empFromData->id :
                                            NULL;
                $instanceDocLog->emp_to = $empToData ? $empToData->id :
                                          NULL;
                $instanceDocLog->action = $log->action;
                $instanceDocLog->remarks = $log->remarks;
                $instanceDocLog->created_at = $log->created_at;
                $instanceDocLog->updated_at = $log->updated_at;
                $instanceDocLog->save();
            }
        }
    }
}
