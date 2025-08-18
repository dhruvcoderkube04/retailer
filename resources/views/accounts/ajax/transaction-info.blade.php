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

    <div class="d-flex justify-content-between pb-2 border-bottom flex-wrap">
        <span>Product Name :</span>
        <span class="text-end text-break" style="max-width: 240px;">
            {{ $transactionDetail->customer_order?->order_product_detail?->name ?? 'N/A' }}
        </span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom flex-wrap">
        <span>Tracking ID :</span>
        <span class="text-end text-break" style="max-width: 240px;">N/A</span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom flex-wrap">
        <span>Remark :</span>
        <span class="text-end text-break" style="max-width: 240px;">
            {{ $transactionDetail->description ?? 'N/A' }}
        </span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom flex-wrap">
        <span>Date :</span>
        <span class="text-end text-break" style="max-width: 240px;">
            {{ \Carbon\Carbon::parse($transactionDetail->created_at)->format('d-M-Y h:i A') }}
        </span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom flex-wrap">
        <span>Order ID :</span>
        <span class="text-end text-break text-primary text-hover-underline" style="max-width: 240px;">
            {{ $transactionDetail->customer_order->order_id ?? 'N/A' }}
        </span>
    </div>

    <div class="d-flex justify-content-between py-1 border-bottom flex-wrap">
        <span>Transaction Amount :</span>
        <span class="text-end text-break {{ $amount_text_css }}" style="max-width: 240px;">
            {{ $transactionDetail->transaction_amount }}
        </span>
    </div>

    @if ($transactionDetail->charges)
        @foreach ($transactionDetail->charges as $key => $charge)
            <div class="d-flex justify-content-between py-1 border-bottom flex-wrap">
               @if ($key != 'RTO Charge')
                    <span>{{ $key }} :</span>
                    <span class="text-end text-break text-danger" style="max-width: 240px;">
                        {{ $charge }}
                    </span>
               @endif
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
