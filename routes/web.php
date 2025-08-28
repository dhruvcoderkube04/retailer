<?php

use App\Http\Controllers\CMS;
use App\Models\WebsiteContent;
use App\Models\RetailerCategory;
use App\Http\Controllers\Setting;
use App\Http\Controllers\VBuilder;
use App\Http\Controllers\Automation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AbandonardCard;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\CouponController;


use App\Http\Controllers\TicketController;
use App\Http\Controllers\RetilerController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\RetilerWebManagement;
use App\Http\Controllers\RetailerAuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\RetailerOrderController;
use App\Http\Controllers\RetailerCategoryController;
use App\Http\Controllers\OrderNotificationController;
use App\Http\Controllers\RetailerAccountTransactionController;

Route::get('/', function () {
    return redirect()->to('login');
});

Route::post('/lorrigo-webhook', [ShippingController::class, 'lorrigoWebhook']);

Route::controller(RetailerAuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('retailer.login')->middleware('user.active');
    Route::post('login', 'login')->name('retailer.post.login');
    Route::get('register', 'showRegistrationForm')->name('retailer.registerform');
    Route::post('register', 'register')->name('retailer.register');

    Route::get('forget-password', 'forgetPassword')->name('retailer.forget.password');
    Route::post('forget-password', 'sendResetLink')->name('retailer.password.email');
    Route::get('/retailer/reset-password/{token}', 'showResetPasswordForm')->name('retailer.password.reset');
    Route::post('/retailer/password/update', [RetailerAuthController::class, 'resetPassword'])->name('retailer.password.update');

    // term-conditions
    Route::get('/terms-and-conditions', function () {
        return view('term-conditions.index');
    })->name('terms-and-conditions');

    Route::post('logout', 'logout')->name('retailer.logout')->middleware('auth'); // Use retailer guard

});

// Email Verification Routes
Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

