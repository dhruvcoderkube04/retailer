<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'request_amount',
        'status',
        'remarks'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
