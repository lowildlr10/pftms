<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingBudgetRealignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_budget_realignments', function (Blueprint $table) {
            $table->date('date_realignment')->after('id');
            $table->uuid('created_by')->after('realignment_order');
            $table->foreign('created_by')->references('id')->on('emp_accounts');
            $table->uuid('approved_by')->nullable()->after('created_by');
            $table->foreign('approved_by')->references('id')->on('emp_accounts');
            $table->uuid('disapproved_by')->nullable()->after('created_by');
            $table->foreign('disapproved_by')->references('id')->on('emp_accounts');
            $table->dateTime('date_approved')->nullable()->after('project_id');
            $table->dateTime('date_disapproved')->nullable()->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funding_budget_realignments', function (Blueprint $table) {
            $table->dropForeign('funding_budget_realignments_created_by_foreign');
            $table->dropForeign('funding_budget_realignments_approved_by_foreign');
            $table->dropForeign('funding_budget_realignments_disapproved_by_foreign');
            $table->dropColumn('date_realignment');
            $table->dropColumn('created_by');
            $table->dropColumn('approved_by');
            $table->dropColumn('disapproved_by');
            $table->dropColumn('date_approved');
            $table->dropColumn('date_disapproved');
        });
    }
}
