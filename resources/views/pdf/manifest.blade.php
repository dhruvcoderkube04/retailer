<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manifest</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
   <div style="margin-bottom:20px; text-align:center;">
        <!-- Logo centered -->
        <img src="{{ public_path('assets/media/logos/dark-logo.svg') }}" alt="JDWEBNSHIP" height="40" style="display:block; margin:0 auto;">

        <!-- Date aligned right under logo -->
        <div style="text-align:right; width:100%; margin-top:5px;">
            Date: {{ $date }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sr.no</th>
                <th>Date</th>
                <th>Customer Name</th>
                <th>Order ID</th>
                <th>AWB</th>
                <th>Rate</th>
                <th>City</th>
                <th>Courier</th>
                <th>Sign</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    <td>{{ @$order->customer->firstname }} {{  @$order->customer->lastname}}</td>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->tracking_number ?? '-' }}</td>
                    <td>{{ $order->final_amount ?? '-' }}</td>
                    <td>{{ @$order->customer->city ?? '-' }}</td>
                    <td>{{ $order->courier_service ?? '-' }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><b>Proprietor</b><br> JDWebnship</p>
    </div>

</body>
</html>
