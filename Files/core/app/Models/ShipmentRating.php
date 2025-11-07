<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentRating extends Model
{
    protected $fillable = [
        'customer_id',
        'courier_info_id',
        'overall_rating',
        'speed_rating',
        'packaging_rating',
        'communication_rating',
        'value_rating',
        'comment',
        'tags',
        'would_recommend',
        'is_public',
        'is_verified',
        'is_approved',
        'admin_notes',
    ];

    protected $casts = [
        'tags' => 'array',
        'would_recommend' => 'boolean',
        'is_public' => 'boolean',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
    ];

    /**
     * Boot method
     */
    protected static function booted()
    {
        static::creating(function ($rating) {
            // Only allow rating of delivered shipments
            $courierInfo = CourierInfo::find($rating->courier_info_id);
            if ($courierInfo && $courierInfo->status != 3) { // 3 = delivered
                throw new \Exception('Can only rate delivered shipments');
            }
            $rating->is_verified = true;
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

    /**
     * Scopes
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true)
                     ->where('is_approved', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeHighRated($query, int $minRating = 4)
    {
        return $query->where('overall_rating', '>=', $minRating);
    }

    public function scopeWithRecommendation($query)
    {
        return $query->where('would_recommend', true);
    }

    /**
     * Approve rating
     */
    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    /**
     * Reject/hide rating
     */
    public function reject(string $reason = null): void
    {
        $this->update([
            'is_approved' => false,
            'admin_notes' => $reason,
        ]);
    }

    /**
     * Calculate average rating for a courier
     */
    public static function averageForCourier(int $courierId): array
    {
        $ratings = self::where('courier_info_id', $courierId)
                       ->approved()
                       ->get();

        if ($ratings->isEmpty()) {
            return [
                'overall' => 0,
                'speed' => 0,
                'packaging' => 0,
                'communication' => 0,
                'value' => 0,
                'count' => 0,
            ];
        }

        return [
            'overall' => round($ratings->avg('overall_rating'), 1),
            'speed' => round($ratings->avg('speed_rating'), 1),
            'packaging' => round($ratings->avg('packaging_rating'), 1),
            'communication' => round($ratings->avg('communication_rating'), 1),
            'value' => round($ratings->avg('value_rating'), 1),
            'count' => $ratings->count(),
        ];
    }

    /**
     * Calculate average rating for a customer
     */
    public static function averageForCustomer(int $customerId): float
    {
        return (float) self::where('customer_id', $customerId)
                           ->approved()
                           ->avg('overall_rating') ?? 0;
    }

    /**
     * Get star display
     */
    public function getStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->overall_rating) {
                $stars .= '<i class="las la-star text-warning"></i>';
            } else {
                $stars .= '<i class="lar la-star text-muted"></i>';
            }
        }
        return $stars;
    }

    /**
     * Get rating badge
     */
    public function getRatingBadgeAttribute(): string
    {
        if ($this->overall_rating >= 4.5) {
            return '<span class="badge badge--success">Excellent</span>';
        } elseif ($this->overall_rating >= 3.5) {
            return '<span class="badge badge--primary">Good</span>';
        } elseif ($this->overall_rating >= 2.5) {
            return '<span class="badge badge--warning">Average</span>';
        } else {
            return '<span class="badge badge--danger">Poor</span>';
        }
    }
}
