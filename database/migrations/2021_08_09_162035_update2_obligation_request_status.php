<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update2ObligationRequestStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('obligation_request_status', function (Blueprint $table) {
            $table->date('date_released')->nullable()->after('date_obligated');
        });

        DB::statement(
            'ALTER TABLE `obligation_request_status` CHANGE `mfo_pap` `mfo_pap` BLOB NULL;'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('obligation_request_status', 'date_released')) {
            Schema::table('obligation_request_status', function (Blueprint $table) {
                $table->dropColumn('date_released');
            });
        }

        DB::statement(
            'ALTER TABLE `obligation_request_status` CHANGE `mfo_pap` `mfo_pap` TEXT NULL;'
        );
    }
}
