<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\RetailerProducts;
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


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productVariations()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variations_id');
    }

    public function retailerProduct()
    {
        return $this->belongsTo(RetailerProducts::class, 'retailer_product_id');
    }
}
