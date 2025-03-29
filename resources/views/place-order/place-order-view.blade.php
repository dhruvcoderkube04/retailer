@extends('layouts.base')
@section('title')
    Retailer's Added Product List
@endsection
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
                            Place New Order</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{route('retailer.dashboard')}}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Product list</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->

                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
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
                    <!--begin::Products-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-ecommerce-product-filter="search"
                                        class="form-control form-control-solid w-250px ps-12"
                                        placeholder="Search Product" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                <div class="w-100 mw-150px">
                                    <!--begin::Select2-->
                                    <select class="form-select form-select-solid" data-control="select2"
                                        data-hide-search="true" data-placeholder="Status"
                                        data-kt-ecommerce-product-filter="status">
                                        <option></option>
                                        <option value="all">All</option>
                                        <option value="published">Published</option>
                                        <option value="scheduled">Scheduled</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>
                                <!--begin::Add product-->
                                {{-- <a href="apps/ecommerce/catalog/add-product.html" class="btn btn-primary">Add Product</a> --}}
                                <!--end::Add product-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center min-w-70px">Actions</th>
                                        <th class="text-center min-w-200px">Product</th>
                                        <th class="text-center min-w-150px">Wholesaler</th>
                                        <th class="text-center min-w-150px">SKU</th>
                                        <th class="text-center min-w-0px"></th>
                                        <th class="text-center min-w-100px">New Price
                                            <br> <span class="text-capitalize fs-9">(Per Pis)</span>
                                        </th>
                                        <th class="text-center min-w-100px">Margin
                                            <br> <span class="text-capitalize fs-9">(In Rs.)</span>
                                        </th>
                                        <th class="text-center min-w-100px">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($filteredRetailerProducts as $retailerProduct)
                                        @foreach ($retailerProduct->products as $product)
                                        {{-- {{dd($retailerProduct)}} --}}
                                            <tr>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary btn-sm placeOrderButton"
                                                        style="white-space: nowrap;"
                                                        data-product-id="{{ $product->id }}"
                                                        data-retailer-id="{{ $retailerProduct->retailer_id }}"
                                                        data-wholesaler-id="{{ $retailerProduct->wholesaler_id }}">
                                                        Punch
                                                    </button>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" class="symbol symbol-50px">
                                                            @php
                                                                $get_image =
                                                                    explode(',', @$product->images)[0] ?? '';
                                                            @endphp
                                                            <span class="symbol-label"
                                                                style="background-image: url('{{ 'https://wholesale.lghosts.com/uploads/' . $get_image }}');"></span>
                                                        </a>
                                                        <div class="ms-5">
                                                            <a href="#"
                                                                class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                                                data-kt-ecommerce-product-filter="product_name">{{ $product->name }}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="ms-5">
                                                        <a href="{{ route('retailer.view-category-margin', $product->wholesaler->id) }}"
                                                            class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                                            data-kt-ecommerce-product-filter="product_name">{{ $retailerProduct->wholesaler->userDetail->company_name }}</a>
                                                    </div>
                                                </td>
                                                <td class="text-center pe-0" data-order="22">
                                                    <span class="fw-bold">{{ $product->sku }}</span>
                                                </td>
                                                <td class="text-center pe-0" data-order="22">
                                                    <span class="fw-bold"></span>
                                                </td>
                                                <td class="text-center" data-order="22">
                                                    <div class="badge badge-light-primary">
                                                        {{ $product->new_price }}
                                                    </div>
                                                </td>
                                                <td class="text-center pe-0" data-order="rating-4">
                                                    <div class="badge badge-light-info">{{ $retailerProduct->margin }}</div>
                                                </td>
                                                <td class="text-center" data-order="Inactive">
                                                    @if ($product->status == 'inactive')
                                                        <div class="badge badge-light-danger">
                                                            {{ $product->status }}
                                                        </div>
                                                    @elseif ($product->status == 'active')
                                                        <div class="badge badge-light-success">
                                                            {{ $product->status }}
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Products-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Footer-->
        @include('layouts.footer')
        <!--end::Footer-->
    </div>
    <!--end:::Main-->

    <!-- Bootstrap Modal -->
    <div class="modal fade @if ($errors->any()) show d-block @endif" id="placeOrderModal" tabindex="-1"
        aria-labelledby="placeOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="placeOrderModalLabel">Customer Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="customerForm" method="POST" action="{{ route('retailer.place-order') }}">
                    <div class="modal-body">
                        @csrf

                        <div class="row g-3">
                            <!-- First Name -->
                            <div class="col-6 mb-3">
                                <label for="firstname" class="form-label required">First Name</label>
                                <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                                    id="firstname" name="firstname" value="{{ old('firstname') }}"
                                    placeholder="Enter First Name">
                                @error('firstname')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-6 mb-3">
                                <label for="lastname" class="form-label required">Last Name</label>
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                                    id="lastname" name="lastname" value="{{ old('lastname') }}"
                                    placeholder="Enter Last Name">
                                @error('lastname')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div class="col-6 mb-3">
                                <label for="phone_number" class="form-label required">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                    id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                                    placeholder="Enter Phone Number">
                                @error('phone_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="Enter Email">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label required">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                                    placeholder="Enter Address">{{ old('address') }}</textarea>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- State -->
                            <div class="col-6 mb-3">
                                <label for="state" class="form-label required">State</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror"
                                    id="state" name="state" value="{{ old('state') }}"
                                    placeholder="Enter State">
                                @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- City -->
                            <div class="col-6 mb-3">
                                <label for="city" class="form-label required">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="city" name="city" value="{{ old('city') }}" placeholder="Enter City">
                                @error('city')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Pincode -->
                            <div class="col-6 mb-3">
                                <label for="pincode" class="form-label required">Pincode</label>
                                <input type="text" class="form-control @error('pincode') is-invalid @enderror"
                                    id="pincode" name="pincode" value="{{ old('pincode') }}"
                                    placeholder="Enter Pincode">
                                @error('pincode')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="col-6 mb-3">
                                <label for="quantity" class="form-label required">Quantity</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                    id="quantity" name="quantity" value="{{ old('quantity', 1) }}"
                                    placeholder="Enter quantity">
                                @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6 mb-3">
                                <label for="payment_method" class="form-label required">Payment Method</label>
                                <div class="mt-2">
                                    <div class="form-check-inline">
                                        <input type="radio" id="cod" name="payment_method" value="cod"
                                            {{ old('payment_method') == 'cod' ? 'checked' : '' }}>
                                        <label for="cod" class="form-check-label">COD</label>
                                    </div>
                                    <div class="form-check-inline">
                                        <input type="radio" id="prepaid" name="payment_method" value="prepaid"
                                            {{ old('payment_method') == 'prepaid' ? 'checked' : '' }}>
                                        <label for="prepaid" class="form-check-label">Prepaid</label>
                                    </div>
                                </div>
                                @error('payment_method')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <input type="hidden" name="product_id" id="product_id">
                        <input type="hidden" name="retailer_id" id="retailer_id">
                        <input type="hidden" name="wholesaler_id" id="wholesaler_id">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" form="customerForm">Punch</button>
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
            let modal = $("#placeOrderModal");
            if (modal.hasClass("show")) {
                modal.modal("show");
                if (!$(".modal-backdrop").length) {
                    $("body").append('<div class="modal-backdrop fade show"></div>');
                }
            }

            $(document).on('click', '.placeOrderButton', function() {
                let product_id = $(this).attr('data-product-id');
                let retailer_id = $(this).attr('data-retailer-id');
                let wholesaler_id = $(this).attr('data-wholesaler-id');

                $('#product_id').val(product_id);
                $('#retailer_id').val(retailer_id);
                $('#wholesaler_id').val(wholesaler_id);

                $('#placeOrderModal').modal('show');
            });
        });
    </script>
@endsection
