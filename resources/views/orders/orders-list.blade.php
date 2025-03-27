@extends('layouts.base')
@section('title')
    Retailer Orders
@endsection

@php
    function order_status($value)
    {
        $statuses = [
            'pending' => 'Pending',
            'transfered_retailer_to_wholesaler' => 'Transferred To Wholesaler',
            'confirmed_by_retailer' => 'Confirmed By Retailer',
            'confirmed_by_wholesaler' => 'Confirmed By Wholesaler',
            'shipped_by_retailer' => 'Shipped By Retailer',
            'shipped_by_wholesaler' => 'Shipped By Wholesaler',
            'delivered_by_retailer' => 'Delivered By Retailer',
            'delivered_by_wholesaler' => 'Delivered By Wholesaler',
            'cancelled_by_customer' => 'Cancelled By Customer',
            'cancelled_by_retailer' => 'Cancelled By Retailer',
            'cancelled_by_wholesaler' => 'Cancelled By Wholesaler',
            'received' => 'Received',
        ];

        return $statuses[$value] ?? 'Unknown Status';
    }
@endphp

@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Your Orders</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Order List</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->

                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    @if (session('success'))
                        <div class="alert alert-success text-green-600 p-2">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger text-green-600 p-2">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="card card-flush">
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <!-- tabs (stages tabs) -->
                            <div class="card-toolbar flex-row-fluid justify-content-center gap-4 fs-6">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('retailer.order.list') && (request('type') == 'new' || request('type') == null) ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'new']) }}">
                                            New
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('retailer.order.list') && request('type') == 'transfered-retailer-to-wholesaler' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'transfered-retailer-to-wholesaler']) }}">
                                            Transfered to Wholesaler
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('retailer.order.list') && request('type') == 'confirmed-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'confirmed-by-retailer']) }}">
                                            Confirmed
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('retailer.order.list') && request('type') == 'ready-to-ship' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'ready-to-ship']) }}">
                                            Ready To Ship
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('retailer.order.list') && request('type') == 'delivered-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'delivered-by-retailer']) }}">
                                            Delivered
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('retailer.order.list') && request('type') == 'cancelled-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'cancelled-by-retailer']) }}">
                                            Cancelled
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('retailer.order.list') && request('type') == 'cancelled-by-customer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'cancelled-by-customer']) }}">
                                            Cancelled By Customer
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- search -->
                            <div class="card-title">
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-ecommerce-product-filter="search"
                                        class="form-control form-control-solid w-250px ps-12"
                                        placeholder="Search Product" />
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            {{-- <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center min-w-70px">Actions</th>
                                        <th class="text-center min-w-200px">Product</th>
                                        <th class="text-center min-w-150px">Wholesaler</th>
                                        <th class="text-center min-w-150px">Customer Name</th>
                                        <th class="text-center min-w-150px">Customer Contact</th>
                                        <th class="text-center min-w-0px"></th>
                                        <th class="text-center min-w-100px">Price
                                            <br> <span class="text-capitalize fs-9">(Per Pis)</span>
                                        </th>
                                        <th class="text-center min-w-150px">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($retailerOrders as $detail)
                                        <tr>
                                            <td class="text-center">
                                                @if ($detail->status == 'pending')
                                                    <button type="button" class="btn btn-primary btn-sm pendingOrderAction"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-order-id="{{ $detail->id }}">
                                                        Action
                                                    </button>
                                                @elseif ($detail->status == 'confirmed_by_retailer')
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm confirmedOrderAction"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-order-id="{{ $detail->id }}">
                                                        Action
                                                    </button>
                                                @elseif ($detail->status == 'shipped_by_retailer')
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm readyToShipOrderAction"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-order-id="{{ $detail->id }}">
                                                        Action
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        style="white-space: nowrap; opacity: 0.4"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-order-id="{{ $detail->id }}" disabled>
                                                        Action
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="symbol symbol-50px">
                                                        @php
                                                            $get_image =
                                                                explode(',', @$detail->product->images)[0] ?? '';
                                                        @endphp
                                                        <span class="symbol-label"
                                                            style="background-image: url('{{ 'https://wholesale.lghosts.com/uploads/' . $get_image }}');"></span>
                                                    </a>
                                                    <div class="ms-5">
                                                        <a href="#"
                                                            class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                                            data-kt-ecommerce-product-filter="product_name">{{ $detail->product->name }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="ms-5">
                                                    <a href="{{ route('retailer.view-category-margin', $detail->wholesaler->id) }}"
                                                        class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                                        data-kt-ecommerce-product-filter="product_name">{{ $detail->wholesaler->userDetail->company_name }}</a>
                                                </div>
                                            </td>
                                            <td class="text-center pe-0" data-order="22">
                                                <span class="fw-bold">{{ $detail->customer->firstname }}
                                                    {{ $detail->customer->lastname }}</span>
                                            </td>
                                            <td class="text-center pe-0" data-order="22">
                                                <span class="fw-bold">{{ $detail->customer->phone_number }}</span>
                                            </td>
                                            <td class="text-center pe-0" data-order="22">
                                                <span class="fw-bold"></span>
                                            </td>
                                            <td class="text-center pe-0" data-order="22">
                                                <div class="badge badge-light-success">{{ $detail->product->new_price }}
                                                </div>
                                            </td>
                                            <td class="text-center" data-order="Inactive">
                                                <div class="badge badge-light-danger text-wrap lh-base">
                                                    {{ order_status($detail->status) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> --}}

                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center">NO.</th>
                                        <th class="text-center">ORDER DATE</th>
                                        <th class="text-center">ORDER DETAIL</th>
                                        <th class="text-center">CUSTOMER DETAIL</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($retailerOrders as $key => $detail)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            <td class="text-center">{{ date('F d, Y, h:i a', strtotime($detail->created_at)) }}</td>
                                            <td>
                                                <div>
                                                    <strong>Order Id:</strong> {{ $detail->id }}<br>
                                                    <strong>Name:</strong> {{ $detail->product->name }}<br>
                                                    <strong>Quantity:</strong> Qty: {{ $detail->quantity }} | Size: {{ $detail->size }}<br>
                                                    <strong>Amount:</strong> ₹ {{ $detail->product->new_price }}<br>
                                                    <strong>Payment:</strong> {{ strtoupper($detail->payment_method) }}<br>
                                                    <strong>Order Status:</strong>
                                                    <span class="badge {{ $detail->status == 'approved' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ order_status($detail->status) }}
                                                    </span>
                                                </div>
                                                <div class="mt-2">
                                                    <img src="{{ asset('uploads/' . explode(',', $detail->product->images)[0]) }}"
                                                         alt="Product Image"
                                                         style="width: 100px; height: auto; border-radius: 5px;">
                                                </div>
                                            </td>
                                            <td>
                                                <strong>Name:</strong> {{ $detail->customer->firstname }} {{ $detail->customer->lastname }}<br>
                                                <strong>Address:</strong> {{ $detail->customer->address }}<br>
                                                <strong>Pin Code:</strong> {{ $detail->customer->pincode }}<br>
                                                <strong>City:</strong> {{ $detail->customer->city }}<br>
                                                <strong>Mobile no:</strong> {{ $detail->customer->phone_number }}
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
        @include('layouts.footer')
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade @if ($errors->any()) show d-block @endif" id="order-action-modal" tabindex="-1"
        aria-labelledby="order-action-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="order-action-modal-label">Order Action</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="customerForm" method="POST" action="{{ route('retailer.order.action') }}">
                    <div class="modal-body">
                        @csrf

                        <!-- Order Action Radio Buttons -->
                        <div class="mb-3">
                            <label class="form-label fw-bold d-none">Order Action:</label>
                            <div class="form-check mt-2 d-none" id="confirmed">
                                <input class="form-check-input" type="radio" name="status" id="confirmed_by_retailer"
                                    value="confirmed_by_retailer">
                                <label class="form-check-label" for="confirmed_by_retailer">Confirmed</label>
                            </div>
                            <div class="form-check mt-3 d-none" id="ready_to_ship">
                                <input class="form-check-input" type="radio" name="status" id="shipped_by_retailer"
                                    value="shipped_by_retailer">
                                <label class="form-check-label" for="shipped_by_retailer">Ready To Ship</label>
                            </div>
                            <div class="form-check mt-3 d-none" id="delivered">
                                <input class="form-check-input" type="radio" name="status" id="delivered_by_retailer"
                                    value="delivered_by_retailer">
                                <label class="form-check-label" for="delivered_by_retailer">Delivered</label>
                            </div>
                            <div class="form-check mt-3 d-none" id="shift">
                                <input class="form-check-input" type="radio" name="status"
                                    id="transfered_retailer_to_wholesaler" value="transfered_retailer_to_wholesaler">
                                <label class="form-check-label" for="transfered_retailer_to_wholesaler">Shift</label>
                            </div>
                            <div class="form-check mt-3 d-none" id="cancelled">
                                <input class="form-check-input" type="radio" name="status" id="cancelled_by_retailer"
                                    value="cancelled_by_retailer">
                                <label class="form-check-label" for="cancelled_by_retailer">Reject</label>
                            </div>

                        </div>
                        @error('status')
                            <span class="text-danger mt-4">{{ $message }}</span>
                        @enderror

                        <input type="hidden" name="product_id" id="product_id">
                        <input type="hidden" name="order_id" id="order_id">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" form="customerForm">Action</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/products.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>

    <script>
        $(document).ready(function() {
            // pending order action
            $(document).on('click', '.pendingOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let order_id = $(this).attr('data-order-id');
                $('#product_id').val(product_id);
                $('#order_id').val(order_id);

                $('#confirmed').removeClass('d-none');
                $('#cancelled').removeClass('d-none');
                $('#shift').removeClass('d-none');

                $('#confirmed_by_retailer').attr('checked', true);

                $('#order-action-modal').modal('show');
            });

            // confirm order action
            $(document).on('click', '.confirmedOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let order_id = $(this).attr('data-order-id');
                $('#product_id').val(product_id);
                $('#order_id').val(order_id);

                $('#ready_to_ship').removeClass('d-none');
                $('#shift').removeClass('d-none');
                $('#cancelled').removeClass('d-none');

                $('#shipped_by_retailer').attr('checked', true);

                $('#order-action-modal').modal('show');
            });

            // ready-to-ship order action
            $(document).on('click', '.readyToShipOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let order_id = $(this).attr('data-order-id');
                $('#product_id').val(product_id);
                $('#order_id').val(order_id);

                $('#delivered').removeClass('d-none');
                $('#cancelled').removeClass('d-none');

                $('#delivered_by_retailer').attr('checked', true);

                $('#order-action-modal').modal('show');
            });
        });
    </script>
@endsection
