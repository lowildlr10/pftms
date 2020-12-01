<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFundingSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funding_sources', function (Blueprint $table) {
            $table->renameColumn('source_name', 'project_name');
            $table->dropColumn('reference_code');
        });
        Schema::rename('funding_sources', 'funding_projects');
        Schema::table('purchase_requests', function (Blueprint $table) {
            //$table->foreign('funding_source')->references('id')->on('funding_projects');
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
            $table->string('reference_code', 200)->after('id')->nullable();
            $table->renameColumn('project_name', 'source_name');
        });
        Schema::rename('funding_projects', 'funding_sources');
    }
}
