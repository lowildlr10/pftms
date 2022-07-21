<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update4DisbursementVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disbursement_vouchers', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->after('for_payment_by');
            $table->foreign('created_by')->nullable()->references('id')->on('emp_accounts');
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
            $table->dropForeign('disbursement_vouchers_created_by_foreign');
            $table->dropColumn('created_by');
        });
    }
}
