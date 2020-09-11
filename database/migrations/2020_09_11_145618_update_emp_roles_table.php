<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmpRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emp_roles', function (Blueprint $table) {
            $table->enum('is_cashier', ['y', 'n'])->after('is_accountant')->default('n');
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
            $table->dropColumn('is_cashier');
        });
    }
}
