<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSettlementItem extends Model
{
    use HasFactory;
    /**
* The attributes that are mass assignable.
*
* @var array
*/
protected $fillable = [
'client_settlement_batch',
'package',
'cod',
'delivery_charge',
'cash_handling_fee',
'amount_payable'


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
protected $table = 'client_settlement_item';

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

public function Package()
{
  return $this->hasOne(Package::class,'id','package');
}

}
