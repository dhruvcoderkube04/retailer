@extends('layouts.base')

@section('title')
    Product Details | TrendMart
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            {{-- Heading --}}
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Product Details</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}"
                                    class="text-muted text-hover-primary">Product</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Product Details</li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="{{ route('retailer.product') }}" class="btn btn-primary">
                            Back to Product List
                        </a>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    {{-- Success/Error Messages --}}
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

                    {{-- General Information --}}
                    <div class="card card-flush border border-secondary mb-7">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>General Information</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">Product Name</label>
                                    <p class="form-control-static">{{ $product_detail->name }}</p>
                                </div>
                                <div class="col-md-6 mb-6">
                                    <label classukka-label fw-bold">Slug</label>
                                    <p class="form-control-static">{{ $product_detail->slug }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">Sub Category</label>
                                    <p class="form-control-static">
                                        {{ $sub_category_list->firstWhere('id', $product_detail->sub_category_id)->sub_category_name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">Tags</label>
                                    <p class="form-control-static">{{ $product_detail->tags ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">Status</label>
                                    <p class="form-control-static">
                                        {{ Str::ucfirst($product_detail->status) }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">Old Price</label>
                                    <p class="form-control-static">{{ $product_detail->old_price ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">New Price</label>
                                    <p class="form-control-static">{{ $product_detail->new_price ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="mb-6">
                                <label class="form-label fw-bold">Description</label>
                                <p class="form-control-static">{!! nl2br(e($product_detail->description)) ?? 'N/A' !!}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Product Variations --}}
                    @if ($product_detail->productVariations && count($product_detail->productVariations) > 0)
                        <div class="card card-flush border border-secondary mb-7">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Product Variations</h2>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                @foreach ($product_detail->productVariations as $variation)
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Variation</label>
                                            <p class="form-control-static">{{ $variation->product_variation ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Price</label>
                                            <p class="form-control-static">{{ $variation->price ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Stock</label>
                                            <p class="form-control-static">{{ $variation->stock ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Media --}}
                    <div class="card card-flush border border-secondary mb-7">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Media</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                {{-- Images --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Images</label>
                                    @php
                                        $images = explode(',', $product_detail->images);
                                    @endphp
                                    @if (!empty($images) && !empty(array_filter($images)))
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach ($images as $key => $image)
                                                @if ($image)
                                                    <div class="card shadow-sm border border-dark-subtle"
                                                         style="width: 11rem;">
                                                        <div class="card-body p-2 text-center">
                                                            @php
                                                                $imageUrl = $image
                                                                    ? Storage::disk('spaces')->url($image)
                                                                    : asset('assets/media/images/no_image.jpg');
                                                                $defaultImage = asset('assets/media/images/no_image.jpg');
                                                            @endphp
                                                            <img src="{{ $imageUrl }}"
                                                                 class="img-fluid rounded"
                                                                 alt="Product Image"
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
                                    @else
                                        <p class="text-muted fs-7">No images available.</p>
                                    @endif
                                </div>

                                {{-- Video --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Video</label>
                                    @if ($product_detail->videos)
                                        @php
                                            $videoPath = Storage::disk('spaces')->url($product_detail->videos);
                                        @endphp
                                        <div class="card shadow-sm border border-dark-subtle"
                                             style="width: 100%; max-width: 300px;">
                                            <div class="card-body p-2 text-center">
                                                <video width="100%" height="150" controls style="object-fit: cover;">
                                                    <source src="{{ $videoPath }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="text-muted fs-8 mt-2">
                                                    Product Info Video
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted fs-7">No video available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Inventory --}}
                    <div class="card card-flush border border-secondary mb-7">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Inventory</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">SKU</label>
                                    <p class="form-control-static">{{ $product_detail->sku ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">Quantity</label>
                                    <p class="form-control-static">{{ $product_detail->quantity ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Meta Options --}}
                    <div class="card card-flush border border-secondary mb-7">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Meta Options</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">Meta Tag Title</label>
                                    <p class="form-control-static">{{ $product_detail->meta_title ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-6">
                                    <label class="form-label fw-bold">Meta Tag Keywords</label>
                                    <p class="form-control-static">{{ $product_detail->meta_keywords ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="mb-6">
                                <label class="form-label fw-bold">Meta Tag Description</label>
                                <p class="form-control-static">{!! nl2br(e($product_detail->meta_description)) ?? 'N/A' !!}</p>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="d-flex justify-content-center">
                        <a href="{{ route('retailer.product') }}" class="btn btn-danger">
                            Back to Product List
                        </a>
                    </div> --}}
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection

@section('script')
    <script>
        // No JavaScript is needed for a read-only details page.
        // If additional interactivity is required (e.g., lightbox for images), it can be added here.
    </script>
@endsection
