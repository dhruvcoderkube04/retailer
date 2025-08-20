<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Customer extends Authenticatable
{
    use HasApiTokens; // Add HasApiTokens here
    protected $fillable = [
        'user_id', 'name', 'email', 'password', 'user_token', 'is_active',
        'email_verification_token', 'email_verified_at'
    ];

    protected $hidden = ['password', 'email_verification_token'];
}
