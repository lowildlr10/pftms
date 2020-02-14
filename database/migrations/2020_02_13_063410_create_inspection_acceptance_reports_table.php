<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionAcceptanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_acceptance_reports', function (Blueprint $table) {
            $table->string('iar_no', 15)->primary();
            $table->string('code');
            $table->unsignedBigInteger('pr_id');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->unsignedBigInteger('ors_id');
            $table->foreign('ors_id')->references('id')->on('obligation_request_status');
            $table->date('date_iar')->nullable();
            $table->string('invoice_no', 100)->nullable();
            $table->date('date_invoice')->nullable();
            $table->unsignedBigInteger('sig_inspection')->nullable();
            $table->foreign('sig_inspection')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_supply')->nullable();
            $table->foreign('sig_supply')->references('id')->on('signatories');
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
        Schema::dropIfExists('inspect_acceptance_reports');
    }
}
