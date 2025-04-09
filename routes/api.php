<?php

use App\Http\Controllers\API\Retailer\RetailerProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/store-info', [RetailerProductController::class, 'storeInfo']);
Route::post('/get-products', [RetailerProductController::class, 'getProducts']);
Route::get('/singal-product-details', [RetailerProductController::class, 'getSingalProductDetails']);
Route::post('/checkout', [RetailerProductController::class, 'checkout']);


// Route::post('/retailer-products', [RetailerProductController::class, 'getProducts']);
// Route::post('/retailer-products', [RetailerProductController::class, 'getRetailerProducts']);
// Route::post('/send-otp', [RetailerProductController::class, 'sendOtp']);
// Route::post('/verify-otp', [RetailerProductController::class, 'verifyOtp']);
