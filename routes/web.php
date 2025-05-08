<?php

use App\Http\Controllers\AbandonardCard;
use App\Http\Controllers\Automation;
use App\Http\Controllers\CMS;
use App\Http\Controllers\Coupan;
use App\Http\Controllers\CoupanController;
use App\Http\Controllers\RetailerAccountTransactionController;
use App\Http\Controllers\RetailerAuthController;
use App\Http\Controllers\RetailerCategoryController;
use App\Http\Controllers\RetailerOrderController;
use App\Http\Controllers\RetilerController;;
use App\Http\Controllers\RetilerWebManagement;
use App\Http\Controllers\Setting;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\VBuilder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificationController;

Route::get('/', function () { return redirect()->to('login'); });

Route::controller(RetailerAuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('retailer.login');
    Route::post('login', 'login')->name('retailer.post.login');
    Route::get('register', 'showRegistrationForm')->name('retailer.registerform');
    Route::post('register', 'register')->name('retailer.register');

    Route::get('forget-password', 'forgetPassword')->name('retailer.forget.password');
    Route::post('forget-password', 'sendResetLink')->name('retailer.password.email');
    Route::get('/password/reset/{token}', 'showResetPasswordForm')->name('retailer.password.reset');
    Route::post('password/update', [RetailerAuthController::class, 'resetPassword'])->name('retailer.password.update');

    Route::post('logout', 'logout')->name('retailer.logout')->middleware('auth'); // Use retailer guard
});

    // Email Verification Routes
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

