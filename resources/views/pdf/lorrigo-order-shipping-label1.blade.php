<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shipping Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 140mm;
            margin: 0 auto;
            padding: 0;
        }

        .wrapper {
            border: 1px solid #3d3d3d;
            border-collapse: collapse;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        th,
        td {
            padding: 9px 5px;
            border: 0.3px solid #3d3d3d;
            vertical-align: middle;
        }

        .no-border {
            border: none !important;
        }

        .no-border-left {
            border-left: none !important;
        }

        .no-border-right {
            border-right: none !important;
        }

        .center {
            text-align: center;
        }

        .section-title {
            font-weight: bold;
        }

        .footer {
            font-size: 10px;
            text-align: center;
            margin-top: 10px;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        img {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Row 1 -->
        <table>
            <tr>
                <td width="25%">&nbsp;</td>
                <td width="50%" class="center">
                    <img src="{{ @$courier_logo }}" style="width: 125px; height: 100px;">
                </td>
                <td width="25%">&nbsp;</td>
            </tr>
        </table>

        <!-- Row 2 -->
        <table>
            <tr>
                <td width="45%" class="center no-border-right">
                    <img src="data:image/png;base64, {!! DNS1D::getBarcodePNG($courier_service_response, 'C128') !!}" alt="barcode"
                        style="height: 60px; width: 175px;" />
                    <div style="margin-top: 3px; font-size: 16px;">{{ $courier_service_response }}</div>
                </td>
                <td width="55%" class="no-border-left">
                    <div></div>
                </td>
            </tr>
        </table>

        <!-- Row 3 -->
        <table>
            <tr>
                <td width="70%">
                    <div class="section-title">Shipping Address:</div>
                    <div>{{ $customerOrder->customer->address }}, {{ $customerOrder->customer->state }},
                        {{ $customerOrder->customer->city }}</div>
                    <div>PIN: {{ $customerOrder->customer->pincode }}</div>
                    <div>Phone: {{ $customerOrder->customer->phone_number }}</div>
                </td>
                <td width="30%">
                    <h4 class="center" style="text-transform: uppercase; font-size: 18px; margin: 5px;">
                        {{ $customerOrder->payment_method }}</h4>
                    <div class="center">{{ $customerOrder->final_amount }}</div>
                </td>
            </tr>
        </table>

        <!-- Row 4 -->
        <table>
            <tr>
                <td width="60%">
                    <div class="section-title">Shipped by (if undelivered, return to):</div>
                    <div>{{ $pickupAddress->warehouse_name }}</div>
                    <div>{{ $pickupAddress->address }}, {{ $pickupAddress->state }}, {{ $pickupAddress->city }}</div>
                    <div>PIN : {{ $pickupAddress->pincode }}</div>
                    <div>Phone : {{ $pickupAddress->mobile_number }}</div>
                </td>
                <td width="40%">
                    <div><strong>Invoice No.: </strong> N/A </div>
                    <div><strong>Date: </strong> {{ $date }}</div>
                </td>
            </tr>
        </table>

        <!-- Row 5 (Headers) -->
        <table>
            <tr>
                <th width="60%">Product</th>
                <th width="20%">Price</th>
                <th width="20%">Total</th>
            </tr>
        </table>

        <!-- Row 6 (Product Row) -->
        <table>
            <tr>
                <td width="60%">{{ $productName }}</td>
                <td width="20%" class="center">{{ $customerOrder->final_amount }}</td>
                <td width="20%" class="center">{{ $customerOrder->final_amount }}</td>
            </tr>
        </table>

        <!-- Row 7 (Total) -->
        <table>
            <tr>
                <th width="60%">Total</th>
                <th width="20%" class="center">{{ $customerOrder->final_amount }}</th>
                <th width="20%" class="center">{{ $customerOrder->final_amount }}</th>
            </tr>
        </table>

        <!-- Row 8 (Return Address) -->
        <table style="border: none;">
            <tr>
                <td width="45%" class="center no-border-right">
                    <img src="data:image/png;base64, {!! DNS1D::getBarcodePNG($courier_service_response, 'C128') !!}" alt="barcode"
                        style="height: 60px; width: 175px;" />
                    <div style="margin-top: 3px; font-size: 16px;">{{ $courier_service_response }}</div>
                </td>
                <td width="55%" class="no-border-left">
                    <div>
                        <div>
                            <strong>Return Address: </strong>
                            {{ $pickupAddress->warehouse_name }},
                            {{ $pickupAddress->address }}, {{ $pickupAddress->state }},
                            {{ $pickupAddress->city }}, PIN : {{ $pickupAddress->pincode }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

    </div>

    <div class="footer">
        Thank you for your order!
    </div>
</body>

</html>
