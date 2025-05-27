@extends('layouts.base')
@section('title')
    Retailer's Order List | TrendMart
@endsection

@section('styles')
    <style>
        #selected-courier-display {
            color: #17a2b8;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            line-height: 1.5;
        }

        #selected-courier-display strong {
            color: #333;
        }
    </style>
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-4 py-lg-4">
                <div id="kt_app_toolbar_container"
                    class="app-container container-xxl d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">

                    {{-- Page Title --}}
                    <div class="page-title">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Your Orders
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Order List</li>
                        </ul>
                    </div>

                    {{-- Filters --}}
                    <div class="w-100 w-md-auto d-flex flex-column flex-md-row gap-3">
                        {{-- Date Picker --}}
                        <div class="flex-grow-1">
                            <div class="input-group bg-secondary">
                                <input type="text" class="form-control form-control-solid bg-secondary border-0"
                                    placeholder="Pick date range" id="kt_daterangepicker_order_list">
                                <span class="input-group-text bg-secondary border-0">
                                    <i class="ki-duotone ki-calendar-8 fs-2">
                                        <span class="path1"></span><span class="path2"></span>
                                        <span class="path3"></span><span class="path4"></span>
                                        <span class="path5"></span><span class="path6"></span>
                                    </i>
                                </span>
                            </div>
                        </div>

                        {{-- Payment Method Dropdown --}}
                        <div style="min-width: 220px; max-width: 220px;">
                            <select id="payment_method_filter"
                                class="form-select form-select-solid bg-secondary border-0 w-100" data-control="select2"
                                data-placeholder="All Payment Method">
                                <option value="all">All Payment Method</option>
                                @foreach ($payment_method_list as $payment_method)
                                    <option value="{{ $payment_method->payment_method }}">
                                        {{ strtoupper($payment_method->payment_method) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

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
                        {{-- Stages --}}
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-center py-1 gap-3">
                            <div class="card-toolbar w-100 d-flex justify-content-center">
                                <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 justify-content-start flex-nowrap"
                                    style="overflow-x: auto; overflow-y: hidden; white-space: nowrap;">
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && (request('type') == 'new' || request('type') == null) ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'new']) }}">
                                            <i class="fas fa-sync-alt pe-2 text-primary"></i> New
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'approved-by-retailer' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'approved-by-retailer']) }}">
                                            <i class="fas fa-thumbs-up pe-2 text-info"></i> Approved
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'pickup' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'pickup']) }}">
                                            <i class="fas fa-box-open pe-2 text-success"></i> Pickup
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'in-transit' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'in-transit']) }}">
                                            <i class="fas fa-route pe-2 text-warning"></i> In Transit
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'ofd' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'ofd']) }}">
                                            <i class="fas fa-truck-arrow-right pe-2 text-warning"></i> OFD
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'delivered' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'delivered']) }}">
                                            <i class="fas fa-check-circle pe-2 text-success"></i> Delivered
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'rto' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'rto']) }}">
                                            <i class="fas fa-undo-alt pe-2 text-danger"></i> RTO
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'rtn-to-seller' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'rtn-to-seller']) }}">
                                            <i class="fas fa-warehouse pe-2 text-success"></i> RTN to Seller
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'close' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'close']) }}">
                                            <i class="fa-regular fa-circle-xmark pe-2 text-danger"></i> Close
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'cancel' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'cancel']) }}">
                                            <i class="fas fa-ban pe-2 text-danger"></i> Cancel
                                        </a>
                                    </li>
                                    <li class="nav-item my-3">
                                        <a class="nav-link px-2 py-2 rounded {{ request()->routeIs('retailer.order.list') && request('type') == 'lost' ? 'active' : '' }}"
                                            href="{{ route('retailer.order.list', ['type' => 'lost']) }}">
                                            <i class="fas fa-question-circle pe-2 text-muted"></i> Lost
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>



                        <div class="card-body pt-0">
                            <table class="table align-middle table-row-dashed fs-7" id="kt_datatable_order_list">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center min-w-50px">SR NO</th>
                                        <th class="text-center min-w-200px">ORDER DATE</th>
                                        <th class="text-center min-w-300px">ORDER DETAIL</th>
                                        <th class="text-center min-w-150px">MEDIA</th>
                                        <th class="text-center min-w-300px">CUSTOMER DETAIL</th>
                                        <th class="text-center min-w-70px">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <!-- START: New Order Modal -->
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
                                        id="approved_by_retailer" value="approved_by_retailer">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Confirm Order</span>
                                </label>

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2 text-danger">
                                    <input class="form-check-input mt-0" type="radio" name="status" id="cancel"
                                        value="cancel">
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
                                    <select name="reject_reason_select"
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
                                        name="reject_reason_input" id="rejectReasonInputNew" min="1"
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
                        <input type="hidden" name="order_product_id" class="order_product_id">
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
    <!-- END: New Order Modal -->

    <!-- START: Confirmed Order Modal -->
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
                                    <input class="form-check-input mt-0" type="radio" name="status" id="pickup"
                                        value="pickup">
                                    <i class="bi bi-truck text-success fs-5"></i>
                                    <span>I Want To Ship</span>
                                </label>

                                <label
                                    class="list-group-item d-flex align-items-center gap-3 mt-2 transfered-retailer-to-wholesaler-section">
                                    <input class="form-check-input mt-0" type="radio" name="status"
                                        id="transfered_retailer_to_wholesaler" value="transfered_retailer_to_wholesaler">
                                    <i class="bi bi-box-arrow-right text-primary fs-5"></i>
                                    <span>Transfer to Wholesaler</span>
                                </label>

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2 text-danger">
                                    <input class="form-check-input mt-0" type="radio" name="status" id="cancel"
                                        value="cancel">
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
                                                    <option value="{{ $address->id }}"
                                                        data-pincode="{{ $address->pincode }}"
                                                        data-warehouse-id="{{ $address->warehouse_id }}">
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
                                            <select name="product_weight" class="form-select form-select-lg"
                                                id="product_weight" data-control="select2">
                                                <option value="" disabled selected>-- Select Product Weight --
                                                </option>
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
                                            <!-- Button to trigger the modal -->
                                            <button type="button" class="btn btn-primary" id="selectCourierBtn"
                                                style="display: none;">
                                                Select Courier Service
                                            </button>
                                            <!-- Display selected courier -->
                                            <div id="selected-courier-display" class="mt-2 text-info"></div>
                                            <!-- Hidden input to store selected courier -->
                                            <input type="hidden" name="courier_service" id="courier_service"
                                                value="">
                                            <input type="hidden" name="courier_service_id" id="courier_service_id">
                                            <input type="hidden" name="courier_service_logo" id="courier_service_logo">
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
                                            <select name="reject_reason_select"
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
                                                name="reject_reason_input" id="rejectReasonInputConfirm" min="1"
                                                placeholder="Enter reject reason">

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
                        <input type="hidden" name="order_product_id" class="order_product_id">
                        <input type="hidden" name="order_id" class="order_id">
                        <input type="hidden" class="corder_id" name="c_order_id">
                        {{-- <input type="hidden" name="courier_service_id" class="courier_service_id"> --}}
                        <input type="hidden" name="customer_pincode" class="customer_pincode" id="customer_pincode">
                        <input type="hidden" name="product_amount" class="product_amount" id="product_amount">
                        <input type="hidden" name="service_mode" class="service_mode" id="service_mode">
                        <input type="hidden" name="rto_charge" class="rto_charge" id="rto_charge">
                        <input type="hidden" name="cod_charge" class="cod_charge" id="cod_charge">
                        <input type="hidden" name="shipping_charge" class="shipping_charge" id="shipping_charge">
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
    <div class="modal fade" id="courierDetailsModal" tabindex="-1" aria-labelledby="courierDetailsModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courierDetailsModalLabel">Courier Service Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="courierDetailsTable">
                        <thead>
                            <tr>
                                <th>Courier Name</th>
                                <th>Shipping Charge</th>
                                <th>COD Charge</th>
                                <th>RTO Charge</th>
                                <th>Service Mode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="courierDetailsBody">
                            <!-- Dynamic rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Confirmed Order Modal -->

    <!-- START: Pickup Order Modal -->
    <div class="modal fade" id="pickup-order-action-modal" tabindex="-1"
        aria-labelledby="pickup-order-action-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-item-center gap-4 mt-1" id="pickup-order-action-modal-label">
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

                <form id="pickupOrderForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold d-none">Order Action:</label>

                            <div class="list-group">
                                <label class="list-group-item d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="radio" name="status" id="in_transit"
                                        value="in_transit" checked>
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Process To Intransit</span>
                                </label>

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2 text-danger">
                                    <input class="form-check-input mt-0" type="radio" name="status" id="cancel"
                                        value="cancel">
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
                                    <label for="rejectReasonSelectPickup"
                                        class="form-label fw-semibold text-gray-700">Choose
                                        a reject reason:</label>
                                    <select name="reject_reason_select"
                                        class="form-select form-select-lg reject_reason_select_pickup"
                                        id="rejectReasonSelectPickup" data-control="select2">
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
                                    <label for="rejectReasonInputPickup"
                                        class="form-label fw-semibold text-gray-700">Enter
                                        Reason Here:</label>
                                    <input type="text" class="form-control reject_reason_input_pickup"
                                        name="reject_reason_input" id="rejectReasonInputPickup" min="1"
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
                        <input type="hidden" name="order_product_id" class="order_product_id">
                        <input type="hidden" name="order_id" class="order_id">
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary" for="pickupOrderForm">
                            <i class="bi bi-send"></i> Submit Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="upload-pickup-image-modal" tabindex="-1"
        aria-labelledby="upload-pickup-image-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form id="uploadPickupImageForm" method="POST">
                    @csrf
                    <div class="modal-body px-2 py-5">
                        <div class="mt-1 mx-7">
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-4">
                                    <label for="pickup_image" class="form-label fw-semibold text-gray-700">Pickup
                                        Image:</label>

                                    <div style="width: 200px; height: 170px;" class="m-3 text-center">
                                        <img src="" alt="Pickup Image" style="width: 100%; height: 100%;"
                                            id="pickup_image_preview">
                                    </div>

                                    <input type="file" class="form-control" name="pickup_image" id="pickup_image"
                                        accept=".jpg,.jpeg,.webp,.png,.svg,image/jpeg,image/jpg,image/webp,image/png,image/svg+xml">

                                    <span class="text-danger mt-5 reject-reason-input-error-section"
                                        style="display: none;">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <span class="reject-reason-input-error"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="order_id" class="pickup_image_order_id">
                    </div>

                    <div class="modal-footer bg-light p-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary" for="uploadPickupImageForm">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: Pickup Order Modal -->

    <!-- START: In Transit Order Modal -->
    <div class="modal fade" id="in-transit-order-action-modal" tabindex="-1"
        aria-labelledby="in-transit-order-action-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-item-center gap-4 mt-1" id="in-transit-order-action-modal-label">
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

                <form id="inTransitOrderForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold d-none">Order Action:</label>

                            <div class="list-group">
                                <label class="list-group-item d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="radio" name="status" id="delivered"
                                        value="delivered" checked>
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span>Delivered</span>
                                </label>

                                <label class="list-group-item d-flex align-items-center gap-3 mt-2 text-danger">
                                    <input class="form-check-input mt-0" type="radio" name="status" id="cancel"
                                        value="cancel">
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
                                    <label for="rejectReasonSelectInTransit"
                                        class="form-label fw-semibold text-gray-700">Choose
                                        a reject reason:</label>
                                    <select name="reject_reason_select"
                                        class="form-select form-select-lg reject_reason_select_in_transit"
                                        id="rejectReasonSelectInTransit" data-control="select2">
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
                                    <label for="rejectReasonInputInTransit"
                                        class="form-label fw-semibold text-gray-700">Enter
                                        Reason Here:</label>
                                    <input type="text" class="form-control reject_reason_input_in_transit"
                                        name="reject_reason_input" id="rejectReasonInputInTransit" min="1"
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
                        <input type="hidden" name="order_product_id" class="order_product_id">
                        <input type="hidden" name="order_id" class="order_id">
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary" for="inTransitOrderForm">
                            <i class="bi bi-send"></i> Submit Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: In Transit Order Modal -->
