@extends('layouts.base')
@section('title')
    TrendMart| My Category List
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
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">My
                            Category List</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Category</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">My Category list</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::API keys-->
                    <div class="card">
                        <!--begin::Header-->
                        <div class="card-header card-header-stretch">
                            <!--begin::Title-->
                            <div class="card-title">
                                <h3>My Category</h3>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <!--begin::Table wrapper-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9"
                                    id="kt_categroy_table">
                                    <!--begin::Thead-->
                                    <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                        <tr>
                                            <th class="min-w-175px ps-9">Images</th>
                                            <th class="min-w-175px ps-9">Category</th>
                                            <th class="min-w-250px px-0">Sub Category</th>
                                            <th class="min-w-100px">Created</th>
                                            <th class="min-w-250px px-0">Action</th>
                                        </tr>
                                    </thead>
                                    <!--end::Thead-->
                                    <!--begin::Tbody-->
                                    <tbody class="fs-6 fw-semibold text-gray-600">
                                        @if (count($retailerCateogries) > 0)
                                            @foreach ($retailerCateogries as $category)
                                                <tr class="data-load" data-id="{{ $category->id }}">
                                                    <td class="ps-9">
                                                        <img id="sub-category-image"
                                                            src="{{ $category->category_image ? $category->category_image : asset('assets/media/images/no_image.jpg') }}"
                                                            class="w-40px me-3" alt="">
                                                    </td>
                                                    <td class="ps-9">{{ strtoupper($category->category->category_name) }}
                                                    </td>
                                                    <td data-bs-target="license" class="ps-0">
                                                        {{ strtoupper($category->subCategory->sub_category_name) }}</td>

                                                    <td>{{ $category->created_at }}</td>

                                                    <td>
                                                        <button class="btn btn-icon btn-light-danger w-30px h-30px me-3"
                                                            id="remove-btn" data-category_id="{{ $category->category_id }}"
                                                            data-sub_category="{{ $category->sub_category_id }}"
                                                            data-id="{{ $category->id }}" data-bs-toggle="tooltip"
                                                            aria-label="Delete">
                                                            <i class="ki-duotone ki-trash fs-3"><span
                                                                    class="path1"></span><span class="path2"></span><span
                                                                    class="path3"></span>
                                                                <span class="path4"></span><span class="path5"></span></i>
                                                        </button>
                                                        <button class="btn btn-icon btn-light-secondary w-30px h-30px"
                                                            id="image-upload"
                                                            data-id="{{ $category->id }}"data-bs-toggle="tooltip"
                                                            data-image="{{ $category->category_image }}"data-bs-toggle="tooltip"
                                                            aria-label="Image Uplaod">
                                                            <i class="ki-duotone ki-setting-3 fs-3"><span
                                                                    class="path1"></span><span class="path2"></span><span
                                                                    class="path3"></span><span class="path4"></span><span
                                                                    class="path5"></span></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">No data found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <!--end::Tbody-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table wrapper-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::API keys-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
    </div>
    <!--end:::Main-->

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="category-image-update" tabindex="-1" aria-labelledby="category-image-update-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="category-image-update-label">Update Category Image</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="categoryImageForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-5 text-center">
                            <label class="form-label fw-bold">Current Category Image:</label>
                            <br>
                            <img id="image-preview" src="" alt="Category Image" class="img-fluid rounded"
                                style="max-width: 200px; max-height: 200px; display: none; border: 1px solid #ccc; margin-bottom: 10px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload New Image:</label>
                            <input type="file" class="form-control" name="category_image" id="category_image">
                        </div>
                        <span class="text-danger mt-2 d-none" id="image-error"></span>

                        <input type="hidden" name="retailer_category_id" id="retailer_category_id">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


@endsection


@section('script')
    <script>
        $(document).ready(function() {
            var xhr;

            function request_call(url, mydata) {
                if (xhr && xhr.readyState != 4) {
                    xhr.abort();
                }

                xhr = $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: mydata,
                });
            };

            $(document).on('click', '#remove-btn', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Are You sure to Delete it !',
                    showCancelButton: true,
                    confirmButtonColor: '#000',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const subCategoryId = $(this).attr('data-sub_category');
                        const categoryId = $(this).attr('data-category_id');
                        const id = $(this).attr('data-id');

                        request_call("{{ url('remove-category') }}", "category_id=" + categoryId +
                            "&sub_category=" + subCategoryId + "&id=" + id);
                        xhr.done(function(mydata) {

                            // Swal.fire({
                            //     icon:'success',
                            //     title: 'Subcategory Remove Successuflly!',
                            //     showCancelButton: true
                            // })

                            $("#kt_categroy_table").load(location.href +
                                " #kt_categroy_table");
                        });
                        xhr.fail(function(mydata) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Subcategory Remove Failed!',
                                showCancelButton: true
                            })
                        });
                    }
                });

            });

            $(document).on("click", "#image-upload", function() {
                let categoryId = $(this).data("id");
                let imageUrl = $(this).data("image");

                $("#retailer_category_id").val(categoryId);

                if (imageUrl) {
                    $("#image-preview").attr("src",imageUrl).show();
                } else {
                    $("#image-preview").attr("src", 'assets/media/images/no_image.jpg').show();
                }

                $("#category-image-update").modal("show");
            });

            // Handle form submission with AJAX
            $(document).on("submit", "#categoryImageForm", function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('retailer.category-image.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $("#image-error").addClass("d-none").text(""); // Reset error message
                    },
                    success: function(response) {
                        if (response.status) {
                            $("#kt_categroy_table").load(location.href + " #kt_categroy_table");
                            $("#category-image-update").modal("hide");
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.category_image) {
                                $("#image-error").removeClass("d-none").text(errors
                                    .category_image[0]);
                            }
                        } else {
                            alert("Something went wrong! " + xhr.responseJSON.msg);
                        }
                    },
                });
            });
        });
    </script>
@endsection
