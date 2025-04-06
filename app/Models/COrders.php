<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class COrders extends Model
{
    protected $table = 'c_orders'; // Explicitly set if model name doesn't match Laravel's naming convention

    protected $fillable = [
        'order_id',
        'customer_id',
        'product_id',
        'retailer_clone_product_id',
        'retailer_id',
        'wholesaler_id',
        'quantity',
        'final_amount',
        'status',
        'delivered_by',
        'cancelled_by',
        'cancelled_reason',
        'pickup_address_id',
        'product_weight',
        'tracking_number',
        'courier_service',
        'expected_delivery',
        'payment_status',
        'payment_method',
    ];

    public function customer()
    {
        return $this->belongsTo(CustomerDetails::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function retailer() {
        return $this->belongsTo(User::class, 'retailer_id')->where('user_type', 3);
    }

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id')->where('user_type', 2);
    }

    public function retailerCloneProduct()
    {
        return $this->belongsTo(RetailerCloneProduct::class, 'retailer_clone_product_id');
    }
}
