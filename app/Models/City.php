<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'city',
        'district',
        'province',
        'zip_code',
        'shipping_zone'
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
    protected $table = 'city';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    //const CREATED_AT = 'creation_date';
    //const UPDATED_AT = 'updated_date';

    public function shippingZone()
    {
        return $this->hasOne(ShippingZone::class,'id','shipping_zone');
    }

    public function ClientPicing()
    {
        return $this->hasOne(ClientPricing::class,'shipping_zone','shipping_zone');
    }

    public function BranchCoverage()
    {
        return $this->hasOne(BranchCoverage::class,'city','id');
    }
}

