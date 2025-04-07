<?php

use App\Http\Controllers\API\Retailer\RetailerProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/retailer-products', [RetailerProductController::class, 'getRetailerProducts']);
Route::get('/singal-product-details', [RetailerProductController::class, 'getSingalProductDetails']);

Route::post('/retailer-webinfo', [RetailerProductController::class, 'getRetailerWebInfo']);
Route::post('/checkout', [RetailerProductController::class, 'checkout']);

Route::post('/send-otp', [RetailerProductController::class, 'sendOtp']);
Route::post('/verify-otp', [RetailerProductController::class, 'verifyOtp']);
