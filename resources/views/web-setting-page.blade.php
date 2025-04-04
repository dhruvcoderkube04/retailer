@extends('layouts.base')
@section('title')
    Web Setting | TrendMart
@endsection
@section('content')
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

                <!--begin::API Overview-->
                <form action="{{route('retailer.web.setting.setup')}}" method="post">
                    @csrf
                    <div class="card mb-5 mb-xxl-10">
                        <!--begin::Header-->
                        <div class="card-header">
                            <!--begin::Title-->
                            <div class="card-title">
                                <h3>Store Management</h3>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-10">
                            <!--begin::Row-->
                            <div class="row mb-10">
                                <!--begin::Col-->
                                <div class="col-md-12 pb-10 pb-lg-0">
                                    <h2>Create Your Store</h2>
                                    <p class="fs-6 fw-semibold text-gray-600 py-2">Set up your online store effortlessly. Customize your website, showcase products, and start selling in no time.</p>

                                    <button type="submit" class="btn btn-success btn-active-light-light" {{!empty($reatiler) ? 'disabled' : '' }}>Create Your Website</button>
                                    @if (!empty($reatiler))
                                        <a href="{{ $reatiler->subdomain }}" target="_blank" class="btn btn-primary btn-active-light-light" >View Your Site</a>
                                    @endif
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                <!--begin::Icon-->
                                <i class="ki-duotone ki-design-1 fs-2tx text-primary me-4"></i>
                                <!--end::Icon-->
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack flex-grow-1">
                                    <!--begin::Content-->
                                    <div class="fw-semibold">
                                        <div class="fs-6 text-gray-700">Create your store easily and start selling online. Set up your website in just a few steps and reach your customers effortlessly.
                                        <a class="fw-bold" href="#">Learn More</a>.</div>
                                    </div>
                                    <!--end::Content-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                </form>
            </div>
        </div>
@endsection
