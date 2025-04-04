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
                                        <th class="min-w-125px">Ticket ID </th>
                                        <th class="min-w-125px">Message </th>
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
                                                    <img src="{{ asset('uploads/ticket/' . $ticket->ref_image) }}"
                                                        class="" alt="">
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="badge
                                                    @if ($ticket->status == 'Pending') badge-light-warning
                                                    @elseif($ticket->status == 'In Progress') badge-light-primary
                                                    @elseif($ticket->status == 'Resolved') badge-light-success
                                                    @else badge-light-danger @endif">
                                                    {{ $ticket->status }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="badge badge-light">{{ $ticket->created_at }}</div>
                                            </td>
                                            <td class="text-end">
                                                <button
                                                    class="btn btn-icon btn-danger btn-light-danger w-30px h-30px me-3 delete-ticket"
                                                    data-id="{{ $ticket->ticket_id }}" data-bs-toggle="tooltip"
                                                    title="remove">
                                                    <i class="ki-duotone ki-trash">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                    </i>
                                                </button>
                                                <button
                                                    class="btn btn-icon btn-success btn-light-success w-30px h-30px me-3 edit-ticket"
                                                    data-id="{{ $ticket->ticket_id }}" data-bs-toggle="model"
                                                    data-bs-target="#kt_modal_edit_ticket" title="Edit">
                                                    <i class="ki-duotone ki-pencil">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </button>
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
        <!--begin::Footer-->
        <!--end::Footer-->
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

    <div class="modal fade" id="kt_modal_edit_ticket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Update Coupon</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="ticketupdateform" class="form" method="POST">
                        @csrf
                        <input type="hidden" id="edit_ticket_id" name="ticket_id">

                        <!--begin::Col-->
                        <div class="col-lg-8">
                            <!--begin::Image input-->

                            <div class="image-input image-input-outline" data-kt-image-input="true"
                                style="background-image: url('assets/media/svg/avatars/blank.svg')">
                                <!--begin::Preview existing avatar-->
                                <div class="image-input-wrapper w-125px h-125px" id="edit_ticket_image_ref">
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
                            <input type="text" class="form-control ticket_name" id="edit_ticket_subject"
                                name="subject">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ticket Description</label>
                            <input type="text" class="form-control ticket_description" id="edit_ticket_description"
                                name="ticket_description">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Ticket</button>
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

            $('.edit-ticket').on('click', function() {
                var ticketId = $(this).data('id');

                $.ajax({
                    url: '/edit-ticket/' + ticketId, // Backend route to fetch coupon details
                    type: 'GET',
                    success: function(response) {
                        $('#edit_ticket_id').val(response.ticket_id);
                        $('#edit_ticket_subject').val(response.subject);
                        $('#edit_ticket_description').val(response.description);
                        // image get and set image

                        if (response.image_ref && response.image_ref.trim() !== "") {
                            let imageUrl = "{{ asset('uploads/ticket') }}/" + response
                                .image_ref;
                            $('#edit_ticket_image_ref').css('background-image', 'url("' +
                                imageUrl + '")');
                        } else {
                            console.log("No image found, setting default.");
                            $('#edit_ticket_image_ref').css('background-image',
                                'url("assets/media/svg/avatars/blank.svg")');
                        }

                        $('#kt_modal_edit_ticket').modal('show');
                    }
                });
            });

            document.getElementById("ticketupdateform").addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent default form submission

                let ticketId = document.getElementById("edit_ticket_id").value;
                let formData = new FormData(this);

                fetch(`/update-ticket/${ticketId}`, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            "Accept": "application/json"
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: "Success!",
                                text: "Ticket updated successfully!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.reload(); // Reload page after successful update
                            });
                        } else {
                            alert("Error updating coupon");
                        }
                    })
                    .catch(error => console.error("Error:", error));
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

            $(".delete-ticket").click(function() {
                let ticket_id = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('retailer.ticket.delete') }}", // Ensure the correct route name
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                ticket_id: ticket_id,
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire("Deleted!", "Ticket has been removed.",
                                        "success");
                                    location
                                .reload(); // Reload the page or update the UI dynamically
                                } else {
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function(xhr) {
                                Swal.fire("Error!",
                                    "Something went wrong. Please try again.",
                                    "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
