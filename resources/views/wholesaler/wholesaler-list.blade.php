@extends('layouts.base')
@section('title')
    Wholesaler List | TechtrendMart
@endsection
@section('content')
    @if ($is_all_wholesaler_visible === 1)
        {{-- Full Access: Show Wholesalers --}}
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                    <div id="kt_app_toolbar_container" class="app-container d-flex flex-stack">
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Wholesalers</h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
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
                    <div id="kt_app_content_container" class="app-container">
                        @if (session('success'))
                            <div class="alert alert-success p-2">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger p-2">{{ session('error') }}</div>
                        @endif

                        @if ($retailer_sub_category_count <= 0)
                            <div class="text-danger fw-semibold mb-4 mx-2 fs-5">
                                You haven't selected any categories yet. Please select categories to access this feature.
                                <br>
                                <a href="{{ route('retailer.category.list') }}" class="text-danger text-decoration-underline">
                                    Click here
                                </a> to choose your categories.
                            </div>
                        @endif

                        <div class="card">
                                {{-- Sub Category Filter --}}
                                    <div class="row g-3 justify-content-md-start pb-4 mt-2">
                                        <div class="col-12 col-md-3 offset-md-1">
                                            <label for="filter_subcategory" class="form-label fw-semibold mb-1">Sub Category</label>
                                            <select id="filter_subcategory"
                                                    class="form-select form-select-solid bg-secondary"
                                                    data-control="select2"
                                                    data-placeholder="Select Sub Category">
                                                <option value="all">All Sub Category</option>
                                                @foreach($allSubCategories as $subCat)
                                                    <option value="{{ $subCat->id }}">{{ $subCat->sub_category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                <table class="table align-middle table-row-dashed fs-7" id="kt_datatable_wholesaler_list">
                                    <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="text-center align-middle min-w-100px">Action</th>
                                            <th class="text-center align-middle min-w-50px"></th>
                                            <th class="text-center align-middle min-w-100px">Wholesaler</th>
                                            <th class="text-center align-middle min-w-200px">Name</th>
                                            <th class="text-center align-middle min-w-100px">Subcategories</th>
                                            <th class="text-center align-middle min-w-80px">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        {{-- Data will be populated via JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @include('layouts.footer')
            </div>
        </div>

    @elseif ($is_all_wholesaler_visible === 0)
        {{-- Restricted Access: Show Request Access --}}
        <div class="app-main flex-column flex-row-fluid mt-5" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div id="kt_app_content_container" class="app-container">
                        <div
                            class="alert alert-info d-flex flex-column flex-md-row align-items-center justify-content-between p-4 mb-10 rounded-3 shadow-sm">
                            <div class="d-flex align-items-center mb-3 mb-md-0">
                                <i class="bi bi-exclamation-circle-fill text-info fs-1 me-3"></i>
                                <div>
                                    <h4 class="alert-heading fw-bold mb-1">Access Needed</h4>
                                    <p class="mb-0">You currently do not have permission to access this feature. If you believe
                                        this is a mistake, please submit an access request for approval.</p>
                                </div>
                            </div>
                            <input type="hidden" name="user_id" value="{{ $retaile_id }}">
                            <button id="requestAccessBtn" type="button" class="btn btn-outline-info btn-lg fw-semibold">
                                Request Access
                            </button>
                        </div>

                        <div id="accessMessage" class="alert alert-success d-none" role="alert">
                            <strong>Request Submitted!</strong> Your request has been sent to the administrator.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif($is_all_wholesaler_visible === 2)
        <div class="app-main flex-column flex-row-fluid mt-5" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div id="kt_app_content_container" class="app-container">
                        <div class="alert alert-success" role="alert">
                            <strong>Request Submitted!</strong> Your request has been sent to the administrator.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Unknown Access: Fallback --}}
        <div class="app-main flex-column flex-row-fluid mt-5" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div id="kt_app_content_container" class="app-container">
                        <div class="alert alert-warning p-4">
                            <h4 class="fw-semibold">No Access</h4>
                            <p>Unfortunately, you do not have the required access to use this feature.</p>
                            <p>If you believe this is a mistake, please contact your administrator for further assistance.</p>
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
            "<'col-8 col-sm-6 col-md-11 d-flex align-items-center justify-content-end dt-toolbar datatable-search-section'f>" +
            ">" +
            "<'table-responsive'tr>" +
            "<'row'" +
                "<'col-12 col-md-1 d-flex align-items-center justify-content-start dt-toolbar datatable-length-section'l>" +
                "<'col-12 col-md-0 d-flex align-items-center justify-content-center justify-content-md-start mt-6'i>" +
                "<'col-12 col-md-12 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                ">",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('retailer.wholesaler.fetch-record') }}",
                type: "POST",
                data: function (d) {
                    d._token = '{{ csrf_token() }}';
                    d.order = d.order; // Add order data
                    d.columns = d.columns; // Add columns data
                    d.sub_category_filter = $('#filter_subcategory').val();
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
                            $('.dataTables_filter input').val('');
                            dataTable.search('').draw();
                        });
                    }
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
                    data: 'subcategory_names',
                    className: 'text-center',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'details',
                    className: 'text-center',
                    orderable: false,
                },
            ],
            initComplete: function () {
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

        $('#filter_subcategory').on('change', function() {
            dataTable.ajax.reload();
        });
        //<------------- END : server-side transaction datatable ------------->


        // submit form for request
        $('#requestAccessBtn').on('click', function (e) {
            e.preventDefault();

            let userId = $('input[name="user_id"]').val();

            $.ajax({
                url: '{{ route("wholesaler.request.access") }}',
                type: 'POST',
                data: {
                    user_id: userId
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Access Request Sent!',
                        text: response.message || 'Your request has been sent to the administrator. You will be notified once approved.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function (xhr) {
                    let error = xhr.responseJSON?.message || 'Something went wrong. Try again.';
                    $('#accessMessage')
                        .removeClass('d-none alert-success')
                        .addClass('alert-danger')
                        .text(error);
                }
            });
        });

    </script>
@endsection
