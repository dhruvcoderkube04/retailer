<?php

use App\Http\Controllers\RetailerAuthController;
use App\Http\Controllers\RetilerController;;
use App\Http\Controllers\RetilerWebManagement;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return redirect()->to('login'); });

Route::controller(RetailerAuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('retailer.login');
    Route::post('login', 'login')->name('retailer.post.login');
    Route::get('register', 'showRegistrationForm')->name('retailer.registerform');
    Route::post('register', 'register')->name('retailer.register');
    Route::get('forget-password', 'forgetPassword')->name('retailer.forget.password');
    Route::post('logout', 'logout')->name('retailer.logout')->middleware('auth'); // Use retailer guard
});

Route::middleware(['auth','retailer'])->group(function () {
    Route::get('/dashboard', [RetilerController::class, 'retailerDashboard'])->name('retailer.dashboard');

    // wholesaler list
    Route::get('/wholesaler-list', [RetilerController::class, 'wholesalerList'])->name('retailer.wholesaler.list'); // wholesaler list

    // add, edit, view, delete margin
    Route::get('/wholesaler/{wholesaler_id}', [RetilerController::class, 'viewCategoryMargin'])->name('retailer.view-category-margin'); // margin add view page
    Route::get('/get-category-wise-products', [RetilerController::class, 'getCategoryWiseProducts'])->name('retailer.get-category-wise-products'); // ajax
    Route::post('/add-category-margin/{wholesaler_id}', [RetilerController::class, 'storeCategoryMargin'])->name('retailer.add-category-margin'); // POST method of add margin
    Route::get('/wholesaler/{wholesaler_id}/{margin_id}', [RetilerController::class, 'editCategoryMargin'])->name('retailer.edit-category-margin'); // margin edit view page
    Route::delete('/remove-category-margin/{wholesaler_id}/{margin_id}', [RetilerController::class, 'removeCategoryMargin'])->name('retailer.remove-category-margin'); // DELETE method of remove margin


    Route::get('/retailer-web-setting', [RetilerWebManagement::class, 'webSetting'])->name('retailer.web.setting');
    Route::post('/retailer-websetting-setup', [RetilerWebManagement::class, 'webSettingSetup'])->name('retailer.web.setting.setup');

    // retailer product
    Route::get('/retailer-product', [RetilerController::class, 'retailerProduct'])->name('retailer.product'); // retailer (added, clone, own) product view page

    Route::get('/retailer-add-product', [RetilerController::class, 'retailerAddProduct'])->name('retailer.add.product'); // retailer (added, clone, own) product view page
    Route::post('/retailer-add-product', [RetilerController::class, 'retailerPostProduct'])->name('retailer.post.product'); // retailer (added, clone, own) product view page

    Route::get('/clone-product/{product_id}', [RetilerController::class, 'cloneProductView'])->name('retailer.clone-product-view'); // clone product view
    Route::post('/clone-product/{product_id}', [RetilerController::class, 'cloneProductStore'])->name('retailer.clone-product-store'); // clone product store
    Route::delete('/clone-product/{product_id}', [RetilerController::class, 'cloneProductRemove'])->name('retailer.clone-product-remove'); // clone product remove

    // Route::get('/add-product/{id}', [RetilerController::class, 'addProductView'])->name('retailer.add-product-view');
    // Route::post('/add-product/{id}', [RetilerController::class, 'addProduct'])->name('retailer.add-product');
    // Route::get('/remove-product/{id}', [RetilerController::class, 'removeProduct'])->name('retailer.remove-product');

    // place order
    Route::get('/place-order', [RetilerController::class, 'placeOrderView'])->name('retailer.place-order-view');
    Route::post('/place-order', [RetilerController::class, 'placeOrder'])->name('retailer.place-order');

    // order
    Route::get('/orders-list/{type?}', [RetilerController::class, 'orderList'])->name('retailer.order.list');
    Route::post('/orders-list/action', [RetilerController::class, 'orderAction'])->name('retailer.order.action');

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

    return 'DONE';
});
