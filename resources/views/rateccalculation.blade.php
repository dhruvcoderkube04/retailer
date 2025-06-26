@extends('layouts.base')
@section('title', 'Rate Calculator | TechtrendMart')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
   <div class="app-container mt-5">

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
                        <option value="0">COD</option>
                        <option value="1">Prepaid</option>
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

        const submitBtn = document.querySelector('#rate_calculator_form button[type="submit"]');

        // Disable button and show loading
        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Calculating...';

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
            let total_rates_list = [];
            submitBtn.disabled = false;
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Calculate';
            // Detect API type and parse accordingly
            // console.log(res.data.rates && Array.isArray(res.data.rates));
            if (res.shipment_rates && Array.isArray(res.shipment_rates)) {
                // Fship API
                total_rates_list = res.shipment_rates.map(rate => ({
                    courier_name: rate.courier_name || '',
                    shipping_charge: rate.shipping_charge || 0,
                    cod_charge: rate.cod_charge || 0,
                    rto_charge: rate.rto_charge || 0,
                    service_mode: rate.service_mode || '',
                    expected_pickup : rate.expectedPickup || ''
                }));

            } else if (res.rates && Array.isArray(res.rates)) {
                // Lorriog API
                total_rates_list = res.rates.map(rate => ({
                    courier_name: rate.name || '',
                    shipping_charge: rate.charge || 0,
                    cod_charge: rate.cod || 0,
                    rto_charge: rate.rtoCharges || 0,
                    service_mode: rate.type || '',
                    expected_pickup : rate.expectedPickup || ''
                }));
            }
            // Only if we have rates, display them
            console.log(total_rates_list.length);
            if (total_rates_list.length > 0) {
                resultSection.style.display = 'block';
                total_rates_list.forEach(rate => {
                    resultList.innerHTML += `
                        <div class="col-md-6 mb-3">
                            <div class="card border shadow-sm p-3">
                                <h5>${rate.courier_name}</h5>
                                <p><strong>Shipping:</strong> ₹${rate.shipping_charge}</p>
                                <p><strong>COD:</strong> ₹${rate.cod_charge}</p>
                                <p><strong>RTO:</strong> ₹${rate.rto_charge}</p>
                                <p><strong>Mode:</strong> ${rate.service_mode}</p>
                                 <p><strong>Expected Pickup :</strong> ${rate.expected_pickup}</p>
                            </div>
                        </div>
                    `;
                });
            } else {
                resultList.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger text-center">
                            No courier services found.
                        </div>
                    </div>
                `;
                resultSection.style.display = 'block';
            }


        })
        .catch(err => {
            console.error(err);
            submitBtn.disabled = false;
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Calculate';
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong while calculating rates.',
            });
        });
    });
</script>
@endsection
