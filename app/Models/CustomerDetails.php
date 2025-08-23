<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerDetails extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'customer_details';

    protected $fillable = [
        'user_id',
        'firstname',
        'lastname',
        'phone_number',
        'email',
        'password',
        'address',
        'state',
        'city',
        'pincode',
        'user_token',
        'is_active',
        'email_verification_token',
    ];
}

