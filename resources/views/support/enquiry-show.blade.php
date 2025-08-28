@extends('layouts.base')

@section('title')
Ticket Manage | TechtrendMart
@endsection

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
                        Ticket List</h1>
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
                        <li class="breadcrumb-item text-muted">Ticket</li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container ">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6 pb-4">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" data-kt-subscription-table-filter="search"
                                    class="form-control form-control-solid w-250px ps-12" placeholder="Search Enquiry" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                           
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-7" id="kt_subscriptions_table">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">SR NO.</th>
                                    <th class="min-w-125px">NAME</th>
                                    <th class="min-w-125px">EMAIL</th>
                                    <th class="min-w-125px">SUBJECT</th>
                                    <th class="min-w-125px">MESSAGE </th>
                                    <th class="min-w-125px">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 fw-semibold fs-6">

                            </tbody>
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        let table = $('#kt_subscriptions_table').DataTable({
            pageLength: 20,
            lengthMenu: [10, 20, 50, 100],
            processing: true,
            serverSide: true,
            // fixedHeader: {
            //     header: true,
            //     headerOffset: document.querySelector("#kt_app_header_wrapper")?.offsetHeight || 0
            // },
            ajax: {
                url: "{{ route('retailer.website-enquiry.fetch-record') }}",
                type: "POST",
                data: function(d) {
                    d.search = $('input[data-kt-subscription-table-filter="search"]').val();
                    d.status = $('#status_filter').val();
                    d._token = '{{ csrf_token() }}';
                },
                error: function(xhr) {
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
                            $('input[data-kt-subscription-table-filter="search"]').val('');
                            table.search('').draw();
                        });
                    }
                }
            },
            columns: [{
                    data: 'sr_no',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'firstname',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'email',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'subject',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'message',
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'created_at',
                    orderable: false,
                    className: 'text-center'
                }
            ]
        });

        // 🔍 Search trigger
        $('input[data-kt-subscription-table-filter="search"]').on('keyup', function() {
            table.ajax.reload();
        });


        // Re-render icons after draw
        table.on('draw', function() {
            if (typeof KTIcon !== 'undefined') {
                KTIcon.update();
            }
        });
    });
</script>
@endsection