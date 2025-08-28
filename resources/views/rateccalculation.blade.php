@extends('layouts.base')
@section('title', 'Rate Calculator | TechtrendMart')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
   <div class="app-container mt-5">
      <div class="row">
         <div class="col-lg-8">
            <form id="rate_calculator_form" class="form">
               @csrf
               <div class="card card-flush py-4">
                  <div class="card-header">
                     <div class="card-title">
                        <h2>Rate Calculator</h2>
                     </div>
                  </div>
      
                  <div class="card-body py-0">
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
                        <div class="col-md-12">
                           <label class="form-label">Weight (Kg)</label>
                           <input type="number" name="shipment_Weight" class="form-control" step="0.01" required>
                        </div>
                     </div>
      
                     <div class="row mb-5">
                        <label class="form-label">Dimensions (cm)</label>
                        {{-- <div class="d-flex gap-4"> --}}
                           <div class="col-md-4">
                              <input type="number" name="shipment_Length" class="form-control w-100" placeholder="L">
                           </div>
                           <div class="col-md-4">
                              <input type="number" name="shipment_Width" class="form-control w-100" placeholder="B">
                           </div>
                           <div class="col-md-4">
                              <input type="number" name="shipment_Height" class="form-control w-100" placeholder="H">
                           </div>
                        {{-- </div> --}}
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
                submitBtn.disabled = false;
                submitBtn.textContent = 'Calculate';
                resultList.innerHTML = ''; // clear previous results

                // Use correct key: res.data instead of res.shipment_rates
                if (res.data && Array.isArray(res.data) && res.data.length > 0) {
                    resultSection.style.display = 'block';

                    res.data.forEach(rate => {
                        // <h5 class="mb-2">${rate.courier_name}</h5>
                        // <p><strong>RTO Charge:</strong> ₹${rate.rto_charge || 0}</p>
                        // <p><strong>Total Price:</strong> ₹${rate.total_price || 0}</p>
                        resultList.innerHTML += `
                            <div class="col-md-6 mb-3">
                                <div class="card border shadow-sm p-3">
                                    ${rate.logoUrl ? `<img src="${rate.logoUrl}" alt="${rate.courier_name}" class="me-2 mb-2" style="height: 40px;">` : ''}
                                    <p><strong>Service Name:</strong> ${rate.service_name || 'N/A'}</p>
                                    <p><strong>Shipping Charge:</strong> ₹${rate.shipping_charge || 0}</p>
                                    <p><strong>COD Charge:</strong> ₹${rate.cod_charge || 0}</p>
                                    <p><strong>Weight:</strong> ${rate.weight || 'N/A'} kg</p>
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
