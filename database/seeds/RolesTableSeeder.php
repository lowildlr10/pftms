<?php

use Illuminate\Database\Seeder;
use App\Models\EmpRole;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            (object) [
                'role' => 'Developer',
                'modules' => '{
                    {
                        module: "pr",
                        create: 1,
                        read: 1,
                        update: 1,
                    },
                    {
                        module: "rfq",
                        read: 1,
                        update: 1,
                    }
                }'

            ], (object) [
                'role' => 'Supply & Property Officer',
                'modules' => '{
                    {
                        module: "pr",
                        create: 1,
                        read: 1,
                        update: 1,
                    },
                    {
                        module: "rfq",
                        read: 1,
                        update: 1,
                    }
                }'

            ], (object) [
                'role' => 'Accountant',
                'modules' => '{
                    {
                        module: "pr",
                        create: 1,
                        read: 1,
                        update: 1,
                    },
                    {
                        module: "rfq",
                        read: 1,
                        update: 1,
                    }
                }'

            ], (object) [
                'role' => 'Budget Officer',
                'modules' => '{
                    {
                        module: "pr",
                        create: 1,
                        read: 1,
                        update: 1,
                    },
                    {
                        module: "rfq",
                        read: 1,
                        update: 1,
                    }
                }'

            ], (object) [
                'role' => 'PSTD',
                'modules' => '{
                    {
                        module: "pr",
                        create: 1,
                        read: 1,
                        update: 1,
                    },
                    {
                        module: "rfq",
                        read: 1,
                        update: 1,
                    }
                }'

            ], (object) [
                'role' => 'Ordinary User',
                'modules' => '{
                    {
                        module: "pr",
                        create: 1,
                        read: 1,
                        update: 1,
                    },
                    {
                        module: "rfq",
                        read: 1,
                        update: 1,
                    }
                }'

            ]
        ];

        dd($roles);

        foreach ($roles as $rol) {
            $role = new EmpRole;
            $roles->role = $rol->role;
            $roles->module_access = $rol->modules;
            $roles->save();
        }
    }
}
