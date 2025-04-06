<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'state',
        'city',
        'country',
        'company_logo',
        'company_name',
        'postal_code',
        'account_number',
        'ifsc_code',
        'account_holder_name',
        'pancard_number',
        'pan_image',
        'aadhar_image',
        'cancel_cheque',
    ];

    // Inverse One-to-One Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
