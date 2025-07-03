<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'request_type',
        'wholesaler_id',
        'request_amount',
        'status',
        'remarks',
        'approver_remark',
        'transaction_id',
        'account_transaction_id',
        'processing_at',
        'rejected_at',
        'completed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id');
    }
}
