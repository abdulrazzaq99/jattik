<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\CourierTrackingService;
use App\Models\CourierTrackingEvent;
use App\Models\CourierInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingManagementController extends Controller
{
    protected CourierTrackingService $trackingService;

    public function __construct(CourierTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Show tracking dashboard
     */
    public function dashboard()
    {
        $staff = Auth::user();

        // Get statistics
        $totalShipments = CourierInfo::where('sender_branch_id', $staff->branch_id)
            ->orWhere('receiver_branch_id', $staff->branch_id)
            ->count();

        $activeShipments = CourierInfo::whereIn('status', [1, 2])
            ->where(function($query) use ($staff) {
                $query->where('sender_branch_id', $staff->branch_id)
                      ->orWhere('receiver_branch_id', $staff->branch_id);
            })
            ->count();

        $exceptionsToday = CourierTrackingEvent::exceptions()
            ->whereHas('courierInfo', function($query) use ($staff) {
                $query->where('sender_branch_id', $staff->branch_id)
                      ->orWhere('receiver_branch_id', $staff->branch_id);
            })
            ->whereDate('created_at', today())
            ->count();

        $unnotifiedExceptions = CourierTrackingEvent::exceptions()
            ->unnotified()
            ->whereHas('courierInfo', function($query) use ($staff) {
                $query->where('sender_branch_id', $staff->branch_id)
                      ->orWhere('receiver_branch_id', $staff->branch_id);
            })
            ->count();

        $statistics = compact('totalShipments', 'activeShipments', 'exceptionsToday', 'unnotifiedExceptions');

        $pageTitle = 'Tracking Management Dashboard';
        return view('staff.tracking.dashboard', compact('pageTitle', 'statistics'));
    }

    /**
     * Show all tracking events
     */
    public function events()
    {
        $staff = Auth::user();

        $events = CourierTrackingEvent::whereHas('courierInfo', function($query) use ($staff) {
                $query->where('sender_branch_id', $staff->branch_id)
                      ->orWhere('receiver_branch_id', $staff->branch_id);
            })
            ->with(['courierInfo', 'courierConfiguration'])
            ->latest('event_time')
            ->paginate(50);

        $pageTitle = 'Tracking Events';
        return view('staff.tracking.events', compact('pageTitle', 'events'));
    }

    /**
     * Show exceptions
     */
    public function exceptions()
    {
        $staff = Auth::user();

        $exceptions = CourierTrackingEvent::exceptions()
            ->whereHas('courierInfo', function($query) use ($staff) {
                $query->where('sender_branch_id', $staff->branch_id)
                      ->orWhere('receiver_branch_id', $staff->branch_id);
            })
            ->with(['courierInfo.senderCustomer', 'courierInfo.receiverCustomer'])
            ->latest('event_time')
            ->paginate(20);

        $pageTitle = 'Tracking Exceptions';
        return view('staff.tracking.exceptions', compact('pageTitle', 'exceptions'));
    }

    /**
     * Resolve exception
     */
    public function resolveException(CourierTrackingEvent $event)
    {
        $staff = Auth::user();

        // Verify event belongs to staff's branch
        $courier = $event->courierInfo;
        if ($courier->sender_branch_id !== $staff->branch_id && $courier->receiver_branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $event->update(['customer_notified' => true]);

        return back()->with('success', 'Exception marked as resolved');
    }

    /**
     * Refresh tracking for a shipment
     */
    public function refreshTracking(CourierInfo $courier)
    {
        $staff = Auth::user();

        // Verify courier belongs to staff's branch
        if ($courier->sender_branch_id !== $staff->branch_id && $courier->receiver_branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $events = $this->trackingService->fetchTrackingEvents($courier);

            if (count($events) > 0) {
                return back()->with('success', count($events) . ' tracking events fetched');
            }

            return back()->with('info', 'No new tracking events found');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch tracking: ' . $e->getMessage());
        }
    }
}
