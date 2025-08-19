@extends('layouts.base')
@section('title')
    Customer List | TechtrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">
                            Customer List</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Customer List</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container ">

                    <div class="card card-flush">
                        <div class="card-body mt-1">
                            <div class="tab-content">
                                <table class="table align-middle table-row-dashed fs-7 table-striped" id="kt_datatable_customer_list">
                                    <thead>
                                        <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="text-center py-5 border-0 align-middle" style="background: #0d0e12;color:#fff !important;">Sr No</th>
                                            <th class="text-center py-5 border-0 align-middle" style="background: #0d0e12;color:#fff !important;">Name</th>
                                            <th class="text-center py-5 border-0 align-middle" style="background: #0d0e12;color:#fff !important;">Mobile No</th>
                                            <th class="text-center py-5 border-0 align-middle" style="background: #0d0e12;color:#fff !important;">Email</th>
                                            <th class="text-center py-5 border-0 align-middle" style="background: #0d0e12;color:#fff !important;">State</th>
                                            <th class="text-center py-5 border-0 align-middle" style="background: #0d0e12;color:#fff !important;">City</th>
                                            <th class="text-center py-5 border-0 align-middle" style="background: #0d0e12;color:#fff !important;">Pincode</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-700 fs-6">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- withdrawal-request-modal --}}
            {{-- <div class="modal fade" id="withdrawalRequestModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-550px">
                    <div class="modal-content">
                        <div class="modal-header bg-white border-bottom">
                            <h2 class="fw-bold mb-0">Withdrawal Request</h2>
                            <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-2">
                                    <span class="path1"></span>
                                    <span class="path2 text-dark"></span>
                                </i>
                            </div>
                        </div>

                        <div class="modal-body p-5">
                            <div class="mb-7">
                                <div class="bg-light-primary p-4 rounded">
                                    <div class="text-gray-700 fs-6">
                                        <div class="mb-2"><strong>Current Balance:</strong>
                                            ₹{{ number_format($user->userDetail->success_wallet, 2) }}</div>
                                        <div class="mb-2"><strong>Account Number:</strong>
                                            {{ Auth::user()->userDetail->account_number ?? 'N/A' }}</div>
                                        <div class="mb-2"><strong>IFSC Code:</strong>
                                            {{ Auth::user()->userDetail->ifsc_code ?? 'N/A' }}</div>
                                        <div><strong>Account Holder Name:</strong>
                                            {{ Auth::user()->userDetail->account_holder_name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" id="withdrawalRequestForm" class="m-1">
                                @csrf
                                <div class="form-group mb-4">
                                    <label class="form-label fw-semibold">Withdrawal Amount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="request_amount"
                                        class="form-control form-control-solid border-secondary" placeholder="Enter amount"
                                        min="1" step="0.01" autocomplete="off">
                                    <span class="error error_request_amount text-danger m-2 d-none"></span>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="form-label fw-semibold">Remarks</label>
                                    <textarea name="remarks" class="form-control form-control-solid border-secondary"
                                        placeholder="Enter remarks" autocomplete="off"></textarea>
                                    <span class="error error_remarks text-danger m-2 d-none"></span>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Submit Request</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}

            @include('layouts.footer')
        </div>
    </div>
@endsection

@section('script')
    <script>
        //<------------- START : server-side transaction datatable ------------->
        dataTable = $('#kt_datatable_customer_list').DataTable({
            dom: "<'row mb-2'" +
            "<'col-8 col-sm-6 col-md-12 d-flex align-items-center justify-content-end dt-toolbar datatable-search-section'f>" +
            ">" +
            "<'table-responsive'tr>" +
            "<'row'" +
                "<'col-12 col-sm-0 col-md-0 d-flex align-items-center justify-content-start dt-toolbar datatable-length-section'l>" +
                "<'col-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-6'i>" +
                "<'col-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                ">",
            pageLength: 20,
            lengthMenu: [10, 20, 50, 100],
            processing: true,
            serverSide: true,
            fixedHeader: {
            header: true,
                headerOffset: document.querySelector("#kt_app_header_wrapper").offsetHeight // height of your fixed header
            },
            ajax: {
                url: "{{ route('retailer.customers.fetch-record') }}",
                type: "POST",
                data: function (d) {
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
            columns: [{
                data: 'sr_no',
                className: 'text-center',
                orderable: false,
            },
            {
                data: 'name',
                className: 'text-center',
            },
            {
                data: 'mobile_no',
                className: 'text-center',
            },
            {
                data: 'email',
                className: 'text-center',
            },
            {
                data: 'state',
                className: 'text-center'
            },
            {
                data: 'city',
                className: 'text-center'
            },
            {
                data: 'pincode',
                className: 'text-center'
            },
            ],
            initComplete: function () {
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
    </script>
@endsection
