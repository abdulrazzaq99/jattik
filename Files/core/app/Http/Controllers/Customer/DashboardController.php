<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware applied in routes file
    }

    /**
     * Show customer dashboard.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $pageTitle = 'Dashboard';

        // Get customer statistics
        $totalSent = $customer->sentCouriers()->count();
        $totalReceived = $customer->receivedCouriers()->count();
        $activeCouriers = $customer->sentCouriers()
            ->whereIn('status', [0, 1, 2]) // Queue, Dispatch, Delivery Queue
            ->count();
        $deliveredCouriers = $customer->receivedCouriers()
            ->where('status', 3) // Delivered
            ->count();

        // Get virtual address
        $virtualAddress = $customer->virtualAddress;

        // Get recent couriers
        $recentSent = $customer->sentCouriers()
            ->with(['receiverBranch', 'receiverCustomer'])
            ->latest()
            ->take(5)
            ->get();

        $recentReceived = $customer->receivedCouriers()
            ->with(['senderBranch', 'senderCustomer'])
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact(
            'pageTitle',
            'customer',
            'totalSent',
            'totalReceived',
            'activeCouriers',
            'deliveredCouriers',
            'virtualAddress',
            'recentSent',
            'recentReceived'
        ));
    }

    /**
     * Show customer profile.
     */
    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        $pageTitle = 'My Profile';
        $virtualAddress = $customer->virtualAddress;

        return view('customer.profile', compact('pageTitle', 'customer', 'virtualAddress'));
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'address' => 'required|string',
            'city' => 'required|string|max:40',
            'state' => 'required|string|max:40',
            'postal_code' => 'required|string|max:20',
        ]);

        $customer->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
        ]);

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show courier tracking.
     */
    public function trackCourier(Request $request)
    {
        $pageTitle = 'Track Courier';
        $courier = null;

        if ($request->has('tracking_code')) {
            $customer = Auth::guard('customer')->user();

            // Search for courier by tracking code where customer is sender or receiver
            $courier = \App\Models\CourierInfo::where('code', $request->tracking_code)
                ->where(function ($query) use ($customer) {
                    $query->where('sender_customer_id', $customer->id)
                          ->orWhere('receiver_customer_id', $customer->id);
                })
                ->with(['senderBranch', 'receiverBranch', 'senderCustomer', 'receiverCustomer'])
                ->first();

            if (!$courier) {
                return redirect()->route('customer.track')->with('error', 'Courier not found or you do not have access to this courier.');
            }
        }

        return view('customer.track_courier', compact('pageTitle', 'courier'));
    }

    /**
     * Show customer's sent couriers.
     */
    public function sentCouriers()
    {
        $customer = Auth::guard('customer')->user();
        $pageTitle = 'My Sent Couriers';

        $couriers = $customer->sentCouriers()
            ->with(['receiverBranch', 'receiverCustomer'])
            ->latest()
            ->paginate(20);

        return view('customer.sent_couriers', compact('pageTitle', 'couriers'));
    }

    /**
     * Show customer's received couriers.
     */
    public function receivedCouriers()
    {
        $customer = Auth::guard('customer')->user();
        $pageTitle = 'My Received Couriers';

        $couriers = $customer->receivedCouriers()
            ->with(['senderBranch', 'senderCustomer'])
            ->latest()
            ->paginate(20);

        return view('customer.received_couriers', compact('pageTitle', 'couriers'));
    }

    /**
     * Show password change form.
     */
    public function password()
    {
        $pageTitle = 'Change Password';
        return view('customer.password', compact('pageTitle'));
    }

    /**
     * Update customer password.
     */
    public function updatePassword(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $customer->password)) {
            return redirect()->route('customer.password')->with('error', 'Current password is incorrect.');
        }

        // Update password
        $customer->password = Hash::make($request->password);
        $customer->save();

        return redirect()->route('customer.password')->with('success', 'Password changed successfully!');
    }
}
