<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update4FundingProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_projects', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->after('project_type');
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
        Schema::table('funding_projects', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
}
