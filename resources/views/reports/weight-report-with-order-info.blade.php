 <div class="row">
    {{-- {{dd($order)}} --}}
    {{-- Left Column --}}
    <div class="col-md-6">
        {{-- Order Summary --}}
        <div class="card mb-4">
            <div class="card-header px-7" style="min-height: 53px;">
                <h3 class="card-title">Order Summary</h3>
            </div>
            <div class="card-body p-7">
                <p><strong>Order ID:</strong> {{ $order->order_id ?? '-' }}</p>
                <p><strong>Amount:</strong> ₹{{ $order->final_amount ?? '0' }}</p>
                <p><strong>Order Type:</strong> {{ $order->payment_method ?? '-' }}</p>
                <p><strong>AWB Number:</strong> {{ $order?->tracking_number ?? '-' }}</p>
                <p><strong>Quantity:</strong> {{ $order->quantity ?? '0' }}</p>
                <p><strong>Status:</strong>
                    <span class="badge badge-light-{{ order_badge_color(@$order->status) }}">
                        {{ order_status(@$order->status) }}
                    </span>
                </p>
                <p><strong>Order Date:</strong>
                    {{ @$order->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                </p>
            </div>
        </div>

        {{-- Product Details --}}
        <div class="card mb-4">
            <div class="card-header px-7" style="min-height: 53px;">
                <h3 class="card-title">Product Details</h3>
            </div>
            <div class="card-body p-7">
                @php @$product = $order->order_product_detail; @endphp
                <p><strong>Name:</strong> {{ $product?->name ?? 'N/A' }}</p>
                @if (@$order->product_variation)
                    <p><strong>Variation:</strong>
                        <span class="badge badge-light-success">{{ @$order->product_variation }}</span>
                    </p>
                @endif
                <p><strong>SKU:</strong> {{ $product?->sku ?? 'N/A' }}</p>
                <p><strong>Category:</strong> {{ $product?->category?->category_name ?? 'N/A' }}</p>
                @if ($product?->images)
                    @php $image = explode(',', $product->images); @endphp
                    <img src="{{ Storage::disk('spaces')->url($image[0]) }}"
                        class="img-thumbnail mt-3" style="max-width: 150px;"
                        onerror="this.onerror=null;this.src='{{ asset('assets/media/images/no_image.jpg') }}';" />
                @endif
            </div>
        </div>



        {{-- Customer Info --}}
        <div class="card mb-4">
            <div class="card-header px-7" style="min-height: 53px;">
                <h3 class="card-title">Customer Information</h3>
            </div>
            <div class="card-body p-7">
                <p><strong>Name:</strong> {{ @$order->customer?->firstname }} {{ @$order->customer?->lastname }}</p>
                <p><strong>Email:</strong> {{ @$order->customer?->email }}</p>
                <p><strong>Phone:</strong> {{ @$order->customer?->phone_number }}</p>
                <p><strong>Address:</strong> {{ @$order->customer?->address ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-md-6">
        {{-- Courier Charge --}}
        @if (@$order->courier_partner_code && $order->courier_partner_id)
            <div class="card mb-4">
                <div class="card-header px-7" style="min-height: 53px;">
                    <h3 class="card-title">Charge</h3>
                </div>
                <div class="card-body p-7">
                    <p><strong>Courier Partner:</strong> {{ @$order?->courierPartner?->name ?? '-' }}</p>
                    <p><strong>Courier Service:</strong> {{ @$order->courier_service ?? '-' }}</p>
                    @switch($order->status)
                        @case('pickup')
                        @case('in_transit')
                        @case('ofd')
                        @case('delivered')
                            <p><strong> Total Shipping Charges:</strong> ₹{{ ($order->shipping_charge ?? 0) + ($order->cod_charge ?? 0) + ($order->shipping_charge_profit ?? 0 ) + ($order->cod_charge_profit ?? 0)   ?? '-' }} (Margin & GST Include)</p>
                            {{-- <p><strong>Total Margin Charge:</strong> ₹{{ (@$order->shipping_charge_profit ?? 0) + (@$order->cod_charge_profit ?? 0 ) ?? '-' }}</p> --}}
                            <p><strong>Pay To Vendor :</strong> ₹{{ $order->final_amount  -  (($order->shipping_charge ?? 0) + ($order->cod_charge ?? 0) + ($order->shipping_charge_profit ?? 0 ) + ($order->cod_charge_profit ?? 0)) ?? '-' }}</p>
                            @break

                        @case('pending')
                        @case('approved_by_retailer')
                        @case('transfered_retailer_to_wholesaler')
                        @case('approved_by_wholesaler')
                            <p><strong>Shipping Charges:</strong> Not available yet</p>
                            {{-- <p><strong>Total Margin Charge:</strong> Not available yet</p> --}}
                            <p><strong>Pay To Vendor:</strong>  Not available yet</p>
                            @break

                        @case('rto')
                        @case('rtn_to_seller')
                        {{-- @case('ndr') --}}
                        @case('close')
                        @case('cancel')
                        @case('lost')
                            <p><strong>RTO Charges:</strong> ₹{{ ($order->rto_charge ?? 0) + ($order->rto_charge_profit)  ?? '-' }} (Margin & GST Include)</p>
                            {{-- <p><strong>Total Margin Charge:</strong> ₹{{ (@$order->shipping_charge_profit ?? 0) + (@$order->cod_charge_profit ?? 0 ) + (@$order->rto_charge_profit ?? 0 ) ?? '-' }}</p> --}}
                            <p><strong>Collect From Vendor:</strong><span class="badge badge-danger"> - {{ ($order->rto_charge ?? 0) + ($order->rto_charge_profit) ?? '-' }}</span></p>
                            @break

                        @default
                            <p><strong>Shipping Charges:</strong> -</p>
                            {{-- <p><strong>Total Margin Charge:</strong> -</p> --}}
                            <p><strong>Pay To Vendor:</strong> - </p>
                    @endswitch

                </div>
            </div>
        @endif

        @php
            $statusHistories = [];
            if (@$order->created_at) {
                $statusHistories[] = [
                    'status' => 'New',
                    'created_at' => $order->created_at,
                    'badge' => 'primary',
                    'user' => $order->retailer->userDetail->company_name . ' (Retailer)',
                ];
            }
            if ($order->approved_by_retailer_at) {
                $statusHistories[] = [
                    'status' => 'Approved by Retailer',
                    'created_at' => @$order->approved_by_retailer_at,
                    'badge' => 'success',
                    'user' => @$order->retailer->userDetail->company_name . ' (Retailer)',
                ];
            }
            if ($order->transfered_retailer_to_wholesaler_at) {
                $statusHistories[] = [
                    'status' => 'Transfer to Wholesaler',
                    'created_at' => $order->transfered_retailer_to_wholesaler_at,
                    'badge' => 'primary',
                    'user' => $order->retailer->userDetail->company_name . '(Retailer)',
                ];
            }
            if ($order->approved_by_wholesaler_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'Approved by Wholesaler',
                    'created_at' => $order->approved_by_wholesaler_at,
                    'badge' => 'success',
                    'user' => $user,
                ];
            }
            if ($order->pickup_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'Pickup',
                    'created_at' => $order->pickup_at,
                    'badge' => 'info',
                    'user' => $user,
                ];
            }
            if ($order->in_transit_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'In Transit',
                    'created_at' => $order->in_transit_at,
                    'badge' => 'primary',
                    'user' => $user,
                ];
            }
            if ($order->ofd_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'OFD',
                    'created_at' => $order->ofd_at,
                    'badge' => 'warning',
                    'user' => $user,
                ];
            }
            if ($order->delivered_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'Delivered',
                    'created_at' => $order->delivered_at,
                    'badge' => 'success',
                    'user' => $user,
                ];
            }
            if ($order->rto_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'RTO',
                    'created_at' => $order->rto_at,
                    'badge' => 'warning',
                    'user' => $user,
                ];
            }
            if ($order->rtn_to_seller_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'Return to Seller',
                    'created_at' => $order->rtn_to_seller_at,
                    'badge' => 'danger',
                    'user' => $user,
                ];
            }
            if ($order->cancel_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'Cancelled',
                    'created_at' => $order->cancel_at,
                    'badge' => 'danger',
                    'user' => $user,
                    'reason' => $order->cancelled_reason
                ];
            }
            if ($order->lost_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'Lost',
                    'created_at' => $order->lost_at,
                    'badge' => 'danger',
                    'user' => $user,
                ];
            }
            if ($order->received_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'Received',
                    'created_at' => $order->received_at,
                    'badge' => 'success',
                    'user' => $user,
                ];
            }
            if ($order->close_at) {
                if ($order->order_process_by == 'retailer') {
                    $user = $order->retailer->userDetail->company_name . ' (Retailer)';
                }
                if ($order->order_process_by == 'wholesaler') {
                    $user = $order->wholesaler->userDetail->company_name . ' (Wholesaler)';
                }
                $statusHistories[] = [
                    'status' => 'Closed',
                    'created_at' => $order->close_at,
                    'badge' => 'success',
                    'user' => $user,
                ];
            }
        @endphp
        {{-- Timeline --}}
        @if(isset($statusHistories) && count($statusHistories))
            <div class="card mb-5">
                <div class="card-header px-7" style="min-height: 53px;">
                    <h3 class="card-title">Order Status Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach ($statusHistories as $history)
                            <div class="d-flex flex-column align-items-start mb-6 position-relative ps-4 border-start border-2 border-{{ $history['badge'] }}">
                                <span class="badge badge-light-{{ $history['badge'] }} mb-1">
                                    {{ ucfirst($history['status']) }}
                                </span>
                                <p class="mb-1"><strong>Process By:</strong> {{ $history['user'] }}</p>
                                @if (!empty($history['reason']))
                                    <p class="mb-1"><strong>Reason:</strong> {{ $history['reason'] }}</p>
                                @endif
                                <p class="mb-1">{{ \Carbon\Carbon::parse($history['created_at'])->format('d M Y, h:i A') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
