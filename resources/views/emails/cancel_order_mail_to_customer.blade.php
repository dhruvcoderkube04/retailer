@extends('layouts.email-base')

@section('content')
    @php
        $order = $customerOrder;
        $customer = $customer ?? ['name' => 'Customer'];
    @endphp

    <p style="font-family: Arial, sans-serif;">Hello {{ $customer['name'] ?? 'Customer' }},</p>

    <p style="font-family: Arial, sans-serif; color: #34495e;">
        We're sorry to inform you that your order has been <strong style="color: #e74c3c;">cancelled.</strong>
        Below are the details of the cancelled order:
    </p>

    <h3 style="font-family: Arial, sans-serif; background-color: #fce4e4; padding: 10px; border-left: 5px solid #e74c3c;">
        Order ID: <span style="color: #2c3e50;">{{ $order->order_id }}</span>
    </h3>

    <table border="1" cellpadding="8" cellspacing="0" width="100%"
        style="margin-bottom: 30px; font-family: Arial, sans-serif; border-collapse: collapse; border: 1px solid #e0e0e0;">
        <tr style="background-color: #f9f9f9;">
            <th colspan="2" style="text-align: center;">Product Details</th>
        </tr>
        <tr>
            <th style="text-align: left;">Product Name</th>
            <td>{{ $order->order_product_detail->name }}</td>
        </tr>
        @if ($order->product_variation)
            <tr>
                <th style="text-align: left;">Product Variation</th>
                <td>{{ $order->product_variation }}</td>
            </tr>
        @endif
        <tr>
            <th style="text-align: left;">Image</th>
            <td>
                @if ($order->order_product_detail->images)
                    @php
                        $image = explode(',', $order->order_product_detail->images);
                    @endphp
                    <img src="{{ Storage::disk('spaces')->url($image[0]) }}" width="60" alt="Product Image"
                        onerror="this.onerror=null;this.src='{{ asset('assets/media/images/no_image.jpg') }}';">
                @else
                    <img src="{{ asset('assets/media/images/no_image.jpg') }}" width="60" alt="No Image">
                @endif
            </td>
        </tr>

        <tr style="background-color: #f9f9f9;">
            <th colspan="2" style="text-align: center;">Order Summary</th>
        </tr>
        <tr>
            <th style="text-align: left;">Quantity</th>
            <td>{{ $order->quantity }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Total Amount</th>
            <td>₹{{ number_format($order->final_amount, 2) }}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Payment Method</th>
            <td>{{ strtoupper($order->payment_method) }}</td>
        </tr>
    </table>

    @if (!empty($cancelled_reason))
        <h3
            style="font-family: Arial, sans-serif; background-color: #fff3cd; padding: 10px; border-left: 5px solid #f39c12;">
            Reason for Cancellation
        </h3>
        <p style="font-family: Arial, sans-serif; color: #7f8c8d; margin-bottom: 30px;">
            {{ $cancelled_reason }}
        </p>
    @endif

    <p style="font-family: Arial, sans-serif; color: #34495e;">
        If you have any questions regarding this cancellation, feel free to contact our support team.
    </p>

    <p style="font-family: Arial, sans-serif; color: #2c3e50;">
        Thank you for understanding.
    </p>
@endsection
