<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    protected $fillable = [
        'customer_id',
        'phone_number',
        'message_id',
        'conversation_id',
        'direction',
        'message_type',
        'message_content',
        'metadata',
        'otp_code',
        'otp_expires_at',
        'otp_verified',
        'courier_info_id',
        'update_type',
        'status',
        'whatsapp_status',
        'delivered_at',
        'read_at',
        'bot_intent',
        'handled_by_bot',
        'escalated_to_human',
    ];

    protected $casts = [
        'metadata' => 'array',
        'otp_expires_at' => 'datetime',
        'otp_verified' => 'boolean',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'handled_by_bot' => 'boolean',
        'escalated_to_human' => 'boolean',
    ];

    // Direction
    const DIRECTION_INBOUND = 'inbound';
    const DIRECTION_OUTBOUND = 'outbound';

    // Message types
    const TYPE_TEXT = 'text';
    const TYPE_OTP = 'otp';
    const TYPE_ORDER_UPDATE = 'order_update';
    const TYPE_FAQ_RESPONSE = 'faq_response';
    const TYPE_IMAGE = 'image';

    // Status
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';
    const STATUS_FAILED = 'failed';

    // Bot intents
    const INTENT_TRACK_SHIPMENT = 'track_shipment';
    const INTENT_FAQ = 'faq';
    const INTENT_SUPPORT = 'support';
    const INTENT_OTP = 'otp';

    /**
     * Boot method
     */
    protected static function booted()
    {
        static::creating(function ($message) {
            // Auto-generate conversation ID for new conversations
            if (!$message->conversation_id && $message->direction === self::DIRECTION_INBOUND) {
                $message->conversation_id = 'conv_' . uniqid();
            }
        });
    }

    /**
     * Relationships
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function courierInfo(): BelongsTo
    {
        return $this->belongsTo(CourierInfo::class);
    }

    /**
     * Scopes
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', self::DIRECTION_INBOUND);
    }

    public function scopeOutbound($query)
    {
        return $query->where('direction', self::DIRECTION_OUTBOUND);
    }

    public function scopeByConversation($query, string $conversationId)
    {
        return $query->where('conversation_id', $conversationId)
                     ->orderBy('created_at');
    }

    public function scopeOTPs($query)
    {
        return $query->where('message_type', self::TYPE_OTP);
    }

    public function scopeUnverifiedOTPs($query)
    {
        return $query->where('message_type', self::TYPE_OTP)
                     ->where('otp_verified', false)
                     ->where('otp_expires_at', '>', now());
    }

    public function scopeEscalated($query)
    {
        return $query->where('escalated_to_human', true)
                     ->where('direction', self::DIRECTION_INBOUND);
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(string $code): bool
    {
        if ($this->message_type !== self::TYPE_OTP) {
            return false;
        }

        if ($this->otp_code === $code && now()->lessThan($this->otp_expires_at)) {
            $this->update(['otp_verified' => true]);
            return true;
        }

        return false;
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'whatsapp_status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'status' => self::STATUS_READ,
            'whatsapp_status' => 'read',
            'read_at' => now(),
        ]);
    }

    /**
     * Escalate to human support
     */
    public function escalate(): void
    {
        $this->update([
            'escalated_to_human' => true,
            'handled_by_bot' => false,
        ]);
    }

    /**
     * Get conversation messages
     */
    public function getConversationMessages()
    {
        if (!$this->conversation_id) {
            return collect([$this]);
        }

        return self::byConversation($this->conversation_id)->get();
    }

    /**
     * Create OTP message
     */
    public static function sendOTP(string $phoneNumber, string $code, ?int $customerId = null): self
    {
        return self::create([
            'customer_id' => $customerId,
            'phone_number' => $phoneNumber,
            'direction' => self::DIRECTION_OUTBOUND,
            'message_type' => self::TYPE_OTP,
            'message_content' => "Your CourierLab verification code is: {$code}. Valid for 10 minutes.",
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes(10),
            'status' => self::STATUS_SENT,
            'bot_intent' => self::INTENT_OTP,
        ]);
    }

    /**
     * Send tracking update
     */
    public static function sendTrackingUpdate(CourierInfo $courier, string $updateMessage): self
    {
        $customer = $courier->senderCustomer ?? $courier->receiverCustomer;

        return self::create([
            'customer_id' => $customer->id ?? null,
            'phone_number' => $customer->mobile ?? '',
            'courier_info_id' => $courier->id,
            'direction' => self::DIRECTION_OUTBOUND,
            'message_type' => self::TYPE_ORDER_UPDATE,
            'message_content' => $updateMessage,
            'update_type' => 'status_change',
            'status' => self::STATUS_SENT,
        ]);
    }
}
