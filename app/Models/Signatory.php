<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Models\EmpAccount as User;

class Signatory extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'signatories';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'emp_id',
        'module',
        'is_active'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public static function boot() {
         parent::boot();
         self::creating(function($model) {
             $model->id = self::generateUuid();
         });
    }

    public static function generateUuid() {
         return Uuid::generate();
    }

    public function user() {
        return $this->belongsTo('App\Models\EmpAccount', 'emp_id', 'id');
    }

    public function getSignatory($id) {
        $signatoryData = $this::with('user')->where('id', $id)->first();

        if ($signatoryData) {
            $signatoryData->module = json_decode($signatoryData->module);
            $userData = $signatoryData->user;
            $firstname = $userData['firstname'];
            $middleInitial = !empty($userData['middlename']) ?
                            ' '.$userData['middlename'][0].'. ' : ' ';
            $lastname = $userData['lastname'];
            $fullname = $firstname.$middleInitial.$lastname;
            $position = $userData['position'];
            $signature = $userData['signature'];
            $prDesignation = $signatoryData->module->pr->designation;
            $rfqDesignation = $signatoryData->module->rfq->designation;
            $absDesignation = $signatoryData->module->abs->designation;
            $poDesignation = $signatoryData->module->po->designation;
            $orsDesignation = $signatoryData->module->ors->designation;
            $iarDesignation = $signatoryData->module->iar->designation;
            $dvDesignation = $signatoryData->module->dv->designation;
            $risDesignation = $signatoryData->module->ris->designation;
            $parDesignation = $signatoryData->module->par->designation;
            $icsDesignation = $signatoryData->module->ics->designation;
            $lrDesignation = $signatoryData->module->lr->designation;
            $lddapDesignation = $signatoryData->module->lddap->designation;
            $summaryDesignation = $signatoryData->module->summary->designation;

            return (object) [
                'name' => $fullname,
                'position' => $position,
                'pr_designation' => $prDesignation,
                'rfq_designation' => $rfqDesignation,
                'abs_designation' => $absDesignation,
                'po_designation' => $poDesignation,
                'ors_designation' => $orsDesignation,
                'iar_designation' => $iarDesignation,
                'dv_designation' => $dvDesignation,
                'ris_designation' => $risDesignation,
                'par_designation' => $parDesignation,
                'ics_designation' => $icsDesignation,
                'lr_designation' => $lrDesignation,
                'lddap_designation' => $lddapDesignation,
                'summary_designation' => $summaryDesignation,
                'signature' => $signature
            ];
        } else {
            return (object) [
                'name' => NULL,
                'position' => NULL,
                'pr_designation' => NULL,
                'rfq_designation' => NULL,
                'abs_designation' => NULL,
                'po_designation' => NULL,
                'ors_designation' => NULL,
                'iar_designation' => NULL,
                'dv_designation' => NULL,
                'ris_designation' => NULL,
                'par_designation' => NULL,
                'ics_designation' => NULL,
                'lr_designation' => NULL,
                'lddap_designation' => NULL,
                'summary_designation' => NULL,
                'signature' => NULL
            ];
        }
    }
}
