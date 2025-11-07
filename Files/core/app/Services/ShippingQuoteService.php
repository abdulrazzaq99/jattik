<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\ShippingQuote;
use App\Models\WarehouseHolding;
use App\Models\CourierConfiguration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShippingQuoteService
{
    protected CourierCalculatorService $calculatorService;

    public function __construct(CourierCalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    /**
     * Create a shipping quote for customer (FR-20).
     */
    public function createCustomerQuote(Customer $customer, array $data): ShippingQuote
    {
        $shipmentData = [
            'weight' => $data['weight'],
            'declared_value' => $data['declared_value'] ?? 0,
            'package_count' => $data['package_count'] ?? 1,
            'origin_address' => $data['origin_address'],
            'destination_address' => $data['destination_address'],
        ];

        $courier = CourierConfiguration::findOrFail($data['courier_configuration_id']);
        $rate = $this->calculatorService->getRateFromCourier($courier, $shipmentData);

        return DB::transaction(function () use ($customer, $data, $rate, $courier) {
            return ShippingQuote::create([
                'customer_id' => $customer->id,
                'warehouse_holding_id' => $data['warehouse_holding_id'] ?? null,
                'courier_configuration_id' => $courier->id,
                'courier_name' => $courier->name,
                'origin_address_id' => $data['origin_address_id'] ?? null,
                'destination_address_id' => $data['destination_address_id'] ?? null,
                'total_weight' => $data['weight'],
                'total_volume' => $data['volume'] ?? 0,
                'declared_value' => $data['declared_value'] ?? 0,
                'package_count' => $data['package_count'] ?? 1,
                'base_fee' => $rate['base_fee'],
                'weight_fee' => $rate['weight_fee'],
                'insurance_fee' => $rate['insurance_fee'],
                'handling_fee' => $rate['handling_fee'],
                'customs_fee' => $rate['customs_fee'],
                'fuel_surcharge' => $rate['fuel_surcharge'],
                'discount_amount' => 0,
                'total_fee' => $rate['total_fee'],
                'quote_type' => ShippingQuote::TYPE_CUSTOMER,
                'status' => ShippingQuote::STATUS_DRAFT,
                'notes' => $data['notes'] ?? null,
                'calculation_details' => $rate,
            ]);
        });
    }

    /**
     * Create a shipping quote by employee (FR-21).
     */
    public function createEmployeeQuote($staffId, Customer $customer, array $data): ShippingQuote
    {
        $quote = $this->createCustomerQuote($customer, $data);

        $quote->update([
            'quote_type' => ShippingQuote::TYPE_EMPLOYEE,
            'calculated_by_staff_id' => $staffId,
            'status' => ShippingQuote::STATUS_SENT,
        ]);

        return $quote->fresh();
    }

    /**
     * Calculate quote for warehouse holding.
     */
    public function createQuoteForHolding(WarehouseHolding $holding, CourierConfiguration $courier, array $addresses): ShippingQuote
    {
        $shipmentData = [
            'weight' => $holding->total_weight,
            'declared_value' => $holding->packages()->sum('declared_value'),
            'package_count' => $holding->package_count,
            'origin_address' => $addresses['origin'],
            'destination_address' => $addresses['destination'],
        ];

        $rate = $this->calculatorService->getRateFromCourier($courier, $shipmentData);

        return DB::transaction(function () use ($holding, $courier, $rate, $addresses) {
            return ShippingQuote::create([
                'customer_id' => $holding->customer_id,
                'warehouse_holding_id' => $holding->id,
                'courier_configuration_id' => $courier->id,
                'courier_name' => $courier->name,
                'origin_address_id' => $addresses['origin_id'] ?? null,
                'destination_address_id' => $addresses['destination_id'] ?? null,
                'total_weight' => $holding->total_weight,
                'total_volume' => $holding->total_volume,
                'declared_value' => $holding->packages()->sum('declared_value'),
                'package_count' => $holding->package_count,
                'base_fee' => $rate['base_fee'],
                'weight_fee' => $rate['weight_fee'],
                'insurance_fee' => $rate['insurance_fee'],
                'handling_fee' => $rate['handling_fee'],
                'customs_fee' => $rate['customs_fee'],
                'fuel_surcharge' => $rate['fuel_surcharge'],
                'discount_amount' => 0,
                'total_fee' => $rate['total_fee'],
                'quote_type' => ShippingQuote::TYPE_EMPLOYEE,
                'status' => ShippingQuote::STATUS_DRAFT,
                'calculation_details' => $rate,
            ]);
        });
    }

    /**
     * Apply discount to quote.
     */
    public function applyDiscount(ShippingQuote $quote, float $discountAmount, string $reason = null): ShippingQuote
    {
        $subtotal = $quote->subtotal;
        $discountAmount = min($discountAmount, $subtotal); // Can't discount more than subtotal

        $quote->update([
            'discount_amount' => $discountAmount,
            'total_fee' => $subtotal - $discountAmount,
            'notes' => $quote->notes . "\nDiscount applied: {$reason}",
        ]);

        return $quote->fresh();
    }

    /**
     * Accept a quote.
     */
    public function acceptQuote(ShippingQuote $quote): ShippingQuote
    {
        if (!$quote->isValid()) {
            throw new \Exception('Quote is expired or invalid');
        }

        $quote->update([
            'status' => ShippingQuote::STATUS_ACCEPTED,
        ]);

        return $quote->fresh();
    }

    /**
     * Get multiple quotes from different couriers.
     */
    public function getMultipleQuotes(Customer $customer, array $shipmentData): array
    {
        $rates = $this->calculatorService->getRatesFromAllCouriers($shipmentData);
        $quotes = [];

        foreach ($rates as $rate) {
            $courier = CourierConfiguration::find($rate['courier_id']);

            if ($courier) {
                $quotes[] = [
                    'courier' => $courier,
                    'rate' => $rate,
                    'estimated_total' => $rate['total_fee'],
                ];
            }
        }

        return $quotes;
    }

    /**
     * Expire old quotes.
     */
    public function expireOldQuotes(): int
    {
        return ShippingQuote::where('valid_until', '<', Carbon::today())
            ->whereIn('status', [ShippingQuote::STATUS_DRAFT, ShippingQuote::STATUS_SENT])
            ->update(['status' => ShippingQuote::STATUS_EXPIRED]);
    }
}
