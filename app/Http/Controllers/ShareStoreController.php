<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShareLink;
use App\Models\ShareStoreToken;
use App\Models\User;

class ShareStoreController extends Controller
{
    public function accessStore($wholesalerId, $token) { 
        $tokenRecord = ShareStoreToken::where('token', $token)->first(); 
        if (!$tokenRecord) { 
            abort(404, 'Invalid link'); 
        } 
        
        // if ($tokenRecord->expires_at && $tokenRecord->expires_at < now()) { 
        //     abort(403, 'This link has expired.'); 
        // } 
        $shareLink = ShareLink::where('token_id', $tokenRecord->id)->where('wholesaler_id', $wholesalerId)->first(); 
        if (!$shareLink) { 
            abort(404, 'Invalid share link'); 
        } 
        if (!auth()->check()) { 
            session([
                'redirect_after_login' => route('access.store', [ 
                    'wholesalerId' => $wholesalerId, 
                    'token' => $token 
                ])
            ]); 
            return redirect()->route('retailer.login'); 
        } 

        $retailerId = auth()->id();

        ShareLink::updateOrCreate(
            [
                'wholesaler_id' => $wholesalerId,
                'retailer_id'   => $retailerId,
            ],
            [
                'token_id'      => $tokenRecord->id,
                'status'        => 1,
            ]
        );

        $tokenRecord->update(['status' => 1]); 

        return redirect()->route('wholesaler.request.category', encryptId($wholesalerId));
    }
}
