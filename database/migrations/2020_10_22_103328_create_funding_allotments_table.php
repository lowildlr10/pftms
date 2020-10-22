<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundingAllotmentsTable extends Migration
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
            $table->string('allotment_name');
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
        Schema::dropIfExists('funding_allotments');
    }
}
