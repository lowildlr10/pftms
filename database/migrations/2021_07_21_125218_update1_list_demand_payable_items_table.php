<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ListDemandPayable;
use App\Models\ListDemandPayableItem;

class Update1ListDemandPayableItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ListDemandPayableItem::truncate();
        ListDemandPayable::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement(
            'ALTER TABLE `list_demand_payable_items` CHANGE `allot_class_uacs` `allot_class_uacs` BLOB NULL;'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            'ALTER TABLE `list_demand_payable_items` CHANGE `allot_class_uacs` `allot_class_uacs` VARCHAR(191);'
        );
    }
}
