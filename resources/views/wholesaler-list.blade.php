@extends('layouts.base')
@section('title')
    Wholesaler List | TrendMart
@endsection
@section('content')
    @if ($is_all_wholesaler_visible)
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

            <div class="d-flex flex-column flex-column-fluid">
                <!--begin::Toolbar-->
                <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                    <!--begin::Toolbar container-->
                    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                        <!--begin::Page title-->
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <!--begin::Title-->
                            <h1
                                class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                                Wholesalers
                            </h1>
                            <!--end::Title-->
                            <!--begin::Breadcrumb-->
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <!--begin::Item-->
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ route('retailer.dashboard') }}"
                                        class="text-muted text-hover-primary">Home</a>
                                </li>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                </li>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <li class="breadcrumb-item text-muted">Social</li>
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
                        <!--begin::Social - Followers -->
                        <div class="d-flex flex-row">
                            <!--begin::Content-->
                            <div class="w-100 flex-lg-row-fluid mx-lg-13">
                                <!--begin::Mobile toolbar-->
                                <div class="d-flex d-lg-none align-items-center justify-content-end mb-10">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="btn btn-icon btn-active-color-primary w-30px h-30px"
                                            id="kt_social_start_sidebar_toggle">
                                            <i class="ki-duotone ki-profile-circle fs-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                        <div class="btn btn-icon btn-active-color-primary w-30px h-30px"
                                            id="kt_social_end_sidebar_toggle">
                                            <i class="ki-duotone ki-scroll fs-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Mobile toolbar-->
                                <!--begin::Row-->
                                <div class="row g-6 mb-6 g-xl-9 mb-xl-9">
                                    <!--begin::Followers-->
                                    <!--begin::Col-->
                                    @foreach ($wholesalers as $wholesaler)
                                        <div class="col-md-4">
                                            <!--begin::Card-->
                                            <div class="card">
                                                <!--begin::Card body-->
                                                <div class="card-body d-flex flex-center flex-column py-9 px-5">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-65px symbol-circle mb-5">
                                                        <!-- {{ $wholesaler->userDetail->company_logo }} -->
                                                        <img src="{{ $wholesaler->userDetail->company_logo
                                                            ?  $wholesaler->userDetail->company_logo
                                                            : asset('assets/media/avatars/no-profile.png') }}"
                                                            alt="image" />
                                                        <div
                                                            class="bg-success position-absolute rounded-circle translate-middle start-100 top-100 border border-4 border-body h-15px w-15px ms-n3 mt-n3">
                                                        </div>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Name-->
                                                    <a href="#"
                                                        class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">{{ $wholesaler->userDetail->company_name }}</a>
                                                    <!--end::Name-->
                                                    <!--begin::Position-->
                                                    <div class="fw-semibold text-gray-500 mb-6">{{ $wholesaler->firstname }}
                                                        {{ $wholesaler->lastname }}</div>
                                                    <!--end::Position-->
                                                    <!--begin::Info-->
                                                    {{-- <div class="d-flex flex-center flex-wrap mb-5">
                                                    <!--begin::Stats-->
                                                    <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                                                        <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                        <div class="fw-semibold text-gray-500">Total Product</div>
                                                    </div>
                                                    <!--end::Stats-->
                                                    <!--begin::Stats-->
                                                    <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                                                        <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                        <div class="fw-semibold text-gray-500">Sales</div>
                                                    </div>
                                                    <!--end::Stats-->
                                                </div> --}}
                                                    <!--end::Info-->
                                                    <!--begin::Follow-->
                                                    <a href="{{ route('retailer.view-category-margin', $wholesaler->id) }}"
                                                        class="btn btn-primary">View Product</a>
                                                    <!--end::Follow-->
                                                </div>
                                                <!--begin::Card body-->
                                            </div>
                                            <!--begin::Card-->
                                        </div>
                                    @endforeach
                                    <!--end::Col-->
                                    <!--end::Followers-->
                                </div>
                                <!--end::Row-->
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Social - Followers-->
                    </div>
                    <!--end::Content container-->
                </div>
                <!--end::Content-->
            </div>

            @include('layouts.footer')

        </div>
    @else
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div id="kt_app_content_container" class="app-container container-xxl">
                        <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10">
                            <i class="ki-duotone ki-message-text-2 fs-2hx text-primary me-4 mt-2 mb-5 mb-sm-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column ps-3 m-1 pe-sm-10">
                                <h4 class="fw-semibold">No Access</h4>
                                <p class="mb-2">Unfortunately, you do not have the required access to use this facility.
                                </p>
                                <p>If you believe you should have access, please contact your administrator for further
                                    assistance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
