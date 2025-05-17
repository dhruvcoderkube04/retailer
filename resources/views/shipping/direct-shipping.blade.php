@extends('layouts.base')
@section('title')
    Direct Shipping | TrendMart
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card mb-5 mb-xl-10">
            <div class="card-header pb-0">
                <div class="card-title">
                    <h3 class="m-0">Direct Shipping</h3>
                </div>
            </div>

            <div class="card-body">
                <form id="directShippingForm" enctype="multipart/form-data">
                    <!-- Product Image -->
                    <div class="col-md-4 mb-4">
                        <label for="product_image" class="d-block mb-2">Choose Product Image</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" />
                    </div>

                    <!-- Product Details -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="product_name" required />
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" class="form-control" name="price" step="0.01" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="qty" min="1" />
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Sub Category</label>
                        <select
                            class="form-select mb-2 @error('sub_category_id') is-invalid @enderror"
                            data-control="select2" name="sub_category_id"
                            data-placeholder="Select an option" id="subCategory">
                            <option></option>
                            @foreach ($sub_category_list ?? [] as $sub_category)
                                <option data-category-id="{{ $sub_category->category_id }}"
                                    value="{{ $sub_category->id }}"
                                    {{ old('sub_category_id') == $sub_category->id ? 'selected' : '' }}>
                                    {{ Str::upper($sub_category->sub_category_name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('sub_category_id')
                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                        @enderror
                        <small class="text-warning d-block mt-1">Note: It’s optional to select a category in direct shipping.</small>
                    </div>

                    <!-- Customer Section -->
                    <div class="mb-4 p-3 bg-light border rounded">
                        <h5 class="mb-2"><i class="bi bi-person-fill"></i> Customer Details</h5>
                        <a href="javascript:void(0)" class="text-primary" onclick="showCustomerModal()">+ Click To Add Customer Details</a>
                        <div id="selectedCustomer" class="mt-3 text-muted"></div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-3">
                        <button type="button" class="btn btn-warning">Self paid order</button>
                        <button type="submit" class="btn btn-dark">Cash on delivery</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select or Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <!-- Customer List -->
                <div class="col-md-6 border-end" id="customerList">
                    <p>Loading customers...</p>
                </div>

                <!-- Add Customer Form -->
                <div class="col-md-6">
                    <h6>Add New Customer</h6>
                    <form id="addCustomerForm">
                        @csrf
                        <div class="mb-2">
                            <input type="text" class="form-control" name="firstname" placeholder="First name" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="lastname" placeholder="Last name" required>
                        </div>
                        <div class="mb-2">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="phone_number" placeholder="Mobile number" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="pincode" placeholder="Pincode">
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="address" placeholder="Address">
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="city" placeholder="City">
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="state" placeholder="State">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Add Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Show modal and fetch customer records
    function showCustomerModal() {
        $('#customerModal').modal('show');
        fetchCustomers();
    }

    // Fetch customers from server
    function fetchCustomers() {
        fetch("{{ route('retailer.getcustomer.data') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.text()) // get raw response
        .then(raw => {
            console.log("RAW RESPONSE:", raw); // for debugging
            try {
                const data = JSON.parse(raw); // try parsing
                renderCustomerList(data); // render list
            } catch (error) {
                console.error("JSON Parse Error:", error);
            document.getElementById('customerList').innerHTML = `<p class="text-danger">Failed to load customers.</p>`;
            }
        })
        .catch(err => {
            console.error("Fetch error:", err);
            document.getElementById('customerList').innerHTML = `<p class="text-danger">Error fetching customers.</p>`;
        });
    }

    // Render customer list
    function renderCustomerList(customers) {
        let html = '';
        if (customers.length === 0) {
            html = '<p>No customers found.</p>';
        } else {
            customers.forEach(customer => {
                html += `
                    <div class="border-bottom p-2">
                        <strong>${customer.id} ${customer.firstname} ${customer.lastname}</strong><br/>
                        <small>${customer.phone_number}</small><br/>
                        <button type="button" class="btn btn-sm btn-primary text-white mt-1" onclick='selectCustomer(${JSON.stringify(customer)})'>Select</button>
                    </div>`;
            });
        }
        document.getElementById('customerList').innerHTML = html;
    }

    // When customer is selected
    function selectCustomer(customer) {
        document.getElementById('selectedCustomer').innerHTML =
            `<strong>Selected:</strong> ${customer.id} ${customer.firstname} ${customer.lastname}, ${customer.phone_number}`;
        $('#customerModal').modal('hide');

        // OPTIONAL: Store selected customer for use later
        window.selectedCustomer = customer;
    }

    // Add new customer form submit
    document.getElementById('addCustomerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("{{ route('retailer.customerdata.store') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.reset();
                fetchCustomers();
                Swal.fire({
                    icon: 'success',
                    title: 'Customer Added',
                    text: data.message || 'Customer added successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Something went wrong'
                });
            }
        })
        .catch(err => {
            console.error("Error adding customer:", err);
        });
    });

    // Handle shipping form submit
    document.getElementById("directShippingForm").addEventListener("submit", function (e) {
        e.preventDefault();

        if (!window.selectedCustomer) {
            Swal.fire({
                icon: 'warning',
                title: 'Select Customer',
                text: 'Please select a customer before placing the order.',
            });
            return;
        }

        const formData = new FormData(this);
        formData.append('customer_id', window.selectedCustomer.id);

        fetch("{{ route('retailer.directshipping.place.order') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Placed',
                    text: data.message,
                });
                this.reset();
                document.getElementById('selectedCustomer').innerHTML = '';
                window.selectedCustomer = null;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }
        })
        .catch(err => {
            console.error("Order placement error:", err);
        });
    });

</script>
@endsection

