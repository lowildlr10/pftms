<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1ListDemandPayablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_demand_payables', function (Blueprint $table) {
            $table->string('serial_no')->after('fund_cluster')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_demand_payables', function (Blueprint $table) {
            $table->dropColumn('serial_no');
        });
    }
}
