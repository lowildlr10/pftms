<?php

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\DocumentLog as DocLog;
use App\Models\ItemUnitIssue as Unit;
use App\User;
use App\Models\Signatory;
use App\Models\EmpDivision;
use App\Models\Supplier;

class PRsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prsData = DB::connection('mysql-old-pftms')
                     ->table('tblpr')
                     ->get();
        $dataCount = $prsData->count();

        foreach ($prsData as $ctr => $pr) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Purchase Requests: [ $percentage% ] migrated.\n";

            $code = $pr->code;
            $prID = $pr->id;
            $prNo = trim($pr->pr_no);
            $_divisionData = DB::connection('mysql-old-pftms')
                               ->table('tbldivision')
                               ->where('id', $pr->pr_division_id)
                               ->first();
            $divisionData = EmpDivision::where('division_name', $_divisionData->division)->first();
            $requestedByData = User::where('emp_id', $pr->requested_by)->first();

            $__sigApprovedByData = DB::connection('mysql-old-pftms')
                                     ->table('tblsignatories')
                                     ->where('id', $pr->approved_by)
                                     ->first();
            $_sigApprovedByData = $__sigApprovedByData ?
                                  User::where('emp_id', $__sigApprovedByData->emp_id)->first() :
                                  NULL;
            $sigApprovedByData = $_sigApprovedByData ?
                                 Signatory::where('emp_id', $_sigApprovedByData->id)->first() :
                                 NULL;

            $__sigAPPData = DB::connection('mysql-old-pftms')
                             ->table('tblsignatories')
                             ->where('id', $pr->sig_app)
                             ->first();
            $_sigAPPData = $__sigAPPData ?
                           User::where('emp_id', $__sigAPPData->emp_id)->first() :
                           NULL;
            $sigAPPData = $_sigAPPData ?
                          Signatory::where('emp_id', $_sigAPPData->id)->first() :
                          NULL;

            $__sigFundsAvailableData = DB::connection('mysql-old-pftms')
                                         ->table('tblsignatories')
                                         ->where('id', $pr->sig_funds_available)
                                         ->first();
            $_sigFundsAvailableData = $__sigFundsAvailableData ?
                                      User::where('emp_id', $__sigFundsAvailableData->emp_id)->first() :
                                      NULL;
            $sigFundsAvailableData =  $_sigFundsAvailableData ?
                                      Signatory::where('emp_id', $_sigFundsAvailableData->id)->first() :
                                      NULL;

            $__sigRecommendedByData = DB::connection('mysql-old-pftms')
                                        ->table('tblsignatories')
                                        ->where('id', $pr->recommended_by)
                                        ->first();
            $_sigRecommendedByData = $__sigRecommendedByData ?
                                      User::where('emp_id', $__sigRecommendedByData->emp_id)->first() :
                                      NULL;
            $sigRecommendedByData = $_sigRecommendedByData ?
                                    Signatory::where('emp_id', $_sigRecommendedByData->id)->first() :
                                    NULL;


            $instancePR = new PurchaseRequest;
            $instancePR->pr_no = $prNo;
            $instancePR->date_pr = $pr->date_pr;
            $instancePR->date_pr_approved = $pr->date_pr_approve;
            $instancePR->date_pr_disapproved = $pr->date_pr_disapprove;
            $instancePR->date_pr_cancelled = $pr->date_pr_cancel;
            //$instancePR->funding_source = $pr->project_id;
            $instancePR->requested_by = $requestedByData->id;
            $instancePR->office = $pr->office;
            $instancePR->division = $divisionData->id;
            $instancePR->approved_by = $sigApprovedByData ?
                                       $sigApprovedByData->id :
                                       NULL;
            $instancePR->sig_app = $sigAPPData ?
                                   $sigAPPData->id :
                                   NULL;
            $instancePR->sig_funds_available = $sigFundsAvailableData ?
                                               $sigFundsAvailableData->id :
                                               NULL;
            $instancePR->recommended_by = $sigRecommendedByData ?
                                          $sigRecommendedByData->id :
                                          NULL;
            $instancePR->purpose = $pr->purpose;
            $instancePR->remarks = $pr->remarks;
            $instancePR->status = $pr->status;
            $instancePR->deleted_at = $pr->deleted_at;
            $instancePR->created_at = $pr->created_at;
            $instancePR->updated_at = $pr->updated_at;
            $instancePR->save();

            $prData = DB::table('purchase_requests')->where('pr_no', $prNo)->first();

            $prItemsData = DB::connection('mysql-old-pftms')
                             ->table('tblpr_items')
                             ->where('pr_id', $prID)
                             ->orderByRaw('LENGTH(item_id)')
                             ->orderBy('item_id')
                             ->get();

            foreach ($prItemsData as $itemCtr => $item) {
                $unitData = DB::connection('mysql-old-pftms')
                              ->table('tblunit_issue')
                              ->where('id', $item->unit_issue)
                              ->first();
                $unitName = $unitData ? $unitData->unit : NULL;
                $instanceUnit = !empty($unitName) ?
                                Unit::where('unit_name', $unitName)->first() :
                                NULL;
                $_supplierData = DB::connection('mysql-old-pftms')
                                  ->table('tblsuppliers')
                                  ->where('id', $item->awarded_to)
                                  ->first();
                $supplierData = $_supplierData ?
                                Supplier::where('company_name', $_supplierData->company_name)->first() :
                                NULL;

                $instancePRItem = new PurchaseRequestItem;
                $instancePRItem->pr_id = $prData->id;
                $instancePRItem->item_no = $itemCtr + 1;
                $instancePRItem->quantity = $item->quantity;
                $instancePRItem->unit_issue = $instanceUnit ? $instanceUnit->id : NULL;
                $instancePRItem->item_description = $item->item_description;
                $instancePRItem->est_unit_cost = $item->est_unit_cost;
                $instancePRItem->est_total_cost = $item->est_total_cost;
                $instancePRItem->awarded_to = $supplierData ? $supplierData->id : NULL;
                $instancePRItem->awarded_remarks = $item->awarded_remarks;
                $instancePRItem->group_no = $item->group_no;
                $instancePRItem->document_type = $item->document_type;
                $instancePRItem->save();
            }

            $docLogData = DB::connection('mysql-old-pftms')
                            ->table('tbldocument_logs_history')
                            ->where('code', $code)
                            ->get();

            foreach ($docLogData as $log) {
                $empFromData = User::where('emp_id', $log->emp_from)->first();
                $empToData = User::where('emp_id', $log->emp_to)->first();

                $instanceDocLog = new DocLog;
                $instanceDocLog->doc_id = $prData->id;
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
