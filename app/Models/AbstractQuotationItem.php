<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbstractQuotationItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'abstract_quotation_items';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'abstract_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'abstract_id',
        'pr_id',
        'pr_item_id',
        'supplier',
        'specification',
        'remarks',
        'unit_cost',
        'total_cost'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
