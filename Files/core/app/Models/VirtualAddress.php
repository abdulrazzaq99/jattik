<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAddress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'address_code',
        'full_address',
        'status',
        'assigned_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the customer that owns the virtual address.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if the virtual address is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Cancel the virtual address.
     */
    public function cancel(string $reason = 'No orders in the past 12 months')
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->save();

        // Send notification to customer
        notify($this->customer, 'CUSTOMER_VIRTUAL_ADDRESS_CANCELLED', [
            'fullname' => $this->customer->fullname,
            'virtual_address_code' => $this->address_code,
            'cancellation_date' => $this->cancelled_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Scope to filter active virtual addresses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter inactive virtual addresses.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope to filter cancelled virtual addresses.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to find virtual addresses for customers with no orders in past year.
     */
    public function scopeInactiveCustomers($query)
    {
        return $query->whereHas('customer', function ($q) {
            $q->where(function ($query) {
                $query->whereNull('last_order_at')
                      ->orWhere('last_order_at', '<', now()->subYear());
            });
        })->where('status', 'active');
    }
}
