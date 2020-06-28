<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_roles', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->string('role', 100);
            $table->binary('module_access')->nullable();
            $table->enum('is_ordinary', ['y', 'n'])->default('y');
            $table->enum('is_property_supply', ['y', 'n'])->default('n');
            $table->enum('is_budget', ['y', 'n'])->default('n');
            $table->enum('is_accountant', ['y', 'n'])->default('n');
            $table->enum('is_administrator', ['y', 'n'])->default('n');
            $table->enum('is_developer', ['y', 'n'])->default('n');
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
        Schema::dropIfExists('emp_roles');
    }
}
