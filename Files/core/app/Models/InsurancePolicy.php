<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsurancePolicy extends Model
{
    protected $fillable = [
        'courier_info_id',
        'customer_id',
        'policy_number',
        'coverage_amount',
        'premium_amount',
        'is_free',
        'subscription_id',
        'status',
        'claim_amount',
        'claimed_at',
        'claim_notes',
        'purchased_at',
        'expires_at',
    ];

    protected $casts = [
        'coverage_amount' => 'decimal:2',
        'premium_amount' => 'decimal:2',
        'claim_amount' => 'decimal:2',
        'is_free' => 'boolean',
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    /**
     * Get the courier shipment
     */
    public function courier(): BelongsTo
    {
        return $this->belongsTo(CourierInfo::class, 'courier_info_id');
    }

    /**
     * Get the customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the subscription (if insurance is free)
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(CustomerSubscription::class, 'subscription_id');
    }

    /**
     * Check if policy is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Check if policy was claimed
     */
    public function isClaimed(): bool
    {
        return $this->status === 'claimed';
    }

    /**
     * File a claim
     */
    public function fileClaim(float $amount, string $notes = null): void
    {
        $claimAmount = min($amount, $this->coverage_amount);

        $this->update([
            'status' => 'claimed',
            'claim_amount' => $claimAmount,
            'claimed_at' => now(),
            'claim_notes' => $notes,
        ]);
    }

    /**
     * Generate unique policy number
     */
    public static function generatePolicyNumber(): string
    {
        return 'INS-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Create free insurance for premium subscriber
     */
    public static function createFreeInsurance(
        int $courierId,
        int $customerId,
        int $subscriptionId,
        float $coverageAmount
    ): self {
        return self::create([
            'courier_info_id' => $courierId,
            'customer_id' => $customerId,
            'subscription_id' => $subscriptionId,
            'policy_number' => self::generatePolicyNumber(),
            'coverage_amount' => $coverageAmount,
            'premium_amount' => 0.00,
            'is_free' => true,
            'status' => 'active',
            'purchased_at' => now(),
            'expires_at' => now()->addYear(), // 1 year validity
        ]);
    }

    /**
     * Create paid insurance
     */
    public static function createPaidInsurance(
        int $courierId,
        int $customerId,
        float $coverageAmount,
        float $premiumAmount
    ): self {
        return self::create([
            'courier_info_id' => $courierId,
            'customer_id' => $customerId,
            'policy_number' => self::generatePolicyNumber(),
            'coverage_amount' => $coverageAmount,
            'premium_amount' => $premiumAmount,
            'is_free' => false,
            'status' => 'active',
            'purchased_at' => now(),
            'expires_at' => now()->addYear(),
        ]);
    }

    /**
     * Scope for active policies
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for claimed policies
     */
    public function scopeClaimed($query)
    {
        return $query->where('status', 'claimed');
    }

    /**
     * Scope for free policies
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Get formatted coverage
     */
    public function getFormattedCoverageAttribute(): string
    {
        return number_format($this->coverage_amount, 2) . ' SAR';
    }

    /**
     * Get formatted premium
     */
    public function getFormattedPremiumAttribute(): string
    {
        if ($this->is_free) {
            return 'Free';
        }

        return number_format($this->premium_amount, 2) . ' SAR';
    }
}
