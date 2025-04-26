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
        'product_amount',
        'charges',
        'total_amount',
        'current_balance',
        'order_type',
        'status'
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
