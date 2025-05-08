<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    protected $fillable = [
        'customer_order_id',
        'user_id',
        'user_type',
        'description',
        'amount_type',
        'transaction_amount',
        'charges',
        'final_transaction_amount',
        'current_balance',
        'order_type',
        'status',
        'type'
    ];

    protected $casts = [
        'charges' => 'array',
    ];

    public function customer_order()
    {
        return $this->belongsTo(CustomerOrders::class, 'customer_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
