@extends('layouts.base')
@section('title')
    Retailers | TechtrendMart
@endsection
@section('content')
    <style>
        body {
            background-color: #f7f7f7 !important;
        }
    </style>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">

            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container"
                    class="app-container w-100 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center px-4">

                    <div class="page-title d-flex flex-column justify-content-center flex-wrap mb-3 mb-md-0 me-0 me-md-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-2 flex-column justify-content-center my-0">
                            Welcome, {{ $user->firstname }}!
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-normal fs-6 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <span class="text-muted">We're glad to have you here.</span>
                            </li>
                        </ul>
                    </div>

                    <div
                        class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 w-100 w-sm-auto">
                        <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left"
                            class="btn btn-sm fw-bold btn-secondary d-flex align-items-center p-0 w-100 w-sm-auto"
                            data-kt-initialized="1">
                            <input class="fs-6 form-control form-control-solid text-gray-600 fw-bold bg-secondary border-0"
                                placeholder="Pick date range" id="kt_daterangepicker_4" />
                            <i class="ki-duotone ki-calendar-8 fs-2 ms-2 me-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                        </div>

                        <div class="position-relative w-100 w-sm-auto">
                            <a href="{{ route('retailer.order.list') }}"
                                class="btn btn-sm fw-bold btn-primary w-100 w-sm-auto position-relative">
                                Check New Orders
                            </a>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-8 text-white"
                                id="new_orders_badge">
                                {{ $data['new_orders_count'] ?? 0 }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>


            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container ">
                    <div class="row gy-5 gx-xl-10">
                        <!-- New Orders -->
                        <div class="col-12 col-sm-6 col-xl-3 mb-3">
                            <div class="card h-100 text-center shadow-none mb-2 bg-primary bg-opacity-50">
                                <a href="{{ route('retailer.order.list', 'new') }}">
                                    <div class="card-body d-flex flex-column align-items-start p-8 gap-5">
                                        <span class="bg-white bg-opacit-50 p-3 rounded-3">
                                            <i class="ki-duotone ki-add-files fs-3hx text-primary">
                                                <span class="path1"></span><span class="path2"></span><span
                                                    class="path3"></span>
                                            </i>
                                        </span>
                                        <div class="d-flex flex-column text-start">
                                            <span class="fw-semibold fs-4x text-dark" id="new_orders">
                                                {{ $data['new_orders_count'] ?? 0 }}
                                            </span>
                                            <span class="fw-semibold fs-2 text-dark mt-0">New Orders</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-9">
                            <div class="row gy-5 gx-xl-10">
                                <!-- Confirmed Orders -->
                                <div class="col-12 col-sm-6 col-xl-4 mb-3">
                                    <div class="card h-100 text-center shadow-none mb-2 bg-info bg-opacity-10">
                                        <a href="{{ route('retailer.order.list', 'approved-by-retailer') }}">
                                            <div class="card-body d-flex align-items-center p-8 gap-5">
                                                <span class="bg-white bg-opacity-25 p-3 rounded-3">
                                                <i class="ki-duotone ki-tablet-ok fs-2hx text-info">
                                                    <span class="path1"></span><span class="path2"></span><span
                                                        class="path3"></span>
                                                </i>
                                                </span>
                                                <div class="d-flex flex-column text-start">
                                                <span class="fw-semibold fs-2x text-dark" id="confirmed_orders">
                                                    {{ $data['confirmed_orders_count'] ?? 0 }}
                                                </span>
                                                <span class="fw-semibold fs-5 text-dark mt-0">Confirmed Orders</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Transfer Retailer To Wholesaler Orders -->
                                <div class="col-12 col-sm-6 col-xl-4 mb-3">
                                    <div class="card h-100 text-center shadow-none mb-2 bg-primary bg-opacity-10">
                                        {{-- <div> --}}
                                            <a href="{{ route('retailer.order.list', 'transferred-to-wholesaler') }}">
                                            <div class="card-body d-flex align-items-center p-8 gap-5">
                                                <span class="bg-white bg-opacity-25 p-3 rounded-3">
                                                    <i class="ki-duotone ki-exit-right fs-2hx text-primary">
                                                        <span class="path1"></span><span class="path2"></span>
                                                    </i>
                                                </span>
                                                <div class="d-flex flex-column text-start">
                                                    <span class="fw-semibold fs-2x text-dark " id="transfered_retailer_to_wholesaler">
                                                        {{ $data['transfered_retailer_to_wholesaler_orders_count'] ?? 0 }}
                                                    </span>
                                                    <span class="fw-semibold fs-5 text-dark mt-0">Transfer To Wholesaler</span>
                                                </div>
                                            </div>
                                            </a>
                                        {{-- </div> --}}
                                    </div>
                                </div>

                                <!-- Ready to Ship -->
                                <div class="col-12 col-sm-6 col-xl-4 mb-3">
                                    <div class="card h-100 text-center shadow-none mb-2 bg-success bg-opacity-10">
                                        <a href="{{ route('retailer.order.list', 'pickup') }}">
                                            <div class="card-body d-flex align-items-center p-8 gap-5">
                                                <span class="bg-white bg-opacity-25 p-3 rounded-3">
                                                    <i class="ki-duotone ki-delivery fs-2hx text-success">
                                                        <span class="path1"></span><span class="path2"></span><span
                                                            class="path3"></span>
                                                        <span class="path4"></span><span class="path5"></span>
                                                    </i>
                                                </span>
                                                <div class="d-flex flex-column text-start">
                                                    <span class="fw-semibold fs-2x text-dark" id="ready_for_ship">
                                                        {{ $data['ready_for_ship_orders_count'] ?? 0 }}
                                                    </span>
                                                    <span class="fw-semibold fs-5 text-dark mt-0">Ready For Ship</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- In Transit -->
                                <div class="col-12 col-sm-6 col-xl-4 mb-5">
                                    <div class="card h-100 text-center shadow-none mb-2 bg-warning bg-opacity-10">
                                        <a href="{{ route('retailer.order.list', 'in-transit') }}">
                                            <div class="card-body d-flex align-items-center p-8 gap-5">
                                                <span class="bg-white bg-opacity-25 p-3 rounded-3">
                                                    <i class="ki-duotone ki-delivery fs-2hx text-warning">
                                                        <span class="path1"></span><span class="path2"></span><span
                                                            class="path3"></span>
                                                        <span class="path4"></span><span class="path5"></span>
                                                    </i>
                                                </span>
                                                <div class="d-flex flex-column text-start">
                                                    <span class="fw-semibold fs-2x text-dark " id="in_transit">
                                                        {{ $data['in_transit_orders_count'] ?? 0 }}
                                                    </span>
                                                    <span class="fw-semibold fs-5 text-dark mt-0">In Transit</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Delivered -->
                                <div class="col-12 col-sm-6 col-xl-4 mb-5">
                                    <div class="card h-100 text-center shadow-none mb-2 bg-success bg-opacity-10">
                                        <a href="{{ route('retailer.order.list', 'delivered') }}">
                                            <div class="card-body d-flex align-items-center p-8 gap-5">
                                                <span class="bg-white bg-opacity-25 p-3 rounded-3">
                                                    <i class="ki-duotone ki-delivery-3 fs-2hx text-success">
                                                        <span class="path1"></span><span class="path2"></span><span
                                                            class="path3"></span>
                                                    </i>
                                                </span>
                                                <div class="d-flex flex-column text-start">
                                                    <span class="fw-semibold fs-2x text-dark" id="delivered">
                                                        {{ $data['delivered_orders_count'] ?? 0 }}
                                                    </span>
                                                    <span class="fw-semibold fs-5 text-dark mt-0">Delivered</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Cancelled -->
                                <div class="col-12 col-sm-6 col-xl-4 mb-5">
                                    <div class="card h-100 text-center shadow-none mb-2 bg-danger bg-opacity-10">
                                        <a href="{{ route('retailer.order.list', 'cancel') }}">
                                            <div class="card-body d-flex align-items-center p-8 gap-5">
                                                <span class="bg-white bg-opacity-25 p-3 rounded-3">
                                                <i class="ki-duotone ki-cross-square fs-2hx text-danger">
                                                    <span class="path1"></span><span class="path2"></span>
                                                </i>    
                                                </span>
                                                <div class="d-flex flex-column text-start">
                                                    <span class="fw-semibold fs-2x text-dark" id="cancel">
                                                        {{ $data['cancelled_orders_count'] ?? 0 }}
                                                    </span>
                                                    <span class="fw-semibold fs-5 text-dark mt-0">Cancelled</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                    <hr class="my-4 mb-6 opacity-15">
                    <div class="row gy-5 gx-xl-10">
                        <!-- Earnings Card -->
                        <div class="col-12 col-md-6 col-xl-9 col-xxl-9 mb-10">
                            <div class="card card-flush h-100">
                                @php
                                    $wholesaler_count = $data['wholesaler_product_count'] ?? 0;
                                    $retailer_count = $data['retailer_product_count'] ?? 0;
                                    $total_products = $wholesaler_count + $retailer_count;

                                    if ($total_products > 0) {
                                        $wholesaler_ratio = ($wholesaler_count * 100) / $total_products;
                                        $retailer_ratio = ($retailer_count * 100) / $total_products;
                                    } else {
                                        $wholesaler_ratio = 0;
                                        $retailer_ratio = 0;
                                    }
                                @endphp
                                <div class="card-header pt-5">
                                    <div class="card-title d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2 me-5">{{ $total_products }}</span>
                                            <span class="badge badge-light-success fs-base">
                                                {{ (int) $wholesaler_ratio }}% - {{ (int) $retailer_ratio }}%
                                            </span>
                                        </div>
                                        <span class="text-gray-700 pt-1 fw-semibold fs-6">Total Products</span>
                                    </div>
                                </div>

                                <div class="card-body pt-2 pb-4 d-flex align-items-center">
                                    <div class="d-flex flex-center me-5 pt-2" style="width: 150px; height: 150px;">
                                        <canvas id="productsChart" style="display: block; box-sizing: border-box; height: 150px; width: 150px;"></canvas>
                                    </div>

                                    <div class="d-flex flex-column w-100">
                                        <a href="{{ url('my-wholesaler-product') }}" class="text-decoration-none">
                                            <div class="d-flex fs-6 fw-semibold align-items-center">
                                                <div class="bullet w-8px h-8px rounded-2 bg-danger me-3"></div>
                                                <div class="text-gray-700 flex-grow-1 me-4">
                                                    Wholesaler's products ({{ (int) $wholesaler_ratio }}%)
                                                </div>
                                                <div class="fw-bolder text-gray-700 text-xxl-end">
                                                    {{ $data['wholesaler_product_count'] ?? 0 }}
                                                </div>
                                            </div>
                                        </a>

                                        <a href="{{ url('my-product') }}" class="text-decoration-none">
                                            <div class="d-flex fs-6 fw-semibold align-items-center my-3">
                                                <div class="bullet w-8px h-8px rounded-2 bg-primary me-3"></div>
                                                <div class="text-gray-700 flex-grow-1 me-4">
                                                    Retailer's own products ({{ (int) $retailer_ratio }}%)
                                                </div>
                                                <div class="fw-bolder text-gray-700 text-xxl-end">
                                                    {{ $data['retailer_product_count'] ?? 0 }}
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- <div class="card-body pt-2 pb-4 d-flex align-items-center">
                                        <div class="d-flex flex-center me-5 pt-2">
                                            {{-- <div id="kt_card_widget_4_chart" style="min-width: 70px; min-height: 70px">
                                            <canvas height="70" width="70"></canvas>
                                        </div> --}}
                                        <span class="alert-danger p-3 rounded-3 me-6 mb-4">
                                            <i class="ki-duotone ki-chart-simple fs-4hx  text-danger">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </span>
                                        </div>

                                        <div class="d-flex flex-column w-100">
                                            <a href="{{ url('my-wholesaler-product') }}" class="text-decoration-none">
                                                <div class="d-flex fs-6 fw-semibold align-items-center">
                                                    <div class="bullet w-8px h-6px rounded-2 bg-danger me-3"></div>
                                                    <div class="text-gray-700 flex-grow-1 me-4">
                                                        Wholesaler's products ({{ (int) $wholesaler_ratio }}%)
                                                    </div>
                                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                                        {{ $data['wholesaler_product_count'] ?? 0 }}
                                                    </div>
                                                </div>
                                            </a>

                                            <a href="{{ url('my-product') }}" class="text-decoration-none">
                                                <div class="d-flex fs-6 fw-semibold align-items-center my-3">
                                                    <div class="bullet w-8px h-6px rounded-2 bg-primary me-3"></div>
                                                    <div class="text-gray-700 flex-grow-1 me-4">
                                                        Retailer's own products ({{ (int) $retailer_ratio }}%)
                                                    </div>
                                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                                        {{ $data['retailer_product_count'] ?? 0 }}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div> -->

                            </div>
                        </div>

                        <!-- Total Sales -->
                        <div class="col-12 col-md-6 col-xl-3 mb-10">
                            <div class="d-flex h-100 flex-column">
                                <div class="card h-100 text-center mb-5">
                                    <div class="card-body d-flex align-items-center p-8 gap-5">
                                        <span class="alert-primary p-3 rounded-3">
                                            <i class="ki-duotone ki-finance-calculator fs-2hx text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                                <span class="path6"></span>
                                                <span class="path7"></span>
                                            </i>
                                        </span>
                                         <div class="d-flex flex-column align-items-start justify-content-center">
                                            <div class="d-flex align-items-start justify-content-center">
                                                <span class="fs-4 fw-semibold text-gray-700 me-1">₹</span>
                                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2" id="total_sales">
                                                    {{ $data['total_sales'] ?? 0 }}
                                                </span>
                                            </div>
                                            <span class="fw-semibold fs-5 text-gray-700 mt-2">Total Sales</span>
                                         </div>
                                    </div>
                                </div>
                                <!-- Total Earning -->
                                <div class="card h-100 text-center mb-2">
                                    <div class="card-body d-flex align-items-center p-8 gap-5">
                                        <span class="alert-primary p-3 rounded-3">
                                        <i class="ki-duotone ki-finance-calculator fs-2hx text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                            <span class="path7"></span>
                                        </i>
                                        </span>
                                        <div class="d-flex flex-column align-items-start justify-content-center">
                                            <div class="d-flex align-items-start justify-content-center">
                                                <span class="fs-4 fw-semibold text-gray-700 me-1">₹</span>
                                                <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2" id="total_earning">
                                                    {{ $data['total_earning'] ?? 0 }}
                                                </span>
                                            </div>
                                        <span class="fw-semibold fs-5 text-gray-700 mt-2">Total Earning</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Earning -->
                        {{-- <div class="col-12 col-md-6 col-xl-3 mb-10">
                            <div class="card h-100 text-center shadow-sm mb-2">
                                <div class="card-body d-flex align-items-start p-5 gap-3">
                                    <span class="alert-primary p-3 rounded-3">
                                    <i class="ki-duotone ki-finance-calculator fs-2hx text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                    </i>
                                    </span>
                                    <div class="d-flex flex-column align-items-start justify-content-center">
                                        <div class="d-flex align-items-start justify-content-center">
                                            <span class="fs-4 fw-semibold text-gray-700 me-1">₹</span>
                                            <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2" id="total_earning">
                                                {{ $data['total_earning'] ?? 0 }}
                                            </span>
                                        </div>
                                    <span class="fw-semibold fs-5 text-gray-700 mt-2">Total Earning</span>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    <div class="row gy-5 g-xl-10">
                        <div class="col-xl-12 mb-xl-12">

                            <div class="card h-md-100 p-4">
                                <div class="card-header align-items-start border-0">
                                    <div class="d-flex align-items-center gap-3">
                                        {{-- <span class="alert-primary p-3 rounded-3 mb-3"> --}}
                                            <i class="ki-duotone ki-time fs-2hx text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        {{-- </span> --}}
                                        <h3 class="fw-bold text-gray-900 mt-1">
                                            Recent Orders
                                        </h3>
                                    </div>

                                    <!-- <button
                                        class="btn btn-icon btn-color-gray-500 btn-active-color-primary justify-content-end"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                        data-kt-menu-overflow="true">

                                        <i class="ki-duotone ki-dots-square fs-1"><span class="path1"></span><span
                                                class="path2"></span><span class="path3"></span><span
                                                class="path4"></span></i>
                                    </button> -->
                                    <!-- <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px"
                                        data-kt-menu="true" style="">
                                        <div class="menu-item px-3">
                                            <div class="menu-content fs-6 text-gray-900 fw-bold px-3 py-4">Quick Actions
                                            </div>
                                        </div>

                                        <div class="separator mb-3 opacity-75"></div>

                                        <div class="menu-item px-3">
                                            <a href="{{ route('retailer.order.list', 'new') }}" class="menu-link px-3">New
                                                Orders</a>
                                        </div>

                                        <div class="menu-item px-3">
                                            <a href="{{ route('retailer.order.list', 'approved-by-retailer') }}"
                                                class="menu-link px-3">Approved Orders</a>
                                        </div>

                                        <div class="menu-item px-3">
                                            <a href="{{ route('retailer.order.list', 'pickup') }}" class="menu-link px-3">In
                                                Pickup Orders</a>
                                        </div>

                                        <div class="menu-item px-3">
                                            <a href="{{ route('retailer.order.list', 'delivered') }}"
                                                class="menu-link px-3">Delivered Orders</a>
                                        </div>
                                    </div> -->
                                </div>

                                <div class="card-body pt-2">
                                    <div class="tab-content">

                                        <div class="tab-pane fade show active" id="kt_stats_widget_2_tab_1" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-row-dashed align-middle fs-7 my-0">
                                                    <thead>
                                                        <tr
                                                            class="text-start text-gray-700 fw-bold fs-7 text-uppercase gs-0">
                                                            <th class="text-center min-w-50px"></th>
                                                            <th class="text-start min-w-150px">ITEM</th>
                                                            <th class="text-center min-w-150px">ORDER RECEIVED AT</th>
                                                            <th class="text-center min-w-80px">QUANTITY</th>
                                                            <th class="text-center min-w-80px">AMOUNT</th>
                                                            <th class="text-center min-w-300px">OTHER DETAILS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="fw-semibold text-gray-600">
                                                        @foreach ($retailerOrders as $key => $detail)
                                                            <tr>
                                                                {{-- media --}}
                                                                <td>
                                                                    <div class="mt-2">
                                                                        @php
                                                                            $defaultImage = asset(
                                                                                'assets/media/images/no_image.jpg',
                                                                            );
                                                                            $imagePath = null;

                                                                            if (
                                                                                !empty(
                                                                                $detail?->order_product_detail
                                                                                        ?->images
                                                                            )
                                                                            ) {
                                                                                $firstImage =
                                                                                    explode(
                                                                                        ',',
                                                                                        $detail->order_product_detail
                                                                                            ->images,
                                                                                    )[0] ?? null;
                                                                                $imagePath = $firstImage
                                                                                    ? Storage::disk('spaces')->url(
                                                                                        $firstImage,
                                                                                    )
                                                                                    : null;
                                                                            }
                                                                        @endphp

                                                                        @if ($imagePath)
                                                                            <img src="{{ $imagePath }}" alt="Product Image"
                                                                                style="width: 100px; height: auto; border-radius: 5px;"
                                                                                onerror="this.onerror=null;this.src='{{ $defaultImage }}';">
                                                                        @endif
                                                                    </div>
                                                                </td>

                                                                {{-- product name --}}
                                                                <td class="text-center">
                                                                    <strong>{{ $detail?->order_product_detail?->name ?? 'N/A' }}</strong>
                                                                </td>

                                                                {{-- order date --}}
                                                                <td class="text-center">
                                                                    {{ date('F d, Y, h:i a', strtotime($detail->created_at)) }}
                                                                </td>

                                                                {{-- quantity --}}
                                                                <td class="text-center">
                                                                    {{ $detail->quantity }}
                                                                </td>

                                                                {{-- amount --}}
                                                                <td class="text-center">
                                                                    ₹{{ $detail?->final_amount }}
                                                                </td>

                                                                {{-- other details --}}
                                                                <td class="text-center">
                                                                    <div>
                                                                        <div class="my-2">
                                                                            <strong>Order Id:</strong>
                                                                            {{ $detail->order_id }}
                                                                        </div>
                                                                        <div class="my-2">
                                                                            <strong>Payment:</strong>
                                                                            {{ strtoupper($detail->payment_method) }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @include('layouts.footer')
    </div>
@endsection


@section('script')
    <script>
        // var start = moment().subtract(29, "days");
        var start = moment();
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker_4").html(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));
        }

        $("#kt_daterangepicker_4").daterangepicker({
            startDate: start,
            endDate: end,
            locale: {
                format: "DD/MM/YYYY" // Set the desired format for the input field
            },
            ranges: {
                "Today": [moment(), moment()],
                "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf(
                    "month")]
            }
        }, cb);

        cb(start, end);


        $(document).on('change', '#kt_daterangepicker_4', function () {
            const fullDate = $(this).val();
            const fullDateArray = fullDate.split(" - ");
            const from = fullDateArray[0];
            const to = fullDateArray[1];

            $.ajax({
                url: '{{ route('retailer.dashboard-reload') }}',
                type: 'POST',
                data: {
                    from: from,
                    to: to,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    console.log('response', response);

                    if (response.status) {
                        $('#new_orders_badge').text(response.data.new_orders_count);
                        $('#new_orders').text(response.data.new_orders_count);
                        $('#new_orderss').text(response.data.new_orders_count);
                        $('#transfered_retailer_to_wholesaler').text(response.data.transfered_retailer_to_wholesaler_orders_count);
                        $('#confirmed_orders').text(response.data.confirmed_orders_count);
                        $('#ready_for_ship').text(response.data.ready_for_ship_orders_count);
                        $('#in_transit').text(response.data.in_transit_orders_count);
                        $('#delivered').text(response.data.delivered_orders_count);
                        $('#cancel').text(response.data.cancelled_orders_count);
                        $('#total_sales').text(response.data.total_sales);
                        $('#total_earning').text(response.data.total_earning);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.msg,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });



        // graph for product
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('productsChart').getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Wholesaler Products', 'Retailer Products'],
                    datasets: [{
                        data: [{{ (int) $wholesaler_ratio }}, {{ (int) $retailer_ratio }}],
                        backgroundColor: ['#dc3545', '#0d6efd'], // danger=red, primary=blue
                        hoverBackgroundColor: ['#bb2d3b', '#0b5ed7'],
                        borderWidth: 1
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    },
                    responsive: false,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endsection