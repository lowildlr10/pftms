<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use SoftDeletes;

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
        'code',
        'date_pr',
        'date_pr_approved',
        'date_pr_disapproved',
        'date_pr_cancelled',
        'funding',
        'requested_by',
        'office',
        'division',
        'approved_by',
        'sig_app',
        'sig_funds_available',
        'recommended_by',
        'purpose',
        'remarks',
        'status',
    ];
}
