<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pr_id');
            $table->string('code');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->date('date_canvass')->nullable();
            $table->unsignedBigInteger('sig_rfq')->nullable();
            $table->foreign('sig_rfq')->references('id')->on('signatories');
            $table->string('document_abrv', 20)->default('RFQ');
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
        Schema::dropIfExists('request_quotations');
    }
}
