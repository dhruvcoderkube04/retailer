@extends('layouts.base')
@section('title')
    Add Product | TrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Add Product</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="#" class="text-muted text-hover-primary">Product</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Product Detail</li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('retailer.product') }}" class="btn btn-sm fw-bold btn-primary">
                            View Product List
                        </a>
                    </div>
                </div>
            </div>

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
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
                    <!--begin::Form-->
                    <form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                        action="{{ route('retailer.post.product') }}" method="post" onsubmit="return validateForm(event)"
                        enctype="multipart/form-data">
                        @csrf
                        <!--begin::Main column-->
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                            <div class="d-flex flex-column gap-7 gap-lg-10">
                                <!--begin::General options-->
                                <div class="card card-flush py-4">
                                    <!--begin::Card header-->
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Product</h2>
                                        </div>
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <!--begin::Input group-->
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required form-label">Product Name</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="text" name="product_name"
                                                        class="form-control mb-2 @error('product_name') is-invalid @enderror"
                                                        placeholder="Product name" value="{{ old('product_name') }}" />
                                                    <!--end::Input-->
                                                    <div class="invalid-feedback fs-7 error error_product_name"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required form-label">Tags</label>
                                                    <input name="product_tags"
                                                        class="form-control mb-2 @error('product_tags') is-invalid @enderror"
                                                        placeholder="fashion,stylesh" id="tags"
                                                        value="{{ old('product_tags') }}" />
                                                    <!--end::Label-->
                                                    <div class="invalid-feedback fs-7 error error_product_tags"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-10 fv-row">
                                                    <label class="required form-label">Categories</label>
                                                    <select
                                                        class="form-select mb-2 @error('categories') is-invalid @enderror"
                                                        data-control="select2" name="categories" id="category_select"
                                                        data-placeholder="Select an option">
                                                        <option></option>
                                                        @foreach ($category_list as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('categories') == $category->id ? 'selected' : '' }}>
                                                                {{ Str::upper($category->category_name) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback fs-7 error error_categories"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-10 fv-row">
                                                    <label class="required form-label">Sub Categories</label>
                                                    <select
                                                        class="form-select mb-2 @error('sub_category') is-invalid @enderror"
                                                        data-control="select2" name="sub_category" id="sub_category_select"
                                                        data-placeholder="Select a sub category">
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                    <div class="invalid-feedback fs-7 error error_sub_category"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row product-variation-section" style="display: none;">
                                            <div class="col-md-8">
                                                <div class="mb-10 fv-row" id="add_variation_input">

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required form-label">Price</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="number" name="new_price"
                                                        class="form-control mb-2 @error('new_price') is-invalid @enderror"
                                                        placeholder="New Price" value="{{ old('new_price') }}" />
                                                    <!--end::Input-->
                                                    <div class="invalid-feedback fs-7 error error_new_price"></div>
                                                    <!--begin::Description-->
                                                    <!--end::Description-->
                                                </div>
                                            </div>

                                            <div>
                                                <!--begin::Label-->
                                                <label class="required form-label">Product Description</label>
                                                <textarea name="product_description" id="" cols="30" rows="3"
                                                    class="form-control @error('product_description') is-invalid @enderror">{{ old('product_description') }}</textarea>
                                                <div class="invalid-feedback fs-7 error error_product_description"></div>
                                                <!--end::Description-->
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Card header-->
                                    </div>
                                    <!--end::General options-->
                                </div>

                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <!--begin::Inventory-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Media</h2>
                                            </div>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="row">
                                                <div class="col-md-6">

                                                    <div class="mb-3">
                                                        <label class="required form-label">Images (Max: 3)</label>
                                                        <input type="file"
                                                            class="form-control @error('images') is-invalid @enderror"
                                                            id="image" name="images[]" multiple accept="image/*"
                                                            onchange="previewImages(event)">
                                                        <div class="invalid-feedback error error_images"></div>
                                                        <small class="text-muted">You can upload up to 3 images.</small>
                                                    </div>

                                                    <!-- Preview container -->
                                                    <div id="image-preview-container" class="d-flex gap-2 mt-2">
                                                        <!-- Thumbnails will be inserted here -->
                                                    </div>

                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-10 fv-row">
                                                        <label class="form-label">Video</label>
                                                        <input type="file" name="video" id="videoInput"
                                                            class="form-control mb-2 @error('video') is-invalid @enderror"
                                                            accept="video/*" />
                                                        <div class="invalid-feedback fs-7 error error_video"></div>

                                                        <div id="videoSize" class="text-muted mt-2"></div>
                                                        <video id="videoPreview" width="100%" height="auto"
                                                            class="mt-2" controls style="display: none;"></video>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                        <!--end::Card header-->
                                    </div>
                                    <!--end::Inventory-->
                                </div>

                                <div class="d-flex flex-column gap-7 gap-lg-10">
                                    <!--begin::Inventory-->
                                    <div class="card card-flush py-4">
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h2>Inventory</h2>
                                            </div>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="required form-label">SKU</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input type="text" name="sku"
                                                            class="form-control mb-2 @error('sku') is-invalid @enderror"
                                                            placeholder="SKU Number" value="{{ old('sku') }}" />
                                                        <!--end::Input-->
                                                        <div class="invalid-feedback fs-7 error error_sku"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="required form-label">Quantity</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input type="number" name="quantity"
                                                            class="form-control mb-2 @error('quantity') is-invalid @enderror"
                                                            placeholder="how many product have"
                                                            value="{{ old('quantity') }}" />
                                                        <div class="invalid-feedback fs-7 error error_quantity"></div>
                                                        <!--end::Input-->
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <!--end::Card header-->
                                    </div>
                                    <!--end::Inventory-->
                                </div>

                                <div class="d-flex justify-content-start m-3">
                                    <!--begin::Button-->
                                    <!-- <a href="#" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5">Cancel</a> -->
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                        <span class="indicator-label">Add Product</span>
                                        <span class="indicator-progress">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                    <!--end::Button-->
                                </div>
                            </div>
                            <!--end::Main column-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        @include('layouts.footer')
    </div>
    <!--end:::Main-->
@endsection

@section('script')
    <script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

    <script>
        $(document).ready(function() {
            $(document).on('change', '#category_select', function() {
                let categoryId = $(this).val();

                if (categoryId) {
                    $.ajax({
                        url: "{{ route('retailer.getSubCategories') }}", // Create this route
                        type: "GET",
                        data: {
                            category_id: categoryId
                        },
                        success: function(data) {
                            $('#sub_category_select').empty().append(
                                '<option value="">Select Sub Category</option>');
                            $.each(data, function(key, value) {
                                $('#sub_category_select').append('<option value="' +
                                    value.id + '">' + value.sub_category_name +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('#sub_category_select').empty().append(
                        '<option value="">Select Sub Category</option>');
                }
            });


            $(document).on('change', '#sub_category_select', function() {
                let subCategoryId = $(this).val();

                if (subCategoryId) {
                    $.ajax({
                        url: '{{ route('retailer.products.get-sub-category-variations') }}',
                        type: 'GET',
                        data: {
                            sub_category_id: subCategoryId
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('.product-variation-section').hide();
                            if (response.status) {
                                let variations = response.sub_category_variation;
                                let inputHtml =
                                    '<label class="required form-label">Product Variations</label>';

                                if (variations) {
                                    let variationArray = variations.split(',');

                                    variationArray.forEach(function(variation, index) {
                                        let trimmedVariation = variation.trim();
                                        inputHtml += `<div class="row mb-3 align-items-center gap-3">
                                            <div class="col"><input type="text" name="variation[${index}]" value="${trimmedVariation}" readonly class="form-control"></div>
                                            <div class="col"><input type="text" name="variation_price[${index}]" class="form-control" placeholder="Enter price" required /></div>
                                        </div>`;
                                    });

                                    $('.product-variation-section').show();
                                    $('#add_variation_input').html(inputHtml);
                                } else {
                                    $('#add_variation_input').html(
                                        '<p class="text-muted">No product variations available.</p>'
                                    );
                                    $('.product-variation-section').show();
                                }

                            } else {
                                if (response.msg == 'Not found') {
                                    $('#add_variation_input').html(
                                        '<p class="text-muted">No product variations available.</p>'
                                    );
                                    $('.product-variation-section').show();
                                }
                                $('.product-variation-section').hide();
                                console.error('AJAX Error:', response.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            $('.product-variation-section').hide();
                            console.error('AJAX Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong! Please try again.'
                            });
                        }
                    });
                } else {
                    $('#add_variation_input').html('');
                }
            });
        });
    </script>

    <script>
        var input = document.querySelector('#tags');
        new Tagify(input, {
            delimiters: " ", // space thi tag split thase
            // comma pan joye to use: delimiters: ", "
        });

        function validateForm(event) {
            const imageInput = document.getElementById('image');
            const imageError = document.getElementById('image-error');

            if (imageInput.files.length === 0) {
                event.preventDefault(); // Stop form submission
                imageInput.classList.add('is-invalid');
                imageError.innerText = 'At least one image is required.';
                return false;
            } else {
                imageInput.classList.remove('is-invalid');
                imageError.innerText = '';
                return true;
            }
        }
        let isImageSelected = false;

        function previewImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('image-preview-container');
            const errorContainer = document.getElementById('image-error');

            previewContainer.innerHTML = ''; // Clear previous previews
            errorContainer.innerText = ''; // Clear previous errors

            if (files.length === 0) {
                isImageSelected = false;
                errorContainer.innerText = 'At least one image is required.';
                return;
            }

            if (files.length > 3) {
                errorContainer.innerText = 'You can upload a maximum of 3 images.';
                event.target.value = '';
                isImageSelected = false;
                return;
            }

            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            const maxSizeInBytes = 5 * 1024 * 1024;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (!allowedTypes.includes(file.type)) {
                    errorContainer.innerText = 'Only PNG, JPG, and JPEG images are allowed.';
                    event.target.value = '';
                    previewContainer.innerHTML = '';
                    isImageSelected = false;
                    return;
                }

                if (file.size > maxSizeInBytes) {
                    errorContainer.innerText = `Image ${file.name} is larger than 5MB.`;
                    event.target.value = '';
                    previewContainer.innerHTML = '';
                    isImageSelected = false;
                    return;
                }
            }

            isImageSelected = true;

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    img.style.border = '1px solid #ccc';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        $(document).ready(function() {
            $('#kt_ecommerce_add_product_form').on('submit', function(e) {
                e.preventDefault();
                // $('.invalid-feedback').remove();
                $('.error').hide();
                $('.is-invalid').removeClass('is-invalid');

                let form = this;
                let formData = new FormData(form);

                // Store category before reset (optional)
                let selectedCategory = $('[name="category_id"]').val();

                $.ajax({
                    type: 'POST',
                    url: $(form).attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#kt_ecommerce_add_product_form')
                            .find('input, select, textarea, button')
                            .prop('disabled', true);
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Product added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        const form = $('#kt_ecommerce_add_product_form')[0];
                        form.reset();

                        // Reset Category & Subcategory completely
                        $('#category_select').val('').trigger('change');
                        $('#sub_category_select').empty().append(
                            '<option value="">Select Sub Category</option>');

                        //  Reset Select2 styles
                        $('#category_select, #sub_category_select').next('.select2')
                            .find('.select2-selection').removeClass('border border-danger');

                        //  Remove Tagify error border and clear tags
                        $('.tagify').removeClass('border border-danger');
                        if (window.tagify) {
                            window.tagify.removeAllTags();
                        }

                        // Clear image input and preview
                        $('#image').val('');
                        $('#image').removeClass('is-invalid');
                        $('#image-preview-container').empty();
                        $('#image-error').text('');

                        // Clear video
                        $('#videoPreview').attr('src', '').hide();
                        $('#videoSize').text('');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                const baseKey = key.split('.')[0];
                                const input = $('[name="' + key + '"]');

                                // Mark regular input
                                input.addClass('is-invalid');

                                // Special: Tagify field
                                if (baseKey === "product_tags") {
                                    $('.tagify').addClass('border border-danger');
                                }

                                // Special: Select2 dropdowns
                                if (baseKey === "categories" || baseKey ===
                                    "sub_category") {
                                    input.next('.select2').find('.select2-selection')
                                        .addClass('border border-danger');
                                }

                                // Special: Image field
                                if (baseKey.startsWith('images')) {
                                    $('#image').addClass('is-invalid');
                                    $('#image-error').text(value[0]).show();
                                }

                                // Display message
                                const errorElement = $('.error_' + baseKey);
                                if (errorElement.length > 0) {
                                    errorElement.text(value[0]).show();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Something went wrong!',
                                text: 'Please try again later.'
                            });
                        }
                    },

                    complete: function() {
                        $('#kt_ecommerce_add_product_form')
                            .find('input, select, textarea, button')
                            .prop('disabled', false);
                    }

                });
            });
        });

        document.getElementById('videoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const sizeLimit = 6 * 1024 * 1024; // 6MB

            const videoSizeDiv = document.getElementById('videoSize');
            const videoPreview = document.getElementById('videoPreview');

            if (file) {
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                videoSizeDiv.innerHTML = `Video size: ${sizeInMB} MB`;

                if (file.size > sizeLimit) {
                    videoSizeDiv.innerHTML += `<br><span class="text-danger">Video must be less than 6MB!</span>`;
                    this.value = ''; // Clear file input
                    videoPreview.style.display = 'none';
                    videoPreview.src = '';
                } else {
                    // Preview
                    const videoUrl = URL.createObjectURL(file);
                    videoPreview.src = videoUrl;
                    videoPreview.style.display = 'block';
                }
            } else {
                videoSizeDiv.innerHTML = '';
                videoPreview.style.display = 'none';
            }
        });
    </script>
@endsection
