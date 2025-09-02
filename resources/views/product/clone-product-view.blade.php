@extends('layouts.base')
@section('title')
    Clone Product | TechtrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 flex-column justify-content-center my-0">
                            Clone Product
                        </h1>
                        <!-- <h3 class="page-heading d-flex text-gray-900 fw-bold fs-7 mt-2 flex-column justify-content-center my-0"></h3> -->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-1 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Clone Product</li>
                        </ul>
                    </div>
                </div>
            </div>

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

                    <form id="kt_ecommerce_add_product_form" class="form"
                        action="{{ route('retailer.clone-product-store', encryptId($product->id)) }}" method="post">
                        @csrf
                        <div class="card card-flush py-4">
                            <div class="card-body">
                                <div class="row col-md-6">
                                    <div class="mb-5">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" id="name" class="form-control"
                                            value="{{ $product->name }}" disabled>
                                    </div>

                                    <div class="mb-5">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if ($product->productVariations->isNotEmpty())
                                        <label class="form-label mt-2">Product Variations :</label>
                                        <div class="table-responsive mb-6">
                                            <table class="table table-bordered align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Variation</th>
                                                        <th>Old Price</th>
                                                        <th>New Price</th>
                                                        <th>Quantity / Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($product->productVariations as $variation)
                                                        <tr>
                                                            <td>{{ $variation->product_variation }}</td>
                                                            <td>{{ $variation->old_price }}</td>
                                                            <td>{{ $variation->price }}</td>
                                                            <td>{{ $variation->stock }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <input type="hidden" name="product_variations" value="1">
                                    @else
                                        <div class="mb-5">
                                            <label for="old_price" class="form-label required">Old Price</label>
                                            <input type="number" id="old_price" name="old_price"
                                                class="form-control @error('old_price') is-invalid @enderror"
                                                value="{{ old('old_price', $product->old_price) }}">
                                            @error('old_price')
                                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-8">
                                            <label for="new_price" class="form-label required">New Price</label>
                                            <input type="number" id="new_price" name="new_price"
                                                class="form-control @error('new_price') is-invalid @enderror"
                                                value="{{ old('new_price', $product->new_price) }}">
                                            @error('new_price')
                                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="ms-3 mb-4 form-check">
                                        <input class="form-check-input" type="checkbox" id="images" name="images"
                                            value="1" {{ old('images') ? 'checked' : '' }}>
                                        <label class="form-check-label fs-6" for="images">Include Images</label>
                                    </div>

                                    <div class="ms-3 mb-4 form-check">
                                        <input class="form-check-input" type="checkbox" id="video" name="video"
                                            value="1" {{ old('video') ? 'checked' : '' }}>
                                        <label class="form-check-label fs-6" for="video">Include Video</label>
                                    </div>

                                    <div class="ms-3 mb-4 form-check">
                                        <input class="form-check-input" type="checkbox" id="category" value="1"
                                            checked disabled>
                                        <label class="form-check-label fs-6" for="category">Category</label>
                                    </div>

                                    <div class="ms-3 mb-4 form-check">
                                        <input class="form-check-input" type="checkbox" id="other_details" value="1"
                                            checked disabled>
                                        <label class="form-check-label fs-6" for="other_details">Other Details</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                    <span class="indicator-label">Clone</span>
                                </button>
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
@endsection
