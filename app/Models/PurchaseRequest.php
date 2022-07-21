<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Models\EmpAccount as User;
use App\Models\DocumentLog as DocLog;
use Kyslik\ColumnSortable\Sortable;

class PurchaseRequest extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_requests';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_no',
        'date_pr',
        'date_pr_approved',
        'date_pr_disapproved',
        'date_pr_cancelled',
        'funding_source',
        'requested_by',
        'office',
        'responsibility_center',
        'division',
        'approved_by',
        'sig_app',
        'sig_funds_available',
        'recommended_by',
        'created_by',
        'purpose',
        'remarks',
        'status',
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

    /**
     * Get the phone record associated with the purchase request.
     */
    public function rfq() {
        return $this->hasOne('App\Models\RequestQuotation', 'pr_id', 'id');
    }

    public function abstract() {
        return $this->hasOne('App\Models\AbstractQuotation', 'pr_id', 'id');
    }

    public function funding() {
        return $this->hasOne('App\Models\FundingProject', 'id', 'funding_source');
    }

    public function requestor() {
        return $this->hasOne('App\Models\EmpAccount', 'id', 'requested_by');
    }

    public function stat() {
        return $this->hasOne('App\Models\ProcurementStatus', 'id', 'status');
    }

    public function items() {
        return $this->hasMany('App\Models\PurchaseRequestItem', 'pr_id', 'id');
    }

    public function division() {
        return $this->hasOne('App\Models\EmpDivision', 'id', 'division');
    }

    public function div() {
        return $this->hasOne('App\Models\EmpDivision', 'id', 'division');
    }

    public function po() {
        return $this->hasMany('App\Models\PurchaseJobOrder', 'pr_id', 'id')->orderBy('po_no');
    }

    public function iar() {
        return $this->hasMany('App\Models\InspectionAcceptance', 'pr_id', 'id')->orderBy('iar_no');
    }

    public $sortable = [
        'pr_no',
        'date_pr',
        'purpose',
    ];

    public function checkDuplication($data) {
        $dataCount = $this::where('pr_no', $data)
                          ->count();

        return ($dataCount > 0) ? 1 : 0;;
    }
}
