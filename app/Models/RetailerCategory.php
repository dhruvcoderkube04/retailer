<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerCategory extends Model
{
    protected $fillable = [
        'retailer_id', 'category_id', 'sub_category_id', 'category_image'
    ];

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
