<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Notifications\RequestQuotation as Notif;
use App\Models\EmpAccount as User;
use Kyslik\ColumnSortable\Sortable;
use App\Models\PurchaseRequest;

class RequestQuotation extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_quotations';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_id',
        'date_canvass',
        'sig_rfq',
        'canvassed_by',
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
     * Get the phone record associated with the request for quotation.
     */
    public function pr() {
        return $this->belongsTo('App\Models\PurchaseRequest', 'pr_id', 'id');
    }

    public function signatory() {
        return $this->hasOne('App\Models\Signatory', 'id', 'sig_rfq');
    }

    public function canvasser() {
        return $this->hasOne('App\Models\EmpAccount', 'id', 'canvassed_by');
    }

    public $sortable = [
        'date_canvass',
    ];
}