Route::middleware(['retailer', 'user.active'])->group(function () {
    Route::get('/dashboard', [RetilerController::class, 'retailerDashboard'])->name('retailer.dashboard');
    Route::post('/dashboard-reload', [RetilerController::class, 'dashboardReload'])->name('retailer.dashboard-reload');


    // wholesaler list
    Route::get('/wholesaler-list', [RetilerController::class, 'wholesalerList'])->name('retailer.wholesaler.list'); // wholesaler list
    Route::post('/wholesaler/fetch-record', [RetilerController::class, 'wholesalerFetchRecord'])->name('retailer.wholesaler.fetch-record'); // ajax - datatable
    Route::post('/wholesaler/request-access', [RetilerController::class, 'requestAccess'])->name('wholesaler.request.access');


    // subscribed category
    Route::prefix('subscribed-category')->group(function () {
        Route::get('/', [RetilerController::class, 'subscribedCategoryIndex'])->name('retailer.subscribed-category.index'); // subscribed category list
        Route::post('/subscribed-category/fetch-record', [RetilerController::class, 'subscribedCategoryFetchRecord'])->name('retailer.subscribed-category.fetch-record'); // ajax - datatable
    });

    // add, edit, view, delete margin
    Route::get('/wholesaler/{wholesaler_id}', [RetilerController::class, 'viewCategoryMargin'])->name('retailer.view-category-margin'); // margin add view page // encrypted
    Route::get('/get-category-wise-products', [RetilerController::class, 'getCategoryWiseProducts'])->name('retailer.get-category-wise-products'); // ajax
    Route::post('/add-category-margin/{wholesaler_id}', [RetilerController::class, 'storeCategoryMargin'])->name('retailer.add-category-margin'); // POST method of add margin // encrypted
    Route::post('/wholesaler-edit-margin', [RetilerController::class, 'editCategoryMargin'])->name('retailer.edit-category-margin'); // margin edit view page
    Route::delete('/remove-category-margin/{wholesaler_id}/{margin_id}', [RetilerController::class, 'removeCategoryMargin'])->name('retailer.remove-category-margin'); // DELETE method of remove margin
    Route::post('update-category-margin', [RetilerController::class, 'updateCategoryMargin'])->name('retailer.update-category-margin'); // margin edit view page
    Route::post('/retailer/subscribed-category/bulk-delete', [RetilerController::class, 'bulkDelete'])->name('retailer.subscribed-category.bulk-delete');



    Route::get('/retailer-web-setting', [RetilerWebManagement::class, 'webSetting'])->name('retailer.web.setting');
    Route::post('/retailer-websetting-setup', [RetilerWebManagement::class, 'webSettingSetup'])->name('retailer.web.setting.setup');

    //<-------------------- START : retailer product -------------------->
    Route::get('/retailer-product', [RetilerController::class, 'retailerProduct'])->name('retailer.product'); // product list view

    Route::get('/my-product', [RetilerController::class, 'myProduct'])->name('retailer.my.product'); // product list view
    Route::get('/my-wholesaler-product', [RetilerController::class, 'myWholesalerProduct'])->name('retailer.my.wholesaler.product'); // product list view


    Route::post('/wholesalers-product/fetch-record', [RetilerController::class, 'fetchRecordWholesalersProduct'])->name('retailer.wholesalers-product.fetch-record'); // AJAX : datatable - wholesaler's product
    Route::get('/my-wholesaler-product/edit/{product_id}', [RetilerController::class, 'editMyWholesalerProduct'])->name('retailer.my.wholesaler.product.edit');
    Route::post('/my-wholesaler-product/update/{product_id}', [RetilerController::class, 'updateMyWholesalerProduct'])->name('retailer.my.wholesaler.product.update');
    Route::delete('/my-wholesaler-product/remove', [RetilerController::class, 'removeMyWholesalerProduct'])->name('retailer.my.wholesaler.product.remove');
    Route::post('/my-wholesaler-product/change-status', [RetilerController::class, 'changeStatusMyWholesalerProduct'])->name('retailer.my.wholesaler.product.change-status');

    Route::post('/retailer-clone-available-product/fetch-record', [RetilerController::class, 'fetchRecordRetailerCloneAvailableProduct'])->name('retailer.retailer-clone-available-product.fetch-record'); // AJAX : datatable - retailer's clone/own available product
    Route::post('/retailer-clone-unavailable-product/fetch-record', [RetilerController::class, 'fetchRecordRetailerCloneUnavailableProduct'])->name('retailer.retailer-clone-unavailable-product.fetch-record'); // AJAX : datatable - retailer's clone/own unavailable product
    Route::post('/retailer-clone-product/change-status', [RetilerController::class, 'changeProductStatus'])->name('retailer.retailer-clone-product.change-status');

    Route::get('/retailer-add-product', [RetilerController::class, 'retailerAddProduct'])->name('retailer.add.product'); // product add view
    Route::post('/retailer-store-product', [RetilerController::class, 'retailerPostProduct'])->name('retailer.post.product'); // product store
    Route::get('/retailer-edit-product/{product_id}', [RetilerController::class, 'retailerEditProduct'])->name('retailer.edit.product'); // product edit view // encrypted
    Route::get('/retailer-details-product/{product_id}', [RetilerController::class, 'retailerDetailsProduct'])->name('retailer.details.product'); // product edit view // encrypted
    Route::post('/retailer-update-product/{product_id}', [RetilerController::class, 'retailerUpdateProduct'])->name('retailer.update.products'); // product update // encrypted

    Route::get('/get-sub-category-variations', [RetilerController::class, 'getSubCategoryVariations'])->name('retailer.products.get-sub-category-variations'); // ajax
    Route::get('/product-unique-slug-check', [RetilerController::class, 'productUniqueSlugCheck'])->name('retailer.products.unique-slug-check'); // ajax
    Route::get('/retailer/get-subcategories', [RetilerController::class, 'getSubCategories'])->name('retailer.getSubCategories'); // ajax
    //<-------------------- END : retailer product -------------------->

    // Bulk product upload
    Route::get('/download-stock-sample/with-variations', [RetilerController::class, 'downloadStockSampleWithVariations'])->name('retailer.download-stock-sample-with-variations'); // retailer (added, clone, own) product view page
    Route::get('/download-stock-sample/without-variations', [RetilerController::class, 'downloadStockSampleWithoutVariations'])->name('retailer.download-stock-sample-without-variations'); // retailer (added, clone, own) product view page
    Route::post('/upload-bulk-product', [RetilerController::class, 'uploadBulkProduct'])->name('retailer.upload.bulkproduct'); // retailer (added, clone, own) product view page

    Route::get('/clone-product/{product_id}', [RetilerController::class, 'cloneProductView'])->name('retailer.clone-product-view'); // clone product view // encrypted
    Route::post('/clone-product/{product_id}', [RetilerController::class, 'cloneProductStore'])->name('retailer.clone-product-store'); // clone product store // encrypted
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
        Route::post('/order-list/fetch-record', [RetailerOrderController::class, 'fetchRecordOrderList'])->name('retailer.order-list.fetch-record'); // AJAX : datatable
        Route::post('/action/new-order', [RetailerOrderController::class, 'newOrderAction'])->name('retailer.order.action.new-order');
        Route::post('/action/confirmed-order', [RetailerOrderController::class, 'confirmedOrderAction'])->name('retailer.order.action.confirmed-order');
        Route::post('/action/pickup-order', [RetailerOrderController::class, 'pickupOrderAction'])->name('retailer.order.action.pickup-order');
        Route::get('/pickup-image/fetch', [RetailerOrderController::class, 'pickupImageFetch'])->name('retailer.order.pickup-image.fetch');
        Route::post('/pickup-image/upload', [RetailerOrderController::class, 'pickupImageUpload'])->name('retailer.order.pickup-image.upload');
        Route::post('/action/in-transit-order', [RetailerOrderController::class, 'inTransitOrderAction'])->name('retailer.order.action.in-transit-order');
        Route::post('/action/cancel-order', [RetailerOrderController::class, 'cancelOrderAction'])->name('retailer.order.action.cancel-order');
    });

    // my-order
    Route::prefix('my-orders')->group(function () {
        Route::get('/list', [RetailerOrderController::class, 'myOrderList'])->name('retailer.my-order.list');
        Route::post('/fetch-record', [RetailerOrderController::class, 'fetchmyOrderList'])->name('retailer.my-order.fetch-record');
    });

    // ndr
    Route::post('/ndr-reattempt', [RetailerOrderController::class, 'reattemptNdr'])->name('retailer.ndr.reattempt');

    // mange Profile
    Route::get('/profile/details', [RetilerController::class, 'Profile'])->name('retailer.profile.details');
    Route::get('/profile/bank-details', [RetilerController::class, 'Profile'])->name('retailer.profile.bank-details');

    Route::post('/profile-update', [RetilerController::class, 'profileUpdate'])->name('retailer.profile.update');
    Route::post('/account-info/save', [RetilerController::class, 'storeAccoutinfo'])->name('retailer.accountinfo.save');
    Route::post('/account-info/edit', [RetilerController::class, 'editAccountInfo'])->name('retailer.accountinfo.edit');
    Route::post('/retailer/bank-details-verify', [RetilerController::class, 'verifyBankDetailsCode'])->name('retailer.bank-details.verify');

    // abandonedcard
    Route::get('/abondard-page', [AbandonardCard::class, 'index'])->name('retailer.abandonard.index');

    // automation
    Route::get('/automation', [Automation::class, 'index'])->name('retailer.automation.index');
    Route::get('/automation-campaign', [Automation::class, 'automationCampaign'])->name('retailer.automation.campaign');

    // cms
    Route::get('/cms-page', [CMS::class, 'index'])->name('retailer.cms.index');

    // coupan
    Route::get('/coupon-page', [CouponController::class, 'index'])->name('retailer.coupon.index');
    Route::post('/coupons/fetch', [CouponController::class, 'fetchCouponsRecord'])->name('coupons.fetch');
    Route::post('/add-coupon', [CouponController::class, 'addCoupon'])->name('retailer.coupon.add');
    Route::post('/delete-coupon', [CouponController::class, 'deleteCoupon'])->name('retailer.coupon.delete');
    Route::get('/edit-coupon/{id}', [CouponController::class, 'editCoupon'])->name('retailer.coupon.edit');
    Route::post('/update-coupon/{id}', [CouponController::class, 'updateCoupon'])->name('retailer.coupon.update');

    // setting
    Route::get('/setting-page', [Setting::class, 'index'])->name('retailer.setting.index');
    Route::post('/retailer-setting-update', [Setting::class, 'webSettingUpdate'])->name('retailer.setting.update');

    // prohibited item page
    Route::get('/prohibited-item', [RetilerController::class, 'prohibitedItem'])->name('retailer.prohibited.item');
    // Generate ticket
    Route::get('/ticket-list', [TicketController::class, 'ticketList'])->name('retailer.ticket.list');
    Route::get('/create-ticket', [TicketController::class, 'createTicket'])->name('retailer.create.ticket');
    Route::post('/fetch-ticket-list', [TicketController::class, 'FetchticketList'])->name('fetch.retailer.ticket.list');
    Route::post('/generate-ticket', [TicketController::class, 'generateTicket'])->name('retailer.generate.ticket');
    Route::get('/ticket-detail/{ticket_id}', [TicketController::class, 'ticketDetail'])->name('retailer.ticket.details');
    Route::post('/ticket/{ticket_id}/update-status', [TicketController::class, 'updateTicketStatus'])->name('retailer.ticket.status.update');
    // Route::post('/delete-ticket', [TicketController::class, 'deleteTicket'])->name('retailer.ticket.delete');
    // Route::get('/edit-ticket/{ticket_id}', [TicketController::class, 'editTicket'])->name('retailer.ticket.edit');
    // Route::post('/update-ticket/{id}', [TicketController::class, 'updateTicket'])->name('retailer.ticket.update');


    // Enquiry
    Route::get('/website-enquiry', [RetailerCategoryController::class, 'websiteEnquiryList'])->name('retailer.website.enquiry.list');
    Route::post('/website-enquiry/fetch-record', [RetailerCategoryController::class, 'websiteEnquiryListFetchRecord'])->name('retailer.website-enquiry.fetch-record');

    // rate calculation
    Route::get('/rate-calculation', [RetilerController::class, 'ratecCalculation'])->name('retailer.rate.calculation');
    Route::post('/rate-calculation', [RetilerController::class, 'ratecCalculationPost'])->name('retailer.rate.calculation.post');

    // v3builder
    Route::get('/v3builder-page', [VBuilder::class, 'index'])->name('retailer.v3builder.index');

    // Shipping
    Route::get('/shipping-page', [ShippingController::class, 'index'])->name('retailer.shipping.index');
    Route::get('/create-own-order', [ShippingController::class, 'CreateOwnOrder'])->name('retailer.ownorder');
    Route::get('/ndr', [ShippingController::class, 'NDR'])->name('retailer.ndr');
    Route::get('/label-setting', [ShippingController::class, 'labelSetting'])->name('retailer.labelsetting');
    Route::get('/pick-address-list', [ShippingController::class, 'pickAddressList'])->name('retailer.pickaddress.list');
    Route::get('/rto-address', [ShippingController::class, 'rtoAddress'])->name('retailer.rto.address');
    Route::get('/report-page', [ShippingController::class, 'reportPage'])->name('retailer.report.page');
    Route::get('/shipping-charges', [ShippingController::class, 'shippingCharges'])->name('retailer.shipping.charges');

    // direct shipping
    Route::get('/direct-shipping', [ShippingController::class, 'directShipping'])->name('retailer.direct.shipping');
    Route::post('/get-customer-data', [ShippingController::class, 'getCustomerRecrodAccrodingOrder'])->name('retailer.getcustomer.data');
    Route::post('/customer-data-store', [ShippingController::class, 'storeCustomer'])->name('retailer.customerdata.store');
    Route::post('/direct-shipping-place-order', [ShippingController::class, 'directShippingPlaceOrder'])->name('retailer.directshipping.place.order');

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
    Route::post('/my-category-list/fetch-record', [RetailerCategoryController::class, 'myCategoryListFetchRecord'])->name('retailer.my-category-list.fetch-record');
    Route::post('/remove-category', [RetailerCategoryController::class, 'removeCategory'])->name('retailer.remove.category');
    Route::post('/update-category-image', [RetailerCategoryController::class, 'updateCategoryImage'])->name('retailer.category-image.update');
    Route::post('/retailer/category/save-selected-categories', [RetailerCategoryController::class, 'saveSelectedCategories'])->name('retailer.category.save-selected-categories');


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
        Route::post('/withdrawal-transactions/verify-wholesaler-email', [RetailerAccountTransactionController::class, 'verifyWholesalerEmail'])->name('retailer.accounts.withdrawal-transactions.verify-wholesaler-email'); // ajax - datatable
    });

    // themes
    Route::prefix('themes')->group(function () {
        Route::get('/', [ThemeController::class, 'indexThemes'])->name('retailer.themes.index');
        Route::post('/active', [ThemeController::class, 'activeTheme'])->name('retailer.themes.active');
    });

    // customers
    Route::prefix('customers')->group(function () {
        Route::get('/', [RetilerController::class, 'indexCustomers'])->name('retailer.customers.index');
        Route::post('/fetch-record', [RetilerController::class, 'fetchRecordsCustomers'])->name('retailer.customers.fetch-record');
    });

    // notification
    Route::get('/notifications', [OrderNotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::get('/notifications-count', [OrderNotificationController::class, 'getNotificationsCount'])->name('notifications.count');

    Route::prefix('accounting')->group(function () {
        Route::get('/finance-tracking', [AccountingController::class, 'financeTracking'])->name('retailer.finance-tracking.index');
        Route::post('/fetch-record', [AccountingController::class, 'getFinanceTracking'])->name('retailer.finance-tracking.fetch-record');
        Route::get('/finance-tracking/export-csv', [AccountingController::class, 'exportCsv'])->name('retailer.finance-tracking.export-csv');
    });

    Route::prefix('website-content')->group(function (){
        Route::prefix('/home')->group(function(){
            Route::get('/create', [WebsiteController::class, 'createHomeContent'])->name('retailer.website-content.create');
            Route::post('/store', [WebsiteController::class, 'storeHomeContent'])->name('retailer.website-content.store');
            Route::post('/update/{id}', [WebsiteController::class, 'updateHomeContent'])->name('retailer.website-content.update');
        });
        Route::prefix('/about-us')->group(function(){
            Route::get('/create', [WebsiteController::class, 'createAboutContent'])->name('retailer.website-content.aboutus.create');
            Route::post('/store', [WebsiteController::class, 'storeAboutContent'])->name('retailer.website-content.aboutus.store');
            Route::post('/update/{id}', [WebsiteController::class, 'updateAboutContent'])->name('retailer.website-content.aboutus.update');
        });
        Route::prefix('/contact-us')->group(function(){
            Route::get('/create', [WebsiteController::class, 'createContactContent'])->name('retailer.website-content.contactus.create');
            Route::post('/store', [WebsiteController::class, 'storeContactContent'])->name('retailer.website-content.contactus.store');
            Route::put('/update/{id}', [WebsiteController::class, 'updateContactContent'])->name('retailer.website-content.contactus.update');
        });
    });
});
// autologin
Route::get('/auto-login/{token}', [AdminAuthController::class, 'loginWithToken']);

Route::get('/cc', function () {
    Artisan::call('config:clear');
    Artisan::call('route:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize');
    return 'DONE';
});

Route::get('/run-queue/{key}', function ($key) {
    if ($key !== 'retailer') { // secret key is retailer
        abort(403, 'Unauthorized');
    }

    Artisan::call('queue:work --stop-when-empty');
    return 'Queue executed.';
});
