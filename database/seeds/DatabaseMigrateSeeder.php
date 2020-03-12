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
            //DivisionsTableSeeder::class,
            //RegionsTableSeeder::class,
            //ProvincesTableSeeder::class,
            //RolesTableSeeder::class,

            //UsersMigrateSeeder::class,
            //InventoryClassificationsMigrateSeeder::class,
            //ItemClassificationsMigrateSeeder::class,
            //ModuleClassificationsMigrateSeeder::class,
            //PaperSizesMigrateSeeder::class,
            //ProcurementModesMigrateSeeder::class,
            //ProcurementStatusMigrateSeeder::class,
            //ProjectsMigrateSeeder::class,
            //SupplierClassificationsMigrateSeeder::class,
            //SuppliersMigrateSeeder::class,
            SignatoriesMigrateSeeder::class,
        ]);
    }
}
