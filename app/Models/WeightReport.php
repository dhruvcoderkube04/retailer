<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightReport extends Model
{
    protected $fillable = [
        'courier_partner',

        // Common
        'awb_number',
        'payment_mode',
        'total_order_value',
        'product_name',
        'dimension',

        // FSHIP
        'weight_applied',
        'raised_date',
        'applied_weight',
        'entered_weight',
        'courier',
        'product_quantity',
        'shipping_charges_forward',
        'shipping_charges_rto',
        'weight_charges_forward',
        'weight_charges_rto',
        'dispute_status',

        // LORRIGO
        'record_created_at',
        'order_reference_id',
        'order_weight',
        'picked_at',
        'delivered_at',
        'rto_date',
        'rto_delivered_at',
        'pickup_name',
        'pickup_city',
        'origin_pincode',
        'dest_address',
        'dest_city',
        'dest_state',
        'dest_pincode',
        'attempt_count',
        'vendor_box_dimension',
        'vendor_weight',
        'fwd_applicable',
        'rto_applicable',
        'status',
        'zone_charged',
        'cod_charges',
        'forward_charges',
        'rto_charges',
        'exs_weight',
        'fwd_excess_charge',
        'rto_excess_charge',
        'gross_charges',
        'tax',
        'net_charge',
        'image_link',
    ];
}
