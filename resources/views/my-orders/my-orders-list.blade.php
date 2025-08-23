@extends('layouts.base')
@section('title')
    My Order List | TechtrendMart
@endsection

@php
    function order_status_get($value)
    {
        $statuses = [
            'pending' => 'Pending',
            'approved_by_retailer' => 'Approved By Retailer',
            'transfered_retailer_to_wholesaler' => 'Transferred To Wholesaler',
            'approved_by_wholesaler' => 'Confirmed By Wholesaler',
            'pickup' => 'Pickup',
            'in_transit' => 'In Transit',
            'ofd' => 'OFD',
            'delivered' => 'Delivered',
            'rto' => 'RTO',
            'rtn_to_seller' => 'RTN To Seller',
            'close' => 'Close',
            'cancel' => 'Cancelled',
            'lost' => 'Lost',
            'received' => 'Received',
        ];

        return $statuses[$value] ?? 'Unknown Status';
    }
@endphp
@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">
                            My Orders</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">My Order List</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->

                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->

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

                    <div class="card card-flush">
                        <!-- Search -->
                        <div class="card-title mx-10 my-5 ms-auto">
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

                        <div class="card-body pt-0">
                            <table class="table align-middle table-row-dashed fs-7 table-striped" id="kt_my_order_list_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center py-5 border-0 min-w-50px">NO.</th>
                                        <th class="text-center py-5 border-0 min-w-150px">ORDER DATE</th>
                                        <th class="min-w-300px py-5 border-0">ORDER DETAIL</th>
                                        <th class="min-w-150px py-5 border-0">MEDIA</th>
                                        <th class="min-w-300px py-5 border-0">WHOLESALER DETAIL</th>
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
        @include('layouts.footer')
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function () {
            $.fn.dataTable.ext.errMode = 'none'; // Prevent default alerts

            const table = $('#kt_my_order_list_table').DataTable({
                pageLength: 20,
                lengthMenu: [10, 20, 50, 100],
                processing: true,
                serverSide: true,
                fixedHeader: {
                    header: true,
                    headerOffset: document.querySelector("#kt_app_header_wrapper").offsetHeight // height of your fixed header
                },
                ajax: {
                    url: "{{ route('retailer.my-order.fetch-record') }}",
                    type: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: function (d) {
                        d._token = "{{ csrf_token() }}";
                        d.search = $('#search_field').val();
                    },
                    dataSrc: function (json) {
                        if (json.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Search',
                                text: json.error,
                            }).then(() => {
                                $('#search_field').val('');
                                table.ajax.reload(); // Optional: refresh with empty input
                            });
                            return [];
                        }
                        return json.data;
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred while loading data.',
                        });
                    }
                },
                columns: [
                    { data: 'no', className: 'text-center' },
                    { data: 'order_date', className: 'text-center', orderable: true},
                    { data: 'order_detail' },
                    { data: 'media' },
                    { data: 'wholesaler_detail' },
                ]
            });

            $('#search_field').on('keyup', function () {
                table.ajax.reload();
            });
        });


    </script>
@endsection
