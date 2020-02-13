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
            $table->bigIncrements('id');
            $table->string('pr_no', 100)->unique();
            $table->string('code');
            $table->date('date_pr');
            $table->dateTime('date_pr_approved')->nullable();
            $table->dateTime('date_pr_disapproved')->nullable();
            $table->dateTime('date_pr_cancelled')->nullable();
            $table->unsignedBigInteger('funding')->nullable();
            $table->string('requested_by', 11);
            $table->foreign('funding')->references('id')->on('fundings');
            $table->foreign('requested_by')->references('emp_id')->on('emp_accounts');
            $table->unsignedBigInteger('division');
            $table->foreign('division')->references('id')->on('emp_divisions');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('sig_app')->nullable();
            $table->unsignedBigInteger('sig_funds_available')->nullable();
            $table->unsignedBigInteger('recommended_by')->nullable();
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
