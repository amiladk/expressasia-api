<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PackageType;
use App\Models\Branch;
use App\Models\Status;
use App\Models\City;


class Package extends Model
{
    
    use HasFactory;
           /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'waybill',
        'status',
        'client',
        'city',
        'client_ref',
        'recipient',
        'address',
        'cod',
        'delivery_charge',
        'client_remarks',
        'courier_remarks',
        'weight',
        'created_date',
        'assigned_branch',
        'assigned_rider',
        'attempts',
        'item_description',
        'client_settled',
        'package_type',
        'pending_return_date',


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
     protected $table = 'package';
 
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

     public function packageType()
     {
         return $this->hasOne(PackageType::class,'id','package_type');
     }
 
     public function Status()
     {
         return $this->hasOne(Status::class,'id','status');
     }
 
     public function City()
     {
         return $this->hasOne(City::class,'id','city');
     }
     
     public function Branch()
     {
         return $this->hasOne(Branch::class,'id','assigned_branch');
     }

     
    public function Phone()
    {
        return $this->hasMany(PackagePhone::class,'package','id');
    }

    public function Notes()
    {
        return $this->hasMany(PackageRemarks::class,'package','id');
    }

    public function Client()
    {
        return $this->hasOne(Client::class,'id','client');
    }
    

}
