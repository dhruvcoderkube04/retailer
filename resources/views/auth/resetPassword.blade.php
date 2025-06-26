@extends('auth.base')

@section('title')
Reset Password | TechtrendMart
@endsection

@section('content')
<div class="d-flex flex-column flex-root" id="kt_app_root">
    <style>
        body {
            background-image: url('{{ asset('assets/media/auth/bg10.jpeg') }}');
        }

        [data-bs-theme="dark"] body {
            background-image: url('{{ asset('assets/media/auth/bg10-dark.jpeg') }}');
        }
    </style>

    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="d-flex flex-lg-row-fluid">
            <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                <img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ asset('assets/media/auth/agency.png') }}" alt=""/>
                <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ asset('assets/media/auth/agency-dark.png') }}" alt=""/>
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
                        <form class="form w-100" action="{{ route('retailer.password.update') }}" method="POST">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="text-center mb-11">
                                <h1 class="text-gray-900 fw-bolder mb-3">Reset Your Password</h1>
                                <p class="text-gray-500 fw-semibold fs-6">Enter a new password to access your account</p>
                            </div>
                            <input type="hidden" name="email" value="{{ $email }}"  required />
                           <!-- New Password Field -->
                            <div class="position-relative fv-row mb-8">
                                <input type="password" placeholder="New Password" name="password"
                                    autocomplete="off" class="form-control bg-transparent pe-10" required
                                    id="new_password" />

                                <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                    onclick="togglePassword(this)" data-target="new_password">
                                    <i class="fa fa-eye-slash text-muted"></i>
                                </span>

                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="position-relative fv-row mb-8">
                                <input type="password" placeholder="Confirm Password" name="password_confirmation"
                                    autocomplete="off" class="form-control bg-transparent pe-10" required
                                    id="confirm_password" />

                                <span class="position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                    onclick="togglePassword(this)" data-target="confirm_password">
                                    <i class="fa fa-eye-slash text-muted"></i>
                                </span>

                                @error('password_confirmation')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-grid mb-10">
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Reset Password</span>
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