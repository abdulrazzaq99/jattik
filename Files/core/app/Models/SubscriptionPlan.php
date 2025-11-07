<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'price',
        'billing_period',
        'billing_cycle',
        'features',
        'includes_insurance',
        'insurance_coverage',
        'max_shipments_per_month',
        'status',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'insurance_coverage' => 'decimal:2',
        'includes_insurance' => 'boolean',
        'status' => 'integer',
        'max_shipments_per_month' => 'integer',
        'billing_cycle' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get subscriptions using this plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(CustomerSubscription::class, 'subscription_plan_id');
    }

    /**
     * Get active subscriptions
     */
    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()->where('status', 'active');
    }

    /**
     * Check if plan is free
     */
    public function isFree(): bool
    {
        return $this->type === 'free' || $this->price == 0;
    }

    /**
     * Check if plan is premium (includes insurance)
     */
    public function isPremium(): bool
    {
        return $this->includes_insurance;
    }

    /**
     * Get plan duration in days
     */
    public function getDurationInDays(): int
    {
        if ($this->billing_period === 'month') {
            return 30 * $this->billing_cycle;
        } elseif ($this->billing_period === 'year') {
            return 365 * $this->billing_cycle;
        }

        return 0; // Free plans don't expire
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for premium plans
     */
    public function scopePremium($query)
    {
        return $query->where('includes_insurance', true);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price == 0) {
            return 'Free';
        }

        return number_format($this->price, 2) . ' SAR';
    }
}
