@extends('layouts.base')
@section('title')
    Prohibited Items | TechtrendMart
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid my-5">
                <div id="kt_app_content_container" class="app-container mx-auto">
                    <div class="card">
                        <div class="card-body p-10 p-lg-15">
                            <div class="mb-13">
                                <div class="mb-15">
                                    <h4 class="fs-2x text-gray-800 w-bolder mb-6">Prohibited Items</h4>
                                    <p class="fw-semibold fs-4 text-gray-600 mb-2">
                                        Certain items are restricted or prohibited from being sold on e-commerce platforms
                                        due to safety, legal, or regulatory concerns. These include hazardous materials,
                                        weapons, currency, and restricted financial instruments. Sellers must comply with
                                        platform policies and local laws.
                                    </p>
                                </div>

                                <div class="row mb-12">
                                    <div class="col-md-12 pe-md-10 mb-10 mb-md-0">
                                        <div class="container">
                                            @php
                                                $prohibitedItems = [
                                                    [
                                                        'image' => 'radioactive.png',
                                                        'items' => ['Toxic compounds, liquids, or gases'],
                                                    ],
                                                    [
                                                        'image' => 'charge-car-battery.png',
                                                        'items' => ['Automobile batteries', 'Lithium batteries'],
                                                    ],
                                                    ['image' => 'submachine.png', 'items' => ['Arms and ammunition']],
                                                    [
                                                        'image' => 'warning.png',
                                                        'items' => [
                                                            'Magnetized materials',
                                                            'Infectious substances',
                                                            'Bleach',
                                                            'Flammable adhesives',
                                                        ],
                                                    ],
                                                    [
                                                        'image' => 'jewelry.png',
                                                        'items' => ['Precious stones, gems, and jewelry'],
                                                    ],
                                                    [
                                                        'image' => 'check.png',
                                                        'items' => ['Uncrossed (bearer) cheques', 'Currency and coins'],
                                                    ],
                                                    ['image' => 'poisoning.png', 'items' => ['Poison']],
                                                    [
                                                        'image' => 'bomb.png',
                                                        'items' => ['Firearms, explosives, military equipment'],
                                                    ],
                                                    [
                                                        'image' => 'paint-bucket.png',
                                                        'items' => [
                                                            'Oil-based paint',
                                                            'Thinners (flammable liquids)',
                                                            'Industrial solvents',
                                                        ],
                                                    ],
                                                    [
                                                        'image' => 'poisoning.png',
                                                        'items' => [
                                                            'Insecticides',
                                                            'Garden chemicals (fertilizers, poisons)',
                                                        ],
                                                    ],
                                                    [
                                                        'image' => 'cogwheel.png',
                                                        'items' => [
                                                            'Machinery (chainsaws, outboard engines containing fuel)',
                                                        ],
                                                    ],
                                                    [
                                                        'image' => 'stove.png',
                                                        'items' => [
                                                            'Fuel for camp stoves, lanterns, torches, heating elements',
                                                        ],
                                                    ],
                                                ];
                                            @endphp

                                            @foreach ($prohibitedItems as $item)
                                                <div class="row mb-4 align-items-center">
                                                    <div class="col-md-4">
                                                        <img src="{{ asset('assets/media/prohibiteimage/' . $item['image']) }}"
                                                            class="img-fluid rounded" height="100" width="100">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <ul>
                                                            @foreach ($item['items'] as $subItem)
                                                                <li>{{ $subItem }}</li>
                                                            @endforeach
                                                        </ul>
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
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection
