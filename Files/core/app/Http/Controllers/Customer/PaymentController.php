<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\CustomerSubscription;
use App\Models\CourierInfo;
use App\Models\InsurancePolicy;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Show checkout page
     */
    public function checkout($paymentId)
    {
        $pageTitle = 'Checkout';
        $customer = Auth::guard('customer')->user();
        $payment = Payment::where('customer_id', $customer->id)
            ->findOrFail($paymentId);

        if ($payment->isCompleted()) {
            return redirect()->route('customer.payment.success', $payment->id)
                ->with('info', 'This payment has already been completed.');
        }

        if ($payment->isFailed()) {
            return redirect()->route('customer.payment.failed', $payment->id)
                ->with('error', 'This payment has failed. Please try again.');
        }

        // Get payment details based on type
        $paymentFor = $this->getPaymentDetails($payment);

        return view('customer.payment.checkout', compact('pageTitle', 'payment', 'paymentFor'));
    }

    /**
     * Validate and apply coupon
     */
    public function applyCoupon(Request $request, $paymentId)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();
        $payment = Payment::where('customer_id', $customer->id)
            ->findOrFail($paymentId);

        // Find coupon
        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ]);
        }

        // Validate coupon
        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has expired or is no longer valid.',
            ]);
        }

        if (!$coupon->canBeUsedBy($customer)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already used this coupon.',
            ]);
        }

        if (!$coupon->isApplicableTo($payment->payable_type, $payment->payable_id)) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon cannot be applied to this purchase.',
            ]);
        }

        // Calculate discount
        $discountData = $coupon->applyTo($payment->amount);

        if ($discountData['discount_amount'] == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum purchase amount not met. Minimum: ' . number_format($coupon->min_purchase, 2) . ' SAR',
            ]);
        }

        // Apply coupon to payment
        $payment->update([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'original_amount' => $discountData['original_amount'],
            'discount_amount' => $discountData['discount_amount'],
            'amount' => $discountData['final_amount'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'data' => [
                'original_amount' => number_format($discountData['original_amount'], 2),
                'discount_amount' => number_format($discountData['discount_amount'], 2),
                'final_amount' => number_format($discountData['final_amount'], 2),
                'coupon_code' => $coupon->code,
                'savings' => number_format($discountData['discount_amount'], 2),
            ],
        ]);
    }

    /**
     * Remove coupon
     */
    public function removeCoupon($paymentId)
    {
        $customer = Auth::guard('customer')->user();
        $payment = Payment::where('customer_id', $customer->id)
            ->findOrFail($paymentId);

        if (!$payment->coupon_id) {
            return response()->json([
                'success' => false,
                'message' => 'No coupon applied to this payment.',
            ]);
        }

        // Restore original amount
        $originalAmount = $payment->original_amount ?? ($payment->amount + $payment->discount_amount);

        $payment->update([
            'coupon_id' => null,
            'coupon_code' => null,
            'amount' => $originalAmount,
            'original_amount' => null,
            'discount_amount' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed.',
            'data' => [
                'amount' => number_format($originalAmount, 2),
            ],
        ]);
    }

    /**
     * Process payment
     */
    public function process(Request $request, $paymentId)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,paypal,credit_card',
        ]);

        $customer = Auth::guard('customer')->user();
        $payment = Payment::where('customer_id', $customer->id)
            ->findOrFail($paymentId);

        if ($payment->isCompleted()) {
            return redirect()->route('customer.payment.success', $payment->id)
                ->with('info', 'This payment has already been completed.');
        }

        DB::beginTransaction();
        try {
            // Update payment method
            $payment->update([
                'payment_method' => $request->payment_method,
                'status' => 'processing',
            ]);

            // TODO: Integrate with actual payment gateway
            // For now, we'll simulate a successful payment
            $transactionId = 'TXN-' . strtoupper(uniqid());
            $gatewayResponse = [
                'status' => 'success',
                'transaction_id' => $transactionId,
                'timestamp' => now()->toIso8601String(),
            ];

            // Mark payment as completed
            $payment->markAsCompleted($transactionId, $gatewayResponse);

            // Record coupon usage if coupon was applied
            if ($payment->coupon_id) {
                $coupon = Coupon::find($payment->coupon_id);
                if ($coupon) {
                    $coupon->incrementUsage();
                    $coupon->recordUsage($customer, $payment->discount_amount, $payment->id);
                }
            }

            // Activate the subscription or courier payment
            $this->activatePayable($payment);

            DB::commit();

            return redirect()->route('customer.payment.success', $payment->id)
                ->with('success', 'Payment completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            $payment->markAsFailed($e->getMessage());

            return redirect()->route('customer.payment.failed', $payment->id)
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Payment success page
     */
    public function success($paymentId)
    {
        $pageTitle = 'Payment Successful';
        $customer = Auth::guard('customer')->user();
        $payment = Payment::where('customer_id', $customer->id)
            ->findOrFail($paymentId);

        $paymentFor = $this->getPaymentDetails($payment);

        return view('customer.payment.success', compact('pageTitle', 'payment', 'paymentFor'));
    }

    /**
     * Payment failed page
     */
    public function failed($paymentId)
    {
        $pageTitle = 'Payment Failed';
        $customer = Auth::guard('customer')->user();
        $payment = Payment::where('customer_id', $customer->id)
            ->findOrFail($paymentId);

        $paymentFor = $this->getPaymentDetails($payment);

        return view('customer.payment.failed', compact('pageTitle', 'payment', 'paymentFor'));
    }

    /**
     * Payment history
     */
    public function history()
    {
        $pageTitle = 'Payment History';
        $customer = Auth::guard('customer')->user();
        $payments = $customer->payments()
            ->with('payable')
            ->latest()
            ->paginate(20);

        return view('customer.payment.history', compact('pageTitle', 'payments'));
    }

    /**
     * Show payment invoice
     */
    public function invoice($paymentId)
    {
        $pageTitle = 'Payment Invoice';
        $customer = Auth::guard('customer')->user();
        $payment = Payment::where('customer_id', $customer->id)
            ->findOrFail($paymentId);

        $paymentFor = $this->getPaymentDetails($payment);

        return view('customer.payment.invoice', compact('pageTitle', 'payment', 'paymentFor', 'customer'));
    }

    /**
     * Get payment details based on payable type
     */
    private function getPaymentDetails(Payment $payment)
    {
        $payable = $payment->payable;

        $details = [
            'type' => '',
            'description' => '',
            'reference' => '',
            'item' => $payable,
        ];

        if ($payment->payable_type === CustomerSubscription::class) {
            $details['type'] = 'Subscription';
            $details['description'] = $payable->plan->name . ' Subscription';
            $details['reference'] = 'SUB-' . $payable->id;
        } elseif ($payment->payable_type === CourierInfo::class) {
            $details['type'] = 'Courier Shipment';
            $details['description'] = 'Shipment #' . $payable->code;
            $details['reference'] = $payable->code;
        } elseif ($payment->payable_type === InsurancePolicy::class) {
            $details['type'] = 'Insurance';
            $details['description'] = 'Insurance Policy #' . $payable->policy_number;
            $details['reference'] = $payable->policy_number;
        }

        return $details;
    }

    /**
     * Activate payable after successful payment
     */
    private function activatePayable(Payment $payment)
    {
        $payable = $payment->payable;

        if ($payment->payable_type === CustomerSubscription::class) {
            // Activate subscription
            $payable->activate();
            $payable->update(['last_payment_id' => $payment->id]);
        } elseif ($payment->payable_type === CourierInfo::class) {
            // Mark courier as paid
            $payable->update([
                'payment_status' => 'paid',
                'payment_id' => $payment->id,
                'paid_at' => now(),
            ]);
        }
    }
}
