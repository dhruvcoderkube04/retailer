@extends('layouts.base')

@section('title')
    Wholesaler Product List | TechtrendMart
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-4 py-lg-4">
                <div id="kt_app_toolbar_container"
                    class="app-container w-100 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center px-4 gap-4">

                    {{-- Page Title --}}
                    <div class="page-title">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">
                            Wholesaler Products List
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 my-0 pt-1">
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
                            <a href="{{ route('retailer.wholesaler.list') }}" class="btn btn-primary">Subscribe Wholesaler
                                Product</a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container ">
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
                        <div class="card-body pt-3">
                            <div class="pb-5">
                                <div class="row g-5 justify-content-md-end">

                                    {{-- Wholesaler Dropdown --}}
                                    <div class="col-12 col-md-3">
                                        <label for="wholesaler_filter"
                                            class="form-label fw-semibold mb-1">Wholesaler</label>
                                        <select id="wholesaler_filter" class="form-select form-select-solid bg-secondary"
                                            data-control="select2" data-placeholder="Select Wholesaler">
                                            <option value="all">All Wholesaler</option>
                                            @foreach ($wholesalers as $wholesaler)
                                                <option value="{{ $wholesaler->id }}">
                                                    {{ $wholesaler->userDetail->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Sub Category Filter --}}
                                    <div class="col-12 col-md-3">
                                        <label for="sub_category_filter" class="form-label fw-semibold mb-1">Sub
                                            Category</label>
                                        <select id="sub_category_filter" class="form-select form-select-solid bg-secondary"
                                            data-control="select2" data-placeholder="Select Sub Category">
                                            <option value="all">All Sub Category</option>
                                            @foreach ($sub_category_list as $sub_category)
                                                <option value="{{ $sub_category->id }}">
                                                    {{ $sub_category->sub_category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Status Filter --}}
                                    <div class="col-12 col-md-3">
                                        <label for="status_filter" class="form-label fw-semibold mb-1">Status</label>
                                        <select id="status_filter" class="form-select form-select-solid bg-secondary "
                                            data-control="select2" data-placeholder="Select Status">
                                            <option value="all">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>

                                    {{-- Stock Filter --}}
                                    <div class="col-12 col-md-3">
                                        <label for="stock_filter" class="form-label fw-semibold mb-1">Stock</label>
                                        <select id="stock_filter" class="form-select form-select-solid bg-secondary "
                                            data-control="select2" data-placeholder="Select Status">
                                            <option value="all">All Stock</option>
                                            <option value="available">Available</option>
                                            <option value="unavailable">Unavailable</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="pb-5">
                                <div class="row g-3 justify-content-md-end">
                                    {{-- Search Input --}}
                                    <div class="col-12 col-md-3">
                                        <label for="search_input" class="form-label fw-semibold mb-1">Search</label>
                                        <div class="position-relative">
                                            <i
                                                class="ki-duotone ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-muted">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <input type="text" id="search_input"
                                                class="form-control form-control-solid ps-12 bg-secondary"
                                                placeholder="Search Product" />
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- Margin Added Products Table --}}
                            <table class="table align-middle table-row-dashed fs-7 table-striped" id="kt_datatable_margin_added_products">
                                <thead>
                                    <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center border-0 py-5 align-middle min-w-70px" style="background: #0d0e12;color:#fff !important;">Image</th>
                                        <th class="text-center border-0 py-5 align-middle min-w-200px" style="background: #0d0e12;color:#fff !important;">Product</th>
                                        <th class="text-center border-0 py-5 align-middle min-w-150px" style="background: #0d0e12;color:#fff !important;">Wholesaler</th>
                                        <th class="text-center border-0 py-5 align-middle min-w-100px" style="background: #0d0e12;color:#fff !important;">Sub Category</th>
                                        <!-- <th class="text-center border-0 py-5 align-middle min-w-70px">Qty</th> -->
                                        <th class="text-center border-0 py-5 align-middle min-w-70px" style="background: #0d0e12;color:#fff !important;">Stock</th>
                                        <th class="text-center border-0 py-5 align-middle min-w-100px" style="background: #0d0e12;color:#fff !important;">Price</th>
                                        <th class="text-center border-0 py-5 align-middle min-w-100px" style="background: #0d0e12;color:#fff !important;">Margin</th>
                                        <th class="text-center border-0 py-5 align-middle min-w-100px" style="background: #0d0e12;color:#fff !important;">Status</th>
                                        <th class="text-center border-0 py-5 align-middle min-w-70px" style="background: #0d0e12;color:#fff !important;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-700 fs-6">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.footer')
        </div>
