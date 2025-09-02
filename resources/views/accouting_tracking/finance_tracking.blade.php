@extends('layouts.base')

@section('title')
    Finance Tracking List | TechtrendMart
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <!-- Toolbar -->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">
                            Finance Tracking List
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Finance Tracking List</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div class="app-container ">
                    <div class="card">
                        {{-- Filters --}}
                        <div class="row g-5 px-9 py-5 align-items-end">

                            {{-- Wholesaler Filter --}}
                            <div class="col-12 col-md-3">
                                <label for="wholesaler_filter" class="form-label fw-semibold">Wholesaler</label>
                                <select id="wholesaler_filter"
                                    class="form-select form-select-solid input-group bg-secondary" data-control="select2"
                                    data-placeholder="Select Wholesaler">
                                    <option value="all">All</option>
                                    @foreach ($wholesalers as $wholesaler)
                                        <option value="{{ $wholesaler->id }}">
                                            {{ ucfirst(@$wholesaler->userDetail->company_name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Courier Partner Filter --}}
                            <div class="col-12 col-md-3">
                                <label for="courier_partner_filter" class="form-label fw-semibold">Courier Partner</label>
                                <select id="courier_partner_filter"
                                    class="form-select form-select-solid input-group bg-secondary" data-control="select2"
                                    data-placeholder="Select Courier Partner">
                                    <option value="all">All</option>
                                    @foreach ($courierPartners as $courierPartner)
                                        <option value="{{ $courierPartner->id }}">
                                            {{ ucfirst(@$courierPartner->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date Range Picker --}}
                            <div class="col-12 col-md-3">
                                <label for="kt_daterangepicker_wholesaler_orders" class="form-label fw-semibold">Date
                                    Range</label>
                                <div class="input-group bg-secondary">
                                    <input type="text" class="form-control form-control-solid bg-secondary border-0"
                                        placeholder="Pick date range" id="kt_daterangepicker_wholesaler_orders">
                                    <span class="input-group-text bg-secondary border-0">
                                        <i class="ki-duotone ki-calendar-8 fs-2">
                                            <span class="path1"></span><span class="path2"></span>
                                            <span class="path3"></span><span class="path4"></span>
                                            <span class="path5"></span><span class="path6"></span>
                                        </i>
                                    </span>
                                </div>
                            </div>

                            {{-- Export Button --}}
                            <div class="col-12 col-md-3">
                                <a href="#" id="export_csv" class="btn btn-light-primary w-100">Export CSV</a>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle fs-6 gy-5 table-striped"
                                    id="kt_datatable_finance_tracking">
                                    <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="text-center">No.</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Tracking ID</th>
                                            <th class="text-center">Remark</th>
                                            <th class="text-center">Weight</th>
                                            <th class="text-center">Order Amount</th>
                                            <th class="text-center">Courier</th>
                                            <th class="text-center">GST</th>
                                            <th class="text-center">Total Charge</th>
                                            <th class="text-center">Credit</th>
                                            <th class="text-center">Debit</th>
                                            <th class="text-center">Balance</th>
                                        </tr>
                                    </thead>
                                </table>
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
        var start = moment().subtract(365, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker_wholesaler_orders").html(start.format("DD/MM/YYYY") + " - " + end.format(
                "DD/MM/YYYY"));
        }

        $("#kt_daterangepicker_wholesaler_orders").daterangepicker({
            startDate: start,
            endDate: end,
            maxDate: moment(), // Prevent future dates
            locale: {
                format: "DD/MM/YYYY" // Set the desired format for the input field
            },
            ranges: {
                "Today": [moment(), moment()],
                "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1,
                    "month").endOf(
                    "month")]
            }
        }, cb);

        cb(start, end);

        $(document).ready(function() {
            dataTable = $('#kt_datatable_finance_tracking').DataTable({
                dom: "<'row mb-5'" +
                "<'col-8 col-sm-6 col-md-12 d-flex align-items-center justify-content-end dt-toolbar datatable-search-section'f>" +
                ">" +
                "<'table-responsive'tr>" +
                "<'row d-flex align-items-center justify-content-between' \
                <'col d-flex align-items-center gap-3'l i> \
                <'col-auto'p> \
                >",
                pageLength: 20,
                lengthMenu: [10, 20, 50, 100],
                processing: true,
                serverSide: true,
                // fixedHeader: {
                // header: true,
                //     headerOffset: document.querySelector("#kt_app_header_wrapper").offsetHeight // height of your fixed header
                // },

                ajax: {
                    url: "{{ route('retailer.finance-tracking.fetch-record') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.wholesaler_filter = $('#wholesaler_filter').val();
                        d.date_filter = $('#kt_daterangepicker_wholesaler_orders').val();
                        d.courier_partner_filter = $('#courier_partner_filter').val();
                        d.order = d.order;
                        d.columns = d.columns;
                    },
                    dataSrc: 'data'
                },
                order: [],
                columns: [
                    { data: 'no', className: 'text-center' },
                    { data: 'date', className: 'text-center' },
                    { data: 'tracking_id', className: 'text-center' },
                    { data: 'remark', className: 'text-center' },
                    { data: 'weight', className: 'text-center' },
                    { data: 'order_amount', className: 'text-center' },
                    { data: 'courier', className: 'text-center' },
                    { data: 'gst', className: 'text-center' },
                    { data: 'total_charge', className: 'text-center' },
                    { data: 'credit', className: 'text-center' },
                    { data: 'debit', className: 'text-center' },
                    { data: 'balance', className: 'text-center' },
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
                    );
                    searchBox.addClass('form-control form-control-solid w-100 ps-12 bg-secondary').attr(
                        'placeholder', 'Search');
                    searchBox.css({
                        'padding': '13px 15px 12px 15px',
                        'font-size': '14px',
                    });

                    searchLabel.hide();

                    lengthSelect.addClass('form-control form-control-solid w-100 bg-secondary');
                    lengthSelect.css({
                        'padding': '13px 27px 12px 14px',
                        'font-size': '14px',
                    });
                }
            });

            $("#wholesaler_filter").on('change', function() {
                dataTable.draw();
            });

            $("#courier_partner_filter").on('change', function() {
                dataTable.draw();
            });

            $("#kt_daterangepicker_wholesaler_orders").on('apply.daterangepicker', function(ev, picker) {
                dataTable.draw();
            });

            $('#export_csv').on('click', function(e) {
                e.preventDefault();

                const params = {
                    wholesaler_filter: $('#wholesaler_filter').val(),
                    courier_partner_filter: $('#courier_partner_filter').val(),
                    date_filter: $('#kt_daterangepicker_wholesaler_orders').val(),
                };

                const query = new URLSearchParams(params).toString();
                const url = "{{ route('retailer.finance-tracking.export-csv') }}?" + query;

                window.location.href = url;
            });
        });
    </script>
@endsection
