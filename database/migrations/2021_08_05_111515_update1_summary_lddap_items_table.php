<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1SummaryLddapItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary_lddap_items', function (Blueprint $table) {
            $table->text('allotment_remarks')->nullable()->after('allotment_fe');

            $table->dropColumn('allotment_ps_remarks');
            $table->dropColumn('allotment_mooe_remarks');
            $table->dropColumn('allotment_co_remarks');
            $table->dropColumn('allotment_fe_remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('summary_lddap_items', function (Blueprint $table) {
            $table->dropColumn('allotment_remarks');

            $table->text('allotment_ps_remarks')->nullable()->after('allotment_fe');
            $table->text('allotment_mooe_remarks')->nullable()->after('allotment_ps_remarks');
            $table->text('allotment_co_remarks')->nullable()->after('allotment_mooe_remarks');
            $table->text('allotment_fe_remarks')->nullable()->after('allotment_co_remarks');
        });
    }
}
