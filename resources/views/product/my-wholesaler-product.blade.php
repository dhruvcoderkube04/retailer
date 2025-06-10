@extends('layouts.base')

@section('title')
    Wholesaler Product List | TechTrendMart
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
                            Wholesaler Products List
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
                            <a href="{{ route('retailer.wholesaler.list') }}" class="btn btn-primary">Subscribe Wholesaler Product</a>
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
                        <div class="card-body pt-5">
                            {{-- Margin Added Products Table --}}
                            <table class="table align-middle table-row-dashed fs-7"
                                id="kt_datatable_margin_added_products">
                                <thead>
                                    <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center align-middle min-w-70px">Actions</th>
                                        <th class="text-center align-middle min-w-250px">Product</th>
                                        <th class="text-center align-middle min-w-150px">Wholesaler</th>
                                        <th class="text-center align-middle min-w-100px">SKU</th>
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
                    });
                }
            });
            //<------------- END : server-side datatable for margin added products ------------->

        </script>
    @endsection
