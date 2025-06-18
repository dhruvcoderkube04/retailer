@extends('layouts.base')
@section('title')
    Product List To Be Add | TrendMart
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
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 flex-column justify-content-center my-0">
                            {{ @$wholesaler->company_name }}
                        </h1>
                        <!-- <h3 class="page-heading d-flex text-gray-900 fw-bold fs-7 mt-2 flex-column justify-content-center my-0"></h3> -->
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1 pt-1">
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
                            <li class="breadcrumb-item text-muted">Manage Margin</li>
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

                    <form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                        action="{{ route('retailer.add-category-margin', encryptId(@$wholesaler->user_id)) }}"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="d-flex flex-column gap-7 gap-lg-10">
                                <div class="card card-flush py-4">
                                    <div class="card-body pt-0">
                                        <div class="row mt-3">
                                            <div class="col-md-5">
                                                <label class="required form-label">Sub Categories</label>
                                                <select
                                                    class="form-select mb-2 @error('sub_category_id') is-invalid @enderror"
                                                    id="sub_category_id" data-control="select2" name="sub_category_id"
                                                    data-placeholder="Select an option">
                                                    <option></option>
                                                    @foreach ($subCategories as $sub_category)
                                                        <option value="{{ $sub_category->id }}">
                                                            {{ $sub_category->sub_category_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('sub_category_id')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-5">
                                                <label class="required form-label">Margin</label>
                                                <input type="number" name="margin"
                                                    class="form-control mb-2 @error('margin') is-invalid @enderror"
                                                    placeholder="Enter margin (In Amount)" value="{{ old('margin') }}" />
                                                @error('margin')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-md-4">
                                                <label class="required form-label">Payment Method</label>
                                                <div class="form-check mb-2">
                                                    <input
                                                        class="form-check-input @error('payment_method') is-invalid @enderror"
                                                        type="checkbox" id="cod" name="payment_method[]"
                                                        value="COD"
                                                        {{ in_array('COD', old('payment_method', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="cod">
                                                        COD
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input
                                                        class="form-check-input @error('payment_method') is-invalid @enderror"
                                                        type="checkbox" id="prepaid" name="payment_method[]"
                                                        value="Prepaid"
                                                        {{ in_array('Prepaid', old('payment_method', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="prepaid">
                                                        Prepaid
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input
                                                        class="form-check-input @error('payment_method') is-invalid @enderror"
                                                        type="checkbox" id="semi" name="payment_method[]"
                                                        value="Semi"
                                                        {{ in_array('Semi', old('payment_method', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="semi">
                                                        Semi
                                                    </label>
                                                </div>

                                                @error('payment_method')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mt-5">
                                            <div class="col-md-4">
                                                <button type="submit" id="kt_ecommerce_add_product_submit"
                                                    class="btn btn-primary">
                                                    <span class="indicator-label">Add Margin</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="kt_app_content_container" class="app-container  mt-5">
                    <div class="card card-flush">
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <div class="card-title">
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-ecommerce-product-filter="search"
                                        class="form-control form-control-solid w-250px ps-12" placeholder="Search Product"
                                        id="retailer_margin_details_search" />
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-7" id="kt_retailer_margin_details">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center min-w-100px">Actions</th>
                                        <th class="text-center min-w-200px">Sub Category</th>
                                        <th class="text-center min-w-200px">Payment Method</th>
                                        <th class="text-center min-w-70px">Margin
                                            <br> <span class="text-capitalize fs-9">(In Rs.)</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($addedMarginDetails as $marginDetail)
                                        <tr>
                                            <td class="text-center pe-0">
                                                <button
                                                    class="btn btn-icon btn-success btn-light-success w-30px h-30px me-3 edit-margin-btn"
                                                    data-bs-toggle="model"
                                                    data-wholesaler-id="{{ $wholesaler->user_id }}"
                                                    data-margin-id="{{ $marginDetail->id }}"
                                                    data-bs-target="#kt_modal_edit_ticket" title="Edit">
                                                    <i class="ki-duotone ki-pencil">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </button>

                                                <button type="button"
                                                    class="btn btn-icon btn-danger btn-light-danger w-30px h-30px me-3 delete-margin-btn"
                                                    data-url="{{ route('retailer.remove-category-margin', ['wholesaler_id' => $wholesaler->user_id, 'margin_id' => $marginDetail->id]) }}"
                                                    title="Delete">
                                                    <i class="ki-duotone ki-trash">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                    </i>
                                                </button>
                                            </td>
                                            <td class="text-center" data-order="0">
                                                {{ $marginDetail->sub_category?->sub_category_name ?? '' }}
                                            </td>
                                            <td class="text-center pe-0" data-order="1">
                                                {{ $marginDetail->payment_method }}
                                            </td>
                                            <td class="text-center pe-0" data-order="2">
                                                <div class="badge badge-light-success">
                                                    {{ $marginDetail->margin }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <div class="modal fade" id="kt_modal_edit_margin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Update Margin</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="marginupdateform" class="form" method="POST"
                        action="{{ route('retailer.update-category-margin') }}">
                        @csrf
                        <input type="hidden" id="edit_wholesaler_id" name="wholesaler_id">
                        <input type="hidden" id="edit_margin_id" name="margin_id">

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="edit_sub_category_id" name="sub_category_id" data-control="select2">
                                <option value="">Select Category</option>
                                {{-- append here dynamically --}}
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Margin</label>
                            <input type="number" min="1" class="form-control" id="edit_margin_value"
                                name="margin">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input mt-1" id="edit_payment_cod"
                                    name="payment_method[]" value="COD">
                                <label class="form-check-label mt-1" for="edit_payment_cod">COD</label>
                            </div>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input mt-1" id="edit_payment_prepaid"
                                    name="payment_method[]" value="Prepaid">
                                <label class="form-check-label mt-1" for="edit_payment_prepaid">Prepaid</label>
                            </div>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input mt-1" id="edit_payment_semi"
                                    name="payment_method[]" value="Semi">
                                <label class="form-check-label mt-1" for="edit_payment_semi">Semi</label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Margin</button>
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
            // Initialize DataTable
            const table = $('#kt_retailer_margin_details').DataTable();

            // Search functionality
            $('#retailer_margin_details_search').on('input', function() {
                table.search(this.value).draw();
            });

            // DELETE margin with Swal confirmation
            $('.delete-margin-btn').on('click', function() {
                const url = $(this).data('url');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', 'Margin has been removed.',
                                    'success').then(() => {
                                    location
                                .reload(); // Reload page or use table.row(...).remove().draw() if dynamic
                                });
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });


            $('.edit-margin-btn').on('click', function() {
                const wholesalerId = $(this).data('wholesaler-id');
                const marginId = $(this).data('margin-id');

                $.ajax({
                    url: "{{ route('retailer.edit-category-margin') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        wholesaler_id: wholesalerId,
                        margin_id: marginId
                    },
                    success: function(response) {

                        if (response.success) {
                            const data = response.data;
                            const subCategories = response.subCategories;
                            let $select = $('#edit_sub_category_id');
                            $select.empty();
                            subCategories.forEach(sub_category => {

                                let selected = sub_category.id === data
                                    .sub_category_id ? 'selected' : '';

                                $select.append(
                                    `<option value="${sub_category.id}" ${selected}>${sub_category.sub_category_name}</option>`
                                    );

                            });

                            $('#edit_margin_value').val(data.margin);
                            $('#edit_margin_id').val(data.id);
                            $('#edit_wholesaler_id').val(data.wholesaler_id);

                            let paymentMethods = data.payment_method ?? [];

                            ['COD', 'Prepaid', 'Semi'].forEach(method => {
                                $('#edit_payment_' + method.toLowerCase()).prop(
                                    'checked', paymentMethods.includes(method));
                            });

                            $('#kt_modal_edit_margin').modal('show');

                        }

                    }

                });
            });

            $('#marginupdateform').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('retailer.update-category-margin') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: "Margin Update successfully!",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                // form reset
                                document.getElementById('marginupdateform').reset();
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
