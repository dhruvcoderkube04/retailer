<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class StoreCustomersDetails extends Authenticatable
{

    use HasApiTokens;
    protected $table = "store_customers_details";
    protected $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'email',
        'password',
        'phone_number',
        'user_token',
        'is_active',
        'email_verification_token',
        'customer_id'
    ];
}

