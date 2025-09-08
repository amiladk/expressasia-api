<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePhone extends Model
{
    use HasFactory;
    /**
* The attributes that are mass assignable.
*
* @var array
*/
protected $fillable = [
 'package',
 'phone'

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
protected $table = 'package_phone';

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