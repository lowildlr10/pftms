<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListDemandPayableItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_demand_payable_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('lddap_id')->nullable();
            $table->foreign('lddap_id')->references('id')->on('list_demand_payables');
            $table->unsignedInteger('item_no');
            $table->enum('category', ['current_year', 'prior_year']);
            $table->string('creditor_name');
            $table->string('creditor_acc_no');
            $table->text('ors_no');
            $table->string('allot_class_uacs');
            $table->double('gross_amount', 50, 2)->default(0.00);
            $table->double('withold_tax', 50, 2)->default(0.00);
            $table->double('net_amount', 50, 2)->default(0.00);
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_demand_payable_items');
    }
}
