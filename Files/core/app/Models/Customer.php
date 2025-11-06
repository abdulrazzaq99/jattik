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
}
