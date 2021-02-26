<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingAllotmentRealignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_allotment_realignments', function (Blueprint $table) {
            $table->text('justification')->nullable()->after('realigned_allotment');
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
            $table->dropColumn('justification');
        });
    }
}
