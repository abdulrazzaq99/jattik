<?php

namespace App\Services;

use App\Models\CourierInfo;
use App\Models\CourierConfiguration;
use App\Models\CourierTrackingEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CourierTrackingService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Fetch tracking updates from all couriers (FR-24)
     * Called by cron job every 30 minutes
     */
    public function fetchAllTrackingUpdates(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'exceptions' => 0,
        ];

        // Get all active shipments
        $activeShipments = CourierInfo::whereIn('status', [1, 2]) // Dispatched or Delivery Queue
            ->with('courierConfiguration')
            ->get();

        foreach ($activeShipments as $shipment) {
            try {
                $events = $this->fetchTrackingEvents($shipment);

                if (!empty($events)) {
                    $results['success']++;

                    // Check for exceptions
                    foreach ($events as $event) {
                        if ($event->is_exception && !$event->customer_notified) {
                            $this->handleException($event);
                            $results['exceptions']++;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to fetch tracking for courier {$shipment->code}: " . $e->getMessage());
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Fetch tracking events for a specific courier
     */
    public function fetchTrackingEvents(CourierInfo $courier): array
    {
        if (!$courier->courierConfiguration) {
            return [];
        }

        $config = $courier->courierConfiguration;
        $events = [];

        // Determine which API to call based on courier
        switch (strtolower($config->code)) {
            case 'aramex':
                $events = $this->fetchAramexTracking($config, $courier);
                break;
            case 'dhl':
                $events = $this->fetchDHLTracking($config, $courier);
                break;
            case 'fedex':
                $events = $this->fetchFedExTracking($config, $courier);
                break;
            case 'ups':
                $events = $this->fetchUPSTracking($config, $courier);
                break;
            default:
                Log::warning("No tracking API configured for courier: {$config->code}");
        }

        return $events;
    }

    /**
     * Fetch tracking from Aramex API
     */
    protected function fetchAramexTracking(CourierConfiguration $config, CourierInfo $courier): array
    {
        try {
            $endpoint = $config->api_endpoint . '/ShipmentTracking';

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'ClientInfo' => [
                        'UserName' => $config->api_username,
                        'Password' => $config->api_password,
                        'Version' => 'v1.0',
                        'AccountNumber' => $config->account_number,
                    ],
                    'Transaction' => [
                        'Reference1' => $courier->code,
                    ],
                    'Shipments' => [$courier->tracking_number ?? $courier->code],
                ]);

            if ($response->successful()) {
                return $this->parseAramexResponse($response->json(), $courier, $config);
            }

            Log::error("Aramex API error: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Aramex tracking fetch failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse Aramex API response
     */
    protected function parseAramexResponse(array $data, CourierInfo $courier, CourierConfiguration $config): array
    {
        $events = [];

        if (!isset($data['TrackingResults'])) {
            return [];
        }

        foreach ($data['TrackingResults'] as $result) {
            if (!isset($result['UpdateCode'])) {
                continue;
            }

            $event = CourierTrackingEvent::updateOrCreate(
                [
                    'courier_info_id' => $courier->id,
                    'tracking_number' => $courier->code,
                    'status_code' => $result['UpdateCode'],
                ],
                [
                    'courier_configuration_id' => $config->id,
                    'carrier_name' => 'Aramex',
                    'event_type' => $this->mapAramexEventType($result['UpdateCode']),
                    'description' => $result['UpdateDescription'] ?? '',
                    'location' => $result['UpdateLocation'] ?? null,
                    'event_time' => Carbon::parse($result['UpdateDateTime']),
                    'is_exception' => $this->isAramexException($result['UpdateCode']),
                    'exception_type' => $this->getAramexExceptionType($result['UpdateCode']),
                    'raw_data' => $result,
                ]
            );

            $events[] = $event;
        }

        return $events;
    }

    /**
     * Fetch tracking from DHL API
     */
    protected function fetchDHLTracking(CourierConfiguration $config, CourierInfo $courier): array
    {
        try {
            $trackingNumber = $courier->tracking_number ?? $courier->code;
            $endpoint = $config->api_endpoint . "/track/shipments?trackingNumber={$trackingNumber}";

            $response = Http::timeout(30)
                ->withHeaders([
                    'DHL-API-Key' => $config->api_key,
                ])
                ->get($endpoint);

            if ($response->successful()) {
                return $this->parseDHLResponse($response->json(), $courier, $config);
            }

            return [];
        } catch (\Exception $e) {
            Log::error("DHL tracking fetch failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse DHL API response
     */
    protected function parseDHLResponse(array $data, CourierInfo $courier, CourierConfiguration $config): array
    {
        $events = [];

        if (!isset($data['shipments'][0]['events'])) {
            return [];
        }

        foreach ($data['shipments'][0]['events'] as $eventData) {
            $event = CourierTrackingEvent::updateOrCreate(
                [
                    'courier_info_id' => $courier->id,
                    'tracking_number' => $courier->code,
                    'status_code' => $eventData['statusCode'] ?? '',
                ],
                [
                    'courier_configuration_id' => $config->id,
                    'carrier_name' => 'DHL',
                    'event_type' => $this->mapDHLEventType($eventData['statusCode'] ?? ''),
                    'description' => $eventData['description'] ?? '',
                    'location' => $eventData['location']['address']['addressLocality'] ?? null,
                    'event_time' => Carbon::parse($eventData['timestamp']),
                    'is_exception' => isset($eventData['remark']),
                    'exception_details' => $eventData['remark'] ?? null,
                    'raw_data' => $eventData,
                ]
            );

            $events[] = $event;
        }

        return $events;
    }

    /**
     * Fetch tracking from FedEx API
     */
    protected function fetchFedExTracking(CourierConfiguration $config, CourierInfo $courier): array
    {
        try {
            $endpoint = $config->api_endpoint . '/track/v1/trackingnumbers';

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config->api_key,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'trackingInfo' => [
                        [
                            'trackingNumberInfo' => [
                                'trackingNumber' => $courier->tracking_number ?? $courier->code,
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                return $this->parseFedExResponse($response->json(), $courier, $config);
            }

            return [];
        } catch (\Exception $e) {
            Log::error("FedEx tracking fetch failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse FedEx API response
     */
    protected function parseFedExResponse(array $data, CourierInfo $courier, CourierConfiguration $config): array
    {
        $events = [];

        if (!isset($data['output']['completeTrackResults'][0]['trackResults'][0]['scanEvents'])) {
            return [];
        }

        foreach ($data['output']['completeTrackResults'][0]['trackResults'][0]['scanEvents'] as $scanEvent) {
            $event = CourierTrackingEvent::updateOrCreate(
                [
                    'courier_info_id' => $courier->id,
                    'tracking_number' => $courier->code,
                    'status_code' => $scanEvent['eventType'] ?? '',
                ],
                [
                    'courier_configuration_id' => $config->id,
                    'carrier_name' => 'FedEx',
                    'event_type' => $this->mapFedExEventType($scanEvent['eventType'] ?? ''),
                    'description' => $scanEvent['eventDescription'] ?? '',
                    'location' => $scanEvent['scanLocation']['city'] ?? null,
                    'event_time' => Carbon::parse($scanEvent['date']),
                    'is_exception' => isset($scanEvent['exceptionDescription']),
                    'exception_details' => $scanEvent['exceptionDescription'] ?? null,
                    'raw_data' => $scanEvent,
                ]
            );

            $events[] = $event;
        }

        return $events;
    }

    /**
     * Fetch tracking from UPS API
     */
    protected function fetchUPSTracking(CourierConfiguration $config, CourierInfo $courier): array
    {
        try {
            $trackingNumber = $courier->tracking_number ?? $courier->code;
            $endpoint = $config->api_endpoint . "/api/track/v1/details/{$trackingNumber}";

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config->api_key,
                ])
                ->get($endpoint);

            if ($response->successful()) {
                return $this->parseUPSResponse($response->json(), $courier, $config);
            }

            return [];
        } catch (\Exception $e) {
            Log::error("UPS tracking fetch failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse UPS API response
     */
    protected function parseUPSResponse(array $data, CourierInfo $courier, CourierConfiguration $config): array
    {
        $events = [];

        if (!isset($data['trackResponse']['shipment'][0]['package'][0]['activity'])) {
            return [];
        }

        foreach ($data['trackResponse']['shipment'][0]['package'][0]['activity'] as $activity) {
            $event = CourierTrackingEvent::updateOrCreate(
                [
                    'courier_info_id' => $courier->id,
                    'tracking_number' => $courier->code,
                    'status_code' => $activity['status']['code'] ?? '',
                ],
                [
                    'courier_configuration_id' => $config->id,
                    'carrier_name' => 'UPS',
                    'event_type' => $this->mapUPSEventType($activity['status']['code'] ?? ''),
                    'description' => $activity['status']['description'] ?? '',
                    'location' => $activity['location']['address']['city'] ?? null,
                    'event_time' => Carbon::parse($activity['date'] . ' ' . $activity['time']),
                    'raw_data' => $activity,
                ]
            );

            $events[] = $event;
        }

        return $events;
    }

    /**
     * Handle exception event (FR-25)
     */
    protected function handleException(CourierTrackingEvent $event): void
    {
        $courier = $event->courierInfo;

        // Send notification to customer
        $this->notificationService->sendExceptionNotification(
            $courier,
            $event->exception_type ?? 'other',
            $event->exception_details ?? $event->description
        );

        // Mark as notified
        $event->markAsNotified();
    }

    /**
     * Map event types for different carriers
     */
    protected function mapAramexEventType(string $code): string
    {
        $mapping = [
            'SH001' => CourierTrackingEvent::EVENT_PICKED_UP,
            'SH002' => CourierTrackingEvent::EVENT_IN_TRANSIT,
            'SH010' => CourierTrackingEvent::EVENT_OUT_FOR_DELIVERY,
            'SH011' => CourierTrackingEvent::EVENT_DELIVERED,
        ];

        return $mapping[$code] ?? CourierTrackingEvent::EVENT_IN_TRANSIT;
    }

    protected function mapDHLEventType(string $code): string
    {
        if (str_contains($code, 'delivered')) return CourierTrackingEvent::EVENT_DELIVERED;
        if (str_contains($code, 'transit')) return CourierTrackingEvent::EVENT_IN_TRANSIT;
        if (str_contains($code, 'picked')) return CourierTrackingEvent::EVENT_PICKED_UP;

        return CourierTrackingEvent::EVENT_IN_TRANSIT;
    }

    protected function mapFedExEventType(string $type): string
    {
        $mapping = [
            'PU' => CourierTrackingEvent::EVENT_PICKED_UP,
            'IT' => CourierTrackingEvent::EVENT_IN_TRANSIT,
            'OD' => CourierTrackingEvent::EVENT_OUT_FOR_DELIVERY,
            'DL' => CourierTrackingEvent::EVENT_DELIVERED,
        ];

        return $mapping[$type] ?? CourierTrackingEvent::EVENT_IN_TRANSIT;
    }

    protected function mapUPSEventType(string $code): string
    {
        if ($code === 'D') return CourierTrackingEvent::EVENT_DELIVERED;
        if ($code === 'P') return CourierTrackingEvent::EVENT_PICKED_UP;
        if ($code === 'I') return CourierTrackingEvent::EVENT_IN_TRANSIT;

        return CourierTrackingEvent::EVENT_IN_TRANSIT;
    }

    /**
     * Check if Aramex status is an exception
     */
    protected function isAramexException(string $code): bool
    {
        $exceptionCodes = ['SH020', 'SH021', 'SH022', 'SH030'];
        return in_array($code, $exceptionCodes);
    }

    protected function getAramexExceptionType(string $code): ?string
    {
        $types = [
            'SH020' => CourierTrackingEvent::EXCEPTION_DELAY,
            'SH021' => CourierTrackingEvent::EXCEPTION_WRONG_ADDRESS,
            'SH022' => CourierTrackingEvent::EXCEPTION_DAMAGED,
            'SH030' => CourierTrackingEvent::EXCEPTION_REFUSED,
        ];

        return $types[$code] ?? null;
    }

    /**
     * Get tracking history for a courier
     */
    public function getTrackingHistory(CourierInfo $courier)
    {
        return CourierTrackingEvent::where('courier_info_id', $courier->id)
            ->orderBy('event_time', 'desc')
            ->get();
    }

    /**
     * Get recent exceptions
     */
    public function getRecentExceptions(int $days = 7)
    {
        return CourierTrackingEvent::exceptions()
            ->recent($days)
            ->with(['courierInfo.senderCustomer', 'courierInfo.receiverCustomer'])
            ->get();
    }
}
