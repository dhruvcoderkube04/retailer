<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Wholesale\WholesalerController;
use App\Http\Controllers\Admin\WholesalerController as AdminWholesalerController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login')->name('post-login');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('register', 'showRegistrationForm')->name('register');
    Route::post('register', 'register');
    Route::get('forget-password', 'forgetPassword')->name('forget.password');
});

// Email Verification
Route::middleware('auth')->group(function () {
    // Route::controller(VerificationController::class)->group(function(){
    //     Route::get('email/verify','notice')->name('verification.notice');
    //     Route::get('email/verify/{id}/{hash}', 'verify')->name('verification.verify');
    //     Route::post('email/resend','resend')->name('verification.resend');
    // });
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::get('/wholesaler-list', [AdminWholesalerController::class, 'index'])->name('admin.wholesaler.list');
    Route::get('/pending-wholesaler-list', [AdminWholesalerController::class, 'pendingWholesalerList'])->name('admin.pending.wholesaler.list');
    Route::get('/add-wholesaler', [AdminWholesalerController::class, 'addWholesaler'])->name('admin.add.wholesaler');
    Route::post('/post-wholesaler', [AdminWholesalerController::class, 'postWholesaler'])->name('admin.post.wholesaler');
    Route::get('/wholesaler-detail/{id}', [AdminWholesalerController::class, 'wholesalerDetail'])->name('admin.wholesaler.detail');
    Route::post('/wholesaler-update/{id}', [AdminWholesalerController::class, 'wholesalerUpdate'])->name('admin.wholesaler.update');
});

// Wholesaler Route
Route::prefix('wholesaler')->middleware(['auth', 'verified', 'wholesaler'])->group(function () {
    Route::get('/dashboard', [WholesalerController::class, 'wholesalerDashboard'])->name('wholesaler.dashboard');
    Route::get('/product-list', [WholesalerController::class, 'productList'])->name('wholesale.product.list');
    Route::get('/add-new-product', [WholesalerController::class, 'addProductview'])->name('wholesale.addnewproduct.view');
    Route::post('/post-new-product', [WholesalerController::class, 'postNewproduct'])->name('wholesale.post.newproduct');
    Route::get('/edit-product/{id}', [WholesalerController::class, 'editProduct'])->name('wholesale.edit.product');
    Route::post('/edit-product-update/{id}', [WholesalerController::class, 'updateProduct'])->name('wholesale.update.product');

    Route::get('/order-list', [WholesalerController::class, 'orderList'])->name('wholesale.order.list');
    Route::get('/order-item/{id}', [WholesalerController::class, 'orderItem'])->name('wholesale.order.item');
    Route::post('/order-item-update', [WholesalerController::class, 'orderItemUpdate'])->name('wholesale.order.item.update');
    Route::get('/payment-history', [WholesalerController::class, 'paymentHistory'])->name('wholesale.payment.history');
});

Route::get('/cc', function() {
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize');
    Artisan::call('stroage:link');

    return 'DONE'; //Return anything
});
