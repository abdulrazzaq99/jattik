<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class WarehouseHolding extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_HOLDING = 0;
    const STATUS_READY = 1;
    const STATUS_SHIPPED = 2;
    const STATUS_EXPIRED = 3;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'holding_code',
        'received_date',
        'scheduled_ship_date',
        'actual_ship_date',
        'max_holding_date',
        'status',
        'total_weight',
        'total_volume',
        'package_count',
        'notes',
        'consolidated_by_staff_id',
        'consolidated_at',
    ];

    protected $casts = [
        'received_date' => 'date',
        'scheduled_ship_date' => 'date',
        'actual_ship_date' => 'date',
        'max_holding_date' => 'date',
        'total_weight' => 'decimal:2',
        'total_volume' => 'decimal:2',
        'consolidated_at' => 'datetime',
    ];

    /**
     * Boot method.
     */
    protected static function booted()
    {
        static::creating(function ($holding) {
            // Generate unique holding code
            if (empty($holding->holding_code)) {
                $holding->holding_code = 'WH' . getTrx(10);
            }

            // Set max holding date (90 days from received date)
            if (empty($holding->max_holding_date)) {
                $holding->max_holding_date = Carbon::parse($holding->received_date)->addDays(90);
            }
        });
    }

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the staff who consolidated.
     */
    public function consolidatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consolidated_by_staff_id');
    }

    /**
     * Get packages in this holding.
     */
    public function packages(): HasMany
    {
        return $this->hasMany(WarehousePackage::class);
    }

    /**
     * Get courier infos linked to this holding.
     */
    public function courierInfos(): HasMany
    {
        return $this->hasMany(CourierInfo::class);
    }

    /**
     * Get shipping quotes for this holding.
     */
    public function shippingQuotes(): HasMany
    {
        return $this->hasMany(ShippingQuote::class);
    }

    /**
     * Scope for holding status.
     */
    public function scopeHolding($query)
    {
        return $query->where('status', self::STATUS_HOLDING);
    }

    /**
     * Scope for ready status.
     */
    public function scopeReady($query)
    {
        return $query->where('status', self::STATUS_READY);
    }

    /**
     * Scope for shipped status.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    /**
     * Scope for expired holdings.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Check if holding is expired.
     */
    public function isExpired(): bool
    {
        return Carbon::today()->greaterThan($this->max_holding_date);
    }

    /**
     * Get days remaining until expiry.
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, Carbon::today()->diffInDays($this->max_holding_date, false));
    }

    /**
     * Update totals from packages.
     */
    public function updateTotals(): void
    {
        $this->total_weight = $this->packages()->sum('weight');
        $this->total_volume = $this->packages()->sum('volume');
        $this->package_count = $this->packages()->count();
        $this->save();
    }

    /**
     * Get status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_HOLDING => '<span class="badge badge-warning">Holding</span>',
            self::STATUS_READY => '<span class="badge badge-info">Ready</span>',
            self::STATUS_SHIPPED => '<span class="badge badge-success">Shipped</span>',
            self::STATUS_EXPIRED => '<span class="badge badge-danger">Expired</span>',
            default => '<span class="badge badge-secondary">Unknown</span>',
        };
    }
}
