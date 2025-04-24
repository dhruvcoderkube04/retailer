<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\RetailerWebManagement;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailerAccountTransactionController extends Controller
{
    // index
    public function indexAccounts(Request $request)
    {
        $user = Auth::user();
        if (!$user->user_type == 3) { // retailer            
            return redirect()->route('retailer.dashboard')->with('error', 'Invalid user!');
        }

        $webManagement = RetailerWebManagement::where('retailer_id', $user->id)->first();

        $transactions = AccountTransaction::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        return view('accounts.index', compact('transactions', 'webManagement'));
    }

    // AJAX - date filter
    public function dateFilterAccounts(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->user_type != 3) { // Must be retailer
                return response()->json(['status' => false, 'msg' => 'Invalid user!']);
            }

            $from = Carbon::createFromFormat('d/m/Y', $request->from)->format('Y-m-d');
            $to = Carbon::createFromFormat('d/m/Y', $request->to)->format('Y-m-d');

            // Basic validation
            if (!$from || !$to) {
                return response()->json(['status' => false, 'msg' => 'Please provide both from and to dates.']);
            }

            $transactions = AccountTransaction::where('user_id', $user->id)
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'html' => view('accounts.ajax.date-filter', compact('transactions'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong!',
                'error' => $e->getMessage(), // Optional: for debugging
            ]);
        }
    }
}
