<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_projects', function (Blueprint $table) {
            $table->renameColumn('project_name', 'project_title');
            $table->uuid('industry_sector')->nullable()->after('id');
            $table->foreign('industry_sector')->references('id')->on('industry_sectors');
            $table->uuid('project_site')->nullable()->after('industry_sector');
            $table->foreign('project_site')->nullable()->references('id')->on('municipalities');
            $table->uuid('implementing_agency')->nullable()->after('project_site');
            $table->foreign('implementing_agency')->references('id')->on('agency_lgus');
            $table->double('implementing_project_cost', 50, 2)->default(0.00)->after('implementing_agency');
            $table->binary('comimplementing_agency_lgus')->nullable()->after('implementing_project_cost');
            $table->binary('proponent_units')->nullable()->after('comimplementing_agency_lgus');
            $table->date('date_from')->nullable()->after('proponent_units');
            $table->date('date_to')->nullable()->after('date_from');
            $table->double('project_cost', 50, 2)->default(0.00)->after('date_to');
            $table->string('project_leader')->nullable()->after('project_cost');
            $table->binary('monitoring_offices')->after('project_leader');
            $table->binary('access_groups')->nullable()->after('monitoring_offices');
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
            $table->dropForeign('funding_projects_industry_sector_foreign');
            $table->dropForeign('funding_projects_project_site_foreign');
            $table->dropForeign('funding_projects_implementing_agency_foreign');

            $table->renameColumn('project_title', 'project_name');
            $table->dropColumn('industry_sector');
            $table->dropColumn('project_site');
            $table->dropColumn('implementing_agency');
            $table->dropColumn('implementing_project_cost');
            $table->dropColumn('comimplementing_agency_lgus');
            $table->dropColumn('proponent_units');
            $table->dropColumn('date_from');
            $table->dropColumn('date_to');
            $table->dropColumn('project_cost');
            $table->dropColumn('project_leader');
            $table->dropColumn('monitoring_offices');
            $table->dropColumn('access_groups');
        });
    }
}
