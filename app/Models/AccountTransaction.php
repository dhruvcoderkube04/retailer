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
        'total_amount',
        'charges',
        'net_amount',
        'current_balance',
        'order_type',
        'status'
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
