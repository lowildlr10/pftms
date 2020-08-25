<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateListDemandPayableItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_demand_payable_items', function (Blueprint $table) {
            DB::statement('ALTER TABLE `list_demand_payable_items` CHANGE `ors_no` `ors_no` BLOB NOT NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_demand_payable_items', function (Blueprint $table) {
            DB::statement('ALTER TABLE `list_demand_payable_items` CHANGE `ors_no` `ors_no`
                           TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                           NOT NULL;');
        });
    }
}
