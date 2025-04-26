@php
    if ($transactionDetail->final_transaction_amount > 0) {
        $final_amount_text_css = 'text-success';
    } elseif ($transactionDetail->final_transaction_amount <= 0) {
        $final_amount_text_css = 'text-danger';
    }

    if ($transactionDetail->transaction_amount > 0) {
        $amount_text_css = 'text-success';
    } elseif ($transactionDetail->transaction_amount <= 0) {
        $amount_text_css = 'text-danger';
    }
@endphp

<div class="d-flex flex-column gap-2 fs-6 fw-semibold text-gray-700">

    <div class="d-flex justify-content-between py-1 border-bottom">
        <span>Product Name :</span>
        <span>{{ $transactionDetail->customer_order?->product?->name ? $transactionDetail->customer_order?->product?->name : $transactionDetail->customer_order?->retailerCloneProduct?->name }}</span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom">
        <span>Tracking ID :</span>
        <span>N/A</span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom">
        <span>Remark :</span>
        <span>{{ $transactionDetail->description ?? 'N/A' }}</span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom">
        <span>Date :</span>
        <span>{{ \Carbon\Carbon::parse($transactionDetail->created_at)->format('d-M-Y h:i A') }}</span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom">
        <span>Order ID :</span>
        <span 
            class="text-primary text-hover-underline">{{ $transactionDetail->customer_order->order_id ?? 'N/A' }}</span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom">
        <span>Transaction Amount :</span>
        <span class="{{ $amount_text_css }}">{{ $transactionDetail->transaction_amount }}</span>
    </div>

    @if ($transactionDetail->charges)
        @foreach ($transactionDetail->charges as $key => $charge)
            <div class="d-flex justify-content-between py-1 border-bottom">
                <span>{{ $key }} :</span>
                <span class="text-danger">-{{ $charge }}</span>
            </div>
        @endforeach
    @endif

</div>

<!-- Total Section -->
<div class="border-top pt-4 mt-5">
    <div class="d-flex justify-content-between align-items-center fs-2 fw-bold">
        <span>Net Amount</span>
        <span class="{{ $final_amount_text_css }}">{{ $transactionDetail->final_transaction_amount }}</span>
    </div>
</div>
