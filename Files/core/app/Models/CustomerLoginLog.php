<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLoginLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'login_at',
        'logout_at',
        'ip_address',
        'user_agent',
        'login_method',
        'session_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
        ];
    }

    /**
     * Get the customer that owns the login log.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Record logout time.
     */
    public function recordLogout()
    {
        $this->logout_at = now();
        $this->save();
    }

    /**
     * Get session duration in minutes.
     */
    public function sessionDuration()
    {
        if (!$this->logout_at) {
            return null;
        }

        return $this->login_at->diffInMinutes($this->logout_at);
    }

    /**
     * Check if session is active (not logged out).
     */
    public function isActive(): bool
    {
        return is_null($this->logout_at);
    }
}
