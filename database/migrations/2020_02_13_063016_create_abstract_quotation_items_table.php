<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractQuotationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abstract_quotation_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('abstract_id');
            $table->foreign('abstract_id')->references('id')->on('abstract_quotations');
            $table->uuid('pr_id');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->uuid('pr_item_id');
            $table->foreign('pr_item_id')->references('id')->on('purchase_request_items');
            $table->uuid('supplier');
            $table->foreign('supplier')->references('id')->on('suppliers');
            $table->text('specification')->nullable();
            $table->text('remarks')->nullable();
            $table->double('unit_cost', 50, 2)->default(0.00);
            $table->double('total_cost', 50, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::dropIfExists('abstract_quotation_items');
    }
}
