<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SupplierClassification;
use App\Models\Supplier;

class SuppliersMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supplierData = DB::connection('mysql-old-pftms')
                          ->table('tblsuppliers')
                          ->get();
        $dataCount = $supplierData->count();

        foreach ($supplierData as $ctr => $sup) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Suppliers: [ $percentage% ] migrated.\n";

            $supplier = new Supplier;
            $supplier->company_name = $sup->company_name;
            $supplier->date_filed = $sup->date_file;
            $supplier->address = $sup->address;
            $supplier->bank_name = $sup->name_bank;
            $supplier->account_name = $sup->account_name;
            $supplier->account_no = $sup->account_no;
            $supplier->email = $sup->email;
            $supplier->website_url = $sup->url_address;
            $supplier->telephone_no = $sup->telephone_no;
            $supplier->fax_no = $sup->fax_no;
            $supplier->mobile_no = $sup->mobile_no;
            $supplier->date_established = $sup->date_established;
            $supplier->vat_no = $sup->vat_no;
            $supplier->contact_person = $sup->contact_person;
            $supplier->nature_business = $sup->nature_business;
            $supplier->nature_business_others = $sup->nature_business_others;
            $supplier->delivery_vehicle_no = $sup->delivery_vehicle_no;
            $supplier->product_lines = $sup->product_lines;
            $supplier->credit_accomodation = str_replace('-', '_', $sup->credit_accomodation);
            $supplier->attachment = $sup->attachment;
            $supplier->attachment_others = $sup->attachment_others;
            $supplier->is_active = $sup->active;

            $supClassData = DB::connection('mysql-old-pftms')
                              ->table('tblsupplier_classifications')
                              ->where('id', $sup->class_id)
                              ->first();
            $supClass = SupplierClassification::where('classification_name', 'like', "%".$supClassData->classification."%")
                                              ->first();

            $supplier->classification = $supClass->id;
            $supplier->save();
        }
    }
}
