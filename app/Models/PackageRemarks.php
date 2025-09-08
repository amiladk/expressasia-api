<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageRemarks extends Model
{
    use HasFactory;

           /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'package',
        'remarks',
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
     protected $table = 'package_remarks';
 


}
