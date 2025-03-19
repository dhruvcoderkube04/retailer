<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'wholesaler_id', 'name', 'slug', 'description',
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

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id')->where('user_type', 2);
    }
}
