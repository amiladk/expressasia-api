<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
 
    use HasFactory;
           /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'phone_one',
        'phone_two',
        'nic',
        'vehicle_number',
        'vehicle_type',
        'branch',
        'bank_account',
        'daily_target',
        'rate_above_target',
        'rate_below_target',
        'rate_per_additional_kg',
        'username',
        'password',
        'is_active',
        'emergency_phone',
        'emergency_address',
        'created_by',
        'last_updated_by'
     ];
 
 
     /**
      * The attributes that should be hidden for arrays.
      *
      * @var array
      */
     protected $hidden = [
         'password',
     ];
 
     /**
      * The table associated with the model.
      *
      * @var string
      */
     protected $table = 'rider';
 
     /**
      * Indicates if the model should be timestamped.
      *
      * @var bool
      */
     //public $timestamps = false;
 
     /**
      * Indicates if the model should be timestamped.
      *
      * @var bool
      */
     //const CREATED_AT = 'creation_date';
     //const UPDATED_AT = 'updated_date';

     public function bankAccount()
     {
         return $this->hasOne(BankAccount::class,'id','bank_account');
     }

     public function bank()
     {
         return $this->hasOneThrough(Bank::class,BankAccount::class,'id','id','bank_account', 'bank_id',);
     }

}

