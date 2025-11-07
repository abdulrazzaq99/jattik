<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentNotification extends Model
{
    protected $fillable = [
        'customer_id',
        'courier_info_id',
        'warehouse_holding_id',
        'notification_type',
        'title',
        'message',
        'metadata',
        'sent_via_email',
        'sent_via_sms',
        'sent_via_whatsapp',
        'is_read',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_via_email' => 'boolean',
        'sent_via_sms' => 'boolean',
        'sent_via_whatsapp' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Notification types
    const TYPE_FACILITY_ARRIVAL = 'facility_arrival';
    const TYPE_DISPATCHED = 'dispatched';
    const TYPE_TRACKING_LINK = 'tracking_link';
    const TYPE_FEE_QUOTE = 'fee_quote';
    const TYPE_DELIVERED = 'delivered';
    const TYPE_EXCEPTION = 'exception';

    /**
     * Relationships
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function courierInfo(): BelongsTo
    {
        return $this->belongsTo(CourierInfo::class);
    }

    public function warehouseHolding(): BelongsTo
    {
        return $this->belongsTo(WarehouseHolding::class);
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark as sent
     */
    public function markAsSent(array $channels = []): void
    {
        $update = ['sent_at' => now()];

        if (in_array('email', $channels)) {
            $update['sent_via_email'] = true;
        }
        if (in_array('sms', $channels)) {
            $update['sent_via_sms'] = true;
        }
        if (in_array('whatsapp', $channels)) {
            $update['sent_via_whatsapp'] = true;
        }

        $this->update($update);
    }
}
