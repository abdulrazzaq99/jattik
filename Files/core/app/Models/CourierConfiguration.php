<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourierConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'api_endpoint',
        'api_key',
        'api_secret',
        'account_number',
        'additional_config',
        'base_rate',
        'per_kg_rate',
        'insurance_percentage',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'additional_config' => 'array',
        'base_rate' => 'decimal:2',
        'per_kg_rate' => 'decimal:2',
        'insurance_percentage' => 'decimal:2',
    ];

    /**
     * Get shipping quotes using this configuration.
     */
    public function shippingQuotes(): HasMany
    {
        return $this->hasMany(ShippingQuote::class);
    }

    /**
     * Scope to get active configurations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Calculate base shipping cost.
     */
    public function calculateBaseCost(float $weight, float $declaredValue = 0): array
    {
        $baseFee = $this->base_rate;
        $weightFee = $weight * $this->per_kg_rate;
        $insuranceFee = ($declaredValue * $this->insurance_percentage) / 100;

        return [
            'base_fee' => $baseFee,
            'weight_fee' => $weightFee,
            'insurance_fee' => $insuranceFee,
            'subtotal' => $baseFee + $weightFee + $insuranceFee,
        ];
    }

    /**
     * Check if API integration is configured.
     */
    public function hasApiIntegration(): bool
    {
        return !empty($this->api_endpoint) && !empty($this->api_key);
    }
}
