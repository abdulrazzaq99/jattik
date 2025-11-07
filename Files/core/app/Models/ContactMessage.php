<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'replied_by',
        'admin_reply',
        'replied_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    // Status
    const STATUS_NEW = 0;
    const STATUS_READ = 1;
    const STATUS_REPLIED = 2;
    const STATUS_CLOSED = 3;

    /**
     * Relationships
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'replied_by');
    }

    /**
     * Scopes
     */
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    public function scopeReplied($query)
    {
        return $query->where('status', self::STATUS_REPLIED);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_READ]);
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        if ($this->status == self::STATUS_NEW) {
            $this->update(['status' => self::STATUS_READ]);
        }
    }

    /**
     * Reply to message
     */
    public function reply(string $replyText, int $adminId): void
    {
        $this->update([
            'status' => self::STATUS_REPLIED,
            'admin_reply' => $replyText,
            'replied_by' => $adminId,
            'replied_at' => now(),
        ]);

        // Send email to customer
        if ($this->customer_id) {
            $customer = $this->customer;
            notify($customer, 'CONTACT_REPLY', [
                'name' => $this->name,
                'subject' => $this->subject,
                'reply' => $replyText,
            ]);
        } else {
            // Send to guest email
            notify((object)['email' => $this->email, 'username' => $this->name], 'CONTACT_REPLY', [
                'name' => $this->name,
                'subject' => $this->subject,
                'reply' => $replyText,
            ]);
        }
    }

    /**
     * Close message
     */
    public function close(): void
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NEW => '<span class="badge badge--danger">New</span>',
            self::STATUS_READ => '<span class="badge badge--warning">Read</span>',
            self::STATUS_REPLIED => '<span class="badge badge--success">Replied</span>',
            self::STATUS_CLOSED => '<span class="badge badge--dark">Closed</span>',
            default => '<span class="badge badge--secondary">Unknown</span>',
        };
    }
}
