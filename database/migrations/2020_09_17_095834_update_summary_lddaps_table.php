<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSummaryLddapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_lddaps', function (Blueprint $table) {
            $table->uuid('for_submission_bank_by')->after('sig_delivered_by')->nullable();
            $table->foreign('for_submission_bank_by')->references('id')->on('emp_accounts');
            $table->uuid('approved_by')->after('sig_delivered_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('emp_accounts');
            $table->uuid('for_approval_by')->after('sig_delivered_by')->nullable();
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
        Schema::table('summary_lddaps', function (Blueprint $table) {
            $table->dropColumn('for_approval_by');
            $table->dropColumn('approved_by');
            $table->dropColumn('for_submission_bank_by');
        });
    }
}
