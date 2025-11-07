<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display all notifications
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $notifications = $this->notificationService->getAllNotifications($customer);

        $pageTitle = 'My Notifications';
        return view('customer.notifications.index', compact('pageTitle', 'notifications'));
    }

    /**
     * Show notification details
     */
    public function show($id)
    {
        $customer = Auth::guard('customer')->user();
        $notification = \App\Models\ShipmentNotification::where('id', $id)
            ->where('customer_id', $customer->id)
            ->with(['courierInfo', 'warehouseHolding'])
            ->firstOrFail();

        // Mark as read
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        $pageTitle = 'Notification Details';
        return view('customer.notifications.show', compact('pageTitle', 'notification'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $customer = Auth::guard('customer')->user();
        $this->notificationService->markAsRead($id, $customer);

        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $customer = Auth::guard('customer')->user();
        $count = $this->notificationService->markAllAsRead($customer);

        return back()->with('success', "{$count} notifications marked as read");
    }

    /**
     * Get unread count (for AJAX)
     */
    public function unreadCount()
    {
        $customer = Auth::guard('customer')->user();
        $count = \App\Models\ShipmentNotification::where('customer_id', $customer->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $customer = Auth::guard('customer')->user();
        $notification = \App\Models\ShipmentNotification::where('id', $id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $notification->delete();

        return redirect()->route('customer.notifications.index')
            ->with('success', 'Notification deleted successfully');
    }
}
