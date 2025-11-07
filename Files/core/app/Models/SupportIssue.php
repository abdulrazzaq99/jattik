<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupportIssue extends Model
{
    protected $fillable = [
        'issue_number',
        'customer_id',
        'courier_info_id',
        'issue_type',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to',
        'assigned_at',
        'resolved_at',
        'closed_at',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Issue types
    const TYPE_WRONG_PARCEL = 'wrong_parcel';
    const TYPE_DAMAGED = 'damaged';
    const TYPE_MISSING = 'missing';
    const TYPE_DELAY = 'delay';
    const TYPE_OTHER = 'other';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Status
    const STATUS_OPEN = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_RESOLVED = 2;
    const STATUS_CLOSED = 3;

    /**
     * Boot method
     */
    protected static function booted()
    {
        static::creating(function ($issue) {
            if (!$issue->issue_number) {
                $issue->issue_number = 'ISS-' . strtoupper(uniqid());
            }
        });
    }

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

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function claim(): HasOne
    {
        return $this->hasOne(SupportClaim::class);
    }

    /**
     * Scopes
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Assign to staff member
     */
    public function assignTo(int $staffId): void
    {
        $this->update([
            'assigned_to' => $staffId,
            'assigned_at' => now(),
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Mark as resolved
     */
    public function markAsResolved(): void
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Close issue
     */
    public function close(): void
    {
        $this->update([
            'status' => self::STATUS_CLOSED,
            'closed_at' => now(),
        ]);
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => '<span class="badge badge--warning">Open</span>',
            self::STATUS_IN_PROGRESS => '<span class="badge badge--primary">In Progress</span>',
            self::STATUS_RESOLVED => '<span class="badge badge--success">Resolved</span>',
            self::STATUS_CLOSED => '<span class="badge badge--dark">Closed</span>',
            default => '<span class="badge badge--secondary">Unknown</span>',
        };
    }

    /**
     * Get priority badge
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => '<span class="badge badge--info">Low</span>',
            self::PRIORITY_MEDIUM => '<span class="badge badge--primary">Medium</span>',
            self::PRIORITY_HIGH => '<span class="badge badge--warning">High</span>',
            self::PRIORITY_URGENT => '<span class="badge badge--danger">Urgent</span>',
            default => '<span class="badge badge--secondary">Normal</span>',
        };
    }
}
