<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseJobOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_job_orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->string('po_no', 15)->unique();
            $table->uuid('pr_id');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->date('date_po')->nullable();
            $table->dateTime('date_po_approved')->nullable();
            $table->dateTime('date_cancelled')->nullable();
            $table->uuid('awarded_to');
            $table->foreign('awarded_to')->references('id')->on('suppliers');
            $table->string('place_delivery')->nullable();
            $table->string('date_delivery')->nullable();
            $table->string('delivery_term')->nullable();
            $table->string('payment_term')->nullable();
            $table->string('amount_words')->nullable();
            $table->double('grand_total', 50, 2)->default(0.00);
            $table->string('fund_cluster', 50)->nullable();
            $table->uuid('sig_department')->nullable();
            $table->foreign('sig_department')->references('id')->on('signatories');
            $table->uuid('sig_approval')->nullable();
            $table->foreign('sig_approval')->references('id')->on('signatories');
            $table->uuid('sig_funds_available')->nullable();
            $table->foreign('sig_funds_available')->references('id')->on('signatories');
            $table->dateTime('date_accountant_signed')->nullable();
            $table->enum('for_approval', ['y', 'n'])->default('n');
            $table->enum('with_ors_burs', ['y', 'n'])->default('n');
            $table->unsignedBigInteger('status');
            $table->foreign('status')->references('id')->on('procurement_status');
            $table->enum('document_type', ['po', 'jo']);
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
        Schema::dropIfExists('purchase_job_orders');
    }
}
