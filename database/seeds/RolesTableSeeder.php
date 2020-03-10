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
                'modules' => '{"ca_ors_burs":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1,"issue":1,"issue_back":1,"receive":1,"receive_back":1,"obligate":1},"ca_dv":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1,"issue":1,"issue_back":1,"receive":1,"receive_back":1,"payment":1},"ca_lr":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1,"issue":1,"issue_back":1,"receive":1,"receive_back":1,"liquidate":1},"proc_pr":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1,"cancel":1,"approve":1,"disapprove":1},"proc_rfq":{"is_allowed":1,"on":1,"update":1,"issue":1,"receive":1},"proc_abstract":{"is_allowed":1,"on":1,"create":1,"update":1,"approve_po_jo":1},"proc_po_jo":{"is_allowed":1,"on":1,"update":1,"delete":1,"destroy":1,"signed":1,"approve":1,"uncancel":1,"issue":1,"receive":1,"inspection":1},"proc_ors_burs":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1,"issue":1,"issue_back":1,"receive":1,"receive_back":1,"obligate":1},"proc_iar":{"is_allowed":1,"on":1,"update":1,"issue":1,"inspect":1},"proc_dv":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1,"issue":1,"issue_back":1,"receive":1,"receive_back":1,"payment":1},"pay_lddap":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1,"approval":1,"approve":1,"summary":1},"track_pr_rfq":{"is_allowed":1,"on":1},"track_rfq_abs":{"is_allowed":1,"on":1},"track_abs_po":{"is_allowed":1,"on":1},"track_po_ors":{"is_allowed":1,"on":1},"track_po_iar":{"is_allowed":1,"on":1},"track_iar_stock":{"is_allowed":1,"on":1},"track_iar_dv":{"is_allowed":1,"on":1},"track_ors_dv":{"is_allowed":1,"on":1},"track_dv_lddap":{"is_allowed":1,"on":1},"track_dis_sum":{"is_allowed":1,"on":1},"track_sum_bank":{"is_allowed":1,"on":1},"lib_inv_class":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"lib_item_class":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"lib_proc_mode":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"lib_funding":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"lib_signatory":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"lib_sup_class":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"lib_supplier":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"lib_unit_issue":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"lib_paper_size":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"acc_division":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"acc_role":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"acc_group":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"acc_account":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"acc_user_log":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"place_region":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1},"place_province":{"is_allowed":1,"on":1,"create":1,"update":1,"delete":1,"destroy":1}}'
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
