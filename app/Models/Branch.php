<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
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
        'phone_one',
        'phone_two',
        'address',
        'manager',
        'is_active',
        'created_by',
        'last_updated_by'
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
    protected $table = 'branch';

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

    public function user()
    {
        return $this->hasOne(User::class,'id','manager');
    }
}
