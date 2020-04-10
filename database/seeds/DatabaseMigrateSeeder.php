<?php

use Illuminate\Database\Seeder;

class DatabaseMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([

            /** Places Library */
            RegionsTableSeeder::class,
            ProvincesTableSeeder::class,

            /** Account Management */
            DivisionsTableSeeder::class,
            RolesTableSeeder::class,
            UsersMigrateSeeder::class,

            /** Modules Library */
            InventoryClassificationsMigrateSeeder::class,
            ItemClassificationsMigrateSeeder::class,
            ModuleClassificationsMigrateSeeder::class,
            PaperSizesMigrateSeeder::class,
            ProcurementModesMigrateSeeder::class,
            ProcurementStatusMigrateSeeder::class,
            ProjectsMigrateSeeder::class,
            SupplierClassificationsMigrateSeeder::class,
            SuppliersMigrateSeeder::class,
            SignatoriesMigrateSeeder::class,
            UnitIssuesMigrateSeeder::class,

            /** Purchase Request */
            PRsMigrateSeeder::class,
            RFQsMigrateSeeder::class,
            AbstractsMigrateSeeder::class,
            POJOsMigrateSeeder::class,
        ]);
    }
}
