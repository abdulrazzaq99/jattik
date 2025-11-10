<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'email',
        'mobile',
        'otp_code',
        'otp_type',
        'purpose',
        'sent_at',
        'verified_at',
        'expires_at',
        'attempts',
        'status',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'verified_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the customer that owns the OTP log.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if OTP is expired.
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    /**
     * Check if OTP is verified.
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Check if max attempts reached (limit to 3).
     */
    public function maxAttemptsReached(): bool
    {
        return $this->attempts >= 3;
    }

    /**
     * Increment verification attempts.
     */
    public function incrementAttempts()
    {
        $this->attempts++;

        if ($this->maxAttemptsReached()) {
            $this->status = 'failed';
        }

        $this->save();
    }

    /**
     * Mark OTP as verified.
     */
    public function markAsVerified()
    {
        $this->status = 'verified';
        $this->verified_at = now();
        $this->save();
    }

    /**
     * Mark OTP as expired.
     */
    public function markAsExpired()
    {
        $this->status = 'expired';
        $this->save();
    }

    /**
     * Scope to filter pending OTPs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter verified OTPs.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope to filter expired OTPs.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope to get recent OTP for a contact (email or mobile).
     */
    public function scopeRecentFor($query, string $contact, string $purpose)
    {
        return $query->where(function ($q) use ($contact) {
            $q->where('email', $contact)
              ->orWhere('mobile', $contact);
        })
        ->where('purpose', $purpose)
        ->where('status', 'pending')
        ->where('expires_at', '>', now())
        ->latest('sent_at');
    }
}
