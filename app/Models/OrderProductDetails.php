<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'wholesaler_id',
        'retailer_id',
        'name',
        'slug',
        'description',
        'brand_name',
        'tags',
        'quantity',
        'old_price',
        'new_price',
        'discount_price',
        'images',
        'videos',
        'url',
        'status',
        'color',
        'size',
        'specifications',
        'category_id',
        'category_name',
        'sub_category_id',
        'sub_category_name',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'tags' => 'array',
        'images' => 'array',
        'videos' => 'array',
        'specifications' => 'array',
    ];

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id')->where('user_type', 2);
    }

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id')->where('user_type', 3);
    }

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
