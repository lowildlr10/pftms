<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionAcceptance extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inspection_acceptance_reports';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'iar_no';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'iar_no',
        'code',
        'pr_id',
        'ors_id',
        'date_iar',
        'invoice_no',
        'date_invoice',
        'sig_inspection',
        'sig_supply'
    ];
}
