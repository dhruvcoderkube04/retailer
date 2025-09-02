@extends('auth.base')
@section('title')
    Sign-up Retailer | TechtrendMart
@endsection

@section('content')
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page bg image-->
        <style>
            body {
                background-image: url('assets/media/auth/bg10.jpeg');
            }

            [data-bs-theme="dark"] body {
                background-image: url('assets/media/auth/bg10-dark.jpeg');
            }
        </style>
        <!--end::Page bg image-->
        <!--begin::Authentication - Sign-up -->
        <section class="d-flex align-items-center h-100 login-section" style="background-image: url({{asset('assets/media/auth/bg-login.jpg')}}); background-size: cover; background-position: bottom; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <!--begin::Aside-->
                    <div class="col-lg-6 d-none">
                        <!--begin::Content-->
                        <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                            <!--begin::Image-->
                            <img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20"
                                src="{{ asset('assets/media/auth/agency.png') }}" alt="" />
                            <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20"
                                src="{{ asset('assets/media/auth/agency-dark.png') }}" alt="" />
                            <!--end::Image-->
                            <!--begin::Title-->
                            <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">Fast, Efficient and Productive</h1>
                            <!--end::Title-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--begin::Aside-->
                    <!--begin::Body-->
                    <div class="col-lg-6">
                        <!--begin::Wrapper-->
                        <div class="bg-body d-flex flex-column flex-center rounded-4 w-100 p-15 login-box shadow">
                            <!--begin::Content-->
                            <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-100">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-center flex-column flex-column-fluid">
                                    <!--begin::Form-->
                                    <form class="form w-100" novalidate="novalidate" id="kt_sign_up_form"
                                        action="{{ route('retailer.register') }}" method="POST">
                                        @csrf
                                        <!--begin::Heading-->
                                        {{-- <div class="text-center mb-11">
                                            <!--begin::Title-->
                                            <h1 class="text-gray-900 fw-bolder mb-3">Sign Up</h1>
                                        </div> --}}
                                        <div class="text-center mb-8">
                                        <!--begin::Title-->
                                        {{-- <h1 class="text-gray-900 fw-bolder mb-3">Sign In</h1> --}}
                                            <img class="theme-light-show mx-auto mw-100 w-150px w-lg-200px mb-8 mb-lg-10" src="{{asset('assets/media/auth/logo-dark.svg')}}" alt="" />
                                            <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-200px mb-8 mb-lg-10" src="{{asset('assets/media/auth/logo-light.svg')}}" alt="" />
                                            <h1 class="text-gray-900 fw-bolder mb-3">Sign Up</h1>
                                            {{-- <p class="text-gray-500 fw-semibold fs-6">Enter your email to receive a password reset link</p> --}}
                                            {{-- <p class="text-gray-500 fw-semibold fs-6">Enter your email to receive a password reset link</p> --}}
        
                                        </div>
                                        <div class="row g-6 mb-9">
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <input type="text" placeholder="First Name" name="firstname" autocomplete="off"
                                                    class="form-control bg-transparent" value="{{ old('firstname') }}" />
                                                @error('firstname')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <input type="text" placeholder="Last Name" name="lastname" autocomplete="off"
                                                    class="form-control bg-transparent" value="{{ old('lastname') }}" />
                                                @error('lastname')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <input type="text" placeholder="Company Name" name="companyname" autocomplete="off"
                                                class="form-control bg-transparent" value="{{ old('companyname') }}" />
                                                @error('companyname')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-6">
                                                 <input type="text" placeholder="Phone Number" name="phonenumber" autocomplete="off"
                                                class="form-control bg-transparent"  pattern="[6-9][0-9]{9}" inputmode="numeric" maxlength="10" value="{{ old('phonenumber') }}" />
                                            @error('phonenumber')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            </div>

                                            <!--end::Col-->
                                        </div>

                                        <div class="fv-row mb-8">
                                            <!--begin::Email-->
                                            <input type="email" placeholder="Email" name="email" autocomplete="off"
                                                class="form-control bg-transparent" value="{{ old('email') }}" />
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <!--end::Email-->
                                        </div>
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-8" data-kt-password-meter="true">
                                            <!--begin::Wrapper-->
                                            <div class="mb-1">
                                                <!--begin::Input wrapper-->
                                                <div class="position-relative mb-3">
                                                    <input id="password_input" type="password" name="password" placeholder="Password"
                                                        class="form-control bg-transparent pe-10" autocomplete="off" />

                                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                                        onclick="togglePassword(this)" data-target="password_input">
                                                        <i class="fa fa-eye-slash text-muted"></i>
                                                    </span>

                                                    @error('password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <!--end::Input wrapper-->
                                                <!--begin::Meter-->
                                                <div class="d-flex align-items-center mb-3"
                                                    data-kt-password-meter-control="highlight">
                                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                    </div>
                                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                    </div>
                                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                    </div>
                                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                                </div>
                                                <!--end::Meter-->
                                            </div>
                                            <!--end::Wrapper-->
                                            <!--begin::Hint-->
                                            <div class="text-muted">Use 8 or more characters with a mix of letters, numbers &
                                                symbols.</div>
                                            <!--end::Hint-->
                                        </div>
                                        <!--end::Input group=-->
                                        <!--end::Input group=-->
                                    <div class="position-relative fv-row mb-8">
                                            <input id="repeat_password_input" type="password" name="password_confirmation" placeholder="Repeat Password"
                                                class="form-control bg-transparent pe-10" autocomplete="off" />

                                            <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                                onclick="togglePassword(this)" data-target="repeat_password_input">
                                                <i class="fa fa-eye-slash text-muted"></i>
                                            </span>

                                            @error('password_confirmation')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Input group=-->
                                        <!--begin::Accept-->
                                        <div class="fv-row mb-8">
                                            <label class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="toc" value="1" />
                                                <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1">I Accept the
                                                    <a href="{{ route('terms-and-conditions') }}"
                                                        class="ms-1 link-primary">Terms</a></span>
                                            </label>
                                            @error('toc')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!--end::Accept-->
                                        <!--begin::Submit button-->
                                        <div class="d-grid mb-10">
                                            <button type="submit" id="kt_sign_up_submit" class="btn btn-dark">
                                                <!--begin::Indicator label-->
                                                <span class="indicator-label">Sign up</span>
                                                <!--end::Indicator label-->
                                                <!--begin::Indicator progress-->
                                                <span class="indicator-progress">Please wait...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                <!--end::Indicator progress-->
                                            </button>
                                        </div>
                                        <!--end::Submit button-->
                                        <!--begin::Sign up-->
                                        <div class="text-gray-500 text-center fw-semibold fs-6">Already have an Account?
                                            <a href="{{ route('retailer.login') }}" class="link-primary fw-semibold">Sign in</a>
                                        </div>
                                        <!--end::Sign up-->
                                    </form>
                                    <!--end::Form-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                </div>
            </div>
        </section>
    <script>
        function togglePassword(element) {
            const inputId = element.getAttribute('data-target');
            const input = document.getElementById(inputId);
            const icon = element.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
@endsection
