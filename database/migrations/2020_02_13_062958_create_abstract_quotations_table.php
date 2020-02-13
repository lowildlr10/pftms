<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbstractQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abstract_quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pr_id');
            $table->string('code');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->date('date_abstract')->nullable();
            $table->dateTime('date_abstract_approved')->nullable();
            $table->unsignedBigInteger('mode_procurement');
            $table->foreign('mode_procurement')->references('id')->on('procurement_modes');
            $table->unsignedBigInteger('sig_chairperson')->nullable();
            $table->foreign('sig_chairperson')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_vice_chairperson')->nullable();
            $table->foreign('sig_vice_chairperson')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_first_member')->nullable();
            $table->foreign('sig_first_member')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_second_member')->nullable();
            $table->foreign('sig_second_member')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_third_member')->nullable();
            $table->foreign('sig_third_member')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_end_user')->nullable();
            $table->foreign('sig_end_user')->references('id')->on('signatories');
            $table->string('document_abrv', 20)->default('ABSTRACT');
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
        Schema::dropIfExists('abstract_quotations');
    }
}
