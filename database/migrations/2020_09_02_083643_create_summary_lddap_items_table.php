<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummaryLddapItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_lddap_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('sliiae_id');
            $table->foreign('sliiae_id')->references('id')->on('summary_lddaps');
            $table->unsignedInteger('item_no');
            $table->uuid('lddap_id');
            $table->foreign('lddap_id')->references('id')->on('list_demand_payables');
            $table->date('date_issue');
            $table->double('total', 50, 2)->default(0.00);
            $table->double('allotment_ps', 50, 2)->default(0.00);
            $table->double('allotment_mooe', 50, 2)->default(0.00);
            $table->double('allotment_co', 50, 2)->default(0.00);
            $table->double('allotment_fe', 50, 2)->default(0.00);
            $table->text('allotment_ps_remarks')->nullable();
            $table->text('allotment_mooe_remarks')->nullable();
            $table->text('allotment_co_remarks')->nullable();
            $table->text('allotment_fe_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summary_lddap_items');
    }
}
