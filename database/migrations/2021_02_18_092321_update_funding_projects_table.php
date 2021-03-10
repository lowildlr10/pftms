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
            $table->binary('comimplementing_agency_lgus')->nullable()->after('implementing_agency');
            $table->binary('proponent_units')->nullable()->after('comimplementing_agency_lgus');
            $table->date('date_from')->nullable()->after('proponent_units');
            $table->date('date_to')->nullable()->after('date_from');
            $table->double('project_cost', 50, 2)->default(0.00)->after('date_to');
            $table->uuid('monitoring_office')->nullable()->after('project_cost');
            $table->foreign('monitoring_office')->nullable()->references('id')->on('agency_lgus');
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
            $table->dropForeign('funding_projects_agency_lgus_foreign');
            $table->dropForeign('funding_projects_implementing_agency_foreign');
            $table->renameColumn('project_title', 'project_name');
            $table->dropColumn('industry_sector');
            $table->dropColumn('project_site');
            $table->dropColumn('proponent_units');
            $table->dropColumn('comimplementing_agency_lgus');
            $table->dropColumn('project_cost');
            $table->dropColumn('date_from');
            $table->dropColumn('date_to');
            $table->dropColumn('agency_lgus');
            $table->dropColumn('implementing_agency');
        });
    }
}
