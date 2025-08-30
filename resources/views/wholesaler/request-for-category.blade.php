@extends('layouts.base')
@section('title')
    Request For Category | TechtrendMart
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
                            <li class="breadcrumb-item text-muted">Request For Category</li>
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
                        <div class="alert alert-success text-green-600 p-2" id="flash-message">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger text-green-600 p-2" id="flash-message">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="categoryRequestForm" class="form d-flex flex-column flex-lg-row"
                        action="#"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="d-flex flex-column gap-7 gap-lg-10">
                                <div class="card card-flush py-4">
                                    <div class="card-body pt-0">
                                        <div class="row mt-3">
                                            <div class="col-md-5">
                                                <input type="hidden" name="wholesaler_id" value="{{ $wholesaler->user_id }}">
                                                <label class="required form-label">Sub Categories</label>
                                                <select class="form-select mb-2 @error('sub_category_ids') is-invalid @enderror"
                                                    id="sub_category_ids" name="sub_category_ids[]" data-control="select2" data-placeholder="Select sub categories" multiple="multiple">
                                                    <option></option>
                                                    @foreach ($subCategories as $subCategory)
                                                        <option value="{{ $subCategory->sub_category_id }}"
                                                            {{ collect(old('sub_category_ids'))->contains($subCategory->sub_category_id) ? 'selected' : '' }}>
                                                            {{ $subCategory->sub_category_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('sub_category_ids')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mt-5">
                                            <div class="col-md-4">
                                                <button type="submit" id="kt_ecommerce_add_product_submit"
                                                    class="btn btn-primary">
                                                    <span class="indicator-label">Send Request</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#categoryRequestForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: '{{ route("wholesaler.request.access") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Request Sent',
                        text: response.message,
                    }).then(() => window.location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong.', 'error');
                }
            });
        });
    });
</script>
@endsection
