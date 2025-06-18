@extends('layouts.base')

@section('title')
    My Product List | TechTrendMart
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
                            My Products List
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">My Product List</li>
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
                        <div class="alert alert-danger text-red-600 p-2">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="card card-flush">
                        <div class="card-body pt-1">

                            {{-- tabs --}}
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                <li class="nav-item">
                                    <a class="nav-link fw-bold pb-4 active" data-bs-toggle="tab"
                                        href="#available_products_tab" data-tab="1">
                                        Available Products
                                        <span class="badge badge-sm badge-circle badge-light-success fs-6 p-2 ms-2"
                                            id="available_products_count">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold pb-4" data-bs-toggle="tab" href="#unavailable_products_tab"
                                        data-tab="2">
                                        Unavailable Products
                                        <span class="badge badge-sm badge-circle badge-light-danger fs-6 p-2 ms-2"
                                            id="unavailable_products_count">0</span>
                                    </a>
                                </li>
                            </ul>

                            {{-- tab 1 --}}
                            <div class="tab-content" id="available_products_tab_content">
                                <div class="tab-pane fade show active" id="available_products_tab" role="tabpanel">

                                    <div class="pb-6">
                                        <div class="row g-3 justify-content-md-end">

                                            {{-- Status Filter --}}
                                            <div class="col-12 col-md-3">
                                                <label for="available_status_filter"
                                                    class="form-label fw-semibold mb-1">Status</label>
                                                <select id="available_status_filter"
                                                    class="form-select form-select-solid bg-secondary w-100"
                                                    data-control="select2" data-placeholder="Select Status">
                                                    <option value="all">All Status</option>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>

                                            {{-- Category Dropdown --}}
                                            <div class="col-12 col-md-3">
                                                <label for="available_sub_category_filter"
                                                    class="form-label fw-semibold mb-1">Sub Category</label>
                                                <select id="available_sub_category_filter"
                                                    class="form-select form-select-solid bg-secondary w-100"
                                                    data-control="select2" data-placeholder="Select Category">
                                                    <option value="all">All Category</option>
                                                    @foreach ($sub_category_filter as $sub_category)
                                                        <option value="{{ $sub_category->id }}">
                                                            {{ $sub_category->sub_category_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Search Input --}}
                                            <div class="col-12 col-md-3">
                                                <label for="available_search_product"
                                                    class="form-label fw-semibold mb-1">Search</label>
                                                <div class="position-relative">
                                                    <i
                                                        class="ki-duotone ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-muted">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <input type="text" id="available_search_product"
                                                        class="form-control form-control-solid ps-12 bg-secondary"
                                                        placeholder="Search Product" />
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <table class="table align-middle table-row-dashed fs-7"
                                        id="kt_datatable_available_retailer_clone_products">
                                        <thead>
                                            <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="text-center align-middle min-w-100px">Actions</th>
                                                <th class="text-center align-middle min-w-70px">Image</th>
                                                <th class="text-center align-middle min-w-200px">Product</th>
                                                <th class="text-center align-middle min-w-100px">Category</th>
                                                <th class="text-center align-middle min-w-100px">Price</th>
                                                <th class="text-center align-middle min-w-50px">Qty</th>
                                                <th class="text-center align-middle min-w-70px"
                                                    style="white-space: normal;">Stock</th>
                                                <th class="text-center align-middle min-w-70px"
                                                    style="white-space: normal;">Status</th>
                                                <th class="text-center align-middle min-w-150px">Added / Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">

                                        </tbody>
                                    </table>

                                </div>
                            </div>

                            {{-- tab 2 --}}
                            <div class="tab-content" id="unavailable_products_tab_content">
                                <div class="tab-pane fade" id="unavailable_products_tab" role="tabpanel">

                                    <div class="pb-6">
                                        <div class="row g-3 justify-content-md-end">

                                            {{-- Status Filter --}}
                                            <div class="col-12 col-md-3">
                                                <label for="unavailable_status_filter"
                                                    class="form-label fw-semibold mb-1">Status</label>
                                                <select id="unavailable_status_filter"
                                                    class="form-select form-select-solid bg-secondary w-100"
                                                    data-control="select2" data-placeholder="Select Status">
                                                    <option value="all">All Status</option>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>

                                            {{-- Category Dropdown --}}
                                            <div class="col-12 col-md-3">
                                                <label for="unavailable_sub_category_filter"
                                                    class="form-label fw-semibold mb-1">Sub Category</label>
                                                <select id="unavailable_sub_category_filter"
                                                    class="form-select form-select-solid bg-secondary w-100"
                                                    data-control="select2" data-placeholder="Select Category">
                                                    <option value="all">All Category</option>
                                                    @foreach ($sub_category_filter as $sub_category)
                                                        <option value="{{ $sub_category->id }}">
                                                            {{ $sub_category->sub_category_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Search Input --}}
                                            <div class="col-12 col-md-3">
                                                <label for="unavailable_search_product"
                                                    class="form-label fw-semibold mb-1">Search</label>
                                                <div class="position-relative">
                                                    <i
                                                        class="ki-duotone ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-muted">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <input type="text" id="unavailable_search_product"
                                                        class="form-control form-control-solid ps-12 bg-secondary"
                                                        placeholder="Search Product" />
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <table class="table align-middle table-row-dashed fs-7"
                                        id="kt_datatable_unavailable_retailer_clone_products">
                                        <thead>
                                            <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="text-center align-middle min-w-100px">Actions</th>
                                                <th class="text-center align-middle min-w-70px">Image</th>
                                                <th class="text-center align-middle min-w-200px">Product</th>
                                                <th class="text-center align-middle min-w-100px">Category</th>
                                                <th class="text-center align-middle min-w-100px">Price</th>
                                                <th class="text-center align-middle min-w-50px">Qty</th>
                                                <th class="text-center align-middle min-w-70px"
                                                    style="white-space: normal;">Stock</th>
                                                <th class="text-center align-middle min-w-70px"
                                                    style="white-space: normal;">Status</th>
                                                <th class="text-center align-middle min-w-150px">Added / Updated</th>
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

            {{-- Bulk Product Upload Modal --}}
            <div class="modal fade" id="kt_modal_add_product" tabindex="-1" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="fw-bold">Upload Product File</h2>
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
                                {{-- <div class="fv-row mb-7 fv-plugins-icon-container">
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" style="border: 1px solid rgb(192, 192, 192)"
                                            type="checkbox" name="images_and_video_update" id="images_and_video_update"
                                            value="1">
                                        <span class="form-label ms-4 mt-3" for="images_and_video_update">
                                            Want to update images & videos?
                                        </span>
                                    </label>
                                </div> --}}
                                <div class="mb-10 fv-row">
                                    <a href="{{ route('retailer.download-stock-sample') }}">Download Sample Product
                                        File</a>
                                    <p class="text-danger">(Only accepted .xlsx format)</p>
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
            // <--------------------- START : Available Product ---------------------->
            let availableDatatable = $('#kt_datatable_available_retailer_clone_products').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('retailer.retailer-clone-available-product.fetch-record') }}",
                    type: "POST",
                    data: function(d) {
                        d.search = $('#available_search_product').val();
                        d.sub_category_filter = $('#available_sub_category_filter').val();
                        d.status = $('#available_status_filter').val();
                        d._token = '{{ csrf_token() }}';
                        d.order = d.order; // Add order data
                        d.columns = d.columns; // Add columns data
                    },
                    dataSrc: function(json) {
                        $('#available_products_count').text(json.recordsTotal);
                        return json.data;
                    }
                },
                order: [],
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
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
                        orderable: false,
                    },
                    {
                        data: 'quantity',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'stock',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'created_updated_at',
                        className: 'text-center',
                        orderable: false,
                    },
                ]
            });

            $('#available_search_product').on('keyup', function() {
                availableDatatable.ajax.reload();
            });

            $('#available_sub_category_filter').on('change', function() {
                availableDatatable.ajax.reload();
            });

            $('#available_status_filter').on('change', function() {
                availableDatatable.ajax.reload();
            });

            // Re-render icons after availableDatatable draw
            availableDatatable.on('draw', function() {
                if (typeof KTIcon !== 'undefined') {
                    KTIcon.update();
                }
            });
            // <--------------------- END : Available Product ---------------------->

            // <--------------------- START : Unavailable Product ---------------------->
            let unavailableDatatable = $('#kt_datatable_unavailable_retailer_clone_products').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('retailer.retailer-clone-unavailable-product.fetch-record') }}",
                    type: "POST",
                    data: function(d) {
                        d.search = $('#unavailable_search_product').val();
                        d.sub_category_filter = $('#unavailable_sub_category_filter').val();
                        d.status = $('#unavailable_status_filter').val();
                        d._token = '{{ csrf_token() }}';
                        d.order = d.order; // Add order data
                        d.columns = d.columns; // Add columns data
                    },
                    dataSrc: function(json) {
                        $('#unavailable_products_count').text(json.recordsTotal);
                        return json.data;
                    }
                },
                order: [],
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
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
                        orderable: false,
                    },
                    {
                        data: 'quantity',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'stock',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'created_updated_at',
                        className: 'text-center',
                        orderable: false,
                    },
                ]
            });

            $('#unavailable_search_product').on('keyup', function() {
                unavailableDatatable.ajax.reload();
            });

            $('#unavailable_sub_category_filter').on('change', function() {
                unavailableDatatable.ajax.reload();
            });

            $('#unavailable_status_filter').on('change', function() {
                unavailableDatatable.ajax.reload();
            });

            // Re-render icons after unavailableDatatable draw
            unavailableDatatable.on('draw', function() {
                if (typeof KTIcon !== 'undefined') {
                    KTIcon.update();
                }
            });
            // <--------------------- END : Unavailable Product ---------------------->

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
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    timer: 2000,
                                }).then((result) => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let response = xhr.responseJSON;

                                // laravel validator errors
                                if (response.errors) {
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

                                // manual errors like missing, already exist, etc
                                if (response.error) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Import Completed With Errors',
                                        html: `<strong>${response.message}</strong><br>${response.error}`,
                                        width: '800px',
                                        customClass: {
                                            popup: 'swal2-danger'
                                        }
                                    }).then((result) => {
                                        if (response.reload) {
                                            location.reload();
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

                //<-------- START : change product status from product-list ----------->
                $(document).on('change', '.changeStatusToggle', function() {
                    let productId = $(this).data('id');
                    let newStatus = $(this).is(':checked') ? 'active' : 'inactive';

                    $.ajax({
                        url: "{{ route('retailer.retailer-clone-product.change-status') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId,
                            status: newStatus
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Updated',
                                text: response.message,
                                timer: 1200,
                                showConfirmButton: false
                            });

                            $('#kt_datatable_available_retailer_clone_products')
                                .DataTable().ajax
                                .reload(null, false);
                            $('#kt_datatable_unavailable_retailer_clone_products')
                                .DataTable().ajax
                                .reload(null, false);
                        },
                        error: function(xhr) {
                            let errorMsg = 'Could not update status.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }

                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
                //<-------- END : change product status from product-list ----------->

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
                                    Swal.fire({
                                        icon: response.status ? 'success' : 'error',
                                        title: response.status ? 'Deleted!' :
                                            'Error',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    if (response.status) {
                                        $('#kt_datatable_available_retailer_clone_products')
                                            .DataTable().ajax
                                            .reload(null,
                                                false
                                            ); // reload table without resetting pagination
                                        $('#kt_datatable_unavailable_retailer_clone_products')
                                            .DataTable().ajax
                                            .reload(
                                                null, false
                                            ); // reload table without resetting pagination
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Oops...', 'Something went wrong.', 'error');
                                }
                            });
                        }
                    });
                });
                //<----------------- END : delete product ---------------->
            });
        </script>
    @endsection
