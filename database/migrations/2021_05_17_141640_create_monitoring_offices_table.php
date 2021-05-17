<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoringOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoring_offices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('agency_lgu')->nullable();
            $table->foreign('agency_lgu')->references('id')->on('agency_lgus');
            $table->string('office_name');
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
        Schema::dropIfExists('monitoring_offices');
    }
}
