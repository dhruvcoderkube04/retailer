@extends('layouts.base')
@section('title')
    Retailer's Success Wallet Transactions | TechtrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">
                            Success Wallet Transactions</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-normal fs-6 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Success Wallet Transactions</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid my-5">
                <div id="kt_app_content_container" class="app-container mx-auto">

                    <div class="card mb-2 pb-5 mb-xl-5">
                        <div class="card-body pt-9 pb-0">
                            <div class="row gy-5 align-items-center flex-column flex-md-row">

                                <!-- Profile Image -->
                                <div class="col-md-auto text-center">
                                    @php
                                        $userDetail = Auth::user()->userDetail;
                                        $logoUrl =
                                            $userDetail && $userDetail->company_logo
                                                ? Storage::disk('spaces')->url($userDetail->company_logo)
                                                : asset('assets/media/avatars/no-profile.png');
                                    @endphp
                                    <div class="symbol symbol-100px symbol-lg-150px symbol-fixed position-relative mx-auto">
                                        <img src="{{ $logoUrl }}"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/media/avatars/no-profile.png') }}';"
                                            alt="image" class="img-fluid rounded-circle">
                                    </div>
                                </div>

                                <!-- User Info & Stats -->
                                <div class="col-md flex-grow-1 text-center text-md-start">
                                    <div class="mb-4">
                                        <div
                                            class="d-flex align-items-center justify-content-center justify-content-md-start mb-2 flex-wrap">
                                            <div class="text-gray-900 fs-2 fw-bold me-2">
                                                {{ Auth::user()->firstname }}
                                            </div>
                                            <i class="ki-duotone ki-verify fs-1 text-primary"></i>
                                        </div>

                                        <div
                                            class="d-flex flex-wrap justify-content-center justify-content-md-start text-gray-700 fw-semibold fs-6">
                                            <div class="me-4 mb-2 d-flex align-items-center">
                                                <i class="ki-duotone ki-geolocation fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                {{ Auth::user()->userDetail->state }},
                                                {{ Auth::user()->userDetail->city }}
                                            </div>
                                            <div class="mb-2 d-flex align-items-center">
                                                <i class="ki-duotone ki-sms fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                {{ Auth::user()->email }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Credit, Debit & Income Stats -->
                                    <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3">
                                        <!-- Credit -->
                                        <div
                                            class="border border-gray-300 border-dashed rounded py-3 px-4 text-center min-w-125px">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ki-duotone ki-arrow-up fs-4 text-success me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fs-4 fw-bold" id="total_credit_section">
                                                    <span class="fs-7">₹ </span>0
                                                </div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-700">Wallet Credit</div>
                                        </div>

                                        <!-- Debit -->
                                        <div
                                            class="border border-gray-300 border-dashed rounded py-3 px-4 text-center min-w-125px">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ki-duotone ki-arrow-down fs-4 text-danger me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fs-4 fw-bold" id="total_debit_section">
                                                    <span class="fs-7">₹ </span>0
                                                </div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-700">Wallet Debit</div>
                                        </div>

                                        <!-- Income -->
                                        <div
                                            class="border border-gray-300 border-dashed rounded py-3 px-4 text-center min-w-125px">
                                            <div class="d-flex align-items-center justify-content-center mb-1"
                                                id="total_income_section">
                                                <div class="fs-4 fw-bold">
                                                    <span class="fs-7">₹ </span>0
                                                </div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-700">Wallet Income</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Wallet -->
                                <div class="col-md-auto text-center">
                                    <div class="border border-gray-300 border-dashed rounded px-8 py-4 w-130">
                                        <div class="fs-1 fw-bold">
                                            <span class="fs-5">₹ </span>{{ $user->userDetail->success_wallet }}
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center mt-2">
                                            <i class="fa-solid fa-circle fs-9 me-2 text-success"></i>
                                            <div class="fw-semibold fs-5 text-gray-700">Success Wallet</div>
                                        </div>
                                    </div>

                                    @if ($user->userDetail->wallet_status == 'approved')
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-flex btn-primary"
                                                data-bs-toggle="modal" data-bs-target="#withdrawalRequestModal">
                                                <i class="ki-duotone ki-bank fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Withdrawal Request
                                            </button>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="card card-flush">
                        <div class="card-header d-flex align-items-center justify-content-between px-9 py-3">
                            <div class="card-title">
                                <div class="d-flex align-items-center w-100 w-sm-auto">
                                    <div class="input-group mw-250px bg-secondary">
                                        <input type="text" class="form-control form-control-solid bg-secondary border-0"
                                            placeholder="Pick date range" id="kt_daterangepicker_account_transactions">
                                        <span class="input-group-text bg-secondary border-0">
                                            <i class="ki-duotone ki-calendar-8 fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                                <span class="path6"></span>
                                            </i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div class="tab-content">
                                <table class="table align-middle table-row-dashed fs-7"
                                    id="kt_datatable_account_transactions">
                                    <thead>
                                        <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="text-center py-5 border-0 align-middle w-80px">Info</th>
                                            <th class="text-center py-5 border-0 align-middle"></th>
                                            <th class="text-center py-5 border-0 align-middle w-250px">Description</th>
                                            <th class="text-center py-5 border-0 align-middle w-150px">Date & Time</th>
                                            <th class="text-center py-5 border-0 align-middle">Order ID</th>
                                            <th class="text-center py-5 border-0 align-middle w-100px">Transaction Amount</th>
                                            <th class="text-center py-5 border-0 align-middle w-100px">Current Balance</th>
                                            <th class="text-center py-5 border-0 align-middle w-100px">Status</th>
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

            {{-- transaction-summary-modal --}}
            <div class="modal fade" id="transactionSummaryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-450px">
                    <div class="modal-content">
                        <div class="modal-header bg-white border-bottom">
                            <h2 class="fw-bold mb-0">Transaction Summary</h2>
                            <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-2">
                                    <span class="path1"></span>
                                    <span class="path2 text-dark"></span>
                                </i>
                            </div>
                        </div>

                        <div class="modal-body p-5" id="transaction-info-section">
                            <div class="d-flex flex-column gap-2 fs-6 fw-semibold text-gray-700">

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Product Name :</span>
                                    <span>N/A</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Tracking ID :</span>
                                    <span>N/A</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Remark :</span>
                                    <span>N/A</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Date :</span>
                                    <span>N/A</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Order ID :</span>
                                    <span class="text-primary text-hover-underline">N/A</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Transaction Amount :</span>
                                    <span>N/A</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Charges :</span>
                                    <span>N/A</span>
                                </div>

                            </div>

                            <!-- Total Section -->
                            <div class="border-top pt-4 mt-5">
                                <div class="d-flex justify-content-between align-items-center fs-2 fw-bold">
                                    <span>Total</span>
                                    <span class="text-success">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- withdrawal-request-modal --}}
            <div class="modal fade" id="withdrawalRequestModal" tabindex="-1" aria-hidden="true">
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
                                {{-- Request Amount --}}
                                <div class="form-group mb-5">
                                    <label class="form-label fw-semibold">Request Amount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="request_amount"
                                        class="form-control form-control-solid border-secondary"
                                        placeholder="Enter amount" min="1" step="0.01" autocomplete="off">
                                    <span class="error error_request_amount text-danger m-2 d-none"></span>
                                </div>

                                {{-- Request Type --}}
                                <div class="form-group mb-5">
                                    <label class="form-label fw-semibold d-block">Request Type <span
                                            class="text-danger">*</span></label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="request_type"
                                            id="toAccount" value="to_account" checked>
                                        <label class="form-check-label" for="toAccount">To Self Account</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="request_type"
                                            id="toWholesaler" value="to_wholesaler">
                                        <label class="form-check-label" for="toWholesaler">To Wholesaler</label>
                                    </div>
                                    <span class="error error_request_type text-danger m-2 d-none"></span>
                                </div>

                                {{-- Wholesaler Email --}}
                                <div class="form-group mb-5 d-none" id="wholesalerDetailSection">
                                    <label class="form-label fw-semibold">Wholesaler Email <span
                                            class="text-danger">*</span></label>

                                    <div class="input-group mb-2 position-relative" id="emailInputGroup">
                                        <input type="email" name="wholesaler_email"
                                            class="form-control form-control-solid border-secondary"
                                            placeholder="Enter wholesaler email" id="wholesaler_email_input">
                                        <button type="button" class="btn btn-light-primary"
                                            id="verifyWholesalerEmail">Verify</button>

                                        {{-- Verified checkmark --}}
                                        <span id="verifiedBadge"
                                            class="position-absolute top-50 translate-middle-y end-0 d-none"
                                            style="z-index: 10; margin-right: 7.5rem;">
                                            <i class="bi bi-patch-check-fill text-success fs-4"></i>
                                        </span>
                                    </div>

                                    <span class="error error_wholesaler_email text-danger m-2 d-none"></span>

                                    {{-- Verified Details --}}
                                    <div id="wholesalerDetails"
                                        class="border border-success bg-light-success rounded p-3 d-none mt-2">
                                        <div class="fw-bold mb-2 text-success">
                                            <i class="bi bi-person-check-fill me-1 text-success"></i> Wholesaler
                                            Verified
                                        </div>
                                        <div class="mb-1"><strong>Name:</strong> <span id="wholesalerName"></span>
                                        </div>
                                        <div class="mb-1"><strong>Company Name:</strong> <span
                                                id="wholesalerCompany"></span></div>
                                        <div class="mb-1"><strong>Mobile No:</strong> <span
                                                id="wholesalerMobile"></span></div>
                                        <div class="mb-1"><strong>Wallet Status:</strong> <span
                                                id="walletStatus"></span></div>
                                        <input type="hidden" name="wholesaler_id" id="wholesaler_id_hidden">
                                        <input type="hidden" name="wholesaler_wallet_status"
                                            id="wholesaler_wallet_status_hidden">
                                    </div>

                                    {{-- If Not Found --}}
                                    <div id="wholesalerNotFoundSection" class="alert alert-danger d-none mt-3 mb-0">
                                        <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
                                        <span id="wholesalerNotFound">Wholesaler not exist.</span>
                                    </div>
                                </div>

                                {{-- Remarks --}}
                                <div class="form-group mb-5">
                                    <label class="form-label fw-semibold">Remarks</label>
                                    <textarea name="remarks" class="form-control form-control-solid border-secondary" placeholder="Enter remarks"
                                        autocomplete="off"></textarea>
                                    <span class="error error_remarks text-danger m-2 d-none"></span>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-light me-3"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Submit Request</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.footer')
        </div>
    </div>
