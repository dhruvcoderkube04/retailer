@extends('layouts.base')
@section('title')
    Retailer's Withdrawal Requests | TrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Withdrawal Requests History</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Withdrawal Requests History</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <div class="card mb-1 mb-xl-5">
                        <div class="card-body">
                            <div class="row gy-5 align-items-center flex-column flex-md-row">

                                <!-- Wallet Section -->
                                <div
                                    class="col-sm-auto text-center w-100 w-md-auto border border-gray-300 border-dashed rounded">
                                    <div class="p-8">
                                        <div class="fs-1 fw-bold">
                                            <span class="fs-5">₹ </span>{{ $user->userDetail->success_wallet }}
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center mt-2">
                                            <i class="ki-duotone ki-wallet fs-1 text-primary me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                            <div class="fw-semibold fs-3 text-gray-500">Wallet</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Info Section -->
                                <div class="col-md flex-grow-1 text-center text-md-start">
                                    <div class="mb-4">

                                        <!-- User Name -->
                                        <div
                                            class="d-flex align-items-center justify-content-center justify-content-md-start m-1 flex-wrap">
                                            <div class="text-gray-900 fs-2 fw-bold me-2">{{ Auth::user()->firstname }}</div>
                                            <i class="ki-duotone ki-verify fs-1 text-primary"></i>
                                        </div>

                                        <!-- Location and Email -->
                                        <div
                                            class="d-flex flex-wrap justify-content-center justify-content-md-start text-gray-500 fw-semibold fs-6">
                                            <div class="me-4 mb-2 d-flex align-items-center">
                                                <i class="ki-duotone ki-geolocation fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span>
                                                    {{ Auth::user()->userDetail->state }},
                                                    {{ Auth::user()->userDetail->city }}
                                                </span>
                                            </div>
                                            <div class="mb-2 d-flex align-items-center">
                                                <i class="ki-duotone ki-sms fs-4 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span>{{ Auth::user()->email }}</span>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 mt-3">
    
                                          <!-- Withdrawal Request Button -->
                                          <button type="button"
                                              class="btn btn-sm btn-primary d-flex align-items-center gap-2"
                                              data-bs-toggle="modal" data-bs-target="#withdrawalRequestModal">
                                              <i class="ki-duotone ki-bank fs-5">
                                                  <span class="path1"></span>
                                                  <span class="path2"></span>
                                              </i>
                                              Withdrawal Request
                                          </button>
                                      
                                          <!-- Date Range Picker -->
                                          <div class="input-group mw-250px bg-secondary">
                                              <input type="text" 
                                                  class="form-control form-control-solid bg-secondary border-0" 
                                                  placeholder="Pick date range" 
                                                  id="kt_daterangepicker_withdrawal_transactions">
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

                            </div>
                        </div>
                    </div>

                    <div class="card card-flush">
                        <div class="card-body mt-1">
                            <div class="tab-content">
                                <table class="table align-middle table-row-dashed fs-6 gy-5"
                                    id="kt_datatable_withdrawal_transactions">
                                    <thead>
                                        <tr class="text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="text-center align-middle"></th>
                                            <th class="text-center align-middle">Date & Time</th>
                                            <th class="text-center align-middle">Remarks</th>
                                            <th class="text-center align-middle w-120px">Request Amount</th>
                                            <th class="text-center align-middle w-120px">Status</th>
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
                                <div class="form-group mb-4">
                                    <label class="form-label fw-semibold">Withdrawal Amount <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="request_amount"
                                        class="form-control form-control-solid border-secondary"
                                        placeholder="Enter amount" min="1" step="0.01" autocomplete="off">
                                    <span class="error error_request_amount text-danger m-2 d-none"></span>
                                </div>
                                <div class="form-group mb-4">
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
            $("#kt_daterangepicker_withdrawal_transactions").html(start.format("DD/MM/YYYY") + " - " + end.format(
                "DD/MM/YYYY"));
        }

        $("#kt_daterangepicker_withdrawal_transactions").daterangepicker({
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
        dataTable = $('#kt_datatable_withdrawal_transactions').DataTable({
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
                url: "{{ route('retailer.accounts.withdrawal-transactions.fetch-record') }}",
                type: "POST",
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.date_filter = $('#kt_daterangepicker_withdrawal_transactions').val();
                    d.order = d.order; // Add order data
                    d.columns = d.columns; // Add columns data
                },
                dataSrc: function(json) {
                    return json.data;
                }
            },
            order: [],
            columns: [{
                    data: 'transaction_type',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    className: 'text-center',
                },
                {
                    data: 'remarks',
                    className: 'text-center',
                },
                {
                    data: 'request_amount',
                    className: 'text-end',
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
            $("#kt_daterangepicker_withdrawal_transactions").on('apply.daterangepicker', function(ev, picker) {
                dataTable.draw();
            });

            //<------------- START : Withdrawal Request From Submit -------------->
            $(document).on('submit', '#withdrawalRequestForm', function(e) {
                e.preventDefault();

                let form = $(this);
                let amountInput = form.find('input[name="request_amount"]');
                let amount = parseFloat(amountInput.val());
                let currentWalletBalance = parseFloat('{{ $user->userDetail->success_wallet ?? 0 }}');

                // validation
                $('.error').text('').addClass('d-none');
                let errorSpan = form.find('.error_request_amount');
                if (!amount || amount <= 0) {
                    errorSpan.text('Please enter a valid withdrawal amount.').removeClass('d-none');
                    amountInput.focus();
                    return false;
                }
                if (amount > currentWalletBalance) {
                    errorSpan.text('Entered amount exceeds your current success wallet balance.').removeClass(
                        'd-none');
                    amountInput.focus();
                    return false;
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
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
            //<------------------- END : Withdrawal Request From Submit ----------------->
        });
    </script>
@endsection
