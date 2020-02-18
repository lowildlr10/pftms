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
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->string('iar_no', 15)->unique();
            $table->string('code');
            $table->uuid('pr_id');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->uuid('ors_id');
            $table->foreign('ors_id')->references('id')->on('obligation_request_status');
            $table->date('date_iar')->nullable();
            $table->string('invoice_no', 100)->nullable();
            $table->date('date_invoice')->nullable();
            $table->uuid('sig_inspection')->nullable();
            $table->foreign('sig_inspection')->references('id')->on('signatories');
            $table->uuid('sig_supply')->nullable();
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
