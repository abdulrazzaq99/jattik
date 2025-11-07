<?php

namespace App\Services;

use App\Models\CourierConfiguration;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourierCalculatorService
{
    /**
     * Get rates from all active courier configurations.
     */
    public function getRatesFromAllCouriers(array $shipmentData): array
    {
        $couriers = CourierConfiguration::active()->get();
        $rates = [];

        foreach ($couriers as $courier) {
            try {
                $rate = $this->getRateFromCourier($courier, $shipmentData);
                if ($rate) {
                    $rates[] = $rate;
                }
            } catch (\Exception $e) {
                Log::error("Error fetching rate from {$courier->name}: " . $e->getMessage());
                continue;
            }
        }

        // Sort by total fee
        usort($rates, function ($a, $b) {
            return $a['total_fee'] <=> $b['total_fee'];
        });

        return $rates;
    }

    /**
     * Get rate from a specific courier.
     */
    public function getRateFromCourier(CourierConfiguration $courier, array $shipmentData): ?array
    {
        // If courier has API integration, fetch from API
        if ($courier->hasApiIntegration()) {
            return $this->fetchFromApi($courier, $shipmentData);
        }

        // Otherwise, use manual calculation
        return $this->calculateManualRate($courier, $shipmentData);
    }

    /**
     * Fetch rate from courier API.
     */
    protected function fetchFromApi(CourierConfiguration $courier, array $shipmentData): ?array
    {
        try {
            $response = match ($courier->code) {
                'aramex' => $this->fetchAramexRate($courier, $shipmentData),
                'dhl' => $this->fetchDHLRate($courier, $shipmentData),
                'fedex' => $this->fetchFedExRate($courier, $shipmentData),
                'ups' => $this->fetchUPSRate($courier, $shipmentData),
                default => null,
            };

            return $response;
        } catch (\Exception $e) {
            Log::error("API fetch error for {$courier->name}: " . $e->getMessage());
            // Fallback to manual calculation
            return $this->calculateManualRate($courier, $shipmentData);
        }
    }

    /**
     * Fetch rate from Aramex API.
     */
    protected function fetchAramexRate(CourierConfiguration $courier, array $shipmentData): ?array
    {
        // Aramex API integration
        // This is a placeholder - implement actual Aramex API call
        $endpoint = $courier->api_endpoint . '/CalculateRate';

        $requestData = [
            'ClientInfo' => [
                'AccountNumber' => $courier->account_number,
                'UserName' => $courier->api_key,
                'Password' => $courier->api_secret,
            ],
            'Transaction' => [
                'Reference1' => $shipmentData['reference'] ?? '',
            ],
            'OriginAddress' => $this->formatAddress($shipmentData['origin_address']),
            'DestinationAddress' => $this->formatAddress($shipmentData['destination_address']),
            'ShipmentDetails' => [
                'Weight' => $shipmentData['weight'],
                'NumberOfPieces' => $shipmentData['package_count'] ?? 1,
                'ActualWeight' => [
                    'Value' => $shipmentData['weight'],
                    'Unit' => 'KG',
                ],
            ],
        ];

        try {
            $response = Http::timeout(30)->post($endpoint, $requestData);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'courier_id' => $courier->id,
                    'courier_name' => $courier->name,
                    'courier_code' => $courier->code,
                    'base_fee' => $data['TotalAmount']['Value'] ?? 0,
                    'weight_fee' => 0,
                    'insurance_fee' => $this->calculateInsurance($courier, $shipmentData['declared_value'] ?? 0),
                    'handling_fee' => 0,
                    'customs_fee' => 0,
                    'fuel_surcharge' => $data['FuelSurcharge'] ?? 0,
                    'total_fee' => ($data['TotalAmount']['Value'] ?? 0) + $this->calculateInsurance($courier, $shipmentData['declared_value'] ?? 0),
                    'api_response' => $data,
                    'transit_days' => $data['TotalTransitDays'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error("Aramex API error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch rate from DHL API.
     */
    protected function fetchDHLRate(CourierConfiguration $courier, array $shipmentData): ?array
    {
        // DHL API integration
        // This is a placeholder - implement actual DHL API call
        $endpoint = $courier->api_endpoint . '/rates';

        $requestData = [
            'accountNumber' => $courier->account_number,
            'shipmentDetails' => [
                'weight' => $shipmentData['weight'],
                'dimensions' => $shipmentData['dimensions'] ?? null,
            ],
            'origin' => $this->formatAddress($shipmentData['origin_address']),
            'destination' => $this->formatAddress($shipmentData['destination_address']),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $courier->api_key,
            ])->timeout(30)->post($endpoint, $requestData);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'courier_id' => $courier->id,
                    'courier_name' => $courier->name,
                    'courier_code' => $courier->code,
                    'base_fee' => $data['totalPrice'] ?? 0,
                    'weight_fee' => 0,
                    'insurance_fee' => $this->calculateInsurance($courier, $shipmentData['declared_value'] ?? 0),
                    'handling_fee' => 0,
                    'customs_fee' => 0,
                    'fuel_surcharge' => 0,
                    'total_fee' => ($data['totalPrice'] ?? 0) + $this->calculateInsurance($courier, $shipmentData['declared_value'] ?? 0),
                    'api_response' => $data,
                    'transit_days' => $data['deliveryTime'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error("DHL API error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch rate from FedEx API.
     */
    protected function fetchFedExRate(CourierConfiguration $courier, array $shipmentData): ?array
    {
        // FedEx API integration placeholder
        return $this->calculateManualRate($courier, $shipmentData);
    }

    /**
     * Fetch rate from UPS API.
     */
    protected function fetchUPSRate(CourierConfiguration $courier, array $shipmentData): ?array
    {
        // UPS API integration placeholder
        return $this->calculateManualRate($courier, $shipmentData);
    }

    /**
     * Calculate rate manually using courier configuration.
     */
    protected function calculateManualRate(CourierConfiguration $courier, array $shipmentData): array
    {
        $weight = $shipmentData['weight'];
        $declaredValue = $shipmentData['declared_value'] ?? 0;

        $costs = $courier->calculateBaseCost($weight, $declaredValue);

        // Add additional fees
        $handlingFee = $shipmentData['handling_fee'] ?? 0;
        $customsFee = $shipmentData['customs_fee'] ?? 0;
        $fuelSurcharge = ($costs['subtotal'] * 0.15); // 15% fuel surcharge

        $total = $costs['subtotal'] + $handlingFee + $customsFee + $fuelSurcharge;

        return [
            'courier_id' => $courier->id,
            'courier_name' => $courier->name,
            'courier_code' => $courier->code,
            'base_fee' => $costs['base_fee'],
            'weight_fee' => $costs['weight_fee'],
            'insurance_fee' => $costs['insurance_fee'],
            'handling_fee' => $handlingFee,
            'customs_fee' => $customsFee,
            'fuel_surcharge' => $fuelSurcharge,
            'total_fee' => $total,
            'calculation_method' => 'manual',
            'transit_days' => null,
        ];
    }

    /**
     * Calculate insurance fee.
     */
    protected function calculateInsurance(CourierConfiguration $courier, float $declaredValue): float
    {
        return ($declaredValue * $courier->insurance_percentage) / 100;
    }

    /**
     * Format address for API calls.
     */
    protected function formatAddress($address): array
    {
        if ($address instanceof CustomerAddress) {
            return [
                'line1' => $address->address_line_1,
                'line2' => $address->address_line_2,
                'city' => $address->city,
                'state' => $address->state,
                'postalCode' => $address->postal_code,
                'country' => $address->country,
            ];
        }

        return $address;
    }
}
