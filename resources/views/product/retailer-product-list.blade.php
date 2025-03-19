@extends('layouts.base')
@section('title')
    Product List | Wholesaler Wise Product List
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
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 flex-column justify-content-center my-0">
                            {{ $wholesaler->company_name }}
                        </h1>
                        <!-- <h3 class="page-heading d-flex text-gray-900 fw-bold fs-7 mt-2 flex-column justify-content-center my-0"></h3> -->
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Home</a>
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
                <div id="kt_app_content_container" class="app-container container-xxl">

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
                        action="{{ route('retailer.add-category-margin', $wholesaler->user_id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="d-flex flex-column gap-7 gap-lg-10">
                                <div class="card card-flush py-4">
                                    <div class="card-body pt-0">
                                        <div class="row mt-3">
                                            <div class="col-md-5">
                                                <label class="required form-label">Categories</label>
                                                <select class="form-select mb-2 @error('category_id') is-invalid @enderror"
                                                    id="category_id" data-control="select2" name="category_id"
                                                    data-placeholder="Select an option">
                                                    <option></option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">
                                                            {{ $category->category_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- <input type="hidden" id="wholesaler_id" value="{{ $wholesaler->user_id }}"> --}}

                                            {{-- <div class="col-md-7">
                                                <label class="form-label">Products</label>
                                                <select class="form-select mb-2 @error('product_id') is-invalid @enderror"
                                                    id="product_id" data-control="select2" name="product_id[]"
                                                    data-placeholder="Select an option"
                                                    multiple="multiple">
                                                    <option></option>
                                                </select>
                                                @error('product_id')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div> --}}
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

                <div id="kt_app_content_container" class="app-container container-xxl mt-5">
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
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="retailer_margin_details">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center min-w-200px">Category</th>
                                        <th class="text-center min-w-200px">Payment Method</th>
                                        <th class="text-center min-w-70px">Margin
                                            <br> <span class="text-capitalize fs-9">(In Rs.)</span>
                                        </th>
                                        <th class="text-center min-w-100px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($addedMarginDetails as $marginDetail)
                                        <tr>
                                            <td class="text-center" data-order="0">
                                                {{ $marginDetail->category->category_name }}
                                            </td>
                                            <td class="text-center pe-0" data-order="1">
                                                {{ $marginDetail->payment_method }}
                                            </td>
                                            <td class="text-center pe-0" data-order="2">
                                                <div class="badge badge-light-success">
                                                    {{ $marginDetail->margin }}
                                                </div>
                                            </td>
                                            <td class="text-center pe-0">
                                                <a href="{{ route('retailer.edit-category-margin', ['wholesaler_id' => $wholesaler->user_id, 'margin_id' => $marginDetail->id]) }}"
                                                    class="btn btn-sm btn-primary">Edit</a>

                                                <form
                                                    action="{{ route('retailer.remove-category-margin', ['wholesaler_id' => $wholesaler->user_id, 'margin_id' => $marginDetail->id]) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to Remove?');">Delete</button>
                                                </form>

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
@endsection

@section('script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/products.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
    <script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>

    <script>
        $(document).ready(function() {
            const table = $('#retailer_margin_details').DataTable();

            $('#retailer_margin_details_search').on('input', function() {
                table.search(this.value).draw();
            });

            // $(document).on('change', '#category_id', function() {
            //     const category_id = $(this).val();
            //     const wholesale_id = $('#wholesaler_id').val();

            //     $('#product_id').empty().append('<option></option>');

            //     if (category_id) {
            //         $.ajax({
            //             url: "{{ route('retailer.get-category-wise-products') }}",
            //             method: 'GET',
            //             data: {
            //                 category_id: category_id,
            //                 wholesale_id: wholesale_id,
            //                 _token: '{{ csrf_token() }}'
            //             },
            //             success: function(response) {
            //                 if (response.status) {
            //                     response.data.forEach(function(product) {
            //                         $('#product_id').append(new Option(product.name,
            //                             product.id));
            //                     });

            //                     $('#product_id').trigger('change');
            //                 } else {
            //                     console.log('Error: ' + response.msg);
            //                 }
            //             },
            //             error: function(xhr, status, error) {
            //                 console.error('AJAX Error:', error);
            //             }
            //         });
            //     }
            // });
        });
    </script>
@endsection
