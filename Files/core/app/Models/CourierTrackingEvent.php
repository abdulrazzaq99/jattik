<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierTrackingEvent extends Model
{
    protected $fillable = [
        'courier_info_id',
        'courier_configuration_id',
        'tracking_number',
        'carrier_name',
        'event_type',
        'status_code',
        'description',
        'location',
        'event_time',
        'is_exception',
        'exception_type',
        'exception_details',
        'customer_notified',
        'raw_data',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'is_exception' => 'boolean',
        'customer_notified' => 'boolean',
        'raw_data' => 'array',
    ];

    // Event types
    const EVENT_PICKED_UP = 'picked_up';
    const EVENT_IN_TRANSIT = 'in_transit';
    const EVENT_OUT_FOR_DELIVERY = 'out_for_delivery';
    const EVENT_DELIVERED = 'delivered';
    const EVENT_EXCEPTION = 'exception';
    const EVENT_RETURNED = 'returned';

    // Exception types
    const EXCEPTION_DELAY = 'delay';
    const EXCEPTION_WRONG_ADDRESS = 'wrong_address';
    const EXCEPTION_DAMAGED = 'damaged';
    const EXCEPTION_LOST = 'lost';
    const EXCEPTION_REFUSED = 'refused';

    /**
     * Relationships
     */
    public function courierInfo(): BelongsTo
    {
        return $this->belongsTo(CourierInfo::class);
    }

    public function courierConfiguration(): BelongsTo
    {
        return $this->belongsTo(CourierConfiguration::class);
    }

    /**
     * Scopes
     */
    public function scopeExceptions($query)
    {
        return $query->where('is_exception', true);
    }

    public function scopeUnnotified($query)
    {
        return $query->where('is_exception', true)
                     ->where('customer_notified', false);
    }

    public function scopeByCarrier($query, string $carrier)
    {
        return $query->where('carrier_name', $carrier);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('event_time', '>=', now()->subDays($days));
    }

    /**
     * Mark customer as notified
     */
    public function markAsNotified(): void
    {
        $this->update(['customer_notified' => true]);
    }

    /**
     * Check if this is a delivery event
     */
    public function isDelivered(): bool
    {
        return $this->event_type === self::EVENT_DELIVERED;
    }

    /**
     * Get formatted event time
     */
    public function getFormattedEventTimeAttribute(): string
    {
        return $this->event_time->format('M d, Y h:i A');
    }
}