@endsection


@section('script')
    <script>
        //<------------- START : date pickert ------------->
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker_order_list").html(start.format("DD/MM/YYYY") + " - " + end.format(
                "DD/MM/YYYY"));
        }

        $("#kt_daterangepicker_order_list").daterangepicker({
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
        //<------------- END : date pickert ------------->


        //<------------- START : server-side transaction datatable ------------->
        const type = @json($type);
        dataTable = $('#kt_datatable_order_list').DataTable({
            dom: "<'row mb-2'" +
                "<'col-4 col-sm-6 col-md-3 d-flex align-items-center justify-content-start dt-toolbar datatable-length-section'l>" +
                "<'col-8 col-sm-6 col-md-9 d-flex align-items-center justify-content-end dt-toolbar datatable-search-section'f>" +
                ">" +
                "<'table-responsive'tr>" +
                "<'row'" +
                "<'col-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-6'i>" +
                "<'col-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                ">",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('retailer.order-list.fetch-record') }}",
                type: "POST",
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.date_filter = $('#kt_daterangepicker_order_list').val();
                    d.payment_method_filter = $('#payment_method_filter').val();
                    d.type = type;
                    d.order = d.order; // Add order data
                    d.columns = d.columns; // Add columns data
                },
                dataSrc: function(json) {
                    return json.data;
                }
            },
            order: [],
            columns: [{
                    data: 'sr_no',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'order_date',
                    className: 'text-center',
                    orderable: true,
                },
                {
                    data: 'order_detail',
                    className: 'text-start',
                    orderable: false,
                },
                {
                    data: 'media',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'customer_detail',
                    className: 'text-start',
                    orderable: false,
                },
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
            ],
            initComplete: function() {
                let searchBox = $('.datatable-search-section input');
                let searchLabel = $('.datatable-search-section label');
                let lengthSelect = $('.datatable-length-section select');

                searchBox.wrap('<div class="d-flex align-items-center position-relative my-1 w-100"></div>');
                searchBox.before(
                    '<i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>'
                ); // add icon
                searchBox.addClass('form-control form-control-solid w-100 ps-12 bg-secondary').attr(
                    'placeholder', 'Search'); // style the search input
                searchBox.css({
                    'padding': '13px 15px 12px 15px',
                    'font-size': '14px',
                });

                searchLabel.css({
                    'display': 'none',
                });

                lengthSelect.addClass('form-control form-control-solid w-100 bg-secondary');
                lengthSelect.css({
                    'padding': '13px 27px 12px 14px',
                    'font-size': '14px',
                })
            }
        });
        //<------------- END : server-side transaction datatable ------------->

        $(document).ready(function() {
            $("#kt_daterangepicker_order_list").on('apply.daterangepicker', function(ev, picker) {
                dataTable.draw();
            });

            $("#payment_method_filter").on('change', function() {
                dataTable.draw();
            });
        });

        $(document).ready(function() {
            // for search inside select option on modal show
            $('#new-order-action-modal').on('shown.bs.modal', function() {
                $('.reject_reason_select_new').select2({
                    dropdownParent: $('#new-order-action-modal')
                });
            });
            $('#confirmed-order-action-modal').on('shown.bs.modal', function() {
                $('#pickup_address_id, #rto_address_id, #courier_service, #product_weight, .reject_reason_select_confirmed')
                    .select2({
                        dropdownParent: $('#confirmed-order-action-modal')
                    });
                // Initialize Select Courier button visibility
                toggleSelectCourierButton();
            });

            // Courier services from controller (passed as JSON)
            const courierServices = @json($courierServices);

            // Initial payload for the API
            let payload = {
                source_Pincode: "",
                destination_Pincode: "",
                payment_Mode: "",
                amount: 0,
                shipment_Weight: 0
            };

            // Function to update payload
            function updatePayload(newData) {
                payload = {
                    ...payload,
                    ...newData
                };
            }

            // Function to validate payload
            function validatePayload() {
                const errors = [];
                if (!payload.source_Pincode || !/^\d{6}$/.test(payload.source_Pincode)) errors.push(
                    "Valid source pincode is required.");
                if (!payload.destination_Pincode || !/^\d{6}$/.test(payload.destination_Pincode)) errors.push(
                    "Valid destination pincode is required.");
                if (!payload.payment_Mode) errors.push("Payment mode is required.");
                if (payload.amount <= 0) errors.push("Amount must be greater than zero.");
                if (payload.shipment_Weight <= 0) errors.push("Shipment weight must be greater than zero.");
                return errors;
            }

            // Function to fetch courier rates from the API
            function fetchCourierRates() {
                const payloadErrors = validatePayload();
                if (payloadErrors.length > 0) {
                    alert('Please fix the following errors:\n- ' + payloadErrors.join('\n- '));
                    $('#courierDetailsBody').html('<tr><td colspan="6">Invalid payload data</td></tr>');
                    return;
                }

                $.ajax({
                    url: "{{ route('retailer.rate.calculation.post') }}",
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify(payload),
                    success: function(response) {

                        if (response.status && response.shipment_rates && response.shipment_rates
                            .length > 0) {
                            populateModalTable(response.shipment_rates);
                        } else if (response.rates && Array.isArray(response.rates) && response.rates
                            .length > 0) {
                            populateModalTableLorrigo(response.rates);
                        } else {
                            $('#courierDetailsBody').html(
                                '<tr><td colspan="6">No courier services available</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching courier rates:', xhr.responseText);
                        let errorMessage = 'Error fetching courier rates';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage += ': ' + xhr.responseJSON.message;
                        }
                        $('#courierDetailsBody').html(`<tr><td colspan="6">${errorMessage}</td></tr>`);
                    }
                });
            }

            // Function to populate the courier modal table  for fship
            function populateModalTable(shipmentRates) {
                let tableBody = '';
                shipmentRates.forEach(function(courier) {
                    const matchingCourier = courierServices.find(cs => cs.courierName === courier
                        .courier_name) || {};
                    tableBody += `
                        <tr>
                            <td>
                                ${matchingCourier.logoUrl ? `<img src="${matchingCourier.logoUrl}" alt="${courier.courier_name}" width="30" class="me-2">` : ''}
                                ${courier.courier_name}
                            </td>
                            <td>₹${(courier.shipping_charge || 0).toFixed(2)}</td>
                            <td>₹${(courier.cod_charge || 0).toFixed(2)}</td>
                            <td>₹${(courier.rto_charge || 0).toFixed(2)}</td>
                            <td>${courier.service_mode || 'N/A'}</td>
                            <td>
                                <button class="btn btn-sm btn-primary select-courier"
                                        data-courier="${courier.courier_name}"
                                        data-courier-id="${matchingCourier.courierId || ''}"
                                        data-courier-logo="${matchingCourier.logoUrl || null}"
                                        data-shipping-charge="${(courier.shipping_charge || 0).toFixed(2)}"
                                        data-cod-charge="${(courier.cod_charge || 0).toFixed(2)}"
                                        data-rto-charge="${(courier.rto_charge || 0).toFixed(2)}"
                                        data-service-mode="${courier.service_mode || 'N/A'}
                                        data-cpartner="fship">
                                    Select
                                </button>
                            </td>
                        </tr>`;
                });
                $('#courierDetailsBody').html(tableBody);
            }

            // Function to populate the courier modal table  for lorrigo
            function populateModalTableLorrigo(shipmentRates) {
                let tableBody = '';
                shipmentRates.forEach(function(courier) {

                    const matchingCourier = courierServices.find(cs => cs.courierName === courier
                        .courier_name) || {};
                    tableBody += `
                        <tr>
                            <td>
                                ${courier.logoUrl ? `<img src="${courier.logoUrl}" alt="${courier.name}" width="30" class="me-2">` : ''}
                                ${courier.name}
                            </td>
                            <td>₹${(courier.charge || 0).toFixed(2)}</td>
                            <td>₹${(courier.cod || 0).toFixed(2)}</td>
                            <td>₹${(courier.rtoCharges || 0).toFixed(2)}</td>
                            <td>${courier.type || 'N/A'}</td>
                            <td>
                                <button class="btn btn-sm btn-primary select-courier"
                                        data-courier="${courier.name}"
                                        data-courier-id="${courier.carrierID || ''}"
                                        data-courier-logo="${courier.name || null}"
                                        data-shipping-charge="${(courier.charge || 0).toFixed(2)}"
                                        data-cod-charge="${(courier.cod || 0).toFixed(2)}"
                                        data-rto-charge="${(courier.rtoCharges || 0).toFixed(2)}"
                                        data-service-mode="${courier.type || 'N/A'}"
                                        data-cpartner="lorrigo">
                                    Select
                                </button>
                            </td>
                        </tr>`;
                });
                $('#courierDetailsBody').html(tableBody);
            }

            // Function to toggle Select Courier button visibility
            function toggleSelectCourierButton() {
                const productWeight = $('#product_weight').val();
                const pickupAddress = $('#pickup_address_id').val();

                if (productWeight && pickupAddress) {
                    $('#selectCourierBtn').show();
                } else {
                    $('#selectCourierBtn').hide();
                }
            }

            // Handle "Select Courier" button click
            $(document).on('click', '#selectCourierBtn', function() {
                // Hide the previous modal to prevent overlap
                $('#confirmed-order-action-modal').modal('hide');

                const productWeight = $('#product_weight').val();
                const pickupAddress = $('#pickup_address_id').val();
                const selectedOption = $('#pickup_address_id option:selected');
                const pincode = selectedOption.data('pincode') || '';
                selectedWarehouseId = selectedOption.data('warehouse-id') || ''; // Store warehouse_id
                const productamount = $('#product_amount').val();
                const cpincode = $('#customer_pincode').val();
                console.log(productamount, cpincode);

                updatePayload({
                    source_Pincode: pincode,
                    destination_Pincode: cpincode,
                    payment_Mode: "COD",
                    amount: productamount,
                    shipment_Weight: productWeight
                });

                // Fetch courier rates and show modal with static backdrop
                fetchCourierRates();
                $('#courierDetailsModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#courierDetailsModal').modal('show');
            });

            // Handle courier selection from modal
            $(document).on('click', '.select-courier', function() {
                const courierName = $(this).data('courier') || 'Unknown';
                const courierId = $(this).data('courier-id') || '';
                const courierLogo = $(this).data('courier-logo') || null;
                const shippingCharge = $(this).data('shipping-charge') || '0.00';
                const codCharge = $(this).data('cod-charge') || '0.00';
                const rtoCharge = $(this).data('rto-charge') || '0.00';
                const serviceMode = $(this).data('service-mode') || 'N/A';
                const cpartner = $(this).data('cpartner');
                console.log(courierId, courierName, "courier Info");

                // Store selected courier in hidden inputs
                $('#rto_charge').val(rtoCharge);
                $('#cod_charge').val(codCharge);
                $('#shipping_charge').val(shippingCharge);
                $('#service_mode').val(serviceMode);

                $('#courier_service').val(courierName);
                $('#courier_service_id').val(courierId);
                $('#courier_service_logo').val(courierLogo);

                // Display all courier details below the Select Courier button
                $('#selected-courier-display').html(`
                    <strong>Selected Courier:</strong> ${courierName}<br>
                    <strong>Shipping Charge:</strong> ₹${shippingCharge}<br>
                    <strong>COD Charge:</strong> ₹${codCharge}<br>
                    <strong>RTO Charge:</strong> ₹${rtoCharge}<br>
                    <strong>Service Mode:</strong> ${serviceMode}
                `);

                // Validate courier match

                const matchedCourier = courierServices.find(cs => cs.courierName === courierName);
                if (cpartner == 'fship') {
                    if (!matchedCourier || matchedCourier.courierId !== courierId) {
                        $('.courier-service-error').text(
                            'Selected courier does not match available services.');
                        $('.courier-service-error-section').show();
                        return;
                    } else {
                        $('.courier-service-error-section').hide();
                    }
                }

                // Close courier modal and show previous modal
                $('#courierDetailsModal').modal('hide');
                $('#confirmed-order-action-modal').modal('show');
            });

            // Handle close button to restore previous modal and clear selection if no courier selected
            $('#courierDetailsModal').on('hidden.bs.modal', function() {
                // Show the previous modal when courier modal closes
                $('#confirmed-order-action-modal').modal('show');
                // Clear selected courier display if no courier is selected
                if (!$('#courier_service').val()) {
                    $('#selected-courier-display').html('');
                }
            });

            // Handle changes to product weight and pickup address to toggle Select Courier button
            $(document).on('change', '#product_weight, #pickup_address_id', function() {
                toggleSelectCourierButton();
            });

            //<-------------- START: New Order --------------->
            $('.reject_reason_select_new').change(function() {
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

                if (status == 'cancel') {
                    $('.rejectReasonSelectContainer').show();
                    $('.reject_reason_select_new').first().trigger('change');
                } else {
                    $('.rejectReasonSelectContainer').hide();
                }
            });

            $(document).on('click', '.newOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_clone_product_id = $(this).attr('data-retailer-clone-product-id');
                let order_product_id = $(this).attr('data-order-product-id');
                let order_id = $(this).attr('data-order-id');
                let c_order_id = $(this).attr('data-c-order-id');


                // <input type="hidden" name="customer_pin_code" id="customer_pin_code_id">
                // <input type="hidden" name="product_amount" id="product_amount_id">

                $('.product_id').val(product_id);
                $('.retailer_clone_product_id').val(retailer_clone_product_id);
                $('.order_product_id').val(order_product_id);
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

                if (status === "cancel") {
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

                $('#new-order-action-modal').modal('hide');

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
                    case "approved_by_retailer":
                        swalConfig.text = "You are about to confirm this order.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Confirm it!";
                        break;
                    case "cancel":
                        swalConfig.text = "You are about to reject this order.";
                        swalConfig.icon = "warning";
                        swalConfig.confirmButtonText = "Yes, Reject it!";
                        break;
                    default:
                        return;
                }

                Swal.fire(swalConfig).then((result) => {
                    if (result.isConfirmed) {
                        const submitBtn = document.getElementById('submitButton');
                        if (submitBtn) submitBtn.disabled = true;

                        Swal.fire({
                            title: "Processing...",
                            text: "Please wait while we process your request.",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
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
                                            .replace(":type", response.type);
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.msg,
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                    if (submitBtn) submitBtn.disabled = false;
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong, Please try later!",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                                if (submitBtn) submitBtn.disabled = false;
                            }
                        });
                    }
                });
            });
            //<-------------- END: New Order --------------->


            //<-------------- START: Confirmed Order --------------->
            $(document).on('change', '.reject_reason_select_confirmed', function() {
                let selectedReason = $(this).val();
                if (selectedReason == "Other") {
                    $('.rejectReasonInputContainer').show();
                } else {
                    $('.rejectReasonInputContainer').hide();
                }
            });

            $(document).on('change', '#confirmed-order-action-modal input[name="status"]', function() {
                const status = $(this).val();

                $('#pickupLocationContainer, #rtoAddressContainer, #productWeightContainer, #courierServicesContainer, .rejectReasonSelectContainer, .rejectReasonInputContainer')
                    .hide();

                if (status == 'pickup') {
                    $('#pickupLocationContainer').show();
                    $('#rtoAddressContainer').show();
                    $('#productWeightContainer').show();
                    $('#courierServicesContainer').show();
                    // Check if Select Courier button should be shown
                    toggleSelectCourierButton();
                } else {
                    $('#pickupLocationContainer').hide();
                    $('#rtoAddressContainer').hide();
                    $('#productWeightContainer').hide();
                    $('#courierServicesContainer').hide();
                    $('#selectCourierBtn').hide(); // Ensure button is hidden for non-shipped status
                }

                if (status == 'cancel') {
                    $('.rejectReasonSelectContainer').show();
                    $('.reject_reason_select_confirmed').first().trigger('change');
                } else {
                    $('.rejectReasonSelectContainer').hide();
                }
            });

            $(document).on('click', '.confirmedOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_clone_product_id = $(this).attr('data-retailer-clone-product-id');
                let order_product_id = $(this).attr('data-order-product-id');
                let order_id = $(this).attr('data-order-id');
                let customer_pincode = $(this).attr('data-product-pincode');
                let product_amount = $(this).attr('data-product-amount');

                if (product_id) {
                    $('.transfered-retailer-to-wholesaler-section').removeClass('d-none');
                }
                if (retailer_clone_product_id) {
                    $('.transfered-retailer-to-wholesaler-section').addClass('d-none');
                }

                $('.customer_pincode').val(customer_pincode);
                $('.product_amount').val(product_amount);

                $('.product_id').val(product_id);
                $('.retailer_clone_product_id').val(retailer_clone_product_id);
                $('.order_product_id').val(order_product_id);
                $('.order_id').val(order_id);

                $('#confirmed-order-action-modal').modal('show');
            });

            $('#courier_service').on('change', function() {
                const selectedOption = $('#courier_service option:selected');
                const courierName = selectedOption.val();
                const courierId = selectedOption.data('id');
                const imageUrl = selectedOption.data('image');
                $('#courier_service_id').val(courierId);
            });

            $(document).on('submit', '#confirmedOrderForm', function(e) {
                e.preventDefault();

                let form = new FormData(this);
                console.log(form, 'form data');

                // START: validation
                let status = form.get("status");
                let retailer_clone_product_id = form.get("retailer_clone_product_id");
                let pickup_address_id = $('#pickup_address_id').val();
                let rto_address_id = $('#rto_address_id').val();
                let courier_service = $('#courier_service').val();
                let product_weight = $('#product_weight').val();
                let shipping_charge = $('#shipping_charge').val();
                let cod_charge = $('#cod_charge').val();
                let rto_charge = $('#rto_charge').val();


                let reject_reason_select_confirmed = $('.reject_reason_select_confirmed').val();
                let reject_reason_input_confirmed = $('.reject_reason_input_confirmed').val();

                let errors = [];
                $('.pickup-address-error-section, .product-weight-error-section, .reject-reason-select-error-section, .reject-reason-input-error-section, .rto-address-error-section, .courier-service-error-section')
                    .hide();

                if (!status) return;
                if (retailer_clone_product_id && status == 'transfered_retailer_to_wholesaler') {
                    return;
                }

                if (status === "pickup") {
                    if (!pickup_address_id) {
                        $(".pickup-address-error").text("Please select pickup address");
                        $(".pickup-address-error-section").show();
                        errors.push("pickup_address_id");
                    }
                    // if (!rto_address_id) {
                    //     $(".rto-address-error").text("Please select RTO address");
                    //     $(".rto-address-error-section").show();
                    //     errors.push("rto_address_id");
                    // }
                    if (!courier_service) {
                        $(".courier-service-error").text("Please select a courier partner");
                        $(".courier-service-error-section").show();
                        errors.push("courier_service");
                    }
                    if (!product_weight) {
                        $(".product-weight-error").text("Please select product weight");
                        $(".product-weight-error-section").show();
                        errors.push("product_weight");
                    }
                }

                if (status === "cancel") {
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
                console.log(errors.length, "error");

                if (errors.length) return;
                // END: validation

                $('#confirmed-order-action-modal').modal('hide');

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
                    case "pickup":
                        swalConfig.text = "You are about to confirm this order.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Confirm it!";
                        break;
                    case "transfered_retailer_to_wholesaler":
                        swalConfig.text = "This order will be transferred to the wholesaler.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Transfer it!";
                        break;
                    case "cancel":
                        swalConfig.text = "You are about to reject this order.";
                        swalConfig.icon = "warning";
                        swalConfig.confirmButtonText = "Yes, Reject it!";
                        break;
                    default:
                        return;
                }

                Swal.fire(swalConfig).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Processing...",
                            text: "Please wait while we confirm your order.",
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('retailer.order.action.confirmed-order') }}",
                            type: "POST",
                            data: form,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.close();
                                if (response.status) {
                                    Swal.fire({
                                        title: "Success!",
                                        text: response.msg,
                                        icon: "success",
                                        confirmButtonText: "OK"
                                    }).then(() => {
                                        window.location.href =
                                            `{{ route('retailer.order.list', ':type') }}`
                                            .replace(":type", response.type);
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
                                Swal.close();
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong, please try again later!",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });
            //<-------------- END: Confirmed Order --------------->

            //<-------------- START: Pickup Order --------------->
            // pickup image fetch
            $(document).on('click', '#uploadPickupImage', function() {
                $('.reject-reason-input-error').text('');
                $('.reject-reason-input-error-section').hide();

                let order_id = $(this).attr('data-order-id');
                $('.pickup_image_order_id').val(order_id);

                $.ajax({
                    url: "{{ route('retailer.order.pickup-image.fetch') }}",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_id: order_id
                    },
                    success: function(response) {
                        var defaultImage = "/assets/media/images/no_image.jpg";

                        if (response.status) {
                            $("#pickup_image_preview")
                                .off("error")
                                .on("error", function() {
                                    $(this).off("error");
                                    $(this).attr("src", defaultImage);
                                })
                                .attr("src", response.pickup_image || defaultImage)
                                .show();
                        } else {
                            $('#pickup_image_preview').attr('src', defaultImage);
                        }
                    },
                    error: function(xhr) {
                        $('#pickup_image_preview').attr('src', defaultImage);
                    }
                });

                $('#upload-pickup-image-modal').modal('show');
            });

            // pickup image upload
            $(document).on('submit', '#uploadPickupImageForm', function(e) {
                e.preventDefault();

                let form = $(this)[0];
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('retailer.order.pickup-image.upload') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status) {
                            $('#uploadPickupImageForm')[0].reset();
                            $('#pickup_image_preview').attr('src', '');
                            $('.pickup_image_order_id').val('');
                            Swal.fire({
                                title: "Image Uploaded!",
                                text: response.msg || 'Image uploaded successfully!',
                                icon: "success",
                                confirmButtonText: "OK"
                            });
                            $('#upload-pickup-image-modal').modal('hide');
                        } else {
                            $('.reject-reason-input-error').text(response.msg ||
                                'Upload failed.');
                            $('.reject-reason-input-error-section').show();
                        }
                    },
                    error: function(xhr) {
                        let error = xhr.responseJSON?.message || 'Something went wrong.';
                        $('.reject-reason-input-error').text(error);
                        $('.reject-reason-input-error-section').show();
                    }
                });
            });

            $(document).on('click', '.pickupOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_clone_product_id = $(this).attr('data-retailer-clone-product-id');
                let order_product_id = $(this).attr('data-order-product-id');
                let order_id = $(this).attr('data-order-id');
                let c_order_id = $(this).attr('data-c-order-id');

                $('.product_id').val(product_id);
                $('.retailer_clone_product_id').val(retailer_clone_product_id);
                $('.order_product_id').val(order_product_id);
                $('.order_id').val(order_id);
                $('.corder_id').val(c_order_id);

                $('#pickup-order-action-modal').modal('show');
            });

            $(document).on('change', '#pickup-order-action-modal input[name="status"]', function() {
                const status = $(this).val();
                $('.rejectReasonSelectContainer, .rejectReasonInputContainer').hide();

                if (status == 'cancel') {
                    $('.rejectReasonSelectContainer').show();
                    $('.reject_reason_select_pickup').first().trigger('change');
                } else {
                    $('.rejectReasonSelectContainer').hide();
                }
            });

            $('.reject_reason_select_pickup').change(function() {
                let selectedReason = $(this).val();
                if (selectedReason == "Other") {
                    $('.rejectReasonInputContainer').show();
                } else {
                    $('.rejectReasonInputContainer').hide();
                }
            });

            $(document).on('submit', '#pickupOrderForm', function(e) {
                e.preventDefault();
                let form = new FormData(this);

                // START: validation
                let status = form.get("status");
                let reject_reason_select_pickup = $('.reject_reason_select_pickup').val();
                let reject_reason_input_pickup = $('.reject_reason_input_pickup').val();

                let errors = [];
                $('.reject-reason-select-error-section, .reject-reason-input-error-section').hide();

                if (!status) return;

                if (status === "cancel") {
                    if (!reject_reason_select_pickup) {
                        $(".reject-reason-select-error").text("Please select a reject reason");
                        $(".reject-reason-select-error-section").show();
                        errors.push("reject_reason_select_pickup");
                    }

                    if (reject_reason_select_pickup === "Other") {
                        if (!reject_reason_input_pickup || reject_reason_input_pickup.trim() === "") {
                            $(".reject-reason-input-error").text("Please enter a valid reject reason");
                            $(".reject-reason-input-error-section").show();
                            errors.push("reject_reason_input_pickup");
                        }
                    }
                }

                if (errors.length) return;
                // END: validation

                $('#pickup-order-action-modal').modal('hide');

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
                    case "in_transit":
                        swalConfig.text = "You are about to in transit this order.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Confirm it!";
                        break;
                    case "cancel":
                        swalConfig.text = "You are about to reject this order.";
                        swalConfig.icon = "warning";
                        swalConfig.confirmButtonText = "Yes, Reject it!";
                        break;
                    default:
                        return;
                }

                Swal.fire(swalConfig).then((result) => {
                    if (result.isConfirmed) {
                        const submitBtn = document.getElementById('submitButton');
                        if (submitBtn) submitBtn.disabled = true;

                        Swal.fire({
                            title: "Processing...",
                            text: "Please wait while we process your request.",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('retailer.order.action.pickup-order') }}",
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
                                            .replace(":type", response.type);
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.msg,
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                    if (submitBtn) submitBtn.disabled = false;
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong, Please try later!",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                                if (submitBtn) submitBtn.disabled = false;
                            }
                        });
                    }
                });
            });
            //<-------------- END: Pickup Order --------------->

            //<-------------- START: In Transit Order --------------->
            $(document).on('click', '.inTransitOrderAction', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_clone_product_id = $(this).attr('data-retailer-clone-product-id');
                let order_product_id = $(this).attr('data-order-product-id');
                let order_id = $(this).attr('data-order-id');
                let c_order_id = $(this).attr('data-c-order-id');

                $('.product_id').val(product_id);
                $('.retailer_clone_product_id').val(retailer_clone_product_id);
                $('.order_product_id').val(order_product_id);
                $('.order_id').val(order_id);
                $('.corder_id').val(c_order_id);

                $('#in-transit-order-action-modal').modal('show');
            });

            $(document).on('change', '#in-transit-order-action-modal input[name="status"]', function() {
                const status = $(this).val();
                $('.rejectReasonSelectContainer, .rejectReasonInputContainer').hide();

                if (status == 'cancel') {
                    $('.rejectReasonSelectContainer').show();
                    $('.reject_reason_select_in_transit').first().trigger('change');
                } else {
                    $('.rejectReasonSelectContainer').hide();
                }
            });

            $('.reject_reason_select_in_transit').change(function() {
                let selectedReason = $(this).val();
                if (selectedReason == "Other") {
                    $('.rejectReasonInputContainer').show();
                } else {
                    $('.rejectReasonInputContainer').hide();
                }
            });

            $(document).on('submit', '#inTransitOrderForm', function(e) {
                e.preventDefault();
                let form = new FormData(this);

                // START: validation
                let status = form.get("status");
                let reject_reason_select_in_transit = $('.reject_reason_select_in_transit').val();
                let reject_reason_input_in_transit = $('.reject_reason_input_in_transit').val();

                let errors = [];
                $('.reject-reason-select-error-section, .reject-reason-input-error-section').hide();

                if (!status) return;

                if (status === "cancel") {
                    if (!reject_reason_select_in_transit) {
                        $(".reject-reason-select-error").text("Please select a reject reason");
                        $(".reject-reason-select-error-section").show();
                        errors.push("reject_reason_select_in_transit");
                    }

                    if (reject_reason_select_in_transit === "Other") {
                        if (!reject_reason_input_in_transit || reject_reason_input_in_transit.trim() ===
                            "") {
                            $(".reject-reason-input-error").text("Please enter a valid reject reason");
                            $(".reject-reason-input-error-section").show();
                            errors.push("reject_reason_input_in_transit");
                        }
                    }
                }

                if (errors.length) return;
                // END: validation

                $('#in-transit-order-action-modal').modal('hide');

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
                    case "delivered":
                        swalConfig.text = "Are you sure to mark as delivered?.";
                        swalConfig.icon = "success";
                        swalConfig.confirmButtonText = "Yes, Delivered!";
                        break;
                    case "cancel":
                        swalConfig.text = "You are about to reject this order.";
                        swalConfig.icon = "warning";
                        swalConfig.confirmButtonText = "Yes, Reject it!";
                        break;
                    default:
                        return;
                }

                Swal.fire(swalConfig).then((result) => {
                    if (result.isConfirmed) {
                        const submitBtn = document.getElementById('submitButton');
                        if (submitBtn) submitBtn.disabled = true;

                        Swal.fire({
                            title: "Processing...",
                            text: "Please wait while we process your request.",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('retailer.order.action.in-transit-order') }}",
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
                                            .replace(":type", response.type);
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.msg,
                                        icon: "error",
                                        confirmButtonText: "OK"
                                    });
                                    if (submitBtn) submitBtn.disabled = false;
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong, Please try later!",
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                                if (submitBtn) submitBtn.disabled = false;
                            }
                        });
                    }
                });
            });
            //<-------------- END: In Transit Order --------------->
        });
    </script>
@endsection
