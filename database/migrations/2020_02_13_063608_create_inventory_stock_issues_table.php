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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->unsignedBigInteger('inventory_id')->nullable();
            $table->foreign('inventory_id')->references('id')->on('inventory_stocks');
            $table->string('serial_no')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('requested_by', 11);
            $table->foreign('requested_by')->references('emp_id')->on('emp_accounts');
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->foreign('issued_by')->references('id')->on('signatories');
            $table->text('issued_remarks')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
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
