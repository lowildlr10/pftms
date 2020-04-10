<?php

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\RequestQuotation;
use App\Models\AbstractQuotation;
use App\Models\AbstractQuotationItem;
use App\User;
use App\Models\Signatory;
use App\Models\ProcurementMode;
use App\Models\Supplier;
use App\Models\DocumentLog as DocLog;

class ORSBURSsMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prsData = DB::connection('mysql-old-pftms')
                     ->table('tblpr')
                     ->get();
    }
}
