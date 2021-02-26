<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingLedgerItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_ledger_items', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->after('ors_id');
            $table->uuid('mooe_account_title_id')->after('ors_id');
            $table->foreign('mooe_account_title_id')->references('id')->on('mooe_account_titles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funding_ledger_items', function (Blueprint $table) {
            $table->dropColumn('order_no');
            $table->dropForeign('funding_ledger_items_mooe_account_title_id_foreign');
            $table->dropColumn('mooe_account_title_id');
        });
    }
}
