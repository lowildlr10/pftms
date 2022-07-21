<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1LiquidationReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liquidation_reports', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->after('liquidated_by');
            $table->foreign('created_by')->nullable()->references('id')->on('emp_accounts');
            $table->dropForeign('liquidation_reports_sig_claimant_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('liquidation_reports', function (Blueprint $table) {
            $table->dropForeign('liquidation_reports_created_by_foreign');
            $table->dropColumn('created_by');
            $table->foreign('sig_claimant')->nullable()->references('id')->on('emp_accounts');
        });
    }
}
