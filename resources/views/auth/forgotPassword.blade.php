@extends('auth.base')

@section('title')
Forgot Password | TechtrendMart
@endsection

@section('content')
<div class="d-flex flex-column flex-root" id="kt_app_root">
    <style>
        body {
            background-image: url('assets/media/auth/bg10.jpeg');
        }

        [data-bs-theme="dark"] body {
            background-image: url('assets/media/auth/bg10-dark.jpeg');
        }
    </style>

    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="d-flex flex-lg-row-fluid">
            <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                <img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ asset('assets/media/auth/agency.png') }}" alt="" />
                <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ asset('assets/media/auth/agency-dark.png') }}" alt="" />
                <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">Fast, Efficient and Productive</h1>
            </div>
        </div>

        <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
            <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10">
                <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action="{{ route('retailer.password.email') }}" method="POST">
                            @csrf

                            <div class="text-center mb-11">
                                <h1 class="text-gray-900 fw-bolder mb-3">Forgot Password?</h1>
                                <p class="text-gray-500 fw-semibold fs-6">Enter your email to receive a password reset link</p>
                            </div>

                            <div class="fv-row mb-8">
                                <input type="email" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" required />
                                 @error('email')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid mb-10">
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Send Reset Link</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>

                            <div class="text-gray-500 text-center fw-semibold fs-6">
                                Remembered your password?
                                <a href="{{ route('retailer.login') }}" class="link-primary">Sign In</a>
                            </div>
                        </form>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
