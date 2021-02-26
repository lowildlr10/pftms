<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyLgusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_lgus', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('region')->nullable();
            $table->foreign('region')->references('id')->on('regions');
            $table->uuid('province')->nullable();
            $table->foreign('province')->references('id')->on('provinces');
            $table->uuid('municipality')->nullable();
            $table->foreign('municipality')->references('id')->on('municipalities');
            $table->string('agency_name');
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
        Schema::dropIfExists('agency_lgus');
    }
}
