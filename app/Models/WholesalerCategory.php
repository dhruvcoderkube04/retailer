<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesalerCategory extends Model
{
    protected $fillable = [
        'wholesaler_id', 'category_id', 'sub_category_id', 'category_image'
    ];

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id');
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
