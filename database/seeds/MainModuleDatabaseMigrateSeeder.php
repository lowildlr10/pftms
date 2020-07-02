<?php

use Illuminate\Database\Seeder;

class MainModuleDatabaseMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([

            /** Procurement */
            PRsMigrateSeeder::class,
            RFQsMigrateSeeder::class,
            AbstractsMigrateSeeder::class,
            POJOsMigrateSeeder::class,
            ORSBURSsMigrateSeeder::class,
            IARsMigrateSeeder::class,
            DVsMigrateSeeder::class,
            InventoryStocksMigrateSeeder::class,
        ]);
    }
}
