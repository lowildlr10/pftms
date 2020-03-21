<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->string('pr_no', 100)->unique();
            $table->date('date_pr');
            $table->dateTime('date_pr_approved')->nullable();
            $table->dateTime('date_pr_disapproved')->nullable();
            $table->dateTime('date_pr_cancelled')->nullable();
            $table->uuid('funding_source')->nullable();
            $table->uuid('requested_by');
            $table->foreign('funding_source')->references('id')->on('funding_sources');
            $table->foreign('requested_by')->references('id')->on('emp_accounts');
            $table->string('office');
            $table->string('responsibility_center')->nullable();
            $table->uuid('division');
            $table->foreign('division')->references('id')->on('emp_divisions');
            $table->uuid('approved_by')->nullable();
            $table->uuid('sig_app')->nullable();
            $table->uuid('sig_funds_available')->nullable();
            $table->uuid('recommended_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('signatories');
            $table->foreign('sig_app')->references('id')->on('signatories');
            $table->foreign('sig_funds_available')->references('id')->on('signatories');
            $table->foreign('recommended_by')->references('id')->on('signatories');
            $table->text('purpose');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('status');
            $table->foreign('status')->references('id')->on('procurement_status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_requests');
    }
}
