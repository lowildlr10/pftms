<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummaryLddapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_lddaps', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->dateTime('date_for_approval')->nullable();
            $table->dateTime('date_approved')->nullable();
            $table->dateTime('date_for_submission_bank')->nullable();
            $table->uuid('mds_gsb_id')->nullable();
            $table->foreign('mds_gsb_id')->references('id')->on('mds_gsb');
            $table->string('department');
            $table->string('entity_name');
            $table->string('operating_unit');
            $table->string('fund_cluster', 50)->nullable();
            $table->string('sliiae_no');
            $table->date('date_sliiae')->nullable();
            $table->string('to');
            $table->string('bank_name');
            $table->text('bank_address');
            $table->uuid('sig_cert_correct')->nullable();
            $table->foreign('sig_cert_correct')->references('id')->on('signatories');
            $table->uuid('sig_approved_by')->nullable();
            $table->foreign('sig_approved_by')->references('id')->on('signatories');
            $table->uuid('sig_delivered_by')->nullable();
            $table->foreign('sig_delivered_by')->references('id')->on('signatories');
            //$table->uuid('sig_received_by')->nullable();
            //$table->foreign('sig_received_by')->references('id')->on('signatories');
            $table->unsignedInteger('lddap_no_pcs')->default(0);
            $table->text('total_amount_words');
            $table->double('total_amount', 50, 2)->default('0.00');
            $table->enum('status', ['pending', 'for_approval', 'approved', 'for_submission_bank'])
                  ->default('pending');
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
        Schema::dropIfExists('summary_lddaps');
    }
}
