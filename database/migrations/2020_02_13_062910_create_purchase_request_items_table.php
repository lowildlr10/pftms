<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->string('item_id')->primary();
            $table->unsignedBigInteger('pr_id');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_issue');
            $table->foreign('unit_issue')->references('id')->on('item_unit_issues');
            $table->text('item_description');
            $table->double('est_unit_cost', 50, 2)->default(0.00);
            $table->double('est_total_cost', 50, 2)->default(0.00);
            $table->unsignedBigInteger('awarded_to');
            $table->foreign('awarded_to')->references('id')->on('suppliers');
            $table->text('awarded_remarks')->nullable();
            $table->unsignedBigInteger('group_no')->default(0);
            $table->enum('document_type', ['PO', 'JO']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_items');
    }
}
