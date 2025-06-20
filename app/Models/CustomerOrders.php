<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOrders extends Model
{
    protected $fillable = [
        'order_id',
        'checkout_type',
        'customer_id',
        'order_product_id',
        'product_id',
        'retailer_clone_product_id',
        'retailer_id',
        'wholesaler_id',
        'product_variation',
        'quantity',
        'final_amount',
        'margin',
        'order_process_by',
        'status',
        'shipment_status',
        'fulfilledby',
        'courier_partner_id',
        'courier_partner_code',
        'shipment_status_updated_at',
        'approved_by_retailer_at',
        'transfered_retailer_to_wholesaler_at',
        'approved_by_wholesaler_at',
        'pickup_at',
        'in_transit_at',
        'ofd_at',
        'delivered_at',
        'rto_at',
        'rtn_to_seller_at',
        'close_at',
        'cancel_at',
        'lost_at',
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
        'shipping_charge_profit',
        'cod_charge_profit',
        'rto_charge_profit',
        'final_charges',
        'expected_delivery',
        'payment_status',
        'payment_method',
        'coupon_applied_id'
    ];

    protected $casts = [
        'final_charges' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(CustomerDetails::class, 'customer_id');
    }

    public function order_product_detail()
    {
        return $this->belongsTo(OrderProductDetails::class, 'order_product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function retailer()
    {
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

    public function appliedCoupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_applied_id');
    }
}
