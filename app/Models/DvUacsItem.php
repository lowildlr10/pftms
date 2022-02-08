<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class DvUacsItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dv_uacs_items';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'dv_id',
        'uacs_id',
        'description',
        'amount',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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

    public function getItemGroupNo($prID) {
        $itemData = $this::select('group_no')
                         ->where('pr_id', $prID)
                         ->first();

        return $itemData ? $itemData->group_no : NULL;
    }

    public function getItemGroupNos($prID) {
        $data = [];
        $_data = [];
        $groupNumbers = $this::select('group_no')
                             ->where('pr_id', $id)
                             ->orderBy('group_no')
                             ->distinct()
                             ->get();

        foreach ($groupNumbers as $grpNo) {
            $_data[] = $grpNo->group_no;
        }

        $_data = array_unique($_data);

        foreach ($_data as $value) {
            $data[] = $value;
        }

        return json_encode($data);
    }

}
