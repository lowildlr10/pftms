<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use App\Notifications\AbstractQuotation as Notif;
use App\Models\EmpAccount as User;
use App\Models\PurchaseRequest;
use Kyslik\ColumnSortable\Sortable;

class AbstractQuotation extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'abstract_quotations';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_id',
        'date_abstract',
        'date_abstract_approved',
        'mode_procurement',
        'sig_chairperson',
        'sig_vice_chairperson',
        'sig_first_member',
        'sig_second_member',
        'sig_third_member',
        'sig_end_user',
        'sig_app',
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

    public function modeproc() {
        return $this->hasOne('App\Models\ProcurementMode', 'id', 'mode_procurement');
    }

    public $sortable = [
        'date_abstract',
    ];
}
