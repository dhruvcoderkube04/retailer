<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'phone_number', // User no phone number
        'otp', // Generated OTP
        'expires_at', // OTP no expiration time
        'verified', // 1 = verified, 0 = not verified
    ];

}
