<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1FundingProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_projects', function (Blueprint $table) {
            $table->dropForeign('funding_projects_project_site_foreign');
            $table->dropColumn('project_site');
        });
        Schema::table('funding_projects', function (Blueprint $table) {
            $table->binary('project_site')->nullable()->after('industry_sector');
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
            $table->dropColumn('project_site');
        });
        Schema::table('funding_projects', function (Blueprint $table) {
            $table->uuid('project_site')->nullable()->after('industry_sector');
            $table->foreign('project_site')->nullable()->references('id')->on('municipalities');
        });
    }
}
