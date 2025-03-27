<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('retailer_web_management')) {
            Schema::create('retailer_web_management', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('retailer_id');
                $table->foreign('retailer_id')->references('id')->on('users')->onDelete('cascade');

                $table->string('store_name');
                $table->string('theme')->default('default');
                $table->string('custom_domain')->nullable()->unique();
                $table->string('subdomain')->unique();
                $table->string('product_listing_key')->unique();
                $table->boolean('is_active')->default(1);
                $table->json('settings')->nullable();

                $table->string('logo')->default('');
                $table->string('brand_name')->default('');
                $table->string('store_time')->default('');
                $table->string('mobile_no')->default('');
                $table->string('email')->default('');
                $table->string('address')->default('');
                $table->string('facebook_url')->default('');
                $table->string('twitter_url')->default('');
                $table->string('instagram_url')->default('');
                $table->string('instagram_id')->default('');
                $table->string('youtube_url')->default('');
                $table->string('pinterest_url')->default('');
                $table->string('linkedin_url')->default('');
                $table->string('google_plus_url')->default('');
                $table->string('google_analytics_id')->default('');
                $table->string('facebook_pixel_id')->default('');
                $table->string('app_store_url')->default('');
                $table->string('apple_store_id')->default('');
                $table->string('play_store_url')->default('');
                $table->string('meta_title')->default('');
                $table->string('meta_keywords')->default('');
                $table->string('meta_description')->default('');
                $table->float('cod_charge')->default(0.00);
                $table->float('shipping_charge')->default(0.00);
                $table->integer('cart_limit')->default(0);
                $table->boolean('sms_service')->default(false);
                $table->boolean('enquiry_whatsapp')->default(false);
                $table->boolean('hide_pickup_address')->default(false);
                $table->boolean('request_offer')->default(false);

                $table->timestamps();
            });
        } else {
            Schema::table('retailer_web_management', function (Blueprint $table) {
                if (!Schema::hasColumn('retailer_web_management', 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn('retailer_web_management', 'retailer_id')) {
                    $table->unsignedBigInteger('retailer_id')->after('id');
                    $table->foreign('retailer_id')->references('id')->on('users')->onDelete('cascade');
                }
                if (!Schema::hasColumn('retailer_web_management', 'store_name')) {
                    $table->string('store_name')->after('retailer_id');
                }
                if (!Schema::hasColumn('retailer_web_management', 'theme')) {
                    $table->string('theme')->default('default')->after('store_name');
                }
                if (!Schema::hasColumn('retailer_web_management', 'custom_domain')) {
                    $table->string('custom_domain')->nullable()->unique()->after('theme');
                }
                if (!Schema::hasColumn('retailer_web_management', 'subdomain')) {
                    $table->string('subdomain')->unique()->after('custom_domain');
                }
                if (!Schema::hasColumn('retailer_web_management', 'product_listing_key')) {
                    $table->string('product_listing_key')->unique()->after('subdomain');
                }
                if (!Schema::hasColumn('retailer_web_management', 'is_active')) {
                    $table->boolean('is_active')->default(1)->after('product_listing_key');
                }
                if (!Schema::hasColumn('retailer_web_management', 'settings')) {
                    $table->json('settings')->nullable()->after('is_active');
                }

                // Add remaining columns (logo, brand_name, etc.)
                foreach ([
                    'logo', 'brand_name', 'store_time', 'mobile_no', 'email', 'address',
                    'facebook_url', 'twitter_url', 'instagram_url', 'instagram_id',
                    'youtube_url', 'pinterest_url', 'linkedin_url', 'google_plus_url',
                    'google_analytics_id', 'facebook_pixel_id', 'app_store_url',
                    'meta_title', 'meta_keywords', 'meta_description', 'cod_charge',
                    'shipping_charge', 'cart_limit', 'sms_service', 'enquiry_whatsapp',
                    'hide_pickup_address', 'request_offer'
                ] as $column) {
                    if (!Schema::hasColumn('retailer_web_management', $column)) {
                        $table->string($column)->default('')->nullable();
                    }
                }

                // Ensure timestamps exist
                if (!Schema::hasColumn('retailer_web_management', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailer_web_management');
    }
};
