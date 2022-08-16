<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1FundingRegAllotmentItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_reg_allotment_items', function (Blueprint $table) {
            $table->char('is_excluded')->default('n')->after('not_due_demandable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funding_reg_allotment_items', function (Blueprint $table) {
            $table->dropColumn('is_excluded');
        });
    }
}
