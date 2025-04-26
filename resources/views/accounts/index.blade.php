@extends('layouts.base')
@section('title')
    Retailer's Account Transactions | TrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Account Transactions History</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Account Transactions History</li>
                        </ul>
                    </div>
                </div>
            </div>

            @php
                $total_credit = 0;
                $total_debit = 0;
                $total_income = 0;
            @endphp
            @foreach ($transactions as $transaction)
                @php
                    if ($transaction->amount_type == 'add') {
                        $total_credit += $transaction->total_amount;
                    } elseif ($transaction->amount_type == 'minus') {
                        $total_debit += $transaction->total_amount;
                    }
                @endphp
            @endforeach
            @php
                $total_income = $total_credit - $total_debit;
            @endphp

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <div class="card mb-2 pb-5 mb-xl-5">
                        <div class="card-body pt-9 pb-0">
                            <div class="row gy-5 align-items-center flex-column flex-md-row">

                                <!-- Profile Image -->
                                <div class="col-md-auto text-center">
                                    @php
                                        $logoUrl =
                                            Auth::user()->userDetail && Auth::user()->userDetail->company_logo
                                                ? Auth::user()->userDetail->company_logo
                                                : asset('assets/media/avatars/no-profile.png');
                                    @endphp
                                    <div class="symbol symbol-100px symbol-lg-150px symbol-fixed position-relative mx-auto">
                                        <img src="{{ $logoUrl }}" alt="image" class="img-fluid rounded-circle">
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
                                            class="d-flex flex-wrap justify-content-center justify-content-md-start text-gray-500 fw-semibold fs-6">
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
                                        <!-- Debit -->
                                        <div
                                            class="border border-gray-300 border-dashed rounded py-3 px-4 text-center min-w-125px">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ki-duotone ki-arrow-down fs-4 text-danger me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fs-4 fw-bold" id="total_debit_section">
                                                    <span class="fs-7">₹ </span>{{ $total_debit }}
                                                </div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-500">Debit</div>
                                        </div>

                                        <!-- Credit -->
                                        <div
                                            class="border border-gray-300 border-dashed rounded py-3 px-4 text-center min-w-125px">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <i class="ki-duotone ki-arrow-up fs-4 text-success me-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="fs-4 fw-bold" id="total_credit_section">
                                                    <span class="fs-7">₹ </span>{{ $total_credit }}
                                                </div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-500">Credit</div>
                                        </div>

                                        <!-- Income -->
                                        <div
                                            class="border border-gray-300 border-dashed rounded py-3 px-4 text-center min-w-125px">
                                            <div class="d-flex align-items-center justify-content-center mb-1"
                                                id="total_income_section">
                                                @if ($total_income > 0)
                                                    <i class="ki-duotone ki-arrow-up fs-4 text-success me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                @elseif ($total_income < 0)
                                                    <i class="ki-duotone ki-arrow-down fs-4 text-danger me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                @else
                                                    <i class="ki-duotone ki-information fs-4 text-muted me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                @endif
                                                <div class="fs-4 fw-bold">
                                                    <span class="fs-7">₹ </span>{{ $total_income }}
                                                </div>
                                            </div>
                                            <div class="fw-semibold fs-6 text-gray-500">Income</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Wallet -->
                                <div class="col-md-auto text-center">
                                    <div class="border border-gray-300 border-dashed rounded p-4 w-100">
                                        <div class="fs-1 fw-bold">
                                            <span class="fs-5">₹ </span>{{ $webManagement->wallet }}
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center mt-2">
                                            <i class="ki-duotone ki-wallet fs-2 text-info me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            <div class="fw-semibold fs-6 text-gray-500">Wallet</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="card card-flush">
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <div class="card-title">
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-ecommerce-product-filter="search"
                                        class="form-control form-control-solid w-250px ps-12 bg-secondary"
                                        placeholder="Search Product" id="search_field" />
                                </div>
                            </div>
                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                <div class="d-flex align-items-center w-100 w-sm-auto">
                                    <div class="input-group mw-250px bg-secondary">
                                        <input type="text"
                                            class="form-control form-control-solid bg-secondary border-0"
                                            placeholder="Pick date range" id="kt_daterangepicker_accounts_history">
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

                                <button type="button" class="btn btn-flex btn-light-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_add_product">
                                    <i class="ki-duotone ki-plus-square fs-3"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span></i>
                                    Export Report
                                </button>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            {{-- tabs --}}
                            {{-- <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_1" data-tab="1">Wholesaler
                                        Products</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2" data-tab="2">My
                                        Products</a>
                                </li>
                            </ul> --}}

                            {{-- tab contents --}}
                            <div class="tab-content" id="myTabContent">

                                {{-- margin added products --}}
                                <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="kt_datatable_accounts_history">
                                        <thead>
                                            <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th></th>
                                                <th class="text-center align-middle">Description</th>
                                                <th class="text-center align-middle">Date & Time</th>
                                                <th class="text-center align-middle">Order ID</th>
                                                <th class="text-center align-middle">Transaction Amount</th>
                                                <th class="text-center align-middle">Current Balance</th>
                                                <th class="text-center align-middle">Info</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td class="text-center">
                                                        @if ($transaction->amount_type == 'add')
                                                            <i class="ki-duotone ki-arrow-up fs-1 text-success me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        @elseif ($transaction->amount_type == 'minus')
                                                            <i class="ki-duotone ki-arrow-down fs-1 text-danger me-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        @endif
                                                    </td>
                                                    <td class="text-start">{{ $transaction->description }}</td>
                                                    <td class="text-center">{{ $transaction->created_at }}</td>
                                                    <td class="text-center">
                                                        {{ $transaction->customer_order?->order_id ?? '-' }}
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($transaction->amount_type == 'add')
                                                            <div class="badge badge-light-success fs-6">
                                                                + {{ $transaction->total_amount }}
                                                            </div>
                                                        @elseif ($transaction->amount_type == 'minus')
                                                            <div class="badge badge-light-danger fs-6">
                                                                - {{ $transaction->total_amount }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="badge badge-light-secondary fs-6">
                                                            {{ $transaction->current_balance }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="transaction-info"
                                                            data-id="{{ $transaction->id }}">
                                                            <i class="ki-duotone ki-information-2 fs-2 text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </a>
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

            {{-- transaction-summary-modal --}}
            <div class="modal fade" id="transactionSummaryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-450px">
                    <div class="modal-content">
                        <div class="modal-header bg-white border-bottom">
                            <h2 class="fw-bold mb-0">Wallet Receipt</h2>
                            <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-2">
                                    <span class="path1"></span>
                                    <span class="path2 text-dark"></span>
                                </i>
                            </div>
                        </div>

                        <div class="modal-body p-5">
                            <div class="d-flex flex-column gap-2 fs-6 fw-semibold text-gray-700">

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Product Name :</span>
                                    <span>Shoes</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Tracking ID :</span>
                                    <span>80680717762</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Remark :</span>
                                    <span>80680717762-Delivered</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Date :</span>
                                    <span>27-Dec-2024 01:21 PM</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Order ID :</span>
                                    <a href="#" class="text-primary text-hover-underline">23247183</a>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Order Amount :</span>
                                    <span>9500</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Amount :</span>
                                    <span>9500</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Courier Charge :</span>
                                    <span>600</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>COD Charge :</span>
                                    <span>190</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>GST :</span>
                                    <span>143</span>
                                </div>

                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span>Charge :</span>
                                    <span>933</span>
                                </div>

                            </div>

                            <!-- Total Section -->
                            <div class="border-top pt-4 mt-5">
                                <div class="d-flex justify-content-between align-items-center fs-2 fw-bold">
                                    <span>Total</span>
                                    <span class="text-success">8567</span>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
        //<------------- START : product datatable ------------->
        var table1 = $("#kt_datatable_accounts_history").DataTable({
            order: [],
            columnDefs: [{
                orderable: false,
                targets: 0
            }]
        });
        $("#search_field").on("keyup", function() {
            table1.search(this.value).draw();
        });
        //<------------- END : product datatable ------------->

        //<------------- START : date pickert ------------->
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker_accounts_history").html(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));
        }

        $("#kt_daterangepicker_accounts_history").daterangepicker({
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

        $(document).ready(function() {
            $(document).on('change', '#kt_daterangepicker_accounts_history', function() {
                const fullDate = $(this).val();
                const fullDateArray = fullDate.split(" - ");
                const from = fullDateArray[0];
                const to = fullDateArray[1];

                $.ajax({
                    url: '{{ route('retailer.accounts.date-filter') }}',
                    type: 'GET',
                    data: {
                        from: from,
                        to: to,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('response', response);

                        if (response.status) {
                            $('tbody').html(response.html);

                            let total_credit = 0;
                            let total_debit = 0;

                            $.each(response.transactions, function(index, transaction) {
                                if (transaction.amount_type === 'add') {
                                    total_credit += parseFloat(transaction
                                        .total_amount);
                                } else if (transaction.amount_type === 'minus') {
                                    total_debit += parseFloat(transaction.total_amount);
                                }
                            });

                            let total_income = total_credit - total_debit;

                            $('#total_credit_section').html(
                                `<span class="fs-7">₹ </span>${total_credit}`);
                            $('#total_debit_section').html(
                                `<span class="fs-7">₹ </span>${total_debit}`);

                            if (total_income > 0) {
                                $('#total_income_section').html(`
                                    <i class="ki-duotone ki-arrow-up fs-4 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fs-4 fw-bold">
                                        <span class="fs-7">₹ </span>${total_income}
                                    </div>
                                `);
                            } else if (total_income < 0) {
                                $('#total_income_section').html(`
                                    <i class="ki-duotone ki-arrow-down fs-4 text-danger me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fs-4 fw-bold">
                                        <span class="fs-7">₹ </span>${total_income}
                                    </div>
                                `);
                            } else {
                                $('#total_income_section').html(`
                                    <i class="ki-duotone ki-information fs-4 text-muted me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fs-4 fw-bold">
                                        <span class="fs-7">₹ </span>${total_income}
                                    </div>
                                `);
                            }
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

            $(document).on('click', '.transaction-info', function() {
                let transaction_id = $(this).attr('data-id');

                $('#transactionSummaryModal').modal('show');
            });
        });
    </script>
@endsection
