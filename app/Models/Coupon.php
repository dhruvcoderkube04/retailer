<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{

    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        
        'retailer_id',
        'coupon_code',
        'coupon_name',
        'discount',
        'discount_type',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'status'
    ];


    
}
