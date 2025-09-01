@extends('layouts.base')
@section('title')
Track Your Order Stauts | TechtrendMart
@endsection

@section('content')
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid my-5">
    <div id="kt_app_content_container" class="app-container mx-auto">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between px-9 py-3">
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
                                    <div id="track_no_error" class="text-danger mt-2" style="display:none;"></div>
                                </div>
                            </div>

                            <div class="mt-5 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Track</button>
                                <button type="reset" class="btn btn-primary" id="cancelBtn">Discard</button>
                            </div>
                        </form>

                        <div id="availabilityResult" class="alert d-none mt-4">
                            <div id="result_status" class="fw-bold mb-2"></div>
                            <div id="result_details"></div>
                        </div>


                        <div class="shipment-and-tracking mt-4 ">
                            <!-- Shipment Details -->
                            <div class="border border-light-subtle rounded-3 p-4 mb-5" id="shipment-details" style="display: none;">
                                <h4 class="mb-3">Shipment Details</h4>
                                <div class="row mx-0 mb-2">
                                    <div class="col-12">
                                        <h5 class="mb-0">Order Status:
                                            <span id="order-status" class="badge badge-light-success fs-6"></span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="row mx-0 mb-2">
                                    <div class="col-md-3"><strong>Waybill:</strong></div>
                                    <div id="awb" class="col-md-9 text-dark fw-semibold"></div>
                                </div>
                                <div class="row mx-0 mb-2">
                                    <div class="col-md-3"><strong>Pickup Date:</strong></div>
                                    <div id="pickup-date" class="col-md-9 text-dark fw-semibold"></div>
                                </div>
                                <div class="row mx-0 mb-2">
                                    <div class="col-md-3"><strong>From:</strong></div>
                                    <div id="from-address" class="col-md-9 text-dark fw-semibold"></div>
                                </div>
                                <div class="row mx-0 mb-2">
                                    <div class="col-md-3"><strong>To:</strong></div>
                                    <div id="to-pin" class="col-md-9 text-dark fw-semibold"></div>
                                </div>
                                <div class="row mx-0 mb-2">
                                    <div class="col-md-3"><strong>Status:</strong></div>
                                    <div id="status" class="col-md-9 text-dark fw-semibold"></div>
                                </div>
                                <div class="row mx-0 mb-2">
                                    <div class="col-md-3"><strong>Date of Delivery:</strong></div>
                                    <div id="delivery-date" class="col-md-9 text-dark fw-semibold"></div>
                                </div>
                            </div>

                            <!-- Tracking History -->
                            <div class="tracking-history" id="racking-history" style="display: none;">
                                <h4 class="mb-3">Tracking History</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Location</th>
                                                <th>Details</th>
                                                <th>Date</th>
                                                <th>Time <em>*</em></th>
                                            </tr>
                                        </thead>
                                        <tbody id="order-stages-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Check Delivery Availability-->
            </div>
        </div>
    </div>
    <!--end::Content container-->
</div>
<!--end::Content-->
@endsection

