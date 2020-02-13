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
            $table->string('po_no', 15)->primary();
            $table->unsignedBigInteger('pr_id');
            $table->string('code');
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->date('date_po')->nullable();
            $table->dateTime('date_po_approved')->nullable();
            $table->dateTime('date_cancelled')->nullable();
            $table->unsignedBigInteger('awarded_to');
            $table->foreign('awarded_to')->references('id')->on('suppliers');
            $table->string('place_delivery')->default('DOST-CAR');
            $table->string('date_delivery')->default('Within 15 days of receipt of this purchase order.	');
            $table->string('delivery_term')->default('Complete');
            $table->string('payment_term')->default('After Inspection and Acceptance');
            $table->string('amount_words')->nullable();
            $table->double('grand_total', 50, 2)->default(0.00);
            $table->unsignedBigInteger('sig_department')->nullable();
            $table->foreign('sig_department')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_approval')->nullable();
            $table->foreign('sig_approval')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_funds_available')->nullable();
            $table->foreign('sig_funds_available')->references('id')->on('signatories');
            $table->unsignedBigInteger('recommended_by')->nullable();
            $table->foreign('recommended_by')->references('id')->on('signatories');
            $table->enum('for_approval', ['y', 'n'])->default('n');
            $table->enum('with_ors_burs', ['y', 'n'])->default('n');
            $table->unsignedBigInteger('status');
            $table->foreign('status')->references('id')->on('procurement_status');
            $table->enum('document_abrv', ['PO', 'JO']);
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
