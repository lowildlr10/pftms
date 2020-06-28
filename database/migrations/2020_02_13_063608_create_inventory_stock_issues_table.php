<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryStockIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stock_issues', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('inv_stock_id')->nullable();
            $table->foreign('inv_stock_id')->references('id')->on('inventory_stocks');
            $table->uuid('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->uuid('po_id')->nullable();
            $table->foreign('po_id')->references('id')->on('purchase_job_orders');
            $table->uuid('sig_requested_by')->nullable();
            $table->foreign('sig_requested_by')->references('id')->on('emp_accounts');
            $table->uuid('sig_approved_by')->nullable();
            $table->foreign('sig_approved_by')->references('id')->on('signatories');
            $table->uuid('sig_issued_by')->nullable();
            $table->foreign('sig_issued_by')->references('id')->on('signatories');
            $table->uuid('sig_received_from')->nullable();
            $table->foreign('sig_received_from')->references('id')->on('signatories');
            $table->uuid('sig_received_by')->nullable();
            $table->foreign('sig_received_by')->references('id')->on('emp_accounts');
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
        Schema::dropIfExists('inventory_stock_issues');
    }
}
