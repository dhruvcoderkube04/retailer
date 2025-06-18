@extends('layouts.base')
@section('title')
    Wholesaler List | TrendMart
@endsection
@section('content')
    @if ($is_all_wholesaler_visible)
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                    <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <h1
                                class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                                Wholesalers
                            </h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ route('retailer.dashboard') }}"
                                        class="text-muted text-hover-primary">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                </li>
                                <li class="breadcrumb-item text-muted">Wholesaler List</li>
                            </ul>
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
                            <div class="alert alert-danger text-green-600 p-2">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-body pt-4">
                                <table class="table align-middle table-row-dashed fs-7"
                                    id="kt_datatable_wholesaler_list">
                                    <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="text-center align-middle min-w-100px">Action</th>
                                            <th class="text-center align-middle min-w-50px"></th>
                                            <th class="text-center align-middle min-w-100px">Wholesaler</th>
                                            <th class="text-center align-middle min-w-200px">Name</th>
                                            <th class="text-center align-middle min-w-80px">Details</th>
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
        </div>
    @else
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div id="kt_app_content_container" class="app-container ">
                        <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10">
                            <i class="ki-duotone ki-message-text-2 fs-2hx text-primary me-4 mt-2 mb-5 mb-sm-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column ps-3 m-1 pe-sm-10">
                                <h4 class="fw-semibold">No Access</h4>
                                <p class="mb-2">Unfortunately, you do not have the required access to use this facility.
                                </p>
                                <p>If you believe you should have access, please contact your administrator for further
                                    assistance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        //<------------- START : server-side transaction datatable ------------->
        dataTable = $('#kt_datatable_wholesaler_list').DataTable({
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
                url: "{{ route('retailer.wholesaler.fetch-record') }}",
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
            columns: [
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'company_logo',
                    className: 'text-end',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'company_name',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'wholesaler_name',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'details',
                    className: 'text-center',
                    orderable: false,
                },
            ],
            initComplete: function() {
                let searchBox = $('.datatable-search-section input');
                let searchLabel = $('.datatable-search-section label');
                let lengthSelect = $('.datatable-length-section select');

                searchBox.wrap(
                    '<div class="d-flex align-items-center position-relative my-1 w-100"></div>'
                );
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
    </script>
@endsection
