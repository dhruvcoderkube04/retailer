@extends('layouts.base')

@section('title')
Create Ticket | TechtrendMart
@endsection

@section('content')
<!--begin::Main-->
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">
                        Create Ticket</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Create Ticket</li>
                    </ul>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container ">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">New Ticket Form</h3>
                    </div>
                    <div class="card-body">
                        {{-- Flash Messages --}}
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        <form action="{{ route('retailer.generate.ticket') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Ticket Subject -->
                            <div class="mb-5">
                                <label class="form-label required">Ticket Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-5">
                                <label class="form-label required">Description</label>
                                <textarea name="ticket_description" class="form-control" rows="5" placeholder="Describe your issue..." required></textarea>
                            </div>

                            <!-- File Upload -->
                            <div class="mb-5">
                                <label class="form-label">Upload Screenshots (optional)</label>
                                <input class="form-control" type="file" name="ticket_image_ref[]" id="ticket_image_ref" multiple accept="image/jpeg,image/png,image/jpg">
                                <div id="image-error" class="text-danger mt-2" style="display: none;"></div>
                                <small class="text-muted">Max 3 images. Allowed types: jpg, jpeg, png. Max size: 2MB each.</small>
                            </div>

                            <!-- Submit -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-danger">Raise Issue</button>
                                <button type="reset" class="btn btn-primary">Discard</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end::Card-->
            </div>
        </div>
        <!--end::Content-->
    </div>
</div>
<!--end::Main-->
@endsection

@section('script')
<script>
    document.getElementById('ticket_image_ref').addEventListener('change', function(e) {
        const files = e.target.files;
        const errorDiv = document.getElementById('image-error');
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';

        if (files.length > 3) {
            errorDiv.textContent = 'You can upload a maximum of 3 images.';
            errorDiv.style.display = 'block';
            e.target.value = '';
            return;
        }

        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 2 * 1024 * 1024) {
                errorDiv.textContent = 'Each image must be less than 2MB.';
                errorDiv.style.display = 'block';
                e.target.value = '';
                return;
            }
        }
    });
</script>
@endsection