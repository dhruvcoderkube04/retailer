@extends('layouts.base')
@section('title')
    Product Details | TrendMart
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
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Product Details</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="#" class="text-muted text-hover-primary">Product</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Product detail</li>
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
                <div id="kt_app_content_container" class="app-container ">
                    @if (session('success'))
                        <div class="alert alert-success text-green-600 p-2">
                            {{ session('success') }}
                        </div>
                    @endif
                    <!--begin::Form-->
                    <form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row" action="{{route('wholesale.update.product',$product_detail->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <!--begin::Main column-->
                        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">

                            <div class="d-flex flex-column gap-7 gap-lg-10">
                                <!--begin::General options-->
                                <div class="card card-flush py-4">
                                    <!--begin::Card header-->
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>General</h2>
                                        </div>
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <div class="card-body pt-0">
                                            <!--begin::Input group-->
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="required form-label">Product Name</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input type="text" name="product_name" class="form-control mb-2 @error('product_name') is-invalid @enderror" placeholder="Product name" value="{{$product_detail->name}}" />
                                                        <!--end::Input-->
                                                        <!--begin::Description-->
                                                        @error('product_name')
                                                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                        @enderror
                                                        <!--end::Description-->
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="required form-label">Slug Name</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input type="text" name="slug" class="form-control mb-2 @error('slug') is-invalid @enderror" placeholder="Slug Name" value="{{$product_detail->slug}}" disabled  />
                                                        <!--end::Input-->
                                                        <!--begin::Description-->
                                                        @error('slug')
                                                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                        @enderror
                                                        <!--end::Description-->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Input group-->
                                                        <!--begin::Label-->
                                                        <label class="form-label">Categories</label>
                                                        <!--end::Label-->
                                                        <!--begin::Select2-->
                                                        <select class="form-select mb-2 @error('categories') is-invalid @enderror" data-control="select2" name="categories" data-placeholder="Select an option">
                                                            <option></option>
                                                            <option value="Computers">Computers</option>
                                                            <option value="Watches">Watches</option>
                                                            <option value="Headphones">Headphones</option>
                                                            <option value="Footwear">Footwear</option>
                                                        </select>
                                                        <!--end::Select2-->
                                                        <!--begin::Description-->

                                                        @error('categories')
                                                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                        @enderror
                                                        <!--end::Description-->
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="required form-label">Tags</label>
                                                        <input  name="product_tags" class="form-control mb-2 @error('product_tags') is-invalid @enderror" value="{{$product_detail->tags}}" placeholder="fashion,stylesh"  />
                                                        @error('product_tags')
                                                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                        @enderror
                                                        <!--end::Label-->
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Label-->
                                                          <label class="required form-label">Status</label>
                                                          <!--begin::Input-->
                                                          <select class="form-select mb-2 @error('status') is-invalid @enderror" data-control="select2" name="status" data-hide-search="true" data-placeholder="Select an option" id="kt_ecommerce_add_product_status_select">
                                                            <option value="" disabled>Select an option</option>
                                                            <option value="active" {{ $product_detail->status == 'active' ? 'selected' : '' }}>Published</option>
                                                            <option value="inactive" {{  $product_detail->status == 'inactive' ? 'selected' : '' }}>Draft</option>
                                                        </select>

                                                        @error('status')
                                                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="required form-label">New Price</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input type="number" name="new_price" class="form-control mb-2 @error('new_price') is-invalid @enderror" placeholder="New Price" value="{{$product_detail->new_price}}" />
                                                        <!--end::Input-->
                                                        <!--begin::Description-->
                                                        @error('new_price')
                                                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                        @enderror
                                                        <!--end::Description-->
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-10 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="required form-label">Old Price</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input type="number" name="old_price" class="form-control mb-2 @error('old_price') is-invalid @enderror" placeholder="Old Price" value="{{$product_detail->old_price}}" />
                                                        <!--end::Input-->
                                                        <!--begin::Description-->
                                                        @error('old_price')
                                                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                        @enderror
                                                        <!--end::Description-->
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <!--begin::Label-->
                                                <label class="form-label">Description</label>
                                                <!--end::Label-->
                                                <!--begin::Editor-->
                                                <textarea name="product_description"  id="" cols="30" rows="3" class="form-control @error('product_description') is-invalid @enderror">{{$product_detail->description}}</textarea>
                                                {{-- <div id="kt_ecommerce_add_product_description" class="min-h-150px mb-2">
                                                </div> --}}
                                                <!--end::Editor-->
                                                <!--begin::Description-->
                                                @error('product_description')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                                <!--end::Description-->
                                            </div>

                                            <!--end::Input group-->
                                        </div>
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
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required form-label">Images</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="file" name="image_1" class="form-control mb-2 @error('image_1') is-invalid @enderror" placeholder="Image 1" value="{{old('image_1')}}" />
                                                    <!--end::Input-->
                                                    <!--begin::Description-->
                                                    @error('image_1')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                    <!--end::Description-->
                                                </div>
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="file" name="image_2" class="form-control mb-2 @error('image_2') is-invalid @enderror" placeholder="Image 2" value="{{old('image_2')}}" />
                                                    <!--end::Input-->
                                                    <!--begin::Description-->
                                                    @error('image_2')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                    <!--end::Description-->
                                                </div>
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="file" name="image_3" class="form-control mb-2 @error('image_3') is-invalid @enderror" placeholder="Image 3" value="{{old('image_3')}}" />
                                                    <!--end::Input-->
                                                    <!--begin::Description-->
                                                    @error('image_3')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                    <!--end::Description-->
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required form-label">Video </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="d-flex gap-3">
                                                        <input type="file" name="video" class="form-control mb-2 @error('video') is-invalid @enderror" placeholder="video" value="{{old('video')}}" />
                                                    </div>
                                                    <!--end::Input-->
                                                    <!--begin::Description-->
                                                    @error('video')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                    <!--end::Description-->
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
                                                    <input type="text" name="sku" class="form-control mb-2 @error('sku') is-invalid @enderror" placeholder="SKU Number" value="{{$product_detail->sku}}" disabled />
                                                    <!--end::Input-->
                                                    <!--begin::Description-->
                                                    @error('sku')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                    <!--end::Description-->
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-10 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="required form-label">Quantity</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="d-flex gap-3">
                                                        <input type="number" name="quantity" class="form-control mb-2 @error('quantity') is-invalid @enderror" placeholder="how many product have" value="{{$product_detail->quantity}}" />
                                                    </div>
                                                    <!--end::Input-->
                                                    <!--begin::Description-->
                                                    @error('quantity')
                                                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                    @enderror
                                                    <!--end::Description-->
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!--end::Card header-->
                                </div>
                                <!--end::Inventory-->
                                <!--begin::Meta options-->
                                <div class="card card-flush py-4">
                                    <!--begin::Card header-->
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h2>Meta Options</h2>
                                        </div>
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            <div class="col-md-6 mb-5">
                                                <!--begin::Label-->
                                                <label class="form-label">Meta Tag Title</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control mb-2 @error('meta_title') is-invalid @enderror" name="meta_title" placeholder="Meta title name" value="{{$product_detail->meta_title}}" />
                                                <!--end::Input-->
                                                <!--begin::Description-->
                                                @error('meta_title')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <!--begin::Label-->
                                                <label class="form-label">Meta Tag Keywords</label>
                                                <!--end::Label-->
                                                <!--begin::Editor-->
                                                <input id="kt_ecommerce_add_product_meta_keywords" name="product_meta_keywords" value="{{$product_detail->meta_keywords}}" class="form-control mb-2 @error('product_meta_keywords') is-invalid @enderror" />
                                                <!--end::Editor-->
                                                <!--begin::Description-->
                                                @error('product_meta_keywords')
                                                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-5">
                                            <!--begin::Label-->
                                            <label class="form-label">Meta Tag Description</label>
                                            <!--end::Label-->
                                            <!--begin::Editor-->
                                            <textarea name="meta_description"  id="" cols="30" rows="3" class="form-control @error('meta_description') is-invalid @enderror">{{$product_detail->meta_description}}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                                            @enderror
                                            <!--end::Editor-->
                                        </div>
                                    </div>
                                    <!--end::Card header-->
                                </div>
                                <!--end::Meta options-->
                            </div>
                            <div class="d-flex justify-content-end">
                                <!--begin::Button-->
                                <a href="#" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5">Cancel</a>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                    <span class="indicator-label">Update Product</span>
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
        @include('wholesale.layouts.footer')
    </div>
    <!--end:::Main-->
@endsection

@section('script')
    <script src="{{asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js')}}"></script>
    <script src="{{asset('assets/js/custom/apps/ecommerce/catalog/save-product.js')}}"></script>
    <script src="{{asset('assets/js/custom/utilities/modals/create-app.js')}}"></script>
    <script src="{{asset('assets/js/custom/utilities/modals/users-search.js')}}"></script>
@endsection
