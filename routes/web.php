<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return view('welcome');
    $roles = [
        (object) [
            'role' => 'Developer',
            'modules' => '{
                "pr" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }, 
                "rfq" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "abstract" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "po_jo" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "ors_burs" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "iar" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "dv" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }
            }'

        ], (object) [
            'role' => 'Supply & Property Officer',
            'modules' => '{
                "pr" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }, 
                "rfq" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "abstract" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "po_jo" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "ors_burs" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "iar" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "dv" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }
            }'

        ], (object) [
            'role' => 'Accountant',
            'modules' => '{
                "pr" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }, 
                "rfq" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "abstract" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "po_jo" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "ors_burs" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "iar" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "dv" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }
            }'
        ], (object) [
            'role' => 'Budget Officer',
            'modules' => '{
                "pr" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }, 
                "rfq" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "abstract" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "po_jo" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "ors_burs" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "iar" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "dv" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }
            }'
        ], (object) [
            'role' => 'PSTD',
            'modules' => '{
                "pr" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }, 
                "rfq" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "abstract" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "po_jo" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "ors_burs" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "iar" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "dv" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }
            }'
        ], (object) [
            'role' => 'Ordinary User',
            'modules' => '{
                "pr" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }, 
                "rfq" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "abstract" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "po_jo" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "ors_burs" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "iar" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                },
                "dv" : {
                    "create": 1,
                    "read": 1,
                    "update": 1
                }
            }'
        ]
    ];

    foreach ($roles as $rol) {
        $rol->modules = str_replace("\n", '', $rol->modules);
        $rol->modules = trim($rol->modules);
        dd (json_decode($rol->modules));
    }
});
