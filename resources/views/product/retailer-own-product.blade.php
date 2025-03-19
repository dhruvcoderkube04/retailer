@extends('layouts.base')
@section('title')
    Retailer's Added Product List
@endsection
@section('content')

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Your Added Products List</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Product list</li>
                        </ul>
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
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
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
                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                <a href="{{route('retailer.add.product')}}" class="btn btn-primary">Add Product</a>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            {{-- tabs --}}
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1">Margin Added
                                        Products</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">Clone Product</a>
                                </li>
                            </ul>

                            {{-- tab contents --}}
                            <div class="tab-content" id="myTabContent">

                                {{-- margin added products --}}
                                <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="kt_ecommerce_products_table">
                                        <thead>
                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="text-center min-w-70px">Actions</th>
                                                <th class="w-10px pe-2"></th>
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
                                            @if ($retailerProducts->isNotEmpty())
                                                @foreach ($retailerProducts as $retailerProduct)
                                                    @foreach ($retailerProduct->products as $product)
                                                        <tr>
                                                            <td class="text-center">
                                                                @if (!in_array($product->id, $clonedProducts))
                                                                    <a href="{{ route('retailer.clone-product-view', $product->id) }}"
                                                                        class="btn btn-primary btn-sm"
                                                                        style="white-space: nowrap;">Clone</a>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{-- <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                    <input class="form-check-input" type="checkbox" value="1" />
                                                                </div> --}}
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <a href="{{ route('retailer.clone-product-view', $product->id) }}"
                                                                        class="symbol symbol-50px">
                                                                        @php
                                                                            $get_image =
                                                                                explode(',', @$product->images)[0] ??
                                                                                '';
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
                                                                        data-kt-ecommerce-product-filter="product_name">{{ @$retailerProduct->wholesaler->userDetail->company_name }}</a>
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
                                                                <div class="badge badge-light-info">
                                                                    {{ $retailerProduct->margin }}</div>
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
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                {{-- clone products --}}
                                <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="kt_ecommerce_products_table">
                                        <thead>
                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="text-center min-w-70px">Actions</th>
                                                <th class="w-10px pe-2">
                                                    <!-- <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_ecommerce_products_table .form-check-input" value="1" />
                                                    </div> -->
                                                </th>
                                                <th class="text-center min-w-200px">Product</th>
                                                <th class="text-center min-w-150px">SKU</th>
                                                <th class="text-center min-w-100px">Catgory</th>
                                                <th class="text-center min-w-100px">New Price
                                                    <br> <span class="text-capitalize fs-9">(Per Pis)</span>
                                                </th>
                                                <th class="text-center min-w-100px">Status</th>
                                                <th class="text-center min-w-0px"></th>
                                                <th class="text-center min-w-0px"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                            @foreach ($retailerCloneProducts as $cloneProduct)
                                                <tr>
                                                    <td class="text-center">
                                                        <form
                                                            action="{{ route('retailer.clone-product-remove', $cloneProduct->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to remove this product from clone?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                style="white-space: nowrap;">Remove</button>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        {{-- <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox" value="1" />
                                                        </div> --}}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <a href="#" class="symbol symbol-50px">
                                                                @php
                                                                    $get_image =
                                                                        explode(',', @$cloneProduct->images)[0] ?? '';
                                                                @endphp
                                                                <span class="symbol-label"
                                                                    style="background-image: url('{{ 'https://wholesale.lghosts.com/uploads/' . $get_image }}');"></span>
                                                            </a>
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                                                    data-kt-ecommerce-product-filter="product_name">{{ $cloneProduct->name }}</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center pe-0" data-order="22">
                                                        <span class="fw-bold">{{ $cloneProduct->sku }}</span>
                                                    </td>
                                                    <td class="text-center pe-0" data-order="22">
                                                        {{ $cloneProduct->category->category_name }}
                                                    </td>
                                                    <td class="text-center" data-order="22">
                                                        <div class="badge badge-light-primary">
                                                            {{ $cloneProduct->new_price }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center" data-order="Inactive">
                                                        @if ($product->status == 'inactive')
                                                            <div class="badge badge-light-danger">
                                                                {{ $cloneProduct->status }}
                                                            </div>
                                                        @elseif ($product->status == 'active')
                                                            <div class="badge badge-light-success">
                                                                {{ $cloneProduct->status }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center pe-0" data-order="22">
                                                        <span class="fw-bold"></span>
                                                    </td>
                                                    <td class="text-center pe-0" data-order="22">
                                                        <span class="fw-bold"></span>
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
            @include('layouts.footer')
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
    @endsection
