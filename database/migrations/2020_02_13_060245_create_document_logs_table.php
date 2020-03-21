<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->string('doc_id');
            $table->dateTime('logged_at');
            $table->uuid('emp_from')->nullable();
            $table->foreign('emp_from')->references('id')->on('emp_accounts');
            $table->uuid('emp_to')->nullable();
            $table->foreign('emp_to')->references('id')->on('emp_accounts');
            $table->string('action');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('document_logs');
    }
}
