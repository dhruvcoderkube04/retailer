<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrders extends Model
{
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
        'confirmed_by_retailer_at',
        'transfered_retailer_to_wholesaler_at',
        'confirmed_by_wholesaler_at',
        'shipped_by_retailer_at',
        'shipped_by_wholesaler_at',
        'delivered_by_retailer_at',
        'delivered_by_wholesaler_at',
        'cancelled_by_customer_at',
        'cancelled_by_retailer_at',
        'cancelled_by_wholesaler_at',
        'received_at',

        'delivered_by',
        'cancelled_by',
        'inactive',
        'cancelled_reason',

        'pickup_address_id',
        'product_weight',

        'tracking_number',
        'api_order_id',
        
        'shipping_label_url',
        'pickup_image',

        'courier_service',
        'service_mode',
        'shipping_charge',
        'cod_charge',
        'rto_charge',
        'expected_delivery',
        'retailer_transit_at',
        'wholesaler_transit_at',
        'payment_status',
        'payment_method',
        'variation_id'

    ];

    protected $casts = [
        'charges' => 'array',
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
