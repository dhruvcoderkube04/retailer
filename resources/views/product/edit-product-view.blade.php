@extends('layouts.base')
@section('title')
    Edit Product | TrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">

            {{-- heading --}}
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container w-100 d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Edit Product</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}"
                                    class="text-muted text-hover-primary">Product</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Edit Product</li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('retailer.my.product') }}" class="btn btn-primary">
                            View Product List
                        </a>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container ">
                    {{-- success/error message --}}
                    @if (session('success'))
                        <div class="alert alert-success text-green-600 p-2">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger text-red-600 p-2">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger text-red-600 p-2">
                            <ul class="mb-0 pl-4 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- <------------------- START : form ---------------> --}}
                    <form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row"
                        action="{{ route('retailer.update.products', encryptId($product_detail->id)) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex flex-column flex-row-fluid gap-2 gap-lg-5">
                            <div class="d-flex flex-column gap-4 gap-lg-7">
                                <div class="card card-flush border border-secondary">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>General</h2>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            {{-- product_name --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Product Name</label>
                                                    <input type="text" name="product_name"
                                                        class="form-control mb-2 @error('product_name') is-invalid @enderror"
                                                        placeholder="Product Name"
                                                        value="{{ old('product_name', $product_detail->name) }}" />
                                                    @error('product_name')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- slug --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Slug Name</label>
                                                    <input type="text" name="slug"
                                                        class="form-control mb-2 @error('slug') is-invalid @enderror"
                                                        placeholder="Auto generated as per product name"
                                                        value="{{ old('slug', $product_detail->slug) }}" disabled />
                                                    @error('slug')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- categories --}}
                                            {{-- <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Categories</label>
                                                    <select
                                                        class="form-select mb-2 @error('categories') is-invalid @enderror"
                                                        data-control="select2" name="categories"
                                                        data-placeholder="Select an option" id="categorySelect">
                                                        <option></option>
                                                        @foreach ($category_list as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('categories', $product_detail->category_id) == $category->id ? 'selected' : '' }}>
                                                                {{ Str::upper($category->category_name) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('categories')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> --}}

                                            {{-- sub_category_id --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Sub Category</label>
                                                    <select
                                                        class="form-select mb-2 @error('sub_category_id') is-invalid @enderror"
                                                        data-control="select2" name="sub_category_id"
                                                        data-placeholder="Select an option" id="subCategory">
                                                        <option></option>
                                                        @foreach ($sub_category_list as $sub_category)
                                                            <option data-category-id="{{ $sub_category->category_id }}"
                                                                value="{{ $sub_category->id }}"
                                                                {{ old('sub_category_id', $product_detail->sub_category_id) == $sub_category->id ? 'selected' : '' }}>
                                                                {{ Str::upper($sub_category->sub_category_name) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('sub_category_id')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- product_variation --}}
                                            <div class="row product-variation-section" style="display: none;">
                                                <div class="col-lg-11 col-md-12">
                                                    <div class="mb-10 fv-row" id="add_variation_input">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- product_tags --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="form-label">Tags</label>
                                                    <input name="product_tags"
                                                        class="form-control mb-2 @error('product_tags') is-invalid @enderror"
                                                        id="product_tags"
                                                        value="{{ old('product_tags', $product_detail->tags) }}"
                                                        placeholder="Fashion, Style, Electric" />
                                                    @error('product_tags')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- status --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Status</label>
                                                    <select class="form-select mb-2 @error('status') is-invalid @enderror"
                                                        data-control="select2" name="status" data-hide-search="true"
                                                        data-placeholder="Select an option"
                                                        id="kt_ecommerce_add_product_status_select">
                                                        <option value="" disabled>Select an option</option>
                                                        <option value="active"
                                                            {{ $product_detail->status == 'active' ? 'selected' : '' }}>
                                                            Published</option>
                                                        <option value="inactive"
                                                            {{ $product_detail->status == 'inactive' ? 'selected' : '' }}>
                                                            Draft</option>
                                                    </select>

                                                    @error('status')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- old_price --}}
                                            <div class="col-md-6" id="old_price_section">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Old Price</label>
                                                    <input type="number" name="old_price"
                                                        class="form-control mb-2 @error('old_price') is-invalid @enderror"
                                                        placeholder="Old Price"
                                                        value="{{ old('old_price', $product_detail->old_price) }}" />
                                                    @error('old_price')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- new_price --}}
                                            <div class="col-md-6" id="new_price_section">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">New Price</label>
                                                    <input type="number" name="new_price"
                                                        class="form-control mb-2 @error('new_price') is-invalid @enderror"
                                                        placeholder="New Price"
                                                        value="{{ old('new_price', $product_detail->new_price) }}" />
                                                    @error('new_price')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        {{-- product_description --}}
                                        <div class="fv-row">
                                            <label class="form-label">Description</label>
                                            <textarea name="product_description" cols="30" rows="3"
                                                class="form-control @error('product_description') is-invalid @enderror" placeholder="Product Description">{{ old('product_description', $product_detail->description) }}</textarea>
                                            @error('product_description')
                                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-4 gap-lg-7">
                                <div class="card card-flush border border-secondary">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Media</h2>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            {{-- images --}}
                                            <div class="col-md-6">
                                                <div class="mb-3 fv-row">
                                                    <label class="form-label">Images (Max: 3)</label>
                                                    <input type="file" class="form-control" id="image"
                                                        name="images[]" multiple accept="image/*"
                                                        onchange="previewImages(event)">
                                                    <small class="text-muted">You can upload up to 3 images.</small>
                                                    @error('images')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div id="image-preview-container" class="d-flex gap-2 mt-2">
                                                    <!-- image preview will be append here -->
                                                </div>
                                            </div>

                                            {{-- video --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="form-label">Video </label>
                                                    <input type="file" name="video"
                                                        class="form-control mb-2 @error('video') is-invalid @enderror"
                                                        placeholder="video" value="{{ old('video') }}" id="videoInput"
                                                        accept="video/*" />
                                                    <small class="text-muted">You can upload 1 video within 10 MB.</small>
                                                    @error('video')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror

                                                    <div>
                                                        <small id="videoSize" class="text-muted mt-2"></small>
                                                        <div style="width: 100%; max-width: 300px;">
                                                            <video id="videoPreview" width="100%" height="150"
                                                                height="auto" class="mt-2" controls autoplay muted
                                                                style="display: none; object-fit: cover;"></video>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Images & Video Preview --}}
                                    <div class="card-body pt-0">
                                        <div class="row g-6">
                                            {{-- Uploaded Images --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    @php
                                                        $images = explode(',', $product_detail->images);
                                                    @endphp
                                                    <div class="card-title mb-3">
                                                        <h4>Uploaded Images :</h4>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-3">
                                                        @foreach ($images as $key => $image)
                                                            @if ($image)
                                                                <div class="card shadow-sm border border-dark-subtle"
                                                                    style="width: 11rem;">
                                                                    <div class="card-body p-2 text-center">
                                                                        @php
                                                                            $imageUrl = $image
                                                                                ? Storage::disk('spaces')->url($image)
                                                                                : asset(
                                                                                    'assets/media/images/no_image.jpg',
                                                                                );
                                                                            $defaultImage = asset(
                                                                                'assets/media/images/no_image.jpg',
                                                                            );
                                                                        @endphp
                                                                        <img src="{{ $imageUrl }}"
                                                                            class="img-fluid rounded" alt="Product Image"
                                                                            style="height: 100px; object-fit: cover;"
                                                                            onerror="this.onerror=null;this.src='{{ $defaultImage }}';" />
                                                                        <div class="text-muted fs-8 mt-2">
                                                                            Image {{ $key + 1 }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Uploaded Video --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <div class="card-title mb-3">
                                                        <h4>Uploaded Video :</h4>
                                                    </div>
                                                    @if ($product_detail->videos)
                                                        <div class="card shadow-sm border border-dark-subtle"
                                                            style="width: 100%; max-width: 300px;">
                                                            <div class="card-body p-2 text-center">
                                                                @php
                                                                    $videoPath = Storage::disk('spaces')->url(
                                                                        $product_detail->videos,
                                                                    );
                                                                @endphp
                                                                <video width="100%" height="150" controls
                                                                    style="object-fit: cover;" muted autoplay>
                                                                    <source src="{{ $videoPath }}" type="video/mp4">
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                                <div class="text-muted fs-8 mt-2">
                                                                    Product Info Through Short Video
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <p class="text-muted fs-7">No video uploaded.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-4 gap-lg-7">
                                <div class="card card-flush border border-secondary">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Inventory</h2>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            {{-- sku --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="form-label">SKU</label>
                                                    <input type="text" name="sku"
                                                        class="form-control mb-2 @error('sku') is-invalid @enderror"
                                                        placeholder="SKU Number"
                                                        value="{{ old('sku', $product_detail->sku) }}" />
                                                    @error('sku')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- quantity --}}
                                            <div class="col-md-6" id="quantity_section">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Quantity</label>
                                                    <input type="number" name="quantity"
                                                        class="form-control mb-2 @error('quantity') is-invalid @enderror"
                                                        placeholder="How many products have?"
                                                        value="{{ old('quantity', $product_detail->quantity) }}" />
                                                    @error('quantity')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-4 gap-lg-7">
                                <div class="card card-flush border border-secondary">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Meta Options</h2>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            {{-- meta_title --}}
                                            <div class="col-md-6 mb-6 fv-row">
                                                <label class="form-label">Meta Tag Title</label>
                                                <input type="text"
                                                    class="form-control mb-2 @error('meta_title') is-invalid @enderror"
                                                    name="meta_title" placeholder="Meta Tag Title"
                                                    value="{{ old('meta_title', $product_detail->meta_title) }}" />
                                                @error('meta_title')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- product_meta_keywords --}}
                                            <div class="col-md-6 mb-6 fv-row">
                                                <label class="form-label">Meta Tag Keywords</label>
                                                <input id="kt_ecommerce_add_product_meta_keywords"
                                                    name="product_meta_keywords"
                                                    value="{{ old('product_meta_keywords', $product_detail->meta_keywords) }}"
                                                    class="form-control mb-2 @error('product_meta_keywords') is-invalid @enderror"
                                                    placeholder="Meta Tag Keywords" />
                                                @error('product_meta_keywords')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- meta_description --}}
                                        <div class="mb-5 fv-row">
                                            <label class="form-label">Meta Tag Description</label>
                                            <textarea name="meta_description" id="" cols="30" rows="3"
                                                class="form-control @error('meta_description') is-invalid @enderror" placeholder="Meta Tag Description">{{ old('meta_description', $product_detail->meta_description) }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <a href="{{ route('retailer.product') }}" id="kt_ecommerce_add_product_cancel"
                                    class="btn btn-danger me-3">Cancel</a>
                                <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                    <span class="indicator-label">Update Product</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </form>
                    {{-- <------------------- END : form ---------------> --}}
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>

    <script>
        //<----------------- START : Image Preview --------------->
        function previewImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('image-preview-container');
            previewContainer.innerHTML = ''; // Clear previous previews

            if (files.length > 3) {
                alert('You can upload a maximum of 3 images.');
                event.target.value = ''; // Reset file input
                return;
            }

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
        //<----------------- END : Image Preview --------------->

        //<----------------- START : Video Preview --------------->
        document.getElementById('videoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const sizeLimit = 10 * 1024 * 1024; // 10 MB

            const videoSizeDiv = document.getElementById('videoSize');
            const videoPreview = document.getElementById('videoPreview');

            if (file) {
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                videoSizeDiv.innerHTML = `Video size: ${sizeInMB} MB`;

                if (file.size > sizeLimit) {
                    videoSizeDiv.innerHTML += `<br><span class="text-danger">Video must be less than 10MB!</span>`;
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
        //<----------------- END : Video Preview --------------->

        $(document).ready(function() {
            // tagify - product_tags
            var tagsInput = document.querySelector('#product_tags');
            new Tagify(tagsInput, {
                delimiters: " ", // space thi tag split thase
                // comma pan joye to use: delimiters: ", "
            });

            const isEmpty = (val) => !val || $.trim(val) === "";

            const showError = (input, message) => {
                const container = input.closest('.fv-row');
                input.addClass('is-invalid');
                container.find('.invalid-feedback').remove();
                container.append(`<div class="invalid-feedback d-block fs-7">${message}</div>`);

                if (input.attr('name') && input.attr('name').includes('product_tags')) {
                    $('.tagify').addClass('border border-danger');
                }
            };

            const clearError = (input) => {
                input.removeClass('is-invalid');
                input.next('.invalid-feedback').remove();

                if (input.is('select')) {
                    const wrapper = input.next('.select2').find('.select2-selection');
                    wrapper.removeClass('is-invalid');
                    input.next('.select2').next('.invalid-feedback').remove();
                }
                if (input.attr('name') && input.attr('name').includes('images')) {
                    input.closest('.fv-row').find('.invalid-feedback').remove();
                }
                if (input.attr('name') && input.attr('name').includes('product_tags')) {
                    $('.tagify').removeClass('border border-danger');
                }
            };

            const validateField = (input) => {
                const name = input.attr('name');
                const value = input.val();
                const hasVariations = $('[name^="variation["]').filter(function() {
                    return $(this).val().trim() !== "";
                }).length > 0;

                switch (name) {
                    // case 'sku':
                    // case 'slug':
                    case 'status':
                    case 'product_name':
                        // case 'product_description':
                        // case 'product_tags':
                        // case 'meta_title':
                    case 'meta_description':
                        if (isEmpty(value)) {
                            showError(input, `${formatFieldName(name)} field is required`);
                        } else {
                            clearError(input);
                        }
                        break;

                        // case 'categories':
                    case 'sub_category_id':
                        const wrapper = input.next('.select2').find('.select2-selection');
                        if (isEmpty(value)) {
                            showError(input, `${formatFieldName(name)} field is required`);
                            wrapper.addClass('is-invalid');
                        } else {
                            clearError(input);
                            wrapper.removeClass('is-invalid');
                        }
                        break;

                    case 'new_price':
                    case 'old_price':
                    case 'quantity':
                        if (!hasVariations) {
                            if (isEmpty(value) || isNaN(value) || parseFloat(value) < 0) {
                                showError(input, `${formatFieldName(name)} field is required`);
                            } else {
                                clearError(input);
                            }
                        } else {
                            clearError(input); // not required when variations exist
                        }
                        break;

                    case 'images[]':
                        if (input.attr('multiple') && input.attr('type') === 'file') {
                            const files = input[0]?.files;
                            if (files) {
                                if (files.length > 3) {
                                    showError(input, `Only a maximum of 3 images are allowed`);
                                } else {
                                    let hasError = false;
                                    Array.from(files).forEach((file, index) => {
                                        if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file
                                                .type)) {
                                            showError(input, `Image ${index + 1} must be JPEG or PNG`);
                                            hasError = true;
                                        } else if (file.size > 4096 * 1024) {
                                            showError(input,
                                                `Image ${index + 1} must be less than 4MB`);
                                            hasError = true;
                                        }
                                    });
                                    if (!hasError) {
                                        clearError(input);
                                    }
                                }
                            }
                        }
                        break;

                    case 'video':
                        const videoFile = input[0]?.files?.[0];
                        if (videoFile) {
                            if (!['video/mp4'].includes(videoFile.type)) {
                                showError(input, `${formatFieldName(name)} must be in MP4 format`);
                            } else if (videoFile.size > 10 * 1024 * 1024) {
                                showError(input, `${formatFieldName(name)} must be less than 10MB`);
                            } else {
                                clearError(input);
                            }
                        }
                        break;
                }

                toggleSubmitButton();
            };

            function formatFieldName(name) {
                return name.replace(/_/g, ' ').replace(/\[\]/g, '').replace(/\b\w/g, char => char.toUpperCase());
            }

            const validateForm = () => {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                const allFields = [
                    'status', 'product_name', 'sub_category_id', 'new_price', 'old_price', 'quantity'
                ];

                allFields.forEach(name => validateField($(`[name="${name}"]`)));

                // Variation consistency check
                const variation = $('[name="variation[]"]');
                const variation_old_price = $('[name="variation_old_price[]"]');
                const variation_new_price = $('[name="variation_new_price[]"]');
                const variation_quantity = $('[name="variation_quantity[]"]');
                if (
                    variation.length !== variation_old_price.length ||
                    variation.length !== variation_new_price.length ||
                    variation.length !== variation_quantity.length
                ) {
                    showError(variation, "Variation data mismatch");
                }

                return $('.is-invalid').length === 0;
            };

            function toggleSubmitButton() {
                const hasErrors = $('.is-invalid').length > 0;
                const hasVariations = $('[name^="variation["]').filter(function() {
                    return $(this).val().trim() !== "";
                }).length > 0;

                const allRequiredFilled = [
                        'status', 'product_name', 'sub_category_id', 'images[]'
                    ].concat(hasVariations ? [] : ['new_price', 'old_price', 'quantity'])
                    .every(name => {
                        const input = $(`[name="${name}"]`);
                        const value = input.val();
                        if (input.attr('type') === 'file') {
                            return input[0]?.files.length > 0;
                        }
                        return !isEmpty(value);
                    });

                // $('#kt_ecommerce_add_product_submit').prop('disabled', hasErrors || !allRequiredFilled);
            }

            $('#kt_ecommerce_add_product_form').submit(function(e) {
                e.preventDefault();
                if (validateForm()) {
                    this.submit();
                }
            });

            // Live field validation
            const selector =
                '[name="product_tags"], [name="status"], [name="product_name"], [name="product_description"], [name="sub_category_id"], [name="new_price"], [name="old_price"], [name="quantity"]';

            $(document).on('input change', selector, function() {
                validateField($(this));
            });

            // For multiple image file input
            $(document).on('change', '[name="images[]"]', function() {
                validateField($(this));
            });

            let productVariations = @json($product_detail->productVariations);
            let product_detail = @json($product_detail);

            // get sub-categories on change of category
            // $('#categorySelect').on('change', function() {
            //     let categoryId = $(this).val();
            //     var selected_sub_category = false;

            //     if (categoryId) {
            //         $.ajax({
            //             url: '{{ route('retailer.getSubCategories') }}',
            //             type: 'GET',
            //             data: {
            //                 category_id: categoryId
            //             },
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             },
            //             success: function(response) {
            //                 // FOR SUB-CATEGORY
            //                 if (response.subCategories) {
            //                     let options = '<option value="">Select an option</option>';
            //                     $.each(response.subCategories, function(key, data) {
            //                         let selected = data.sub_category.id ==
            //                             product_detail.sub_category_id ? 'selected' :
            //                             '';
            //                         options +=
            //                             `<option value="${data.sub_category.id}" ${selected}>$   {data.sub_category.sub_category_name.toUpperCase()}</option>`;

            //                         if (data.sub_category.id ==
            //                             product_detail.sub_category_id) {
            //                             selected_sub_category = true;
            //                         }
            //                     });

            //                     $('#subCategory').html(options); // Update subCategory dropdown
            //                 }
            //                 if (selected_sub_category) {
            //                     setTimeout(() => {
            //                         $('#subCategory').trigger('change');
            //                     }, 500);
            //                 }
            //             },
            //             error: function(xhr, status, error) {
            //                 console.error('AJAX Error:', error);
            //             }
            //         });
            //     } else {
            //         $('#add_variation_input').html('');
            //     }
            // });
            // $('#categorySelect').trigger('change');

            // change sub-category
            $('#subCategory').on('change', function() {
                let subCategoryId = $(this).val();

                $('#old_price_section').show();
                $('#new_price_section').show();
                $('#quantity_section').show();

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

                                        if (subCategoryId == product_detail
                                            .sub_category_id) {

                                            let old_price = '';
                                            let price = '';
                                            let stock = '';
                                            let matchedVariation = productVariations
                                                .find(item =>
                                                    item.product_variation && item
                                                    .product_variation.trim() ===
                                                    trimmedVariation
                                                );
                                            if (matchedVariation) {
                                                old_price = matchedVariation
                                                    .old_price ?? '';
                                                price = matchedVariation.price ?? '';
                                                stock = matchedVariation.stock ?? '';
                                            }

                                            inputHtml += `<div class="row mb-3">
                                                <div class="col-12 col-md-6 mb-2 mb-md-0">
                                                    <input type="text" name="variation[${index}]" value="${trimmedVariation}" readonly class="form-control">
                                                </div>
                                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                                    <input type="text" name="variation_old_price[${index}]" class="form-control" placeholder="Old price" value="${old_price}" ${index === 0 ? 'required' : ''} />
                                                </div>
                                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                                    <input type="text" name="variation_new_price[${index}]" class="form-control" placeholder="New price" value="${price}" ${index === 0 ? 'required' : ''} />
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <input type="text" name="variation_stock[${index}]" class="form-control" placeholder="Quantity" value="${stock}" ${index === 0 ? 'required' : ''} />
                                                </div>
                                            </div>`;
                                        } else {
                                            inputHtml += `<div class="row mb-3">
                                                <div class="col-12 col-md-6 mb-2 mb-md-0">
                                                    <input type="text" name="variation[${index}]" value="${trimmedVariation}" readonly class="form-control">
                                                </div>
                                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                                    <input type="text" name="variation_old_price[${index}]" class="form-control" placeholder="Old price" ${index === 0 ? 'required' : ''} />
                                                </div>
                                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                                    <input type="text" name="variation_new_price[${index}]" class="form-control" placeholder="New price" ${index === 0 ? 'required' : ''} />
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <input type="text" name="variation_stock[${index}]" class="form-control" placeholder="Quantity" ${index === 0 ? 'required' : ''} />
                                                </div>
                                            </div>`;
                                        }
                                    });

                                    $('.product-variation-section').show();
                                    $('#add_variation_input').html(inputHtml);

                                    $('#old_price_section').hide();
                                    $('#new_price_section').hide();
                                    $('#quantity_section').hide();
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
            $('#subCategory').trigger('change');


            document.getElementById("kt_ecommerce_add_product_form").addEventListener("submit", function() {
                // Enable all disabled inputs before submitting
                document.querySelectorAll("input[disabled]").forEach(input => {
                    input.removeAttribute("disabled");
                });
            });
        });
    </script>
@endsection
