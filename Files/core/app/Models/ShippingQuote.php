<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ShippingQuote extends Model
{
    use HasFactory;

    // Quote types
    const TYPE_CUSTOMER = 1;
    const TYPE_EMPLOYEE = 2;

    // Quote status
    const STATUS_DRAFT = 0;
    const STATUS_SENT = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_EXPIRED = 3;

    protected $fillable = [
        'quote_number',
        'customer_id',
        'warehouse_holding_id',
        'courier_configuration_id',
        'courier_name',
        'origin_address_id',
        'destination_address_id',
        'total_weight',
        'total_volume',
        'declared_value',
        'package_count',
        'base_fee',
        'weight_fee',
        'insurance_fee',
        'handling_fee',
        'customs_fee',
        'fuel_surcharge',
        'discount_amount',
        'total_fee',
        'quote_type',
        'calculated_by_staff_id',
        'status',
        'valid_until',
        'notes',
        'calculation_details',
    ];

    protected $casts = [
        'total_weight' => 'decimal:2',
        'total_volume' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'base_fee' => 'decimal:2',
        'weight_fee' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'handling_fee' => 'decimal:2',
        'customs_fee' => 'decimal:2',
        'fuel_surcharge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'valid_until' => 'date',
        'calculation_details' => 'array',
    ];

    /**
     * Boot method.
     */
    protected static function booted()
    {
        static::creating(function ($quote) {
            // Generate unique quote number
            if (empty($quote->quote_number)) {
                $quote->quote_number = 'SQ' . getTrx(10);
            }

            // Set default validity (7 days)
            if (empty($quote->valid_until)) {
                $quote->valid_until = Carbon::today()->addDays(7);
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
     * Get the warehouse holding.
     */
    public function warehouseHolding(): BelongsTo
    {
        return $this->belongsTo(WarehouseHolding::class);
    }

    /**
     * Get the courier configuration.
     */
    public function courierConfiguration(): BelongsTo
    {
        return $this->belongsTo(CourierConfiguration::class);
    }

    /**
     * Get the origin address.
     */
    public function originAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'origin_address_id');
    }

    /**
     * Get the destination address.
     */
    public function destinationAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'destination_address_id');
    }

    /**
     * Get the staff who calculated.
     */
    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by_staff_id');
    }

    /**
     * Scope for valid quotes.
     */
    public function scopeValid($query)
    {
        return $query->where('valid_until', '>=', Carbon::today())
            ->whereIn('status', [self::STATUS_SENT, self::STATUS_ACCEPTED]);
    }

    /**
     * Scope for expired quotes.
     */
    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', Carbon::today())
            ->orWhere('status', self::STATUS_EXPIRED);
    }

    /**
     * Check if quote is valid.
     */
    public function isValid(): bool
    {
        return Carbon::today()->lessThanOrEqualTo($this->valid_until)
            && in_array($this->status, [self::STATUS_SENT, self::STATUS_ACCEPTED]);
    }

    /**
     * Get subtotal before discount.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->base_fee
            + $this->weight_fee
            + $this->insurance_fee
            + $this->handling_fee
            + $this->customs_fee
            + $this->fuel_surcharge;
    }

    /**
     * Get status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => '<span class="badge badge-secondary">Draft</span>',
            self::STATUS_SENT => '<span class="badge badge-info">Sent</span>',
            self::STATUS_ACCEPTED => '<span class="badge badge-success">Accepted</span>',
            self::STATUS_EXPIRED => '<span class="badge badge-danger">Expired</span>',
            default => '<span class="badge badge-secondary">Unknown</span>',
        };
    }
}
