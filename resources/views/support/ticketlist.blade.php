@extends('layouts.base')

@section('title')
    Ticket Manage | TrendMart
@endsection

@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Ticket List</h1>
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
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header border-0 pt-6">
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
                                    <!--begin::Add subscription-->
                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_add_ticket">
                                        <i class="ki-duotone ki-plus fs-2"></i>
                                        Create Ticket
                                    </button>
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
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_subscriptions_table">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_subscriptions_table .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-125px">Ticket ID</th>
                                        <th class="min-w-125px">Message</th>
                                        <th class="min-w-125px">Description</th>
                                        <th class="min-w-125px">Image Ref</th>
                                        <th class="min-w-125px">Status</th>
                                        <th class="min-w-125px">Created Date</th>
                                        <th class="text-end min-w-70px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" value="1" />
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#"
                                                    class="text-gray-800 text-hover-primary mb-1">{{ $ticket->ticket_id }}</a>
                                            </td>
                                            <td>
                                                <p>{{ $ticket->subject }}</p>
                                            </td>
                                            <td>
                                                <p>{{ $ticket->description }}</p>
                                            </td>
                                            <td>
                                                <div class="symbol symbol-50px me-3">
                                                    <img src="{{ $ticket->ref_image }}" class="" alt="">
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $status = strtolower($ticket->status);
                                                    $badgeClass = match($status) {
                                                        'open' => 'badge badge-danger',
                                                        'in progress' => 'badge badge-info',
                                                        'resolved' => 'badge badge-success',
                                                        'closed' => 'badge badge-secondary',
                                                        default => 'badge badge-light',
                                                    };
                                                @endphp
                                                <span class="{{ $badgeClass }}" data-status="{{ $ticket->status }}">
                                                    {{ ucfirst($ticket->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="badge badge-light">{{ $ticket->created_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="text-end">
                                                @if ($ticket->status =='Closed')
                                                    <select class="form-select form-select-sm ticket-status"
                                                            data-ticket-id="{{ $ticket->ticket_id }}">
                                                        <option value="" {{ $ticket->status == '' ? 'selected' : '' }}>Action</option>
                                                        <option value="Open" {{ $ticket->status == 'Open' ? 'selected' : '' }}>Open</option>
                                                    </select>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
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

                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
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
                            <input type="text" class="form-control" id="ticket_description"
                                name="ticket_description">
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
        $(document).ready(function() {
            console.log('Document ready, attaching event listeners to .ticket-status');
            // Handle status change using jQuery
            $(document).on('change', '.ticket-status', function() {
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
                    success: function(data) {
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
                    error: function(xhr, status, error) {
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

            $('#ticketaddform').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('retailer.generate.ticket') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
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
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = "";
                        $.each(errors, function(key, value) {
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
