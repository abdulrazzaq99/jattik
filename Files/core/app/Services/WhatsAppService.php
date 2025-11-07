<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CourierInfo;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $accessToken;
    protected string $phoneNumberId;

    public function __construct()
    {
        // Configuration from .env or database
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v17.0');
        $this->accessToken = config('services.whatsapp.access_token', '');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $to, string $message, array $metadata = []): ?WhatsAppMessage
    {
        if (empty($this->accessToken) || empty($this->phoneNumberId)) {
            Log::warning('WhatsApp not configured');
            return null;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return WhatsAppMessage::create([
                    'phone_number' => $to,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
                    'message_type' => WhatsAppMessage::TYPE_TEXT,
                    'message_content' => $message,
                    'metadata' => $metadata,
                    'status' => WhatsAppMessage::STATUS_SENT,
                    'whatsapp_status' => 'sent',
                ]);
            }

            Log::error('WhatsApp send failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('WhatsApp error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send OTP via WhatsApp (FR-30)
     */
    public function sendOTP(string $phoneNumber, ?int $customerId = null): ?WhatsAppMessage
    {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $message = "Your CourierLab verification code is: *{$code}*\n\nThis code will expire in 10 minutes.\n\nDo not share this code with anyone.";

        $whatsappMessage = WhatsAppMessage::sendOTP($phoneNumber, $code, $customerId);

        // Actually send via API
        $this->sendMessage($phoneNumber, $message, [
            'type' => 'otp',
            'code' => $code,
        ]);

        return $whatsappMessage;
    }

    /**
     * Send tracking update via WhatsApp
     */
    public function sendTrackingUpdate(CourierInfo $courier, string $updateType = 'status_change'): ?WhatsAppMessage
    {
        $customer = $courier->senderCustomer ?? $courier->receiverCustomer;

        if (!$customer || !$customer->mobile) {
            return null;
        }

        $messages = [
            'status_change' => "📦 *Shipment Update*\n\nTracking: {$courier->code}\nStatus: " . $this->getCourierStatusText($courier->status) . "\n\nTrack your shipment: " . route('customer.track') . "?code={$courier->code}",
            'picked_up' => "✅ *Package Picked Up*\n\nYour shipment {$courier->code} has been picked up and is on its way!",
            'in_transit' => "🚚 *In Transit*\n\nYour shipment {$courier->code} is currently in transit to {$courier->receiverBranch->name}.",
            'delivered' => "🎉 *Delivered!*\n\nYour shipment {$courier->code} has been successfully delivered!\n\nPlease rate your experience: " . route('customer.shipment.rate', $courier->id),
        ];

        $message = $messages[$updateType] ?? $messages['status_change'];

        $whatsappMessage = WhatsAppMessage::sendTrackingUpdate($courier, $message);

        // Actually send via API
        $this->sendMessage($customer->mobile, $message, [
            'type' => 'tracking_update',
            'courier_id' => $courier->id,
            'update_type' => $updateType,
        ]);

        return $whatsappMessage;
    }

    /**
     * Handle incoming WhatsApp message (webhook)
     */
    public function handleIncomingMessage(array $webhookData): void
    {
        try {
            if (!isset($webhookData['entry'][0]['changes'][0]['value']['messages'][0])) {
                return;
            }

            $messageData = $webhookData['entry'][0]['changes'][0]['value']['messages'][0];
            $from = $messageData['from'];
            $messageText = $messageData['text']['body'] ?? '';
            $messageId = $messageData['id'];

            // Store inbound message
            $whatsappMessage = WhatsAppMessage::create([
                'phone_number' => $from,
                'message_id' => $messageId,
                'direction' => WhatsAppMessage::DIRECTION_INBOUND,
                'message_type' => WhatsAppMessage::TYPE_TEXT,
                'message_content' => $messageText,
                'status' => WhatsAppMessage::STATUS_DELIVERED,
            ]);

            // Process message with bot
            $this->processBotMessage($whatsappMessage);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error: ' . $e->getMessage());
        }
    }

    /**
     * Process message with chatbot logic (FR-30)
     */
    protected function processBotMessage(WhatsAppMessage $inboundMessage): void
    {
        $messageText = strtolower(trim($inboundMessage->message_content));

        // Track shipment intent
        if (preg_match('/track|status|where|shipment/i', $messageText)) {
            $this->handleTrackingQuery($inboundMessage);
            return;
        }

        // FAQ intent
        if (preg_match('/help|how|what|price|cost|rate/i', $messageText)) {
            $this->handleFAQ($inboundMessage);
            return;
        }

        // Support intent
        if (preg_match('/support|issue|problem|complaint/i', $messageText)) {
            $this->handleSupportRequest($inboundMessage);
            return;
        }

        // Check if message contains tracking code
        if (preg_match('/[A-Z0-9]{10,}/', $messageText, $matches)) {
            $this->sendTrackingInfo($inboundMessage->phone_number, $matches[0]);
            return;
        }

        // Default response
        $this->sendDefaultResponse($inboundMessage->phone_number);
    }

    /**
     * Handle tracking query
     */
    protected function handleTrackingQuery(WhatsAppMessage $inboundMessage): void
    {
        $response = "📦 *Track Your Shipment*\n\nPlease send me your tracking code to get the latest status.\n\nYour tracking code looks like: ABC1234567890";

        $this->sendMessage($inboundMessage->phone_number, $response, [
            'bot_intent' => 'tracking_query',
        ]);

        $inboundMessage->update([
            'bot_intent' => WhatsAppMessage::INTENT_TRACK_SHIPMENT,
            'handled_by_bot' => true,
        ]);
    }

    /**
     * Send tracking information
     */
    protected function sendTrackingInfo(string $phoneNumber, string $trackingCode): void
    {
        $courier = CourierInfo::where('code', $trackingCode)->first();

        if (!$courier) {
            $response = "❌ Sorry, I couldn't find a shipment with tracking code: {$trackingCode}\n\nPlease check the code and try again.";
        } else {
            $status = $this->getCourierStatusText($courier->status);
            $response = "📦 *Shipment Status*\n\n";
            $response .= "Tracking: {$courier->code}\n";
            $response .= "Status: {$status}\n";
            $response .= "From: {$courier->senderBranch->name}\n";
            $response .= "To: {$courier->receiverBranch->name}\n\n";
            $response .= "View full details: " . route('customer.track') . "?code={$courier->code}";
        }

        $this->sendMessage($phoneNumber, $response);
    }

    /**
     * Handle FAQ
     */
    protected function handleFAQ(WhatsAppMessage $inboundMessage): void
    {
        $faqs = "❓ *Frequently Asked Questions*\n\n";
        $faqs .= "1️⃣ How to track my shipment?\nSend me your tracking code\n\n";
        $faqs .= "2️⃣ What are your rates?\nVisit: " . url('/') . "\n\n";
        $faqs .= "3️⃣ How long does delivery take?\nStandard: 5-7 days\nExpress: 2-3 days\n\n";
        $faqs .= "4️⃣ Need more help?\nType 'support' to talk to our team";

        $this->sendMessage($inboundMessage->phone_number, $faqs);

        $inboundMessage->update([
            'bot_intent' => WhatsAppMessage::INTENT_FAQ,
            'handled_by_bot' => true,
        ]);
    }

    /**
     * Handle support request
     */
    protected function handleSupportRequest(WhatsAppMessage $inboundMessage): void
    {
        $response = "🆘 *Customer Support*\n\nOur team is here to help!\n\nPlease describe your issue and we'll get back to you as soon as possible.\n\nOr call us at: +1-800-COURIER\nEmail: support@courierlab.com";

        $this->sendMessage($inboundMessage->phone_number, $response);

        // Escalate to human
        $inboundMessage->escalate();

        // Notify support team
        $admins = \App\Models\Admin::where('status', 1)->get();
        foreach ($admins as $admin) {
            notify($admin, 'WHATSAPP_SUPPORT_REQUEST', [
                'phone' => $inboundMessage->phone_number,
                'message' => $inboundMessage->message_content,
            ]);
        }
    }

    /**
     * Send default response
     */
    protected function sendDefaultResponse(string $phoneNumber): void
    {
        $response = "👋 *Welcome to CourierLab!*\n\nI can help you with:\n\n";
        $response .= "📦 Track your shipment\n";
        $response .= "❓ Answer FAQs\n";
        $response .= "🆘 Contact support\n\n";
        $response .= "What would you like to do?";

        $this->sendMessage($phoneNumber, $response);
    }

    /**
     * Get courier status text
     */
    protected function getCourierStatusText(int $status): string
    {
        return match($status) {
            0 => '📥 Queue - Awaiting dispatch',
            1 => '🚚 In Transit',
            2 => '📍 At destination - Ready for delivery',
            3 => '✅ Delivered',
            default => 'Unknown',
        };
    }

    /**
     * Format phone number for WhatsApp
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add country code if not present
        if (!str_starts_with($phone, '1') && strlen($phone) == 10) {
            $phone = '1' . $phone; // Assume US
        }

        return $phone;
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(string $phoneNumber, string $code): bool
    {
        $otpMessage = WhatsAppMessage::unverifiedOTPs()
            ->where('phone_number', $phoneNumber)
            ->latest()
            ->first();

        if (!$otpMessage) {
            return false;
        }

        return $otpMessage->verifyOTP($code);
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory(string $phoneNumber, int $limit = 50)
    {
        return WhatsAppMessage::where('phone_number', $phoneNumber)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get escalated conversations
     */
    public function getEscalatedConversations()
    {
        return WhatsAppMessage::escalated()
            ->with('customer')
            ->latest()
            ->get()
            ->groupBy('phone_number');
    }
}
