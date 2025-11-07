<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'customer_id',
        'payable_type',
        'payable_id',
        'payment_reference',
        'amount',
        'original_amount',
        'discount_amount',
        'coupon_id',
        'coupon_code',
        'currency',
        'payment_method',
        'transaction_id',
        'status',
        'gateway_response',
        'failure_reason',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the payable model (subscription, courier, insurance)
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the coupon used
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Check if payment has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Get savings amount
     */
    public function getSavingsAttribute(): float
    {
        return $this->discount_amount ?? 0;
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted(string $transactionId, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'gateway_response' => json_encode($gatewayResponse),
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $reason, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'gateway_response' => json_encode($gatewayResponse),
        ]);
    }

    /**
     * Refund payment
     */
    public function refund(): void
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Generate unique payment reference
     */
    public static function generateReference(): string
    {
        return 'PAY-' . strtoupper(uniqid() . bin2hex(random_bytes(4)));
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for subscription payments
     */
    public function scopeForSubscriptions($query)
    {
        return $query->where('payable_type', CustomerSubscription::class);
    }

    /**
     * Scope for courier payments
     */
    public function scopeForCouriers($query)
    {
        return $query->where('payable_type', CourierInfo::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
}
