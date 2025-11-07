<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\CustomerSubscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Show subscription plans
     */
    public function plans()
    {
        $pageTitle = 'Subscription Plans';
        $customer = Auth::guard('customer')->user();
        $plans = SubscriptionPlan::active()->orderBy('sort_order')->get();
        $currentSubscription = $customer->activeSubscription;

        return view('customer.subscription.plans', compact('pageTitle', 'plans', 'currentSubscription'));
    }

    /**
     * Show current subscription details
     */
    public function current()
    {
        $pageTitle = 'My Subscription';
        $customer = Auth::guard('customer')->user();
        $subscription = $customer->activeSubscription;

        if (!$subscription) {
            return redirect()->route('customer.subscription.plans')->with('info', 'You don\'t have an active subscription.');
        }

        $plan = $subscription->plan;
        $payments = $customer->payments()
            ->where('payable_type', CustomerSubscription::class)
            ->where('payable_id', $subscription->id)
            ->latest()
            ->paginate(10);

        return view('customer.subscription.current', compact('pageTitle', 'subscription', 'plan', 'payments'));
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $customer = Auth::guard('customer')->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if already has active subscription
        if ($customer->activeSubscription && $customer->activeSubscription->isActive()) {
            return back()->with('error', 'You already have an active subscription. Please cancel it first to switch plans.');
        }

        DB::beginTransaction();
        try {
            // Create subscription
            $subscription = $customer->subscribeTo($plan);

            // If free plan, activate immediately
            if ($plan->isFree()) {
                DB::commit();
                return redirect()->route('customer.subscription.current')->with('success', 'Successfully subscribed to ' . $plan->name . '!');
            }

            // For paid plans, create payment and redirect to checkout
            $payment = Payment::create([
                'customer_id' => $customer->id,
                'payable_type' => CustomerSubscription::class,
                'payable_id' => $subscription->id,
                'payment_reference' => Payment::generateReference(),
                'amount' => $plan->price,
                'currency' => 'SAR',
                'payment_method' => 'pending',
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('customer.payment.checkout', $payment->id)
                ->with('success', 'Subscription created! Please complete the payment to activate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $customer = Auth::guard('customer')->user();
        $subscription = $customer->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        // Don't allow cancelling free plan
        if ($subscription->plan->isFree()) {
            return back()->with('error', 'Cannot cancel free plan.');
        }

        $subscription->cancel($request->reason);

        // Update customer status
        $customer->update([
            'active_subscription_id' => null,
            'subscription_type' => 'free',
            'is_premium' => false,
        ]);

        return redirect()->route('customer.subscription.plans')
            ->with('success', 'Subscription cancelled successfully. You will have access until ' . $subscription->expires_at->format('M d, Y'));
    }

    /**
     * Resume cancelled subscription
     */
    public function resume()
    {
        $customer = Auth::guard('customer')->user();
        $subscription = $customer->activeSubscription;

        if (!$subscription || $subscription->status !== 'cancelled') {
            return back()->with('error', 'No cancelled subscription found.');
        }

        if ($subscription->isExpired()) {
            return back()->with('error', 'Subscription has expired. Please subscribe to a new plan.');
        }

        $subscription->update([
            'status' => 'active',
            'auto_renew' => true,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);

        $customer->update([
            'is_premium' => $subscription->plan->isPremium(),
        ]);

        return redirect()->route('customer.subscription.current')
            ->with('success', 'Subscription resumed successfully!');
    }

    /**
     * Toggle auto-renewal
     */
    public function toggleAutoRenew()
    {
        $customer = Auth::guard('customer')->user();
        $subscription = $customer->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $subscription->update([
            'auto_renew' => !$subscription->auto_renew,
        ]);

        $message = $subscription->auto_renew
            ? 'Auto-renewal enabled. Your subscription will renew automatically.'
            : 'Auto-renewal disabled. Your subscription will end on ' . $subscription->expires_at->format('M d, Y');

        return back()->with('success', $message);
    }

    /**
     * Subscription history
     */
    public function history()
    {
        $pageTitle = 'Subscription History';
        $customer = Auth::guard('customer')->user();
        $subscriptions = $customer->subscriptions()
            ->with('plan')
            ->latest()
            ->paginate(15);

        return view('customer.subscription.history', compact('pageTitle', 'subscriptions'));
    }
}
