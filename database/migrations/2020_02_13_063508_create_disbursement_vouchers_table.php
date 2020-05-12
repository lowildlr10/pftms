<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisbursementVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disbursement_vouchers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->uuid('ors_id')->nullable();
            $table->foreign('ors_id')->references('id')->on('obligation_request_status');
            $table->string('dv_no')->nullable();
            $table->enum('transaction_type', ['cash_advance', 'reimbursement', 'others'])->default('others');
            $table->uuid('payee');
            $table->text('address')->nullable();
            $table->date('date_dv')->nullable();
            $table->dateTime('date_disbursed')->nullable();
            $table->string('fund_cluster', 50)->nullable();
            $table->string('payment_mode', 10)->default('0-0-0-0');
            $table->text('other_payment')->nullable();
            $table->text('particulars')->nullable();
            $table->string('responsibility_center')->nullable();
            $table->text('mfo_pap')->nullable();
            $table->double('amount', 50, 2)->default(0.00);
            $table->uuid('sig_certified')->nullable();
            $table->foreign('sig_certified')->references('id')->on('signatories');
            $table->uuid('sig_accounting')->nullable();
            $table->foreign('sig_accounting')->references('id')->on('signatories');
            $table->uuid('sig_agency_head')->nullable();
            $table->foreign('sig_agency_head')->references('id')->on('signatories');
            $table->date('date_accounting')->nullable();
            $table->date('date_agency_head')->nullable();
            $table->string('check_ada_no')->nullable();
            $table->date('date_check_ada')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('jev_no')->nullable();
            $table->string('receipt_printed_name')->nullable();
            $table->date('date_jev')->nullable();
            $table->text('signature')->nullable();
            $table->string('or_no')->nullable();
            $table->string('other_documents')->nullable();
            $table->unsignedBigInteger('module_class');
            $table->foreign('module_class')->references('id')->on('module_classifications');
            $table->enum('for_payment', ['y', 'n'])->default('n');
            $table->uuid('disbursed_by')->nullable();
            $table->foreign('disbursed_by')->references('id')->on('emp_accounts');
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
        Schema::dropIfExists('disbursement_vouchers');
    }
}
