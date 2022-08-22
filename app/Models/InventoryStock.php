<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;
use Kyslik\ColumnSortable\Sortable;

class InventoryStock extends Model
{
    use SoftDeletes, Sortable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_stocks';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'pr_id',
        'po_id',
        'entity_name',
        'fund_cluster',
        'inventory_no',
        'division',
        'office',
        'responsibility_center',
        'po_no',
        'date_po',
        'supplier',
        'purpose',
        'inventory_classification',
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

    public function stockrecipients() {
        return $this->hasMany('App\Models\InventoryStockIssue', 'inv_stock_id', 'id');
    }

    public function stockitems() {
        return $this->hasMany('App\Models\InventoryStockItem', 'inv_stock_id', 'id')->orderBy('item_no');
    }

    public function supplier() {
        return $this->hasOne('App\Models\Supplier', 'id', 'supplier');
    }

    public function procstatus() {
        return $this->hasOne('App\Models\ProcurementStatus', 'id', 'status');
    }

    public function inventoryclass() {
        return $this->hasOne('App\Models\InventoryClassification', 'id', 'inventory_classification');
    }

    public $sortable = [
        'inventory_no',
    ];
}
