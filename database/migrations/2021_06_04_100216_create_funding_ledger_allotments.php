<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundingLedgerAllotments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_ledger_allotments', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('funding_projects');
            $table->uuid('budget_id');
            $table->foreign('budget_id')->references('id')->on('funding_budgets');
            $table->uuid('ledger_id');
            $table->foreign('ledger_id')->references('id')->on('funding_ledgers');
            $table->uuid('ledger_item_id');
            $table->foreign('ledger_item_id')->references('id')->on('funding_ledger_items');
            $table->uuid('allotment_id')->nullable();
            $table->foreign('allotment_id')->references('id')->on('funding_allotments');
            $table->uuid('realign_allotment_id')->nullable();
            $table->foreign('realign_allotment_id')->references('id')->on('funding_allotment_realignments');
            $table->double('current_cost', 50, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funding_ledger_allotments');
    }
}
