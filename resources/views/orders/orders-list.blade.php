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
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
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
                            <div class="card-toolbar d-flex justify-content-center">
                                <ul class="nav nav-pills d-flex justify-content-center flex-wrap gap-3">
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-primary px-4 py-2 {{ request()->routeIs('retailer.order.list') && (request('type') == 'new' || request('type') == null) ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'new']) }}">
                                            New
                                            <span class="badge badge-light ms-2">{{ $count['new'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-primary px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'transfered-retailer-to-wholesaler' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'transfered-retailer-to-wholesaler']) }}">
                                            Transfered to Wholesaler
                                            <span
                                                class="badge badge-light ms-2">{{ $count['transfered_retailer_to_wholesaler'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-info px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'confirmed-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'confirmed-by-retailer']) }}">
                                            Confirmed
                                            <span
                                                class="badge badge-light ms-2">{{ $count['confirmed_by_retailer'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-success px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'ready-to-ship' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'ready-to-ship']) }}">
                                            Ready To Ship
                                            <span class="badge badge-light ms-2">{{ $count['ready_to_ship'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-success px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'delivered-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'delivered-by-retailer']) }}">
                                            Delivered
                                            <span
                                                class="badge badge-light ms-2">{{ $count['delivered_by_retailer'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-danger px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'cancelled-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'cancelled-by-retailer']) }}">
                                            Cancelled
                                            <span
                                                class="badge badge-light ms-2">{{ $count['cancelled_by_retailer'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-danger px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'cancelled-by-customer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'cancelled-by-customer']) }}">
                                            Cancelled By Customer
                                            <span
                                                class="badge badge-light ms-2">{{ $count['cancelled_by_customer'] ?? 0 }}</span>
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
                                        class="form-control form-control-solid w-250px ps-12" placeholder="Search Product"
                                        id="search_field" />
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">

                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_order_list_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center min-w-50px">NO.</th>
                                        <th class="text-center min-w-150px">ORDER DATE</th>
                                        <th class="min-w-300px">ORDER DETAIL</th>
                                        <th class="min-w-150px">MEDIA</th>
                                        <th class="min-w-300px">CUSTOMER DETAIL</th>
                                        <th class="min-w-70px">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($retailerOrders as $key => $detail)
                                        <tr>
                                            {{-- key --}}
                                            <td class="text-center">{{ $key + 1 }}</td>

                                            {{-- order date --}}
                                            <td class="text-center">
                                                {{ date('F d, Y, h:i a', strtotime($detail->created_at)) }}</td>

                                            {{-- order detail --}}
                                            <td class="">
                                                <div>
                                                    <div class="my-2">
                                                        <strong>Order Id:</strong> {{ $detail->order_id }}
                                                    </div>
                                                    <div class="my-2">
                                                        <strong>Name:</strong>
                                                        {{ $detail?->product?->name ?? ($detail?->retailerCloneProduct?->name ?? '') }}
                                                    </div>
                                                    <div class="my-2">
                                                        <strong>Quantity:</strong> Qty: {{ $detail->quantity }}
                                                        {{ $detail->size ? '| Size: ' . $detail->size : '' }}
                                                    </div>
                                                    <div class="my-2">
                                                        <strong>Amount:</strong> ₹
                                                        {{ $detail?->final_amount }}
                                                        {{-- {{ $detail?->product?->new_price ?? ($detail?->retailerCloneProduct?->new_price ?? '') }} --}}
                                                    </div>
                                                    <div class="my-2">
                                                        <strong>Payment:</strong> {{ strtoupper($detail->payment_method) }}
                                                    </div>
                                                    <div class="my-2">
                                                        <strong>Order Status:</strong>
                                                        <span
                                                            class="badge {{ $detail->status == 'approved' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ order_status($detail->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- media --}}
                                            <td>
                                                <div class="mt-2">
                                                    @php
                                                        if (!empty($detail?->product?->images)) {
                                                            $imagePath =
                                                                'https://wholesale.lghosts.com/uploads/' .
                                                                explode(',', $detail->product->images)[0];
                                                        } elseif (!empty($detail?->retailerCloneProduct?->images)) {
                                                            $imagePath =
                                                                'https://wholesale.lghosts.com/uploads/' .
                                                                explode(',', $detail->retailerCloneProduct->images)[0];
                                                        } else {
                                                            $imagePath = null;
                                                        }
                                                    @endphp

                                                    @if ($imagePath)
                                                        <img src="{{ $imagePath }}" alt="Product Image"
                                                            style="width: 100px; height: auto; border-radius: 5px;">
                                                    @endif
                                                </div>
                                            </td>
                                            {{-- <td class="">
                                                <div>
                                                    <strong>Order Id:</strong> {{ $detail->order_id }}<br>
                                                    <strong>Name:</strong>{{ $detail?->product?->name ?? ($detail?->retailerCloneProduct?->name ?? '') }}<br>
                                                    <strong>Quantity:</strong> Qty: {{ $detail->quantity }} | Size:
                                                    {{ $detail->size }}<br>
                                                    <strong>Amount:</strong> ₹
                                                    {{ $detail?->final_amount }}
                                                    {{ $detail?->product?->new_price ?? ($detail?->retailerCloneProduct?->new_price ?? '') }}<br>
                                                    <strong>Payment:</strong> {{ strtoupper($detail->payment_method) }}<br>
                                                    <strong>Checkout At:
                                                        {{ date('F d, Y, h:i a', strtotime($detail->created_at)) }}</strong><br>
                                                    <strong>Order Status:</strong>
                                                    <span
                                                        class="badge {{ $detail->status == 'approved' ? 'badge-success' : 'badge-danger' }}">
                                                        {{ order_status($detail->status) }}
                                                    </span>
                                                </div>
                                            </td> --}}

                                            {{-- customer detail --}}
                                            <td>
                                                <div class="my-2">
                                                    <strong>Name:</strong> {{ $detail->customer->firstname }}
                                                    {{ $detail->customer->lastname }}
                                                </div>
                                                <div class="my-2">
                                                    <strong>Email Id:</strong> {{ $detail->customer->email }}
                                                </div>
                                                <div class="my-2">
                                                    <strong>Address:</strong> {{ $detail->customer->address }}
                                                </div>
                                                <div class="my-2">
                                                    <strong>Pin Code:</strong> {{ $detail->customer->pincode }}
                                                </div>
                                                <div class="my-2">
                                                    <strong>City:</strong> {{ $detail->customer->city }}
                                                </div>
                                                <div class="my-2">
                                                    <strong>Mobile no:</strong> {{ $detail->customer->phone_number }}
                                                </div>
                                            </td>

                                            {{-- action --}}
                                            <td>
                                                @if ($detail->status == 'pending')
                                                    <button type="button" class="btn btn-primary btn-sm newOrderAction"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-retailer-clone-product-id="{{ $detail->retailer_clone_product_id }}"
                                                        data-order-id="{{ $detail->id }}">
                                                        Action
                                                    </button>
                                                @elseif ($detail->status == 'confirmed_by_retailer')
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm confirmedOrderAction"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-retailer-clone-product-id="{{ $detail->retailer_clone_product_id }}"
                                                        data-order-id="{{ $detail->id }}">
                                                        Action
                                                    </button>
                                                @elseif ($detail->status == 'shipped_by_retailer')
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm readyToShipOrderAction"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-retailer-clone-product-id="{{ $detail->retailer_clone_product_id }}"
                                                        data-order-id="{{ $detail->id }}">
                                                        Action
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        style="white-space: nowrap; opacity: 0.4"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-retailer-clone-product-id="{{ $detail->retailer_clone_product_id }}"
                                                        data-order-id="{{ $detail->id }}" disabled>
                                                        Action
                                                    </button>
                                                @endif
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

    <!-- New Order Modal -->
    <div class="modal fade" id="new-order-action-modal" tabindex="-1" aria-labelledby="new-order-action-modal-label"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-item-center gap-4 mt-1" id="new-order-action-modal-label">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-delivery-3 fs-1" style="color: rgb(51, 51, 51)">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        <span>Order Action</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="newOrderForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold d-none">Order Action:</label>

                            <div class="list-group">
                                <label class="list-group-item d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="confirmed_by_retailer" value="confirmed_by_retailer">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Confirm Order</span>
                                </label>

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="transfered_retailer_to_wholesaler" value="transfered_retailer_to_wholesaler">
                                    <i class="bi bi-box-arrow-right text-primary fs-5"></i>
                                    <span>Transfer to Wholesaler</span>
                                </label>

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2 text-danger">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="cancelled_by_retailer" value="cancelled_by_retailer">
                                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    <span>Cancel Order</span>
                                </label>
                            </div>
                        </div>

                        {{-- <span class="text-danger mt-5 d-block">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <span class="new-order-error">asdfa asdf asdf asd</span>
                        </span> --}}

                        <input type="hidden" name="product_id" id="product_id">
                        <input type="hidden" name="retailer_clone_product_id" id="retailer_clone_product_id">
                        <input type="hidden" name="order_id" id="order_id">
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary" for="newOrderForm">
                            <i class="bi bi-send"></i> Submit Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmed Order Modal -->
    <div class="modal fade" id="confirmed-order-action-modal" tabindex="-1"
        aria-labelledby="confirmed-order-action-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-item-center gap-4 mt-1" id="confirmed-order-action-modal-label">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-delivery-3 fs-1" style="color: rgb(51, 51, 51)">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        <span>Order Action</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="confirmedOrderForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-7">
                            <div class="list-group">
                                <label class="list-group-item d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="shipped_by_retailer" value="shipped_by_retailer">
                                    <i class="bi bi-truck text-success fs-5"></i>
                                    <span>I Want To Ship</span>
                                </label>

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="transfered_retailer_to_wholesaler" value="transfered_retailer_to_wholesaler">
                                    <i class="bi bi-box-arrow-right text-primary fs-5"></i>
                                    <span>Transfer to Wholesaler</span>
                                </label>

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2 text-danger">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="cancelled_by_retailer" value="cancelled_by_retailer">
                                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    <span>Cancel Order</span>
                                </label>
                            </div>
                        </div>

                        <!-- Pickup Location -->
                        <div class="mt-12 mx-7" id="pickupLocationContainer" style="display: none;">
                            <h5 class="fw-bold text-gray-800 mb-3">
                                <i class="bi bi-geo-alt text-primary me-2"></i> Select Pickup Location
                            </h5>
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-3">
                                    <label class="form-label fw-semibold text-gray-700">Choose a location:</label>
                                    <select name="pickup_address_id" class="form-select form-select-lg"
                                        id="pickup_address_id" data-control="select2">
                                        <option value="" disabled selected>-- Select Pickup Location --</option>
                                        @foreach ($pickupAddress as $address)
                                            <option value="{{ $address->id }}" data-address="123 Main St, City A">
                                                📍 {{ $address->first_name }} {{ $address->last_name }} -
                                                {{ $address->address }}, {{ $address->state }}, {{ $address->city }} -
                                                {{ $address->pincode }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger mt-5 pickup-address-error-section" style="display: none;">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <span class="pickup-address-error">asdfa asdf asdf asd</span>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <input type="hidden" name="product_id" class="product_id">
                        <input type="hidden" name="retailer_clone_product_id" class="retailer_clone_product_id">
                        <input type="hidden" name="order_id" class="order_id">
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Submit Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal -->
    {{-- <div class="modal fade @if ($errors->any()) show d-block @endif" id="order-action-modal" tabindex="-1"
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
    </div> --}}
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
            var table1 = $("#kt_order_list_table").DataTable();
            $("#search_field").on("keyup", function() {
                table1.search(this.value).draw();
            });

            //<-------------- START: New Order --------------->
            $(document).on('click', '.newOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_clone_product_id = $(this).attr('data-retailer-clone-product-id');
                let order_id = $(this).attr('data-order-id');
                $('.product_id').val(product_id);
                $('.retailer_clone_product_id').val(retailer_clone_product_id);
                $('.order_id').val(order_id);

                $('#new-order-action-modal').modal('show');
            });

            $(document).on('submit', '#newOrderForm', function(e) {
                e.preventDefault();

                let form = new FormData(this);
                let status = form.get("status");

                if (!status) return; // Exit if no status is selected

                let swalConfig = {
                    title: "Are you sure?",
                    text: "",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "",
                };

                switch (status) {
                    case "confirmed_by_retailer":
                        swalConfig.text = "You are about to confirm this order.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Confirm it!";
                        break;
                    case "transfered_retailer_to_wholesaler":
                        swalConfig.text = "This order will be transferred to the wholesaler.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Transfer it!";
                        break;
                    case "cancelled_by_retailer":
                        swalConfig.text = "You are about to reject this order.";
                        swalConfig.icon = "warning";
                        swalConfig.confirmButtonText = "Yes, Reject it!";
                        break;
                    default:
                        return;
                }

                Swal.fire(swalConfig).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('retailer.order.action.new-order') }}",
                            type: "POST",
                            data: form,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: "Success!",
                                        text: response.msg,
                                        icon: "success",
                                        confirmButtonText: "OK",
                                    }).then(() => {
                                        window.location.href =
                                            `{{ route('retailer.order.list', ':type') }}`
                                            .replace(
                                                ":type",
                                                response.type
                                            );
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.msg,
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong, Please try later!",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });
            //<-------------- END: New Order --------------->

            //<-------------- START: Confirmed Order --------------->
            $(document).on('change', '#confirmed-order-action-modal input[name="status"]', function() {
                const status = $(this).val();
                if (status == 'shipped_by_retailer') {
                    $('#pickupLocationContainer').show();
                } else {
                    $('#pickupLocationContainer').hide();
                }
            })

            $(document).on('click', '.confirmedOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_clone_product_id = $(this).attr('data-retailer-clone-product-id');
                let order_id = $(this).attr('data-order-id');
                $('.product_id').val(product_id);
                $('.retailer_clone_product_id').val(retailer_clone_product_id);
                $('.order_id').val(order_id);

                $('#confirmed-order-action-modal').modal('show');
            });

            $(document).on('submit', '#confirmedOrderForm', function(e) {
                e.preventDefault();

                let form = new FormData(this);
                let status = form.get("status");
                let pickup_address_id = $('#pickup_address_id').val();

                if (!pickup_address_id) {
                    $('.pickup-address-error').text('Please select pickup address');
                    $('.pickup-address-error-section').show();
                    return;
                }
                if (!status) {
                    return;
                }

                let swalConfig = {
                    title: "Are you sure?",
                    text: "",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "",
                };

                switch (status) {
                    case "shipped_by_retailer":
                        swalConfig.text = "You are about to confirm this order.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Confirm it!";
                        break;
                    case "transfered_retailer_to_wholesaler":
                        swalConfig.text = "This order will be transferred to the wholesaler.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Transfer it!";
                        break;
                    case "cancelled_by_retailer":
                        swalConfig.text = "You are about to reject this order.";
                        swalConfig.icon = "warning";
                        swalConfig.confirmButtonText = "Yes, Reject it!";
                        break;
                    default:
                        return;
                }

                Swal.fire(swalConfig).then((result) => {
                    if (result.isConfirmed) {
                        if (status == "shipped_by_retailer") {
                            const pickup_address_id = $('#pickup_address_id').val();

                            if (!pickup_address_id) {
                                $('.pickup-address-error').text('Please select pickup address');
                                $('.pickup-address-error-section').show();
                                return;
                            }
                        }

                        $.ajax({
                            url: "{{ route('retailer.order.action.confirmed-order') }}",
                            type: "POST",
                            data: form,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: "Success!",
                                        text: response.msg,
                                        icon: "success",
                                        confirmButtonText: "OK",
                                    }).then(() => {
                                        window.location.href =
                                            `{{ route('retailer.order.list', ':type') }}`
                                            .replace(
                                                ":type",
                                                response.type
                                            );
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.msg,
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong, Please try later!",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });
            //<-------------- END: Confirmed Order --------------->

            // ready-to-ship order action
            // $(document).on('click', '.readyToShipOrderAction', function() {
            //     let product_id = $(this).attr('data-product-id');
            //     let order_id = $(this).attr('data-order-id');
            //     $('#product_id').val(product_id);
            //     $('#order_id').val(order_id);

            //     $('#delivered').removeClass('d-none');
            //     $('#cancelled').removeClass('d-none');

            //     $('#delivered_by_retailer').attr('checked', true);

            //     $('#order-action-modal').modal('show');
            // });
        });
    </script>
@endsection
