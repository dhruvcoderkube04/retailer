@extends('layouts.base')
@section('title')
    Retailer's RTO Address | TechtrendMart
@endsection
@section('content')
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container ">
            <!--begin::Payment methods-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header card-header-stretch pb-0">
                    <!--begin::Title-->
                    <div class="card-title">
                        <h3 class="m-0">Add RTO Address</h3>
                    </div>
                    <!--end::Title-->

                </div>
                <!--end::Card header-->
                <!--begin::Tab content-->
                <div id="kt_billing_payment_tab_content" class="card-body">

                    <div class="row gx-9 gy-6">
                        @foreach ($RTOaddresses as $address)
                            <div class="col-xl-6" data-kt-billing-element="card">
                                <!--begin::Card-->
                                <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                                    <!--begin::Info-->
                                    <div class="d-flex flex-column py-2">
                                        <!--begin::Owner-->
                                        <div class="d-flex align-items-center fs-4 fw-bold mb-5">{{ $address->first_name }}
                                            {{ $address->last_name }}
                                            <span class="badge badge-light-success fs-7 ms-2">Primary</span>
                                        </div>
                                        <!--end::Owner-->
                                        <!--begin::Wrapper-->
                                        <div class="d-flex align-items-center">
                                            <!--begin::Icon-->
                                            <img src="assets/media/svg/card-logos/visa.svg" alt="" class="me-4" />
                                            <!--end::Icon-->
                                            <!--begin::Details-->
                                            <div>
                                                <div class="fs-4 fw-bold">Mobile: {{ $address->mobile_number }}</div>
                                                <div class="fs-6 fw-semibold text-gray-500">{{ $address->address }},</div>
                                                <div class="fs-6 fw-semibold text-gray-500">{{ $address->city }},
                                                    {{ $address->state }} - {{ $address->pincode }}</div>
                                            </div>

                                            <!--end::Details-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Actions-->
                                    <div class="d-flex align-items-center py-2">
                                        <button class="btn btn-sm btn-light btn-active-light-primary me-3 delete-address"
                                            data-id="{{ $address->id }}">
                                            <span class="indicator-label">Delete</span>
                                            <span class="indicator-progress">Please wait...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>

                                        <button class="btn btn-sm btn-light btn-active-light-primary edit-address"
                                            data-id="{{ $address->id }}" data-bs-toggle="modal"
                                            data-bs-target="#editAddressModal">
                                            Edit
                                        </button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Card-->
                            </div>
                        @endforeach

                        <div class="col-xl-6">
                            <!--begin::Notice-->
                            <div
                                class="notice d-flex bg-light-primary rounded border-primary border border-dashed h-lg-100 p-6">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                    <!--begin::Content-->
                                    <div class="mb-3 mb-md-0 fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Important Note!</h4>
                                        <div class="fs-6 text-gray-700 pe-7">
                                            Please carefully read
                                            <a href="#" class="fw-bold me-1">RTO Address Guidelines</a> before adding
                                            <br />your return-to-origin (RTO) address.
                                        </div>
                                    </div>
                                    <!--end::Content-->
                                    <!--begin::Action-->
                                    {{-- <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_new_card">Add Card</a> --}}
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addAddressModal">
                                        + Add
                                    </button>
                                    <!--end::Action-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Notice-->
                        </div>
                    </div>


                </div>
                <!--end::Row-->
            </div>
            <!--end::Tab content-->
        </div>
        <!--end::Payment methods-->
    </div>
    <!--end::Content container-->
    </div>
    <!--end::Content-->

    <div class="modal fade @if ($errors->any()) show d-block @endif" id="addAddressModal" tabindex="-1"
        aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAddressModalLabel">Add Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Validation Error Display -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Address Form -->
                    <form action="{{ url('/rto-address/store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="first_name" class="form-label"><span class="text-danger">*</span>First Name</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label"><span class="text-danger">*</span>Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="mobile" class="form-label"><span class="text-danger">*</span>Mobile Number</label>
                            <input type="text" class="form-control @error('mobile') is-invalid @enderror" name="mobile"
                                value="{{ old('mobile') }}" required>
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pincode" class="form-label"><span class="text-danger">*</span>Pincode</label>
                            <input type="text" class="form-control @error('pincode') is-invalid @enderror" name="pincode"
                                value="{{ old('pincode') }}" required>
                            @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label"><span class="text-danger">*</span>Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="2" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="state" class="form-label"><span class="text-danger">*</span>State</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror"
                                    name="state" value="{{ old('state') }}" required>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label"><span class="text-danger">*</span>City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    name="city" value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer mt-3">
                            <button type="submit" class="btn btn-success">Add</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAddressModalLabel">Edit RTO Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="editAddressForm" action="{{ url('/rto-address/update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Hidden Field for Address ID -->
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name" id="edit_first_name" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name" id="edit_last_name" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                name="mobile_number" id="edit_mobile" required>
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pincode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pincode') is-invalid @enderror"
                                name="pincode" id="edit_pincode" required>
                            @error('pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="edit_address"
                                rows="2" required></textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror"
                                    name="state" id="edit_state" required>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    name="city" id="edit_city" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="modal-footer mt-3">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize Form Validation
            $("#productUploadForm").submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                let stockfile = $("input[name='product_file']")[0].files[0];
                let categoryId = $("select[name='categories']").val(); // Correct selector
                let submitButton = $(this).find("button[type='submit']");

                if (!stockfile) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select an Excel (.xlsx) file!'
                    });
                    return;
                }

                if (stockfile.type !==
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type!',
                        text: 'Only .xlsx files are allowed.'
                    });
                    return;
                }

                formData.append("categories", categoryId); // Append category to formdata.

                submitButton.prop("disabled", true);
                submitButton.find(".indicator-label").hide();
                submitButton.find(".indicator-progress").show();

                $.ajax({
                    url: "{{ url('upload-bulk-product') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(mydata) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Product Import Successful!'
                        });
                        $("#kt_ecommerce_products_table").load(location.href +
                            " #kt_ecommerce_products_table");
                        $("#kt_modal_add_product").modal('hide');
                    },
                    // error: function(mydata) {
                    //     Swal.fire({ icon: 'error', title: 'Product Import Failed!' });
                    // }

                    error: function(mydata) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Product Import Failed!'
                        });
                        let errorMessage = "Product Import Failed!";

                        if (mydata.responseJSON && mydata.responseJSON.error) {
                            errorMessage = mydata.responseJSON
                            .error; // Show backend error message
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        // Enable submit button and reset loading indicator
                        submitButton.prop("disabled", false);
                        submitButton.find(".indicator-label").show();
                        submitButton.find(".indicator-progress").hide();
                    }
                });
            });

            $(".delete-product").click(function() {
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
                            url: "{{ route('retailer.clone-product-remove', '') }}/" +
                                productId,
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: "DELETE"
                            },
                            success: function(response) {
                                Swal.fire("Deleted!", "Product has been removed.",
                                    "success");
                                location
                            .reload(); // Reload the page or update the table dynamically
                                $("#kt_tab_pane_1").removeClass(
                                "active"); // Remove active from all tabs
                                $("#kt_tab_pane_2").addClass(
                                "active"); // Add active to Clone tab
                            },
                            error: function(xhr) {
                                Swal.fire("Error!",
                                    "Something went wrong. Please try again.",
                                    "error");
                            }
                        });
                    }
                });
            });

            $(".edit-product").on("click", function() {
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

            // **Remove Image from Preview**
            $(document).on("click", ".remove-image", function() {
                let imageToRemove = $(this).data("image");
                $(this).parent().remove();

                // Remove the image from hidden input field
                let remainingImages = [];
                $("#image-preview .image-container").each(function() {
                    remainingImages.push($(this).data("image"));
                });
                $("#product_id").data("images", remainingImages.join(",")); // Update the stored images
            });

            // **Validate Image Upload Limit**
            $("#image").on("change", function() {
                let existingImagesCount = $("#image-preview .image-container").length;
                let newImagesCount = this.files.length;
                if (existingImagesCount + newImagesCount > 3) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'You can upload a maximum of 3 images!',
                    });
                    this.value = "";
                }
            });

            // **Validate Video Upload Limit**
            $("#video").on("change", function() {
                if (this.files.length > 1) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Only 1 video is allowed!',
                    });
                    this.value = "";
                }
            });

            // **Submit Form with AJAX**
            $("#updateProductForm").on("submit", function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                // Append remaining images to formData
                let remainingImages = [];
                $("#image-preview .image-container").each(function() {
                    remainingImages.push($(this).data("image"));
                });
                formData.append("remaining_images", remainingImages.join(","));

                $.ajax({
                    url: "/retailer-update-product",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Product updated successfully!'
                        });
                        $("#kt_modal_update_permission").modal("hide");
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'ProSomething went wrong!'
                        });
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.delete-address').forEach(button => {
                button.addEventListener('click', function() {
                    let addressId = this.getAttribute('data-id');

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
                            fetch(`/rto-addresses/${addressId}`, {
                                    method: "DELETE",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            "content"),
                                        "Content-Type": "application/json"
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire("Deleted!",
                                            "The address has been deleted.",
                                            "success");
                                        location
                                    .reload(); // Reload page after successful deletion
                                    } else {
                                        Swal.fire("Error!",
                                            "There was a problem deleting the address.",
                                            "error");
                                    }
                                })
                                .catch(error => Swal.fire("Error!", "Something went wrong.",
                                    "error"));
                        }
                    });
                });
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("editAddressForm").addEventListener("submit", function(event) {
                let addressId = document.getElementById("edit_id").value;
                this.action = "/rto-address/update/" + addressId;
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.edit-address').on('click', function() {
                var addressId = $(this).data('id');
                // console.log('addressId' , addressId);
                $.ajax({
                    url: '/rto-address/edit/' + addressId, // Backend route to fetch address details
                    type: 'GET',
                    success: function(response) {
                        $('#edit_id').val(response.id);
                        $('#edit_first_name').val(response.first_name);
                        $('#edit_last_name').val(response.last_name);
                        $('#edit_mobile').val(response.mobile_number);
                        $('#edit_pincode').val(response.pincode);
                        $('#edit_address').val(response.address);
                        $('#edit_state').val(response.state);
                        $('#edit_city').val(response.city);

                        $('#editAddressModal').modal('show');
                    }
                });
            });
        });
    </script>
@endsection
