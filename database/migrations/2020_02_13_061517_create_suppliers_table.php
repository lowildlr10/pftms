<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('classification');
            $table->foreign('classification')->references('id')->on('supplier_classifications');
            $table->string('company_name');
            $table->date('date_filed')->nullable();
            $table->text('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('email', 191)->nullable();
            $table->string('website_url')->nullable();
            $table->string('telephone_no', 191)->nullable();
            $table->string('fax_no', 191)->nullable();
            $table->string('mobile_no', 191)->nullable();
            $table->date('date_established')->nullable();
            $table->string('tin_no', 200)->nullable();
            $table->string('vat_no', 200)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->enum('nature_business', ['', 'manufacturer', 'trading_firms',
                                             'service_contractor', 'others']);
            $table->string('nature_business_others')->nullable();
            $table->unsignedInteger('delivery_vehicle_no')->default(0);
            $table->string('product_lines')->nullable();
            $table->enum('credit_accomodation', ['', '90_days_above', '60_days',
                                                 '30_days', '15_days', 'below_15_days']);
            $table->string('attachment', 20)->nullable();
            $table->string('attachment_others')->nullable();
            $table->enum('is_active', ['y', 'n'])->default('n');
            $table->dateTime('blacklisted_at')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
