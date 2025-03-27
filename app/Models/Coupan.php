<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupan extends Model
{
    protected $fillable = [
        'coupan_code_name',
        'coupan_code',
        'discount_price',
        'discount_type',
        'quantity',
        'date_range',
        'status',
    ];
}
