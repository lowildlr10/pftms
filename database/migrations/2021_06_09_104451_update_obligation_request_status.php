<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateObligationRequestStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('obligation_request_status', function (Blueprint $table) {
            $table->uuid('funding_source')->nullable()->after('module_class');
            $table->foreign('funding_source')->references('id')->on('funding_projects');
            //$table->binary('uacs_object_code')->nullable()->change();
        });

        DB::statement(
            'ALTER TABLE `obligation_request_status` CHANGE `uacs_object_code` `uacs_object_code` BLOB NULL;'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('obligation_request_status', function (Blueprint $table) {
            $table->dropForeign('obligation_request_status_funding_source_foreign');
            $table->dropColumn('funding_source');
            //$table->string('uacs_object_code')->nullable()->change();
        });

        DB::statement(
            'ALTER TABLE `obligation_request_status` CHANGE `uacs_object_code` `uacs_object_code` TEXT NULL;'
        );
    }
}
