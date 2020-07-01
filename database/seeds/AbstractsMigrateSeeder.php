<?php

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\RequestQuotation;
use App\Models\AbstractQuotation;
use App\Models\AbstractQuotationItem;
use App\User;
use App\Models\Signatory;
use App\Models\ProcurementMode;
use App\Models\Supplier;
use App\Models\DocumentLog as DocLog;

class AbstractsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $abstractsData = DB::connection('mysql-old-pftms')
                           ->table('tblabstract')
                           ->get();
        $dataCount = $abstractsData->count();

        foreach ($abstractsData as $ctr => $abs) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Abstract of Quotation: [ $percentage% ] migrated.\n";

            $code = $abs->code;
            $prID = $abs->pr_id;
            $prData = DB::connection('mysql-old-pftms')
                        ->table('tblpr')
                        ->where('id', $prID)
                        ->first();
            $prNo = $prData->pr_no;
            $instancePR = DB::table('purchase_requests')->where('pr_no', $prNo)->first();

            $__modeProcData = DB::connection('mysql-old-pftms')
                                ->table('tblmode_procurement')
                                ->where('id', $abs->mode_procurement_id)
                                ->first();
            $_modeProcData = $__modeProcData ?
                             ProcurementMode::where('mode_name', 'like', "%$__modeProcData->mode%")->first() :
                             NULL;
            $modeProcurement = $_modeProcData ? $_modeProcData->id : NULL;

            $__sigChairmanData = DB::connection('mysql-old-pftms')
                                   ->table('tblsignatories')
                                   ->where('id', $abs->sig_chairperson)
                                   ->first();
            $_sigChairmanData = $__sigChairmanData ?
                                User::where('emp_id', $__sigChairmanData->emp_id)->first() :
                                NULL;
            $sigChairmanData = $_sigChairmanData ?
                               Signatory::where('emp_id', $_sigChairmanData->id)->first() :
                               NULL;

            $__sigViceChairmanData = DB::connection('mysql-old-pftms')
                                       ->table('tblsignatories')
                                       ->where('id', $abs->sig_vice_chairperson)
                                       ->first();
            $_sigViceChairmanData = $__sigViceChairmanData ?
                                    User::where('emp_id', $__sigViceChairmanData->emp_id)->first() :
                                    NULL;
            $sigViceChairmanData = $_sigViceChairmanData ?
                                   Signatory::where('emp_id', $_sigViceChairmanData->id)->first() :
                                   NULL;

            $__sigFirstMemberData = DB::connection('mysql-old-pftms')
                                       ->table('tblsignatories')
                                       ->where('id', $abs->sig_first_member)
                                       ->first();
            $_sigFirstMemberData = $__sigFirstMemberData ?
                                   User::where('emp_id', $__sigFirstMemberData->emp_id)->first() :
                                   NULL;
            $sigFirstMemberData = $_sigFirstMemberData ?
                                  Signatory::where('emp_id', $_sigFirstMemberData->id)->first() :
                                  NULL;

            $__sigSecondMemberData = DB::connection('mysql-old-pftms')
                                       ->table('tblsignatories')
                                       ->where('id', $abs->sig_second_member)
                                       ->first();
            $_sigSecondMemberData = $__sigSecondMemberData ?
                                    User::where('emp_id', $__sigSecondMemberData->emp_id)->first() :
                                    NULL;
            $sigSecondMemberData = $_sigSecondMemberData ?
                                   Signatory::where('emp_id', $_sigSecondMemberData->id)->first() :
                                   NULL;

            $__sigThirdMemberData = DB::connection('mysql-old-pftms')
                                      ->table('tblsignatories')
                                      ->where('id', $abs->sig_third_member)
                                      ->first();
            $_sigThirdMemberData = $__sigThirdMemberData ?
                                   User::where('emp_id', $__sigThirdMemberData->emp_id)->first() :
                                   NULL;
            $sigThirdMemberData = $_sigThirdMemberData ?
                                  Signatory::where('emp_id', $_sigThirdMemberData->id)->first() :
                                  NULL;

            $endUserData = User::where('emp_id', $abs->sig_end_user)->first();
            $sigEndUser = !empty($endUserData) ? $endUserData->id : NULL;

            $instanceAbstract = new AbstractQuotation;
            $instanceAbstract->pr_id = $instancePR->id;
            $instanceAbstract->date_abstract = $abs->date_abstract;
            $instanceAbstract->date_abstract_approved = $abs->date_abstract_approve;
            $instanceAbstract->mode_procurement = $modeProcurement;
            $instanceAbstract->sig_chairperson = $sigChairmanData ?
                                                 $sigChairmanData->id :
                                                 NULL;
            $instanceAbstract->sig_vice_chairperson = $sigViceChairmanData ?
                                                      $sigViceChairmanData->id :
                                                      NULL;
            $instanceAbstract->sig_first_member = $sigFirstMemberData ?
                                                  $sigFirstMemberData->id :
                                                  NULL;
            $instanceAbstract->sig_second_member = $sigSecondMemberData ?
                                                   $sigSecondMemberData->id :
                                                   NULL;
            $instanceAbstract->sig_third_member = $sigThirdMemberData ?
                                                  $sigThirdMemberData->id :
                                                  NULL;
            $instanceAbstract->sig_end_user = $sigEndUser;
            $instanceAbstract->deleted_at = $abs->deleted_at;
            $instanceAbstract->created_at = $abs->created_at;
            $instanceAbstract->updated_at = $abs->updated_at;
            $instanceAbstract->save();

            $abstractData = DB::table('abstract_quotations')
                              ->where('pr_id', $instancePR->id)
                              ->first();
            $abstractID = $abstractData->id;

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
                $abstractItemsData = DB::connection('mysql-old-pftms')
                                       ->table('tblabstract_items')
                                       ->where('pr_item_id', $prItem->item_id)
                                       ->get();

                foreach ($abstractItemsData as $item) {
                    $_supplierData = DB::connection('mysql-old-pftms')
                                        ->table('tblsuppliers')
                                        ->where('id', $item->supplier_id)
                                        ->first();
                    $supplierData = Supplier::where('company_name', $_supplierData->company_name)
                                             ->first();
                    $supplier = $supplierData->id;

                    $instanceAbsItem = new AbstractQuotationItem;
                    $instanceAbsItem->abstract_id = $abstractID;
                    $instanceAbsItem->pr_id = $instancePR->id;
                    $instanceAbsItem->pr_item_id = $prItemID;
                    $instanceAbsItem->supplier = $supplier;
                    $instanceAbsItem->specification = $item->specification;
                    $instanceAbsItem->remarks = $item->remarks;
                    $instanceAbsItem->unit_cost = $item->unit_cost;
                    $instanceAbsItem->total_cost = $item->total_cost;
                    $instanceAbsItem->save();
                }
            }

            $docLogData = DB::connection('mysql-old-pftms')
                            ->table('tbldocument_logs_history')
                            ->where('code', $code)
                            ->get();

            foreach ($docLogData as $log) {
                $empFromData = User::where('emp_id', $log->emp_from)->first();
                $empToData = User::where('emp_id', $log->emp_to)->first();

                $instanceDocLog = new DocLog;
                $instanceDocLog->doc_id = $abstractID;
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
