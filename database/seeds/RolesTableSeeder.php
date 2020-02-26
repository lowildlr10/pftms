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
                    "pr" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "rfq" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "abstract" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "po_jo" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "ors_burs" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "iar" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "dv" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    }
                }'

            ], (object) [
                'role' => 'Supply & Property Officer',
                'modules' => '{
                    "pr" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "rfq" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "abstract" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "po_jo" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "ors_burs" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "iar" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "dv" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    }
                }'

            ], (object) [
                'role' => 'Accountant',
                'modules' => '{
                    "pr" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "rfq" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "abstract" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "po_jo" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "ors_burs" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "iar" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "dv" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    }
                }'
            ], (object) [
                'role' => 'Budget Officer',
                'modules' => '{
                    "pr" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "rfq" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "abstract" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "po_jo" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "ors_burs" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "iar" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "dv" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    }
                }'
            ], (object) [
                'role' => 'PSTD',
                'modules' => '{
                    "pr" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "rfq" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "abstract" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "po_jo" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "ors_burs" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "iar" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "dv" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    }
                }'
            ], (object) [
                'role' => 'Ordinary User',
                'modules' => '{
                    "pr" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "rfq" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "abstract" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "po_jo" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "ors_burs" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "iar" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    },
                    "dv" : {
                        "create": 1,
                        "view": 1,
                        "update": 1
                    }
                }'
            ]
        ];

        foreach ($roles as $rol) {
            $rol->modules = str_replace("\n", '', $rol->modules);
            $rol->modules = trim($rol->modules);
            $rol->modules = preg_replace('/\s/', '', $rol->modules );

            $role = new EmpRole;
            $role->role = $rol->role;
            $role->module_access = $rol->modules;
            $role->save();
        }
    }
}
