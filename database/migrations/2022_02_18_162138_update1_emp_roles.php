<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1EmpRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emp_roles', function (Blueprint $table) {
            $table->enum('is_project_staff', ['y', 'n'])->default('n')->after('is_cashier');
            $table->enum('is_planning', ['y', 'n'])->default('n')->after('is_project_staff');
            $table->enum('is_pstd', ['y', 'n'])->default('n')->after('is_planning');
            $table->enum('is_ard', ['y', 'n'])->default('n')->after('is_pstd');
            $table->enum('is_rd', ['y', 'n'])->default('n')->after('is_ard');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emp_roles', function (Blueprint $table) {
            $table->dropColumn('is_rd');
            $table->dropColumn('is_ard');
            $table->dropColumn('is_pstd');
            $table->dropColumn('is_planning');
            $table->dropColumn('is_project_staff');
        });
    }
}
