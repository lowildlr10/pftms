<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundingRegAllotmentItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_reg_allotment_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reg_allotment_id');
            $table->foreign('reg_allotment_id')->references('id')->on('funding_reg_allotments');
            $table->uuid('ors_id');
            $table->foreign('ors_id')->references('id')->on('obligation_request_status');
            $table->unsignedInteger('order_no');
            $table->date('date_received')->nullable();
            $table->date('date_obligated');
            $table->date('date_released')->nullable();
            $table->uuid('payee');
            $table->text('particulars');
            $table->string('serial_number')->nullable();
            $table->binary('uacs_object_code')->nullable();
            $table->double('allotments', 2)->default('0.00');
            $table->double('obligations', 2)->default('0.00');
            $table->double('unobligated_allot', 2)->default('0.00');
            $table->double('disbursement', 2)->default('0.00');
            $table->double('due_demandable', 2)->default('0.00');
            $table->double('not_due_demandable', 2)->default('0.00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funding_reg_allotment_items');
    }
}
