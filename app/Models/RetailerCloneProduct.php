<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerCloneProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'retailer_id', 'name', 'slug', 'description',
        'brand_name', 'tags', 'quantity', 'new_price','old_price', 'discount_price',
        'sku', 'images', 'videos', 'url', 'status', 'color', 'size',
        'specifications', 'category_id', 'meta_title', 'meta_description', 'meta_keywords'
    ];

    protected $casts = [
        'tags' => 'array',
        'images' => 'array',
        'videos' => 'array',
        'specifications' => 'array',
    ];

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id')->where('user_type', 3);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }
}
