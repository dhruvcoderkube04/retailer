<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'category_id', 'sub_category_name', 'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function retailer_categories()
    {
        return $this->hasMany(RetailerCategory::class, 'sub_category_id');
    }
}
