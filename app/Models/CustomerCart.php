<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerCart extends Model
{

    use HasFactory;

    protected $table = 'customers_cart';

    protected $fillable = [

        'customer_id',
        'product_id',
        'product_variations_id',    
        'retailer_product_id',
        'quantity',
        'type',
        'status',
    ];



}
