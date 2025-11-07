<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Show contact form
     */
    public function create()
    {
        $customer = Auth::guard('customer')->user();

        $pageTitle = 'Contact Us';
        return view('customer.contact.create', compact('pageTitle'));
    }

    /**
     * Store contact message
     */
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->check() ? Auth::guard('customer')->user() : null;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $validated['customer_id'] = $customer?->id;
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        ContactMessage::create($validated);

        // Notify admins
        $admins = \App\Models\Admin::where('status', 1)->get();
        foreach ($admins as $admin) {
            notify($admin, 'NEW_CONTACT_MESSAGE', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
            ]);
        }

        return redirect()->back()
            ->with('success', 'Thank you for contacting us! We will respond to you shortly.');
    }

    /**
     * Show customer's contact messages
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $messages = ContactMessage::where('customer_id', $customer->id)
            ->latest()
            ->paginate(20);

        $pageTitle = 'My Messages';
        return view('customer.contact.index', compact('pageTitle', 'messages'));
    }
}
