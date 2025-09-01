<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\CustomerOrders;
use App\Models\MarginManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Accounting;

class ReportController extends Controller
{
    public function saleReport()
    {
        return view('reports.sale');
    }

    public function fetchSaleReport(Request $request)
    {
        $draw = $request->input('draw');
        $searchValue = $request->input('search.value');
        $date_filter = explode(' - ', $request->input('date_filter'));
        $from = Carbon::createFromFormat('d/m/Y', $date_filter[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date_filter[1])->format('Y-m-d');

        $user_id = Auth::user()->id;
        $order = CustomerOrders::with(['customer', 'retailer', 'wholesaler', 'order_product_detail','paymentsettlement'])->where('retailer_id', $user_id)->where('status', 'delivered')
          ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->when($searchValue, function ($q) use ($searchValue) {
                $q->where(function ($query) use ($searchValue) {
                    $query->where('order_id', 'like', "%{$searchValue}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
        $total = CustomerOrders::where('retailer_id', $user_id)->count();
        $filtered = $order->count();

        // courier charge calculation

        $data = [];
        foreach ($order as $item) {
            $shipping_charge = round(($item->shipping_charge ?? 0) + ($item->cod_charge ?? 0) + ($item->shipping_charge_profit ?? 0) + ($item->cod_charge_profit ?? 0), 2);
            $profit_loss = !empty($item->wholesaler) ? $item->retailer_margin_amount - $shipping_charge : 0;

            $data[] = [
                'order_id' => $item->order_id,
                'customer_name' => @$item->customer->firstname . ' ' . @$item->customer->lastname,
                'product_name' => $item->order_product_detail->name,
                'weight' => $item->product_weight,
                'order_amount' => $item->final_amount,
                'wholesaler_base_amount' => !empty($item->wholesaler) ? $item->final_amount - $item->retailer_margin_amount : '-',
                'retailer_margin' => !empty($item->wholesaler) ? $item->retailer_margin_amount : '-',
                'shipping_charges' => $shipping_charge,
                'profit_loss' =>  $profit_loss > 0 ? '<span class="badge badge-success">'.$profit_loss.'</span>' : '<span class="badge badge-danger">'.$profit_loss.'</span>',
                // 'platform_margin' => round(($item->shipping_charge_profit ?? 0) + ($item->cod_charge_profit ?? 0), 2),
                'net_cash_in_hand' => round(($item->final_amount ?? 0) - ($item->shipping_charge ?? 0) - ($item->cod_charge ?? 0) - ($item->shipping_charge_profit ?? 0) - ($item->cod_charge_profit ?? 0), 2),
                'settlement_status' => $item->paymentsettlement ? '<span class="badge badge-success">Settled</span>' : '<span class="badge badge-danger">Pending</span>',
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function punchOrderReport()
    {
        return view('reports.punch_order');
    }

    public function fetchPunchOrderReport(Request $request)
    {
        $draw = $request->input('draw');
        $searchValue = $request->input('search.value');
        $date_filter = explode(' - ', $request->input('date_filter'));
        $from = Carbon::createFromFormat('d/m/Y', $date_filter[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date_filter[1])->format('Y-m-d');

        $user_id = Auth::user()->id;
        $order = CustomerOrders::with(['order_product_detail', 'retailer.userDetail','wholesaler.userDetail','punchOrder'])
            ->whereIn('order_process_by', ['wholesaler','retailer'])
            ->where('checkout_type', 'punch')
            ->orWhereIn('status',['approved_by_retailer','delivered','cancel','pending'])
            ->where('retailer_id', $user_id)
            ->whereNotNull('wholesaler_id')
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->when($searchValue, function ($q) use ($searchValue) {
                $q->where(function ($query) use ($searchValue) {
                    $query->where('order_id', 'like', "%{$searchValue}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // dd($order);

        $total = CustomerOrders::where('retailer_id', $user_id)->count();
        $filtered = $order->count();

        $data = [];
        foreach ($order as $item) {


            $data[] = [
                'punch_order_id' => $item->order_id,
                'wholesaler_name' => @$item->wholesaler->userDetail->company_name ?? '-',
                'product_amount' => $item->final_amount,
                'payment_mode' => !empty($item->punchOrder->payment_type) ?  Str::upper($item->punchOrder->payment_type) : (!empty($item->payment_method) ? Str::upper($item->payment_method) : '-'),
                'wallet_debit' => $item->wallet_debit,
                'status' => $item->status,
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function shippingChargesReport()
    {
        return view('reports.shipping_charges');
    }

    public function fetchShippingChargesReport(Request $request)
    {
        $searchValue = $request->input('search.value');
        $date_filter = explode(' - ', $request->input('date_filter'));
        $from = Carbon::createFromFormat('d/m/Y', $date_filter[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date_filter[1])->format('Y-m-d');
        $draw = $request->input('draw');

        $user_id = Auth::user()->id;
        $order = CustomerOrders::with(['customer', 'retailer', 'wholesaler', 'order_product_detail'])->where('retailer_id', $user_id)->where('status', 'delivered')->whereNotNull('tracking_number')
        ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
        ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
        ->when($searchValue, function ($q) use ($searchValue) {
            $q->where(function ($query) use ($searchValue) {
                $query->where('order_id', 'like', "%{$searchValue}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->get();

        $total = CustomerOrders::where('retailer_id', $user_id)->where('retailer_id', $user_id)->where('status', 'delivered')->whereNotNull('tracking_number')->count();
        $filtered = $order->count();

        $data = [];
        foreach ($order as $item) {
            $data[] = [
                'order_id' => $item->order_id,
                'product_weight' => $item->product_weight,
                'courier_partner' => $item->courier_service,
                'base_charge' => round(($item->shipping_charge ?? 0) + ($item->cod_charge ?? 0) + ($item->shipping_charge_profit ?? 0) + ($item->cod_charge_profit ?? 0), 2),
                'gst_amount' => round(($item->shipping_output_gst ?? 0) + ($item->cod_output_gst ?? 0), 2),
                'rto_charges' => in_array($item->status, ['rto', 'rtn_to_seller','can'])
                                ? round((($item->rto_charge ?? 0) + ($item->rto_charge_profit ?? 0)), 2)
                                : '-',
                'total_shipping_charges' => round(($item->shipping_charge ?? 0) + ($item->cod_charge ?? 0) + ($item->shipping_charge_profit ?? 0) + ($item->cod_charge_profit ?? 0) + ($item->shipping_output_gst ?? 0) + ($item->cod_output_gst ?? 0), 2),
                'status' => !empty($item->courier_partner_code) ? Str::title($item->status) : '-',
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function rtoReport()
    {
        return view('reports.rto-report');
    }

    public function fetchRtoReport(Request $request)
    {
        $draw = $request->input('draw');
        $searchValue = $request->input('search.value');
        $date_filter = explode(' - ', $request->input('date_filter'));
        $from = Carbon::createFromFormat('d/m/Y', $date_filter[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date_filter[1])->format('Y-m-d');

        $user_id = Auth::user()->id;
        $order = CustomerOrders::with(['customer', 'retailer', 'wholesaler', 'order_product_detail'])->where('retailer_id', $user_id)->whereIn('status', ['rto', 'rtn_to_seller','cancel'])
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                ->when($searchValue, function ($q) use ($searchValue) {
                    $q->where(function ($query) use ($searchValue) {
                        $query->where('order_id', 'like', "%{$searchValue}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();

        $total = CustomerOrders::where('retailer_id', $user_id)->count();
        $filtered = $order->count();

        $data = [];
        foreach ($order as $item) {
            $data[] = [
                'order_id' => $item->order_id,
                'customer' => @$item->customer->firstname ?? '-',
                'rto_date' => (!empty($item->in_transit_at) && !empty($item->cancel_at))
                            ? Carbon::parse($item->cancel_at)->format('d/m/Y')
                            : '-',
                'rto_charges' => !empty($item->in_transit_at) && in_array($item->status, ['rto', 'rtn_to_seller','cancel'])
                                ? round((($item->rto_charge ?? 0) + ($item->rto_charge_profit ?? 0)), 2)
                                : '-',
                'reason' => $item->cancelled_reason ?? '-',
                'wallet_impact' => $this->walletImpactCheck($item),
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }


    private function  walletImpactCheck($item)
    {
        $check_account_history = AccountTransaction::where('tracking_number', $item->tracking_number)->where('order_type','cancelled')->first();

        return $check_account_history ? ($check_account_history->final_transaction_amount < 0 ? '<span class=" badge badge-danger text-white">' . $check_account_history->final_transaction_amount . '</span>' : '<span class=" badge badge-success text-white">' . $check_account_history->final_transaction_amount . '</span>') : '-';
    }
}
