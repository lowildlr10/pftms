<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update2FundingLedgerItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_ledger_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('funding_projects');
            $table->uuid('budget_id');
            $table->foreign('budget_id')->references('id')->on('funding_budgets');
            $table->uuid('ledger_id');
            $table->foreign('ledger_id')->references('id')->on('funding_ledgers');
            $table->uuid('ors_id')->nullable();
            $table->foreign('ors_id')->references('id')->on('obligation_request_status');
            $table->uuid('dv_id')->nullable();
            $table->foreign('dv_id')->references('id')->on('disbursement_vouchers');
            $table->unsignedInteger('order_no');
            $table->date('date_ors_dv');
            $table->uuid('payee');
            $table->text('particulars');
            $table->string('ors_no')->nullable();
            $table->binary('mooe_account')->nullable();
            $table->double('total', 50, 2)->default(0.00);
            $table->double('prior_year', 50, 2)->default(0.00);
            $table->double('continuing', 50, 2)->default(0.00);
            $table->double('current', 50, 2)->default(0.00);
            $table->uuid('unit')->nullable();
            $table->foreign('unit')->references('id')->on('emp_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funding_ledger_items');
    }
}
