@extends('wholesale.layouts.base')
@section('title')
Wholesaler Orders
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
               <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Wholesaler Orders</h1>
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
                  <li class="breadcrumb-item text-muted">Order List</li>
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
                     <ul class="nav nav-pills">
                        <li class="nav-item">
                           <a class="nav-link active" aria-current="page" href="#">New</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" href="#">Confirmed</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" href="#">Shipped</a>
                        </li>
                     </ul>
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
                           <th class="text-center min-w-200px">Product</th>
                           <th class="text-center min-w-150px">Retailer</th>
                           <th class="text-center min-w-150px">Customer Name</th>
                           <th class="text-center min-w-150px">Customer Contact</th>
                           <th class="text-center min-w-70px">Qty</th>
                           <th class="text-center min-w-100px">Price</th>
                           <th class="text-center min-w-100px">Status</th>
                        </tr>
                     </thead>
                     <tbody class="fw-semibold text-gray-600">
                        @foreach ($wholesalerOrders as $detail)
                        <tr>
                           <td class="text-center">
                              <button type="button" class="btn btn-primary btn-sm orderActionButton"
                                 data-product-id="{{ $detail->product_id }}"
                                 data-order-id="{{ $detail->id }}">
                                 Action
                              </button>
                           </td>
                           <td>
                              <div class="d-flex align-items-center">
                                 <a href="#" class="symbol symbol-50px">
                                    <span class="symbol-label" style="background-image:url(assets/media//stock/ecommerce/1.png);"></span>
                                 </a>
                                 <div class="ms-5">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">{{$detail->product->name}}</a>
                                 </div>
                              </div>
                           </td>
                           <td class="text-center">
                              <div class="ms-5">
                                 <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold" data-kt-ecommerce-product-filter="product_name">{{$detail->retailer->userDetail->company_name}}</a>
                              </div>
                           </td>
                           <td class="text-center pe-0" data-order="22">
                              <span class="fw-bold">{{$detail->customer->firstname}} {{$detail->customer->lastname}}</span>
                           </td>
                           <td class="text-center pe-0" data-order="22">
                              <span class="fw-bold">{{$detail->customer->phone_number}}</span>
                           </td>
                           <td class="text-center pe-0" data-order="22">
                              <span class="fw-bold">{{$detail->quantity}}
               </div>
               </td>
               <td class="text-center pe-0" data-order="22">
                  <div class="badge badge-light-success">{{$detail->product->new_price}}</div>
               </td>
               <td class="text-center" data-order="Inactive">
                  <div class="badge badge-light-danger">{{$detail->status}}</div>
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
@include('wholesale.layouts.footer')
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