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
<<<<<<< Updated upstream
            '_route' => 'generated::mcPe83l3vcnzqkgR',
=======
            '_route' => 'generated::oW7c33RrLHHGpbo3',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
            '_route' => 'generated::t7NCLKPqL0qgtdUP',
=======
            '_route' => 'generated::N9gAaJ15a3oyPPlP',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
            '_route' => 'generated::WLwPu2sGG9LH4wbd',
=======
            '_route' => 'generated::Ta5sccYidZZKObsh',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
            '_route' => 'generated::Wz85cZXmgrwChLFl',
=======
            '_route' => 'generated::sDv93qUkIzKoDV1V',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
            '_route' => 'generated::n2Bky4l5nBXWjvup',
=======
            '_route' => 'generated::21NWoTeAMJfLjhry',
>>>>>>> Stashed changes
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
        1 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.password.email',
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
      '/password/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.password.update',
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
      '/email/verify' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verification.notice',
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
      '/email/resend' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verification.resend',
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
      '/dashboard-reload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.dashboard-reload',
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
      '/prohibited-item' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.prohibited.item',
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
      '/ticket-list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.ticket.list',
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
      '/generate-ticket' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.generate.ticket',
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
      '/delete-ticket' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.ticket.delete',
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
      '/rate-calculation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.rate.calculation',
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
      '/pick-address/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.pickaddress.pickAddressStore',
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
      '/rto-address/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.rtoaddress.rtoAddressStore',
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
      '/cc' => 
      array (
        0 => 
        array (
          0 => 
          array (
<<<<<<< Updated upstream
            '_route' => 'generated::2RhTuHfCoDYAZFgx',
=======
            '_route' => 'generated::BoR9h205RTheoyWI',
>>>>>>> Stashed changes
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
      0 => '{^(?|/p(?|assword/reset/([^/]++)(*:34)|ick\\-address(?|/(?|edit/([^/]++)(*:73)|update/([^/]++)(*:95))|es/([^/]++)(*:114)))|/e(?|mail/verify/([^/]++)/([^/]++)(*:158)|dit\\-(?|coupon/([^/]++)(*:189)|ticket/([^/]++)(*:212)))|/wholesaler/([^/]++)(?|(*:245)|/([^/]++)(*:262))|/add\\-category\\-margin/([^/]++)(*:302)|/r(?|emove\\-category\\-margin/([^/]++)/([^/]++)(*:356)|to\\-address(?|/(?|edit/([^/]++)(*:395)|update/([^/]++)(*:418))|es/([^/]++)(*:438)))|/clone\\-product/([^/]++)(?|(*:475))|/orders\\-list(?|(?:/([^/]++))?(*:514)|/action(*:529))|/update\\-(?|coupon/([^/]++)(*:565)|ticket/([^/]++)(*:588))|/storage/(.*)(*:610))/?$}sDu',
    ),
    3 => 
    array (
      34 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.password.reset',
          ),
          1 => 
          array (
            0 => 'token',
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
      73 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::r0nUpzXSgERKrHuW',
          ),
          1 => 
          array (
            0 => 'id',
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
      95 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::atc4FyThVvCI5DVJ',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      114 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'pickAddresses.destroy',
          ),
          1 => 
          array (
            0 => 'id',
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
      158 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verification.verify',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'hash',
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
      189 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.coupon.edit',
          ),
          1 => 
          array (
            0 => 'id',
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
      212 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.ticket.edit',
          ),
          1 => 
          array (
            0 => 'ticket_id',
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
      245 => 
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
      262 => 
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
      302 => 
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
      356 => 
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
      395 => 
      array (
        0 => 
        array (
          0 => 
          array (
<<<<<<< Updated upstream
            '_route' => 'generated::YCkNBn774Ai1fSE6',
=======
            '_route' => 'generated::51sIcgxPcwLm0Q9B',
>>>>>>> Stashed changes
          ),
          1 => 
          array (
            0 => 'id',
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
      418 => 
      array (
        0 => 
        array (
          0 => 
          array (
<<<<<<< Updated upstream
            '_route' => 'generated::dznoANks3ALtJLh2',
=======
            '_route' => 'generated::bWT0pmKy3cLPQQg3',
>>>>>>> Stashed changes
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      438 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'rtoAddresses.destroy',
          ),
          1 => 
          array (
            0 => 'id',
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
      475 => 
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
      514 => 
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
      529 => 
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
      565 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.coupon.update',
          ),
          1 => 
          array (
            0 => 'id',
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
      588 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'retailer.ticket.update',
          ),
          1 => 
          array (
            0 => 'id',
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
<<<<<<< Updated upstream
      470 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hII38NRC6hFpydHG',
          ),
          1 => 
          array (
            0 => 'id',
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
      493 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NVzpGbonqpPbYcx4',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      513 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'pickAddresses.destroy',
          ),
          1 => 
          array (
            0 => 'id',
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
      535 => 
=======
      610 => 
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
    'generated::mcPe83l3vcnzqkgR' => 
=======
    'generated::oW7c33RrLHHGpbo3' => 
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
        'as' => 'generated::mcPe83l3vcnzqkgR',
=======
        'as' => 'generated::oW7c33RrLHHGpbo3',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
    'generated::t7NCLKPqL0qgtdUP' => 
=======
    'generated::N9gAaJ15a3oyPPlP' => 
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
        'as' => 'generated::t7NCLKPqL0qgtdUP',
=======
        'as' => 'generated::N9gAaJ15a3oyPPlP',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
    'generated::WLwPu2sGG9LH4wbd' => 
=======
    'generated::Ta5sccYidZZKObsh' => 
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
        'as' => 'generated::WLwPu2sGG9LH4wbd',
=======
        'as' => 'generated::Ta5sccYidZZKObsh',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
    'generated::Wz85cZXmgrwChLFl' => 
=======
    'generated::sDv93qUkIzKoDV1V' => 
>>>>>>> Stashed changes
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:847:"function () {
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

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'C:\\\\wamp64\\\\www\\\\New folder\\\\RetailerTrendMart\\\\vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Foundation\\\\Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
<<<<<<< Updated upstream
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000009520000000000000000";}}',
        'as' => 'generated::Wz85cZXmgrwChLFl',
=======
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000004f60000000000000000";}}',
        'as' => 'generated::sDv93qUkIzKoDV1V',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
    'generated::n2Bky4l5nBXWjvup' => 
=======
    'generated::21NWoTeAMJfLjhry' => 
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:48:"function () { return \\redirect()->to(\'login\'); }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000009590000000000000000";}}',
=======
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:48:"function () { return \\redirect()->to(\'login\'); }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"00000000000004fd0000000000000000";}}',
>>>>>>> Stashed changes
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
<<<<<<< Updated upstream
        'as' => 'generated::n2Bky4l5nBXWjvup',
=======
        'as' => 'generated::21NWoTeAMJfLjhry',
>>>>>>> Stashed changes
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
    'retailer.password.email' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'forget-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@sendResetLink',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@sendResetLink',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.password.email',
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
    'retailer.password.reset' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'password/reset/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@showResetPasswordForm',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@showResetPasswordForm',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.password.reset',
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
    'retailer.password.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'password/update',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\RetailerAuthController@resetPassword',
        'controller' => 'App\\Http\\Controllers\\RetailerAuthController@resetPassword',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.password.update',
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
    'verification.notice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email/verify',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\VerificationController@show',
        'controller' => 'App\\Http\\Controllers\\VerificationController@show',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'verification.notice',
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
    'verification.verify' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email/verify/{id}/{hash}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'signed',
        ),
        'uses' => 'App\\Http\\Controllers\\VerificationController@verify',
        'controller' => 'App\\Http\\Controllers\\VerificationController@verify',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'verification.verify',
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
    'verification.resend' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'email/resend',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'throttle:6,1',
        ),
        'uses' => 'App\\Http\\Controllers\\VerificationController@resend',
        'controller' => 'App\\Http\\Controllers\\VerificationController@resend',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'verification.resend',
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
    'retailer.dashboard-reload' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard-reload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@dashboardReload',
        'controller' => 'App\\Http\\Controllers\\RetilerController@dashboardReload',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.dashboard-reload',
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
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'edit-coupon/{id}',
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
      'uri' => 'update-coupon/{id}',
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
    'retailer.prohibited.item' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'prohibited-item',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@prohibitedItem',
        'controller' => 'App\\Http\\Controllers\\RetilerController@prohibitedItem',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.prohibited.item',
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
    'retailer.ticket.list' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'ticket-list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@ticketList',
        'controller' => 'App\\Http\\Controllers\\RetilerController@ticketList',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.ticket.list',
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
    'retailer.generate.ticket' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'generate-ticket',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@generateTicket',
        'controller' => 'App\\Http\\Controllers\\RetilerController@generateTicket',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.generate.ticket',
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
    'retailer.ticket.delete' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'delete-ticket',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@deleteTicket',
        'controller' => 'App\\Http\\Controllers\\RetilerController@deleteTicket',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.ticket.delete',
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
    'retailer.ticket.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'edit-ticket/{ticket_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@editTicket',
        'controller' => 'App\\Http\\Controllers\\RetilerController@editTicket',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.ticket.edit',
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
    'retailer.ticket.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-ticket/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@updateTicket',
        'controller' => 'App\\Http\\Controllers\\RetilerController@updateTicket',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.ticket.update',
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
    'retailer.rate.calculation' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rate-calculation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\RetilerController@ratecCalculation',
        'controller' => 'App\\Http\\Controllers\\RetilerController@ratecCalculation',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.rate.calculation',
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
    'retailer.pickaddress.pickAddressStore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'pick-address/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@pickAddressStore',
        'controller' => 'App\\Http\\Controllers\\ShippingController@pickAddressStore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.pickaddress.pickAddressStore',
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
<<<<<<< Updated upstream
    'generated::hII38NRC6hFpydHG' => 
=======
    'generated::r0nUpzXSgERKrHuW' => 
>>>>>>> Stashed changes
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'pick-address/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@pickAddressedit',
        'controller' => 'App\\Http\\Controllers\\ShippingController@pickAddressedit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
<<<<<<< Updated upstream
        'as' => 'generated::hII38NRC6hFpydHG',
=======
        'as' => 'generated::r0nUpzXSgERKrHuW',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
    'generated::NVzpGbonqpPbYcx4' => 
=======
    'generated::atc4FyThVvCI5DVJ' => 
>>>>>>> Stashed changes
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'pick-address/update/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@pickAddressupdate',
        'controller' => 'App\\Http\\Controllers\\ShippingController@pickAddressupdate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
<<<<<<< Updated upstream
        'as' => 'generated::NVzpGbonqpPbYcx4',
=======
        'as' => 'generated::atc4FyThVvCI5DVJ',
>>>>>>> Stashed changes
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
    'pickAddresses.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'pick-addresses/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@pickAddressdestroy',
        'controller' => 'App\\Http\\Controllers\\ShippingController@pickAddressdestroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'pickAddresses.destroy',
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
    'retailer.rtoaddress.rtoAddressStore' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'rto-address/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@RTOAddressStore',
        'controller' => 'App\\Http\\Controllers\\ShippingController@RTOAddressStore',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'retailer.rtoaddress.rtoAddressStore',
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
<<<<<<< Updated upstream
    'generated::YCkNBn774Ai1fSE6' => 
=======
    'generated::51sIcgxPcwLm0Q9B' => 
>>>>>>> Stashed changes
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'rto-address/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@RTOAddressedit',
        'controller' => 'App\\Http\\Controllers\\ShippingController@RTOAddressedit',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
<<<<<<< Updated upstream
        'as' => 'generated::YCkNBn774Ai1fSE6',
=======
        'as' => 'generated::51sIcgxPcwLm0Q9B',
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
    'generated::dznoANks3ALtJLh2' => 
=======
    'generated::bWT0pmKy3cLPQQg3' => 
>>>>>>> Stashed changes
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'rto-address/update/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@RTOAddressupdate',
        'controller' => 'App\\Http\\Controllers\\ShippingController@RTOAddressupdate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
<<<<<<< Updated upstream
        'as' => 'generated::dznoANks3ALtJLh2',
=======
        'as' => 'generated::bWT0pmKy3cLPQQg3',
>>>>>>> Stashed changes
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
    'rtoAddresses.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'rto-addresses/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'retailer',
        ),
        'uses' => 'App\\Http\\Controllers\\ShippingController@RTOAddressdestroy',
        'controller' => 'App\\Http\\Controllers\\ShippingController@RTOAddressdestroy',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'rtoAddresses.destroy',
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
<<<<<<< Updated upstream
    'generated::2RhTuHfCoDYAZFgx' => 
=======
    'generated::BoR9h205RTheoyWI' => 
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000095b0000000000000000";}}',
=======
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"000000000000050b0000000000000000";}}',
>>>>>>> Stashed changes
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
<<<<<<< Updated upstream
        'as' => 'generated::2RhTuHfCoDYAZFgx',
=======
        'as' => 'generated::BoR9h205RTheoyWI',
>>>>>>> Stashed changes
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
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:62:"C:\\wamp64\\www\\New folder\\RetailerTrendMart\\storage\\app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
<<<<<<< Updated upstream
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000009a00000000000000000";}}',
=======
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"000000000000054a0000000000000000";}}',
>>>>>>> Stashed changes
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
