@extends('layouts.base')
@section('title')
    Retailer's Pending Wallet Transactions | TechtrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">
                            Pending Wallet Transactions</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-normal fs-6 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Pending Wallet Transactions</li>
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
                                            onerror="this.onerror=null; this.src='{{ asset('assets/media/avatars/no-profile.png') }}';"
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
                                            <span class="fs-5">₹ </span>{{ $user->userDetail->pending_wallet }}
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center mt-2">
                                            <i class="fa-solid fa-circle fs-9 me-2 text-danger"></i>
                                            <div class="fw-semibold fs-5 text-gray-700">Pending Wallet</div>
                                        </div>
                                    </div>
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
                                        <tr class="text-gray-700 fw-bold fs-7 text-uppercase gs-0">
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
                                    <tbody class="fw-semibold text-gray-600 fs-6">

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
            lengthMenu: [10, 20, 50 ,100],
            processing: true,
            serverSide: true,
            fixedHeader: {
            header: true,
                headerOffset: document.querySelector("#kt_app_header_wrapper").offsetHeight // height of your fixed header
            },
            ajax: {
                url: "{{ route('retailer.accounts.fetch-record.pending-wallet') }}",
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
        });
    </script>
@endsection
