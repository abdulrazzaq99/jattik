<?php

namespace App\Models;

use App\Traits\FileExport;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use FileExport, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'mobile',
        'country_code',
        'password',
        'address',
        'city',
        'state',
        'postal_code',
        'status',
        'otp_code',
        'otp_expiry',
        'otp_type',
        'email_verified_at',
        'mobile_verified_at',
        'last_login_at',
        'last_order_at',
        'active_subscription_id',
        'subscription_type',
        'is_premium',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'otp_expiry' => 'datetime',
            'last_login_at' => 'datetime',
            'last_order_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the customer's full name.
     */
    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    /**
     * Get the customer's full mobile with country code.
     */
    public function fullMobile(): Attribute
    {
        return new Attribute(
            get: fn () => $this->country_code . $this->mobile,
        );
    }

    /**
     * Check if customer's email is verified.
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if customer's mobile is verified.
     */
    public function isMobileVerified(): bool
    {
        return !is_null($this->mobile_verified_at);
    }

    /**
     * Check if customer is active.
     */
    public function isActive(): bool
    {
        return $this->status == 1;
    }

    /**
     * Get the virtual address for this customer.
     */
    public function virtualAddress()
    {
        return $this->hasOne(VirtualAddress::class)->where('status', 'active');
    }

    /**
     * Get all virtual addresses (including inactive/cancelled).
     */
    public function virtualAddresses()
    {
        return $this->hasMany(VirtualAddress::class);
    }

    /**
     * Get OTP logs for this customer.
     */
    public function otpLogs()
    {
        return $this->hasMany(OtpLog::class);
    }

    /**
     * Get login logs for this customer.
     */
    public function loginLogs()
    {
        return $this->hasMany(CustomerLoginLog::class);
    }

    /**
     * Get couriers where this customer is the sender.
     */
    public function sentCouriers()
    {
        return $this->hasMany(CourierInfo::class, 'sender_customer_id');
    }

    /**
     * Get couriers where this customer is the receiver.
     */
    public function receivedCouriers()
    {
        return $this->hasMany(CourierInfo::class, 'receiver_customer_id');
    }

    /**
     * Scope to filter active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to filter verified customers.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at')
                     ->orWhereNotNull('mobile_verified_at');
    }

    /**
     * Update last order timestamp.
     */
    public function updateLastOrder()
    {
        $this->last_order_at = now();
        $this->save();
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }

    /**
     * Get all subscriptions for this customer.
     */
    public function subscriptions()
    {
        return $this->hasMany(CustomerSubscription::class);
    }

    /**
     * Get the active subscription.
     */
    public function activeSubscription()
    {
        return $this->belongsTo(CustomerSubscription::class, 'active_subscription_id');
    }

    /**
     * Get all payments for this customer.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get insurance policies for this customer.
     */
    public function insurancePolicies()
    {
        return $this->hasMany(InsurancePolicy::class);
    }

    /**
     * Check if customer has active premium subscription.
     */
    public function isPremium(): bool
    {
        return (bool) $this->is_premium;
    }

    /**
     * Check if customer has free insurance.
     */
    public function hasFreeInsurance(): bool
    {
        return $this->isPremium();
    }

    /**
     * Get current subscription plan.
     */
    public function currentPlan()
    {
        return $this->activeSubscription?->plan;
    }

    /**
     * Check if customer can create more shipments.
     */
    public function canCreateShipment(): bool
    {
        if (!$this->activeSubscription) {
            return true; // Pay-per-use customers can always create shipments
        }

        return $this->activeSubscription->canShipMore();
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribeTo(SubscriptionPlan $plan): CustomerSubscription
    {
        $subscription = CustomerSubscription::create([
            'customer_id' => $this->id,
            'subscription_plan_id' => $plan->id,
            'status' => $plan->isFree() ? 'active' : 'pending_payment',
            'auto_renew' => true,
        ]);

        if ($plan->isFree()) {
            $subscription->activate();
        }

        return $subscription;
    }

    /**
     * Scope for premium customers.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope for free tier customers.
     */
    public function scopeFreeTier($query)
    {
        return $query->where('is_premium', false);
    }
}
