<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('emp_id');
            $table->foreign('emp_id')->references('id')->on('emp_accounts');
            $table->string('request')->nullable();
            $table->string('method')->nullable();
            $table->string('host')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('remarks');
            $table->timestamp('logged_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emp_logs');
    }
}
