<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingAllotmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_allotments', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->foreign('project_id')->references('id')->on('funding_projects');
            $table->uuid('budget_id');
            $table->foreign('budget_id')->references('id')->on('funding_budgets');
            $table->uuid('allotment_class');
            $table->foreign('allotment_class')->references('id')->on('allotment_classes');
            $table->unsignedInteger('order_no');
            $table->text('allotment_name');
            $table->double('allotment_cost', 50, 2)->default(0.00);
            $table->binary('coimplementers')->nullable();
            $table->timestamps();
        });

        DB::statement(
            'ALTER TABLE `funding_allotments` CHANGE `coimplementers` `coimplementers` LONGBLOB NOT NULL;'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funding_allotments');
    }
}
