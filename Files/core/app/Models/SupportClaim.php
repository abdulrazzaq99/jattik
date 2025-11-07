<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportClaim extends Model
{
    protected $fillable = [
        'claim_number',
        'customer_id',
        'courier_info_id',
        'support_issue_id',
        'claim_type',
        'claimed_amount',
        'approved_amount',
        'claim_details',
        'status',
        'rejection_reason',
        'evidence',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'resolved_at',
        'processing_days',
    ];

    protected $casts = [
        'claimed_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'evidence' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // Claim types
    const TYPE_DAMAGE = 'damage';
    const TYPE_LOSS = 'loss';
    const TYPE_DELAY_COMPENSATION = 'delay_compensation';

    // Status
    const STATUS_PENDING = 0;
    const STATUS_UNDER_REVIEW = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_PAID = 4;

    // SLA: 10 business days
    const SLA_DAYS = 10;

    /**
     * Boot method
     */
    protected static function booted()
    {
        static::creating(function ($claim) {
            if (!$claim->claim_number) {
                $claim->claim_number = 'CLM-' . strtoupper(uniqid());
            }
            if (!$claim->submitted_at) {
                $claim->submitted_at = now();
            }
        });

        static::saved(function ($claim) {
            // Calculate processing days
            if ($claim->submitted_at && $claim->resolved_at) {
                $claim->processing_days = $claim->submitted_at->diffInDays($claim->resolved_at);
                $claim->saveQuietly();
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

    public function supportIssue(): BelongsTo
    {
        return $this->belongsTo(SupportIssue::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', self::STATUS_UNDER_REVIEW);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeNearingSLA($query)
    {
        $deadline = now()->subDays(self::SLA_DAYS - 2); // 2 days before deadline
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_UNDER_REVIEW])
                     ->where('submitted_at', '<=', $deadline);
    }

    public function scopeOverdueSLA($query)
    {
        $deadline = now()->subDays(self::SLA_DAYS);
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_UNDER_REVIEW])
                     ->where('submitted_at', '<=', $deadline);
    }

    /**
     * Approve claim
     */
    public function approve(float $amount, int $reviewerId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_amount' => $amount,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Reject claim
     */
    public function reject(string $reason, int $reviewerId): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'resolved_at' => now(),
        ]);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Get days until SLA deadline
     */
    public function getDaysUntilDeadlineAttribute(): int
    {
        if (!$this->submitted_at) {
            return self::SLA_DAYS;
        }

        $daysElapsed = $this->submitted_at->diffInDays(now());
        return max(0, self::SLA_DAYS - $daysElapsed);
    }

    /**
     * Check if overdue
     */
    public function isOverdue(): bool
    {
        return $this->days_until_deadline <= 0
               && in_array($this->status, [self::STATUS_PENDING, self::STATUS_UNDER_REVIEW]);
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => '<span class="badge badge--warning">Pending</span>',
            self::STATUS_UNDER_REVIEW => '<span class="badge badge--primary">Under Review</span>',
            self::STATUS_APPROVED => '<span class="badge badge--success">Approved</span>',
            self::STATUS_REJECTED => '<span class="badge badge--danger">Rejected</span>',
            self::STATUS_PAID => '<span class="badge badge--info">Paid</span>',
            default => '<span class="badge badge--secondary">Unknown</span>',
        };
    }
}
