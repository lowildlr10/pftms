<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_ledgers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('funding_projects');
            $table->uuid('budget_id');
            $table->foreign('budget_id')->references('id')->on('funding_budgets');
            $table->enum('ledger_for', ['obligation', 'disbursement']);
            $table->enum('ledger_type', ['saa', 'mooe', 'lgia', 'setup'])->default('saa');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funding_ledgers');
    }
}
