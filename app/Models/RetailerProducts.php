<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerProducts extends Model
{
    use HasFactory;

    protected $fillable = [
        'retailer_id', 'wholesaler_id', 'category_id', 'category_id', 'margin', 'payment_method', 'notes'
    ];

    public function retailer() {
        return $this->belongsTo(User::class, 'retailer_id')->where('user_type', 3);
    }

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id')->where('user_type', 2);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'wholesaler_id', 'wholesaler_id');
    }
}
