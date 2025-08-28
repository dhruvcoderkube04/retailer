<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class AdminAuthController extends Controller
{
    public function loginWithToken($token)
    {
       $record = DB::table('login_tokens')
        ->where('token', $token)
        ->where('expires_at', '>', now())
        ->first();

        if (!$record) {
            abort(403, 'Token expired or invalid.');
        }

        $user = User::find($record->user_id);
        if (!$user) {
            abort(404, 'User not found.');
        }

        Auth::login($user);
        session(['admin_mode' => true]);

        // Optional: delete token after use
        DB::table('login_tokens')->where('token', $token)->delete();

        return redirect()->route('retailer.dashboard');
    }
}
