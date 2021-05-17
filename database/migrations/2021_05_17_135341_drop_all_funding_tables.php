<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAllFundingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('funding_budgets');
        Schema::dropIfExists('funding_allotments');
        Schema::dropIfExists('allotment_classes');
        Schema::dropIfExists('funding_budget_realignments');
        Schema::dropIfExists('funding_allotment_realignments');
        Schema::dropIfExists('funding_ledgers');
        Schema::dropIfExists('funding_ledger_items');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
