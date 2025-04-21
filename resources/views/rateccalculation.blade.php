@extends('layouts.base')
@section('title', 'Rate Calculator | TrendMart')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
   <div class="app-container container-xxl mt-5">

      <form id="rate_calculator_form" class="form">
         @csrf
         <div class="card card-flush py-4">
            <div class="card-header">
               <div class="card-title">
                  <h2>Rate Calculator</h2>
               </div>
            </div>

            <div class="card-body pt-0">
               <div class="row mb-5">
                  <div class="col-md-6">
                     <label class="form-label">Source Pincode</label>
                     <input type="text" name="source_Pincode" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                     <label class="form-label">Destination Pincode</label>
                     <input type="text" name="destination_Pincode" class="form-control" required>
                  </div>
               </div>

               <div class="row mb-5">
                  <div class="col-md-6">
                     <label class="form-label">Payment Mode</label>
                     <select name="payment_Mode" class="form-select">
                        <option value="COD">COD</option>
                        <option value="Prepaid">Prepaid</option>
                     </select>
                  </div>
                  <div class="col-md-6">
                     <label class="form-label">Amount</label>
                     <input type="number" name="amount" class="form-control" required>
                  </div>
               </div>

               <div class="row mb-5">
                  <div class="col-md-6">
                     <label class="form-label">Weight (Kg)</label>
                     <input type="number" name="shipment_Weight" class="form-control" step="0.01" required>
                  </div>
               </div>

               <div class="row mb-5">
                  <label class="form-label">Dimensions (cm)</label>
                  <div class="d-flex gap-2">
                     <input type="number" name="shipment_Length" class="form-control w-25" placeholder="L">
                     <input type="number" name="shipment_Width" class="form-control w-25" placeholder="B">
                     <input type="number" name="shipment_Height" class="form-control w-25" placeholder="H">
                  </div>
               </div>
            </div>

            <div class="d-flex justify-content-end m-3">
               <button type="submit" class="btn btn-primary">Calculate</button>
            </div>
         </div>
      </form>

      <!-- Response -->
      <div id="rateResults" class="mt-5" style="display:none;">
         <h3 class="mb-3">Shipping Options</h3>
         <div id="rateList" class="row g-4"></div>
      </div>
   </div>
</div>
@endsection

@section('script')
<script>
    document.getElementById('rate_calculator_form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // Reset old results
        const resultSection = document.getElementById('rateResults');
        const resultList = document.getElementById('rateList');
        resultSection.style.display = 'none';
        resultList.innerHTML = '';

        // Calculate volumetric weight
        const L = parseFloat(formData.get('shipment_Length') || 0);
        const W = parseFloat(formData.get('shipment_Width') || 0);
        const H = parseFloat(formData.get('shipment_Height') || 0);
        const volWeight = (L * W * H) / 5000;

        formData.append('volumetric_Weight', volWeight.toFixed(2));

        fetch("{{ route('retailer.rate.calculation.post') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status && res.shipment_rates && res.shipment_rates.length > 0) {
                resultSection.style.display = 'block';
                res.shipment_rates.forEach(rate => {
                    resultList.innerHTML += `
                        <div class="col-md-6">
                            <div class="card border shadow p-3">
                                <h5>${rate.courier_name}</h5>
                                <p><strong>Shipping:</strong> ₹${rate.shipping_charge}</p>
                                <p><strong>COD:</strong> ₹${rate.cod_charge}</p>
                                <p><strong>RTO:</strong> ₹${rate.rto_charge}</p>
                                <p><strong>Mode:</strong> ${rate.service_mode}</p>
                            </div>
                        </div>
                    `;
                });
            } else {
                resultList.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger text-center">
                            No record found.
                        </div>
                    </div>
                `;
                resultSection.style.display = 'block';
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong while calculating rates.',
            });
        });
    });
    </script>
@endsection
