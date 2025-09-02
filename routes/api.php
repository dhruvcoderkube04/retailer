<?php

use App\Http\Controllers\API\Retailer\CustomerRegisterController;
use App\Http\Controllers\API\Retailer\OtpController;
use App\Http\Controllers\API\Retailer\RetailerProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/store-info', [RetailerProductController::class, 'storeInfo']);
Route::post('/get-products', [RetailerProductController::class, 'getProducts']);
Route::post('/search-products', [RetailerProductController::class, 'searchProducts']);
Route::get('/singal-product-details/{slug}', [RetailerProductController::class, 'getSingalProductDetails']);
Route::post('/checkout', [RetailerProductController::class, 'checkout']);
Route::post('/checkout1', [RetailerProductController::class, 'checkoutNew']);  //this is checkout1
Route::post('/new-arrivals', [RetailerProductController::class, 'getNewArrivals']);
Route::post('/contact-us', [RetailerProductController::class, 'contactUs']);
Route::post('/otp/send', [OtpController::class, 'sendOtp']);
Route::post('/otp/verify/login', [OtpController::class, 'verifyOtpLogin']);
Route::post('/otp/verify/checkout', [OtpController::class, 'verifyOtpCheckout']);
Route::post('/apply-coupon', [RetailerProductController::class, 'applyCoupon']);
Route::get('/home-page-sections', [RetailerProductController::class, 'getHomePageSections']);
Route::get('/about-us-page', [RetailerProductController::class, 'getAboutUsPage']);
Route::get('/contact-us-page', [RetailerProductController::class, 'getContactUsPage']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// for customer register api
Route::prefix('customer')->group(function () {
    // Auth routes
    Route::post('register', [CustomerRegisterController::class, 'register']);
    Route::post('login', [CustomerRegisterController::class, 'login']);
    Route::post('login/otp', [CustomerRegisterController::class, 'loginOtp']);
    Route::post('logout', [CustomerRegisterController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('forgot-password', [CustomerRegisterController::class, 'forgotPassword']);
    Route::post('reset-password', [CustomerRegisterController::class, 'resetPassword']);
    Route::get('verify-email/{token}', [CustomerRegisterController::class, 'verifyEmail']);

    // Send password reset link
    Route::post('/forgot-password', [CustomerRegisterController::class, 'forgotPassword']);
    Route::post('/token-password', [CustomerRegisterController::class, 'resetPassword']);

    Route::middleware('auth:customer')->group(function () {
        Route::get('/details', [CustomerRegisterController::class, 'getCustomerDetails']);
        Route::get('/orders', [RetailerProductController::class, 'customerOrders']);
        Route::post('/shipping-address', [RetailerProductController::class, 'shippingAddress']);
        Route::get('/get-shipping-address', [RetailerProductController::class, 'getShippingAddress']);
        Route::post('/account-details', [RetailerProductController::class, 'accountDetails']);
        Route::post('/reset-password', [RetailerProductController::class, 'resetPassword']);
        Route::post('/add-to-wishlist', [RetailerProductController::class, 'addToWishlist']);
        Route::get('/wishlist', [RetailerProductController::class, 'wishlist']);
        Route::post('/add-to-cart', [RetailerProductController::class, 'addToCart']);
        Route::get('/cart', [RetailerProductController::class, 'cart']);
        Route::post('/remove-to-wishlist', [RetailerProductController::class, 'removeToWishlist']);
        Route::post('/remove-to-cart', [RetailerProductController::class, 'removeToCart']);
        Route::post('/cancel-order', [RetailerProductController::class, 'cancelOrder']);
    });

});