@endsection

    @section('script')
        <script>
            //<------------- START : server-side datatable for margin added products ------------->
            dataTable = $('#kt_datatable_margin_added_products').DataTable({
                pageLength: 20,
                lengthMenu: [10, 20, 50, 100],
                processing: true,
                serverSide: true,
                fixedHeader: {
                    header: true,
                    headerOffset: document.querySelector("#kt_app_header_wrapper").offsetHeight // height of your fixed header
                },
                ajax: {
                    url: "{{ route('retailer.wholesalers-product.fetch-record') }}",
                    type: "POST",
                    data: function (d) {
                        d.search = $('#search_input').val();
                        d.wholesaler_filter = $('#wholesaler_filter').val();
                        d.sub_category_filter = $('#sub_category_filter').val();
                        d.status_filter = $('#status_filter').val();
                        d.stock_filter = $('#stock_filter').val();
                        d._token = '{{ csrf_token() }}';
                        d.order = d.order; // Add order data
                        d.columns = d.columns; // Add columns data
                    },
                    dataSrc: function (json) {
                        return json.data;
                    },
                    error: function (xhr) {
                        // Detect 400 Bad Request from Laravel
                        if (xhr.status === 400) {
                            let message = 'An error occurred.';
                            try {
                                let response = JSON.parse(xhr.responseText);
                                message = response.message || message;
                            } catch (e) {
                                message = xhr.responseText || message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Search',
                                text: message,
                            }).then(() => {
                                // Clear only the search input field that caused it
                                $('#search_input').val('');
                                // You can choose to comment this out to prevent auto-refresh
                                dataTable.search('').draw();
                            });
                        }
                    }

                },
                order: [],
                columns: [

                {
                    data: 'image',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'product',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'wholesaler',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'sub_category',
                    className: 'text-center',
                    orderable: false,
                },
                // {
                //     data: 'quantity',
                //     className: 'text-center',
                //     orderable: false,
                // },
                {
                    data: 'stock',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'new_price',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'margin',
                    className: 'text-center',
                    orderable: true,
                },
                {
                    data: 'status',
                    className: 'text-center',
                    orderable: false,
                },
                 {
                    data: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                },
                ]
            });

            $('#search_input').on('keyup', function () {
                dataTable.ajax.reload();
            });

            $('#wholesaler_filter').on('change', function () {
                dataTable.ajax.reload();
            });

            $('#sub_category_filter').on('change', function () {
                dataTable.ajax.reload();
            });

            $('#stock_filter').on('change', function () {
                dataTable.ajax.reload();
            });

            $('#status_filter').on('change', function () {
                dataTable.ajax.reload();
            });
            //<------------- END : server-side datatable for margin added products ------------->

            $(document).ready(function () {
                //<-------- START : change product status from product-list ----------->
                $(document).on('change', '.changeStatusToggle', function () {
                    let productId = $(this).data('product-id');
                    let wholesalerId = $(this).data('wholesaler-id');
                    let subCategoryId = $(this).data('sub-category-id');
                    let margin = $(this).data('margin');
                    let paymentMethod = $(this).data('payment-method');
                    let newStatus = $(this).is(':checked') ? 'active' : 'inactive';

                    $.ajax({
                        url: "{{ route('retailer.my.wholesaler.product.change-status') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId,
                            wholesaler_id: wholesalerId,
                            sub_category_id: subCategoryId,
                            margin: margin,
                            payment_method: paymentMethod,
                            status: newStatus
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Updated',
                                text: response.message,
                                timer: 1200,
                                showConfirmButton: false
                            });

                            $('#kt_datatable_margin_added_products')
                                .DataTable().ajax
                                .reload(null, false);
                        },
                        error: function (xhr) {
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
                $(document).on('click', '.remove-wholesaler-product', function () {
                    let productId = $(this).data("id");
                    let wholesalerId = $(this).data("wholesaler-id");
                    let subCategoryId = $(this).data("sub-category-id");

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
                                url: "{{ route('retailer.my.wholesaler.product.remove') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    _method: "DELETE",
                                    product_id: productId,
                                    wholesaler_id: wholesalerId,
                                    sub_category_id: subCategoryId,
                                },
                                success: function (response) {
                                    Swal.fire({
                                        icon: response.status ? 'success' : 'error',
                                        title: response.status ? 'Deleted!' :
                                            'Error',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    if (response.status) {
                                        $('#kt_datatable_margin_added_products')
                                            .DataTable().ajax
                                            .reload(null, false);
                                    }
                                },
                                error: function (xhr) {
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
