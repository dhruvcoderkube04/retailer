@extends('retailers.layouts.base')
@section('title')
Add Product Details - TrendMart
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
               <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Product Details & Margin</h1>
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
                  <li class="breadcrumb-item text-muted">Product Detail</li>
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
            <form id="kt_ecommerce_add_product_form" class="form d-flex flex-column flex-lg-row" action="{{ route('retailer.add-product',$product->id) }}" method="post">
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
                                       <label class="form-label">Product Name</label>
                                       <!--end::Label-->
                                       <!--begin::Input-->
                                       <input type="text" name="product_name" class="form-control mb-2 @error('product_name') is-invalid @enderror" placeholder="Product name" value="{{$product->name}}" disabled />
                                       <!--end::Input-->
                                    </div>
                                 </div>
                                 <div class="col-md-5">
                                    <div class="mb-10 fv-row">
                                       <!--begin::Label-->
                                       <label class="form-label">Tags</label>
                                       <input name="product_tags" class="form-control mb-2 @error('product_tags') is-invalid @enderror" value="{{$product->tags}}" placeholder="fashion,stylesh" disabled />
                                       <!--end::Label-->
                                    </div>
                                 </div>
                              </div>

                              <div class="row">
                                 <!-- <div class="col-md-4">
                                    <div class="mb-10 fv-row">
                                       <label class="form-label">Categories</label>
                                       <select class="form-select mb-2 @error('categories') is-invalid @enderror" data-control="select2" name="categories" data-placeholder="Select an option">
                                          <option></option>
                                          <option value="Computers">Computers</option>
                                          <option value="Watches">Watches</option>
                                          <option value="Headphones">Headphones</option>
                                          <option value="Footwear">Footwear</option>
                                       </select>
                                    </div>
                              </div> -->
                              </div>

                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="mb-10 fv-row">
                                       <!--begin::Label-->
                                       <label class="form-label">New Price</label>
                                       <!--end::Label-->
                                       <!--begin::Input-->
                                       <input type="number" name="new_price" class="form-control mb-2 @error('new_price') is-invalid @enderror" placeholder="New Price" value="{{$product->new_price}}" disabled />
                                       <!--end::Input-->
                                       <!--begin::Description-->
                                       <!--end::Description-->
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="mb-10 fv-row">
                                       <!--begin::Label-->
                                       <label class="form-label">Old Price</label>
                                       <!--end::Label-->
                                       <!--begin::Input-->
                                       <input type="number" name="old_price" class="form-control mb-2 @error('old_price') is-invalid @enderror" placeholder="Old Price" value="{{$product->old_price}}" disabled />
                                       <!--end::Input-->
                                       <!--begin::Description-->
                                       <!--end::Description-->
                                    </div>
                                 </div>
                              </div>

                              <div>
                                 <!--begin::Label-->
                                 <label class="form-label">Product Description</label>
                                 <!--end::Label-->
                                 <!--begin::Editor-->
                                 <textarea name="product_description" id="" cols="30" rows="3" class="form-control @error('product_description') is-invalid @enderror" disabled>{{$product->description}}</textarea>
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
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                           <!-- begin::image -->
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="mb-10 fv-row">
                                    @php
                                    $images = explode(',', $product->images);
                                    @endphp
                                    <div class="card-header">
                                       <div class="card-title">
                                          <h2>Images</h2>
                                       </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-3">
                                       @foreach ($images as $key => $image)
                                       @if ($image)
                                       <div class="card card-flush py-4" style="width: 20rem;">
                                          <div class="card-body text-center pt-0">
                                             <img src="{{ Storage::url($image) }}"
                                                class="img-fluid"
                                                alt="Product Image"
                                                style="width: 100%; height: 200px; object-fit: cover;" />
                                             <div class="text-muted fs-7 mt-2">
                                                Image {{$key+1}}
                                             </div>
                                          </div>
                                       </div>
                                       @endif
                                       @endforeach
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!-- end::image -->
                           <!-- begin::video -->
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="mb-10 fv-row">
                                    <div class="card-header">
                                       <div class="card-title">
                                          <h2>Video</h2>
                                       </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-3">
                                       @if ($product->videos)
                                       <div class="card card-flush py-4" style="width: 30rem;">
                                          <div class="card-body d-flex flex-column align-items-center text-center pt-0">
                                             <video width="100%" height="200" controls style="object-fit: cover;" muted autoplay>
                                                <source src="{{ Storage::url($product->videos) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                             </video>
                                             <div class="text-muted fs-7 mt-2">
                                                Product Info Through Short Video
                                             </div>
                                          </div>
                                       </div>
                                       @endif
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!-- end::video -->
                        </div>
                        <!--end::Card body-->
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
                                    <label class="form-label">SKU</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="sku" class="form-control mb-2 @error('sku') is-invalid @enderror" placeholder="SKU Number" value="{{$product->sku}}" disabled />
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
                                    <label class="form-label">Quantity</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <div class="d-flex gap-3">
                                       <input type="number" name="quantity" class="form-control mb-2 @error('quantity') is-invalid @enderror" placeholder="how many product have" value="{{$product->quantity}}" disabled />
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
                  </div>

                  <div class="d-flex flex-column gap-7 gap-lg-10">
                     <!--begin::Inventory-->
                     <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                           <div class="card-title">
                              <h2>Margin</h2>
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
                                    <label class="required form-label">Add Your Margin (In Percentage)</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="hidden" name="wholesaler_id" value="{{ $product->wholesaler_id }}" />
                                    <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                    <input type="number" name="margin" class="form-control mb-2 @error('margin') is-invalid @enderror" placeholder="Add Your Margin"
                                       value="{{ $retailer_product ? $retailer_product->margin : '' }}" />
                                    <!--end::Input-->
                                    <!--begin::Description-->
                                    @error('margin')
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

                  <div class="d-flex justify-content-start m-3">
                     <!--begin::Button-->
                     <!-- <a href="#" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5">Cancel</a> -->
                     <!--end::Button-->
                     <!--begin::Button-->
                     @if ($retailer_product)
                     <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                        <span class="indicator-label">Update Margin</span>
                        <span class="indicator-progress">Please wait...
                           <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                     </button>
                     <a href="{{ route('retailer.remove-product', $retailer_product->id) }}" class="ms-3 btn btn-danger"
                     onclick="return confirm('Are you sure you want to remove this product?');">
                        <span class="indicator-label">Remove Product</span>
                     </a>
                     @else
                     <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                        <span class="indicator-label">Add Product</span>
                        <span class="indicator-progress">Please wait...
                           <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                     </button>
                     @endif
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