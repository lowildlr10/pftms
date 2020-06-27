<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stock_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('inv_stock_id')->nullable();
            $table->foreign('inv_stock_id')->references('id')->on('inventory_stocks');
            $table->uuid('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->uuid('po_id')->nullable();
            $table->foreign('po_id')->references('id')->on('purchase_job_orders');
            $table->uuid('po_item_id')->nullable();
            $table->foreign('po_item_id')->references('id')->on('purchase_job_order_items');
            $table->unsignedInteger('item_no');
            $table->uuid('item_classification');
            $table->foreign('item_classification')->references('id')->on('item_classifications');
            $table->uuid('unit_issue');
            $table->foreign('unit_issue')->references('id')->on('item_unit_issues');
            $table->text('description');
            $table->unsignedInteger('quantity')->default(0);
            $table->enum('stock_available', ['y', 'n'])->default('y');
            $table->double('amount', 50, 2)->default(0.00);
            $table->string('est_useful_life')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_stock_items');
    }
}
