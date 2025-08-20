@extends('layouts.base')
@section('title')
    Direct Shipping | TechtrendMart
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid my-5">
    <div id="kt_app_content_container" class="app-container mx-auto">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between px-9 py-3">
                        <div class="card-title">
                            <h3 class="page-heading text-gray-900 fw-bold fs-2 my-0">Direct Shipping</h3>
                        </div>
                    </div>
        
                    <div class="card-body">
                        <form id="directShippingForm" class="row" enctype="multipart/form-data">
                            <!-- Product Image -->
                            <div class="col-md-12 mb-5">
                                <label for="product_image" class="d-block mb-2 fs-6">Choose Product Image</label>
                                <input type="file" class="form-control" id="product_image" name="product_image" accept="image/png, image/jpeg, image/jpg" />
                            </div>
        
                            <!-- Product Details -->
                            <div class="col-md-12 mb-5">
                                <label class="form-label fs-6">Product Name</label>
                                <input type="text" class="form-control" name="product_name" id="product_name"
                                       required pattern="^[A-Za-z0-9 ]+$"
                                       title="Only letters, numbers, and spaces allowed." />
                            </div>
                            <div class="col-md-12 mb-5">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label d-flex justify-content-between align-items-center fs-6">
                                            Price (₹)
                                            <span class="fw-bold text-primary">Price per piece</span>
                                        </label>
                                        <input type="number" class="form-control" name="price" id="price"
                                            required pattern="^[1-9][0-9]*$" max="5000" min="1"
                                            title="Only positive whole numbers allowed." />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fs-6">Quantity</label>
                                        <input type="number" class="form-control" name="qty" id="qty" min="1" max="5"
                                            required pattern="^[1-5]$"
                                            title="Only numbers 1 to 5 allowed." />
                                    </div>
                                </div>
                            </div>
        
                            <!-- Total Price -->
                            <div class="col-md-12 mb-5">
                                <label class="form-label fs-6">Total Price</label>
                                <input type="text" class="form-control" id="total_price" disabled placeholder="₹ 0.00" />
                            </div>
        
                            <!-- Category -->
                            <div class="col-md-12 mb-5">
                                <label class="form-label fs-6">Sub Category</label>
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
                            <div class="col-md-12 mb-5">
                                <div class="bg-light border rounded p-3 ">
                                    <h5 class="mb-2"><i class="bi bi-person-fill"></i> Customer Details</h5>
                                    <a href="javascript:void(0)" class="text-primary" onclick="showCustomerModal()">+ Click To Add Customer Details</a>
                                    <div id="selectedCustomer" class="mt-3 text-muted"></div>
                                </div>
                            </div>
        
                            <!-- Buttons -->
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-dark">Cash on delivery</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Modal Header with Close Button -->
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Select or Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body row">
                <!-- Customer List -->
                <div class="col-md-6 border-end" id="customerListWrapper">
                    <!-- Search Input Group -->
                    <div class="input-group mb-2">
                        <input type="text" id="customerSearch" class="form-control" placeholder="Search by name, phone, or email">
                        <button id="customerSearchBtn" class="btn btn-primary" type="button">Search</button>
                    </div>

                    <!-- Customer List Output -->
                    <div id="customerList"></div>
                </div>

                <!-- Add Customer Form -->
                <div class="col-md-6">
                    <h6>Add New Customer</h6>
                    <form id="addCustomerForm">
                        @csrf

                        <div class="mb-2">
                            <input type="text" class="form-control" name="firstname" placeholder="First name">
                            <div class="invalid-feedback error-firstname"></div>
                        </div>

                        <div class="mb-2">
                            <input type="text" class="form-control" name="lastname" placeholder="Last name">
                            <div class="invalid-feedback error-lastname"></div>
                        </div>

                        <div class="mb-2">
                            <input type="email" class="form-control" name="email" placeholder="Email">
                            <div class="invalid-feedback error-email"></div>
                        </div>

                        <div class="mb-2">
                            <input type="text" class="form-control" name="phone_number" placeholder="Mobile number">
                            <div class="invalid-feedback error-phone_number"></div>
                        </div>

                        <div class="mb-2">
                            <input type="text" class="form-control" name="pincode" placeholder="Pincode">
                            <div class="invalid-feedback error-pincode"></div>
                        </div>

                        <div class="mb-2">
                            <input type="text" class="form-control" name="address" placeholder="Address">
                            <div class="invalid-feedback error-address"></div>
                        </div>

                        <div class="mb-2">
                            <input type="text" class="form-control" name="city" placeholder="City">
                            <div class="invalid-feedback error-city"></div>
                        </div>

                        <div class="mb-2">
                            <input type="text" class="form-control" name="state" placeholder="State">
                            <div class="invalid-feedback error-state"></div>
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
    function showCustomerModal() {
        $('#customerModal').modal('show');
        document.getElementById('addCustomerForm').reset();
        document.querySelectorAll('#addCustomerForm .form-control').forEach(input => input.classList.remove('is-invalid'));
        document.querySelectorAll('#addCustomerForm .invalid-feedback').forEach(div => div.textContent = '');
        document.getElementById('customerSearch').value = '';
        document.getElementById('customerList').innerHTML = '';
    }

    document.getElementById('customerSearchBtn').addEventListener('click', function () {
        const query = document.getElementById('customerSearch').value.trim();
        if (query === '') {
            document.getElementById('customerList').innerHTML = '';
            return;
        }
        fetch("{{ route('retailer.getcustomer.data') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ query: query })
        })
        .then(res => res.json())
        .then(customers => renderCustomerList(customers))
        .catch(err => {
            console.error("Search error:", err);
            document.getElementById('customerList').innerHTML = `<p class="text-danger">Search failed.</p>`;
        });
    });

    function renderCustomerList(customers) {
        const container = document.getElementById('customerList');
        let html = '';
        if (customers.length === 0) {
            html = '<p>No matching customers found.</p>';
        } else {
            customers.forEach(customer => {
                html += `
                    <div class="border-bottom p-2 customer-item">
                        <strong>${customer.id} ${customer.firstname} ${customer.lastname}</strong><br/>
                        <small>${customer.phone_number}</small><br/>
                        <button type="button" class="btn btn-sm btn-primary text-white mt-1" onclick='selectCustomer(${JSON.stringify(customer)})'>Select</button>
                    </div>`;
            });
        }
        container.innerHTML = html;
    }

    function selectCustomer(customer) {
        document.getElementById('selectedCustomer').innerHTML =
            `<strong>Selected:</strong> ${customer.id} ${customer.firstname} ${customer.lastname}, ${customer.phone_number}`;
        $('#customerModal').modal('hide');
        window.selectedCustomer = customer;
    }

    document.getElementById('addCustomerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        form.querySelectorAll('.form-control').forEach(input => input.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(div => div.innerText = '');

        fetch("{{ route('retailer.customerdata.store') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                form.reset();
                Swal.fire({
                    icon: 'success',
                    title: 'Customer Added',
                    text: data.message || 'Customer added successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else if (data.errors) {
                for (const field in data.errors) {
                    const input = form.querySelector(`[name="${field}"]`);
                    const errorDiv = form.querySelector(`.error-${field}`);
                    if (input) input.classList.add('is-invalid');
                    if (errorDiv) errorDiv.innerText = data.errors[field][0];
                }
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Something went wrong' });
            }
        })
        .catch(err => {
            console.error("Error adding customer:", err);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong while adding the customer.' });
        });
    });

    document.getElementById("directShippingForm").addEventListener("submit", function (e) {
        e.preventDefault();
        if (!window.selectedCustomer) {
            Swal.fire({ icon: 'warning', title: 'Select Customer', text: 'Please select a customer before placing the order.' });
            return;
        }
        const formData = new FormData(this);
        formData.append('customer_id', window.selectedCustomer.id);

        fetch("{{ route('retailer.directshipping.place.order') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Order Placed', text: data.message });
                this.reset();
                document.getElementById('selectedCustomer').innerHTML = '';
                window.selectedCustomer = null;
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        })
        .catch(err => console.error("Order placement error:", err));
    });

    document.getElementById('price').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        calculateTotal();
    });
    document.getElementById('qty').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        calculateTotal();
    });

    function calculateTotal() {
        const price = parseInt(document.getElementById('price').value.replace(/[^\d]/g, '')) || 0;
        const qty = parseInt(document.getElementById('qty').value.replace(/[^\d]/g, '')) || 0;
        const total = price * qty;
        document.getElementById('total_price').value = total > 0 ? '₹ ' + total.toLocaleString() : '₹ 0.00';
    }

    document.getElementById("directShippingForm").addEventListener("submit", function (e) {
        const name = document.getElementById('product_name').value.trim();
        const price = document.getElementById('price').value.trim();
        const qty = document.getElementById('qty').value.trim();

        const nameRegex = /^[A-Za-z0-9 ]+$/;
        const priceRegex = /^[1-9][0-9]*$/;
        const qtyRegex = /^[1-5]$/;

        if (!nameRegex.test(name)) {
            e.preventDefault();
            Swal.fire('Invalid Product Name', 'Only letters, numbers, and spaces are allowed.', 'warning');
            return;
        }

        if (!priceRegex.test(price)) {
            e.preventDefault();
            Swal.fire('Invalid Price', 'Price must be a positive whole number (no decimals or special characters).', 'warning');
            return;
        }

        if (!qtyRegex.test(qty)) {
            e.preventDefault();
            Swal.fire('Invalid Quantity', 'Quantity must be a number between 1 and 5.', 'warning');
            return;
        }
    });
</script>
@endsection
