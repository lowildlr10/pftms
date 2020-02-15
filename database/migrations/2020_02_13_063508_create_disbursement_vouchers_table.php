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
            
            $table->bigIncrements('id');
            $table->string('code');
            $table->unsignedBigInteger('pr_id')->nullable();
            $table->foreign('pr_id')->references('id')->on('purchase_requests');
            $table->unsignedBigInteger('ors_id');
            $table->foreign('ors_id')->references('id')->on('obligation_request_status');
            $table->string('dv_no')->nullable();
            $table->date('date_dv')->nullable();
            $table->dateTime('date_disbursed')->nullable();
            $table->string('fund_cluster', 50)->nullable();
            $table->string('payment_mode', 10)->default('0-0-0-0');
            $table->text('particulars')->nullable();
            $table->unsignedBigInteger('sig_accounting')->nullable();
            $table->foreign('sig_accounting')->references('id')->on('signatories');
            $table->unsignedBigInteger('sig_agency_head')->nullable();
            $table->foreign('sig_agency_head')->references('id')->on('signatories');
            $table->date('date_accounting')->nullable();
            $table->date('date_agency_head')->nullable();
            $table->text('other_payment')->nullable();
            $table->unsignedBigInteger('module_class');
            $table->foreign('module_class')->references('id')->on('module_classifications');
            $table->enum('for_payment', ['y', 'n'])->default('n');
            $table->string('document_abrv', 5)->default('DV');
            $table->string('disbursed_by', 11)->nullable();
            $table->foreign('disbursed_by')->references('emp_id')->on('emp_accounts');
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
