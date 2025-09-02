<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shipping Label</title>
</head>

<body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; font-size:14px; color:#000;">
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
        $logo = 'default.png'; // fallbacks

        foreach ($courierLogos as $key => $file) {
            if (str_contains($courierName, $key)) {
                $logo = $file;
                break;
            }
        }
    @endphp
    <table style="width:100%; border-collapse:collapse; table-layout:fixed; border-bottom:px solid #000;">
        <tr>
            <td style="height:150px; text-align:center; vertical-align:middle; padding:10px;">
            </td>
        </tr>
    </table>
    <div style="width:700px; margin:0 auto; border:2px solid #000;">
        <!-- Header (3 equal cells for logos or text) -->
        <table style="width:100%; border-collapse:collapse; table-layout:fixed; border-bottom:2px solid #000;">
            <tr>
                <td style="height:150px; text-align:center; vertical-align:middle; padding:10px;">
                    @if(optional($customerOrder->retailer->userDetail)->company_logo)
                        <img
                            src="{{ optional($customerOrder->retailer->userDetail)->company_logo }}"
                            alt="{{ optional($customerOrder->retailer->userDetail)->company_name }}"
                            style="max-height:100px; max-width:100%;">
                    @else
                        <p style="font-size:18px; font-weight:bold; height:50px; vertical-align:middle;">
                            {{ optional($customerOrder->retailer->userDetail)->company_name }}
                        </p>
                    @endif
                </td>
                <td style="height:150px; text-align:center; vertical-align:middle; padding:10px; border-left:2px solid #000;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/media/courier_partner/new_logo/' . $logo))) }}" height="50" style="max-height:100px; max-width:100%;">
                </td>
                <td style="height:150px; text-align:center; vertical-align:middle; padding:10px; border-left:2px solid #000;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/media/logos/dark-logo.svg'))) }}" height="50" style="max-height:100px; max-width:100%;">
                    <b>
                       Powered by
                    </b>
                </td>
            </tr>

        </table>

        <!-- Product Details -->
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="padding:15px 20px; border-bottom:2px solid #000;">
                    <b>{{ @$productName }}  {{ @$customerOrder->order_product_detail->variation ?? '' }} ( Qty : {{ @$customerOrder->order_product_detail->quantity }})</b>
                </td>
            </tr>
        </table>

        <!-- Seller -->
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="padding:15px 20px; border-bottom:2px solid #000;">
                    <b>This order comes from -</b> {{ optional($customerOrder->retailer->userDetail)->company_name ?? '' }} <br>
                </td>
            </tr>
        </table>

        <!-- Note + Order Info -->
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="padding:15px 20px; border-bottom:2px solid #000;">
                    <b>Note- No Open Delivery Allowed</b><br>
                    <b>Order Number-</b> {{ @$customerOrder->order_id }} &nbsp;&nbsp;
                    {{-- <b>Channel Order ID:</b> --}}
                </td>
            </tr>
        </table>

        <!-- Buyer Details + COD Box (no absolute positioning) -->
        <table style="width:100%; border-collapse:collapse; border-bottom:1px solid #000;">
            <tr>
                <!-- Buyer -->
                <td style="width:70%; vertical-align:top; padding:15px 20px;">
                    <b>Buyer Details:</b><br>
                    {{ @$customerOrder->customer->firstname }} {{ @$customerOrder->customer->lastname }} <br>
                    {{ @$customerOrder->customer->address }},
                    {{ @$customerOrder->customer->state }},
                    {{ @$customerOrder->customer->city }} <br>
                    - {{ @$customerOrder->customer->pincode }} <br><br>
                    <b>Contact Number:</b> {{ @$customerOrder->customer->phone_number }}
                </td>

                <!-- COD Box -->
                <td style="width:30%; padding:10px; vertical-align:middle; padding-right: 0;">
                    <table style="width:100%; border-collapse:collapse; border:2px solid #000; text-align:center; border-right: 0;">
                        <tr>
                            <td style="padding:8px; border-bottom:2px solid #000;">
                                <b>COD</b><br><b>Rs.{{ @$customerOrder->final_amount }}</b>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td style="padding:8px; border-bottom:2px solid #000;">
                                <b>NDA/NEC</b>
                            </td>
                        </tr> -->
                        <tr>
                            <td style="padding:8px; border-bottom:2px solid #000;">
                                <b>{{ @$customerOrder->customer->pincode }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:8px;">
                                <b>{{ @$courier_service }}</b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Barcode placeholder -->
        <table style="width:100%; border-collapse:collapse; border-top:1px solid #000; border-bottom:1px solid #000;">
            <tr>
                <td style="text-align:center; padding:15px 20px;">
                <span class="bold">{{ strtoupper(@$courierName) }}</span><br>
                <img src="data:image/png;base64,{!! DNS1D::getBarcodePNG($courier_service_response, 'C128') !!}" alt="Barcode" style="display:block; margin:15px auto;"><br>
                {{ $courier_service_response }}
                </td>
            </tr>
        </table>

        <!-- Return Address -->
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="padding:15px 20px;">
                    <b>If not delivered, please return at below address:-</b><br>
                    {{ optional($customerOrder->retailer->userDetail)->company_name ?? '' }}<br>
                    Return Address: {{ @$pickupAddress->address }}
                    {{ @$pickupAddress->state }},
                    {{ @$pickupAddress->city }} - {{ @$pickupAddress->pincode }} <br>
                    <b>Mobile: {{ @$pickupAddress->mobile_number }} </b>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>
