@extends('layouts.base')
@section('title')
    Check Delivery Availability | TechtrendMart
@endsection

@section('content')
    <!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid my-5">
    <div id="kt_app_content_container" class="app-container mx-auto">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between px-9 py-3">
                    <div class="card-title">
                        <h3 class="m-0">Check Delivery Availability</h3>
                    </div>
                </div>

                <div class="card-body">
                    <form id="pincodeCheckForm">
                        <div class="row gy-5 gx-5">
                            <div class="col-md-6">
                                <label class="form-label">Source Pincode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="source_pincode" id="source_pincode" required />
                            </div>
                            @if ($partner->code == 'fship')
                                <div class="col-md-6">
                                    <label class="form-label">Destination Pincode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="destination_pincode" id="destination_pincode"/>
                                </div>
                            @endif
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary">Check Availability</button>
                        </div>
                    </form>

                    <!-- Response Box -->
                    <div id="availabilityResult" class="mt-5" style="display: none;">
                        <div class="card shadow-sm border-0 bg-light">
                            <div class="card-body">
                                <h5 class="fw-bold text-gray-800 mb-3">
                                    <i class="bi bi-check2-circle me-2 fs-4" id="status_icon"></i>
                                    <span id="result_status"></span>
                                </h5>
                                <p class="text-muted" id="result_details"></p>
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
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("pincodeCheckForm");
        const submitButton = form.querySelector('button[type="submit"]'); // <-- Add this!

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            // Disable the submit button
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Checking...';

            const sourcePincode = document.getElementById("source_pincode").value.trim();
            const pincodeRegex = /^[1-9][0-9]{5}$/;

            let destinationInput = document.getElementById("destination_pincode");
            let destinationPincode = destinationInput ? destinationInput.value.trim() : "";

            // If destination input exists and is empty, assign source pincode
            if (destinationInput && destinationPincode === "") {
                destinationPincode = sourcePincode;
                destinationInput.value = sourcePincode;
            }

            // Validate source pincode
            if (!pincodeRegex.test(sourcePincode)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Pincode',
                    text: 'Please enter a valid 6-digit source pincode.',
                });
                submitButton.disabled = false;
                submitButton.innerHTML = 'Check Availability';
                return;
            }

            // Validate destination pincode ONLY if input exists
            if (destinationInput && !pincodeRegex.test(destinationPincode)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Pincode',
                    text: 'Please enter a valid 6-digit destination pincode.',
                });
                submitButton.disabled = false;
                submitButton.innerHTML = 'Check Availability';
                return;
            }

            fetch("{{ url('/check-availability') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    source_pincode: sourcePincode,
                    destination_pincode: destinationPincode
                })
            })
            .then(res => res.json())
            .then(response => {
                const resultBox = document.getElementById("availabilityResult");
                const statusEl = document.getElementById("result_status");
                const detailsEl = document.getElementById("result_details");

                if (!response.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message || 'No courier is available for these pincodes.',
                    });
                    resultBox.style.display = "none";
                    return;
                }

                const data = response.data;
                const courier = response.courier || 'N/A';
                const isAvailable = data.available === true;

                // Set status text and styling
                statusEl.innerText = isAvailable ? "Service Available" : "Service Not Available";
                resultBox.classList.remove("alert-success", "alert-danger");
                resultBox.classList.add(isAvailable ? "alert-success" : "alert-danger");

                // Show normalized courier info
                // <strong>Courier:</strong> ${courier}<br>
                // <strong>State:</strong> ${data.state || 'N/A'}<br>
                detailsEl.innerHTML = `
                    <strong>City:</strong> ${data.city || 'N/A'}<br>
                    <strong>Source:</strong> ${data.source || 'N/A'}<br>
                    <strong>Destination:</strong> ${data.destination || 'N/A'}<br>
                    <strong>Zone:</strong> ${data.zone || 'N/A'}<br>
                    <strong>Pickup:</strong> ${data.pickup || 'No'}<br>
                    <strong>Delivery:</strong> ${data.delivery || 'No'}<br>
                    <strong>COD:</strong> ${data.cod || 'No'}<br>
                    <strong>Message:</strong> ${data.message || 'No message'}
                `;

                resultBox.style.display = "block";
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong while checking pincode serviceability.',
                });
                console.error('Fetch Error:', error);
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Check Availability';
            });
        });
    });
</script>
@endsection
