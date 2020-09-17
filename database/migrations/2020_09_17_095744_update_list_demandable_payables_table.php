<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateListDemandablePayablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_demand_payables', function (Blueprint $table) {
            $table->uuid('for_summary_by')->after('sig_agency_auth_4')->nullable();
            $table->foreign('for_summary_by')->references('id')->on('emp_accounts');
            $table->uuid('approved_by')->after('sig_agency_auth_4')->nullable();
            $table->foreign('approved_by')->references('id')->on('emp_accounts');
            $table->uuid('for_approval_by')->after('sig_agency_auth_4')->nullable();
            $table->foreign('for_approval_by')->references('id')->on('emp_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_demand_payables', function (Blueprint $table) {
            $table->dropColumn('for_approval_by');
            $table->dropColumn('approved_by');
            $table->dropColumn('for_summary_by');
        });
    }
}
