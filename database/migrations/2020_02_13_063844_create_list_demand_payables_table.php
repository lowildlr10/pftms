<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListDemandPayablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_demand_payables', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('dv_id')->nullable();
            $table->foreign('dv_id')->references('id')->on('disbursement_vouchers');
            $table->dateTime('date_for_approval')->nullable();
            $table->dateTime('date_approved')->nullable();
            $table->dateTime('date_for_summary')->nullable();
            $table->string('department');
            $table->string('entity_name');
            $table->string('operating_unit');
            $table->string('nca_no', 150);
            $table->string('lddap_ada_no');
            $table->date('date_lddap')->nullable();
            $table->string('fund_cluster', 50)->nullable();
            $table->string('mds_gsb_accnt_no', 150);
            $table->uuid('sig_cert_correct')->nullable();
            $table->foreign('sig_cert_correct')->references('id')->on('signatories');
            $table->uuid('sig_approval_1')->nullable();
            $table->foreign('sig_approval_1')->references('id')->on('signatories');
            $table->uuid('sig_approval_2')->nullable();
            $table->foreign('sig_approval_2')->references('id')->on('signatories');
            $table->uuid('sig_approval_3')->nullable();
            $table->foreign('sig_approval_3')->references('id')->on('signatories');
            $table->uuid('sig_agency_auth_1')->nullable();
            $table->foreign('sig_agency_auth_1')->references('id')->on('signatories');
            $table->uuid('sig_agency_auth_2')->nullable();
            $table->foreign('sig_agency_auth_2')->references('id')->on('signatories');
            $table->uuid('sig_agency_auth_3')->nullable();
            $table->foreign('sig_agency_auth_3')->references('id')->on('signatories');
            $table->uuid('sig_agency_auth_4')->nullable();
            $table->foreign('sig_agency_auth_4')->references('id')->on('signatories');
            $table->text('total_amount_words');
            $table->double('total_amount', 50, 2)->default('0.00');
            $table->enum('status', ['pending', 'for_approval', 'approved', 'for_summary'])->default('pending');
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
        Schema::dropIfExists('list_demand_payables');
    }
}
