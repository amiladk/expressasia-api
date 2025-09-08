<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Status;
use App\Models\Rider;
use App\Models\Client;
use App\Models\User;


class PackageHistory extends Model
{  use HasFactory;
    /**
* The attributes that are mass assignable.
*
* @var array
*/
protected $fillable = [
  'id',
  'date',
  'package',
  'remark',
  'description',
  'user',
  'client',
  'rider',
  'status'
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
protected $table = 'package_history';

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
const date = 'creation_date';
//const UPDATED_AT = 'updated_date';

  public function Status()
  {
      return $this->hasOne(Status::class,'id','status');
  }

  public function User()
  {
     return $this->hasOne(User::class,'id','user');
  }

}
