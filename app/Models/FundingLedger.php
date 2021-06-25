<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class FundingLedger extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funding_ledgers';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'budget_id',
        'ledger_for',
        'ledger_type',
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

    public function ledgerItems() {
        return $this->hasMany('App\Models\FundingLedgerItem', 'id', 'ledger_id');
    }

    public function ledgerAllotments() {
        return $this->hasMany('App\Models\FundingLedgerAllotment', 'id', 'ledger_id');
    }

    public function project() {
        return $this->hasOne('App\Models\FundingProject', 'id', 'project_id');
    }

    public function allotments() {
        return $this->hasMany('App\Models\FundingAllotment', 'id', 'allotment_id');
    }
}
