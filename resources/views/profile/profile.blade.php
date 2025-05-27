@extends('layouts.base')
@section('title')
    Profile | TrendMart
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
                            Account Settings</h1>
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
                            <li class="breadcrumb-item text-muted">Account</li>
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
                    <!--begin::Basic info-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_profile_details" aria-expanded="true"
                            aria-controls="kt_account_profile_details">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Profile Details</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--begin::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_profile_details" class="collapse show">
                            <!--begin::Form-->
                            @if (session('success'))
                                <div class="alert alert-success text-green-600 p-2">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <form id="kt_account_profile_details_form" class="form"
                                action="{{ route('retailer.profile.update') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <!--begin::Card body-->
                                <div class="card-body border-top p-9">
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Avatar</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Image input-->

                                            <div class="image-input image-input-outline" data-kt-image-input="true"
                                                style="background-image: url('assets/media/svg/avatars/blank.svg')">
                                                <!--begin::Preview existing avatar-->
                                                <div class="image-input-wrapper w-125px h-125px"
                                                    style="background-image: url('{{ @$userprofile->userDetail->company_logo }}')">
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
                                                    <input type="file" name="profile" accept=".png, .jpg, .jpeg" />
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
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Full Name</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Row-->
                                            <div class="row">
                                                <!--begin::Col-->
                                                <div class="col-lg-6 fv-row">
                                                    <input type="text" name="firstname"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 @error('firstname') is-invalid @enderror"
                                                        placeholder="First name" value="{{ $userprofile->firstname }}" />
                                                    @error('firstname')
                                                        <div class="invalid-feedback">{{ $message }} </div>
                                                    @enderror
                                                </div>
                                                <!--end::Col-->
                                                <!--begin::Col-->
                                                <div class="col-lg-6 fv-row">
                                                    <input type="text" name="lastname"
                                                        class="form-control form-control-lg form-control-solid @error('lastname') is-invalid @enderror"
                                                        placeholder="Last name" value="{{ $userprofile->lastname }}" />
                                                    @error('lastname')
                                                        <div class="invalid-feedback">{{ $message }} </div>
                                                    @enderror
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
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Company</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="company"
                                                class="form-control form-control-lg form-control-solid @error('company') is-invalid @enderror"
                                                placeholder="Company name"
                                                value="{{ @$userprofile->userDetail->company_name }}" />
                                            @error('company')
                                                <div class="invalid-feedback">{{ $message }} </div>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Email</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="email"
                                                class="form-control form-control-lg form-control-solid 	@error('email') is-invalid @enderror"
                                                placeholder="Email" value="{{ $userprofile->email }}" disabled />
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }} </div>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                            <span class="required">Contact Phone</span>
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
                                            <input type="tel" name="phone"
                                                class="form-control form-control-lg form-control-solid 	@error('phone') is-invalid @enderror"
                                                placeholder="Phone number" value="{{ $userprofile->phone_number }}" disabled />
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }} </div>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                            <span class="required">Country</span>
                                            <span class="ms-1" data-bs-toggle="tooltip" title="Country of origination">
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
                                            <select name="country" id="countySel" aria-label="Select a Country"
                                                data-control="select2" data-placeholder="Select a country..."
                                                class="form-select form-select-solid form-select-lg fw-semibold">
                                                <option
                                                    value="{{ !empty(@$userprofile->userDetail->country) ? @$userprofile->userDetail->country : '' }}">
                                                    {{ !empty(@$userprofile->userDetail->country) ? @$userprofile->userDetail->country : 'Select a Country...' }}
                                                </option>
                                            </select>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                            <span class="required">State</span>
                                            <span class="ms-1" data-bs-toggle="tooltip" title="Country of origination">
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
                                            <select name="state" id="stateSel" aria-label="Select a State"
                                                data-control="select2" data-placeholder="Select a state..."
                                                class="form-select form-select-solid form-select-lg fw-semibold">
                                                <option
                                                    value="{{ !empty(@$userprofile->userDetail->state) ? @$userprofile->userDetail->state : '' }}">
                                                    {{ !empty(@$userprofile->userDetail->state) ? @$userprofile->userDetail->state : 'Select a State...' }}
                                                </option>
                                            </select>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                            <span class="required">City</span>
                                            <span class="ms-1" data-bs-toggle="tooltip" title="Country of origination">
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
                                            <select name="city" id="districtSel" aria-label="Select a City"
                                                data-control="select2" data-placeholder="Select a city..."
                                                class="form-select form-select-solid form-select-lg fw-semibold">
                                                <option
                                                    value="{{ !empty(@$userprofile->userDetail->city) ? @$userprofile->userDetail->city : '' }}">
                                                    {{ !empty(@$userprofile->userDetail->city) ? @$userprofile->userDetail->city : 'Select a City...' }}
                                                </option>
                                            </select>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Address</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="address"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="Address" value="{{  @$userprofile->userDetail->address }}" />
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Pincode</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="text" name="pincode"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="Pin code" value="{{ @$userprofile->userDetail->postal_code }}" />
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
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
                    {{-- Accoutn info  --}}
                    <div class="card mb-5 mb-xl-10">
                        @if (session('success-account-info'))
                            <div class="alert alert-success text-green-600 p-2">
                                {{ session('success-account-info') }}
                            </div>
                        @endif
                        <!-- Card Header -->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_info">
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Account Info</h3>
                            </div>
                        </div>
                        <!-- Card Content -->
                        <div id="kt_account_info" class="collapse show">
                            <form action="{{ route('retailer.accountinfo') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body border-top p-9">
                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label class="form-label">Account Number</label>
                                            <input type="text" class="form-control form-control-solid" placeholder="Enter Account No" name="account_number" value="{{ @$userprofile->userDetail->account_number }}" />
                                            @error('account_number')
                                                <div class="invalid-feedback">{{ $message }} </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bank IFSC Code</label>
                                            <input type="text" class="form-control form-control-solid" placeholder="Enter Bank IFSC Code" name="ifsc_code" value="{{ @$userprofile->userDetail->ifsc_code }}" />
                                            @error('ifsc_code')
                                                <div class="invalid-feedback">{{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-6">
                                        <div class="col-md-6">
                                            <label class="form-label">Holder Name</label>
                                            <input type="text" class="form-control form-control-solid" placeholder="Enter Holder Name" name="account_holder_name"  value="{{ @$userprofile->userDetail->account_holder_name }}"/>
                                            @error('account_holder_name')
                                                <div class="invalid-feedback">{{ $message }} </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Pancard Number</label>
                                            <input type="text" class="form-control form-control-solid" placeholder="Enter Pancard Number" name="pancard_number" value="{{@$userprofile->userDetail->pancard_number }}" />
                                            @error('pancard_number')
                                                <div class="invalid-feedback">{{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Uploads -->
                                    <div class="row mb-6">
                                        <div class="col-md-3 text-center">
                                            <label class="form-label">Pan Card</label>
                                            <div class="border p-2 rounded">
                                                <img src="{{ asset(@$userprofile->userDetail->pan_image) }}" class="img-fluid mb-2" alt="Pan Card" />
                                                <input type="file" name="pan_image"
                                                       class="form-control form-control-sm @error('pan_image') is-invalid @enderror" />
                                                @error('pan_image')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <label class="form-label">Aadhar Card</label>
                                            <div class="border p-2 rounded">
                                                <img src="{{ asset(@$userprofile->userDetail->aadhar_image) }}" class="img-fluid mb-2" alt="Aadhar Card" />
                                                <input type="file" name="aadhar_image"
                                                       class="form-control form-control-sm @error('aadhar_image') is-invalid @enderror" />
                                                @error('aadhar_image')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <label class="form-label">Cancel Cheque</label>
                                            <div class="border p-2 rounded">
                                                <img src="{{ asset(@$userprofile->userDetail->cancel_cheque) }}" class="img-fluid mb-2" alt="Cancel Cheque" />
                                                <input type="file" name="cancel_cheque"
                                                       class="form-control form-control-sm @error('cancel_cheque') is-invalid @enderror" />
                                                @error('cancel_cheque')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    {{-- <div class="mb-6">
                                        <label class="form-label d-block mb-2">Status</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="status" type="radio" value="1" checked />
                                            <label class="form-check-label">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" name="status" type="radio" value="0" />
                                            <label class="form-check-label">Inactive</label>
                                        </div>
                                    </div> --}}

                                    <div class="text-end">
                                        <button class="btn btn-primary">Save Change</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
    <script src="{{ asset('assets/js/countries.js') }}"></script>
@endsection
