<?php

namespace App\Http\Controllers;

use App\Models\OrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderNotificationController extends Controller
{
    public function getNotifications()
    {
        $userId = Auth::user()->id; // make sure user is authenticated

        $notifications = OrderNotification::where('user_id', $userId)
                            ->where('is_read',0)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get()
                            ->map(function ($notification) {
            $notification->time_ago = Carbon::parse($notification->created_at)->diffForHumans();
            return $notification;
        });
        // Mark as read
        OrderNotification::where('user_id', $userId)->where('is_read', 0)->update(['is_read' => 1]);
        return response()->json(['notifications' => $notifications]);
    }

    public function getNotificationsCount()
    {
        $userId = Auth::user()->id; // make sure user is authenticated
        $notifications = OrderNotification::where('user_id', $userId)
                        ->where('is_read',0)
                        ->count();

        // Mark as read
        return response()->json(['notifications_count' => $notifications]);
    }
}
