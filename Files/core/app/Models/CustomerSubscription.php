<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class CustomerSubscription extends Model
{
    protected $fillable = [
        'customer_id',
        'subscription_plan_id',
        'status',
        'started_at',
        'expires_at',
        'next_billing_date',
        'auto_renew',
        'cancelled_at',
        'cancellation_reason',
        'last_payment_id',
        'shipments_this_period',
        'period_started_at',
    ];

    protected $casts = [
        'auto_renew' => 'boolean',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'period_started_at' => 'datetime',
        'shipments_this_period' => 'integer',
    ];

    /**
     * Get the customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the subscription plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Get all payments for this subscription
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payable_id')->where('payable_type', self::class);
    }

    /**
     * Get the last payment
     */
    public function lastPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'id', 'last_payment_id');
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if subscription is due for renewal
     */
    public function isDueForRenewal(): bool
    {
        return $this->auto_renew &&
               $this->next_billing_date &&
               $this->next_billing_date->isPast();
    }

    /**
     * Check if can ship more
     */
    public function canShipMore(): bool
    {
        $maxShipments = $this->plan->max_shipments_per_month;

        if ($maxShipments === null) {
            return true; // Unlimited
        }

        return $this->shipments_this_period < $maxShipments;
    }

    /**
     * Increment shipment count
     */
    public function incrementShipments(): void
    {
        $this->increment('shipments_this_period');
    }

    /**
     * Reset shipment counter for new period
     */
    public function resetShipmentCounter(): void
    {
        $this->update([
            'shipments_this_period' => 0,
            'period_started_at' => now(),
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'auto_renew' => false,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Activate subscription
     */
    public function activate(): void
    {
        $duration = $this->plan->getDurationInDays();

        $this->update([
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => $duration > 0 ? now()->addDays($duration) : null,
            'next_billing_date' => $duration > 0 ? now()->addDays($duration) : null,
            'period_started_at' => now(),
        ]);

        // Update customer premium status
        $this->customer->update([
            'active_subscription_id' => $this->id,
            'subscription_type' => $this->plan->type,
            'is_premium' => $this->plan->isPremium(),
        ]);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope for due for renewal
     */
    public function scopeDueForRenewal($query)
    {
        return $query->where('auto_renew', true)
                     ->where('next_billing_date', '<=', now())
                     ->where('status', 'active');
    }
}
