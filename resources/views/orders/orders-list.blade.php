@extends('layouts.base')
@section('title')
    Retailer's Order List | TrendMart
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
                        <div class="card-header d-flex flex-wrap flex-md-nowrap align-items-center justify-content-between py-5 gap-3">
                            <!-- Tabs -->
                            <div class="card-toolbar flex-grow-1">
                                <ul class="nav nav-pills d-flex flex-nowrap overflow-auto gap-2">
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-primary px-4 py-2 {{ request()->routeIs('retailer.order.list') && (request('type') == 'new' || request('type') == null) ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'new']) }}">
                                            New
                                            <span class="badge badge-light ms-2">{{ $count['new'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-info px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'confirmed-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'confirmed-by-retailer']) }}">
                                            Confirmed <span class="badge badge-light ms-2">
                                                {{ $count['confirmed_by_retailer'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-primary px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'transfered-retailer-to-wholesaler' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'transfered-retailer-to-wholesaler']) }}">
                                            Transfered to Wholesaler
                                            <span class="badge badge-light ms-2">{{ $count['transfered_retailer_to_wholesaler'] ?? 0 }}</span>
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
                                            <span class="badge badge-light ms-2">{{ $count['delivered_by_retailer'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-danger px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'cancelled-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'cancelled-by-retailer']) }}">
                                            Cancelled
                                            <span class="badge badge-light ms-2">{{ $count['cancelled_by_retailer'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-danger px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'cancelled-by-customer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'cancelled-by-customer']) }}">
                                            Cancelled By Customer <span class="badge badge-light ms-2">{{ $count['cancelled_by_customer'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-light-danger px-4 py-2 {{ request()->routeIs('retailer.order.list') && request('type') == 'inactive' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'inactive']) }}">
                                            Inactive <span class="badge badge-light ms-2">{{ @$count['inactive'] ?? 0 }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Search -->
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
                                                    <div class="my-2">
                                                        <strong>Tracking Id:</strong> {{ @$detail->tracking_number }} <br/>
                                                        <strong>API Oroder Id:</strong> {{ @$detail->api_order_id }}
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- media --}}
                                            <td>
                                                <div class="mt-2">
                                                    @php
                                                        if (!empty($detail?->product?->images)) {
                                                            $imagePath = explode(',', $detail->product->images)[0];
                                                        } elseif (!empty($detail?->retailerCloneProduct?->images)) {
                                                            $imagePath = explode(',', $detail->retailerCloneProduct->images)[0];
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
                                                        data-order-id="{{ $detail->id }}"
                                                        data-c-order-id="{{$detail->order_id}}">
                                                        Action
                                                    </button>
                                                @elseif ($detail->status == 'confirmed_by_retailer')
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm confirmedOrderAction"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-retailer-clone-product-id="{{ $detail->retailer_clone_product_id }}"
                                                        data-order-id="{{ $detail->id }}"
                                                        data-c-order-id="{{$detail->order_id}}">
                                                        Action
                                                    </button>
                                                @elseif ($detail->status == 'shipped_by_retailer')
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm readyToShipOrderAction"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-retailer-clone-product-id="{{ $detail->retailer_clone_product_id }}"
                                                        data-order-id="{{ $detail->id }}"
                                                        data-c-order-id="{{$detail->order_id}}">
                                                        Action
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        style="white-space: nowrap; opacity: 0.4"
                                                        data-product-id="{{ $detail->product_id }}"
                                                        data-retailer-clone-product-id="{{ $detail->retailer_clone_product_id }}"
                                                        data-order-id="{{ $detail->id }}"
                                                        data-c-order-id="{{$detail->order_id}}" disabled>
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

                                {{-- <label class="list-group-item d-flex align-items-center gap-3 mt-2">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="transfered_retailer_to_wholesaler" value="transfered_retailer_to_wholesaler">
                                    <i class="bi bi-box-arrow-right text-primary fs-5"></i>
                                    <span>Transfer to Wholesaler</span>
                                </label> --}}

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2 text-danger">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="cancelled_by_retailer" value="cancelled_by_retailer">
                                    <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    <span>Cancel Order</span>
                                </label>
                            </div>
                        </div>

                        {{-- Reject Reason Select --}}
                        <div class="mt-12 mx-7 rejectReasonSelectContainer" style="display: none;">
                            <h5 class="fw-bold text-gray-800 mb-3">
                                <i class="bi bi-journal-x text-primary me-2"></i> Select Reject Reason
                            </h5>
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-3">
                                    <label for="rejectReasonSelectNew" class="form-label fw-semibold text-gray-700">Choose
                                        a reject reason:</label>
                                    <select name="reject_reason_select_new"
                                        class="form-select form-select-lg reject_reason_select_new"
                                        id="rejectReasonSelectNew" data-control="select2">
                                        <option value="" disabled selected>-- Select Reason --</option>
                                        <option value="Out of Stock">Out of Stock</option>
                                        <option value="Pricing Issue">Pricing Issue</option>
                                        <option value="Customer Request">Customer Requested Cancellation</option>
                                        <option value="Payment Issue">Payment Not Received</option>
                                        <option value="Shipping Restriction">Cannot Deliver to Customer's Location</option>
                                        <option value="Product Discontinued">Product Discontinued</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <span class="text-danger mt-5 reject-reason-select-error-section"
                                        style="display: none;">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <span class="reject-reason-select-error"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Reject Reason Input --}}
                        <div class="mt-1 mx-7 rejectReasonInputContainer" style="display: none;">
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-3">
                                    <label for="rejectReasonInputNew" class="form-label fw-semibold text-gray-700">Enter
                                        Reason Here:</label>
                                    <input type="text" class="form-control reject_reason_input_new"
                                        name="reject_reason_input_new" id="rejectReasonInputNew" min="1"
                                        placeholder="Enter reject reason">

                                    <span class="text-danger mt-5 reject-reason-input-error-section"
                                        style="display: none;">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <span class="reject-reason-input-error"></span>
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
        <div class="modal-dialog modal-xl">
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

                        <div class="row mt-12">
                            {{-- Pickup Location --}}
                            <div class="col-md-6">
                                <div class="mt-5 mx-7" id="pickupLocationContainer" style="display: none;">
                                    <h5 class="fw-bold text-gray-800 mb-3">
                                        <i class="bi bi-geo-alt text-primary me-2 fs-4"></i> Select Pickup Location
                                    </h5>
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-3">
                                            {{-- <label class="form-label fw-semibold text-gray-700">Choose a location:</label> --}}
                                            <select name="pickup_address_id" class="form-select form-select-lg"
                                                id="pickup_address_id" data-control="select2">
                                                <option value="" disabled selected>-- Select Pickup Location --
                                                </option>
                                                @foreach ($pickupAddress as $address)
                                                    <option value="{{ $address->id }}">
                                                        📍 {{ $address->first_name }} {{ $address->last_name }} -
                                                        {{ $address->address }}, {{ $address->state }},
                                                        {{ $address->city }} -
                                                        {{ $address->pincode }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger mt-5 pickup-address-error-section"
                                                style="display: none;">
                                                <i class="bi bi-exclamation-triangle"></i>
                                                <span class="pickup-address-error"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             {{-- product weight --}}
                             <div class="col-md-6">
                                <div class="mt-5 mx-7" id="productWeightContainer" style="display: none;">
                                    <h5 class="fw-bold text-gray-800 mb-3">
                                        <i class="bi bi-box text-primary me-2 fs-4"></i> Enter Product Weight (in grams)
                                    </h5>
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-3">
                                            {{-- <label for="product_weight"
                                                class="form-label fw-semibold text-gray-700">Product
                                                Weight:</label> --}}
                                            <select name="product_weight" class="form-select form-select-lg" id="product_weight" data-control="select2">
                                                <option value="" disabled selected>-- Select Product Weight --</option>
                                                <option value="0.5">500 GM</option>
                                                <option value="1">1KG</option> <!-- 1KG -->
                                                <option value="1.5">1.5KG</option> <!-- 1.5KG -->
                                                <option value="2">2KG</option> <!-- 2KG -->
                                                <option value="2.5">2.5KG</option> <!-- 2.5KG -->
                                            </select>
                                            {{-- <input type="number" class="form-control" name="product_weight"
                                                id="product_weight" min="1" placeholder="Enter weight in grams"> --}}

                                            <span class="text-danger mt-5 product-weight-error-section"
                                                style="display: none;">
                                                <i class="bi bi-exclamation-triangle"></i>
                                                <span class="product-weight-error"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                               {{-- Courier Service --}}
                               <div class="col-md-6">
                                <div class="mt-5 mx-7" id="courierServicesContainer" style="display: none;">
                                    <h5 class="fw-bold text-gray-800 mb-3">
                                        <i class="bi bi-truck text-primary me-2 fs-4"></i> Select Courier Services
                                    </h5>
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-3">
                                            {{-- <label class="form-label fw-semibold text-gray-700">Choose a location:</label> --}}
                                            <select name="courier_service" class="form-select form-select-lg" id="courier_service" data-control="select2">
                                                <option value="" disabled selected>-- Select Courier Service --</option>

                                                @if (!empty($courierServices))
                                                    @foreach ($courierServices as $courier)
                                                        <option
                                                            value="{{ $courier['courierName'] ?? '' }}"
                                                            data-id="{{ $courier['courierId'] ?? ''}}"
                                                            data-image="{{ $courier['logoUrl'] ?? '' }}"> <!-- Image from API -->
                                                            {{ $courier['courierName'] ?? '' }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option disabled>No courier services available</option>
                                                @endif
                                            </select>


                                            {{-- <select name="courier_service" class="form-select form-select-lg"
                                                id="courier_service" data-control="select2">
                                                <option value="" disabled selected>-- Select Courier Service --
                                                </option>
                                                @foreach ($courierServices as $courier)
                                                    <option value="{{ $courier['courierName'] }}"
                                                        data-id="{{ $courier['courierId'] }}">
                                                        {{ $courier['courierName'] }}</option>
                                                @endforeach
                                            </select> --}}
                                            <span class="text-danger mt-5 courier-service-error-section"
                                                style="display: none;">
                                                <i class="bi bi-exclamation-triangle"></i>
                                                <span class="courier-service-error"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- RTO Address --}}
                            <div class="col-md-6">
                                <div class="mt-5 mx-7" id="rtoAddressContainer" style="display: none;">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-3">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="sameAsRTO">
                                                <label class="form-check-label" for="sameAsRTO">
                                                    Same as RTO Address
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-12">
                            {{-- Reject Reason Select --}}
                            <div class="col-md-6">
                                <div class="mt-5 mx-7 rejectReasonSelectContainer" style="display: none;">
                                    <h5 class="fw-bold text-gray-800 mb-3">
                                        <i class="bi bi-journal-x text-primary me-2"></i> Select Reject Reason
                                    </h5>
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-3">
                                            {{-- <label for="rejectReasonSelectConfirmed"
                                                class="form-label fw-semibold text-gray-700">Choose
                                                a reject reason:</label> --}}
                                            <select name="reject_reason_select_confirmed"
                                                class="form-select form-select-lg reject_reason_select_confirmed"
                                                id="rejectReasonSelectConfirmed" data-control="select2">
                                                <option value="" disabled selected>-- Select Reason --</option>
                                                <option value="Out of Stock">Out of Stock</option>
                                                <option value="Pricing Issue">Pricing Issue</option>
                                                <option value="Customer Request">Customer Requested Cancellation</option>
                                                <option value="Payment Issue">Payment Not Received</option>
                                                <option value="Shipping Restriction">Cannot Deliver to Customer's Location
                                                </option>
                                                <option value="Product Discontinued">Product Discontinued</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <span class="text-danger mt-5 reject-reason-select-error-section"
                                                style="display: none;">
                                                <i class="bi bi-exclamation-triangle"></i>
                                                <span class="reject-reason-select-error"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Reject Reason Input --}}
                            <div class="col-md-6">
                                <div class="mt-5 mx-7 rejectReasonInputContainer" style="display: none;">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body p-3">
                                            <label for="rejectReasonInputConfirm"
                                                class="form-label fw-semibold text-gray-700">Enter
                                                Reason Here:</label>
                                            <input type="text" class="form-control reject_reason_input_confirmed"
                                                name="reject_reason_input_confirmed" id="rejectReasonInputConfirm"
                                                min="1" placeholder="Enter reject reason">

                                            <span class="text-danger mt-5 reject-reason-input-error-section"
                                                style="display: none;">
                                                <i class="bi bi-exclamation-triangle"></i>
                                                <span class="reject-reason-input-error"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="product_id" class="product_id">
                        <input type="hidden" name="retailer_clone_product_id" class="retailer_clone_product_id">
                        <input type="hidden" name="order_id" class="order_id">
                        <input type="hidden" class="corder_id" name="c_order_id">
                        <input type="hidden" name="courier_service_id" id="courier_service_id">
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitButton">
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
    <script>
        $(document).ready(function() {

            // for datatable load and table's data search
            var table1 = $("#kt_order_list_table").DataTable();
            $("#search_field").on("keyup", function() {
                table1.search(this.value).draw();
            });

            // for search inside select option on modal show
            $('#new-order-action-modal').on('shown.bs.modal', function() {
                $('.reject_reason_select_new').select2({
                    dropdownParent: $('#new-order-action-modal')
                });
            });
            $('#confirmed-order-action-modal').on('shown.bs.modal', function() {
                $('#pickup_address_id, #rto_address_id, #courier_service, .reject_reason_select_confirmed').select2({
                    dropdownParent: $('#confirmed-order-action-modal')
                });
            });

            //<-------------- START: New Order --------------->
            $('.reject_reason_select_new').change(function() { // for reject reason
                let selectedReason = $(this).val();
                if (selectedReason == "Other") {
                    $('.rejectReasonInputContainer').show();
                } else {
                    $('.rejectReasonInputContainer').hide();
                }
            });

            $(document).on('change', '#new-order-action-modal input[name="status"]', function() {
                const status = $(this).val();
                $('.rejectReasonSelectContainer, .rejectReasonInputContainer').hide();

                if (status == 'cancelled_by_retailer') {
                    $('.rejectReasonSelectContainer').show();
                    $('#new-order-action-modal .reject_reason_select_new').trigger('change');
                } else {
                    $('.rejectReasonSelectContainer').hide();
                }
            });


            $('#courier_service').on('change', function () {
                const selectedOption = $('#courier_service option:selected');
                const courierName = selectedOption.val(); // Gets value (courier name)
                const courierId = selectedOption.data('id'); // Gets data-id
                const imageUrl = selectedOption.data('image');
                $('#courier_service_id').val(courierId);
            });

            $(document).on('click', '.newOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_clone_product_id = $(this).attr('data-retailer-clone-product-id');
                let order_id = $(this).attr('data-order-id');
                let c_order_id = $(this).attr('data-c-order-id');

                $('.product_id').val(product_id);
                $('.retailer_clone_product_id').val(retailer_clone_product_id);
                $('.order_id').val(order_id);
                $('.corder_id').val(c_order_id);

                $('#new-order-action-modal').modal('show');
            });


            $(document).on('submit', '#newOrderForm', function(e) {
                e.preventDefault();

                let form = new FormData(this);

                // START: validation
                let status = form.get("status");
                let reject_reason_select_new = $('.reject_reason_select_new').val();
                let reject_reason_input_new = $('.reject_reason_input_new').val();

                let errors = [];
                $('.reject-reason-select-error-section, .reject-reason-input-error-section').hide();

                if (!status) return;

                if (status === "cancelled_by_retailer") {
                    if (!reject_reason_select_new) {
                        $(".reject-reason-select-error").text("Please select a reject reason");
                        $(".reject-reason-select-error-section").show();
                        errors.push("reject_reason_select_new");
                    }

                    if (reject_reason_select_new === "Other") {
                        if (!reject_reason_input_new || reject_reason_input_new.trim() === "") {
                            $(".reject-reason-input-error").text("Please enter a valid reject reason");
                            $(".reject-reason-input-error-section").show();
                            errors.push("reject_reason_input_new");
                        }
                    }
                }

                if (errors.length) return;
                // END: validation

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
                        document.getElementById('submitButton').addEventListener('click', function () {
                            const btn = this;
                            btn.disabled = true;
                        });
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
                                    document.getElementById('submitButton').addEventListener('click', function () {
                                        const btn = this;
                                        btn.disabled = false;
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
            $('.reject_reason_select_confirmed').change(function() { // for reject reason
                let selectedReason = $(this).val();
                if (selectedReason == "Other") {
                    $('.rejectReasonInputContainer').show();
                } else {
                    $('.rejectReasonInputContainer').hide();
                }
            });

            $(document).on('change', '#confirmed-order-action-modal input[name="status"]', function() {
                const status = $(this).val();
                $('#pickupLocationContainer, #rtoAddressContainer, #productWeightContainer, #courierServicesContainer, .rejectReasonSelectContainer, .rejectReasonInputContainer').hide();

                if (status == 'shipped_by_retailer') {
                    $('#pickupLocationContainer').show();
                    $('#rtoAddressContainer').show();
                    $('#productWeightContainer').show();
                    $('#courierServicesContainer').show();
                } else {
                    $('#pickupLocationContainer').hide();
                    $('#rtoAddressContainer').hide();
                    $('#productWeightContainer').hide();
                    $('#courierServicesContainer').hide();
                }

                if (status == 'cancelled_by_retailer') {
                    $('.rejectReasonSelectContainer').show();
                    $('.reject_reason_select_confirmed').trigger('change');
                } else {
                    $('.rejectReasonSelectContainer').hide();
                }
            });

            $(document).on('click', '.confirmedOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_clone_product_id = $(this).attr('data-retailer-clone-product-id');
                let order_id = $(this).attr('data-order-id');
                $('.product_id').val(product_id);
                $('.retailer_clone_product_id').val(retailer_clone_product_id);
                $('.order_id').val(order_id);

                $('#confirmed-order-action-modal').modal('show');
            });

            $('#rejectReasonInputConfirm').on('click', function() {
                $('#new-order-action-modal').modal('show');
            });

            $(document).on('submit', '#confirmedOrderForm', function(e) {
                e.preventDefault();

                let form = new FormData(this);
                console.log(form,"form dadata");

                // START: validation
                let status = form.get("status");
                let pickup_address_id = $('#pickup_address_id').val();
                let rto_address_id = $('#pickup_address_id').val();
                let courier_service = $('#courier_service').val();
                let product_weight = $('#product_weight').val();
                let reject_reason_select_confirmed = $('.reject_reason_select_confirmed').val();
                let reject_reason_input_confirmed = $('.reject_reason_input_confirmed').val();

                let errors = [];
                $('.pickup-address-error-section, .product-weight-error-section, .reject-reason-select-error-section, .reject-reason-input-error-section, .rto-address-error-section, .courier-service-error-section').hide();

                if (!status) return;

                if (status === "shipped_by_retailer") {
                    if (!pickup_address_id) {
                        $(".pickup-address-error").text("Please select pickup address");
                        $(".pickup-address-error-section").show();
                        errors.push("pickup_address_id");
                    }
                    if (!rto_address_id) {
                        $(".rto-address-error").text("Please Select RTO Address");
                        $(".rto-address-error-section").show();
                        errors.push("product_weight");
                    }
                    if (!courier_service) {
                        $(".courier-service-error").text("Please select Courier Partner");
                        $(".courier-service-error-section").show();
                        errors.push("product_weight");
                    }
                    if (!product_weight) {
                        $(".product-weight-error").text("Please enter product weight (in grams)");
                        $(".product-weight-error-section").show();
                        errors.push("product_weight");
                    }
                }

                if (status === "cancelled_by_retailer") {
                    if (!reject_reason_select_confirmed) {
                        $(".reject-reason-select-error").text("Please select a reject reason");
                        $(".reject-reason-select-error-section").show();
                        errors.push("reject_reason_select_confirmed");
                    }

                    if (reject_reason_select_confirmed === "Other") {
                        if (!reject_reason_input_confirmed || reject_reason_input_confirmed.trim() === "") {
                            $(".reject-reason-input-error").text("Please enter a valid reject reason");
                            $(".reject-reason-input-error-section").show();
                            errors.push("reject_reason_input_confirmed");
                        }
                    }
                }

                if (errors.length) return;
                // END: validation

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
