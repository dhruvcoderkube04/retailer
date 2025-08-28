<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CourierPartner;
use App\Models\CustomerOrders;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    public function financeTracking()
    {
        $wholesalers = User::with('userDetail')->where('user_type', '2')->where('status', 1)->get();
        $retailers = User::with('userDetail')->where('user_type', '3')->where('status', 1)->get();
        $courierPartners = CourierPartner::all();
        return view('accouting_tracking.finance_tracking', compact('wholesalers', 'retailers', 'courierPartners'));
    }

    public function getFinanceTracking(Request $request)
    {
        $retailerId = Auth::id();

        $query = CustomerOrders::with(['retailer', 'wholesaler', 'buyer', 'courierPartner'])
            ->where('retailer_id', $retailerId)
            ->where('status', 'delivered');

        if ($request->filled('wholesaler_filter') && $request->wholesaler_filter !== 'all') {
            $query->whereHas('wholesaler', function ($q) use ($request) {
                $q->where('id', $request->wholesaler_filter);
            });
        }

        if ($request->filled('courier_partner_filter') && $request->courier_partner_filter !== 'all') {
            $query->whereHas('courierPartner', function ($q) use ($request) {
                $q->where('id', $request->courier_partner_filter);
            });
        }

        if ($request->filled('date_filter')) {
            $dates = explode(' - ', $request->date_filter);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        $totalData = $query->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('status', 'like', '%' . str_replace(' ', '_', strtolower($search)) . '%')
                    ->orWhere('final_amount', 'like', '%' . str_replace(' ', '_', strtolower($search)) . '%')
                    ->orWhere('courier_service', 'like', '%' . str_replace(' ', '_', strtolower($search)) . '%')
                    ->orWhere(function ($q2) use ($search) {
                        if (stripos($search, 'retailer') !== false) {
                            $q2->whereNotNull('retailer_clone_product_id');
                        } elseif (stripos($search, 'wholesaler') !== false) {
                            $q2->whereNotNull('product_id');
                        }
                    })
                    ->orWhereHas('retailer', function ($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%");
                    })
                    ->orWhereHas('wholesaler', function ($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%");
                    })
                    ->orWhereHas('buyer', function ($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%");
                    })
                    ->orWhereHas('courierPartner', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Column sorting
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');
        $columns = [
            'order_id',
            'delivered_at',
            'retailer_id',
            'wholesaler_id',
            'customer_id',
            'product_type',
            'final_amount',
            'gst',
            'platform_margin',
            'courier_cost',
            'courier_partner_id',
            'courier_service',
            'courier_margin',
            'net_seller_earning',
            'status',
        ];
        $columnName = $columns[$orderColumnIndex] ?? 'id';

        if (in_array($columnName, [
            'order_id',
            'delivered_at',
            'final_amount',
            'courier_service',
            'status'
        ])) {
            $query->orderBy($columnName, $orderDirection);
        } else {
            $query->orderByDesc('id');
        }

        $filtered = $query->count();

        $allOrders = $query->orderBy($columnName, $orderDirection)->get();

        $cumulativeBalance = 0;
        $balances = [];
        foreach ($allOrders->reverse() as $order) {
            $credit = $this->calculateCredit($order);
            $debit  = $this->calculateDebit($order);

            $showDebit = !($order->status === 'delivered' && $credit > 0);

            if ($showDebit) {
                $cumulativeBalance += $credit - $debit;
            } else {
                $cumulativeBalance += $credit;
            }

            $balances[$order->id] = [
                'credit'  => $credit,
                'debit'   => $showDebit ? $debit : null,
                'balance' => $cumulativeBalance,
            ];
        }

        $pagedOrders = $allOrders->skip($start)->take($length);

        $data = [];
        $counter = $start + 1;
        foreach ($pagedOrders as $order) {
            $courierCost = $this->calculateCourierCost($order);
            $gst         = $this->calculateGst($order);
            $totalCharge = $courierCost + $gst;

            $data[] = [
                'no'           => $counter++,
                'date'         => $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('d-m-Y H:i A') : '-',
                'tracking_id'  => $order->order_id,
                'remark'       => $order->status ?? '-',
                'weight'       => $order->product_weight ?? '-',
                'order_amount' => number_format((float)$order->final_amount, 2),
                'courier'      => number_format((float)$courierCost, 2),
                'gst'          => number_format((float)$gst, 2),
                'total_charge' => number_format((float)$totalCharge, 2),
                'credit'       => number_format($balances[$order->id]['credit'], 2),
                'debit'        => $balances[$order->id]['debit'] !== null ? number_format($balances[$order->id]['debit'], 2) : '',
                'balance'      => number_format($balances[$order->id]['balance'], 2),
            ];
        }

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $filtered,
            "data"            => $data
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $retailerId = Auth::id();

        $query = CustomerOrders::with(['retailer', 'wholesaler', 'buyer', 'courierPartner'])
            ->where('retailer_id', $retailerId)
            ->where('status', 'delivered');

        if ($request->filled('wholesaler_filter') && $request->wholesaler_filter !== 'all') {
            $query->whereHas('wholesaler', function ($q) use ($request) {
                $q->where('id', $request->wholesaler_filter);
            });
        }

        if ($request->filled('courier_partner_filter') && $request->courier_partner_filter !== 'all') {
            $query->whereHas('courierPartner', function ($q) use ($request) {
                $q->where('id', $request->courier_partner_filter);
            });
        }

        if ($request->filled('date_filter')) {
            $dates = explode(' - ', $request->date_filter);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate   = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        $fileName = 'finance_tracking_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $columns = [
            'No.', 
            'Date',
            'Tracking ID',
            'Remark',
            'Weight',
            'Order Amount',
            'Courier',
            'GST',
            'Total Charge',
            'Credit',
            'Debit',
            'Balance'
        ];

        return Response::stream(function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            $allOrders = $query->orderBy('delivered_at', 'desc')->get();

            $cumulativeBalance = 0;
            $balances = [];
            foreach ($allOrders->reverse() as $order) {
                $credit = $this->calculateCredit($order);
                $debit  = $this->calculateDebit($order);

                $showDebit = !($order->status === 'delivered' && $credit > 0);

                if ($showDebit) {
                    $cumulativeBalance += $credit - $debit;
                } else {
                    $cumulativeBalance += $credit;
                }

                $balances[$order->id] = [
                    'credit'  => $credit,
                    'debit'   => $showDebit ? $debit : null,
                    'balance' => $cumulativeBalance,
                ];
            }

            $counter = 1;
            foreach ($allOrders as $order) {
                $courierCost = $this->calculateCourierCost($order);
                $gst         = $this->calculateGst($order);
                $totalCharge = $courierCost + $gst;

                fputcsv($handle, [
                    $counter++,
                    $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('d-m-Y H:i A') : '-',
                    $order->order_id,
                    ucwords(str_replace('_', ' ', $order->status)),
                    $order->product_weight ?? '-',
                    number_format((float)$order->final_amount, 2),
                    number_format((float)$courierCost, 2),
                    number_format((float)$gst, 2),
                    number_format((float)$totalCharge, 2),
                    number_format($balances[$order->id]['credit'], 2),
                    $balances[$order->id]['debit'] !== null ? number_format($balances[$order->id]['debit'], 2) : '',
                    number_format($balances[$order->id]['balance'], 2),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function calculateCourierCost($order): float
    {
        return (float) ($order->shipping_charge ?? 0)
            + (float) ($order->cod_charge ?? 0);
    }

    public function calculateGst($order): float
    {
        return (float) ($order->shipping_charge_gst_amount ?? 0)
            + (float) ($order->cod_charge_gst_amount ?? 0);
    }

    public function calculateCredit($order): float
    {
        if ($order && $order->final_amount > 0) {
            // Credit = Final amount - (Courier cost + GST)
            return (float) ($order->final_amount ?? 0)
                - ($this->calculateCourierCost($order) + $this->calculateGst($order));
        }
        return 0.0;
    }

    public function calculateDebit($order): float
    {
        if ($order && $order->final_amount > 0) {
            // Debit = Courier cost + GST
            return $this->calculateCourierCost($order) + $this->calculateGst($order);
        }
        return 0.0;
    }

    public function calculateBalance($order): float
    {
        if ($order) {
            return $this->calculateCredit($order) - $this->calculateDebit($order);
        }
        return 0.0;
    }
}
