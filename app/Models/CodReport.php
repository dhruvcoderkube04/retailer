<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodReport extends Model
{
    protected $fillable = [
        'courier_partner',

        // Common
        'awb_no',
        'amount',

        // FSHIP
        'service_provider_name',
        'order_date',
        'order_time',
        'order_id',
        'delivery_date',
        'delivery_time',

        // LORRIGO
        'remittance_number',
        'date',
        'bank_transaction_id',
        'status',
    ];
}
