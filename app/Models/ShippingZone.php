<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;
           /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    'id',
    'title',
    'default_delivery_charge',
    'default_charge_for_additional_kg'

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
     protected $table = 'shipping_zone';
 
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
}
