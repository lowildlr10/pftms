<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObligationRequestStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obligation_request_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('po_no', 15)->nullable();
            $table->string('code');
            $table->foreign('po_no')->references('po_no')->on('purchase_job_orders');
            $table->unsignedBigInteger('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->enum('transaction_type', ['cash_advance', 'reimbursement', 'others'])->default('others');
            $table->enum('document_type', ['ORS', 'BURS'])->default('ORS');
            $table->string('func_cluster', 50)->nullable();
            $table->string('serial_no')->nullable();
            $table->date('date_ors_burs')->nullable();
            $table->dateTime('date_obligated')->nullable();
            $table->string('payee', 11);
            $table->string('office')->nullable();
            $table->text('address')->nullable();
            $table->string('responsibility_center')->nullable();
            $table->string('particulars')->nullable();
            $table->string('mfo_pap')->nullable();
            $table->string('uacs_object_code')->nullable();
            $table->double('amount', 50, 2)->default(0.00);
            $table->unsignedBigInteger('sig_certified_1')->nullable();
            $table->foreign('sig_certified_1')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_certified_2')->nullable();
            $table->foreign('sig_certified_2')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_accounting')->nullable();
            $table->foreign('sig_accounting')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_agency_head')->nullable();
            $table->foreign('sig_agency_head')->references('id')->on('signatories');
            $table->unsignedBigInteger('obligated_by')->nullable();
            $table->foreign('obligated_by')->references('emp_id')->on('emp_accounts');
            $table->date('date_certified_1')->nullable();
            $table->date('date_certified_2')->nullable();
            $table->unsignedBigInteger('module_class');
            $table->foreign('obligated_by')->references('id')->on('module_classifications');
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
        Schema::dropIfExists('obligation_request_status');
    }
}