Route::middleware(['retailer'])->group(function () {
    Route::get('/dashboard', [RetilerController::class, 'retailerDashboard'])->name('retailer.dashboard');
    Route::post('/dashboard-reload', [RetilerController::class, 'dashboardReload'])->name('retailer.dashboard-reload');


    // wholesaler list
    Route::get('/wholesaler-list', [RetilerController::class, 'wholesalerList'])->name('retailer.wholesaler.list'); // wholesaler list

    // add, edit, view, delete margin
    Route::get('/wholesaler/{wholesaler_id}', [RetilerController::class, 'viewCategoryMargin'])->name('retailer.view-category-margin'); // margin add view page
    Route::get('/get-category-wise-products', [RetilerController::class, 'getCategoryWiseProducts'])->name('retailer.get-category-wise-products'); // ajax
    Route::post('/add-category-margin/{wholesaler_id}', [RetilerController::class, 'storeCategoryMargin'])->name('retailer.add-category-margin'); // POST method of add margin
    Route::post('/wholesaler-edit-margin', [RetilerController::class, 'editCategoryMargin'])->name('retailer.edit-category-margin'); // margin edit view page
    Route::delete('/remove-category-margin/{wholesaler_id}/{margin_id}', [RetilerController::class, 'removeCategoryMargin'])->name('retailer.remove-category-margin'); // DELETE method of remove margin
    Route::post('update-category-margin', [RetilerController::class, 'updateCategoryMargin'])->name('retailer.update-category-margin'); // margin edit view page


    Route::get('/retailer-web-setting', [RetilerWebManagement::class, 'webSetting'])->name('retailer.web.setting');
    Route::post('/retailer-websetting-setup', [RetilerWebManagement::class, 'webSettingSetup'])->name('retailer.web.setting.setup');

    //<-------------------- START : retailer product -------------------->
    Route::get('/retailer-product', [RetilerController::class, 'retailerProduct'])->name('retailer.product'); // product list view
    Route::get('/retailer-add-product', [RetilerController::class, 'retailerAddProduct'])->name('retailer.add.product'); // product add view
    Route::post('/retailer-store-product', [RetilerController::class, 'retailerPostProduct'])->name('retailer.post.product'); // product store
    Route::get('/retailer-edit-product/{product_id}', [RetilerController::class, 'retailerEditProduct'])->name('retailer.edit.product'); // product edit view
    Route::post('/retailer-update-product/{product_id}', [RetilerController::class, 'retailerUpdateProduct'])->name('retailer.update.products'); // product update

    Route::get('/get-sub-category-variations', [RetilerController::class, 'getSubCategoryVariations'])->name('retailer.products.get-sub-category-variations'); // ajax
    Route::get('/product-unique-slug-check', [RetilerController::class, 'productUniqueSlugCheck'])->name('retailer.products.unique-slug-check'); // ajax
    Route::get('/retailer/get-subcategories', [RetilerController::class, 'getSubCategories'])->name('retailer.getSubCategories'); // ajax
    //<-------------------- END : retailer product -------------------->

    // Bulk product upload
    Route::get('/download-stock-sample', [RetilerController::class, 'downloadStockSample'])->name('retailer.download-stock-sample'); // retailer (added, clone, own) product view page
    Route::post('/upload-bulk-product', [RetilerController::class, 'uploadBulkProduct'])->name('retailer.upload.bulkproduct'); // retailer (added, clone, own) product view page

    Route::get('/clone-product/{product_id}', [RetilerController::class, 'cloneProductView'])->name('retailer.clone-product-view'); // clone product view
    Route::post('/clone-product/{product_id}', [RetilerController::class, 'cloneProductStore'])->name('retailer.clone-product-store'); // clone product store
    Route::delete('/clone-product/{product_id}', [RetilerController::class, 'cloneProductRemove'])->name('retailer.clone-product-remove'); // clone product remove

    Route::get('/add-product/{id}', [RetilerController::class, 'addProductView'])->name('retailer.add-product-view');
    Route::post('/add-product/{id}', [RetilerController::class, 'addProduct'])->name('retailer.add-product');
    Route::get('/remove-product/{id}', [RetilerController::class, 'removeProduct'])->name('retailer.remove-product');

    // place order
    Route::get('/place-order', [RetailerOrderController::class, 'placeOrderView'])->name('retailer.place-order-view');
    Route::post('/place-order', [RetailerOrderController::class, 'placeOrder'])->name('retailer.place-order');

    // order
    Route::prefix('orders-list')->group(function () {
        Route::get('/{type?}', [RetailerOrderController::class, 'orderList'])->name('retailer.order.list');
        Route::post('/action/new-order', [RetailerOrderController::class, 'newOrderAction'])->name('retailer.order.action.new-order');
        Route::post('/action/confirmed-order', [RetailerOrderController::class, 'confirmedOrderAction'])->name('retailer.order.action.confirmed-order');
        Route::post('/action/pickup-order', [RetailerOrderController::class, 'pickupOrderAction'])->name('retailer.order.action.pickup-order');
        Route::get('/pickup-image/fetch', [RetailerOrderController::class, 'pickupImageFetch'])->name('retailer.order.pickup-image.fetch');
        Route::post('/pickup-image/upload', [RetailerOrderController::class, 'pickupImageUpload'])->name('retailer.order.pickup-image.upload');
        Route::post('/action/in-transit-order', [RetailerOrderController::class, 'inTransitOrderAction'])->name('retailer.order.action.in-transit-order');
    });

    // my-order
    Route::prefix('my-orders')->group(function () {
        Route::get('/list', [RetailerOrderController::class, 'myOrderList'])->name('retailer.my-order.list');
    });

    // mange Profile
    Route::get('/profile', [RetilerController::class, 'Profile'])->name('retailer.profile');
    Route::post('/profile-update', [RetilerController::class, 'profileUpdate'])->name('retailer.profile.update');
    Route::post('/account-info', [RetilerController::class, 'storeAccoutinfo'])->name('retailer.accountinfo');

    // abandonedcard
    Route::get('/abondard-page', [AbandonardCard::class, 'index'])->name('retailer.abandonard.index');



    // automation
    Route::get('/automation', [Automation::class, 'index'])->name('retailer.automation.index');
    Route::get('/automation-campaign', [Automation::class, 'automationCampaign'])->name('retailer.automation.campaign');

    // cms
    Route::get('/cms-page', [CMS::class, 'index'])->name('retailer.cms.index');

    // coupan
    Route::get('/coupan-page', [CoupanController::class, 'index'])->name('retailer.coupon.index');
    Route::post('/add-coupon', [CoupanController::class, 'AddCoupon'])->name('retailer.coupon.add');
    Route::post('/delete-coupon', [CoupanController::class, 'deleteCoupon'])->name('retailer.coupon.delete');
    Route::get('/edit-coupon/{id}', [CoupanController::class, 'editCoupon'])->name('retailer.coupon.edit');
    Route::post('/update-coupon/{id}', [CoupanController::class, 'updateCoupon'])->name('retailer.coupon.update');

    // setting
    Route::get('/setting-page', [Setting::class, 'index'])->name('retailer.setting.index');
    Route::post('/retailer-setting-update', [Setting::class, 'webSettingUpdate'])->name('retailer.setting.update');

    // prohibited item page
    Route::get('/prohibited-item', [RetilerController::class, 'prohibitedItem'])->name('retailer.prohibited.item');
    // Generate ticket
    Route::get('/ticket-list', [RetilerController::class, 'ticketList'])->name('retailer.ticket.list');
    Route::post('/generate-ticket', [RetilerController::class, 'generateTicket'])->name('retailer.generate.ticket');
    Route::post('/delete-ticket', [RetilerController::class, 'deleteTicket'])->name('retailer.ticket.delete');
    Route::get('/edit-ticket/{ticket_id}', [RetilerController::class, 'editTicket'])->name('retailer.ticket.edit');
    Route::post('/update-ticket/{id}', [RetilerController::class, 'updateTicket'])->name('retailer.ticket.update');

    // rate calculation
    Route::get('/rate-calculation', [RetilerController::class, 'ratecCalculation'])->name('retailer.rate.calculation');
    Route::post('/rate-calculation', [RetilerController::class, 'ratecCalculationPost'])->name('retailer.rate.calculation.post');

    // v3builder
    Route::get('/v3builder-page', [VBuilder::class, 'index'])->name('retailer.v3builder.index');

    // Shipping
    Route::get('/shipping-page', [ShippingController::class, 'index'])->name('retailer.shipping.index');
    Route::get('/direct-shipping', [ShippingController::class, 'directShipping'])->name('retailer.direct.shipping');
    Route::get('/create-own-order', [ShippingController::class, 'CreateOwnOrder'])->name('retailer.ownorder');
    Route::get('/ndr', [ShippingController::class, 'NDR'])->name('retailer.ndr');
    Route::get('/label-setting', [ShippingController::class, 'labelSetting'])->name('retailer.labelsetting');
    Route::get('/pick-address-list', [ShippingController::class, 'pickAddressList'])->name('retailer.pickaddress.list');
    Route::get('/rto-address', [ShippingController::class, 'rtoAddress'])->name('retailer.rto.address');
    Route::get('/report-page', [ShippingController::class, 'reportPage'])->name('retailer.report.page');
    Route::get('/shipping-charges', [ShippingController::class, 'shippingCharges'])->name('retailer.shipping.charges');

    // delivery check availblity
    Route::get('/pincode-serviceable', [ShippingController::class, 'pincodeServiceable'])->name('retailer.pincode.serviceable');
    Route::post('/check-availability', [ShippingController::class, 'pincodeCheckAvailability'])->name('retailer.pincode.check.availability');

    // track order
    Route::get('/track-order', [ShippingController::class, 'trackOrder'])->name('retailer.track.order');
    Route::post('/track-order', [ShippingController::class, 'trackOrderStatus'])->name('retailer.track.order.status');

    Route::post('/pick-address/store', [ShippingController::class, 'pickAddressStore'])->name('retailer.pickaddress.pickAddressStore');
    Route::get('/pick-address/edit/{id}', [ShippingController::class, 'pickAddressedit']);
    Route::put('/pick-address/update/{id}', [ShippingController::class, 'pickAddressupdate']);
    Route::delete('/pick-addresses/{id}', [ShippingController::class, 'pickAddressdestroy'])->name('pickAddresses.destroy');

    Route::post('/rto-address/store', [ShippingController::class, 'RTOAddressStore'])->name('retailer.rtoaddress.rtoAddressStore');
    Route::get('/rto-address/edit/{id}', [ShippingController::class, 'RTOAddressedit']);
    Route::put('/rto-address/update/{id}', [ShippingController::class, 'RTOAddressupdate']);
    Route::delete('/rto-addresses/{id}', [ShippingController::class, 'RTOAddressdestroy'])->name('rtoAddresses.destroy');

    // retailer catgory manage
    Route::get('/category-list', [RetailerCategoryController::class, 'categoryList'])->name('retailer.category.list');

    Route::post('/add-retailer-category', [RetailerCategoryController::class, 'addRetailerCategory'])->name('retailer.category.add-retailer-category');
    Route::get('/my-category-list', [RetailerCategoryController::class, 'myCategoryList'])->name('retailer.mycategory.list');
    Route::post('/remove-category', [RetailerCategoryController::class, 'removeCategory'])->name('retailer.remove.category');
    Route::post('/update-category-image', [RetailerCategoryController::class, 'updateCategoryImage'])->name('retailer.category-image.update');

    // category suggestion maanage
    Route::get('/category-suggestion', [RetailerCategoryController::class, 'categorySuggestion'])->name('retailer.category-suggestion');
    Route::post('/category-suggestion-create', [RetailerCategoryController::class, 'createCategorySuggestion'])->name('retailer.category-suggestion-create');
    Route::post('/category-suggestion-delete', [RetailerCategoryController::class, 'deleteCategorySuggestion'])->name('retailer.category-suggestion-delete');

    // accounts
    Route::prefix('accounts')->group(function () {
        Route::get('/transactions/success-wallet', [RetailerAccountTransactionController::class, 'indexSuccessAccountsTransactions'])->name('retailer.accounts.transactions.success-wallet');
        Route::post('/fetch-record/success-wallet', [RetailerAccountTransactionController::class, 'fetchSuccessRecord'])->name('retailer.accounts.fetch-record.success-wallet'); // ajax - datatable

        Route::get('/transactions/pending-wallet', [RetailerAccountTransactionController::class, 'indexPendingAccountsTransactions'])->name('retailer.accounts.transactions.pending-wallet');
        Route::post('/fetch-record/pending-wallet', [RetailerAccountTransactionController::class, 'fetchPendingRecord'])->name('retailer.accounts.fetch-record.pending-wallet'); // ajax - datatable


        Route::get('/transaction-info', [RetailerAccountTransactionController::class, 'transactionInfo'])->name('retailer.accounts.transaction-info'); // ajax

        // withdrawal-request
        Route::get('/withdrawal-request', [RetailerAccountTransactionController::class, 'withdrawalRequestIndex'])->name('retailer.accounts.withdrawal-request');
        Route::post('/withdrawal-request', [RetailerAccountTransactionController::class, 'withdrawalRequestStore'])->name('retailer.accounts.withdrawal-request-post'); // ajax
        Route::POST('/withdrawal-transactions/fetch-record', [RetailerAccountTransactionController::class, 'fetchRecordWithdrawalTransactions'])->name('retailer.accounts.withdrawal-transactions.fetch-record'); // ajax - datatable
    });
});

Route::get('/cc', function() {
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize');

    return 'DONE';
});
