<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
               /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_name',
        'image_alt',
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
        protected $table = 'image';
    
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
