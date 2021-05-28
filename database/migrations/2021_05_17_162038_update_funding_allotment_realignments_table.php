<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingAllotmentRealignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_allotment_realignments', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('funding_projects');
            $table->uuid('budget_id');
            $table->foreign('budget_id')->references('id')->on('funding_budgets');
            $table->uuid('allotment_id')->nullable();
            $table->foreign('allotment_id')->references('id')->on('funding_allotments');
            $table->uuid('budget_realign_id');
            $table->foreign('budget_realign_id')->references('id')->on('funding_budget_realignments');
            $table->uuid('allotment_class');
            $table->foreign('allotment_class')->references('id')->on('allotment_classes');
            $table->unsignedInteger('order_no');
            $table->text('allotment_name');
            $table->double('realigned_allotment_cost', 50, 2)->default(0.00);
            $table->binary('coimplementers')->nullable();
            $table->text('justification')->nullable();
            $table->timestamps();
        });

        DB::statement(
            'ALTER TABLE `funding_allotment_realignments` CHANGE `coimplementers` `coimplementers` LONGBLOB NOT NULL;'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funding_allotment_realignments');
    }
}
