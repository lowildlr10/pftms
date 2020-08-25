<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateListDemandPayablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_demand_payables', function (Blueprint $table) {
            DB::statement('ALTER TABLE `list_demand_payables`
                           CHANGE `mds_gsb_accnt_no` `mds_gsb_accnt_no`
                           CHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                           NOT NULL;');
            $table->foreign('mds_gsb_accnt_no')->references('id')->on('mds_gsb');
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
            DB::statement('ALTER TABLE `list_demand_payables`
                           DROP FOREIGN KEY list_demand_payables_mds_gsb_accnt_no_foreign;');
            DB::statement('ALTER TABLE `list_demand_payables`
                           CHANGE `mds_gsb_accnt_no` `mds_gsb_accnt_no`
                           VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                           NOT NULL;');
        });
    }
}
