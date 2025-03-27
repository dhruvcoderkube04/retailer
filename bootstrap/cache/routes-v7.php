<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/sanctum/csrf-cookie' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sanctum.csrf-cookie',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/retailer-products' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WWwbFXQTHi9ICB8K',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/retailer-webinfo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KWX2606I3DbFv7Ib',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/checkout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::RAk5WVQrGZDO2mMf',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::g7DarIV1epiQJjpT',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::VIz9Ae33Bpf6V7aA',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.post.login',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/register' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.registerform',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.register',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/forget-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.forget.password',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.logout',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/wholesaler-list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.wholesaler.list',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-category-wise-products' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.get-category-wise-products',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/retailer-web-setting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.web.setting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/retailer-websetting-setup' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.web.setting.setup',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/retailer-product' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.product',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/retailer-add-product' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.add.product',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.post.product',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/retailer-update-product' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.products.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/download-stock-sample' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.download-stock-sample',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/upload-bulk-product' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.upload.bulkproduct',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/place-order' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.place-order-view',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.place-order',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/profile-update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.profile.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/abondard-page' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.abandonard.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/automation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.automation.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/automation-campaign' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.automation.campaign',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/cms-page' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.cms.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/coupan-page' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.coupon.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/add-coupon' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.coupon.add',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/delete-coupon' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.coupon.delete',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/edit-coupon' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.coupon.edit',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-coupon' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.coupon.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/setting-page' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.setting.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/retailer-setting-update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.setting.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/v3builder-page' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.v3builder.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/shipping-page' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.shipping.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/direct-shipping' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.direct.shipping',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/create-own-order' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.ownorder',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/ndr' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.ndr',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/label-setting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.labelsetting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/pick-address-list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.pickaddress.list',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/rto-address' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.rto.address',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/report-page' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.report.page',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/shipping-charges' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.shipping.charges',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/cc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vpdg0BfYwL7NvF3i',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/wholesaler/([^/]++)(?|(*:30)|/([^/]++)(*:46))|/add\\-category\\-margin/([^/]++)(*:85)|/remove\\-category\\-margin/([^/]++)/([^/]++)(*:135)|/clone\\-product/([^/]++)(?|(*:170))|/orders\\-list(?|(?:/([^/]++))?(*:209)|/action(*:224))|/storage/(.*)(*:246))/?$}sDu',
    ),
    3 => 
    array (
      30 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.view-category-margin',
          ),
          1 => 
          array (
            0 => 'wholesaler_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      46 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.edit-category-margin',
          ),
          1 => 
          array (
            0 => 'wholesaler_id',
            1 => 'margin_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      85 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.add-category-margin',
          ),
          1 => 
          array (
            0 => 'wholesaler_id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      135 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.remove-category-margin',
          ),
          1 => 
          array (
            0 => 'wholesaler_id',
            1 => 'margin_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      170 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.clone-product-view',
          ),
          1 => 
          array (
            0 => 'product_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.clone-product-store',
          ),
          1 => 
          array (
            0 => 'product_id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.clone-product-remove',
          ),
          1 => 
          array (
            0 => 'product_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      209 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.order.list',
            'type' => NULL,
          ),
          1 => 
          array (
            0 => 'type',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      224 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.order.action',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      246 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'sanctum.csrf-cookie' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'sanctum/csrf-cookie',
      'action' => 
      array (
        'uses' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'controller' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'namespace' => NULL,
        'prefix' => 'sanctum',
        'where' => 
        array (
        ),
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'sanctum.csrf-cookie',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WWwbFXQTHi9ICB8K' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/retailer-products',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\Retailer\\RetailerProductController@getRetailerProducts',
        'controller' => 'App\\Http\\Controllers\\API\\Retailer\\RetailerProductController@getRetailerProducts',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::WWwbFXQTHi9ICB8K',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KWX2606I3DbFv7Ib' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/retailer-webinfo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\Retailer\\RetailerProductController@getRetailerWebInfo',
        'controller' => 'App\\Http\\Controllers\\API\\Retailer\\RetailerProductController@getRetailerWebInfo',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::KWX2606I3DbFv7Ib',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::RAk5WVQrGZDO2mMf' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/checkout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\Retailer\\RetailerProductController@checkout',
        'controller' => 'App\\Http\\Controllers\\API\\Retailer\\RetailerProductController@checkout',
        'namespace' => NULL,
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::RAk5WVQrGZDO2mMf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::g7DarIV1epiQJjpT' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:841:"function () {
                    $exception = null;

                    try {
                        \\Illuminate\\Support\\Facades\\Event::dispatch(new \\Illuminate\\Foundation\\Events\\DiagnosingHealth);
                    } catch (\\Throwable $e) {
                        if (app()->hasDebugModeEnabled()) {
                            throw $e;
                        }

                        report($e);

                        $exception = $e->getMessage();
                    }

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'/home/zero/projects/Trendmart/RetailerTrendMart/vendor/laravel/framework/src/Illuminate/Foundation/Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000004f70000000000000000";}}',
        'as' => 'generated::g7DarIV1epiQJjpT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VIz9Ae33Bpf6V7aA' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:48:"function () { return \\redirect()->to(\'login\'); }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000004fe0000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::VIz9Ae33Bpf6V7aA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@showLoginForm',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.post.login' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@login',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@login',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.post.login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.registerform' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'register',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@showRegistrationForm',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@showRegistrationForm',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.registerform',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.register' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'register',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@register',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@register',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.register',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.forget.password' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'forget-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@forgetPassword',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@forgetPassword',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.forget.password',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@logout',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@logout',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@retailerDashboard',
        'controller' => 'App\\Http\\Controllers\\RetilerController@retailerDashboard',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.wholesaler.list' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'wholesaler-list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@wholesalerList',
        'controller' => 'App\\Http\\Controllers\\RetilerController@wholesalerList',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.wholesaler.list',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.view-category-margin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'wholesaler/{wholesaler_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@viewCategoryMargin',
        'controller' => 'App\\Http\\Controllers\\RetilerController@viewCategoryMargin',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.view-category-margin',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.get-category-wise-products' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-category-wise-products',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@getCategoryWiseProducts',
        'controller' => 'App\\Http\\Controllers\\RetilerController@getCategoryWiseProducts',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.get-category-wise-products',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.add-category-margin' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add-category-margin/{wholesaler_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@storeCategoryMargin',
        'controller' => 'App\\Http\\Controllers\\RetilerController@storeCategoryMargin',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.add-category-margin',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.edit-category-margin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'wholesaler/{wholesaler_id}/{margin_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@editCategoryMargin',
        'controller' => 'App\\Http\\Controllers\\RetilerController@editCategoryMargin',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.edit-category-margin',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.remove-category-margin' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'remove-category-margin/{wholesaler_id}/{margin_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@removeCategoryMargin',
        'controller' => 'App\\Http\\Controllers\\RetilerController@removeCategoryMargin',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.remove-category-margin',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.web.setting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'retailer-web-setting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerWebManagement@webSetting',
        'controller' => 'App\\Http\\Controllers\\RetilerWebManagement@webSetting',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.web.setting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.web.setting.setup' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'retailer-websetting-setup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerWebManagement@webSettingSetup',
        'controller' => 'App\\Http\\Controllers\\RetilerWebManagement@webSettingSetup',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.web.setting.setup',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.product' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'retailer-product',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@retailerProduct',
        'controller' => 'App\\Http\\Controllers\\RetilerController@retailerProduct',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.product',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.add.product' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'retailer-add-product',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@retailerAddProduct',
        'controller' => 'App\\Http\\Controllers\\RetilerController@retailerAddProduct',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.add.product',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.post.product' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'retailer-add-product',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@retailerPostProduct',
        'controller' => 'App\\Http\\Controllers\\RetilerController@retailerPostProduct',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.post.product',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.products.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'retailer-update-product',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@updateCloneProduct',
        'controller' => 'App\\Http\\Controllers\\RetilerController@updateCloneProduct',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.products.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.download-stock-sample' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'download-stock-sample',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@downloadStockSample',
        'controller' => 'App\\Http\\Controllers\\RetilerController@downloadStockSample',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.download-stock-sample',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.upload.bulkproduct' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-bulk-product',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@uploadBulkProduct',
        'controller' => 'App\\Http\\Controllers\\RetilerController@uploadBulkProduct',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.upload.bulkproduct',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.clone-product-view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clone-product/{product_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@cloneProductView',
        'controller' => 'App\\Http\\Controllers\\RetilerController@cloneProductView',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.clone-product-view',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.clone-product-store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clone-product/{product_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@cloneProductStore',
        'controller' => 'App\\Http\\Controllers\\RetilerController@cloneProductStore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.clone-product-store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.clone-product-remove' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'clone-product/{product_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@cloneProductRemove',
        'controller' => 'App\\Http\\Controllers\\RetilerController@cloneProductRemove',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.clone-product-remove',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.place-order-view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'place-order',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@placeOrderView',
        'controller' => 'App\\Http\\Controllers\\RetilerController@placeOrderView',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.place-order-view',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.place-order' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'place-order',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@placeOrder',
        'controller' => 'App\\Http\\Controllers\\RetilerController@placeOrder',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.place-order',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.order.list' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'orders-list/{type?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@orderList',
        'controller' => 'App\\Http\\Controllers\\RetilerController@orderList',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.order.list',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.order.action' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'orders-list/action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@orderAction',
        'controller' => 'App\\Http\\Controllers\\RetilerController@orderAction',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.order.action',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@Profile',
        'controller' => 'App\\Http\\Controllers\\RetilerController@Profile',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.profile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'profile-update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@profileUpdate',
        'controller' => 'App\\Http\\Controllers\\RetilerController@profileUpdate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.profile.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.abandonard.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'abondard-page',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\AbandonardCard@index',
        'controller' => 'App\\Http\\Controllers\\AbandonardCard@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.abandonard.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.automation.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'automation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\Automation@index',
        'controller' => 'App\\Http\\Controllers\\Automation@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.automation.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.automation.campaign' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'automation-campaign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\Automation@automationCampaign',
        'controller' => 'App\\Http\\Controllers\\Automation@automationCampaign',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.automation.campaign',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.cms.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cms-page',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\CMS@index',
        'controller' => 'App\\Http\\Controllers\\CMS@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.cms.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.coupon.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'coupan-page',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\CoupanController@index',
        'controller' => 'App\\Http\\Controllers\\CoupanController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.coupon.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.coupon.add' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add-coupon',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\CoupanController@AddCoupon',
        'controller' => 'App\\Http\\Controllers\\CoupanController@AddCoupon',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.coupon.add',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.coupon.delete' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'delete-coupon',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\CoupanController@deleteCoupon',
        'controller' => 'App\\Http\\Controllers\\CoupanController@deleteCoupon',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.coupon.delete',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.coupon.edit' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'edit-coupon',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\CoupanController@editCoupon',
        'controller' => 'App\\Http\\Controllers\\CoupanController@editCoupon',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.coupon.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.coupon.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-coupon',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\CoupanController@updateCoupon',
        'controller' => 'App\\Http\\Controllers\\CoupanController@updateCoupon',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.coupon.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.setting.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'setting-page',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\Setting@index',
        'controller' => 'App\\Http\\Controllers\\Setting@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.setting.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.setting.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'retailer-setting-update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\Setting@webSettingUpdate',
        'controller' => 'App\\Http\\Controllers\\Setting@webSettingUpdate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.setting.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.v3builder.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'v3builder-page',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\VBuilder@index',
        'controller' => 'App\\Http\\Controllers\\VBuilder@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.v3builder.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.shipping.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'shipping-page',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@index',
        'controller' => 'App\\Http\\Controllers\\ShippingController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.shipping.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.direct.shipping' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'direct-shipping',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@directShipping',
        'controller' => 'App\\Http\\Controllers\\ShippingController@directShipping',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.direct.shipping',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.ownorder' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'create-own-order',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@CreateOwnOrder',
        'controller' => 'App\\Http\\Controllers\\ShippingController@CreateOwnOrder',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.ownorder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.ndr' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ndr',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@NDR',
        'controller' => 'App\\Http\\Controllers\\ShippingController@NDR',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.ndr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.labelsetting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'label-setting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@labelSetting',
        'controller' => 'App\\Http\\Controllers\\ShippingController@labelSetting',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.labelsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.pickaddress.list' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'pick-address-list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@pickAddressList',
        'controller' => 'App\\Http\\Controllers\\ShippingController@pickAddressList',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.pickaddress.list',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.rto.address' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rto-address',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@rtoAddress',
        'controller' => 'App\\Http\\Controllers\\ShippingController@rtoAddress',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.rto.address',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.report.page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'report-page',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@reportPage',
        'controller' => 'App\\Http\\Controllers\\ShippingController@reportPage',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.report.page',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'retailer.shipping.charges' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'shipping-charges',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@shippingCharges',
        'controller' => 'App\\Http\\Controllers\\ShippingController@shippingCharges',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.shipping.charges',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vpdg0BfYwL7NvF3i' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'cc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:341:"function() {
    \\Illuminate\\Support\\Facades\\Artisan::call(\'config:cache\');
    \\Illuminate\\Support\\Facades\\Artisan::call(\'route:cache\');
    \\Illuminate\\Support\\Facades\\Artisan::call(\'cache:clear\');
    \\Illuminate\\Support\\Facades\\Artisan::call(\'view:clear\');
    \\Illuminate\\Support\\Facades\\Artisan::call(\'optimize\');

    return \'DONE\';
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000005000000000000000000";}}',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::vpdg0BfYwL7NvF3i',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:67:"/home/zero/projects/Trendmart/RetailerTrendMart/storage/app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000005350000000000000000";}}',
        'as' => 'storage.local',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
