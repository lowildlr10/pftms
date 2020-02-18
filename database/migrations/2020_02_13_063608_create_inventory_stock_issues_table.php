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
            $table->uuid('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->uuid('inventory_id')->nullable();
            $table->foreign('inventory_id')->references('id')->on('inventory_stocks');
            $table->string('serial_no')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->uuid('requested_by');
            $table->foreign('requested_by')->references('id')->on('emp_accounts');
            $table->uuid('issued_by')->nullable();
            $table->foreign('issued_by')->references('id')->on('signatories');
            $table->text('issued_remarks')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('signatories');
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
