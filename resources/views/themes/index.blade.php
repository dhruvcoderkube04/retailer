@extends('layouts.base')
@section('title')
    Themes | TrendMart
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Themes</h1>

                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Themes</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <div class="card mb-2 pb-5 mb-xl-5">
                        <div class="card-body pt-9 pb-0">
                            <div class="row gy-5 align-items-center flex-column flex-md-row">

                                @foreach ($themes as $key => $theme)
                                    <div class="col-md-3 mb-4">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-img-top">
                                                <img src="{{ $theme->theme_image }}" alt="{{ $theme->theme_name }} Theme"
                                                    class="img-fluid rounded-top"
                                                    style="height:180px; object-fit:cover; width:100%;">
                                            </div>
                                            <div class="card-body text-center">
                                                <h5 class="card-title">{{ $theme->theme_name }}</h5>
                                                @if ($webManagement->theme == $theme->id)
                                                    <button class="btn btn-success w-100" disabled>Installed</button>
                                                @else
                                                    <button class="btn btn-primary w-100 active-theme"
                                                        data-id="{{ $theme->id }}">Active</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.active-theme', function() {
                const themeId = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, active it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('retailer.themes.active') }}`,
                            method: 'POST',
                            data: {
                                theme_id: themeId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: 'Activated!',
                                        text: response.msg,
                                        icon: 'success'
                                    }).then(function() {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.msg,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong. Please try again later.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
