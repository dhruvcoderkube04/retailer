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
        'subdomain',
        'product_listing_key',
        'is_active',
        'settings',
        'logo',
        'brand_name',
        'store_time',
        'mobile_no',
        'email',
        'address',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'apple_store_id',
        'instagram_id',
        'youtube_url',
        'pinterest_url',
        'linkedin_url',
        'google_plus_url',
        'google_analytics_id',
        'facebook_pixel_id',
        'app_store_url',
        'play_store_url',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'cod_charge',
        'shipping_charge',
        'cart_limit',
        'sms_service',
        'enquiry_whatsapp',
        'hide_pickup_address',
        'request_offer',
        'favicon',
        'banner',
        'offer_text',
        'banner_title',
        'banner_sub_title',
        'banner_button_title'
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id', 'id');
    }

    public function theme_data()
    {
        return $this->belongsTo(Theme::class, 'theme');
    }

}
