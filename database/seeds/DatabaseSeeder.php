<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DivisionsTableSeeder::class,
            RegionsTableSeeder::class,
            ProvincesTableSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            AllotmentClassSeeder::class,
            MOOEClassificationSeeder::class,
            MOOEAccountTitleSeeder::class,
        ]);
    }
}
