@extends('layouts.base')
@section('title')
    Retailer Category List | TrendMart
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
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            Category List</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Category</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Category List </li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container ">
                    <div class="card border-1">
                        <div class="card-body p-lg-17">
                            <div class="d-flex flex-column flex-lg-row mb-17">

                                <div class="flex-lg-row-fluid me-0 me-lg-20">
                                    <div class="mb-17" id="reload-categorylist">
                                        <div class="m-0">
                                            <h2 class="fs-3 text-dark text-center fw-bold mb-8">Choose Your Category List
                                            </h2>
                                        </div>

                                        @foreach ($categories as $a => $category)
                                            <div class="m-0">
                                                <div class="d-flex align-items-center collapsible py-3 toggle mb-0"
                                                    data-bs-toggle="collapse" data-bs-target="#category{{ $a }}">
                                                    <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5">
                                                        <i class="ki-duotone ki-minus-square toggle-on text-primary fs-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <i class="ki-duotone ki-plus-square toggle-off fs-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                    </div>
                                                    <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">
                                                        {{ strtoupper($category->category_name) }}
                                                    </h4>
                                                </div>

                                                <div id="category{{ $a }}" class="collapse show fs-6 ms-1">
                                                    @foreach ($category->subCategory as $b => $sub_category)
                                                        <div class="mb-4">
                                                            {{-- <div class="d-flex align-items-center ps-10 mb-n1">
                                                                <span class="bullet me-3"></span>
                                                                <div class="text-gray-600 fw-semibold fs-6">
                                                                    {{ strtoupper($sub_category->sub_category_name) }}
                                                                    <a href="javascript:void(0)" class="select-sub-category"
                                                                        data-sub-category-id={{ $sub_category->id }}
                                                                        data-category-id={{ $sub_category->category_id }}
                                                                        data-type="select">

                                                                        @if (!in_array($sub_category->id, $addedCategories ?? []))
                                                                            <span class="badge badge-primary">
                                                                                Select
                                                                            </span>
                                                                        @endif
                                                                    </a>
                                                                </div>
                                                            </div> --}}

                                                            <div
                                                                class="form-check form-check-custom form-check-solid d-flex align-items-center ps-10">
                                                                <input class="form-check-input me-3 sub-category-checkbox"
                                                                    type="checkbox"
                                                                    id="sub_category_{{ $sub_category->id }}"
                                                                    name="sub_categories[]" value="{{ $sub_category->id }}"
                                                                    {{ in_array($sub_category->id, $addedCategories ?? []) ? 'checked' : '' }}
                                                                    data-sub-category-id="{{ $sub_category->id }}"
                                                                    data-category-id="{{ $sub_category->category_id }}"
                                                                    data-type="{{ in_array($sub_category->id, $addedCategories ?? []) ? 'remove' : 'select' }}"
                                                                    style="width: 17px; height: 17px;" />

                                                                <label
                                                                    class="form-check-label text-gray-700 fw-semibold fs-6 mb-0"
                                                                    for="sub_category_{{ $sub_category->id }}">
                                                                    {{ strtoupper($sub_category->sub_category_name) }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="separator separator-dashed"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex-lg-row-auto w-100 w-lg-375px w-xxl-450px">
                                    <div class="card bg-light shadow-sm border-3">
                                        <div class="card-body p-4" id="reload-selected-categorylist">
                                            <div class="mb-10 mt-2 text-center">
                                                <h2 class="fs-3 text-dark fw-bold">Selected Category List</h2>
                                            </div>

                                            @foreach ($retailerCateogries as $category)
                                                <div
                                                    class="d-flex justify-content-between align-items-center p-3 bg-white shadow-sm rounded mb-3">
                                                    <div>
                                                        <h5 class="text-dark fw-semibold mb-0">
                                                            {{ $category->subCategory->sub_category_name ?? '' }}
                                                            <span
                                                                class="text-muted">({{ $category->category->category_name }})</span>
                                                        </h5>
                                                    </div>
                                                    <button
                                                        class="btn btn-sm btn-danger d-flex align-items-center remove-sub-category"
                                                        data-sub-category-id={{ $category->sub_category_id }}
                                                        data-category-id={{ $category->category_id }} data-type="remove">
                                                        <i class="bi bi-x-circle me-1"></i> Remove
                                                    </button>
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
    <script>
        $(document).ready(function() {
            $(document).on('click', '.remove-sub-category', function() {
                const checkbox = $(this);
                const subCategoryId = $(this).attr('data-sub-category-id');
                const categoryId = $(this).attr('data-category-id');
                const actionType = $(this).attr('data-type');

                if (actionType == 'remove') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Are You sure to Remove it !',
                        showCancelButton: true,
                        confirmButtonColor: '#000',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sendAjaxRequest(subCategoryId, categoryId, actionType, checkbox);
                        }
                    });
                } else {
                    sendAjaxRequest(subCategoryId, categoryId, actionType, checkbox);
                }
            });

            $(document).on('change', '.sub-category-checkbox', function() {
                const checkbox = $(this);
                const subCategoryId = $(this).attr('data-sub-category-id');
                const categoryId = $(this).attr('data-category-id');
                const actionType = $(this).attr('data-type');

                if (actionType == 'remove') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Are You sure to Remove it !',
                        showCancelButton: true,
                        confirmButtonColor: '#000',
                        confirmButtonText: 'Yes, remove it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sendAjaxRequest(subCategoryId, categoryId, actionType, checkbox);
                        } else {
                            $(this).prop('checked', true);
                        }
                    });
                } else {
                    sendAjaxRequest(subCategoryId, categoryId, actionType, checkbox);
                }
            })

            function sendAjaxRequest(subCategoryId, categoryId, actionType, checkbox) {
                $('.sub-category-checkbox, .remove-sub-category').prop('disabled', true);

                $.ajax({
                    url: '{{ route('retailer.category.add-retailer-category') }}',
                    type: 'POST',
                    data: {
                        sub_category_id: subCategoryId,
                        category_id: categoryId,
                        actionType: actionType,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#reload-categorylist').html(response.html_1);
                            $('#reload-selected-categorylist').html(response.html_2);

                            Swal.fire({
                                title: 'Success!',
                                text: response.msg,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            checkbox.prop('checked', true);
                            Swal.fire({
                                title: 'Error!',
                                text: response.msg,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        checkbox.prop('checked', true);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        // Always re-enable controls after AJAX finishes
                        $('.sub-category-checkbox, .remove-sub-category').prop('disabled', false);
                    }
                });
            }
        });
    </script>
@endsection
