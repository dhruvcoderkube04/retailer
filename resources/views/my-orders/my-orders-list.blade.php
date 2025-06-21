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
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            My Orders</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
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
                        <div class="card-title mx-10 my-5">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" data-kt-ecommerce-product-filter="search"
                                    class="form-control form-control-solid w-250px ps-12 bg-secondary" placeholder="Search Product"
                                    id="search_field" />
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <table class="table align-middle table-row-dashed fs-7" id="kt_my_order_list_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center min-w-50px">NO.</th>
                                        <th class="text-center min-w-150px">ORDER DATE</th>
                                        <th class="min-w-300px">ORDER DETAIL</th>
                                        <th class="min-w-150px">MEDIA</th>
                                        <th class="min-w-300px">WHOLESALER DETAIL</th>
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
        @include('layouts.footer')
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            const table = $('#kt_my_order_list_table').DataTable({
            processing: true,
            serverSide: true,
                ajax: {
                    url: "{{ route('retailer.my-order.fetch-record') }}",
                    type: 'POST',
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.search = $('#search_field').val();
                    }
                },
                columns: [
                    { data: 'no', className: 'text-center' },
                    { data: 'order_date', className: 'text-center' },
                    { data: 'order_detail' },
                    { data: 'media' },
                    { data: 'wholesaler_detail' },
                ]
            });

            // for datatable load and table's data search
            var table1 = $("#kt_my_order_list_table").DataTable();
            $("#search_field").on("keyup", function() {
                table1.search(this.value).draw();
            });
        });
    </script>
@endsection
