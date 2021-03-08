<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingAllotmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_allotments', function (Blueprint $table) {
            $table->double('allotted_budget', 50, 2)->default(0.00)->after('allotment_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funding_allotments', function (Blueprint $table) {
            $table->dropColumn('allotted_budget');
        });
    }
}
