<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiquidationReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liquidation_reports', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->string('period_covered')->nullable();
            $table->string('entity_name')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('fund_cluster', 50)->nullable();
            $table->date('date_liquidation')->nullable();
            $table->string('responsibility_center')->nullable();
            $table->text('particulars')->nullable();
            $table->double('amount', 50, 2)->default(0.00);
            $table->double('total_amount', 50, 2)->default(0.00);
            $table->double('amount_cash_adv', 50, 2)->default(0.00);
            $table->string('or_no')->nullable();
            $table->date('or_dtd')->nullable();
            $table->double('amount_refunded', 50, 2)->default(0.00);
            $table->double('amount_reimbursed', 50, 2)->default(0.00);
            $table->uuid('sig_claimant')->nullable();
            $table->foreign('sig_claimant')->references('id')->on('emp_accounts');
            $table->uuid('sig_supervisor')->nullable();
            $table->foreign('sig_supervisor')->references('id')->on('signatories');
            $table->uuid('sig_accounting')->nullable();
            $table->foreign('sig_accounting')->references('id')->on('signatories');
            $table->date('date_claimant')->nullable();
            $table->date('date_supervisor')->nullable();
            $table->date('date_accounting')->nullable();
            $table->string('jev_no')->nullable();
            $table->uuid('dv_id');
            $table->foreign('dv_id')->references('id')->on('disbursement_vouchers');
            $table->date('dv_dtd')->nullable();
            $table->uuid('liquidated_by')->nullable();
            $table->foreign('liquidated_by')->references('id')->on('emp_accounts');
            $table->dateTime('date_liquidated')->nullable();
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
        Schema::dropIfExists('liquidation_reports');
    }
}
