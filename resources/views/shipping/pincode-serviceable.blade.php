@extends('layouts.base')
@section('title')
    Check Delivery Availability  | TrendMart
@endsection

@section('content')
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::Check Delivery Availability-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header card-header-stretch pb-0">
                    <div class="card-title">
                        <h3 class="m-0">Check Delivery Availability</h3>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body">
                    <form id="pincodeCheckForm">
                        <div class="row gy-5 gx-5">
                            <div class="col-md-6">
                                <label class="form-label">Source Pincode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="source_pincode" id="source_pincode" required />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Destination Pincode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="destination_pincode" id="destination_pincode" required />
                            </div>
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
                <!--end::Card body-->
            </div>
            <!--end::Check Delivery Availability-->

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("pincodeCheckForm");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const sourcePincode = document.getElementById("source_pincode").value.trim();
            const destinationPincode = document.getElementById("destination_pincode").value.trim();
            const pincodeRegex = /^[1-9][0-9]{5}$/; // Valid Indian pincode

            if (!pincodeRegex.test(sourcePincode) || !pincodeRegex.test(destinationPincode)) {
                alert("Please enter valid 6-digit Indian pincodes.");
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

                const data = response.data; // Adjusted: accessing nested `data` object

                statusEl.innerText = data.status ? "Service Available" : "Service Not Available";
                resultBox.classList.remove("alert-success", "alert-danger");
                resultBox.classList.add(data.status ? "alert-success" : "alert-danger");

                detailsEl.innerHTML = `
                    <strong>Source:</strong> ${data.source || 'N/A'}<br>
                    <strong>Destination:</strong> ${data.destination || 'N/A'}<br>
                    <strong>Zone:</strong> ${data.zone || 'N/A'}<br>
                    <strong>Pickup:</strong> ${data.pickup || 'No'}<br>
                    <strong>Delivery:</strong> ${data.delivery || 'No'}<br>
                    <strong>COD:</strong> ${data.cod || 'No'}<br>
                    <strong>Message:</strong> ${data.response || 'No message'}
                `;

                resultBox.style.display = "block";
                })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong while check pincode serviceable.',
                });
            });
        });
    });
</script>
@endsection
