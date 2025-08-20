<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class CheckRetailerStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */

     public function handle(Request $request, Closure $next)
     {
         Log::info('CheckWholesalerStatus middleware triggered for user:', [
             'user_id' => Auth::id(),
             'user_type' => Auth::check() ? Auth::user()->user_type : null,
             'status' => Auth::check() ? Auth::user()->status : null,
         ]);
     
         if (Auth::check() && Auth::user()->user_type == "3" && Auth::user()->status == 0) {
             Log::warning('User deactivated - logging out:', ['user_id' => Auth::id()]);
     
             Auth::logout();
     
             return redirect()->route('retailer.login')->withErrors([
                 'email' => 'Your account has been deactivated. Please contact support.'
             ]);
         }
     
         return $next($request);
     }
     
    
}
