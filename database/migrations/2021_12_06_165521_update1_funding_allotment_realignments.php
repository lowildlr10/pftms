<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1FundingAllotmentRealignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_allotment_realignments', function (Blueprint $table) {
            $table->uuid('uacs_id')->after('allotment_class')->nullable();
            $table->foreign('uacs_id')->references('id')
                  ->on('mooe_account_titles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funding_allotment_realignments', function (Blueprint $table) {
            $table->dropColumn('uacs_id');
        });
    }
}
