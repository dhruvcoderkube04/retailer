<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\WithdrawalRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RetailerAccountTransactionController extends Controller
{
    //<-------------------- START : account transactions ------------------------>
    // index - success account transactions
    public function indexSuccessAccountsTransactions(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type != 3) { // retailer
            return redirect()->route('retailer.dashboard')->with('error', 'Invalid user!');
        }

        $transactions = AccountTransaction::where('user_id', $user->id)
            ->where('type', 'success')
            ->orderBy('id', 'desc')
            ->get();

        return view('accounts.success-accounts.index', compact('transactions', 'user'));
    }

    // AJAX - server-side datatable fetch-success-records
    public function fetchSuccessRecord(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');
        $date_filter = explode(' - ', $request->input('date_filter'));

        $from = Carbon::createFromFormat('d/m/Y', $date_filter[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date_filter[1])->format('Y-m-d');
        $retailer = Auth::user();

        $query = AccountTransaction::where('user_id', $retailer->id)
            ->where('type', 'success')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                    ->orWhere('transaction_amount', 'like', '%' . $search . '%')
                    ->orWhere('charges', 'like', '%' . $search . '%')
                    ->orWhere('final_transaction_amount', 'like', '%' . $search . '%')
                    ->orWhere('current_balance', 'like', '%' . $search . '%')
                    ->orWhereHas('customer_order', function ($q) use ($search) {
                        $q->where('order_id', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy($columnName, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        $cntFilter = clone $query;
        $query->offset($page)->limit($limit);
        $transactions = $query->get();

        $queryTotal = DB::select("SELECT COUNT(*) AS count FROM account_transactions WHERE user_id = $retailer->id AND `type` = 'success'")[0]->count;

        $data = [];
        $i = $page;
        $total_credit = 0;
        $total_debit = 0;
        $total_income = 0;
        foreach ($transactions as $item) {
            $i++;

            if ($item->final_transaction_amount > 0) {
                $total_credit += $item->final_transaction_amount;
            }
            if ($item->final_transaction_amount < 0) {
                $total_debit += $item->final_transaction_amount;
            }
            $total_income += $item->final_transaction_amount;

            $transaction_type = '';
            if ($item->final_transaction_amount > 0) {
                $transaction_type = '<i class="ki-duotone ki-arrow-up fs-1 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>';
            } elseif ($item->final_transaction_amount < 0) {
                $transaction_type = '<i class="ki-duotone ki-arrow-down fs-1 text-danger me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>';
            }

            $final_transaction_amount = '';
            if ($item->final_transaction_amount > 0) {
                $final_transaction_amount = '<div class="badge badge-light-success fs-6">
                                                +' . $item->final_transaction_amount . '
                                            </div>';
            } elseif ($item->final_transaction_amount <= 0) {
                $final_transaction_amount = '<div class="badge badge-light-danger fs-6">
                                                ' . $item->final_transaction_amount . '
                                            </div>';
            }

            $current_balance = '<div class="badge badge-light-secondary fs-6">
                                    ' . $item->current_balance . '
                                </div>';

            $status = '';
            if ($item->status == 1) {
                $status = '<div class="text-success fs-6">Clear</div>';
            } else if ($item->status == 0) {
                $status = '<div class="text-danger fs-6">Pending</div>';
            }

            $info = '<a href="javascript:void(0)" class="transaction-info"
                        data-id="' . $item->id . '">
                        <i class="ki-duotone ki-information-2 fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </a>';

            $data[] = array(
                "transaction_type" => $transaction_type,
                "description" => $item->description,
                "created_at" => $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A',
                "order_id" => $item->customer_order?->order_id ?? 'N/A',
                "final_transaction_amount" => $final_transaction_amount,
                "current_balance" => $current_balance,
                "status" => $status,
                "info" => $info
            );
        }
        return response()->json([
            "draw" => $_POST['draw'],
            "recordsTotal" => $queryTotal,
            "recordsFiltered" => $cntFilter->count(),
            'data' => $data,
            "totals" => [
                "total_credit" => round($total_credit, 2),
                "total_debit" => round($total_debit, 2),
                "total_income" => round($total_income, 2),
            ]
        ]);
    }

    // index - pending account transactions
    public function indexPendingAccountsTransactions(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type != 3) { // retailer
            return redirect()->route('retailer.dashboard')->with('error', 'Invalid user!');
        }

        $transactions = AccountTransaction::where('user_id', $user->id)
            ->where('type', 'pending')
            ->orderBy('id', 'desc')
            ->get();

        return view('accounts.pending-accounts.index', compact('transactions', 'user'));
    }

    // AJAX - server-side datatable fetch-pending-records
    public function fetchPendingRecord(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');
        $date_filter = explode(' - ', $request->input('date_filter'));

        $from = Carbon::createFromFormat('d/m/Y', $date_filter[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date_filter[1])->format('Y-m-d');
        $retailer = Auth::user();

        $query = AccountTransaction::where('user_id', $retailer->id)
            ->where('type', 'pending')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                    ->orWhere('transaction_amount', 'like', '%' . $search . '%')
                    ->orWhere('charges', 'like', '%' . $search . '%')
                    ->orWhere('final_transaction_amount', 'like', '%' . $search . '%')
                    ->orWhere('current_balance', 'like', '%' . $search . '%')
                    ->orWhereHas('customer_order', function ($q) use ($search) {
                        $q->where('order_id', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy($columnName, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        $cntFilter = clone $query;
        $query->offset($page)->limit($limit);
        $transactions = $query->get();

        $queryTotal = DB::select("SELECT COUNT(*) AS count FROM account_transactions WHERE user_id = $retailer->id AND `type` = 'pending'")[0]->count;

        $data = [];
        $i = $page;
        $total_credit = 0;
        $total_debit = 0;
        $total_income = 0;
        foreach ($transactions as $item) {
            $i++;

            if ($item->final_transaction_amount > 0) {
                $total_credit += $item->final_transaction_amount;
            }
            if ($item->final_transaction_amount < 0) {
                $total_debit += $item->final_transaction_amount;
            }
            $total_income += $item->final_transaction_amount;

            $transaction_type = '';
            if ($item->final_transaction_amount > 0) {
                $transaction_type = '<i class="ki-duotone ki-arrow-up fs-1 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>';
            } elseif ($item->final_transaction_amount < 0) {
                $transaction_type = '<i class="ki-duotone ki-arrow-down fs-1 text-danger me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>';
            }

            $final_transaction_amount = '';
            if ($item->final_transaction_amount > 0) {
                $final_transaction_amount = '<div class="badge badge-light-success fs-6">
                                                +' . $item->final_transaction_amount . '
                                            </div>';
            } elseif ($item->final_transaction_amount <= 0) {
                $final_transaction_amount = '<div class="badge badge-light-danger fs-6">
                                                ' . $item->final_transaction_amount . '
                                            </div>';
            }

            $current_balance = '<div class="badge badge-light-secondary fs-6">
                                    ' . $item->current_balance . '
                                </div>';

            $status = '';
            if ($item->status == 1) {
                $status = '<div class="text-success fs-6">Clear</div>';
            } else if ($item->status == 0) {
                $status = '<div class="text-danger fs-6">Pending</div>';
            }

            $info = '<a href="javascript:void(0)" class="transaction-info"
                        data-id="' . $item->id . '">
                        <i class="ki-duotone ki-information-2 fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </a>';

            $data[] = array(
                "transaction_type" => $transaction_type,
                "description" => $item->description,
                "created_at" => $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A',
                "order_id" => $item->customer_order?->order_id ?? 'N/A',
                "final_transaction_amount" => $final_transaction_amount,
                "current_balance" => $current_balance,
                "status" => $status,
                "info" => $info
            );
        }
        return response()->json([
            "draw" => $_POST['draw'],
            "recordsTotal" => $queryTotal,
            "recordsFiltered" => $cntFilter->count(),
            'data' => $data,
            "totals" => [
                "total_credit" => round($total_credit, 2),
                "total_debit" => round($total_debit, 2),
                "total_income" => round($total_income, 2),
            ]
        ]);
    }

    // AJAX - transaction info
    public function transactionInfo(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->user_type != 3) { // Must be retailer
                return response()->json(['status' => false, 'msg' => 'Invalid user!']);
            }

            // Basic validation
            if (!$request->transaction_id) {
                return response()->json(['status' => false, 'msg' => 'Invalid transaction details.']);
            }

            $transactionDetail = AccountTransaction::with('customer_order', 'user')
                ->where('id', $request->transaction_id)
                ->first();

            return response()->json([
                'status' => true,
                'html' => view('accounts.ajax.transaction-info', compact('transactionDetail'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ]);
        }
    }
    //<-------------------- END : account transactions ------------------------>


    //<-------------------- START : withdrawal Request ------------------------>
    // withdrawal request view page
    public function withdrawalRequestIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->user_type == 3) { // retailer
            return redirect()->route('retailer.dashboard')->with('error', 'Invalid user!');
        }

        return view('accounts.withdrawal-request.index', compact('user'));
    }

    // AJAX - server-side datatable fetch-records of withdrawal transactions
    public function fetchRecordWithdrawalTransactions(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');
        $date_filter = explode(' - ', $request->input('date_filter'));

        $from = Carbon::createFromFormat('d/m/Y', $date_filter[0])->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', $date_filter[1])->format('Y-m-d');
        $retailer = Auth::user();

        $query = WithdrawalRequest::where('user_id', $retailer->id)
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('request_amount', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('request_type', 'like', '%' . $search . '%')
                    ->orWhere('transaction_id', 'like', '%' . $search . '%')
                    ->orWhere('created_at', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%')
                    ->orWhereHas('wholesaler.userDetail', function ($q) use ($search) {
                        $q->where('company_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('wholesaler', function ($q) use ($search) {
                        $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%$search%"])
                            ->orWhere('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy($columnName, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        $cntFilter = clone $query;
        $query->offset($page)->limit($limit);
        $withdrawal_transactions = $query->get();

        $queryTotal = DB::select("SELECT COUNT(*) AS count FROM withdrawal_requests WHERE user_id = $retailer->id")[0]->count;

        $data = [];
        $i = $page;
        foreach ($withdrawal_transactions as $item) {
            $i++;

            $transaction_type = '<i class="ki-duotone ki-arrow-down fs-1 text-danger me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>';

            $request_type = '';
            $wholesaler_details = '-';
            if ($item->request_type == 'to_account') {
                $request_type = '<div class="badge badge-light-success fs-7">To Self Account</div>';
            } elseif ($item->request_type == 'to_wholesaler') {
                $request_type = '<div class="badge badge-light-info fs-7">To Wholesaler</div>';

                $wholesaler_details = '<div>
                    <div><strong>Name: </strong>' . $item->wholesaler->firstname . ' ' . $item->wholesaler->lastname . '</div>
                    <div><strong>Comapny: </strong>' . $item->wholesaler->userDetail->company_name . '</div>
                </div>';
            }

            $remarks = $item->remarks ? $item->remarks : 'No Remarks Entered';

            $request_amount = '<div class="badge badge-light-danger fs-6">
                                    ' . -abs($item->request_amount) . '
                                </div>';

            $process_by = 'Unknown';
            if ($item->user_id == $item->process_by) {
                $process_by = 'Self';
            } else if ($item?->process_by_user?->user_type == 1) {
                $process_by = 'Admin';
            }

            $status = '';
            if ($item->status == 'completed') {
                $status = '<div class="badge badge-light-success fs-6">Completed</div>';
            } else if ($item->status == 'pending') {
                $status = '<div class="badge badge-light-primary fs-6">Pending</div>';
            } else if ($item->status == 'processing') {
                $status = '<div class="badge badge-light-info fs-6">Processing</div>';
            } else if ($item->status == 'rejected') {
                $status = '<div class="badge badge-light-danger fs-6">Rejected</div>';
            }

            $data[] = array(
                "transaction_type" => $transaction_type,
                "transaction_id" => $item->transaction_id ?? 'N/A',
                "request_type" => $request_type ?? 'N/A',
                "wholesaler_detail" => $wholesaler_details,
                "process_by" => $process_by,
                "remarks" => $remarks,
                "created_at" => $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A',
                "request_amount" => $request_amount,
                "status" => $status
            );
        }
        return response()->json(array("draw" => $_POST['draw'], "recordsTotal" => $queryTotal, "recordsFiltered" => $cntFilter->count(), 'data' => $data));
    }

    // AJAX - verify-wholesaler-email
    public function verifyWholesalerEmail(Request $request)
    {
        try {
            $user = User::where('email', $request->email)
                ->where('user_type', 2)
                ->where('status', 1)
                ->where('is_delete', 0)
                ->first();

            if ($user) {
                if ($user->userDetail->wallet_status == 'approved') {
                    $wallet_status = 'Active';
                } else {
                    $wallet_status = 'Inactive';
                }
                return response()->json([
                    'status' => true,
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->firstname . ' ' . $user->lastname,
                        'company_name' => $user->userDetail->company_name ?? 'N/A',
                        'mobile' => $user->phone_number ?? 'N/A',
                        'wallet_status' => $wallet_status
                    ]
                ]);
            }

            return response()->json(['status' => false, 'msg' => 'Wholesaler not exist.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    // AJAX - withdrawal-request store
    public function withdrawalRequestStore(Request $request)
    {
        $request->validate([
            'request_amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:255',
        ], [
            'request_amount.required' => 'Withdrawal amount is required.',
            'request_amount.numeric' => 'Amount must be a number.',
            'request_amount.min' => 'Amount must be greater than zero.',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $userDetail = UserDetail::where('user_id', $user->id)->first();
            if ($userDetail->wallet_status !== 'approved') {
                return response()->json([
                    'status' => false,
                    'msg' => 'Your wallet is not activated!, Kindly activate the wallet',
                    'error' => 'Your wallet is not activated!, Kindly activate the wallet',
                ]);
            }

            // check balance
            if ($request->request_amount > $userDetail->success_wallet) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Insufficient balance.',
                    'error' => 'Insufficient balance.',
                ]);
            }

            $userDetail->success_wallet = ($userDetail->success_wallet) - ($request->request_amount);
            $userDetail->save();

            $desctiption = 'Withdrawal Request : to self account on ' . Carbon::now()->format('F d, Y, h:i a');
            $msg = 'Withdrawal request submitted successfully to self account, Amount will be transfered shortly after the approval';

            $accountTransaction = AccountTransaction::create([
                'user_id' => $user->id,
                'user_type' => 'retailer',
                'description' => $desctiption,
                'transaction_amount' => -abs($request->request_amount),
                'final_transaction_amount' => -abs($request->request_amount),
                'current_balance' => $userDetail->success_wallet,
                'order_type' => 'other',
                'status' => 0, // pending till not approved
                'type' => 'success'
            ]);

            do {
                $unique_transaction_id = substr(time(), 0, 10);
            } while (WithdrawalRequest::where('transaction_id', $unique_transaction_id)->exists());

            WithdrawalRequest::create([
                'user_id' => $user->id,
                'user_type' => 'retailer',
                'request_type' => $request->request_type,
                'wholesaler_id' => $request->wholesaler_id ?? null,
                'request_amount' => $request->request_amount,
                'remarks' => $request->remarks ?? null,
                'transaction_id' => $unique_transaction_id,
                'account_transaction_id' => $accountTransaction->id,
                'process_by' => $user->id
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'msg' => $msg
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ]);
        }
    }
    //<-------------------- END : withdrawal Request ------------------------>
}

//<----------------- JUST FOR DUMMY ENTRY ---------------->
// AccountTransaction::create([
//     'customer_order_id' => 1,
//     'user_id' => 7,
//     'user_type' => 'retailer',
//     'description' => 'Charges',
//     'amount_type' => 'minus',
//     'transaction_amount' => 0,
//     'charges' => [
//         // 'RTO charges' => 200,
//         // 'Shipping charges' => 50,
//         // 'COD charges' => 150,
//         'Account Charges' => 109
//     ],
//     'final_transaction_amount' => 109,
//     'current_balance' => 100491,
//     'order_type' => 'other',
//     'status' => 1
// ]);
//<----------------- JUST FOR DUMMY ENTRY ---------------->
