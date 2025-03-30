@extends('layouts.base')
@section('title')
Rate Calculator - TrendMart
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
               <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Rate Calculator</h1>
               <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                  <li class="breadcrumb-item text-muted">
                     <a href="#" class="text-muted text-hover-primary">Shipping</a>
                  </li>
                  <li class="breadcrumb-item">
                     <span class="bullet bg-gray-500 w-5px h-2px"></span>
                  </li>
                  <li class="breadcrumb-item text-muted">Rate Calculation</li>
               </ul>
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
            <form id="rate_calculator_form" class="form" action="#" method="post">
               @csrf
               <div class="card card-flush py-4">
                  <div class="card-header">
                     <div class="card-title">
                        <h2>Rate Calculation</h2>
                     </div>
                  </div>

                  <!--begin::Card body-->
                  <div class="card-body pt-0">
                     <div class="row mb-5">
                        <div class="col-md-6">
                           <label class="form-label">Shipment Type</label>
                           <div class="d-flex gap-4">
                              <label class="form-check form-check-custom form-check-solid">
                                 <input class="form-check-input" type="radio" name="shipment_type" value="air" checked />
                                 <span class="form-check-label">By Air</span>
                              </label>
                              <label class="form-check form-check-custom form-check-solid">
                                 <input class="form-check-input" type="radio" name="shipment_type" value="road" />
                                 <span class="form-check-label">By Road</span>
                              </label>
                           </div>
                        </div>
                     </div>

                     <div class="row mb-5">
                        <div class="col-md-6">
                           <label class="form-label">Approximate Weight (Kg)</label>
                           <input type="number" name="weight" class="form-control" placeholder="E.g 0.5" step="0.01" />
                        </div>
                     </div>

                     <div class="row mb-5">
                        <label class="form-label">Dimensions (l × b × h) / 5000</label>
                        <div class="d-flex align-items-center gap-2">
                           <button type="button" class="btn btn-warning">CM</button>
                           <input type="number" name="length" class="form-control w-25" placeholder="L" />
                           <input type="number" name="breadth" class="form-control w-25" placeholder="B" />
                           <input type="number" name="height" class="form-control w-25" placeholder="H" />
                        </div>
                     </div>

                     <div class="row mb-5">
                        <label class="form-label">Applicable Weight</label>
                        <input type="text" name="applicable_weight" class="form-control" readonly />
                     </div>

                     <div class="row mb-5">
                        <label class="form-label">Payment Type</label>
                        <div class="d-flex gap-4">
                           <label class="form-check form-check-custom form-check-solid">
                              <input class="form-check-input" type="radio" name="payment_type" value="cod" checked />
                              <span class="form-check-label">COD</span>
                           </label>
                           <label class="form-check form-check-custom form-check-solid">
                              <input class="form-check-input" type="radio" name="payment_type" value="prepaid" />
                              <span class="form-check-label">Prepaid</span>
                           </label>
                        </div>
                     </div>

                     <div class="row mb-5">
                        <label class="form-label">Declared Value in INR</label>
                        <input type="number" name="declared_value" class="form-control" placeholder="Declared Value" />
                     </div>
                  </div>
                  <!--end::Card body-->
                  <div class="d-flex justify-content-end m-3">
                     <button type="submit" class="btn btn-primary">Calculate</button>
                  </div>
               </div>

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
<script>
   document.querySelector('#rate_calculator_form').addEventListener('submit', function(event) {
       event.preventDefault();

       let length = parseFloat(document.querySelector('input[name="length"]').value) || 0;
       let breadth = parseFloat(document.querySelector('input[name="breadth"]').value) || 0;
       let height = parseFloat(document.querySelector('input[name="height"]').value) || 0;

       let volumetricWeight = (length * breadth * height) / 5000;
       document.querySelector('input[name="applicable_weight"]').value = volumetricWeight.toFixed(2);
   });
</script>
@endsection
