<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\BankAccount;
use App\Models\Bank;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;
    /**
 * The attributes that are mass assignable.
 *
 * @var array
 */
protected $fillable = [
    'id',
    'name',
    'logo',
    'phone_one',
    'phone_two',
    'pickup_address',
    'pickup_city',
    'email_two',
    'pickup_location_link',
    'email',
    'username',
    'password',
    'bank_account',
    'is_active',
    'last_updated_at',
    'created_by',
    'last_updated_by',
    'payment_cycle',
    'cash_handling_rate',
    'sales_person',
    'api_key'
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
protected $table = 'client';

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

    public function Logo()
    {
        return $this->hasOne(Image::class,'id','logo');
    }


}
