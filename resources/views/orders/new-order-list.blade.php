@extends('layouts.base')
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
               <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                 Wholesaler Orders</h1>
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

            <!--begin::Tabs Navigation-->
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
               <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && (request('type') == 'new' || request('type') == null) ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'new']) }}">
                      <i class="fas fa-sync-alt pe-2"></i> New
                      <span class="badge bg-success ms-2 d-none">{{ $count['new'] ?? 0 }}</span>
                    </a>
                  </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'processing' ? 'active' : '' }}"  href="{{ route('retailer.new-order.list', ['type' => 'processing']) }}">
                     <i class="fas fa-sync-alt pe-2"></i> Processing
                     <span class="badge bg-success ms-2 d-none">{{ $count['processing'] ?? 0 }}</span>
                   </a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'ready_to_ship' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'ready_to_ship']) }}">
                     <i class="fas fa-box pe-2"></i> Ready to Ship
                     <span class="badge bg-success ms-2 d-none">{{ $count['ready_to_ship'] ?? 0 }}</span>
                   </a>
                 </li>
                 <li class="nav-item dropdown">
                    <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'pickups' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'pickups']) }}">
                        <i class="fas fa-box pe-2"></i> Pickups
                        <span class="badge bg-success ms-2 d-none">{{ $count['pickups'] ?? 0 }}</span>
                      </a>
                   {{-- <a class="nav-link dropdown-toggle" href="{{ route('retailer.new-order.list', ['type' => 'pickups']) }}" id="pickupsDropdown" role="button"
                     data-bs-toggle="dropdown" aria-expanded="false">
                     <i class="fas fa-truck-loading pe-2"></i> Pickups
                     <span class="badge bg-success ms-2 d-none">0</span>
                   </a> --}}
                   {{-- <ul class="dropdown-menu" aria-labelledby="pickupsDropdown">
                     <li><a class="dropdown-item" href="#">All</a></li>
                     <li><a class="dropdown-item" href="#">Manifested</a></li>
                     <li><a class="dropdown-item" href="#">Unmanifested</a></li>
                   </ul> --}}
                 </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'transit' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'transit']) }}">
                     <i class="fas fa-shipping-fast pe-2"></i> Transit
                     <span class="badge bg-success ms-2 d-none">{{ $count['transit'] ?? 0 }}</span>
                   </a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'ofd' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'ofd']) }}">
                     <i class="fas fa-truck pe-2"></i> OFD
                     <span class="badge bg-success ms-2 d-none">{{ $count['ofd'] ?? 0 }}</span>
                   </a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'delivered' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'delivered']) }}">
                     <i class="fas fa-check-circle pe-2"></i> Delivered
                     <span class="badge bg-success ms-2 d-none">{{ $count['delivered'] ?? 0 }}</span>
                   </a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'rto' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'rto']) }}">
                     <i class="fas fa-undo-alt pe-2"></i> RTO
                     <span class="badge bg-success ms-2 d-none">{{ $count['rto'] ?? 0 }}</span>
                   </a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'received' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'received']) }}">
                     <i class="fas fa-box-open pe-2"></i> Received
                     <span class="badge bg-success ms-2 d-none">{{ $count['received'] ?? 0 }}</span>
                   </a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'cancel' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'cancel']) }}">
                     <i class="fas fa-times-circle pe-2"></i> Cancel
                     <span class="badge bg-success ms-2 d-none">{{ $count['cancel'] ?? 0 }}</span>
                   </a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link {{ request()->routeIs('retailer.new-order.list') && request('type') == 'close' ? 'active' : '' }}" href="{{ route('retailer.new-order.list', ['type' => 'close']) }}">
                     <i class="fas fa-lock pe-2"></i> Close
                     <span class="badge bg-success ms-2 d-none">{{ $count['close'] ?? 0 }}</span>
                   </a>
                 </li>
               </ul>
            </div>

            <!--end::Tabs Navigation-->

            <hr>
            <!--begin::Products-->
            <div class="card card-flush">
               <!--begin::Card header-->
               <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                 <!--begin::Card title-->
                 <div class="card-title d-flex flex-wrap align-items-center gap-3 justify-content-end w-100">

                   <!-- Date Filter -->
                   <select class="form-select w-200px">
                     <option value="today">Today</option>
                     <option value="yesterday">Yesterday</option>
                     <option value="last7days">Last 7 Days</option>
                     <option value="last30days">Last 30 Days</option>
                     <option value="thisMonth">This Month</option>
                     <option value="lastMonth">Last Month</option>
                     <option value="custom">Custom Range</option>
                   </select>

                   <!-- Order Type Filter -->
                   <select class="form-select w-150px">
                     <option value="all">All Orders</option>
                     <option value="punch">Punch</option>
                     <option value="other">Other</option>
                   </select>

                   <!-- Download Button (Green with Excel Icon) -->
                   <button type="submit" class="btn btn-success" id="downloadBtn">
                     <i class="ki-duotone ki-file-down fs-3 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                     </i> Download Orders
                   </button>

                   <!-- Additional Buttons -->
                   <button type="button" class="btn btn-primary d-none" id="downloadLabelBtn">
                     <i class="fas fa-tag me-2"></i> Download Label
                   </button>

                   <button type="button" class="btn btn-warning d-none" id="packedBtn">
                     <i class="fas fa-box me-2"></i> Packed
                   </button>

                   <button type="button" class="btn btn-secondary d-none" id="unpackedBtn">
                     <i class="fas fa-box-open me-2"></i> Unpacked
                   </button>

                   <button type="button" class="btn btn-info d-none" id="manifestBtn">
                     <i class="fas fa-file-invoice me-2"></i> Manifest
                   </button>
                   <!-- Search Bar -->
                   <div class="d-flex align-items-center position-relative my-1">
                     <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                     </i>
                     <input type="text" data-kt-ecommerce-product-filter="search"
                        class="form-control form-control-solid w-250px ps-12" placeholder="Search Product" />
                   </div>
                 </div>
                 <!--end::Card title-->
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
                    @foreach ($retailerOrders as $detail)
                      <tr>
                        <td class="text-center">
                          <button type="button" class="btn btn-primary btn-sm orderActionButton"
                            data-product-id="{{ $detail->retailer_clone_product_id }}"
                            @if (is_null($detail->wholesaler_id))
                              data-retailer-id="{{ $detail->retailer_id }}"
                            @endif
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
                              <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                data-kt-ecommerce-product-filter="product_name">{{ $detail->retailerCloneProduct->name ?? $detail->product->name }}</a>
                            </div>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="ms-5">
                            <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold"
                              data-kt-ecommerce-product-filter="product_name">{{$detail->retailer->userDetail->company_name}}</a>
                          </div>
                        </td>
                        <td class="text-center pe-0">
                          <span class="fw-bold">{{$detail->customer->firstname}} {{$detail->customer->lastname}}</span>
                        </td>
                        <td class="text-center pe-0">
                          <span class="fw-bold">{{$detail->customer->phone_number}}</span>
                        </td>
                        <td class="text-center pe-0">
                          <span class="fw-bold">{{$detail->quantity}}</span>
                        </td>
                        <td class="text-center pe-0">
                          <div class="badge badge-light-success">  {{ $detail->retailerCloneProduct->new_price ?? $detail->product->new_price }}</div>
                        </td>
                        <td class="text-center">
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
      @include('layouts.footer')
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

   <script>
      $(document).ready(function () {
        // Simulated badge counts (Replace these with real dynamic counts)
        let tabBadgeCounts = {
          pickups: 10,
          transit: 5,
          ofd: 3,
          delivered: 8,
          rto: 2,
          received: 6,
          cancel: 4,
          processing: 7,
          ready_to_ship: 9,
          close: 1
        };

        // Handle tab switching
        $(".nav-link").on("shown.bs.tab", function () {
         $(".nav-link.dropdown-toggle.active").removeClass("active");
          let selectedTab = $(this).attr("href")?.replace("#", ""); // Get tab ID
          if (selectedTab) {
            toggleButtons(selectedTab);
            updateActiveTabBadge($(this), tabBadgeCounts[selectedTab] || 0);
          }
        });

        // Handle dropdown selection inside the Pickups tab
        $(".dropdown-item").click(function (e) {
          e.preventDefault(); // Prevent default link behavior

          let pickupsTab = $("#pickupsDropdown"); // Main Pickups tab
          let badge = pickupsTab.find(".badge"); // Badge inside Pickups
          let selectedOption = $(this).text().trim(); // Get the selected option (All, Manifested, Unmanifested)

          // Simulated count update for pickups (Adjust based on real data)
          let pickupCounts = {
            All: 8,
            Manifested: 5,
            Unmanifested: 3
          };

          let count = pickupCounts[selectedOption] || 0; // Get number for selected option
          badge.text(count).removeClass("d-none"); // Set number inside the badge

          // Set only the Pickups tab as active and remove active from others
          $(".nav-link").removeClass("active");
          pickupsTab.addClass("active");

          // Ensure only Pickups badge is shown
          updateActiveTabBadge(pickupsTab, count);

          // Ensure the correct buttons are displayed for Pickups
          toggleButtons("pickups");
        });

        function toggleButtons(tab) {
          let downloadBtn = $("#downloadBtn");
          let downloadLabelBtn = $("#downloadLabelBtn");
          let packedBtn = $("#packedBtn");
          let unpackedBtn = $("#unpackedBtn");
          let manifestBtn = $("#manifestBtn");

          // Tabs where all buttons should be visible
          let showAllTabs = ["pickups", "transit", "ofd", "delivered", "rto", "received", "cancel"];

          // Tabs where only the Download button should be visible
          let showDownloadOnlyTabs = ["processing", "ready_to_ship", "close"];

          if (tab === "pickups") {
            // Show all buttons for Pickups tab
            downloadBtn.removeClass("d-none");
            downloadLabelBtn.removeClass("d-none");
            packedBtn.removeClass("d-none");
            unpackedBtn.removeClass("d-none");
            manifestBtn.removeClass("d-none");
            return;
          }

          if (showAllTabs.includes(tab)) {
            // Show all buttons
            downloadBtn.removeClass("d-none");
            downloadLabelBtn.removeClass("d-none");
            packedBtn.removeClass("d-none");
            unpackedBtn.removeClass("d-none");
            manifestBtn.removeClass("d-none");
          } else if (showDownloadOnlyTabs.includes(tab)) {
            // Show only the Download button
            downloadBtn.removeClass("d-none");
            downloadLabelBtn.addClass("d-none");
            packedBtn.addClass("d-none");
            unpackedBtn.addClass("d-none");
            manifestBtn.addClass("d-none");
          } else {
            // Default behavior (adjust if needed)
            downloadBtn.removeClass("d-none");
            downloadLabelBtn.addClass("d-none");
            packedBtn.addClass("d-none");
            unpackedBtn.addClass("d-none");
            manifestBtn.addClass("d-none");
          }
        }

        function updateActiveTabBadge(activeTab, count) {
          // Hide all badges first
          $(".badge").addClass("d-none");
          // Show only the active tab's badge with a number
          activeTab.find(".badge").text(count).removeClass("d-none");
          
        }

        // Initialize badges and buttons for the default active tab
        let defaultTab = $(".nav-link.active").attr("href")?.replace("#", "");
        if (defaultTab) {
          toggleButtons(defaultTab);
          updateActiveTabBadge($(".nav-link.active"), tabBadgeCounts[defaultTab] || 0);
        }
      });

   </script>
@endsection