<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update3ObligationRequestStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('obligation_request_status', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->after('obligated_by');
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
        Schema::table('obligation_request_status', function (Blueprint $table) {
            $table->dropForeign('obligation_request_status_created_by_foreign');
            $table->dropColumn('created_by');
        });
    }
}
