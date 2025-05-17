@extends('layouts.base')
@section('title')
    Retailer's Product List | TrendMart
@endsection
@section('content')

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Products List</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
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
                                        class="form-control form-control-solid w-250px ps-12" placeholder="Search Product"
                                        id="search_field" />
                                </div>
                            </div>
                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                <button type="button" class="btn btn-flex btn-light-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_add_product">
                                    <i class="ki-duotone ki-plus-square fs-3"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span></i>
                                    Bulk Product Upload
                                </button>
                                <a href="{{ route('retailer.add.product') }}" class="btn btn-primary">Add Product</a>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            {{-- tabs --}}
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_1" data-tab="1">Wholesaler
                                        Products</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2" data-tab="2">My
                                        Products</a>
                                </li>
                            </ul>

                            {{-- tab contents --}}
                            <div class="tab-content" id="myTabContent">

                                {{-- margin added products --}}
                                <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="kt_margin_added_products_table">
                                        <thead>
                                            <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="text-center align-middle min-w-70px">Actions</th>
                                                <th class="text-center align-middle min-w-200px">Product</th>
                                                <th class="text-center align-middle min-w-150px">Wholesaler</th>
                                                <th class="text-center align-middle min-w-150px">SKU</th>
                                                <th class="text-center align-middle min-w-100px">
                                                    New Price <br>
                                                    <span class="text-capitalize fs-9">(Per Piece)</span>
                                                </th>
                                                <th class="text-center align-middle min-w-100px">
                                                    Margin <br>
                                                    <span class="text-capitalize fs-9">(In Rs.)</span>
                                                </th>
                                                <th class="text-center align-middle min-w-100px">Status</th>
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
                                                                <div class="d-flex align-items-center">
                                                                    <div class="symbol symbol-50px">
                                                                        @php
                                                                            $get_image =
                                                                                explode(',', @$product->images)[0] ??
                                                                                '';
                                                                        @endphp
                                                                        <span class="symbol-label"
                                                                            style="background-image: url('{{ $get_image }}');"></span>
                                                                    </div>
                                                                    <div class="ms-5">
                                                                        <div class="text-gray-800  fs-5 fw-bold"
                                                                            data-kt-ecommerce-product-filter="product_name">
                                                                            {{ ucfirst($product->name) ?? 'N/A' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="ms-5">
                                                                    <a href="{{ route('retailer.view-category-margin', $product->wholesaler->id) }}"
                                                                        class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                                                        data-kt-ecommerce-product-filter="product_name">{{ ucfirst($retailerProduct?->wholesaler?->userDetail?->company_name) ?? 'N/A' }}</a>
                                                                </div>
                                                            </td>
                                                            <td class="text-center pe-0" data-order="22">
                                                                <span class="fw-bold">{{ $product->sku ?? 'N/A' }}</span>
                                                            </td>
                                                            <td class="text-center" data-order="22">
                                                                <div class="badge badge-light-primary">
                                                                    {{ $product->new_price ? '₹ ' . $product->new_price : 'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td class="text-center pe-0" data-order="rating-4">
                                                                <div class="badge badge-light-info">
                                                                    {{ $retailerProduct->margin ? '₹ ' . $retailerProduct->margin : 'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td class="text-center" data-order="Inactive">
                                                                @if ($product->status == 'inactive')
                                                                    <div class="badge badge-light-danger">
                                                                        {{ ucfirst($product->status) }}
                                                                    </div>
                                                                @elseif ($product->status == 'active')
                                                                    <div class="badge badge-light-success">
                                                                        {{ ucfirst($product->status) }}
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
                                        id="kt_clone_products_table">
                                        <thead>
                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="text-center align-middle min-w-70px">Actions</th>
                                                <th class="text-center align-middle min-w-200px">Product</th>
                                                <th class="text-center align-middle min-w-150px">SKU</th>
                                                <th class="text-center align-middle min-w-100px">Catgory</th>
                                                <th class="text-center align-middle min-w-100px">New Price
                                                    <br> <span class="text-capitalize fs-9">(Per Pis)</span>
                                                </th>
                                                <th class="text-center align-middle min-w-100px">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                            @foreach ($retailerCloneProducts as $cloneProduct)
                                                <tr>
                                                    <td
                                                        class="text-center d-flex justify-content-center align-items-center gap-2">
                                                        <button type="button"
                                                            class="btn btn-icon btn-danger btn-active-light-danger w-30px h-30px delete-product"
                                                            data-id="{{ $cloneProduct->id }}">
                                                            <i class="ki-duotone ki-trash fs-3">
                                                                <span class="path1"></span><span
                                                                    class="path2"></span><span class="path3"></span>
                                                                <span class="path4"></span><span class="path5"></span>
                                                            </i>
                                                        </button>
                                                        <a href="{{ route('retailer.edit.product', $cloneProduct->id) }}"
                                                            title="Edit"
                                                            class="btn btn-icon btn-primary btn-active-light-primary w-30px h-30px">
                                                            <i class="ki-duotone ki-pencil fs-4">
                                                                <span class="path1"></span><span
                                                                    class="path2"></span><span class="path3"></span>
                                                                <span class="path4"></span><span class="path5"></span>
                                                            </i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <a href="#" class="symbol symbol-50px">
                                                                @php
                                                                    $imageUrls = explode(',', @$cloneProduct->images);
                                                                    $firstImageUrl = $imageUrls[0] ?? ''; // Get the first image URL
                                                                @endphp
                                                                <span class="symbol-label"
                                                                    style="background-image: url('{{ $firstImageUrl }}');"></span>
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
                                                        {{ @$cloneProduct->sub_category->sub_category_name }}
                                                    </td>
                                                    <td class="text-center" data-order="22">
                                                        <div class="badge badge-light-primary">
                                                            {{ $cloneProduct->new_price }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center" data-order="Inactive">
                                                        <div
                                                            class="badge {{ $cloneProduct->status == 'inactive' ? 'badge-light-danger' : 'badge-light-success' }}">
                                                            {{ ucfirst($cloneProduct->status) }}
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

            <div class="modal fade" id="kt_modal_add_product" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="fw-bold">Upload Product File </h2>
                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                            </div>
                        </div>
                        <div class="modal-body scroll-y mx-5 mx-xl-7 my-3">
                            <form id="productUploadForm" class="form" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Product File </span>
                                        <span class="ms-2" data-bs-toggle="tooltip"
                                            aria-label="The invoice number must be unique."
                                            data-bs-original-title="The invoice number must be unique."
                                            data-kt-initialized="1">
                                            <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                                    class="path2"></span><span class="path3"></span></i>
                                        </span>
                                    </label>
                                    <input type="file" class="form-control form-control-solid" name="product_file">
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Category Name</span>
                                        <span class="ms-2" data-bs-toggle="tooltip"
                                            aria-label="The invoice number must be unique."
                                            data-bs-original-title="The invoice number must be unique."
                                            data-kt-initialized="1">
                                            <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                                    class="path2"></span><span class="path3"></span></i>
                                        </span>
                                    </label>
                                    <div class="mb-6 fv-row">
                                        <select class="form-select mb-2 @error('categories') is-invalid @enderror"
                                            data-control="select2" name="categories" data-placeholder="Select an option">
                                            @foreach ($category_list as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ Str::upper($category->category_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-10 fv-row">
                                        <a href="{{ route('retailer.download-stock-sample') }}">Download Sample Product
                                            File </a>
                                        <p style="color: red">(Only excepted .xlsx formate)</p>
                                    </div>
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="reset" class="btn btn-light me-3"
                                        data-bs-dismiss="modal">Discard</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Upload</span>
                                        <span class="indicator-progress">Please wait... <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- add product modal --}}
            <div class="modal fade" id="kt_modal_add_clone_product" tabindex="-1" style="display: none;"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="fw-bold">Upload Product File </h2>
                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                            </div>
                        </div>
                        <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                            <form id="productUploadForm" class="form" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Product File </span>
                                        <span class="ms-2" data-bs-toggle="tooltip"
                                            aria-label="The invoice number must be unique."
                                            data-bs-original-title="The invoice number must be unique."
                                            data-kt-initialized="1">
                                            <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                                    class="path2"></span><span class="path3"></span></i>
                                        </span>
                                    </label>
                                    <input type="file" class="form-control form-control-solid" name="product_file">
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Category Name</span>
                                        <span class="ms-2" data-bs-toggle="tooltip"
                                            aria-label="The invoice number must be unique."
                                            data-bs-original-title="The invoice number must be unique."
                                            data-kt-initialized="1">
                                            <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span
                                                    class="path2"></span><span class="path3"></span></i>
                                        </span>
                                    </label>
                                    <div class="mb-10 fv-row">
                                        <select class="form-select mb-2 @error('categories') is-invalid @enderror"
                                            data-control="select2" name="categories" data-placeholder="Select an option">
                                            @foreach ($category_list as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ Str::upper($category->category_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="reset" class="btn btn-light me-3"
                                        data-bs-dismiss="modal">Discard</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Upload</span>
                                        <span class="indicator-progress">Please wait... <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.footer')
        </div>
    @endsection

    @section('script')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
        <script>
            // product datatable
            var table1 = $("#kt_margin_added_products_table").DataTable({
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }]
            });
            $("#search_field").on("keyup", function() {
                table1.search(this.value).draw();
            });

            // clone product datatable
            var table2 = $("#kt_clone_products_table").DataTable({
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }]
            });
            $("#search_field").on("keyup", function() {
                table2.search(this.value).draw();
            });


            $(document).ready(function() {
                //<----------------- START : product upload form submit ---------------->
                $("#productUploadForm").submit(function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);

                    let stockfile = $("input[name='product_file']")[0].files[0];
                    let categoryId = $("select[name='categories']").val(); // Correct selector
                    let submitButton = $(this).find("button[type='submit']");

                    if (!stockfile) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please select an Excel (.xlsx) file!'
                        });
                        return;
                    }

                    if (stockfile.type !==
                        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Type!',
                            text: 'Only .xlsx files are allowed.'
                        });
                        return;
                    }

                    formData.append("categories", categoryId); // Append category to formdata.

                    submitButton.prop("disabled", true);
                    submitButton.find(".indicator-label").hide();
                    submitButton.find(".indicator-progress").show();

                    $.ajax({
                        url: "{{ url('upload-bulk-product') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(mydata) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Product Import Successful!'
                            });
                            $("#kt_margin_added_products_table").load(location.href +
                                " #kt_margin_added_products_table");
                            $("#kt_modal_add_product").modal('hide');
                        },
                        // error: function(mydata) {
                        //     Swal.fire({ icon: 'error', title: 'Product Import Failed!' });
                        // }

                        error: function(mydata) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Product Import Failed!'
                            });
                            let errorMessage = "Product Import Failed!";

                            if (mydata.responseJSON && mydata.responseJSON.error) {
                                errorMessage = mydata.responseJSON
                                    .error; // Show backend error message
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage
                            });
                        },
                        complete: function() {
                            // Enable submit button and reset loading indicator
                            submitButton.prop("disabled", false);
                            submitButton.find(".indicator-label").show();
                            submitButton.find(".indicator-progress").hide();
                        }
                    });
                });
                //<----------------- END : product upload form submit ---------------->

                //<----------------- START : delete product ---------------->
                $(".delete-product").click(function() {
                    let productId = $(this).data("id");

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('retailer.clone-product-remove', '') }}/" +
                                    productId,
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    _method: "DELETE"
                                },
                                success: function(response) {
                                    Swal.fire("Deleted!", "Product has been removed.",
                                        "success");
                                    location
                                        .reload(); // Reload the page or update the table dynamically
                                    $("#kt_tab_pane_1").removeClass(
                                        "active"); // Remove active from all tabs
                                    $("#kt_tab_pane_2").addClass(
                                        "active"); // Add active to Clone tab
                                },
                                error: function(xhr) {
                                    Swal.fire("Error!",
                                        "Something went wrong. Please try again.",
                                        "error");
                                }
                            });
                        }
                    });
                });
                //<----------------- END : delete product ---------------->

                //<----------------- START : active-tab pass on url ---------------->
                // Check if "active-tab" is in the URL, if not, redirect to ?active-tab=1
                let urlParams = new URLSearchParams(window.location.search);
                if (!urlParams.has("active-tab")) {
                    let newUrl = window.location.pathname + "?active-tab=1";
                    window.history.replaceState({}, "", newUrl);
                    urlParams.set("active-tab", "1"); // Set default active tab
                }

                let activeTab = urlParams.get("active-tab");

                // Set active tab on page load
                $(".nav-link").removeClass("active");
                $(`.nav-link[data-tab="${activeTab}"]`).addClass("active");

                // Show corresponding tab content
                $(".tab-pane").removeClass("show active");
                $(`#kt_tab_pane_${activeTab}`).addClass("show active");

                // Update URL on tab click
                $(".nav-link").on("click", function() {
                    let tabValue = $(this).data("tab");
                    let newUrl = new URL(window.location.href);
                    newUrl.searchParams.set("active-tab", tabValue);
                    window.history.pushState({}, "", newUrl);
                });
                //<----------------- END : active-tab pass on url ---------------->
            });
        </script>
    @endsection
