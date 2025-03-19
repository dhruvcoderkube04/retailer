<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerWebManagement extends Model
{
    protected $table = 'retailer_web_management';

    protected $fillable = [
        'retailer_id',
        'store_name',
        'theme',
        'custom_domain',
        'subdomain',
        'product_listing_key',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id', 'id');
    }

}
