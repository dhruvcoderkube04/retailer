@extends('layouts.base')
@section('title')
    Retailer's Pick Up Address | TrendMart
@endsection
@section('content')
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Payment methods-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header card-header-stretch pb-0">
                    <!--begin::Title-->
                    <div class="card-title">
                        <h3 class="m-0">Add Pick Up Address</h3>
                    </div>
                    <!--end::Title-->


                </div>
                <!--end::Card header-->
                <!--begin::Tab content-->
                <div id="kt_billing_payment_tab_content" class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row gx-9 gy-6">
                        @foreach ($addresses as $address)
                            <div class="col-xl-6" data-kt-billing-element="card">
                                <!--begin::Card-->
                                <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                                    <!--begin::Info-->
                                    <div class="d-flex flex-column py-2">
                                        <!--begin::Owner-->
                                        <div class="d-flex align-items-center fs-4 fw-bold mb-5">{{ $address->first_name }}
                                            {{ $address->last_name }}
                                            {{-- <span class="badge badge-light-success fs-7 ms-2">Primary</span> --}}
                                        </div>
                                        <!--end::Owner-->
                                        <!--begin::Wrapper-->
                                        <div class="d-flex align-items-center">
                                            <!--begin::Icon-->
                                            <img src="assets/media/svg/card-logos/visa.svg" alt="" class="me-4" />
                                            <!--end::Icon-->
                                            <!--begin::Details-->
                                            <div>
                                                <div>
                                                    <div class="fs-4 fw-bold">Mobile: {{ $address->mobile_number }}</div>
                                                    <div class="fs-6 fw-semibold text-gray-500">{{ $address->address }},
                                                    </div>
                                                    <div class="fs-6 fw-semibold text-gray-500">{{ $address->city }},
                                                        {{ $address->state }} - {{ $address->pincode }}</div>
                                                </div>

                                            </div>
                                            <!--end::Details-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Actions-->
                                    {{-- <div class="d-flex align-items-center py-2">
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
                                    </div> --}}
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
                                            <a href="#" class="fw-bold me-1">Shipping Policy</a> before adding
                                            <br />your new shipping address.
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

    <div class="modal fade @if ($errors->any() || session('custom_errors')) show d-block @endif"
     id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
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
                    <form action="{{ url('/pick-address/store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="first_name" class="form-label"><span class="text-danger">*</span>Warehouse Name</label>
                            <input type="text" class="form-control @error('warehouse_name') is-invalid @enderror"
                                name="warehouse_name" value="{{ old('warehouse_name') }}" required>
                            @error('warehouse_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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
                    <h5 class="modal-title" id="editAddressModalLabel">Edit Pick Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="editAddressForm" action="{{ url('/pick-address/update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Hidden Field for Address ID -->
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('warehouse_name') is-invalid @enderror"
                                name="warehouse_name" id="edit_warehouse_name" disabled >
                            @error('warehouse_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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
                            fetch(`/pick-addresses/${addressId}`, {
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
                this.action = "/pick-address/update/" + addressId;
            });
        });

        $(document).ready(function() {
            $('.edit-address').on('click', function() {
                var addressId = $(this).data('id');
                // console.log('addressId' , addressId);
                $.ajax({
                    url: '/pick-address/edit/' +
                    addressId, // Backend route to fetch address details
                    type: 'GET',
                    success: function(response) {
                        $('#edit_id').val(response.warehouse_id);
                        $('#edit_warehouse_name').val(response.warehouse_name);
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
