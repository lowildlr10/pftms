<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDisbursementVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disbursement_vouchers', function (Blueprint $table) {
            $table->dateTime('date_for_payment')->after('date_disbursed')->nullable();
            $table->uuid('for_payment_by')->after('disbursed_by')->nullable();
            $table->foreign('for_payment_by')->references('id')->on('emp_accounts');
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
            $table->dropColumn('date_for_payment');
            $table->dropColumn('for_payment_by');
        });
    }
}
