@extends('layouts.base')

@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Coupan List</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Coupan</li>
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
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header border-0 pt-6">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-subscription-table-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Coupan" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Toolbar-->
                                <div class="d-flex justify-content-end" data-kt-subscription-table-toolbar="base">
                                    <!--begin::Add subscription-->
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_coupan">
                                            <i class="ki-duotone ki-plus fs-2"></i>
                                            Create Coupan
                                        </button>
                                    <!--end::Add subscription-->
                                </div>
                                <!--end::Toolbar-->
                                <!--begin::Group actions-->
                                <div class="d-flex justify-content-end align-items-center d-none" data-kt-subscription-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                    <span class="me-2" data-kt-subscription-table-select="selected_count"></span>Selected</div>
                                    <button type="button" class="btn btn-danger" data-kt-subscription-table-select="delete_selected">Delete Selected</button>
                                </div>
                                <!--end::Group actions-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_subscriptions_table">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_subscriptions_table .form-check-input" value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-125px">Name</th>
                                        <th class="min-w-125px">Status</th>
                                        <th class="min-w-125px">Coupan Code</th>
                                        <th class="min-w-125px">Disount Price</th>
                                        <th class="min-w-125px">Created Date</th>
                                        <th class="text-end min-w-70px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td>
                                            <a href="#" class="text-gray-800 text-hover-primary mb-1">Emma Smith</a>
                                        </td>
                                        <td>
                                            <div class="badge badge-light-success">Active</div>
                                        </td>
                                        <td>
                                            <div class="badge badge-light">Auto-debit</div>
                                        </td>
                                        <td>Basic</td>
                                        <td>Aug 19, 2024</td>
                                        <td class="text-end">
                                            <button class="btn btn-icon btn-danger btn-light-danger w-30px h-30px me-3 delete-coupan"
                                                data-id=""
                                                data-ws_id=""
                                                data-bs-toggle="tooltip"
                                                title="remove">
                                                <i class="ki-duotone ki-trash">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                </i>
                                            </button>
                                            <button class="btn btn-icon btn-success btn-light-success  w-30px h-30px me-3 edit-coupan"
                                                data-id=""
                                                data-ws_id=""
                                                data-bs-toggle="tooltip"
                                                title="Edit">
                                                <i class="ki-duotone ki-pencil">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                    <div class="modal fade" id="kt_modal_add_coupan" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Create Coupan</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1"></i>
                                    </div>
                                </div>
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <form id="coupanaddform" class="form" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Coupan Name</label>
                                            <input type="text" class="form-control" id="coupan_name" name="coupan_name">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Coupan Code</label>
                                            <input class="form-control" id="coupan_code" name="coupan_code"></input>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Disount Price</label>
                                            <input type="number" class="form-control" id="discount_price" name="discount_price">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity">
                                        </div>

                                        {{-- <div class="mb-3">
                                            <label class="form-label">Select Date Range</label>
                                            <input type="text" class="form-control" id="dateRange" name="date_range" placeholder="Select date range">
                                        </div> --}}

                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="d-flex">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="status" value="1" class="form-check-input" checked> Active
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" name="status" value="0" class="form-check-input"> Inactive
                                                </label>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Add Coupan</button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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


@section('scritp')
    <script>
        $(document).ready(function() {

            $(".delete-coupan").click(function() {
                let productId = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('retailer.clone-product-remove', '') }}/" + productId,
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: "DELETE"
                            },
                            success: function(response) {
                                Swal.fire("Deleted!", "Product has been removed.", "success");
                                location.reload(); // Reload the page or update the table dynamically
                                $("#kt_tab_pane_1").removeClass("active"); // Remove active from all tabs
                                $("#kt_tab_pane_2").addClass("active"); // Add active to Clone tab
                            },
                            error: function(xhr) {
                                Swal.fire("Error!", "Something went wrong. Please try again.", "error");
                            }
                        });
                    }
                });
            });

            $(".edit-coupan").on("click", function () {
                let productId = $(this).data("id");
                let productName = $(this).data("name");
                let description = $(this).data("description");
                let tags = $(this).data("tags");
                let category = $(this).data("category");
                let price = $(this).data("price");
                let images = $(this).data("images");
                let videos = $(this).data("videos");
                let sku = $(this).data("sku");
                let quantity = $(this).data("quantity");

                $("#product_id").val(productId);
                $("#product_name").val(productName);
                $("#description").val(description);
                $("#tags").val(tags);
                $("#categories").val(category);
                $("#price").val(price);
                $("#sku").val(sku);
                $("#quantity").val(quantity);

                // **Clear Previous Preview**
                $("#image-preview").html("");
                $("#video-preview").html("");

                // **Handle Image Preview with Delete Option**
                if (images) {
                    let imageList = images.split(",");
                    let imagePreviewHtml = "";
                    imageList.forEach((img, index) => {
                        let imagePath = `/uploads/products/${img}`;
                        imagePreviewHtml += `
                        <div class="col-4 d-flex flex-column align-items-center">
                            <div class="position-relative">
                                <img src="${imagePath}" class="img-thumbnail m-1" style="width: 120px; height: 120px; object-fit: cover;">

                                <button type="button" class="btn btn-icon btn-danger btn-active-light-danger w-30px h-30px position-absolute top-0 end-0 remove-image" data-image="${img}">
                                    <i class="ki-duotone ki-cross fs-3">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        <span class="path4"></span><span class="path5"></span>
                                    </i>
                                </button>
                            </div>
                        </div>`;
                    });
                    $("#image-preview").html(imagePreviewHtml);
                }

                // **Handle Video Preview**
                if (videos) {
                    let videoPath = `/uploads/videos/${videos}`;
                    let videoPreviewHtml = `
                        <video width="200" controls>
                            <source src="${videoPath}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>`;
                    $("#video-preview").html(videoPreviewHtml);
                }
            });
        });
    </script>
@endsection
