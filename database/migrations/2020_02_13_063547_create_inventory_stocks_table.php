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
            $table->uuid('po_id')->nullable();
            $table->foreign('po_id')->references('id')->on('purchase_job_orders');
            $table->string('entity_name')->nullable();
            $table->string('fund_cluster', 50)->nullable();
            $table->string('inventory_no')->unique();
            $table->uuid('division')->nullable();
            $table->foreign('division')->references('id')->on('emp_divisions');
            $table->string('office')->nullable();
            $table->string('responsibility_center')->nullable();
            $table->string('po_no', 15)->nullable();
            $table->date('date_po')->nullable();
            $table->uuid('supplier')->nullable();
            $table->foreign('supplier')->references('id')->on('suppliers');
            $table->text('purpose');
            $table->uuid('inventory_classification');
            $table->foreign('inventory_classification')->references('id')->on('inventory_stock_classifications');
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
