@component('mail::message')
# Order Status Update

Dear {{ $order->retailer->firstname }},

Your order with ID **#{{ $order->order_id }}** has been moved to **NDR (Non-Delivered Report)** status as of {{ $order->ndr_at->format('d M Y, h:i A') }}.

**Reason:** {{ $order->shipment_activity ?? 'No specific reason provided.' }}

Please log in to your dashboard for more details.

Thanks,
{{ config('app.name') }}
@endcomponent
