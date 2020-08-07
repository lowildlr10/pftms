<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emp_logs', function (Blueprint $table) {
            //$table->char('emp_id', 36)->nullable()->change();
            DB::statement('ALTER TABLE `emp_logs` CHANGE `emp_id` `emp_id` CHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emp_logs', function (Blueprint $table) {
            //$table->char('emp_id', 36)->nullable(false)->change();
            DB::statement('ALTER TABLE `emp_logs` CHANGE `emp_id` `emp_id` CHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;');
        });
    }
}
