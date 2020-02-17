<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbstractQuotation extends Model
{
    use SoftDeletes;

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
        'code',
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
        'sig_funds_available',
        'document_abrv'
    ];
}
