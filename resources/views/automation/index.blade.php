@extends('layouts.base')
@section('title')
    Automation | TechtrendMart
@endsection
@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Licenses</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="#" class="text-muted text-hover-primary">Automation</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Broadcast</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">

                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container ">
                    <div class="row">
                        <!--begin::Membership Content-->
                        <div class="col-lg-8">
                            <div class="card">
                                <!--begin::Body-->
                                <div class="card-body p-5 px-lg-19 py-lg-16">
                                    <!--begin::Content main-->
                                    <div class="mb-14">
                                        <!--begin::Heading-->
                                        <div class="mb-15">
                                            <!--begin::Title-->
                                            <h1 class="fs-2x text-gray-900 mb-6">Membership Benefits</h1>
                                            <!--end::Title-->
                                            <!--begin::Text-->
                                            <div class="fs-5 text-gray-600 fw-semibold">
                                                Become a member and enjoy exclusive benefits like lower transaction fees, priority customer support, and more.
                                            </div>
                                            <!--end::Text-->
                                        </div>
                                        <!--end::Heading-->

                                        <!--begin::Table-->
                                        <div class="mb-14">
                                            <div class="table-responsive">
                                                <table class="table table-row-dashed table-row-gray-300 align-middle fs-7">
                                                    <thead>
                                                        <tr class="fw-bold fs-6 text-gray-800 text-center border-0 bg-light">
                                                            <th class="min-w-200px rounded-start"></th>
                                                            <th class="min-w-140px">Basic</th>
                                                            <th class="min-w-120px">Premium</th>
                                                            <th class="min-w-100px rounded-end">Elite</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="border-bottom border-dashed">
                                                        <tr class="fw-semibold fs-6 text-gray-800 text-center">
                                                            <td class="text-start ps-6 fs-4">Priority Support</td>
                                                            <td><i class="ki-duotone ki-0 fs-2x text-danger"></i></td>
                                                            <td><i class="ki-duotone ki-check fs-2x text-success"></i></td>
                                                            <td><i class="ki-duotone ki-check fs-2x text-success"></i></td>
                                                        </tr>
                                                        <tr class="fw-semibold fs-6 text-gray-800 text-center">
                                                            <td class="text-start ps-6 fs-4">Lower Transaction Fees</td>
                                                            <td>2%</td>
                                                            <td>1.5%</td>
                                                            <td>1%</td>
                                                        </tr>
                                                        <tr class="fw-semibold fs-6 text-gray-800 text-center">
                                                            <td class="text-start ps-6 fs-4">Exclusive Discounts</td>
                                                            <td><i class="ki-duotone ki-0 fs-2x text-danger"></i></td>
                                                            <td><i class="ki-duotone ki-check fs-2x text-success"></i></td>
                                                            <td><i class="ki-duotone ki-check fs-2x text-success"></i></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Content main-->
                                </div>
                                <!--end::Body-->
                            </div>
                        </div>
                        <!--end::Membership Content-->

                        <!--begin::Pricing Card-->
                        <div class="col-lg-4">
                            <div class="card bg-primary text-white p-5">
                                <div class="card-body text-center">
                                    <h2 class="fs-2x fw-bold mb-4">Monthly Charges</h2>
                                    <h3 class="fs-1 fw-semibold">Transaction Charge</h3>
                                    <p class="fs-4 mb-2">₹ 249</p>
                                    <p class="fs-6">(GST Inclusive)</p>
                                    <button class="btn btn-light fw-bold mt-4">Activate Membership</button>
                                </div>
                            </div>
                        </div>
                        <!--end::Pricing Card-->
                    </div>
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->

        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->
@endsection

@section('scripts')

@endsection
