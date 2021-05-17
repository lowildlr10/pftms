<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_budgets', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('funding_projects');
            $table->dateTime('date_approved')->nullable();
            $table->dateTime('date_disapproved')->nullable();
            $table->date('date_from');
            $table->date('date_to');
            $table->double('approved_budget', 50, 2)->default(0.00);
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('emp_accounts');
            $table->uuid('sig_submitted_by')->nullable();
            $table->foreign('sig_submitted_by')->nullable()->references('id')->on('emp_accounts');
            $table->uuid('sig_approved_by')->nullable();
            $table->foreign('sig_approved_by')->references('id')->on('signatories');
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
        Schema::dropIfExists('funding_budgets');
    }
}
