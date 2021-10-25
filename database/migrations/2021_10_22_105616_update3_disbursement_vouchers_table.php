<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update3DisbursementVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disbursement_vouchers', function (Blueprint $table) {
            //
        });

        Schema::table('disbursement_vouchers', function (Blueprint $table) {
            //$table->date('date_released')->nullable()->after('date_obligated');
        });

        DB::statement(
            'ALTER TABLE `disbursement_vouchers` CHANGE `mfo_pap` `mfo_pap` BLOB NULL;'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        if (Schema::hasColumn('disbursement_vouchers', 'date_released')) {
            Schema::table('disbursement_vouchers', function (Blueprint $table) {
                $table->dropColumn('date_released');
            });
        }*/

        DB::statement(
            'ALTER TABLE `disbursement_vouchers` CHANGE `mfo_pap` `mfo_pap` TEXT NULL;'
        );
    }
}
