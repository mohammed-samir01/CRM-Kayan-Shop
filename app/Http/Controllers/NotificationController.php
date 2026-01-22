<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (isset($notification->data['order_id'])) {
            return redirect()->route('orders.show', $notification->data['order_id']);
        }

        if (isset($notification->data['lead_id'])) {
            return redirect()->route('leads.show', $notification->data['lead_id']);
        }

        if (isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }

        return back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
}
