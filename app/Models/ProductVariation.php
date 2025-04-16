<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'product_variation',
        'variation_type',
        'price',
        'stock',
        'sku',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
