@extends('layouts.base')
@section('title')
    Coupon Manage | TrendMart
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
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Coupon List</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{route('retailer.dashboard')}}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Coupon</li>
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
                                    <input type="text" data-kt-subscription-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Coupon" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Toolbar-->
                                <div class="d-flex justify-content-end" data-kt-subscription-table-toolbar="base">
                                    <!--begin::Add subscription-->
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_coupan">
                                            <i class="ki-duotone ki-plus fs-2"></i>
                                            Create Coupon
                                        </button>
                                    <!--end::Add subscription-->
                                </div>
                                <!--end::Toolbar-->
                                <!--begin::Group actions-->
                                <div class="d-flex justify-content-end align-items-center d-none" data-kt-subscription-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                    <span class="me-2" data-kt-subscription-table-select="selected_count"></span>Selected</div>
                                    <button type="button" class="btn btn-danger" data-kt-subscription-table-select="delete_selected">Delete Selected</button>
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
                                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_subscriptions_table .form-check-input" value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-125px">Name</th>
                                        <th class="min-w-125px">Status</th>
                                        <th class="min-w-125px">Coupon Code</th>
                                        <th class="min-w-125px">Disount Price</th>
                                        <th class="min-w-125px">Created Date</th>
                                        <th class="text-end min-w-70px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">

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

    <div class="modal fade" id="kt_modal_add_coupan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Create Coupon</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="coupanaddform" class="form" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Coupon Name</label>
                            <input type="text" class="form-control" id="coupon_name" name="coupon_name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Coupon Code</label>
                            <input class="form-control" id="coupon_code" name="coupon_code"></input>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Disount Price</label>
                            <input type="number" class="form-control" id="discount_price" name="discount_price">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity">
                        </div>

                        {{-- <div class="mb-3">
                            <label class="form-label">Select Date Range</label>
                            <input type="text" class="form-control" id="dateRange" name="date_range" placeholder="Select date range">
                        </div> --}}

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
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add Coupon</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="kt_modal_edit_coupan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Update Coupon</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="coupanupdateform" class="form" method="POST">
                        @csrf


                        <input type="hidden" id="edit_coupon_id" name="coupon_id">

                        <div class="mb-3">
                            <label class="form-label">Coupon Name</label>
                            <input type="text" class="form-control coupon_name" id="edit_coupon_name" name="coupon_name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Coupon Code</label>
                            <input class="form-control" id="edit_coupon_code" name="coupon_code"></input>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Disount Price</label>
                            <input type="number" class="form-control" id="edit_discount_price" name="discount">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="edit_quantity" name="quantity">
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
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Coupon</button>
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
        // 📊 Initialize DataTable
        let table = $('#kt_subscriptions_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('coupons.fetch') }}",
                type: "POST",
                data: function (d) {
                    d.search = $('input[data-kt-subscription-table-filter="search"]').val();
                    d._token = '{{ csrf_token() }}';
                }
            },
            columns: [
                { data: 'checkbox', orderable: false, searchable: false },
                { data: 'coupon_name' },
                { data: 'status' },
                { data: 'coupon_code' },
                { data: 'discount' },
                { data: 'created_at' },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });

        // 🔍 Search trigger
        $('input[data-kt-subscription-table-filter="search"]').on('keyup', function () {
            table.ajax.reload();
        });

        // 🔁 Re-render icons after table draw
        table.on('draw', function () {
            if (typeof KTIcon !== 'undefined') {
                KTIcon.update();
            }
        });

        // ✏️ Edit Coupon
        $(document).on('click', '.edit-coupan', function () {
            let couponId = $(this).data('id');
            $.ajax({
                url: `/edit-coupon/${couponId}`,
                type: 'GET',
                success: function (response) {
                    $('#edit_coupon_id').val(response.id);
                    $('#edit_coupon_name').val(response.coupon_name);
                    $('#edit_coupon_code').val(response.coupon_code);
                    $('#edit_discount_price').val(response.discount);
                    $('#edit_quantity').val(response.usage_limit);
                    $('input[name="status"][value="' + response.status + '"]').prop('checked', true);
                    $('#kt_modal_edit_coupan').modal('show');
                },
                error: function () {
                    Swal.fire("Error!", "Failed to load coupon details.", "error");
                }
            });
        });

        // 💾 Update Coupon
        $('#coupanupdateform').on('submit', function (e) {
            e.preventDefault();
            let couponId = $('#edit_coupon_id').val();
            let formData = new FormData(this);

            $.ajax({
                url: `/update-coupon/${couponId}`,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function (data) {
                    if (data.success) {
                        Swal.fire("Success!", "Coupon updated successfully!", "success")
                            .then(() => {
                                $('#kt_modal_edit_coupan').modal('hide');
                                table.ajax.reload(null, false);
                            });
                    } else {
                        Swal.fire("Error!", "Update failed.", "error");
                    }
                },
                error: function () {
                    Swal.fire("Error!", "An unexpected error occurred.", "error");
                }
            });
        });

        // ❌ Delete Coupon
        $(document).on('click', '.delete-coupan', function () {
            let coupon_id = $(this).data("id");

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
                            coupon_id: coupon_id
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

        // ➕ Add Coupon
        $('#coupanaddform').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('retailer.coupon.add') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: "Coupon added successfully!",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            document.getElementById('coupanaddform').reset();
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
