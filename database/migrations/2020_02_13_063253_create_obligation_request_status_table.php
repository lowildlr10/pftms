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
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->string('po_no', 15)->nullable();
            $table->foreign('po_no')->references('po_no')->on('purchase_job_orders');
            $table->enum('transaction_type', ['cash_advance', 'reimbursement', 'others'])->default('others');
            $table->enum('document_type', ['ors', 'burs'])->default('ors');
            $table->string('fund_cluster', 50)->nullable();
            $table->string('serial_no')->nullable();
            $table->date('date_ors_burs')->nullable();
            $table->dateTime('date_obligated')->nullable();
            $table->uuid('payee');
            $table->string('office')->nullable();
            $table->text('address')->nullable();
            $table->string('responsibility_center')->nullable();
            $table->text('particulars')->nullable();
            $table->text('mfo_pap')->nullable();
            $table->string('uacs_object_code')->nullable();
            $table->double('amount', 50, 2)->default(0.00);
            $table->uuid('sig_certified_1')->nullable();
            $table->foreign('sig_certified_1')->references('id')->on('signatories');
            $table->uuid('sig_certified_2')->nullable();
            $table->foreign('sig_certified_2')->references('id')->on('signatories');
            $table->uuid('sig_accounting')->nullable();
            $table->foreign('sig_accounting')->references('id')->on('signatories');
            $table->uuid('sig_agency_head')->nullable();
            $table->foreign('sig_agency_head')->references('id')->on('signatories');
            $table->uuid('obligated_by')->nullable();
            $table->foreign('obligated_by')->references('id')->on('emp_accounts');
            $table->date('date_certified_1')->nullable();
            $table->date('date_certified_2')->nullable();
            $table->date('date_received')->nullable();
            $table->date('date_released')->nullable();
            $table->unsignedBigInteger('module_class');
            $table->foreign('module_class')->references('id')->on('module_classifications');
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
