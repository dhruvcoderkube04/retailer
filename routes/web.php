<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Wholesale\WholesalerController;
use App\Http\Controllers\Retailer\RetilerController;
use App\Http\Controllers\Admin\WholesalerController as AdminWholesalerController;
use App\Http\Controllers\Admin\RetailerController as AdminRetailerController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return redirect()->to('login'); });

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

    // mange Profile
    Route::get('/profile', [DashboardController::class, 'Profile'])->name('admin.profile');
    Route::post('/profile-update', [DashboardController::class, 'profileUpdate'])->name('admin.profile.update');

    // wholesaler
    Route::get('/wholesaler-list', [AdminWholesalerController::class, 'index'])->name('admin.wholesaler.list');
    Route::get('/pending-wholesaler-list', [AdminWholesalerController::class, 'pendingWholesalerList'])->name('admin.pending.wholesaler.list');
    Route::get('/add-wholesaler', [AdminWholesalerController::class, 'addWholesaler'])->name('admin.add.wholesaler');
    Route::post('/post-wholesaler', [AdminWholesalerController::class, 'postWholesaler'])->name('admin.post.wholesaler');
    Route::get('/wholesaler-detail/{id}', [AdminWholesalerController::class, 'wholesalerDetail'])->name('admin.wholesaler.detail');
    Route::post('/wholesaler-update/{id}', [AdminWholesalerController::class, 'wholesalerUpdate'])->name('admin.wholesaler.update');

    // retailer
    Route::get('/retailer-list', [AdminRetailerController::class, 'index'])->name('admin.retailer.list');
    Route::get('/pending-retailer-list', [AdminRetailerController::class, 'pendingRetailerList'])->name('admin.pending.retailer.list');
    Route::get('/add-retailer', [AdminRetailerController::class, 'addRetailer'])->name('admin.add.retailer');
    Route::post('/post-retailer', [AdminRetailerController::class, 'postRetailer'])->name('admin.post.retailer');
    Route::get('/retailer-detail/{id}', [AdminRetailerController::class, 'retailerDetail'])->name('admin.retailer.detail');
    Route::post('/retailer-update/{id}', [AdminRetailerController::class, 'retailerUpdate'])->name('admin.retailer.update');

    // category
    Route::get('/add-category', [CategoryController::class, 'categoryPage'])->name('admin.category.page');
    Route::post('/post-category', [CategoryController::class, 'postCategory'])->name('admin.category.post');
    Route::get('/category-detail/{category_id}', [CategoryController::class, 'categoryDetail'])->name('admin.category.detail');
    Route::post('/category-update/{category_id}', [CategoryController::class, 'categoryUpdate'])->name('admin.category.update');
    Route::get('/category-list', [CategoryController::class, 'categoryList'])->name('admin.category.list');
    Route::post('/category-delete/{category_id}', [CategoryController::class, 'deleteCategory'])->name('admin.category.delete');
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


    // manage profile page
    Route::get('/profile', [WholesalerController::class, 'Profile'])->name('wholesale.profile');
    Route::post('/profile-update', [WholesalerController::class, 'profileUpdate'])->name('wholesale.profile.update');
});


// Retailer Route
Route::prefix('retailer')->middleware(['auth', 'verified', 'retailer'])->group(function () {
    Route::get('/dashboard', [RetilerController::class, 'retailerDashboard'])->name('retailer.dashboard');
    Route::get('/wholesaler-list', [RetilerController::class, 'wholesalerList'])->name('retailer.wholesaler.list');
    Route::get('/wholesaler-list/{id}', [RetilerController::class, 'wholesalerWiseProductList'])->name('retailer.wholesalerwise.productlist');

    // add product
    Route::get('/add-product/{id}', [RetilerController::class, 'addProductView'])->name('retailer.add-product-view');
    Route::post('/add-product/{id}', [RetilerController::class, 'addProduct'])->name('retailer.add-product');
    Route::get('/retailer-product', [RetilerController::class, 'retailerProduct'])->name('retailer.product');

    // order
    Route::get('/retailer-orders', [RetilerController::class, 'retailerOrder'])->name('retailer.order');

    // mange Profile
    Route::get('/profile', [RetilerController::class, 'Profile'])->name('retailer.profile');
    Route::post('/profile-update', [RetilerController::class, 'profileUpdate'])->name('retailer.profile.update');
});

Route::get('/cc', function() {
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize');

    return 'DONE'; //Return anything
});
