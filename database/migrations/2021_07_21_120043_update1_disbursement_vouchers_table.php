<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1DisbursementVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disbursement_vouchers', function (Blueprint $table) {
            $table->double('prior_year', 50, 2)->default(0.00)->after('mfo_pap');
            $table->double('continuing', 50, 2)->default(0.00)->after('prior_year');
            $table->double('current', 50, 2)->default(0.00)->after('continuing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disbursement_vouchers', function (Blueprint $table) {
            $table->dropColumn('prior_year');
            $table->dropColumn('continuing');
            $table->dropColumn('current');
        });
    }
}