@endsection

@section('script')
    <script>
        //<------------- START : date pickert ------------->
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker_account_transactions").html(start.format("DD/MM/YYYY") + " - " + end.format(
                "DD/MM/YYYY"));
        }

        $("#kt_daterangepicker_account_transactions").daterangepicker({
            startDate: start,
            endDate: end,
            maxDate: moment(),
            locale: {
                format: "DD/MM/YYYY" // Set the desired format for the input field
            },
            ranges: {
                "Today": [moment(), moment()],
                "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf(
                    "month")]
            }
        }, cb);

        cb(start, end);
        //<------------- END : date pickert ------------->


        //<------------- START : server-side transaction datatable ------------->
        dataTable = $('#kt_datatable_account_transactions').DataTable({
            dom: "<'row mb-5'" +
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
                url: "{{ route('retailer.accounts.fetch-record.success-wallet') }}",
                type: "POST",
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.date_filter = $('#kt_daterangepicker_account_transactions').val();
                    d.order = d.order; // Add order data
                    d.columns = d.columns; // Add columns data
                },
                dataSrc: function(json) {
                    $('#total_credit_section').html('<span class="fs-7">₹ </span>' + json.totals.total_credit);
                    $('#total_debit_section').html('<span class="fs-7">₹ </span>' + json.totals.total_debit);

                    let icon = '';
                    let income = parseFloat(json.totals.total_income);

                    if (income > 0) {
                        icon = `<i class="ki-duotone ki-arrow-up fs-4 text-success me-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>`;
                    } else if (income < 0) {
                        icon = `<i class="ki-duotone ki-arrow-down fs-4 text-danger me-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>`;
                    } else {
                        icon = `<i class="ki-duotone ki-information fs-4 text-muted me-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>`;
                    }

                    $('#total_income_section').html(
                        icon + `<div class="fs-4 fw-bold"><span class="fs-7">₹ </span>${income}</div>`
                    );

                    return json.data;
                }
            },
            order: [],
            columns: [{
                    data: 'info',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'transaction_type',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'description',
                },
                {
                    data: 'created_at',
                    className: 'text-center',
                },
                {
                    data: 'order_id',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'final_transaction_amount',
                    className: 'text-center',
                },
                {
                    data: 'current_balance',
                    className: 'text-center',
                },
                {
                    data: 'status',
                    className: 'text-center'
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
            $("#kt_daterangepicker_account_transactions").on('apply.daterangepicker', function(ev, picker) {
                dataTable.draw();
            });

            $(document).on('click', '.transaction-info', function() {
                let transaction_id = $(this).attr('data-id');

                $.ajax({
                    url: '{{ route('retailer.accounts.transaction-info') }}',
                    type: 'GET',
                    data: {
                        transaction_id: transaction_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('response', response);

                        if (response.status) {
                            $('#transaction-info-section').html(response.html);

                            $('#transactionSummaryModal').modal('show');
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.msg,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            //<------------- START : Withdrawal Request -------------->
            // toggle wholesaler section
            $('input[name="request_type"]').on('change', function() {
                const type = $(this).val();
                if (type === 'to_wholesaler') {
                    $('#wholesalerDetailSection').removeClass('d-none');
                } else {
                    $('#wholesalerDetailSection').addClass('d-none');
                }
            });

            $(document).on('input', '#wholesaler_email_input', function() {
                $('#wholesaler_id_hidden').val('');
                $('#wholesaler_wallet_status_hidden').val('');
                $('#wholesalerDetails').fadeOut().addClass('d-none');
                $('#wholesalerNotFoundSection').fadeOut().addClass('d-none');
                $('#verifiedBadge').fadeOut().addClass('d-none');
            });

            // verify wholesaler email
            $(document).on('click', '#verifyWholesalerEmail', function(e) {
                e.preventDefault();

                $('.error_wholesaler_email').text('').addClass('d-none');

                const email = $('input[name="wholesaler_email"]').val().trim();
                if (email === '') {
                    $('.error_wholesaler_email').text('Please enter wholesaler email.').removeClass(
                        'd-none');
                    return;
                }

                // Ajax call to check wholesaler
                $.ajax({
                    url: '{{ route('retailer.accounts.withdrawal-transactions.verify-wholesaler-email') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        email: email
                    },
                    success: function(res) {
                        if (res.status) {
                            $('#wholesaler_id_hidden').val(res.data.id);
                            $('#wholesalerName').text(res.data.name);
                            $('#wholesalerCompany').text(res.data.company_name);
                            $('#wholesalerMobile').text(res.data.mobile);
                            $('#walletStatus').text(res.data.wallet_status);
                            if (res.data.wallet_status == 'Active') {
                                $('#wholesaler_wallet_status_hidden').val('yes');
                            } else {
                                $('#wholesaler_wallet_status_hidden').val('');
                            }

                            $('#wholesalerDetails').hide().removeClass('d-none').fadeIn();
                            $('#wholesalerNotFoundSection').fadeOut().addClass('d-none');
                            $('#verifiedBadge').fadeIn().removeClass('d-none');
                        } else {
                            $('#wholesalerDetails').fadeOut().addClass('d-none');
                            $('#wholesaler_id_hidden').val('');
                            $('#wholesaler_wallet_status_hidden').val('');
                            $('#wholesalerNotFoundSection').hide().removeClass('d-none')
                                .fadeIn();
                            $('#wholesalerNotFound').text('Wholesaler not exist.');
                            $('#verifiedBadge').fadeOut().addClass('d-none');
                        }
                    },
                    error: function() {
                        $('#wholesalerDetails').fadeOut().addClass('d-none');
                        $('#wholesaler_id_hidden').val('');
                        $('#wholesaler_wallet_status_hidden').val('');
                        $('#wholesalerNotFoundSection').hide().removeClass('d-none').fadeIn();
                        $('#wholesalerNotFound').text('Error verifying wholesaler.');
                        $('#verifiedBadge').fadeOut().addClass('d-none');
                    }
                });
            });

            // withdrawal request from submit
            $(document).on('submit', '#withdrawalRequestForm', function(e) {
                e.preventDefault();

                const form = $(this);
                const requestType = $('input[name="request_type"]:checked').val();
                const amountInput = form.find('input[name="request_amount"]');
                const amount = parseFloat(amountInput.val());
                const currentWalletBalance = parseFloat('{{ $user->userDetail->success_wallet ?? 0 }}');
                const wholesalerEmailInput = form.find('input[name="wholesaler_email"]');
                const wholesalerEmail = wholesalerEmailInput.val().trim();

                $('.error').text('').addClass('d-none');

                // Validate amount
                if (!amount || amount <= 0) {
                    $('.error_request_amount').text('Please enter a valid withdrawal amount.').removeClass(
                        'd-none');
                    amountInput.focus();
                    return false;
                }

                if (amount > currentWalletBalance) {
                    $('.error_request_amount').text(
                        'Entered amount exceeds your current success wallet balance.').removeClass(
                        'd-none');
                    amountInput.focus();
                    return false;
                }

                // Validate request type
                if (!requestType) {
                    $('.error_request_type').text('Please select a request type.').removeClass('d-none');
                    return false;
                }

                // If to_wholesaler, validate email
                if (requestType === 'to_wholesaler') {
                    if (wholesalerEmail === '') {
                        $('.error_wholesaler_email').text('Please enter wholesaler email.').removeClass(
                            'd-none');
                        wholesalerEmailInput.focus();
                        return false;
                    }
                    if ($('#wholesaler_id_hidden').val() === '') {
                        $('.error_wholesaler_email').text('Please verify the wholesaler email.')
                            .removeClass('d-none');
                        wholesalerEmailInput.focus();
                        return false;
                    }
                    if ($('#wholesaler_wallet_status_hidden').val() === '') {
                        $('.error_wholesaler_email').text(
                                'Wholesaler wallet is inactive, Request wholesaler to activate the wallet.'
                            )
                            .removeClass('d-none');
                        wholesalerEmailInput.focus();
                        return false;
                    }
                }

                $.ajax({
                    url: '{{ route('retailer.accounts.withdrawal-request-post') }}',
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#withdrawalRequestModal').modal('hide');
                            Swal.fire({
                                title: 'Request Sent!',
                                text: response.msg,
                                icon: 'success'
                            });
                            window.location.reload();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.msg,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        $('.error').text('').addClass('d-none');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(field, messages) {
                                if (field == 'wholesaler_id' || field ==
                                    'wholesaler_wallet_status') {
                                    field = 'wholesaler_email';
                                }
                                let errorElement = $('.error_' + field);
                                if (errorElement.length) {
                                    errorElement.text(messages[0]).removeClass(
                                        'd-none');
                                } else {
                                    console.warn('No error span found for', field);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong. Please try again later.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });
            //<------------------- END : Withdrawal Request ----------------->
        });
    </script>
@endsection
