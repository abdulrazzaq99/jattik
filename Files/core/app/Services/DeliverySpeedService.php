<?php

namespace App\Services;

use App\Models\CourierInfo;
use App\Models\WarehouseHolding;
use App\Models\ShippingQuote;
use Carbon\Carbon;

class DeliverySpeedService
{
    // Standard delivery: 5-7 business days
    const STANDARD_DELIVERY_DAYS = 7;
    const STANDARD_MIN_DAYS = 5;

    // Express delivery: 2-3 business days
    const EXPRESS_DELIVERY_DAYS = 3;
    const EXPRESS_MIN_DAYS = 2;

    // Express surcharge: 50% of base rate
    const EXPRESS_SURCHARGE_PERCENTAGE = 50;

    /**
     * Calculate express surcharge (FR-34)
     */
    public function calculateExpressSurcharge(float $baseRate): float
    {
        return round($baseRate * (self::EXPRESS_SURCHARGE_PERCENTAGE / 100), 2);
    }

    /**
     * Calculate estimated delivery date
     */
    public function calculateEstimatedDelivery(string $speed, Carbon $shipDate = null): array
    {
        $shipDate = $shipDate ?? now();

        if ($speed === 'express') {
            $minDate = $this->addBusinessDays($shipDate, self::EXPRESS_MIN_DAYS);
            $maxDate = $this->addBusinessDays($shipDate, self::EXPRESS_DELIVERY_DAYS);
        } else {
            $minDate = $this->addBusinessDays($shipDate, self::STANDARD_MIN_DAYS);
            $maxDate = $this->addBusinessDays($shipDate, self::STANDARD_DELIVERY_DAYS);
        }

        return [
            'min_date' => $minDate,
            'max_date' => $maxDate,
            'estimated_date' => $maxDate,
            'days' => $speed === 'express' ? self::EXPRESS_DELIVERY_DAYS : self::STANDARD_DELIVERY_DAYS,
        ];
    }

    /**
     * Add business days (skip weekends)
     */
    protected function addBusinessDays(Carbon $date, int $days): Carbon
    {
        $result = $date->copy();
        $addedDays = 0;

        while ($addedDays < $days) {
            $result->addDay();
            if ($result->isWeekday()) {
                $addedDays++;
            }
        }

        return $result;
    }

    /**
     * Get delivery options for a quote
     */
    public function getDeliveryOptions(float $baseRate): array
    {
        $standardDelivery = $this->calculateEstimatedDelivery('standard');
        $expressDelivery = $this->calculateEstimatedDelivery('express');
        $expressSurcharge = $this->calculateExpressSurcharge($baseRate);

        return [
            'standard' => [
                'speed' => 'standard',
                'label' => 'Standard Delivery',
                'cost' => $baseRate,
                'surcharge' => 0,
                'total_cost' => $baseRate,
                'estimated_days' => self::STANDARD_DELIVERY_DAYS,
                'min_date' => $standardDelivery['min_date']->format('M d, Y'),
                'max_date' => $standardDelivery['max_date']->format('M d, Y'),
                'estimated_date' => $standardDelivery['estimated_date']->format('M d, Y'),
            ],
            'express' => [
                'speed' => 'express',
                'label' => 'Express Delivery',
                'cost' => $baseRate,
                'surcharge' => $expressSurcharge,
                'total_cost' => $baseRate + $expressSurcharge,
                'estimated_days' => self::EXPRESS_DELIVERY_DAYS,
                'min_date' => $expressDelivery['min_date']->format('M d, Y'),
                'max_date' => $expressDelivery['max_date']->format('M d, Y'),
                'estimated_date' => $expressDelivery['estimated_date']->format('M d, Y'),
            ],
        ];
    }

    /**
     * Apply delivery speed to courier
     */
    public function applySpe edToCourier(CourierInfo $courier, string $speed): void
    {
        $estimatedDelivery = $this->calculateEstimatedDelivery($speed);

        $courier->update([
            'delivery_speed' => $speed,
            'speed_surcharge' => $speed === 'express' ? $this->calculateExpressSurcharge($courier->total_amount ?? 0) : 0,
            'estimated_delivery_days' => $estimatedDelivery['days'],
            'estimated_delivery_date' => $estimatedDelivery['estimated_date'],
        ]);
    }

    /**
     * Apply delivery speed to quote
     */
    public function applySpeedToQuote(ShippingQuote $quote, string $speed): void
    {
        $surcharge = $speed === 'express' ? $this->calculateExpressSurcharge($quote->base_fee) : 0;

        $quote->update([
            'delivery_speed' => $speed,
            'express_surcharge' => $surcharge,
            'total_fee' => $quote->subtotal + $surcharge - $quote->discount_amount,
        ]);
    }

    /**
     * Apply delivery speed to warehouse holding
     */
    public function applySpeedToHolding(WarehouseHolding $holding, string $speed): void
    {
        $holding->update([
            'preferred_delivery_speed' => $speed,
        ]);
    }

    /**
     * Get speed label
     */
    public function getSpeedLabel(string $speed): string
    {
        return $speed === 'express' ? 'Express Delivery (2-3 days)' : 'Standard Delivery (5-7 days)';
    }

    /**
     * Get speed badge HTML
     */
    public function getSpeedBadge(string $speed): string
    {
        if ($speed === 'express') {
            return '<span class="badge badge--warning">Express</span>';
        }

        return '<span class="badge badge--info">Standard</span>';
    }

    /**
     * Calculate savings with express
     */
    public function calculateExpressSavings(float $baseRate): array
    {
        $standardDays = self::STANDARD_DELIVERY_DAYS;
        $expressDays = self::EXPRESS_DELIVERY_DAYS;
        $expressCost = $this->calculateExpressSurcharge($baseRate);
        $daysSaved = $standardDays - $expressDays;

        return [
            'days_saved' => $daysSaved,
            'extra_cost' => $expressCost,
            'percentage_faster' => round((($daysSaved / $standardDays) * 100)),
        ];
    }
}
