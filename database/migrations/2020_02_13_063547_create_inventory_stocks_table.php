<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->uuid('po_item_id');
            $table->foreign('po_item_id')->references('id')->on('purchase_job_order_items');
            $table->string('po_no', 15)->nullable();
            $table->foreign('po_no')->references('po_no')->on('purchase_job_orders');
            $table->string('inventory_no', 100);
            $table->string('property_no', 100)->nullable();
            $table->uuid('inventory_classification');
            $table->foreign('inventory_classification')->references('id')->on('inventory_stock_classifications');
            $table->uuid('item_classification');
            $table->foreign('item_classification')->references('id')->on('item_classifications');
            $table->uuid('requested_by');
            $table->foreign('requested_by')->references('id')->on('emp_accounts');
            $table->string('office')->nullable();
            $table->uuid('division')->nullable();
            $table->foreign('division')->references('id')->on('emp_divisions');
            $table->text('purpose');
            $table->enum('stock_available', ['y', 'n'])->default('y');
            $table->string('est_useful_life')->nullable();
            $table->unsignedInteger('group_no');
            $table->unsignedBigInteger('status')->default(12);
            $table->foreign('status')->references('id')->on('procurement_status');
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
        Schema::dropIfExists('inventory_stocks');
    }
}
