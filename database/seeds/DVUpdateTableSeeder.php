<?php

use Illuminate\Database\Seeder;

class DVUpdateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // Update Sept 17, 2020: Added for_payment_by and date_for_payment columns
        $dvData = DB::table('disbursement_vouchers')->get();

        foreach ($dvData as $dv) {
            DB::table('disbursement_vouchers')->where('id', $dv->id)
                              ->update([
                'date_disbursed' => NULL,
                'disbursed_by' => NULL,
                'date_for_payment' => $dv->date_disbursed,
                'for_payment_by' => $dv->disbursed_by,
            ]);
        }
    }
}
