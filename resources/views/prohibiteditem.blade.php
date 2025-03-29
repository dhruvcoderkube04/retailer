@extends('layouts.base')
@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::FAQ card-->
                    <div class="card">
                        <!--begin::Body-->
                        <div class="card-body p-10 p-lg-15">
                            <!--begin::Classic content-->
                            <div class="mb-13">
                                <!--begin::Intro-->
                                <div class="mb-15">
                                    <!--begin::Title-->
                                    <h4 class="fs-2x text-gray-800 w-bolder mb-6">Prohibited Items</h4>
                                    <!--end::Title-->
                                    <!--begin::Text-->
                                    <p class="fw-semibold fs-4 text-gray-600 mb-2">
                                        Certain items are restricted or prohibited from being sold on e-commerce platforms
                                        due to safety, legal, or regulatory concerns. These include hazardous materials
                                        (e.g., toxic substances, batteries, and flammable liquids), weapons, currency, and
                                        restricted financial instruments. Sellers must comply with platform policies and
                                        local laws to ensure safe and legal transactions.</p>
                                    <!--end::Text-->
                                </div>
                                <!--end::Intro-->
                                <!--begin::Row-->
                                <div class="row mb-12">
                                    <!--begin::Col-->
                                    <div class="col-md-12 pe-md-10 mb-10 mb-md-0">
                                        <div class="container">
                                            <!-- Row 1 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/radioactive.png') }}"
                                                        class="img-fluid rounded" alt="Prohibited Item"
                                                         height="100" width="100"
                                                        >
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Any compound, liquid, or gas that has toxic characteristics</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Row 2 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/charge-car-battery.png') }}"
                                                        class="img-fluid rounded" alt="Prohibited Item"
                                                         height="100" width="100"
                                                        >
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Automobile batteries</li>
                                                        <li>Lithium batteries</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/submachine.png') }}"
                                                        class="img-fluid rounded" alt="Prohibited Item"
                                                         height="100" width="100"
                                                        >
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Arms and ammunition</li>
                                                    </ul>
                                                </div>
                                            </div>


                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="#" class="img-fluid rounded" alt="Prohibited Item"
                                                    height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Dry ice (solid carbon dioxide)</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/warning.png') }}"
                                                        class="img-fluid rounded" alt="Prohibited Item" height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Magnetized materials</li>
                                                        <li>Infectious substances</li>
                                                        <li>Bleach</li>
                                                        <li>Flammable adhesives</li>

                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/jewelry.png') }}"
                                                        class="img-fluid rounded" alt="Prohibited Item" height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Precious stones, gems, and jewelry</li>

                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/check.png') }}" class="img-fluid rounded" alt="Prohibited Item"  height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Uncrossed (bearer cheques) drafts/cheques</li>
                                                        <li>Currency and coins</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/poisoning.png') }}"
                                                        class="img-fluid rounded" alt="Prohibited Item"  height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Poison</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/bomb.png') }}"
                                                        class="img-fluid rounded"
                                                        alt="Prohibited Item"  height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Firearms, explosives, and military equipment</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/paint-bucket.png') }}"
                                                        class="img-fluid rounded" alt="Prohibited Item"  height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Oil-based paint</li>
                                                        <li>Thinners (flammable liquids)</li>
                                                        <li>Industrial solvents</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('assets/media/prohibiteimage/poisoning.png') }}"
                                                        class="img-fluid rounded" alt="Prohibited Item"  height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Insecticides</li>
                                                        <li>Garden chemicals (fertilizers, poisons)</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="image3.jpg" class="img-fluid rounded" alt="Prohibited Item"  height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Machinery (chainsaws, outboard engines containing fuel or that
                                                            have contained fuel)</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- Row 3 -->
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-md-4">
                                                    <img src="image3.jpg" class="img-fluid rounded" alt="Prohibited Item"  height="100" width="100">
                                                </div>
                                                <div class="col-md-8">
                                                    <ul>
                                                        <li>Fuel for camp stoves, lanterns, torches, or heating elements
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Row-->
                            </div>
                            <!--end::Classic content-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::FAQ card-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Footer-->

        <!--end::Footer-->
    </div>
    <!--end:::Main-->
@endsection
@section('script')
@endsection
