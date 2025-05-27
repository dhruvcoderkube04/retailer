@extends('layouts.base')
@section('title')
    Setting | TrendMart
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
                            Site Settings</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Site Setting</li>
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
                        <div class="alert alert-danger text-red-600 p-2">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-warning text-yellow-400 p-2">
                            {{ session('info') }}
                        </div>
                    @endif

                    <!--begin::Basic info-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_profile_details" aria-expanded="true"
                            aria-controls="kt_account_profile_details">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Store Setting</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--begin::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_profile_details" class="collapse show">
                            <!--begin::Form-->
                            <form id="kt_account_profile_details_form" class="form" method="POST"
                                action="{{ route('retailer.setting.update') }}" enctype="multipart/form-data">
                                @csrf
                                <!--begin::Card body-->
                                <div class="card-body border-top p-9">
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Logo</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Image input-->

                                            <div class="image-input image-input-outline" data-kt-image-input="true"
                                                style="background-image: url('assets/media/svg/avatars/blank.svg')">
                                                <!--begin::Preview existing avatar-->
                                                <div class="image-input-wrapper w-125px h-125px"
                                                    style="background-image: url('{{ $store->logo ? Storage::disk('spaces')->url($store->logo) : asset('uploads/company_logo/default.png') }}')">
                                                </div>

                                                {{-- <div class="image-input-wrapper w-125px h-125px" style="background-image: url(assets/media/avatars/300-1.jpg)"></div> --}}
                                                <!--end::Preview existing avatar-->
                                                <!--begin::Label-->
                                                <label
                                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                    title="Change avatar">
                                                    <i class="ki-duotone ki-pencil fs-7">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <!--begin::Inputs-->
                                                    <input type="file" name="logo" accept=".png, .jpg, .jpeg" />
                                                    <input type="hidden" name="avatar_remove" />
                                                    <!--end::Inputs-->
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Cancel-->
                                                <span
                                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                    title="Cancel avatar">
                                                    <i class="ki-duotone ki-cross fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                                <!--end::Cancel-->
                                                <!--begin::Remove-->
                                                <span
                                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                    title="Remove avatar">
                                                    <i class="ki-duotone ki-cross fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                                <!--end::Remove-->
                                            </div>
                                            <!--end::Image input-->
                                            <!--begin::Hint-->
                                            <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                                            <!--end::Hint-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Brand Name</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Row-->
                                            <div class="row">
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row">
                                                    <input type="text" name="store_name" value="{{ $store->store_name }}"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                        placeholder="Ex:- Stylica" />
                                                </div>
                                                @error('store_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Row-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Web Token</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Row-->
                                            <div class="row">
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row">
                                                    <input type="text" value="{{ $store->product_listing_key }}"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                        disabled />
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Row-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Mobile No</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="mobile_no"
                                                class="form-control form-control-lg form-control-solid"
                                                value="{{ $store->mobile_no }}" placeholder="7273672368" />
                                        </div>
                                        @error('mobile_no')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                            <span class="required">Address</span>
                                            <span class="ms-1" data-bs-toggle="tooltip"
                                                title="Phone number must be active">
                                                <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </span>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="address" value="{{ $store->address }}"
                                                class="form-control form-control-lg form-control-solid" placeholder="" />
                                        </div>
                                        @error('address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Store Time</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <select name="store_time" aria-label="Select Working Hours"
                                                data-control="select2" data-placeholder="Select Working Hours.."
                                                class="form-select form-select-solid form-select-lg">
                                                <option value="">Select Working Hours..</option>
                                                <option value="9am-6pm"
                                                    {{ $store->store_time == '9am-6pm' ? 'selected' : '' }}>9 AM - 6 PM
                                                </option>
                                                <option value="10am-7pm"
                                                    {{ $store->store_time == '10am-7pm' ? 'selected' : '' }}>10 AM - 7 PM
                                                </option>
                                                <option value="11am-8pm"
                                                    {{ $store->store_time == '11am-8pm' ? 'selected' : '' }}>11 AM - 8 PM
                                                </option>
                                            </select>
                                        </div>
                                        @error('store_time')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Favicon</label>
                                                <input type="file" class="form-control" id="favicon" name="favicon"
                                                    accept="image/x-icon,image/png,image/jpeg"
                                                    onchange="previewFavicon(event)">
                                                <small class="text-muted">Upload a favicon (Recommended: 32x32 or 64x64,
                                                    .ico or .png)</small>
                                            </div>
                                            {{-- Favicon preview --}}
                                            <div id="favicon-preview-container" class="d-flex gap-2 mt-2">
                                                @php
                                                    $defaultFavicon = asset('assets/media/images/no_image.jpg');
                                                    $faviconUrl = !empty($store->favicon)
                                                        ? Storage::disk('spaces')->url($store->favicon)
                                                        : $defaultFavicon;
                                                @endphp
                                                @if (!empty($store->favicon))
                                                    <img src="{{ $faviconUrl }}" alt="Favicon Preview"
                                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc;"
                                                        onerror="this.onerror=null;this.src='{{ $defaultFavicon }}';" />
                                                @endif
                                            </div>

                                            {{-- <div id="favicon-preview-container" class="d-flex gap-2 mt-2"></div> --}}
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Website Banner</label>
                                                <input type="file" class="form-control" id="banner" name="banner"
                                                    accept="image/*" onchange="previewBanner(event)">
                                                <small class="text-muted">Recommended size: 1200x300 pixels</small>
                                            </div>
                                            {{-- Banner preview --}}
                                            <div id="banner-preview-container" class="mt-3">
                                                @php
                                                    $defaultBanner = asset('assets/media/images/no_image.jpg');
                                                    $bannerUrl = !empty($store->banner)
                                                        ? Storage::disk('spaces')->url($store->banner)
                                                        : $defaultBanner;
                                                @endphp
                                                @if (!empty($store->banner))
                                                    <img src="{{ $bannerUrl }}" alt="Banner Preview"
                                                        style="max-width: 100%; width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid #ccc;"
                                                        onerror="this.onerror=null;this.src='{{ $defaultBanner }}';" />
                                                @endif
                                            </div>
                                            {{-- <div id="banner-preview-container" class="mt-3"></div> --}}

                                        </div>
                                    </div>

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Offer Text </span>
                                                <span class="ms-1" data-bs-toggle="tooltip" title="">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="offer_text" value="{{ $store->offer_text }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="" />
                                            </div>
                                            @error('offer_text')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Banner Title </span>
                                                <span class="ms-1" data-bs-toggle="tooltip" title="">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="banner_title"
                                                    value="{{ $store->banner_title }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="" />
                                            </div>
                                            @error('banner_title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Banner Sub Title </span>
                                                <span class="ms-1" data-bs-toggle="tooltip" title="">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="banner_sub_title"
                                                    value="{{ $store->banner_sub_title }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="" />
                                            </div>
                                            @error('banner_sub_title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Banner Button Title</span>
                                                <span class="ms-1" data-bs-toggle="tooltip" title="">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="banner_button_title"
                                                    value="{{ $store->banner_button_title }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="" />
                                            </div>
                                            @error('banner_button_title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->


                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Facebook URL </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="facebook_url"
                                                    value="{{ $store->facebook_url }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('facebook_url')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Google Analytics Id</span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="google_analytics_id"
                                                    value="{{ $store->google_analytics_id }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('google_analytics_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Twitter URL</span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="twitter_url"
                                                    value="{{ $store->twitter_url }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('twitter_url')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Instagram Id </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="instagram_id"
                                                    value="{{ $store->instagram_id }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('instagram_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Google Plus URL </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="google_plus_url"
                                                    value="{{ $store->google_plus_url }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('google_plus_url')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Facebook Pixel ID </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="facebook_pixel_id"
                                                    value="{{ $store->facebook_pixel_id }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('facebook_pixel_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> App Store URL </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="apple_store_id"
                                                    value="{{ $store->apple_store_id }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('apple_store_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Linkedin URL </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="linkedin_url"
                                                    value="{{ $store->linkedin_url }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('linkedin_url')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Play Store URL </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="play_store_url"
                                                    value="{{ $store->play_store_url }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('play_store_url')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Instagram URL </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="instagram_url"
                                                    value="{{ $store->instagram_url }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="#" />
                                            </div>
                                            @error('instagram_url')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Meta Title </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="meta_title" value="{{ $store->meta_title }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="Ex:- Stylica Fasion" />
                                            </div>
                                            @error('meta_title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Meta Keywords </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="meta_keywords"
                                                    value="{{ $store->meta_keywords }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="" />
                                            </div>
                                            @error('meta_keywords')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required">Meta Description</span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="meta_description"
                                                    value="{{ $store->meta_description }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="" />
                                            </div>
                                            @error('meta_description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> COD Charge</span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="number" name="cod_charge" value="{{ $store->cod_charge }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="" />
                                            </div>
                                            @error('cod_charge')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Shipping Charge </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="shipping_charge"
                                                    value="{{ $store->shipping_charge }}" min="1"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="1" />
                                            </div>
                                            @error('shipping_charge')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Cart Limit Value </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Phone number must be active">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Col-->
                                            <div class="col-lg-8 fv-row">
                                                <input type="text" name="cart_limit" value="{{ $store->cart_limit }}"
                                                    class="form-control form-control-lg form-control-solid"
                                                    placeholder="10" />
                                            </div>
                                            @error('cart_limit')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <!--begin::Label-->
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> SMS Service </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Enable or disable SMS service">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <!--end::Label-->
                                            <div class="col-lg-8 fv-row">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="sms_service" value="1"
                                                        class="form-check-input"
                                                        {{ $store->sms_service == 1 ? 'checked' : '' }}> ON
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="sms_service" value="0"
                                                        class="form-check-input"
                                                        {{ $store->sms_service == 0 ? 'checked' : '' }}> OFF
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Enquiry Whatsapp </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Enable or disable WhatsApp Enquiry">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <div class="col-lg-8 fv-row">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="enquiry_whatsapp" value="1"
                                                        class="form-check-input"
                                                        {{ $store->enquiry_whatsapp == 1 ? 'checked' : '' }}> Active
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="enquiry_whatsapp" value="0"
                                                        class="form-check-input"
                                                        {{ $store->enquiry_whatsapp == 0 ? 'checked' : '' }}> Inactive
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Hide Pickup Address </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Enable or disable pickup address visibility">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <div class="col-lg-8 fv-row">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="hide_pickup_address" value="1"
                                                        class="form-check-input"
                                                        {{ $store->hide_pickup_address == 1 ? 'checked' : '' }}> YES
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="hide_pickup_address" value="0"
                                                        class="form-check-input"
                                                        {{ $store->hide_pickup_address == 0 ? 'checked' : '' }}> NO
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                <span class="required"> Request Offer </span>
                                                <span class="ms-1" data-bs-toggle="tooltip"
                                                    title="Enable or disable request offer">
                                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </span>
                                            </label>
                                            <div class="col-lg-8 fv-row">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="request_offer" value="1"
                                                        class="form-check-input"
                                                        {{ $store->request_offer == 1 ? 'checked' : '' }}> Active
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="request_offer" value="0"
                                                        class="form-check-input"
                                                        {{ $store->request_offer == 0 ? 'checked' : '' }}> Inactive
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!--end::Card body-->
                                <!--begin::Actions-->
                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <button type="reset"
                                        class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                    <button type="submit" class="btn btn-primary"
                                        id="kt_account_profile_details_submit">Save Changes</button>
                                </div>
                                <!--end::Actions-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Basic info-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->
@endsection


@section('script')
    <script>
        function previewFavicon(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('favicon-preview-container');
            previewContainer.innerHTML = ''; // Clear previous preview

            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '32px'; // Typical favicon size
                img.style.height = '32px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '4px';
                img.style.border = '1px solid #ccc';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }

        function previewBanner(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('banner-preview-container');
            previewContainer.innerHTML = ''; // Clear previous preview

            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%'; // Full width for preview
                img.style.maxWidth = '600px'; // Adjust based on your layout
                img.style.height = 'auto';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                img.style.border = '1px solid #ccc';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    </script>
@endsection
