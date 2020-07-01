<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Signatory;
use App\User;


class SignatoriesMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $signatoryData = DB::connection('mysql-old-pftms')
                           ->table('tblsignatories')
                           ->get();
        $empIDs = [];
        $modules = [];
        $actives = [];

        $dataCount = $signatoryData->count();

        foreach ($signatoryData as $ctr => $sig) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Signatories: [ $percentage% ] migrated.\n";

            $user = User::where('emp_id', $sig->emp_id)
                        ->first();

            if ($user) {
                $empID = $user->id;
                $designation = $sig->position;
                $isActive = $sig->active;

                $isPR = $sig->p_req == 'y' ? 1 : 0;
                $isRFQ = $sig->rfq == 'y' ? 1 : 0;
                $isABS = $sig->abs == 'y' ? 1 : 0;
                $isPO = $sig->po_jo == 'y' ? 1 : 0;
                $isJO = $sig->po_jo == 'y' ? 1 : 0;
                $isORS = $sig->ors == 'y' ? 1 : 0;
                $isIAR = $sig->iar == 'y' ? 1 : 0;
                $isDV = $sig->dv == 'y' ? 1 : 0;
                $isRIS = $sig->ris == 'y' ? 1 : 0;
                $isPAR = $sig->par == 'y' ? 1 : 0;
                $isICS = $sig->ics == 'y' ? 1 : 0;
                $isLR = $sig->liquidation == 'y' ? 1 : 0;
                $isLDDAP = $sig->lddap == 'y' ? 1 : 0;

                $prSignType = $sig->pr_sign_type;
                $absSignType = $sig->abstract_sign_type;
                $poSignType = $sig->po_jo_sign_type;
                $orsSignType = $sig->ors_burs_sign_type;
                $iarSignType = $sig->iar_sign_type;
                $dvSignType = $sig->dv_sign_type;
                $risSignType = $sig->ris_sign_type;
                $parSignType = $sig->par_sign_type;
                $icsSignType = $sig->ics_sign_type;
                $lrSignType = $sig->liquidation_sign_type;
                $lddapSignType = $sig->lddap_sign_type;

                if (!in_array($empID, $empIDs)) {
                    $empIDs[] = $empID;
                }

                $actives[$empID] = $isActive;

                if (!array_key_exists($empID, $modules)) {
                    $modules[$empID] = [
                        'pr' => [
                            'is_allowed' => $isPR,
                            'designation' => $isPR ? $designation : '',
                            'approval' => $prSignType == 'approval' ? 1 : 0,
                            'within_app' => $prSignType == 'within-app' ? 1 : 0,
                            'funds_available' => $prSignType == 'funds-available' ? 1 : 0,
                            'recommended_by' => $prSignType == 'recommended-by' ? 1 : 0,
                        ],
                        'rfq' => [
                            'is_allowed' => $isRFQ,
                            'designation' => $isRFQ ? $designation : '',
                            'truly_yours' => $isRFQ ? 1 : 0,
                        ],
                        'abs' => [
                            'is_allowed' => $isABS,
                            'designation' => $isABS ? $designation : '',
                            'chairperson' => $absSignType == 'chairperson' ? 1 : 0,
                            'vice_chair' => $absSignType == 'vice-chairperson' ? 1 : 0,
                            'member' => $absSignType == 'member' ? 1 : 0,
                        ],
                        'po' => [
                            'is_allowed' => $isPO,
                            'designation' => $isPO ? $designation : '',
                            'funds_available' => $poSignType == 'accountant' ? 1 : 0,
                            'approved' => $poSignType == 'approval' ? 1 : 0,
                        ],
                        'jo' => [
                            'is_allowed' => $isPO,
                            'designation' => $isPO ? $designation : '',
                            'funds_available' => $poSignType == 'accountant' ? 1 : 0,
                            'requisitioning' => $poSignType == 'requisitioning' ? 1 : 0,
                            'approved' => $poSignType == 'approval' ? 1 : 0,
                        ],
                        'ors' => [
                            'is_allowed' => $isORS,
                            'designation' => $isORS ? $designation : '',
                            'approval' => $orsSignType == 'approval' ? 1 : 0,
                            'funds_available' => $orsSignType == 'budget' ? 1 : 0,
                        ],
                        'iar' => [
                            'is_allowed' => $isIAR,
                            'designation' => $isIAR ? $designation : '',
                            'inspection' => $iarSignType == 'inspector' ? 1 : 0,
                            'prop_supply' => $iarSignType == 'custodian' ? 1 : 0,
                        ],
                        'dv' => [
                            'is_allowed' => $isDV,
                            'designation' => $isDV ? $designation : '',
                            'supervisor' => $dvSignType == 'supervisor' ? 1 : 0,
                            'accounting' => $dvSignType == 'accountant' ? 1 : 0,
                            'agency_head' => $dvSignType == 'agency-head' ? 1 : 0,
                        ],
                        'ris' => [
                            'is_allowed' => $isRIS,
                            'designation' => $isRIS ? $designation : '',
                            'approved_by' => $risSignType == 'approval' ? 1 : 0,
                            'issued_by' => $risSignType == 'issuer' ? 1 : 0,
                        ],
                        'par' => [
                            'is_allowed' => $isPAR,
                            'designation' => $isPAR ? $designation : '',
                            'issued_by' => $parSignType == 'issuer' ? 1 : 0,
                        ],
                        'ics' => [
                            'is_allowed' => $isICS,
                            'designation' => $isICS ? $designation : '',
                            'received_from' => $icsSignType == 'issuer' ? 1 : 0,
                        ],
                        'lr' => [
                            'is_allowed' => $isLR,
                            'designation' => $isLR ? $designation : '',
                            'immediate_sup' => $lrSignType == 'supervisor' ? 1 : 0,
                            'accounting' => $lrSignType == 'accountant' ? 1 : 0,
                        ],
                        'lddap' => [
                            'is_allowed' => $isLDDAP,
                            'designation' => $isLDDAP ? $designation : '',
                            'cert_correct' => $lddapSignType == 'cert_correct' ? 1 : 0,
                            'approval' => $lddapSignType == 'approval' ? 1 : 0,
                            'agency_auth' => $lddapSignType == 'agency_authorized' ? 1 : 0,
                        ],
                    ];
                } else {
                    if ($isPR) {
                        $modules[$empID]['pr']['is_allowed'] = 1;
                        $modules[$empID]['pr']['designation'] = $designation;
                    }

                    if ($isRFQ) {
                        $modules[$empID]['rfq']['is_allowed'] = 1;
                        $modules[$empID]['rfq']['designation'] = $designation;
                        $modules[$empID]['rfq']['truly_yours'] = 1;
                    }

                    if ($isABS) {
                        $modules[$empID]['abs']['is_allowed'] = 1;
                        $modules[$empID]['abs']['designation'] = $designation;
                    }

                    if ($isPO) {
                        $modules[$empID]['po']['is_allowed'] = 1;
                        $modules[$empID]['po']['designation'] = $designation;
                    }

                    if ($isJO) {
                        $modules[$empID]['jo']['is_allowed'] = 1;
                        $modules[$empID]['jo']['designation'] = $designation;
                    }

                    if ($isORS) {
                        $modules[$empID]['ors']['is_allowed'] = 1;
                        $modules[$empID]['ors']['designation'] = $designation;
                    }

                    if ($isIAR) {
                        $modules[$empID]['iar']['is_allowed'] = 1;
                        $modules[$empID]['iar']['designation'] = $designation;
                    }

                    if ($isDV) {
                        $modules[$empID]['dv']['is_allowed'] = 1;
                        $modules[$empID]['dv']['designation'] = $designation;
                    }

                    if ($isRIS) {
                        $modules[$empID]['ris']['is_allowed'] = 1;
                        $modules[$empID]['ris']['designation'] = $designation;
                    }

                    if ($isPAR) {
                        $modules[$empID]['par']['is_allowed'] = 1;
                        $modules[$empID]['par']['designation'] = $designation;
                    }

                    if ($isICS) {
                        $modules[$empID]['ics']['is_allowed'] = 1;
                        $modules[$empID]['ics']['designation'] = $designation;
                    }

                    if ($isLR) {
                        $modules[$empID]['lr']['is_allowed'] = 1;
                        $modules[$empID]['lr']['designation'] = $designation;
                    }

                    if ($isLDDAP) {
                        $modules[$empID]['lddap']['is_allowed'] = 1;
                        $modules[$empID]['lddap']['designation'] = $designation;
                    }

                    switch ($prSignType) {
                        case 'approval':
                            $modules[$empID]['pr']['is_allowed'] = 1;
                            $modules[$empID]['pr']['approval'] = 1;
                            break;
                        case 'within-app':
                            $modules[$empID]['pr']['is_allowed'] = 1;
                            $modules[$empID]['pr']['within_app'] = 1;
                            break;
                        case 'funds-available':
                            $modules[$empID]['pr']['is_allowed'] = 1;
                            $modules[$empID]['pr']['funds_available'] = 1;
                            break;
                        case 'recommended-by':
                            $modules[$empID]['pr']['is_allowed'] = 1;
                            $modules[$empID]['pr']['recommended_by'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($absSignType) {
                        case 'chairperson':
                            $modules[$empID]['abs']['is_allowed'] = 1;
                            $modules[$empID]['abs']['chairperson'] = 1;
                            break;
                        case 'vice-chairperson':
                            $modules[$empID]['abs']['is_allowed'] = 1;
                            $modules[$empID]['abs']['vice_chair'] = 1;
                            break;
                        case 'member':
                            $modules[$empID]['abs']['is_allowed'] = 1;
                            $modules[$empID]['abs']['member'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($poSignType) {
                        case 'accountant':
                            $modules[$empID]['po']['is_allowed'] = 1;
                            $modules[$empID]['po']['funds_available'] = 1;

                            $modules[$empID]['jo']['is_allowed'] = 1;
                            $modules[$empID]['jo']['funds_available'] = 1;
                            break;
                        case 'requisitioning':
                            $modules[$empID]['jo']['is_allowed'] = 1;
                            $modules[$empID]['jo']['requisitioning'] = 1;
                            break;
                        case 'approval':
                            $modules[$empID]['po']['is_allowed'] = 1;
                            $modules[$empID]['po']['approved'] = 1;

                            $modules[$empID]['jo']['is_allowed'] = 1;
                            $modules[$empID]['jo']['approved'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($orsSignType) {
                        case 'approval':
                            $modules[$empID]['ors']['is_allowed'] = 1;
                            $modules[$empID]['ors']['approval'] = 1;
                            break;
                        case 'budget':
                            $modules[$empID]['ors']['is_allowed'] = 1;
                            $modules[$empID]['ors']['funds_available'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($iarSignType) {
                        case 'inspector':
                            $modules[$empID]['iar']['is_allowed'] = 1;
                            $modules[$empID]['iar']['inspection'] = 1;
                            break;
                        case 'custodian':
                            $modules[$empID]['iar']['is_allowed'] = 1;
                            $modules[$empID]['iar']['prop_supply'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($dvSignType) {
                        case 'supervisor':
                            $modules[$empID]['dv']['is_allowed'] = 1;
                            $modules[$empID]['dv']['supervisor'] = 1;
                            break;
                        case 'accountant':
                            $modules[$empID]['dv']['is_allowed'] = 1;
                            $modules[$empID]['dv']['accounting'] = 1;
                            break;
                        case 'agency-head':
                            $modules[$empID]['dv']['is_allowed'] = 1;
                            $modules[$empID]['dv']['agency_head'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($risSignType) {
                        case 'approval':
                            $modules[$empID]['ris']['is_allowed'] = 1;
                            $modules[$empID]['ris']['approved_by'] = 1;
                            break;
                        case 'issuer':
                            $modules[$empID]['ris']['is_allowed'] = 1;
                            $modules[$empID]['ris']['issued_by'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($parSignType) {
                        case 'issuer':
                            $modules[$empID]['par']['is_allowed'] = 1;
                            $modules[$empID]['par']['issued_by'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($icsSignType) {
                        case 'issuer':
                            $modules[$empID]['ics']['is_allowed'] = 1;
                            $modules[$empID]['ics']['received_from'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($lrSignType) {
                        case 'supervisor':
                            $modules[$empID]['lr']['is_allowed'] = 1;
                            $modules[$empID]['lr']['immediate_sup'] = 1;
                            break;
                        case 'accountant':
                            $modules[$empID]['lr']['is_allowed'] = 1;
                            $modules[$empID]['lr']['accounting'] = 1;
                            break;
                        default:
                            break;
                    }

                    switch ($lddapSignType) {
                        case 'cert_correct':
                            $modules[$empID]['lddap']['is_allowed'] = 1;
                            $modules[$empID]['lddap']['cert_correct'] = 1;
                            break;
                        case 'approval':
                            $modules[$empID]['lddap']['is_allowed'] = 1;
                            $modules[$empID]['lddap']['approval'] = 1;
                            break;
                        case 'agency_authorized':
                            $modules[$empID]['lddap']['is_allowed'] = 1;
                            $modules[$empID]['lddap']['agency_auth'] = 1;
                            break;
                        default:
                            break;
                    }

                    $actives[$empID] = $isActive;
                }
            }
        }

        if (count($empIDs) > 0) {
            foreach ($empIDs as $empid) {
                $signatory = new Signatory;
                $signatory->emp_id = $empid;
                $signatory->module = json_encode($modules[$empid]);
                $signatory->is_active = $actives[$empid];
                $signatory->save();
            }
        }
    }
}
