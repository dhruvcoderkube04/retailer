@extends('layouts.base')
@section('title')
    Edit Wholesaler Product | TechtrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">

            {{-- heading --}}
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container w-100 d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Edit Wholesaler Product</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}"
                                    class="text-muted text-hover-primary">Product</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Edit Wholesaler Product</li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('retailer.my.wholesaler.product') }}" class="btn btn-primary">
                            View Wholesaler Product List
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
                        action="{{ route('retailer.my.wholesaler.product.update', encryptId($product_detail->id)) }}"
                        method="post" enctype="multipart/form-data">
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
                                                        value="{{ old('product_name', optional($updated_product_detail)->product_name ?? $product_detail->name) }}" />
                                                    @error('product_name')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- margin --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Margin</label>
                                                    <input type="number" name="margin" step="0.1"
                                                        class="form-control mb-2 @error('margin') is-invalid @enderror"
                                                        placeholder="Margin"
                                                        value="{{ old('margin', optional($updated_product_detail)->margin ?? $margin_detail->margin) }}" />
                                                    @error('margin')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                @php
                                                    $payment_method = explode(
                                                        ',',
                                                        optional($updated_product_detail)->payment_method ??
                                                            $margin_detail->payment_method,
                                                    );
                                                @endphp
                                                <label class="form-label required">Payment Method</label>
                                                <div class="fv-row ">
                                                    <div class="d-flex gap-3 mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="edit_payment_cod" name="payment_method[]" value="COD"
                                                                {{ in_array('COD', $payment_method) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="edit_payment_cod">COD</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="edit_payment_prepaid" name="payment_method[]"
                                                                value="Prepaid"
                                                                {{ in_array('Prepaid', $payment_method) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="edit_payment_prepaid">Prepaid</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="edit_payment_semi" name="payment_method[]"
                                                                value="Semi"
                                                                {{ in_array('Semi', $payment_method) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="edit_payment_semi">Semi</label>
                                                        </div>
                                                    </div>
                                                    @error('payment_method')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- status --}}
                                            <div class="col-md-6">
                                                <div class="mb-6 fv-row">
                                                    <label class="required form-label">Status</label>
                                                    <select class="form-select mb-2 @error('status') is-invalid @enderror"
                                                        data-control="select2" name="status" id="status"
                                                        data-hide-search="true" data-placeholder="Select an option">
                                                        <option value="" disabled>Select an option</option>
                                                        <option value="active"
                                                            {{ (optional($updated_product_detail)->product_status ?? $product_detail->status) == 'active' ? 'selected' : '' }}>
                                                            Published</option>
                                                        <option value="inactive"
                                                            {{ (optional($updated_product_detail)->product_status ?? $product_detail->status) == 'inactive' ? 'selected' : '' }}>
                                                            Draft</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        {{-- product_description --}}
                                        <div class="fv-row">
                                            <label class="form-label">Description</label>
                                            <textarea name="product_description" id="product_description" cols="30" rows="3"
                                                class="form-control @error('product_description') is-invalid @enderror" placeholder="Product Description">{{ old('product_description', optional($updated_product_detail)->product_description ?? $product_detail->description) }}</textarea>
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
                                                        $images = explode(
                                                            ',',
                                                            optional($updated_product_detail)->product_images ??
                                                                $product_detail->images,
                                                        );
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
                                                    @php
                                                        $videoPath = null;
                                                        if (optional($updated_product_detail)->product_videos) {
                                                            $videoPath = Storage::disk('spaces')->url(
                                                                $updated_product_detail->product_videos,
                                                            );
                                                        } elseif ($product_detail->videos) {
                                                            $videoPath = Storage::disk('spaces')->url(
                                                                $product_detail->videos,
                                                            );
                                                        }
                                                    @endphp
                                                    @if ($videoPath)
                                                        <div class="card shadow-sm border border-dark-subtle"
                                                            style="width: 100%; max-width: 300px;">
                                                            <div class="card-body p-2 text-center">
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
                                            <h2>Pricing</h2>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        {{-- price --}}
                                        <div class="row mb-6">
                                            @if ($product_detail->productVariations->isNotEmpty())
                                                <label class="form-label fw-bold">Product Variations :</label>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Variation</th>
                                                                <th>Old Price</th>
                                                                <th>New Price</th>
                                                                <th>Final Price (With Margin)</th>
                                                                <th>Quantity / Stock</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($product_detail->productVariations as $variation)
                                                                <tr>
                                                                    <td>{{ $variation->product_variation }}</td>
                                                                    <td>{{ $variation->old_price }}</td>
                                                                    <td>{{ $variation->price }}</td>
                                                                    <td>{{ $variation->price + ($updated_product_detail->margin ?? $margin_detail->margin) }}
                                                                    </td>
                                                                    <td>{{ $variation->stock }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <label class="form-label fw-bold">Product Prices :</label>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Old Price</th>
                                                                <th>New Price</th>
                                                                <th>Final Price (With Margin)</th>
                                                                <th>Quantity / Stock</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>{{ $product_detail->old_price ?? 'N/A' }}</td>
                                                                <td>{{ $product_detail->new_price ?? 'N/A' }}</td>
                                                                <td>{{ $product_detail->new_price ? $product_detail->new_price + ($updated_product_detail->margin ?? $margin_detail->margin) : 'N/A' }}
                                                                </td>
                                                                <td>{{ $product_detail->quantity ?? 'N/A' }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="hidden" name="sub_category_id"
                                            value="{{ $product_detail->sub_category_id }}">
                                        <input type="hidden" name="existing_images"
                                            value="{{ $product_detail->images }}">
                                        <input type="hidden" name="existing_videos"
                                            value="{{ $product_detail->videos }}">
                                        <input type="hidden" name="wholesaler_id"
                                            value="{{ $product_detail->wholesaler_id }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <a href="{{ route('retailer.my.wholesaler.product') }}"
                                    id="kt_ecommerce_add_product_cancel" class="btn btn-danger me-3">Cancel</a>
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
        let selectedFiles = [];

        function previewImages(event) {
            const files = Array.from(event.target.files);
            const previewContainer = document.getElementById('image-preview-container');

            if (selectedFiles.length + files.length > 3) {
                alert('You can upload a maximum of 3 images.');
                updateFileInput();
                return; 
            }

            files.forEach(file => {
                selectedFiles.push(file); // Add to tracked list

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create wrapper div
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('image-wrapper');
                    wrapper.style.position = 'relative';
                    wrapper.style.display = 'inline-block';
                    wrapper.style.marginRight = '8px';
                    wrapper.style.marginBottom = '8px';

                    // Create image
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    img.style.border = '1px solid #ccc';

                    // Create remove (X) button
                    const removeBtn = document.createElement('span');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.style.position = 'absolute';
                    removeBtn.style.top = '-5px';
                    removeBtn.style.right = '-5px';
                    removeBtn.style.background = '#ff0000';
                    removeBtn.style.color = '#fff';
                    removeBtn.style.borderRadius = '50%';
                    removeBtn.style.width = '20px';
                    removeBtn.style.height = '20px';
                    removeBtn.style.textAlign = 'center';
                    removeBtn.style.lineHeight = '20px';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.style.fontWeight = 'bold';
                    removeBtn.title = 'Remove';

                    // Remove image on click
                    removeBtn.addEventListener('click', function() {
                        previewContainer.removeChild(wrapper);
                        selectedFiles = selectedFiles.filter(f => f !== file); // Remove from list
                        updateFileInput();
                    });

                    // Append image and button to wrapper
                    wrapper.appendChild(img);
                    wrapper.appendChild(removeBtn);
                    previewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });

            // event.target.value = ''; // Reset input so same file can be reselected
            updateFileInput();       // Sync input with selected files
        }

        function updateFileInput() {
            const input = document.getElementById('image'); // ID of your file input
            const dataTransfer = new DataTransfer();

            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });

            input.files = dataTransfer.files;
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
            const isEmpty = (val) => !val || $.trim(val) === "";

            const showError = (input, message) => {
                const container = input.closest('.fv-row');
                input.addClass('is-invalid');
                container.find('.invalid-feedback').remove();
                container.append(`<div class="invalid-feedback d-block fs-7">${message}</div>`);
            };

            const clearError = (input) => {
                input.removeClass('is-invalid');
                input.closest('.fv-row').find('.invalid-feedback').remove();

                if (input.is('select')) {
                    const wrapper = input.next('.select2').find('.select2-selection');
                    wrapper.removeClass('is-invalid');
                }
            };

            const validateField = (input) => {
                const name = input.attr('name');
                const value = input.val();

                if (name === 'payment_method[]') {
                    const isChecked = $('[name="payment_method[]"]:checked').length > 0;
                    if (!isChecked) {
                        showError(input.closest('.fv-row'), 'At least one payment method is required');
                    } else {
                        clearError(input.closest('.fv-row'));
                    }
                }

                switch (name) {
                    case 'product_name':
                    case 'margin':
                    case 'status':
                        if (isEmpty(value)) {
                            showError(input, `${formatFieldName(name)} is required`);
                        } else {
                            clearError(input);
                        }
                        break;

                    case 'images[]':
                        const files = input[0]?.files;
                        if (files && files.length > 0) {
                            if (files.length > 3) {
                                showError(input, `Only a maximum of 3 images are allowed`);
                                return;
                            }

                            let hasError = false;
                            for (let i = 0; i < files.length; i++) {
                                const file = files[i];
                                if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                                    showError(input, `Image ${i + 1} must be JPEG or PNG`);
                                    hasError = true;
                                } else if (file.size > 4 * 1024 * 1024) {
                                    showError(input, `Image ${i + 1} must be less than 4MB`);
                                    hasError = true;
                                }
                            }

                            if (!hasError) {
                                clearError(input);
                            }
                        }
                        break;

                    case 'video':
                        const videoFile = input[0]?.files?.[0];
                        if (videoFile) {
                            if (videoFile.type !== 'video/mp4') {
                                showError(input, `Video must be in MP4 format`);
                            } else if (videoFile.size > 10 * 1024 * 1024) {
                                showError(input, `Video must be less than 10MB`);
                            } else {
                                clearError(input);
                            }
                        }
                        break;
                }
            };

            function formatFieldName(name) {
                return name.replace(/_/g, ' ').replace(/\[\]/g, '').replace(/\b\w/g, char => char.toUpperCase());
            }

            const validateForm = () => {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                const requiredFields = ['product_name', 'margin', 'status', 'images[]'];

                requiredFields.forEach(name => validateField($(`[name="${name}"]`)));

                // Special case: payment_method[] (checkboxes)
                const paymentCheckbox = $('[name="payment_method[]"]').first();
                validateField(paymentCheckbox);

                return $('.is-invalid').length === 0;
            };

            $('#kt_ecommerce_add_product_form').submit(function(e) {
                e.preventDefault();
                if (validateForm()) {
                    this.submit();
                }
            });

            const selector = '[name="status"], [name="product_name"], [name="margin"]';

            $(document).on('input change', selector, function() {
                validateField($(this));
            });

            $(document).on('change', '[name="images[]"]', function() {
                validateField($(this));
            });

            $(document).on('change', '[name="payment_method[]"]', function() {
                validateField($(this));
            });
        });
    </script>
@endsection
