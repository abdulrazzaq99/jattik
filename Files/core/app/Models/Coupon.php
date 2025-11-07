<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'max_discount',
        'min_purchase',
        'applicable_to',
        'applicable_plans',
        'usage_limit',
        'usage_limit_per_customer',
        'total_used',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'applicable_plans' => 'array',
        'usage_limit' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'total_used' => 'integer',
        'status' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    /**
     * Get coupon usage records
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get payments that used this coupon
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        // Check status
        if ($this->status != 1) {
            return false;
        }

        // Check validity dates
        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->total_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if customer can use this coupon
     */
    public function canBeUsedBy(Customer $customer): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check per-customer usage limit
        $customerUsageCount = $this->usages()
            ->where('customer_id', $customer->id)
            ->count();

        return $customerUsageCount < $this->usage_limit_per_customer;
    }

    /**
     * Check if coupon is applicable to payment type
     */
    public function isApplicableTo(string $payableType, $payableId = null): bool
    {
        // Check if applicable to all
        if ($this->applicable_to === 'all') {
            return true;
        }

        // Map payable types to coupon applicable types
        $typeMap = [
            CustomerSubscription::class => 'subscriptions',
            CourierInfo::class => 'shipments',
            InsurancePolicy::class => 'insurance',
        ];

        if (!isset($typeMap[$payableType])) {
            return false;
        }

        if ($this->applicable_to !== $typeMap[$payableType]) {
            return false;
        }

        // For subscriptions, check if specific plans are set
        if ($payableType === CustomerSubscription::class && $this->applicable_plans) {
            $subscription = CustomerSubscription::find($payableId);
            if ($subscription && !in_array($subscription->subscription_plan_id, $this->applicable_plans)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount for given amount
     */
    public function calculateDiscount(float $amount): float
    {
        // Check minimum purchase
        if ($amount < $this->min_purchase) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;

            // Apply max discount limit if set
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }

            return round($discount, 2);
        }

        // Fixed amount discount
        $discount = min($this->value, $amount); // Don't discount more than the total
        return round($discount, 2);
    }

    /**
     * Apply coupon to amount
     */
    public function applyTo(float $amount): array
    {
        $discount = $this->calculateDiscount($amount);
        $finalAmount = $amount - $discount;

        return [
            'original_amount' => $amount,
            'discount_amount' => $discount,
            'final_amount' => max(0, $finalAmount),
        ];
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('total_used');
    }

    /**
     * Record usage by customer
     */
    public function recordUsage(Customer $customer, float $discountAmount, ?int $paymentId = null): CouponUsage
    {
        return CouponUsage::create([
            'coupon_id' => $this->id,
            'customer_id' => $customer->id,
            'payment_id' => $paymentId,
            'discount_amount' => $discountAmount,
        ]);
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1)
                     ->where(function ($q) {
                         $q->whereNull('valid_from')
                           ->orWhere('valid_from', '<=', now());
                     })
                     ->where(function ($q) {
                         $q->whereNull('valid_until')
                           ->orWhere('valid_until', '>=', now());
                     });
    }

    /**
     * Scope for subscription coupons
     */
    public function scopeForSubscriptions($query)
    {
        return $query->whereIn('applicable_to', ['all', 'subscriptions']);
    }

    /**
     * Scope for shipment coupons
     */
    public function scopeForShipments($query)
    {
        return $query->whereIn('applicable_to', ['all', 'shipments']);
    }

    /**
     * Get formatted discount
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%' . ($this->max_discount ? ' (max ' . number_format($this->max_discount, 2) . ' SAR)' : '');
        }

        return number_format($this->value, 2) . ' SAR';
    }
}
