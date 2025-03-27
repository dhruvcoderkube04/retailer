<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'coupan_code_name',
        'code',
        'discount',
        'discount_type',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'status',
    ];
}
