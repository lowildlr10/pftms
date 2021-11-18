<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrsBursUacsItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ors_burs_uacs_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ors_id');
            $table->foreign('ors_id')->references('id')
                  ->on('obligation_request_status')
                  ->onDelete('cascade');
            $table->uuid('uacs_id');
            $table->foreign('uacs_id')->references('id')
                  ->on('mooe_account_titles')
                  ->onDelete('cascade');
            $table->text('description')->nullable();
            $table->double('amount', 50, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ors_burs_uacs_items');
    }
}
