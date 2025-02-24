@extends('retailers.layouts.base')
@section('title')
Retailer's Added Product List
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
               <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Retailer's Added Products List</h1>
               <!--end::Title-->
               <!--begin::Breadcrumb-->
               <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
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
                  <li class="breadcrumb-item text-muted">Product list</li>
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
            <!--begin::Products-->
            <div class="card card-flush">
               <!--begin::Card header-->
               <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                  <!--begin::Card title-->
                  <div class="card-title">
                     <!--begin::Search-->
                     <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                           <span class="path1"></span>
                           <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-ecommerce-product-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Product" />
                     </div>
                     <!--end::Search-->
                  </div>
                  <!--end::Card title-->
                  <!--begin::Card toolbar-->
                  <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                     <div class="w-100 mw-150px">
                        <!--begin::Select2-->
                        <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-product-filter="status">
                           <option></option>
                           <option value="all">All</option>
                           <option value="published">Published</option>
                           <option value="scheduled">Scheduled</option>
                           <option value="inactive">Inactive</option>
                        </select>
                        <!--end::Select2-->
                     </div>
                     <!--begin::Add product-->
                     {{-- <a href="apps/ecommerce/catalog/add-product.html" class="btn btn-primary">Add Product</a> --}}
                     <!--end::Add product-->
                  </div>
                  <!--end::Card toolbar-->
               </div>
               <!--end::Card header-->
               <!--begin::Card body-->
               <div class="card-body pt-0">
                  <!--begin::Table-->
                  <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                     <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                           <th class="text-center min-w-70px">Actions</th>
                           <th class="w-10px pe-2">
                              <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                 <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_ecommerce_products_table .form-check-input" value="1" />
                              </div>
                           </th>
                           <th class="text-center min-w-200px">Product</th>
                           <th class="text-center min-w-150px">Wholesaler</th>
                           <th class="text-center min-w-150px">SKU</th>
                           <th class="text-center min-w-70px">Qty</th>
                           <th class="text-center min-w-100px">Price</th>
                           <th class="text-center min-w-100px">Margin</th>
                           <th class="text-center min-w-100px">Status</th>
                        </tr>
                     </thead>
                     <tbody class="fw-semibold text-gray-600">
                        @foreach ($retailerProducts as $product)
                        <tr>
                           <td class="text-center">
                              <a href="{{ route('retailer.add-product-view', $product->product->id) }}" class="btn btn-danger btn-sm" style="white-space: nowrap;">Edit/Remove</a>
                           </td>

                           <td>
                              <div class="form-check form-check-sm form-check-custom form-check-solid">
                                 <input class="form-check-input" type="checkbox" value="1" />
                              </div>
                           </td>
                           <td>
                              <div class="d-flex align-items-center">
                                 <!--begin::Thumbnail-->
                                 <a href="#" class="symbol symbol-50px">
                                    <span class="symbol-label" style="background-image:url(assets/media//stock/ecommerce/1.png);"></span>
                                 </a>
                                 <!--end::Thumbnail-->
                                 <div class="ms-5">
                                    <!--begin::Title-->
                                    <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">{{$product->product->name}}</a>
                                    <!--end::Title-->
                                 </div>
                              </div>
                           </td>
                           <td class="text-center">
                              <div class="ms-5">
                                 <a href="{{route('retailer.wholesalerwise.productlist',$product->wholesaler->id)}}" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">{{$product->wholesaler->userDetail->company_name}}</a>
                              </div>
                           </td>
                           <td class="text-center pe-0" data-order="22">
                              <span class="fw-bold">{{$product->product->sku}}</span>
                           </td>
                           <td class="text-center pe-0" data-order="22">
                              <span class="fw-bold">{{$product->product->quantity}}</span>
                           </td>
                           <td class="text-center pe-0">34</td>
                           <td class="text-center pe-0" data-order="rating-4">
                              <div class="badge badge-light-success">{{$product->margin}}%</div>
                           </td>
                           <td class="text-center" data-order="Inactive">
                              <!--begin::Badges-->
                              <div class="badge badge-light-danger">{{$product->product->status}}</div>
                              <!--end::Badges-->
                           </td>

                        </tr>
                        @endforeach
                     </tbody>
                  </table>
                  <!--end::Table-->
               </div>
               <!--end::Card body-->
            </div>
            <!--end::Products-->
         </div>
         <!--end::Content container-->
      </div>
      <!--end::Content-->
   </div>
   <!--end::Content wrapper-->
   <!--begin::Footer-->
   @include('retailers.layouts.footer')
   <!--end::Footer-->
</div>
<!--end:::Main-->
@endsection

@section('script')
<script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/products.js')}}"></script>
<script src="{{ asset('assets/js/widgets.bundle.js')}}"></script>
<script src="{{ asset('assets/js/custom/widgets.js')}}"></script>
<script src="{{ asset('assets/js/custom/apps/chat/chat.js')}}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js')}}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/create-app.js')}}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/users-search.js')}}"></script>
@endsection