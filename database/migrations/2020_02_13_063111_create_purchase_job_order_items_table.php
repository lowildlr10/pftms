<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseJobOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_job_order_items', function (Blueprint $table) {
            $table->string('item_id')->primary();
            $table->string('po_no', 15);
            $table->foreign('po_no')->references('po_no')->on('purchase_job_orders');
            $table->unsignedBigInteger('pr_id');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->string('stock_no', 100)->nullable();
            $table->unsignedInteger('quantitiy');
            $table->unsignedBigInteger('unit_issue');
            $table->foreign('unit_issue')->references('id')->on('item_unit_issues');
            $table->text('item_description');
            $table->double('unit_cost', 50, 2)->default(0.00);
            $table->double('total_cost', 50, 2)->default(0.00);
            $table->enum('excluded', ['y', 'n'])->default('n');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_job_order_items');
    }
}
