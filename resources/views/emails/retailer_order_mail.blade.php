@extends('layouts.email-base')

@section('content')
    @php
        $groupedOrders = collect($orderItemsForMail)->groupBy('order_id');
        $i = 0;
    @endphp

    <p style="font-family: Arial, sans-serif; color: #2c3e50;">Hello {{ $retailer->firstname ?? 'Retailer' }},</p>

    <p style="font-family: Arial, sans-serif; color: #34495e;">
        You have received new order(s) with the following details:
    </p>

    @foreach ($groupedOrders as $orderId => $items)
        @php
            $i++;
        @endphp
        <h3
            style="font-family: Arial, sans-serif; background-color: #f1f1f1; padding: 10px; border-left: 5px solid #3498db;">
            Order {{ $i }} : <span style="color: #2c3e50;">{{ $orderId }}</span> -
            {{ $items[0]['product_name'] }}
        </h3>

        <table border="1" cellpadding="8" cellspacing="0" width="100%"
            style="margin-bottom: 30px; font-family: Arial, sans-serif; border-collapse: collapse;">
            @foreach ($items as $item)
                <tr style="background-color: #ecf0f1;">
                    <th colspan="2" style="text-align: center;">Product Details</th>
                </tr>
                <tr>
                    <th style="text-align: left;">Product Name</th>
                    <td>{{ $item['product_name'] }}</td>
                </tr>
                @if ($item['product_variation'])
                    <tr>
                        <th style="text-align: left;">Product Variation</th>
                        <td>{{ $item['product_variation'] }}</td>
                    </tr>
                @endif
                <tr>
                    <th style="text-align: left;">Image</th>
                    <td>
                        @if ($item['product_image'])
                            @php
                                $image = explode(',', $item['product_image']);
                            @endphp
                            <img src="{{ Storage::disk('spaces')->url($image[0]) }}" width="60"
                                onerror="this.onerror=null;this.src='{{ asset('assets/media/images/no_image.jpg') }}';">
                        @else
                            <img src="{{ asset('assets/media/images/no_image.jpg') }}" width="60">
                        @endif
                    </td>
                </tr>

                <tr style="background-color: #ecf0f1;">
                    <th colspan="2" style="text-align: center;">Order Details</th>
                </tr>
                <tr>
                    <th style="text-align: left;">Order ID</th>
                    <td>{{ $orderId }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">Quantity</th>
                    <td>{{ $item['quantity'] }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">Total Amount</th>
                    <td>₹{{ number_format($item['final_amount'], 2) }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">Payment Method</th>
                    <td>{{ strtoupper($item['payment_method']) }}</td>
                </tr>

                <tr style="background-color: #ecf0f1;">
                    <th colspan="2" style="text-align: center;">Customer Details</th>
                </tr>
                <tr>
                    <th style="text-align: left;">Customer Name</th>
                    <td>{{ $item['firstname'] }} {{ $item['lastname'] }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">Phone</th>
                    <td>{{ $item['phone_number'] }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">Email</th>
                    <td>{{ $item['email'] }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">Address</th>
                    <td>{{ $item['address'] }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">City</th>
                    <td>{{ $item['city'] }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">State</th>
                    <td>{{ $item['state'] }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">Pincode</th>
                    <td>{{ $item['pincode'] }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
@endsection
