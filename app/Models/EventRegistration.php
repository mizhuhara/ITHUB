<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'email',
        'phone',
        'ticket_code',
        'checked_in_at',
        'amount',
        'payment_status',
        'payment_proof',
        'sender_bank',
        'sender_name',
        'payment_notes',
        'confirmed_at',
        'confirmed_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'rejected_at' => 'datetime',
            'amount' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $registration) {
            $registration->ticket_code ??= 'EVT-'.Str::upper(Str::random(8));
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return in_array($this->payment_status, ['confirmed', 'free'], true);
    }

    public function isRejected(): bool
    {
        return $this->payment_status === 'rejected';
    }
}
