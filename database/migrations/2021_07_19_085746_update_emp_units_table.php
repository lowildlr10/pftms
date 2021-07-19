<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmpUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emp_units', function (Blueprint $table) {
            $table->uuid('unit_head')->nullable()->after('unit_name');
            $table->foreign('unit_head')->references('id')->on('emp_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emp_units', function (Blueprint $table) {
            $table->dropForeign('emp_units_unit_head_foreign');
            $table->dropColumn('unit_head');
        });
    }
}
