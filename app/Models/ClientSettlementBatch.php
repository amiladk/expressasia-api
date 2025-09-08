<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\User;

class ClientSettlementBatch extends Model
{
    use HasFactory;
           /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id',
       'created_date',
       'item_count',
       'cod',
       'delivery_charge',
       'cash_handling_free',
       'amount_payble',
       'created_by'

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
     protected $table = 'client_settlement_batch';
 
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

     public function Client()
     {
         return $this->hasOne(Client::class,'id','client');
     }

     public function User()
     {
         return $this->hasOne(User::class,'id','created_by');
     }


}
