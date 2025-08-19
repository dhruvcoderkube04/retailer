@extends('layouts.base')

@section('title')
    Coupon Manage | TechtrendMart
@endsection

@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">Coupon List</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-6 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Coupon</li>
                        </ul>
                    </div>
                    {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_coupon">
                            <i class="ki-duotone ki-plus fs-2"></i>
                            Create Coupon
                        </button>
                    </div> --}}
                </div>
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container ">
                    <!--begin::Card-->
                    <div class="card">
                        <div class="card-header border-0 pt-6 pb-5">
                            <div class="card-title">
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-subscription-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Coupon" />
                                </div>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex justify-content-end" data-kt-subscription-table-toolbar="base">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_coupon">
                                        <i class="ki-duotone ki-plus fs-2"></i>
                                        Create Coupon
                                    </button>
                                </div>
                                <div class="d-flex justify-content-end align-items-center d-none" data-kt-subscription-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2" data-kt-subscription-table-select="selected_count"></span>Selected
                                    </div>
                                    <button type="button" class="btn btn-danger" data-kt-subscription-table-select="delete_selected">Delete Selected</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <table class="table align-middle table-row-dashed fs-7 table-striped" id="kt_subscriptions_table">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-125px py-5 border-0 ps-3" style="background: #0d0e12;color:#fff !important;">Actions</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Name</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Status</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Coupon Code</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Used Count</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Valid From</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Valid Until</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Quantity</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Discount Price</th>
                                        <th class="min-w-125px py-5 border-0" style="background: #0d0e12;color:#fff !important;">Created Date</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 fw-semibold fs-6"></tbody>
                            </table>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end::Main-->

    <!-- Add Coupon Modal -->
    <div class="modal fade" id="kt_modal_add_coupon" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                     <h2 class="fw-bold">Create Coupon</h2>
                    <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </button>
                </div>

                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="coupon_add_form" class="form" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Coupon Name</label>
                            <input type="text" class="form-control" id="coupon_name" name="coupon_name">
                            <div class="text-danger" id="error-coupon_name"></div>
                        </div>

                        <div class="mb-3">
                            <label class="required fs-6 fw-semibold mb-2">Expiration Date</label>
                            <div class="position-relative d-flex align-items-center">
                                <i class="ki-duotone ki-calendar-8 fs-2 position-absolute mx-4">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span>
                                </i>
                                <input class="form-control form-control-solid ps-12 flatpickr-input active" required id="kt_daterangepicker_2" placeholder="Select a date" name="offer_date" type="text">
                            </div>
                            <div class="text-danger" id="error-offer_date"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Coupon Code</label>
                            <input class="form-control" id="coupon_code" name="coupon_code">
                            <div class="text-danger" id="error-coupon_code"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Discount Price</label>
                            <input type="number" class="form-control" id="discount_price" name="discount_price">
                            <div class="text-danger" id="error-discount_price"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity">
                            <div class="text-danger" id="error-quantity"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="d-flex">
                                <label class="form-check form-check-inline">
                                    <input type="radio" name="status" value="1" class="form-check-input" checked> Active
                                </label>
                                <label class="form-check form-check-inline">
                                    <input type="radio" name="status" value="0" class="form-check-input"> Inactive
                                </label>
                            </div>
                            <div class="text-danger" id="error-status"></div>
                        </div>

                        <div class="text-center">
                            <button type="submit" id="kt_modal_add_coupon_submit" class="btn btn-primary">Add Coupon</button>
                            <button type="button" id="kt_modal_add_coupon_cancel" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Edit Coupon Modal -->
    <div class="modal fade" id="kt_modal_edit_coupon" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Update Coupon</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="coupon_update_form" class="form" method="POST">
                        @csrf
                        <input type="hidden" id="edit_coupon_id" name="coupon_id">

                        <div class="mb-3">
                            <label class="form-label">Coupon Name</label>
                            <input type="text" class="form-control" id="edit_coupon_name" name="coupon_name">
                            <div class="text-danger" id="editError-coupon_name"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Coupon Code</label>
                            <input class="form-control" id="edit_coupon_code" readonly name="coupon_code">
                            <div class="text-danger" id="editError-coupon_code"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Discount Price</label>
                            <input type="number" class="form-control" readonly id="edit_discount_price" name="discount">
                            <div class="text-danger" id="editError-discount"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="edit_quantity" name="quantity">
                            <div class="text-danger" id="editError-quantity"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="d-flex">
                                <label class="form-check form-check-inline">
                                    <input type="radio" name="status" value="1" class="form-check-input"> Active
                                </label>
                                <label class="form-check form-check-inline">
                                    <input type="radio" name="status" value="0" class="form-check-input"> Inactive
                                </label>
                            </div>
                            <div class="text-danger" id="editError-status"></div>
                        </div>

                        <div class="text-center">
                            <button type="submit" id="kt_modal_edit_coupon_submit" class="btn btn-primary">Update Coupon</button>
                            <button type="button" id="kt_modal_edit_coupon_cancel" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
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
    // Log the DataTable AJAX URL for debugging
   function initDateRangePicker() {
        $("#kt_daterangepicker_2").daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            startDate: moment().startOf("hour"),
            endDate: moment().startOf("hour").add(1, "days"),
            locale: {
                format: "YYYY-MM-DD HH:mm:ss"
            }
        }, function(start, end, label) {
            $('#kt_daterangepicker_2').val(start.format('YYYY-MM-DD HH:mm:ss') + ' - ' + end.format('YYYY-MM-DD HH:mm:ss'));
            $('#start_date').val(start.format('YYYY-MM-DD HH:mm:ss'));
            $('#end_date').val(end.format('YYYY-MM-DD HH:mm:ss'));
        });
    }
    $('#kt_modal_add_coupon').on('shown.bs.modal', function () {
        initDateRangePicker();
    });
    // console.log(  "DataTable AJAX URL: {{ route('coupons.fetch') }}");

    // Initialize DataTable
    let table = $('#kt_subscriptions_table').DataTable({
        processing: true,
        serverSide: true,
        fixedHeader: {
        header: true,
            headerOffset: document.querySelector("#kt_app_header_wrapper").offsetHeight // height of your fixed header
        },
        ajax: {
            url: "{{ route('coupons.fetch') }}",
            type: "POST",
            data: function (d) {
                d.search = $('input[data-kt-subscription-table-filter="search"]').val();
                d._token = '{{ csrf_token() }}';
            }
        },
        columns: [
            { data: 'actions', orderable: false, searchable: false, className: 'ps-3' },
            { data: 'coupon_name' },
            { data: 'status' },
            { data: 'coupon_code' },
            { data: 'used_count' },
            { data: 'valid_from' },
            { data: 'valid_until' },
            { data: 'quantity' },
            { data: 'discount' },
            { data: 'created_at' },
        ]
    });

    // Search trigger
    $('input[data-kt-subscription-table-filter="search"]').on('keyup', function () {
        table.ajax.reload();
    });

    // Re-render icons after table draw
    table.on('draw', function () {
        if (typeof KTIcon !== 'undefined') {
            KTIcon.update();
        }
    });

    // Edit Coupon
    $(document).on('click', '.edit-coupon', function () {
        let couponId = $(this).data('id');
        $.ajax({
            url: `{{ url('edit-coupon') }}/${couponId}`,
            type: 'GET',
            success: function (response) {
                $('#edit_coupon_id').val(response.id);
                $('#edit_coupon_name').val(response.coupon_name);
                $('#edit_coupon_code').val(response.coupon_code);
                $('#edit_discount_price').val(response.discount);
                $('#edit_quantity').val(response.usage_limit);
                $('input[name="status"][value="' + response.status + '"]').prop('checked', true);
                $('#kt_modal_edit_coupon').modal('show');
            },
            error: function () {
                Swal.fire("Error!", "Failed to load coupon details.", "error");
            }
        });
    });

    // Update Coupon
    $('#coupon_update_form').on('submit', function (e) {
        e.preventDefault();
        let couponId = $('#edit_coupon_id').val();
        let formData = new FormData(this);

            $('#kt_modal_edit_coupon').on('submit', function () {
                $('#kt_modal_edit_coupon_submit').prop('disabled', true).html('Updating... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                $('#kt_modal_edit_coupon_cancel').prop('disabled', true);
            });

        $.ajax({
            url: `{{ url('update-coupon') }}/${couponId}`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": '{{ csrf_token() }}'
            },
            success: function (data) {
                $('#kt_modal_edit_coupon_submit').prop('disabled', false).html('Update Coupon');
                $('#kt_modal_edit_coupon_cancel').prop('disabled', false);
                if (data.success) {
                    Swal.fire("Success!", "Coupon updated successfully!", "success").then(() => {
                        $('#kt_modal_edit_coupon').modal('hide');
                        table.ajax.reload(null, false);
                    });
                } else {
                    $('#kt_modal_edit_coupon_submit').prop('disabled', false).html('Update Coupon');
                    $('#kt_modal_edit_coupon_cancel').prop('disabled', false);
                    Swal.fire("Error!", "Update failed.", "error");
                }
            },
            error: function (xhr) {
                $('#kt_modal_edit_coupon_submit').prop('disabled', false).html('Update Coupon');
                $('#kt_modal_edit_coupon_cancel').prop('disabled', false);

                let errors = xhr.responseJSON.errors;

                // Clear previous errors
                $('.text-danger').html('');
                $('.form-control, .form-check-input').removeClass('is-invalid');

                if (errors) {
                    $.each(errors, function (key, value) {
                        // Show error below correct field
                        $('#editError-' + key).html(value[0]);

                        // Highlight the field with red border
                        const input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');

                        // Avoid rebinding the same event
                        input.off('input change').on('input change', function () {
                            $(this).removeClass('is-invalid');
                            $('#editError-' + key).html('');
                        });
                    });
                }
            }

        });
    });

    // Delete Coupon
    $(document).on('click', '.delete-coupon', function () {
        let couponId = $(this).data("id");
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
                    url: "{{ route('retailer.coupon.delete') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        coupon_id: couponId
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire("Deleted!", "Coupon has been removed.", "success");
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error!", "Something went wrong.", "error");
                    }
                });
            }
        });
    });

    // Add Coupon
    $('#coupon_add_form').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);

            $('#kt_modal_add_coupon').on('submit', function () {
                $('#kt_modal_add_coupon_submit').prop('disabled', true).html('Adding... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                $('#kt_modal_add_coupon_cancel').prop('disabled', true);
            });
            // $('#editAddressForm').on('submit', function () {
            //     $('#editsubmitBtn').prop('disabled', true).html('Updating... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            //     $('#editcancelBtn').prop('disabled', true);
            // });
        $.ajax({
            url: "{{ route('retailer.coupon.add') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#kt_modal_add_coupon_submit').prop('disabled', false).html('Add Coupon <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                $('#kt_modal_add_coupon_cancel').prop('disabled', false);
                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: "Coupon added successfully!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        $('#kt_modal_add_coupon').modal('hide');
                        document.getElementById('coupon_add_form').reset();
                        table.ajax.reload(null, false);
                    });
                } else {
                    $('#kt_modal_add_coupon_submit').prop('disabled', false).html('Add Coupon <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    $('#kt_modal_add_coupon_cancel').prop('disabled', false);
                    Swal.fire({
                        title: "Error!",
                        text: response.message || "Something went wrong!",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function (xhr) {
                $('#kt_modal_add_coupon_submit').prop('disabled', false).html('Add Coupon');
                $('#kt_modal_add_coupon_cancel').prop('disabled', false);

                let errors = xhr.responseJSON.errors;

                // Clear previous errors
                $('.text-danger').html('');
                $('.form-control, .form-check-input').removeClass('is-invalid');

                if (errors) {
                    $.each(errors, function (key, value) {
                        $('#error-' + key).html(value[0]);

                        // Highlight the input field
                        const input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');

                        // 🔄 Add live validation remover
                        input.on('input change', function () {
                            $(this).removeClass('is-invalid');
                            $('#error-' + key).html('');
                        });
                    });
                }
            }


        });
    });
    $('#kt_modal_add_coupon, #kt_modal_edit_coupon').on('hidden.bs.modal', function () {
        // Reset form
        $('#coupon_add_form')[0].reset();

        // Clear validation error messages
        $('.text-danger').html('');

        // Remove red borders
        $('.form-control, .form-check-input').removeClass('is-invalid');
    });

});
</script>
@endsection
