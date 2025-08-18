<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'category_name', 'sub_category_name', 'status','category_variation'
    ];

    public function subCategory()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}
