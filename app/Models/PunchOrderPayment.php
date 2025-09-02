<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PunchOrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'retailer_id',
        'payment_type',
        'amount_paid',
        'status',
        'notes',
        'paid_at',
        'user_id',
        'remaining_amount',
        'product_amount',
    ];

    public function order()
    {
        return $this->belongsTo(CustomerOrders::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
