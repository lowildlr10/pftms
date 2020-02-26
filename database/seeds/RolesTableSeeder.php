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
                    "procurement_pr" : {
						"is_allowed": 1,
                        "create": 1,
                        "read": 1,
                        "update": 1,
						"delete": 1,
						"approve": 1,
						"disapprove": 1,
						"cancel": 1
                    },
                    "procurement_rfq" : {
                        "is_allowed": 1,
                        "update": 1,
						"issue": 1,
						"receive": 1
                    },
                    "procurement_abstract" : {
						"is_allowed": 1,
                        "create": 1,
                        "update": 1,
                        "delete": 1,
						"approve": 1
                    },
                    "procurement_po_jo" : {
						"is_allowed": 1,
                        "update": 1,
                        "accountant_signed": 1,
                        "approve": 1,
						"issue": 1,
						"receive": 1,
						"cancel": 1,
						"uncancel": 1,
						"create_ors_burs": 1,
						"for_delivery": 1,
						"to_inspection": 1
                    },
                    "procurement_ors_burs" : {
						"is_allowed": 1,
                        "update": 1,
                        "issue": 1,
                        "receive": 1,
						"obligate": 1
                    },
                    "procurement_iar" : {
						"is_allowed": 1,
                        "update": 1,
						"issue": 1,
						"inspect": 1
                    },
                    "procurement_dv" : {
						"is_allowed": 1,
                        "update": 1,
                        "issue": 1,
                        "receive": 1,
						"disburse": 1
                    }
                }'

            ], (object) [
                'role' => 'Supply & Property Officer',
                'modules' => '{
                    "procurement_pr" : {
						"is_allowed": 1,
                        "create": 1,
                        "read": 1,
                        "update": 1,
						"delete": 1,
						"approve": 1,
						"disapprove": 1,
						"cancel": 1
                    },
                    "procurement_rfq" : {
                        "is_allowed": 1,
                        "update": 1,
						"issue": 1,
						"receive": 1
                    },
                    "procurement_abstract" : {
						"is_allowed": 1,
                        "create": 1,
                        "update": 1,
                        "delete": 1,
						"approve": 1
                    },
                    "procurement_po_jo" : {
						"is_allowed": 1,
                        "update": 1,
                        "accountant_signed": 1,
                        "approve": 1,
						"issue": 1,
						"receive": 1,
						"cancel": 1,
						"uncancel": 1,
						"create_ors_burs": 1,
						"for_delivery": 1,
						"to_inspection": 1
                    },
                    "procurement_ors_burs" : {
						"is_allowed": 1,
                        "update": 1,
                        "issue": 1,
                        "receive": 0,
						"obligate": 0
                    },
                    "procurement_iar" : {
						"is_allowed": 1,
                        "update": 1,
						"issue": 1,
						"inspect": 1
                    },
                    "procurement_dv" : {
						"is_allowed": 1,
                        "update": 1,
                        "issue": 1,
                        "receive": 0,
						"disburse": 0
                    }
                }'

            ], (object) [
                'role' => 'Accountant',
                'modules' => '{
                    "procurement_pr" : {
						"is_allowed": 1,
                        "create": 1,
                        "read": 1,
                        "update": 1,
						"delete": 1,
						"approve": 0,
						"disapprove": 0,
						"cancel": 1
                    },
                    "procurement_rfq" : {
                        "is_allowed": 1,
                        "update": 1,
						"issue": 0,
						"receive": 0
                    },
                    "procurement_abstract" : {
						"is_allowed": 0,
                        "create": 0,
                        "update": 0,
                        "delete": 0,
						"approve": 0
                    },
                    "procurement_po_jo" : {
						"is_allowed": 1,
                        "update": 0,
                        "accountant_signed": 1,
                        "approve": 0,
						"issue": 0,
						"receive": 0,
						"cancel": 0,
						"uncancel": 0,
						"create_ors_burs": 0,
						"for_delivery": 0,
						"to_inspection": 0
                    },
                    "procurement_ors_burs" : {
						"is_allowed": 0,
                        "update": 0,
                        "issue": 0,
                        "receive": 0,
						"obligate": 0
                    },
                    "procurement_iar" : {
						"is_allowed": 0,
                        "update": 0,
						"issue": 0,
						"inspect": 0
                    },
                    "procurement_dv" : {
						"is_allowed": 1,
                        "update": 1,
                        "issue": 1,
                        "receive": 1,
						"disburse": 1
                    }
                }'
            ], (object) [
                'role' => 'Budget Officer',
                'modules' => '{
                    "procurement_pr" : {
						"is_allowed": 1,
                        "create": 1,
                        "read": 1,
                        "update": 1,
						"delete": 1,
						"approve": 0,
						"disapprove": 0,
						"cancel": 1
                    },
                    "procurement_rfq" : {
                        "is_allowed": 1,
                        "update": 1,
						"issue": 0,
						"receive": 0
                    },
                    "procurement_abstract" : {
						"is_allowed": 0,
                        "create": 0,
                        "update": 0,
                        "delete": 0,
						"approve": 0
                    },
                    "procurement_po_jo" : {
						"is_allowed": 0,
                        "update": 0,
                        "accountant_signed": 0,
                        "approve": 0,
						"issue": 0,
						"receive": 0,
						"cancel": 0,
						"uncancel": 0,
						"create_ors_burs": 0,
						"for_delivery": 0,
						"to_inspection": 0
                    },
                    "procurement_ors_burs" : {
						"is_allowed": 1,
                        "update": 1,
                        "issue": 1,
                        "receive": 1,
						"obligate": 1
                    },
                    "procurement_iar" : {
						"is_allowed": 0,
                        "update": 0,
						"issue": 0,
						"inspect": 0
                    },
                    "procurement_dv" : {
						"is_allowed": 0,
                        "update": 0,
                        "issue": 0,
                        "receive": 0,
						"disburse": 0
                    }
                }'
            ], (object) [
                'role' => 'PSTD',
                'modules' => '{
                    "procurement_pr" : {
						"is_allowed": 1,
                        "create": 1,
                        "read": 1,
                        "update": 1,
						"delete": 1,
						"approve": 0,
						"disapprove": 0,
						"cancel": 1
                    },
                    "procurement_rfq" : {
                        "is_allowed": 1,
                        "update": 1,
						"issue": 1,
						"receive": 1
                    },
                    "procurement_abstract" : {
						"is_allowed": 1,
                        "create": 1,
                        "update": 1,
                        "delete": 1,
						"approve": 1
                    },
                    "procurement_po_jo" : {
						"is_allowed": 1,
                        "update": 1,
                        "accountant_signed": 0,
                        "approve": 1,
						"issue": 1,
						"receive": 1,
						"cancel": 1,
						"uncancel": 1,
						"create_ors_burs": 1,
						"for_delivery": 1,
						"to_inspection": 1
                    },
                    "procurement_ors_burs" : {
						"is_allowed": 1,
                        "update": 1,
                        "issue": 1,
                        "receive": 0,
						"obligate": 0
                    },
                    "procurement_iar" : {
						"is_allowed": 1,
                        "update": 1,
						"issue": 1,
						"inspect": 1
                    },
                    "procurement_dv" : {
						"is_allowed": 1,
                        "update": 1,
                        "issue": 1,
                        "receive": 0,
						"disburse": 0
                    }
                }'
            ], (object) [
                'role' => 'Ordinary User',
                'modules' => '{
                    "procurement_pr" : {
						"is_allowed": 1,
                        "create": 1,
                        "read": 1,
                        "update": 1,
						"delete": 1,
						"approve": 0,
						"disapprove": 0,
						"cancel": 1
                    },
                    "procurement_rfq" : {
                        "is_allowed": 1,
                        "update": 1,
						"issue": 0,
						"receive": 0
                    },
                    "procurement_abstract" : {
						"is_allowed": 0,
                        "create": 0,
                        "update": 0,
                        "delete": 0,
						"approve": 0
                    },
                    "procurement_po_jo" : {
						"is_allowed": 0,
                        "update": 0,
                        "accountant_signed": 0,
                        "approve": 0,
						"issue": 0,
						"receive": 0,
						"cancel": 0,
						"uncancel": 0,
						"create_ors_burs": 0,
						"for_delivery": 0,
						"to_inspection": 0
                    },
                    "procurement_ors_burs" : {
						"is_allowed": 0,
                        "update": 0,
                        "issue": 0,
                        "receive": 0,
						"obligate": 0
                    },
                    "procurement_iar" : {
						"is_allowed": 0,
                        "update": 0,
						"issue": 0,
						"inspect": 0
                    },
                    "procurement_dv" : {
						"is_allowed": 0,
                        "update": 0,
                        "issue": 0,
                        "receive": 0,
						"disburse": 0
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
