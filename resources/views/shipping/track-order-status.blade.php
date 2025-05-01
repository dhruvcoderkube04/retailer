@extends('layouts.base')
@section('title')
     Track Your Order Stauts | TrendMart
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
                        <h3 class="m-0">Track Your Order Status</h3>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Card body-->
                <div class="card-body">
                    <form id="orderTrackForm">
                        <div class="row gy-5 gx-5">
                            <div class="col-md-6">
                                <label class="form-label">Tracking No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="track_no" id="track_no" required />
                            </div>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary">Track</button>
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
        const form = document.getElementById("orderTrackForm");
        const submitButton = form.querySelector('button[type="submit"]');

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            // Disable button and show loading text
            // First, get the submit button
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Tracking ...
            `;

            const track_no = document.getElementById("track_no").value.trim();
            const track_noRegex = /^[1-9][0-9]{5}$/; // Valid Indian pincode

            // if (!track_noRegex.test(track_no)) {
            //     trackInput.classList.add("is-invalid");
            //     document.getElementById("track_no_error").innerText = "Please enter a valid 6-digit Indian pincode.";
            //     return;
            // } else {
            //     trackInput.classList.remove("is-invalid");
            //     document.getElementById("track_no_error").innerText = "";
            // }


            fetch("{{ url('/track-order') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    track_no: track_no
                })
            })
            .then(res => res.json())
            .then(response => {
                const resultBox = document.getElementById("availabilityResult");
                const statusEl = document.getElementById("result_status");
                const detailsEl = document.getElementById("result_details");

                const data = response.data;

                if (data.summary) {
                    // TYPE 1: Old Format (Summary + TrackingData)
                    const summary = data.summary || {};
                    const tracking = data.trackingdata && data.trackingdata.length > 0 ? data.trackingdata[0] : {};

                    statusEl.innerText = `Status: ${summary.status || 'N/A'}`;
                    resultBox.classList.remove("alert-danger");
                    resultBox.classList.add("alert-success");

                    detailsEl.innerHTML = `
                        <strong>Order ID:</strong> ${summary.orderid || 'N/A'}<br>
                        <strong>Waybill:</strong> ${summary.waybill || 'N/A'}<br>
                        <strong>Fulfilled By:</strong> ${summary.fulfilledby || 'N/A'}<br>
                        <strong>Ordered On:</strong> ${summary.orderedon || 'N/A'}<br>
                        <strong>Last Scan Date:</strong> ${summary.lastscandate || 'N/A'}<br><br>

                        <strong>Latest Tracking:</strong><br>
                        <strong>Status:</strong> ${tracking.status || 'N/A'}<br>
                        <strong>Remark:</strong> ${tracking.remark || 'N/A'}<br>
                        <strong>Location:</strong> ${tracking.location || 'N/A'}<br>
                        <strong>Date & Time:</strong> ${tracking.dateandTime || 'N/A'}<br>
                    `;
                } else if (data.order) {
                    // TYPE 2: Lorrigo Format (Order Object)

                    const order = data.order;
                    const latestStage = order.orderStages && order.orderStages.length > 0 ? order.orderStages[order.orderStages.length - 1] : {};

                    statusEl.innerText = `Order Status: ${latestStage.action || 'N/A'}`;
                    resultBox.classList.remove("alert-danger");
                    resultBox.classList.add("alert-info");

                    detailsEl.innerHTML = `
                        <strong>Order Reference ID:</strong> ${order.client_order_reference_id || 'N/A'}<br>
                        <strong>AWB:</strong> ${order.awb || 'N/A'}<br>
                        <strong>Product Name:</strong> ${order.productId?.name || 'N/A'}<br>
                        <strong>Customer Name:</strong> ${order.customerDetails?.name || 'N/A'}<br>
                        <strong>Customer Phone:</strong> ${order.customerDetails?.phone || 'N/A'}<br>
                        <strong>Pickup City:</strong> ${order.pickupAddress?.city || 'N/A'}<br><br>

                        <strong>Latest Stage:</strong><br>
                        <strong>Action:</strong> ${latestStage.action || 'N/A'}<br>
                        <strong>Date & Time:</strong> ${latestStage.stageDateTime || 'N/A'}<br>
                    `;
                } else {
                    // Unknown structure
                    statusEl.innerText = "Error";
                    resultBox.classList.remove("alert-success");
                    resultBox.classList.add("alert-danger");

                    detailsEl.innerHTML = `<strong>Error:</strong> Tracking order not Found.`;
                }

                resultBox.style.display = "block";

                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = 'Track';
            })

            .catch(error => {
                console.error(error);
                const resultBox = document.getElementById("availabilityResult");
                const statusEl = document.getElementById("result_status");
                const detailsEl = document.getElementById("result_details");

                statusEl.innerText = "Error";
                resultBox.classList.remove("alert-success");
                resultBox.classList.add("alert-danger");
                detailsEl.innerHTML = `<strong>Error:</strong> Unable to fetch tracking details.`;

                submitButton.disabled = false;
                submitButton.innerHTML = 'Track';

                resultBox.style.display = "block";
            });

        });
    });
</script>
@endsection
