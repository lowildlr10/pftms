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

            $table->uuid('id')->primary();
            $table->uuid('pr_id');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->unsignedInteger('item_no');
            $table->unsignedInteger('quantity');
            $table->uuid('unit_issue')->nullable();
            $table->foreign('unit_issue')->references('id')->on('item_unit_issues');
            $table->text('item_description');
            $table->double('est_unit_cost', 50, 2)->default(0.00);
            $table->double('est_total_cost', 50, 2)->default(0.00);
            $table->uuid('awarded_to')->nullable();
            $table->foreign('awarded_to')->references('id')->on('suppliers');
            $table->text('awarded_remarks')->nullable();
            $table->unsignedInteger('group_no')->default(0);
            $table->enum('document_type', ['po', 'jo']);
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
