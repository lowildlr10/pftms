<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryStockIssueItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stock_issue_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inv_stock_id')->nullable();
            $table->foreign('inv_stock_id')->references('id')->on('inventory_stocks');
            $table->uuid('inv_stock_item_id')->nullable();
            $table->foreign('inv_stock_item_id')->references('id')->on('inventory_stock_items');
            $table->uuid('inv_stock_issue_id')->nullable();
            $table->foreign('inv_stock_issue_id')->references('id')->on('inventory_stock_issues');
            $table->date('date_issued');
            $table->binary('prop_stock_no')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->string('est_useful_life')->nullable();
            $table->enum('stock_available', ['y', 'n'])->default('y');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('inventory_stock_issue_items');
    }
}
