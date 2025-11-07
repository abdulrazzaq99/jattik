<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehousePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_holding_id',
        'package_code',
        'description',
        'weight',
        'length',
        'width',
        'height',
        'volume',
        'declared_value',
        'category',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'volume' => 'decimal:2',
        'declared_value' => 'decimal:2',
    ];

    /**
     * Boot method.
     */
    protected static function booted()
    {
        static::creating(function ($package) {
            // Generate unique package code
            if (empty($package->package_code)) {
                $package->package_code = 'PKG' . getTrx(10);
            }

            // Calculate volume if dimensions provided
            if ($package->length && $package->width && $package->height && empty($package->volume)) {
                $package->volume = ($package->length * $package->width * $package->height) / 1000000; // cm³ to m³
            }
        });

        // Update holding totals when package is created/updated/deleted
        static::saved(function ($package) {
            $package->warehouseHolding->updateTotals();
        });

        static::deleted(function ($package) {
            $package->warehouseHolding->updateTotals();
        });
    }

    /**
     * Get the warehouse holding.
     */
    public function warehouseHolding(): BelongsTo
    {
        return $this->belongsTo(WarehouseHolding::class);
    }

    /**
     * Get volumetric weight (used for shipping calculations).
     */
    public function getVolumetricWeightAttribute(): float
    {
        if ($this->length && $this->width && $this->height) {
            // Standard formula: (L × W × H) / 5000 for cm to kg
            return ($this->length * $this->width * $this->height) / 5000;
        }

        return 0;
    }

    /**
     * Get chargeable weight (higher of actual or volumetric).
     */
    public function getChargeableWeightAttribute(): float
    {
        return max($this->weight, $this->volumetric_weight);
    }
}
