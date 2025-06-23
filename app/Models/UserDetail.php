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
        'success_wallet',
        'pending_wallet',
        'wallet_status',
        'verification_code',
        'wallet_verification_attempt',
        'wallet_verification_reject_reason',
        'account_number',
        'ifsc_code',
        'account_holder_name',
        'pancard_number',
        'pan_image',
        'aadhar_image',
        'cancel_cheque',
        'bank_details_submitted_at',
        'bank_details_verified_at'
    ];

    // Inverse One-to-One Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
