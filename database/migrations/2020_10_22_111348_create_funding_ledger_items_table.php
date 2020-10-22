<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundingLedgerItemsTable extends Migration
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
            $table->uuid('ledger_id');
            $table->foreign('ledger_id')->references('id')->on('funding_ledgers');
            $table->uuid('allotment_id');
            $table->foreign('allotment_id')->references('id')->on('funding_allotments');
            $table->uuid('ors_id');
            $table->foreign('ors_id')->references('id')->on('obligation_request_status');
            $table->date('date_ors_dv');
            $table->uuid('payee');
            $table->text('paticulars');
            $table->string('ors_no');
            $table->double('total', 50, 2)->default(0.00);
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
