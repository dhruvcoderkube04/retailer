@extends('layouts.base')
@section('title')
    Shipping Charges Report | TechtrendMart
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <!-- Toolbar -->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">
                            Shipping Charges Report
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Shipping Charges Report</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div class="app-container ">
                    <div class="row g-2 pb-7 align-items-end">
                        {{-- Date Range Picker --}}
                        <div class="col-12 col-md-3">
                            <label for="kt_daterangepicker_gst" class="form-label fw-semibold">Date Range</label>
                            <div class="input-group bg-secondary">
                                <input type="text" class="form-control form-control-solid bg-secondary border-0" placeholder="Pick date range" id="kt_daterangepicker_gst">
                                <span class="input-group-text bg-secondary border-0">
                                    <i class="ki-duotone ki-calendar-8 fs-2">
                                        <span class="path1"></span><span class="path2"></span>
                                        <span class="path3"></span><span class="path4"></span>
                                        <span class="path5"></span><span class="path6"></span>
                                    </i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body pt-5">
                            <div class="table-responsive">
                                <table class="table align-middle table-striped fs-6 gy-5"
                                    id="kt_datatable_profit_and_loss">
                                    <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0 align-middle border-0">
                                            <th class="text-center min-w-100px">Order ID</th>
                                            <th class="text-center min-w-100px">Product Weight</th>
                                            <th class="text-center min-w-100px">Courier Partner</th>
                                            <th class="text-center min-w-100px">Base Charge</th>
                                            <th class="text-center min-w-100px">GST Amount</th>
                                            <th class="text-center min-w-100px">RTO Charge</th>
                                            <th class="text-center min-w-100px">Total Shipping Charges</th>
                                            <th class="text-center min-w-100px">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-700 fs-6" id="order_table_body">

                                    </tbody>
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
        //<------------- START : date pickert ------------->
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker_gst").html(start.format("DD/MM/YYYY") + " - " + end.format(
                "DD/MM/YYYY"));
        }

        $("#kt_daterangepicker_gst").daterangepicker({
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
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1,
                    "month").endOf(
                    "month")]
            }
        }, cb);

        cb(start, end);
        //<------------- END : date pickert ------------->

        //<------------- START : server-side transaction datatable ------------->
        dataTable = $('#kt_datatable_profit_and_loss').DataTable({
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
            scrollX: true,
            //fixedHeader: {
            //header: true,
            //    headerOffset: document.querySelector("#kt_app_header_wrapper").offsetHeight // height of your fixed header
            //},
            ajax: {
                url: "{{ route('shipping.charges.report.fetch-record') }}",
                type: "POST",
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.date_filter = $('#kt_daterangepicker_gst').val();
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
                    data: 'order_id',
                    className: 'text-center',
                    orderable: true,
                },
                {
                    data: 'product_weight',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'courier_partner',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'base_charge',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'gst_amount',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'rto_charges',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'total_shipping_charges',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'status',
                    className: 'text-center',
                    orderable: false,
                }
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

        $(document).ready(function() {
            $("#type_filter").on('change', function() {
                dataTable.draw();
            });

            $("#wholesaler_filter").on('change', function() {
                dataTable.draw();
            });

            $("#retailer_filter").on('change', function() {
                dataTable.draw();
            });

            $("#sub_category_filter").on('change', function() {
                dataTable.draw();
            });

            $("#courier_service_filter").on('change', function() {
                dataTable.draw();
            });

            $("#product_filter").on('change', function() {
                dataTable.draw();
            });

            $("#kt_daterangepicker_gst").on('apply.daterangepicker', function(ev, picker) {
                dataTable.draw();
            });
        });
    </script>
@endsection
