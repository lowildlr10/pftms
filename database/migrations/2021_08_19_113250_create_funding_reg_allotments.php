<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundingRegAllotments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funding_reg_allotments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('period_ending');
            $table->string('entity_name');
            $table->string('fund_cluster');
            $table->string('legal_basis');
            $table->binary('mfo_pap')->nullable();
            $table->string('sheet_no');
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
        Schema::dropIfExists('funding_reg_allotments');
    }
}
