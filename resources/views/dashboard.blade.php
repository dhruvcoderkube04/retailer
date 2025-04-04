@extends('layouts.base')
@section('title')
    Retailers | TrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">

            <div id="kt_app_toolbar" class="app-toolbar  py-3 py-lg-6 ">
                <div id="kt_app_toolbar_container" class="app-container  container-xxl d-flex flex-stack ">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 ">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Orders Tracking
                        </h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">
                                    Home </a>
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left"
                            class="btn btn-sm fw-bold btn-secondary d-flex align-items-center p-0" data-kt-initialized="1">
                            <input class="fs-6 form-control form-control-solid text-gray-600 fw-bold bg-secondary border-0"
                                placeholder="Pick date rage" id="kt_daterangepicker_4" />
                            <i class="ki-duotone ki-calendar-8 fs-2 ms-2 me-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                        </div>

                        <a href="{{ route('retailer.order.list') }}" class="btn btn-sm fw-bold btn-primary">Check New
                            Orders</a>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="row gy-5 gx-xl-10">
                        <div class="col-sm-4 col-xl-3 mb-xl-10">
                            <div class="card h-lg-60 text-center">
                                <div
                                    class="card-body flex-column
                            {{-- d-flex justify-content-between align-items-start --}}
                            ">
                                    <div>
                                        <i class="ki-duotone ki-abstract-35 fs-2hx">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                    <div class="d-flex flex-column my-7">
                                        <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2" id="new_orders">
                                            {{ $data['new_orders_count'] ?? 0 }}
                                        </span>
                                        <div class="mt-4">
                                            <span class="fw-semibold fs-5 text-gray-500">New Orders</span>
                                        </div>
                                    </div>
                                    {{-- <span class="badge badge-light-success fs-base">
                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>2.1%
                                </span> --}}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 col-xl-3 mb-xl-10">
                            <div class="card h-lg-60 text-center">
                                <div
                                    class="card-body flex-column
                            {{-- d-flex justify-content-between align-items-start --}}
                            ">
                                    <div>
                                        <i class="ki-duotone ki-check-square fs-2hx">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                    <div class="d-flex flex-column my-7">
                                        <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2" id="confirmed_orders">
                                            {{ $data['confirmed_orders_count'] ?? 0 }}
                                        </span>
                                        <div class="mt-4">
                                            <span class="fw-semibold fs-5 text-gray-500">Confirmed Orders</span>
                                        </div>
                                    </div>
                                    {{-- <span class="badge badge-light-success fs-base">
                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>2.1%
                                </span> --}}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 col-xl-3 mb-xl-10">
                            <div class="card h-lg-60 text-center">
                                <div
                                    class="card-body flex-column
                            {{-- d-flex justify-content-between align-items-start --}}
                            ">
                                    <div>
                                        <i class="ki-duotone ki-delivery fs-2hx">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </div>
                                    <div class="d-flex flex-column my-7">
                                        <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2" id="ready_for_ship">
                                            {{ $data['ready_for_ship_orders_count'] ?? 0 }}
                                        </span>
                                        <div class="mt-4">
                                            <span class="fw-semibold fs-5 text-gray-500">Ready For Ship</span>
                                        </div>
                                    </div>
                                    {{-- <span class="badge badge-light-success fs-base">
                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>2.1%
                                </span> --}}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 col-xl-3 mb-xl-10">
                            <div class="card h-lg-60 text-center">
                                <div
                                    class="card-body flex-column
                            {{-- d-flex justify-content-between align-items-start --}}
                            ">
                                    <div>
                                        <i class="ki-duotone ki-delivery-3 fs-2hx">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                    <div class="d-flex flex-column my-7">
                                        <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2" id="delivered">
                                            {{ $data['delivered_orders_count'] ?? 0 }}
                                        </span>
                                        <div class="mt-4">
                                            <span class="fw-semibold fs-5 text-gray-500">Delivered</span>
                                        </div>
                                    </div>
                                    {{-- <span class="badge badge-light-success fs-base">
                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>2.1%
                                </span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row gy-5 gx-xl-10">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-xxl">
                                <div class="row gy-5 gx-xl-10">
                                    <div class="col-sm-4 col-xl-3 mb-xl-10">
                                        <div class="card h-lg-60 text-center">
                                            <div
                                                class="card-body flex-column
                                    {{-- d-flex justify-content-between align-items-start --}}
                                    ">
                                                <div>
                                                    <i class="ki-duotone ki-cube-2 fs-2hx">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </div>
                                                <div class="d-flex flex-column my-7">
                                                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2"
                                                        id="total_sales">
                                                        {{ $data['total_sales'] ?? 0 }}
                                                    </span>
                                                    <div class="mt-4">
                                                        <span class="fw-semibold fs-5 text-gray-500">Total Sales
                                                            <br></span>
                                                    </div>
                                                </div>
                                                {{-- <span class="badge badge-light-success fs-base">
                                            <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>2.1%
                                        </span> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4 col-xl-3 mb-xl-10">
                                        <div class="card h-lg-60 text-center">
                                            <div
                                                class="card-body flex-column
                                    {{-- d-flex justify-content-between align-items-start --}}
                                    ">
                                                <div>
                                                    <i class="ki-duotone ki-abstract-26 fs-2hx">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                                <div class="d-flex flex-column my-7">
                                                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2"
                                                        id="new_orderss">
                                                        {{ $data['new_orders_count'] ?? 0 }}
                                                    </span>
                                                    <div class="mt-4">
                                                        <span class="fw-semibold fs-5 text-gray-500">New Orders</span>
                                                    </div>
                                                </div>
                                                {{-- <span class="badge badge-light-success fs-base">
                                            <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>2.1%
                                        </span> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection


@section('script')
    <script>
        var start = moment().subtract(29, "days");
        var end = moment();

        function cb(start, end) {
            $("#kt_daterangepicker_4").html(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));
        }

        $("#kt_daterangepicker_4").daterangepicker({
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


        $(document).on('change', '#kt_daterangepicker_4', function() {
            const fullDate = $(this).val();
            const fullDateArray = fullDate.split(" - ");
            const from = fullDateArray[0];
            const to = fullDateArray[1];

            $.ajax({
                url: '{{ route('retailer.dashboard-reload') }}',
                type: 'POST',
                data: {
                    from: from,
                    to: to,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('response', response);

                    if (response.status) {
                        $('#new_orders').text(response.data.new_orders_count);
                        $('#new_orderss').text(response.data.new_orders_count);
                        $('#confirmed_orders').text(response.data.confirmed_orders_count);
                        $('#ready_for_ship').text(response.data.ready_for_ship_orders_count);
                        $('#delivered').text(response.data.delivered_orders_count);
                        $('#total_sales').text(response.data.total_sales);
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
    </script>
@endsection
