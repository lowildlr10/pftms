<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('group_name');
            $table->unsignedBigInteger('group_head')->nullable();
            $table->foreign('group_head')->references('id')->on('emp_accounts');
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
        Schema::dropIfExists('emp_groups');
    }
}
