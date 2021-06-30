<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update2DisbursementVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disbursement_vouchers', function (Blueprint $table) {
            $table->uuid('funding_source')->nullable()->after('for_payment_by');
            $table->foreign('funding_source')->references('id')->on('funding_projects');
            $table->binary('uacs_object_code')->nullable()->after('date_agency_head');
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
            $table->dropForeign('disbursement_vouchers_funding_source_foreign');
            $table->dropColumn('funding_source');
        });

        if (Schema::hasColumn('disbursement_vouchers', 'uacs_object_code')) {
            Schema::table('disbursement_vouchers', function (Blueprint $table) {
                $table->dropColumn('uacs_object_code');
            });
        }
    }
}
