<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CourierInfo;
use App\Models\WarehouseHolding;
use App\Models\ShipmentNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send facility arrival notification (FR-22)
     */
    public function sendFacilityArrival(CourierInfo $courier): ShipmentNotification
    {
        $customer = $courier->receiverCustomer;

        $notification = ShipmentNotification::create([
            'customer_id' => $customer->id,
            'courier_info_id' => $courier->id,
            'notification_type' => ShipmentNotification::TYPE_FACILITY_ARRIVAL,
            'title' => 'Your shipment has arrived at our facility',
            'message' => "Your shipment (Tracking: {$courier->code}) has arrived at {$courier->receiverBranch->name} facility and is ready for processing.",
            'metadata' => [
                'tracking_code' => $courier->code,
                'branch_name' => $courier->receiverBranch->name,
                'branch_address' => $courier->receiverBranch->address,
            ],
        ]);

        // Send via email and SMS
        try {
            notify($customer, 'SHIPMENT_ARRIVED', [
                'tracking_code' => $courier->code,
                'branch_name' => $courier->receiverBranch->name,
                'customer_name' => $customer->fullname,
            ]);

            $notification->markAsSent(['email', 'sms']);
        } catch (\Exception $e) {
            Log::error('Failed to send facility arrival notification: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Send dispatch notification (FR-23)
     */
    public function sendDispatchNotification(CourierInfo $courier): ShipmentNotification
    {
        $customer = $courier->senderCustomer;

        $notification = ShipmentNotification::create([
            'customer_id' => $customer->id,
            'courier_info_id' => $courier->id,
            'notification_type' => ShipmentNotification::TYPE_DISPATCHED,
            'title' => 'Your shipment has been dispatched',
            'message' => "Your shipment (Tracking: {$courier->code}) has been dispatched and is on its way to {$courier->receiverBranch->name}.",
            'metadata' => [
                'tracking_code' => $courier->code,
                'destination_branch' => $courier->receiverBranch->name,
                'estimated_delivery' => $courier->estimated_delivery_date?->format('M d, Y'),
            ],
        ]);

        try {
            notify($customer, 'SHIPMENT_DISPATCHED', [
                'tracking_code' => $courier->code,
                'destination' => $courier->receiverBranch->name,
                'customer_name' => $customer->fullname,
                'estimated_delivery' => $courier->estimated_delivery_date?->format('M d, Y') ?? 'TBD',
            ]);

            $notification->markAsSent(['email', 'sms']);
        } catch (\Exception $e) {
            Log::error('Failed to send dispatch notification: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Send tracking link notification (FR-26)
     */
    public function sendTrackingLink(CourierInfo $courier): ShipmentNotification
    {
        $customer = $courier->senderCustomer;
        $trackingUrl = route('customer.track') . '?code=' . $courier->code;

        $notification = ShipmentNotification::create([
            'customer_id' => $customer->id,
            'courier_info_id' => $courier->id,
            'notification_type' => ShipmentNotification::TYPE_TRACKING_LINK,
            'title' => 'Track your shipment',
            'message' => "Your shipment is ready to track. Use tracking code: {$courier->code}",
            'metadata' => [
                'tracking_code' => $courier->code,
                'tracking_url' => $trackingUrl,
            ],
        ]);

        try {
            notify($customer, 'TRACKING_LINK', [
                'tracking_code' => $courier->code,
                'tracking_url' => $trackingUrl,
                'customer_name' => $customer->fullname,
            ]);

            $notification->markAsSent(['email', 'sms']);
        } catch (\Exception $e) {
            Log::error('Failed to send tracking link notification: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Send shipping fee notification with payment link (FR-27)
     */
    public function sendShippingFeeQuote($quote, Customer $customer): ShipmentNotification
    {
        $paymentUrl = route('customer.payment.process', ['quote_id' => $quote->id]);

        $notification = ShipmentNotification::create([
            'customer_id' => $customer->id,
            'notification_type' => ShipmentNotification::TYPE_FEE_QUOTE,
            'title' => 'Your shipping quote is ready',
            'message' => "Your shipping quote #{$quote->quote_number} is ready. Total fee: " . showAmount($quote->total_fee),
            'metadata' => [
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'total_fee' => $quote->total_fee,
                'payment_url' => $paymentUrl,
            ],
        ]);

        try {
            notify($customer, 'SHIPPING_FEE_QUOTE', [
                'quote_number' => $quote->quote_number,
                'total_fee' => showAmount($quote->total_fee),
                'payment_url' => $paymentUrl,
                'customer_name' => $customer->fullname,
            ]);

            $notification->markAsSent(['email']);
        } catch (\Exception $e) {
            Log::error('Failed to send shipping fee notification: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Send delivery confirmation notification
     */
    public function sendDeliveryConfirmation(CourierInfo $courier): ShipmentNotification
    {
        $customer = $courier->receiverCustomer;

        $notification = ShipmentNotification::create([
            'customer_id' => $customer->id,
            'courier_info_id' => $courier->id,
            'notification_type' => ShipmentNotification::TYPE_DELIVERED,
            'title' => 'Your shipment has been delivered',
            'message' => "Your shipment (Tracking: {$courier->code}) has been successfully delivered.",
            'metadata' => [
                'tracking_code' => $courier->code,
                'delivered_at' => now()->format('M d, Y h:i A'),
            ],
        ]);

        try {
            notify($customer, 'SHIPMENT_DELIVERED', [
                'tracking_code' => $courier->code,
                'customer_name' => $customer->fullname,
                'delivered_at' => now()->format('M d, Y h:i A'),
            ]);

            $notification->markAsSent(['email', 'sms']);
        } catch (\Exception $e) {
            Log::error('Failed to send delivery confirmation: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Send exception notification (FR-25)
     */
    public function sendExceptionNotification(CourierInfo $courier, string $exceptionType, string $details): ShipmentNotification
    {
        $customer = $courier->senderCustomer ?? $courier->receiverCustomer;

        $titles = [
            'delay' => 'Shipment Delayed',
            'wrong_address' => 'Address Issue Detected',
            'damaged' => 'Package Damage Reported',
            'lost' => 'Shipment Investigation',
        ];

        $notification = ShipmentNotification::create([
            'customer_id' => $customer->id,
            'courier_info_id' => $courier->id,
            'notification_type' => ShipmentNotification::TYPE_EXCEPTION,
            'title' => $titles[$exceptionType] ?? 'Shipment Alert',
            'message' => $details,
            'metadata' => [
                'tracking_code' => $courier->code,
                'exception_type' => $exceptionType,
                'exception_details' => $details,
            ],
        ]);

        try {
            notify($customer, 'SHIPMENT_EXCEPTION', [
                'tracking_code' => $courier->code,
                'customer_name' => $customer->fullname,
                'exception_type' => ucwords(str_replace('_', ' ', $exceptionType)),
                'details' => $details,
            ]);

            $notification->markAsSent(['email', 'sms']);
        } catch (\Exception $e) {
            Log::error('Failed to send exception notification: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Get unread notifications for customer
     */
    public function getUnreadNotifications(Customer $customer)
    {
        return ShipmentNotification::where('customer_id', $customer->id)
            ->unread()
            ->latest()
            ->get();
    }

    /**
     * Get all notifications for customer
     */
    public function getAllNotifications(Customer $customer, int $perPage = 20)
    {
        return ShipmentNotification::where('customer_id', $customer->id)
            ->with(['courierInfo', 'warehouseHolding'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, Customer $customer): bool
    {
        $notification = ShipmentNotification::where('id', $notificationId)
            ->where('customer_id', $customer->id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Customer $customer): int
    {
        return ShipmentNotification::where('customer_id', $customer->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}
