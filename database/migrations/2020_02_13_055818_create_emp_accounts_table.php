<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_accounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->string('emp_id', 11)->unique();
            $table->uuid('division');
            $table->uuid('province');
            $table->uuid('region');
            $table->binary('groups')->nullable();
            $table->binary('roles')->nullable();
            $table->foreign('division')->references('id')->on('emp_divisions');
            $table->foreign('province')->references('id')->on('provinces');
            $table->foreign('region')->references('id')->on('regions');
            $table->string('firstname', 50);
            $table->string('middlename', 50)->nullable();
            $table->string('lastname', 50);
            $table->enum('gender', ['male', 'female']);
            $table->string('position', 200);
            $table->enum('emp_type', ['regular', 'contractual']);
            $table->string('username', 100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('address')->nullable();
            $table->string('mobile_no', 191)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->enum('is_active', ['y', 'n'])->default('n');
            $table->text('avatar')->nullable();
            $table->text('signature')->nullable();
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
