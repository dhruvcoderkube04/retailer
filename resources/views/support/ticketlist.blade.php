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
                                        class="form-control form-control-solid w-250px ps-12" placeholder="Search Ticket" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Toolbar-->
                                <div class="d-flex justify-content-end" data-kt-subscription-table-toolbar="base">
                                    <!--begin::Status Filter-->
                                    <div class="ms-4 mx-3">
                                        <select id="status_filter" class="form-select form-select-solid"
                                            data-control="select2" data-hide-search="false"
                                            data-placeholder="Filter by Status">
                                            <option></option>
                                            <option value="open">Open</option>
                                            <option value="in progress">In Progress</option>
                                            <option value="resolved">Resolved</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                    </div>
                                    <!--end::Status Filter-->
                                    <!--begin::Add subscription-->
                                    {{-- <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_add_ticket">
                                        <i class="ki-duotone ki-plus fs-2"></i>
                                        Create Ticket
                                    </button> --}}
                                    <a href="{{route('retailer.create.ticket')}}" class="btn btn-primary">
                                        <i class="ki-duotone ki-plus fs-2"></i>
                                        Create Ticket
                                    </a>
                                    <!--end::Add subscription-->
                                </div>
                                <!--end::Toolbar-->
                                <!--begin::Group actions-->
                                <div class="d-flex justify-content-end align-items-center d-none"
                                    data-kt-subscription-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2"
                                            data-kt-subscription-table-select="selected_count"></span>Selected
                                    </div>
                                    <button type="button" class="btn btn-danger"
                                        data-kt-subscription-table-select="delete_selected">Delete Selected</button>
                                </div>
                                <!--end::Group actions-->
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
                                        <th class="min-w-125px">Ticket ID</th>
                                        <th class="min-w-125px">Message</th>
                                        <th class="min-w-125px">Description</th>
                                        <th class="min-w-125px">Category</th>
                                        <th class="min-w-125px">Image Ref</th>
                                        <th class="min-w-125px">Status</th>
                                        <th class="min-w-125px">Created Date</th>
                                        <th class="min-w-125px">Actions</th>
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
    <!--end:::Main-->
    <div class="modal fade" id="kt_modal_add_ticket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Create Ticket</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="ticketaddform" class="form" method="POST">
                        @csrf
                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Image input-->

                            <div class="image-input image-input-outline" data-kt-image-input="true"
                                style="background-image: url('assets/media/svg/avatars/blank.svg')">
                                <!--begin::Preview existing avatar-->
                                <div class="image-input-wrapper w-125px h-125px"
                                    style="background-image: url('{{ asset('uploads/company_profile/') }}')">
                                </div>

                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                    <i class="ki-duotone ki-pencil fs-7">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <!--begin::Inputs-->
                                    <input type="file" name="ticket_image_ref" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="avatar_remove" />
                                    <!--end::Inputs-->
                                </label>
                                <!--end::Label-->
                                <!--begin::Cancel-->
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <!--end::Cancel-->
                                <!--begin::Remove-->
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <!--end::Remove-->
                            </div>
                            <!--end::Image input-->
                            <!--begin::Hint-->
                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                            <!--end::Hint-->
                        </div>
                        <!--end::Col-->
                        <div class="mb-3">
                            <label class="form-label">Ticket Name</label>
                            <input type="text" class="form-control" id="subject" name="subject">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ticket Description</label>
                            <input type="text" class="form-control" id="ticket_description" name="ticket_description">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Create Ticket</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            console.log('Document ready, attaching event listeners to .ticket-status');
            // 📊 Initialize DataTable
            let table = $('#kt_subscriptions_table').DataTable({
                pageLength: 20,
                lengthMenu: [10, 20, 50, 100],
                processing: true,
                serverSide: true,
                // fixedHeader: {
                // header: true,
                //     headerOffset: document.querySelector("#kt_app_header_wrapper").offsetHeight // height of your fixed header
                // },
                ajax: {
                    url: "{{ route('fetch.retailer.ticket.list') }}",
                    type: "POST",
                    data: function (d) {
                        d.search = $('input[data-kt-subscription-table-filter="search"]').val();
                        d.status = $('#status_filter').val();
                        d._token = '{{ csrf_token() }}';
                    },
                    error: function (xhr) {
                        // Detect 400 Bad Request from Laravel
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

                                // Clear only the search input field that caused it
                                $('input[data-kt-subscription-table-filter="search"]').val('');
                                // You can choose to comment this out to prevent auto-refresh
                                dataTable.search('').draw();
                            });
                        }
                    }
                },
                columns: [
                    { data: 'ticket_id', orderable: false, className: 'text-center', },
                    { data: 'subject', orderable: false, className: 'text-center', },
                    { data: 'description', orderable: false, className: 'text-center', },
                    { data: 'category', orderable: false, className: 'text-center', },
                    { data: 'ref_image', orderable: false, searchable: false, className: 'text-center', },
                    { data: 'status', orderable: false, className: 'text-center', },
                    { data: 'created_at', orderable: false, className: 'text-center', },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-center', },
                ]
            });

            // 🔍 Search trigger
            $('input[data-kt-subscription-table-filter="search"]').on('keyup', function () {
                table.ajax.reload();
            });

            // 🔄 Status filter
            $('#status_filter').on('change', function () {
                table.ajax.reload();
            });

            // 🔁 Re-render icons after table draw
            table.on('draw', function () {
                if (typeof KTIcon !== 'undefined') {
                    KTIcon.update();
                }
            });

            // Handle status change using jQuery
            $(document).on('change', '.ticket-status', function () {
                console.log('Ticket status change event triggered');
                console.log('Selected value:', $(this).val());
                console.log('Ticket ID:', $(this).data('ticket-id'));

                const ticketId = $(this).data('ticket-id');
                const newStatus = $(this).val();
                const row = $(this).closest('tr');
                const badge = row.find('td span.badge');
                const url = "{{ url('/ticket') }}/" + ticketId + "/update-status";
                const csrfToken = '{{ csrf_token() }}';

                console.log('Row:', row);
                console.log('Badge:', badge);
                console.log('Fetch URL:', url);
                console.log('CSRF Token:', csrfToken);
                console.log('Request body:', { status: newStatus });

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    data: JSON.stringify({ status: newStatus }),
                    contentType: 'application/json',
                    success: function (data) {
                        console.log('AJAX success, response:', data);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Notice',
                                text: data.message
                            });

                            // Update the badge text and class
                            badge.text(data.status.charAt(0).toUpperCase() + data.status.slice(1)); // Capitalize first letter
                            const statusLower = data.status.toLowerCase();
                            badge.removeClass().addClass('badge'); // Reset classes
                            if (statusLower === 'open') {
                                badge.addClass('badge-danger');
                            } else if (statusLower === 'in progress') {
                                badge.addClass('badge-info');
                            } else if (statusLower === 'closed') {
                                badge.addClass('badge-secondary');
                            } else if (statusLower === 'resolved') {
                                badge.addClass('badge-success');
                            } else {
                                badge.addClass('badge-light');
                            }
                            badge.attr('data-status', data.status);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Notice',
                                text: 'Failed to update status: ' + (data.error || 'Unknown error')
                            });
                            // Revert the dropdown to the previous value
                            $(this).val(badge.attr('data-status'));
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', xhr, status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Notice',
                            text: 'An error occurred while updating the ticket status: ' + (xhr.responseJSON?.error || error)
                        });
                        // Revert the dropdown to the previous value
                        $(this).val(badge.attr('data-status'));
                    }
                });
            });

            $('#ticketaddform').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('retailer.generate.ticket') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: "Ticket Generate successfully!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                // form reset
                                document.getElementById('ticketaddform').reset();
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong!",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = "";
                        $.each(errors, function (key, value) {
                            errorMsg += value[0] + "\n";
                        });

                        Swal.fire({
                            title: "Validation Error",
                            text: errorMsg,
                            icon: "warning",
                            confirmButtonText: "OK"
                        });
                    }
                });
            });
        });
    </script>
@endsection