@section('script')
<script>
    function formatDateToShortString(dateStr) {
        if (!dateStr) return "N/A";
        const date = new Date(dateStr);
        const options = {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        };
        return date.toLocaleDateString('en-GB', options); // e.g., "23 Aug 2025"
    }

    function formatTime(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleTimeString("en-GB", {
            hour: "2-digit",
            minute: "2-digit",
            hour12: false
        }); // e.g., "15:51"
    }

    function populateOrderStagesTable(orderStages) {
        try {
            const tbody = document.getElementById("order-stages-body");
            tbody.innerHTML = "";

            const seen = new Set();

            const sortedStages = [...orderStages].sort((a, b) => new Date(b.stageDateTime) - new Date(a.stageDateTime));

            sortedStages.forEach((stage) => {
                const location = stage.location || "N/A";
                const details = stage.activity || stage.action || "N/A";
                const date = stage.stageDateTime ? formatDateToShortString(stage.stageDateTime) : "N/A";
                const time = stage.stageDateTime ? formatTime(stage.stageDateTime) : "N/A";

                // Skip "Courier Assigned" and "New" with location "N/A"
                if (location === "N/A" && (details === "Courier Assigned" || details === "New")) {
                    return;
                }

                // Create a unique key for deduplication (based on location + details + date + time)
                const key = `${location}|${details}|${date}|${time}`;
                if (seen.has(key)) return;
                seen.add(key);

                const row = `
                <tr>
                    <td>${location}</td>
                    <td>${details}</td>
                    <td>${date}</td>
                    <td>${time}</td>
                </tr>
            `;
                tbody.insertAdjacentHTML("beforeend", row);
            });
        } catch (error) {
            console.error("Error populating table:", error);
        }
    }


    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("orderTrackForm");
        const submitButton = form.querySelector('button[type="submit"]');

        form.addEventListener("submit", function(e) {
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

                    // Reset previous messages
                    resultBox.className = "alert mt-4";
                    resultBox.classList.remove("alert-success", "alert-danger");
                    resultBox.classList.add("d-none");
                    statusEl.innerText = "";
                    detailsEl.innerHTML = "";

                    // Success format: Check which structure we got
                    const data = response.data;

                    if (data.summary) {
                        // TYPE 1 - summary and trackingdata
                        const summary = data.summary;
                        const trackingData = data.trackingdata || [];

                        statusEl.innerText = `Status: ${summary.status || 'N/A'}`;
                        resultBox.classList.add("alert-success");
                        resultBox.classList.remove("d-none");

                        let trackingHtml = '';
                        trackingData.forEach((track, index) => {
                            trackingHtml += `
                                                <div class="mb-3 p-2 border-bottom">
                                                    <strong>Tracking Entry #${index + 1}</strong><br>
                                                    <strong>Status:</strong> ${track.status || 'N/A'}<br>
                                                    <strong>Remark:</strong> ${track.remark || 'N/A'}<br>
                                                    <strong>Location:</strong> ${track.location || 'N/A'}<br>
                                                    <strong>Date & Time:</strong> ${track.dateandTime || 'N/A'}<br>
                                                </div>`;
                                                        });

                                                        detailsEl.innerHTML = `
                                            <strong>Order ID:</strong> ${summary.orderid || 'N/A'}<br>
                                            <strong>Waybill:</strong> ${summary.waybill || 'N/A'}<br>
                                            <strong>Fulfilled By:</strong> ${summary.fulfilledby || 'N/A'}<br>
                                            <strong>Ordered On:</strong> ${summary.orderedon || 'N/A'}<br>
                                            <strong>Last Scan Date:</strong> ${summary.lastscandate || 'N/A'}<br><br>
                                            <strong>Tracking History:</strong><br>
                                            ${trackingHtml}
                                        `;

                    } else if (data.order) {
                        // TYPE 2 - Lorrigo Format (Order Object)
                        const order = data.order;
                        const latestStage = order.orderStages?.[order.orderStages.length - 1] || {};

                        document.getElementById("shipment-details").style.display = "block";
                        document.getElementById("racking-history").style.display = "block";

                        document.getElementById("order-status").textContent = latestStage.action || "N/A";
                        document.getElementById("awb").textContent = order.awb || "N/A";
                        document.getElementById("pickup-date").textContent = formatDateToShortString(order.pickupDate);
                        document.getElementById("from-address").textContent = order.pickupAddress?.city || "N/A";
                        document.getElementById("to-pin").textContent = latestStage.location || "N/A";
                        document.getElementById("status").textContent = latestStage.activity || "N/A";

                        const deliveredStage = order.orderStages.find(stage => stage.action === "Delivered");
                        if (deliveredStage && deliveredStage.stageDateTime) {
                            document.getElementById("delivery-date").textContent = formatDateToShortString(deliveredStage.stageDateTime);
                        } else {
                            document.getElementById("delivery-date").textContent = "N/A";
                        }

                        populateOrderStagesTable(order.orderStages);

                    } else {
                        // Unknown response
                        resultBox.classList.add("alert-danger");
                        statusEl.innerText = "Tracking failed";
                        detailsEl.innerHTML = "<strong>Error:</strong> Invalid tracking number or no data found.";
                        resultBox.classList.remove("d-none");
                    }

                    submitButton.disabled = false;
                    submitButton.innerHTML = "Track";
                })
                .catch(error => {
                    console.error(error);
                    const resultBox = document.getElementById("availabilityResult");
                    const statusEl = document.getElementById("result_status");
                    const detailsEl = document.getElementById("result_details");

                    resultBox.className = "alert alert-danger mt-4";
                    statusEl.innerText = "Error";
                    detailsEl.innerHTML = `<strong>Error:</strong> Server error or network issue occurred.`;
                    resultBox.classList.remove("d-none");

                    submitButton.disabled = false;
                    submitButton.innerHTML = "Track";
                });

        });
    });

    $("#cancelBtn").on("click", function() {
        // Reset form fields
        $("#orderTrackForm")[0].reset();

        // Clear validation messages
        $("#track_no_error").hide().text("");

        // Hide results box
        $("#availabilityResult").hide();

        // Reload the page (if you want a hard reset)
        location.reload();
    });

    //validation error
    $(document).ready(function() {
        let maxLength = 25;

        // Live validation on typing
        $("#track_no").on("input", function() {
            let track_no = $(this).val().trim();
            let errorBox = $("#track_no_error");

            if (track_no.length > maxLength) {
                errorBox.text("Tracking number cannot exceed " + maxLength + " characters.")
                    .show();
            } else {
                errorBox.hide().text("");
            }
        });

        // On form submit
        $("#orderTrackForm").on("submit", function(e) {
            e.preventDefault();
            let track_no = $("#track_no").val().trim();
            let errorBox = $("#track_no_error");

            if (track_no.length > maxLength) {
                errorBox.text("Tracking number cannot exceed " + maxLength + " characters.")
                    .show();
                return false;
            }

        });
    });
</script>
@endsection