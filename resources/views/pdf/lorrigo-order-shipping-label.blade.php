<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        .label-container {
            width: 700px;
            border: 2px solid #000;
            margin: auto;
        }

        /* --- Header (3 equal columns) --- */
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            table-layout: fixed;
        }
        .header td {
            border-left: 2px solid #000;
            text-align: center;
            vertical-align: middle;
            height: 150px;
            padding: 10px;
        }
        .header td:first-child {
            border-left: none;
        }

        .section {
            border-bottom: 1px solid #000;
            padding: 8px;
        }
        .bold {
            font-weight: bold;
        }

        /* Buyer + COD box */
        .buyer-details {
            position: relative;
            min-height: 120px;
            border-bottom: 1px solid #000;
            padding: 8px;
            padding-right: 200px; /* reserve space for COD box */
        }
        .cod-box {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 180px;
            border: 2px solid #000;
            text-align: center;
        }
        .cod-section {
            border-bottom: 2px solid #000;
            padding: 6px;
        }
        .cod-section:last-child {
            border-bottom: none;
        }

        /* Barcode */
        .barcode {
            text-align: center;
            padding: 15px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        /* Footer */
        .footer {
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="label-container">
            @php
                $courierLogos = [
                    'bluedart'   => 'bluedart.png',
                    'xpressbees' => 'expressbee.png',
                    'ekart'      => 'ekart.png',
                    'ecom'       => 'ecomexpress.png',
                    'delhivery'  => 'delhivery.png',
                    'dtdc'       => 'dtdc.png',
                    'amazon'     => 'amazon.png',
                    'indiapost'  => 'indianpost.png',
                    'velocity'   => 'velocity.png',
                    'zippyy'     => 'zippyy.png',
                ];

                $courierName = strtolower($courier_service ?? '');
                $logo = 'default.png'; // fallback

                foreach ($courierLogos as $key => $file) {
                    if (str_contains($courierName, $key)) {
                        $logo = $file;
                        break;
                    }
                }
            @endphp
        <!-- Header logos -->
        <table class="header">
            <tr>
                <td>
                    <img src="{{ optional($customerOrder->retailer->userDetail)->company_logo }}" alt="{{@$customerOrder->retailer->userDetail->company_name}}" height="50">
                </td>
                <td>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/media/courier_partner/new_logo/' . $logo))) }}" height="50">
                </td>
                <td style="font-size:18px; font-weight:bold; height:50px; vertical-align:middle;">
                        Powered by <br>
                        JDWEBNSHIP
                </td>
                {{-- <td>
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/media/logos/bigmart.jpg'))) }}" height="50">
                </td> --}}
            </tr>
        </table>

        {{-- {{dd($customerOrder->retailer->userDetail->company_logo)}} --}}
        <!-- Product details -->
        <div class="section">
            <b>{{ @$productName }}  {{ @$customerOrder->order_product_detail->variation ?? '' }} ( Qty : {{ @$customerOrder->order_product_detail->quantity }})</b>
        </div>

        <!-- Seller info -->
        <div class="section">
            <b>This order comes from:</b> {{ optional($customerOrder->retailer->userDetail)->company_name ?? '' }} <br>
        </div>

        <!-- Note + Order Info -->
        <div class="section">
            <span class="bold">Note- No Open Delivery Allowed </span> <br>
            <span class="bold">Order Number:  {{ @$customerOrder->order_id }} &nbsp;&nbsp; || </span>
            {{-- <span class="bold">Channel Order ID:  CHN987654 </span> --}}
        </div>

        <!-- Buyer Details + COD Box -->
        <div class="buyer-details">
            <div class="buyer-info">
                <span class="bold">Buyer Details:<br>
                {{ @$customerOrder->firstname }} <br>
                {{ @$customerOrder->customer->address }},
                {{ @$customerOrder->customer->state }},
                {{ @$customerOrder->customer->city }} <br>
                - {{ @$customerOrder->customer->pincode }} <br>
                </span>
                <span class="bold">Contact Number:</span> {{ @$customerOrder->customer->phone_number }}
            </div>

            <div class="cod-box">
                <div class="cod-section"><span class="bold">COD <br> Rs.{{ @$customerOrder->final_amount }}</span></div>
                <div class="cod-section"><span class="bold">{{ @$customerOrder->customer->city }}</span></div>
                <div class="cod-section"><span class="bold">{{ @$customerOrder->customer->pincode }}</span></div>
            </div>
        </div>

        <!-- Barcode Section -->
        <div class="barcode">
            <span class="bold">{{ strtoupper(@$courierName) }}</span><br>
                <img src="data:image/png;base64,{!! DNS1D::getBarcodePNG($courier_service_response, 'C128') !!}" alt="Barcode"><br>
                {{ $courier_service_response }}
        </div>

        <!-- Return Address -->
        <div class="footer">
            <span class="bold">If not delivered, please return at below address:-</span><br>
            <span class="bold">{{ optional($customerOrder->retailer->userDetail)->company_name ?? '' }} </span>
            <span class="bold">{{ @$pickupAddress->address }} </span>
            {{ @$pickupAddress->state }},<br>
            {{ @$pickupAddress->city }} - {{ @$pickupAddress->pincode }} <br>
            <span class="bold"> Mobile: {{ @$pickupAddress->mobile_number }}</span>
        </div>
    </div>
</body>
</html>
