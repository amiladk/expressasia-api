<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaybillRequest extends Model
{
    use HasFactory;
            /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'client',
        'created_date',
        'waybill_quantity',
        'barcode_quantity',
        'barcode_from',
        'barcode_to',
        'confirm_date',
        'status',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    /*protected $hidden = [
        'password',
    ];*/

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'waybill_request';

}
