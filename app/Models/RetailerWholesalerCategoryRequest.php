<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerWholesalerCategoryRequest extends Model
{
     protected $fillable = [
        'retailer_id',
        'wholesaler_id',
        'sub_category_ids',
        'status'
    ];

    protected $casts = [
        'sub_category_ids' => 'array',
    ];

    const STATUS_PENDING  = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id');
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING  => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        };
    }
}
