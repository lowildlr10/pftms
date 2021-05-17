<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmpAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emp_accounts', function (Blueprint $table) {
            $table->uuid('unit')->nullable()->after('division');
            $table->foreign('unit')->references('id')->on('emp_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emp_accounts', function (Blueprint $table) {
            $table->dropForeign('emp_accounts_unit_foreign');
            $table->dropColumn('unit');
        });
    }
}
