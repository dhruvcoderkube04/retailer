@extends('layouts.base')
@section('title')
    Retailer's Product List | TrendMart
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
                            Products List
                        </h1>
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

                    <div class="w-100 w-md-auto d-flex flex-column flex-md-row gap-3">
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
                        <div class="card-body pt-5">
                            {{-- tabs --}}
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                <li class="nav-item">
                                    <a class="nav-link fw-bold pb-4" data-bs-toggle="tab" href="#kt_tab_pane_1"
                                        data-tab="1">Wholesaler
                                        Products</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold pb-4" data-bs-toggle="tab" href="#kt_tab_pane_2"
                                        data-tab="2">My
                                        Products</a>
                                </li>
                            </ul>

                            {{-- tab contents --}}
                            <div class="tab-content" id="myTabContent">

                                {{-- margin added products --}}
                                <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                    <table class="table align-middle table-row-dashed fs-7"
                                        id="kt_datatable_margin_added_products">
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

                                        </tbody>
                                    </table>
                                </div>

                                {{-- clone products --}}
                                <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                    <table class="table align-middle table-row-dashed fs-7"
                                        id="kt_datatable_retailer_clone_products">
                                        <thead>
                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="text-center align-middle min-w-70px">Actions</th>
                                                <th class="text-center align-middle min-w-200px">Product</th>
                                                <th class="text-center align-middle min-w-150px">SKU</th>
                                                <th class="text-center align-middle min-w-100px">Sub Catgory</th>
                                                <th class="text-center align-middle min-w-100px">New Price
                                                    <br> <span class="text-capitalize fs-9">(Per Pis)</span>
                                                </th>
                                                <th class="text-center align-middle min-w-100px">Status</th>
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
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Product File</label>
                                    <input type="file" class="form-control" name="product_file" id="product_file">
                                    <span class="invalid-feedback d-block" id="product_file_error"></span>
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>

                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Sub Category Name</label>
                                    <div class="mb-6 fv-row">
                                        <select class="form-select mb-2" data-control="select2" name="sub_category"
                                            id="sub_category" data-placeholder="Select an option">
                                            <option></option>
                                            @foreach ($sub_category_list as $sub_category)
                                                <option value="{{ $sub_category->id }}">
                                                    {{ Str::upper($sub_category->sub_category_name) }}</option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback d-block" id="sub_category_error"></span>
                                    </div>
                                </div>

                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" style="border: 1px solid rgb(192, 192, 192)" type="checkbox" name="images_and_video_update"
                                            id="images_and_video_update" value="1">
                                        <span class="form-label ms-4 mt-3" for="images_and_video_update">
                                            Want to update images & videos?
                                        </span>
                                    </label>
                                </div>

                                <div class="mb-10 fv-row">
                                    <a href="{{ route('retailer.download-stock-sample') }}">Download Sample Product
                                        File </a>
                                    <p class="text-danger">(Only accepted .xlsx formate)</p>
                                </div>
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
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
        <script>
            //<------------- START : server-side transaction datatable ------------->
            dataTable1 = $('#kt_datatable_margin_added_products').DataTable({
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
                    url: "{{ route('retailer.wholesalers-product.fetch-record') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.order = d.order; // Add order data
                        d.columns = d.columns; // Add columns data
                    },
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                order: [],
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'product',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'wholesaler',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'sku',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'new_price',
                        className: 'text-end',
                        orderable: true,
                    },
                    {
                        data: 'margin',
                        className: 'text-end',
                        orderable: true,
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        orderable: false,
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

            dataTable2 = $('#kt_datatable_retailer_clone_products').DataTable({
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
                    url: "{{ route('retailer.retailer-clone-product.fetch-record') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.order = d.order; // Add order data
                        d.columns = d.columns; // Add columns data
                    },
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                order: [],
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'product',
                        className: 'text-start',
                        orderable: false,
                    },
                    {
                        data: 'sku',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'sub_category',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'new_price',
                        className: 'text-end',
                        orderable: true,
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        orderable: false,
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
                //<----------------- START : product upload form submit ---------------->
                $('#kt_modal_add_product').on('shown.bs.modal', function() {
                    $('[data-control="select2"]').select2({
                        dropdownParent: $('#kt_modal_add_product'),
                        placeholder: 'Select an option',
                        allowClear: true
                    });
                });

                $(document).on('submit', '#productUploadForm', function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);
                    let stockfile = $("input[name='product_file']")[0].files[0];
                    let subCategoryId = $("select[name='sub_category']").val();
                    let submitButton = $(this).find("button[type='submit']");

                    // Clear previous validation states
                    $('#product_file_error').text('');
                    $('#sub_category_error').text('');
                    $('input[name="product_file"]').removeClass('is-invalid');
                    $('select[name="sub_category"]').removeClass('is-invalid');
                    $('#sub_category').next('.select2-container').find('.select2-selection').removeClass(
                        'border border-danger');

                    formData.append("sub_category", subCategoryId);

                    submitButton.prop("disabled", true);
                    submitButton.find(".indicator-label").hide();
                    submitButton.find(".indicator-progress").show();

                    $.ajax({
                        url: "{{ url('upload-bulk-product') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Product Import Successful!'
                            });
                            location.reload();
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let response = xhr.responseJSON;

                                if (response.errors) {
                                    // Laravel validator errors
                                    if (response.errors.product_file) {
                                        $('#product_file_error').text(response.errors.product_file[
                                            0]);
                                        $('input[name="product_file"]').addClass('is-invalid');
                                    }
                                    if (response.errors.sub_category) {
                                        $('#sub_category_error').text(response.errors.sub_category[
                                            0]);
                                        $('select[name="sub_category"]').addClass('is-invalid');
                                        $('#sub_category').next('.select2-container')
                                            .find('.select2-selection')
                                            .addClass('border border-danger');
                                    }
                                }

                                if (response.error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Validation Error',
                                        html: response.error,
                                        customClass: {
                                            popup: 'swal2-danger'
                                        }
                                    });
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Server Error!',
                                    text: 'Something went wrong. Please try again.',
                                });
                            }
                        },
                        complete: function() {
                            submitButton.prop("disabled", false);
                            submitButton.find(".indicator-label").show();
                            submitButton.find(".indicator-progress").hide();
                        }
                    });
                });
                //<----------------- END : product upload form submit ---------------->

                //<----------------- START : delete product ---------------->
                $(document).on('click', '.delete-product', function() {
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
