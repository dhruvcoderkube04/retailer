@extends('layouts.base')
@section('title')
    Profile | TechtrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Account Settings</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Account</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container ">

                    @if ($userprofile->userDetail->wallet_status == 'pending')
                        <div class="text-danger fw-semibold mb-4 mx-1 fs-5">
                            Please <a href="{{ route('retailer.profile.bank-details') }}"
                                class="text-danger text-decoration-underline">Click Here</a> to submit your bank details to
                            activate your wallet.
                        </div>
                    @elseif ($userprofile->userDetail->wallet_status == 'submitted' || $userprofile->userDetail->wallet_status == 'processing')
                        <div class="text-info fw-semibold mb-1 mx-1 fs-5">
                            You have successfully submitted your bank details. You will receive a verification message from
                            the bank shortly.
                        </div>
                        <div class="text-info fw-semibold mb-4 mx-1 fs-5">
                            <a href="javascript:void(0)" class="text-info text-decoration-underline verify-code">Click
                                here</a> to
                            verify your wallet
                            after receiving the message.
                        </div>
                    @elseif ($userprofile->userDetail->wallet_status == 'attempt_limit_reached')
                        <div class="text-danger fw-semibold mb-4 mx-1 fs-5">
                            You have entered the wrong verification code too many times. Your attempt limit has been
                            reached. Please contact support team.
                        </div>
                    @elseif ($userprofile->userDetail->wallet_status == 'rejected')
                        <div class="text-danger fw-semibold mb-4 mx-1 fs-5">
                            Your wallet activation request has been rejected. Kindly <a
                                href="{{ route('retailer.create.ticket') }}"
                                class="text-danger text-decoration-underline">Contact support team</a> for more info.
                        </div>
                        <div class="text-danger fw-semibold mb-4 mx-1 fs-5">
                            <strong>Rejected Reason :
                            </strong>{{ $userprofile->userDetail->wallet_verification_reject_reason }}
                        </div>
                    @endif

                    <div class="card mb-5 mb-xl-10">
                        <div id="kt_account_settings_profile_details" class="collapse show">
                            @if (session('success'))
                                <div class="alert alert-success text-green-600 p-3 px-4">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="card-body border-top p-9">

                                {{-- tabs --}}
                                <ul class="nav nav-tabs nav-line-tabs mb-5 fs-4" id="customTabs">
                                    <li class="nav-item">
                                        <a class="nav-link fw-bold pb-4 {{ $activeTab == 'details' ? 'active' : '' }}"
                                            href="{{ route('retailer.profile.details') }}">
                                            <i class="fa-solid fa-user fs-4 me-2"></i>
                                            Profile Details
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link fw-bold pb-4 {{ $activeTab == 'bank-details' ? 'active' : '' }}"
                                            href="{{ route('retailer.profile.bank-details') }}">
                                            <i class="fa-solid fa-building-columns fs-4 me-2"></i>
                                            Bank Details
                                        </a>
                                    </li>
                                </ul>

                                {{-- tab 1 --}}
                                <div class="tab-content" id="profile_details_tab_content">
                                    <div class="tab-pane fade {{ $activeTab == 'details' ? 'show active' : '' }}"
                                        id="profile_details">
                                        <form id="kt_account_profile_details_form" class="form"
                                            action="{{ route('retailer.profile.update') }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="card-body pt-8 p-5">
                                                <div class="row mb-6">
                                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Avatar</label>
                                                    <div class="col-lg-8">
                                                        <div class="image-input image-input-outline"
                                                            data-kt-image-input="true"
                                                            style="background-image: url('assets/media/svg/avatars/blank.svg')">
                                                            @php
                                                                $logoUrl =
                                                                    $userprofile->userDetail &&
                                                                    $userprofile->userDetail->company_logo
                                                                        ? Storage::disk('spaces')->url(
                                                                            $userprofile->userDetail->company_logo,
                                                                        )
                                                                        : asset('assets/media/avatars/no-profile.png');
                                                            @endphp
                                                            <div class="image-input-wrapper w-125px h-125px"
                                                                style="background-image: url('{{ $logoUrl }}')">
                                                            </div>
                                                            <label
                                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                                title="Change avatar">
                                                                <i class="ki-duotone ki-pencil fs-7">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                <input type="file" name="profile"
                                                                    accept=".png, .jpg, .jpeg" />
                                                                <input type="hidden" name="avatar_remove" />
                                                            </label>
                                                            <span
                                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                                title="Cancel avatar">
                                                                <i class="ki-duotone ki-cross fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </span>
                                                            <span
                                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                                title="Remove avatar">
                                                                <i class="ki-duotone ki-cross fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                            </span>
                                                        </div>
                                                        <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
                                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Full
                                                        Name</label>
                                                    <div class="col-lg-8">
                                                        <div class="row">
                                                            <div class="col-lg-6 fv-row">
                                                                <input type="text" name="firstname"
                                                                    class="form-control form-control-lg form-control-solid mb-3 mb-lg-0 @error('firstname') is-invalid @enderror"
                                                                    placeholder="First name"
                                                                    value="{{ $userprofile->firstname }}" />
                                                                @error('firstname')
                                                                    <div class="invalid-feedback">{{ $message }} </div>
                                                                @enderror
                                                            </div>
                                                            <div class="col-lg-6 fv-row">
                                                                <input type="text" name="lastname"
                                                                    class="form-control form-control-lg form-control-solid @error('lastname') is-invalid @enderror"
                                                                    placeholder="Last name"
                                                                    value="{{ $userprofile->lastname }}" />
                                                                @error('lastname')
                                                                    <div class="invalid-feedback">{{ $message }} </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
                                                    <label
                                                        class="col-lg-4 col-form-label required fw-semibold fs-6">Company</label>
                                                    <div class="col-lg-8 fv-row">
                                                        <input type="text" name="company"
                                                            class="form-control form-control-lg form-control-solid @error('company') is-invalid @enderror"
                                                            placeholder="Company name"
                                                            value="{{ @$userprofile->userDetail->company_name }}" />
                                                        @error('company')
                                                            <div class="invalid-feedback">{{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
                                                    <label
                                                        class="col-lg-4 col-form-label required fw-semibold fs-6">Email</label>
                                                    <div class="col-lg-8 fv-row">
                                                        <input type="email"
                                                            class="form-control form-control-lg form-control-solid 	@error('email') is-invalid @enderror"
                                                            placeholder="Email" value="{{ $userprofile->email }}"
                                                            disabled />
                                                        @error('email')
                                                            <div class="invalid-feedback">{{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
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
                                                    <div class="col-lg-8 fv-row">
                                                        <input type="tel" name="phone"
                                                            class="form-control form-control-lg form-control-solid 	@error('phone') is-invalid @enderror"
                                                            placeholder="Phone number"
                                                            value="{{ $userprofile->phone_number }}" disabled />
                                                        @error('phone')
                                                            <div class="invalid-feedback">{{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
                                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                        <span class="required">Country</span>
                                                        <span class="ms-1" data-bs-toggle="tooltip"
                                                            title="Country of origination">
                                                            <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </span>
                                                    </label>
                                                    <div class="col-lg-8 fv-row">
                                                        <select name="country" id="countySel"
                                                            aria-label="Select a Country" data-control="select2"
                                                            data-placeholder="Select a country..."
                                                            class="form-select form-select-solid form-select-lg fw-semibold">
                                                            <option
                                                                value="{{ !empty(@$userprofile->userDetail->country) ? @$userprofile->userDetail->country : '' }}">
                                                                {{ !empty(@$userprofile->userDetail->country) ? @$userprofile->userDetail->country : 'Select a Country...' }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
                                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                        <span class="required">State</span>
                                                        <span class="ms-1" data-bs-toggle="tooltip"
                                                            title="Country of origination">
                                                            <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </span>
                                                    </label>
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
                                                </div>

                                                <div class="row mb-6">
                                                    <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                                        <span class="required">City</span>
                                                        <span class="ms-1" data-bs-toggle="tooltip"
                                                            title="Country of origination">
                                                            <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </span>
                                                    </label>
                                                    <div class="col-lg-8 fv-row">
                                                        <select name="city" id="districtSel"
                                                            aria-label="Select a City" data-control="select2"
                                                            data-placeholder="Select a city..."
                                                            class="form-select form-select-solid form-select-lg fw-semibold">
                                                            <option
                                                                value="{{ !empty(@$userprofile->userDetail->city) ? @$userprofile->userDetail->city : '' }}">
                                                                {{ !empty(@$userprofile->userDetail->city) ? @$userprofile->userDetail->city : 'Select a City...' }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
                                                    <label
                                                        class="col-lg-4 col-form-label required fw-semibold fs-6">Address</label>
                                                    <div class="col-lg-8 fv-row">
                                                        <input type="text" name="address"
                                                            class="form-control form-control-lg form-control-solid"
                                                            placeholder="Address"
                                                            value="{{ @$userprofile->userDetail->address }}" />
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
                                                    <label
                                                        class="col-lg-4 col-form-label required fw-semibold fs-6">Pincode</label>
                                                    <div class="col-lg-8 fv-row">
                                                        <input type="text" name="pincode"
                                                            class="form-control form-control-lg form-control-solid"
                                                            placeholder="Pin code"
                                                            value="{{ @$userprofile->userDetail->postal_code }}" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                                <button type="reset"
                                                    class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                <button type="submit" class="btn btn-primary"
                                                    id="kt_account_profile_details_submit">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- tab 2 --}}
                                <div class="tab-content" id="bank_details_tab_content">
                                    <div class="tab-pane fade {{ $activeTab == 'bank-details' ? 'show active' : '' }}"
                                        id="bank_details">

                                        @if ($userprofile->userDetail->wallet_status == 'approved')
                                            <div class="d-flex align-item-center mt-8 ms-3 fs-5">
                                                <i class="ki-duotone ki-verify fs-1 me-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span>Your account has been verified</span>
                                            </div>
                                        @endif

                                        <form action="{{ route('retailer.accountinfo') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="card-body pt-9 p-5">

                                                <div class="row mb-6">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Account Number</label>
                                                        <input type="text"
                                                            class="form-control @error('account_number') is-invalid @enderror"
                                                            placeholder="Enter Account No" name="account_number"
                                                            value="{{ old('account_number', @$userprofile->userDetail->account_number) }}"
                                                            {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }} />
                                                        @error('account_number')
                                                            <div class="invalid-feedback d-block">{{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Bank IFSC Code</label>
                                                        <input type="text"
                                                            class="form-control @error('ifsc_code') is-invalid @enderror"
                                                            placeholder="Enter Bank IFSC Code" name="ifsc_code"
                                                            value="{{ old('ifsc_code', @$userprofile->userDetail->ifsc_code) }}"
                                                            {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }} />
                                                        @error('ifsc_code')
                                                            <div class="invalid-feedback d-block">{{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-6">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Holder Name</label>
                                                        <input type="text"
                                                            class="form-control @error('account_holder_name') is-invalid @enderror"
                                                            placeholder="Enter Holder Name" name="account_holder_name"
                                                            value="{{ old('account_holder_name', @$userprofile->userDetail->account_holder_name) }}"
                                                            {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }} />
                                                        @error('account_holder_name')
                                                            <div class="invalid-feedback d-block">{{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Pancard Number</label>
                                                        <input type="text"
                                                            class="form-control @error('pancard_number') is-invalid @enderror"
                                                            placeholder="Enter Pancard Number" name="pancard_number"
                                                            value="{{ old('pancard_number', @$userprofile->userDetail->pancard_number) }}"
                                                            {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }} />
                                                        @error('pancard_number')
                                                            <div class="invalid-feedback d-block">{{ $message }} </div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Uploads -->
                                                <div class="row mb-6">
                                                    <div class="col-md-3 text-center">
                                                        <label class="form-label">Pan Card</label>
                                                        <div class="border p-2 rounded">
                                                            @php
                                                                $panImage = @$userprofile->userDetail->pan_image;
                                                                $panImageUrl = $panImage
                                                                    ? Storage::disk('spaces')->url($panImage)
                                                                    : asset('assets/media/images/no_image.jpg');
                                                                $defaultImage = asset(
                                                                    'assets/media/images/no_image.jpg',
                                                                );
                                                            @endphp
                                                            <img src="{{ $panImageUrl }}" class="img-fluid mb-2"
                                                                alt="Pan Card"
                                                                onerror="this.onerror=null;this.src='{{ $defaultImage }}';" />
                                                            <input type="file" name="pan_image"
                                                                accept=".jpg, .jpeg, .png"
                                                                class="form-control form-control-sm @error('pan_image') is-invalid @enderror"
                                                                {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }} />
                                                            @error('pan_image')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                        <div id="pan_image_preview_container"
                                                            class="d-flex gap-2 mt-2 justify-content-center">
                                                            <!-- image preview will be append here -->
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-center">
                                                        <label class="form-label">Aadhar Card (Front)</label>
                                                        <div class="border p-2 rounded">
                                                            @php
                                                                $aadharImage = @$userprofile->userDetail
                                                                    ->aadhar_1_image;
                                                                $aadharImageUrl = $aadharImage
                                                                    ? Storage::disk('spaces')->url($aadharImage)
                                                                    : $defaultImage;

                                                                $defaultImage = asset(
                                                                    'assets/media/images/no_image.jpg',
                                                                );
                                                            @endphp
                                                            <img src="{{ $aadharImageUrl }}" class="img-fluid mb-2"
                                                                alt="Aadhar Card"
                                                                onerror="this.onerror=null;this.src='{{ $defaultImage }}';" />
                                                            <input type="file" name="aadhar_1_image"
                                                                accept=".jpg, .jpeg, .png"
                                                                class="form-control form-control-sm @error('aadhar_1_image') is-invalid @enderror"
                                                                {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }} />
                                                            @error('aadhar_1_image')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                        <div id="aadhar_1_image_preview_container"
                                                            class="d-flex gap-2 mt-2 justify-content-center">
                                                            <!-- image preview will be append here -->
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-center">
                                                        <label class="form-label">Aadhar Card (Back)</label>
                                                        <div class="border p-2 rounded">
                                                            @php
                                                                $aadharImage = @$userprofile->userDetail
                                                                    ->aadhar_2_image;
                                                                $aadharImageUrl = $aadharImage
                                                                    ? Storage::disk('spaces')->url($aadharImage)
                                                                    : $defaultImage;

                                                                $defaultImage = asset(
                                                                    'assets/media/images/no_image.jpg',
                                                                );
                                                            @endphp
                                                            <img src="{{ $aadharImageUrl }}" class="img-fluid mb-2"
                                                                alt="Aadhar Card"
                                                                onerror="this.onerror=null;this.src='{{ $defaultImage }}';" />
                                                            <input type="file" name="aadhar_2_image"
                                                                accept=".jpg, .jpeg, .png"
                                                                class="form-control form-control-sm @error('aadhar_2_image') is-invalid @enderror"
                                                                {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }} />
                                                            @error('aadhar_2_image')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                        <div id="aadhar_2_image_preview_container"
                                                            class="d-flex gap-2 mt-2 justify-content-center">
                                                            <!-- image preview will be append here -->
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-center">
                                                        <label class="form-label">Cancel Cheque</label>
                                                        <div class="border p-2 rounded">
                                                            @php
                                                                $cancelChequeImage = @$userprofile->userDetail
                                                                    ->cancel_cheque;
                                                                $cancelChequeImageUrl = $cancelChequeImage
                                                                    ? Storage::disk('spaces')->url($cancelChequeImage)
                                                                    : $defaultImage;

                                                                $defaultImage = asset(
                                                                    'assets/media/images/no_image.jpg',
                                                                );
                                                            @endphp
                                                            <img src="{{ $cancelChequeImageUrl }}" class="img-fluid mb-2"
                                                                alt="Cancel Cheque"
                                                                onerror="this.onerror=null;this.src='{{ $defaultImage }}';" />
                                                            <input type="file" name="cancel_cheque"
                                                                accept=".jpg, .jpeg, .png"
                                                                class="form-control form-control-sm @error('cancel_cheque') is-invalid @enderror"
                                                                {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }} />
                                                            @error('cancel_cheque')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                        <div id="cancel_cheque_preview_container"
                                                            class="d-flex gap-2 mt-2 justify-content-center">
                                                            <!-- image preview will be append here -->
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <button class="btn btn-primary"
                                                        {{ $userprofile->userDetail->wallet_status !== 'pending' ? 'disabled' : '' }}>Save
                                                        Change</button>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="text-dark fw-semibold mb-4 mx-1 fs-5">
                                            <i class="fa-solid fa-circle-info me-1"></i>
                                            For more info, <a href="{{ route('retailer.create.ticket') }}"
                                                class="text-primary text-decoration-underline">Contact our support team</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        {{-- START : Code Verification Modal --}}
        @if ($userprofile->userDetail->wallet_status !== 'pending')
            <div class="modal fade" id="wallet-verification-code-modal" tabindex="-1"
                aria-labelledby="wallet-verification-code-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title d-flex align-item-center gap-4 mt-1"
                                id="wallet-verification-code-modal-label">
                                <span class="menu-icon">
                                    <i class="fa-solid fa-spell-check"></i>
                                </span>
                                <span>Verify Your Code</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <form id="verifyCodeForm" method="POST">
                            @csrf
                            @php
                                $attemptsLeft = 3 - ($userprofile->userDetail->wallet_verification_attempt ?? 0);
                                $isLocked = $attemptsLeft <= 0;
                            @endphp
                            <div class="modal-body p-6">
                                <div class="mb-4">
                                    <label class="form-label">Code 1</label>
                                    <input type="number" class="form-control" placeholder="Enter Code 1" name="code_1"
                                        step="0.1" {{ $isLocked ? 'disabled' : '' }} required />
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Code 2</label>
                                    <input type="number" class="form-control" placeholder="Enter Code 2" name="code_2"
                                        step="0.1" {{ $isLocked ? 'disabled' : '' }} required />
                                </div>

                                <div id="verify-code-error" class="alert alert-danger d-none mb-3"></div>
                                <div id="attempts-info" class="text-danger mb-1">
                                    Attempts Left: <span
                                        id="wallet_verification_attempt">{{ 3 - ($userprofile->userDetail->wallet_verification_attempt ?? 0) }}</span>
                                    out of 3
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle"></i> Close
                                </button>
                                <button type="submit" class="btn btn-primary verify-code-btn" for="verifyCodeForm"
                                    {{ $isLocked ? 'disabled' : '' }}>
                                    <i class="bi bi-send"></i> Verify
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        {{-- START : Code Verification Modal --}}

        @include('layouts.footer')
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/countries.js') }}"></script>
    <script>
        function previewImages(input, previewContainerId) {
            let previewContainer = $('#' + previewContainerId);
            let file = input.files[0];

            // Clear previous preview
            previewContainer.empty();

            if (file && file.type.startsWith('image/')) {
                let reader = new FileReader();

                reader.onload = function(e) {
                    let img = $('<img>', {
                        src: e.target.result,
                        class: 'img-thumbnail',
                        css: {
                            maxHeight: '150px'
                        }
                    });

                    previewContainer.append(img);
                };

                reader.readAsDataURL(file);
            }
        }

        $(document).ready(function() {
            $('input[name="pan_image"], input[name="aadhar_1_image"], input[name="aadhar_2_image"], input[name="cancel_cheque"]')
                .on('change', function() {
                    let inputName = $(this).attr('name');
                    let previewId = '';

                    switch (inputName) {
                        case 'pan_image':
                            previewId = 'pan_image_preview_container';
                            break;
                        case 'aadhar_1_image':
                            previewId = 'aadhar_1_image_preview_container';
                            break;
                        case 'aadhar_2_image':
                            previewId = 'aadhar_2_image_preview_container';
                            break;
                        case 'cancel_cheque':
                            previewId = 'cancel_cheque_preview_container';
                            break;
                    }

                    if (previewId) {
                        previewImages(this, previewId);
                    }
                });

            $(document).on('click', '.verify-code', function() {
                $('#wallet-verification-code-modal').modal('show');
            });

            $(document).on('submit', '#verifyCodeForm', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();

                $.ajax({
                    url: "{{ route('retailer.bank-details.verify') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    beforeSend: function() {
                        $('#verify-code-error').addClass('d-none').text('');
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#wallet-verification-code-modal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                timer: 2000,
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            $('#verify-code-error').removeClass('d-none').text(response
                                .message);
                            if (response.attempts_left !== undefined) {
                                $('#wallet_verification_attempt').text(
                                    `${response.attempts_left}`);

                                if (response.attempts_left == 0) {
                                    location.reload();
                                }
                            }
                        }
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        if (response && response.errors) {
                            let allErrors = Object.values(response.errors).flat().join(' ');
                            $('#verify-code-error').removeClass('d-none').text(allErrors);
                        } else if (response && response.message) {
                            $('#verify-code-error').removeClass('d-none').text(response
                                .message);
                        } else {
                            $('#verify-code-error').removeClass('d-none').text(
                                'Something went wrong. Please try again.'
                            );
                        }
                    }
                });
            });
        });
    </script>
@endsection
