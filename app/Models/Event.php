<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'events';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'organizer',
        'organizer_type',
        'category',
        'mode',
        'price',
        'date',
        'short_date',
        'full_date',
        'time',
        'location',
        'quota',
        'total_quota',
        'contact_email',
        'contact_phone',
        'description',
        'what_you_will_learn',
        'speakers',
        'image',
        'status',
        'is_verified',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'what_you_will_learn' => 'array',
            'speakers' => 'array',
            'is_verified' => 'boolean',
            'total_quota' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Check if the event is a paid event.
     */
    public function isPaid(): bool
    {
        $clean = strtolower(trim((string) $this->price));
        if ($clean === '' || $clean === 'free' || $clean === 'gratis' || $clean === '0') {
            return false;
        }

        $numeric = (int) preg_replace('/[^\d]/', '', $clean);

        return $numeric > 0;
    }

    /**
     * Get numeric price (e.g. 50000).
     */
    public function getPriceNumericAttribute(): int
    {
        return (int) preg_replace('/[^\d]/', '', (string) $this->price);
    }

    /**
     * Get clean formatted price string (e.g. 'Rp 50.000' or 'Gratis').
     */
    public function getFormattedPriceAttribute(): string
    {
        if (! $this->isPaid()) {
            return 'Gratis';
        }

        return 'Rp '.number_format($this->price_numeric, 0, ',', '.');
    }

    /**
     * Retrieve the organizer's active bank account.
     */
    public function getBankAccountAttribute(): ?OrganizerBankAccount
    {
        if ($this->user_id) {
            return OrganizerBankAccount::where('user_id', $this->user_id)
                ->where('is_active', true)
                ->latest()
                ->first();
        }

        return null;
    }

    /**
     * Computed quota filled (alias for the 'quota' column value).
     */
    public function getQuotaAttribute(?string $value): ?string
    {
        return $value ?: null;
    }

    /**
     * Compute remaining quota from total_quota minus current quota.
     */
    public function getTotalQuotaAttribute(?int $value): int
    {
        return $value ?? 0;
    }

    /**
     * Convenience for Blade — human-friendly category tag.
     */
    public function getCategoryTagAttribute(): string
    {
        return strtoupper(str_replace('_', ' ', $this->category));
    }

    /**
     * Convenience for Blade — tailwind color class based on mode.
     */
    public function getModeColorAttribute(): string
    {
        return match (strtolower($this->mode)) {
            'online', 'webinar' => 'bg-emerald-500',
            'in-person', 'onsite' => 'bg-amber-600',
            'hybrid' => 'bg-indigo-600',
            default => 'bg-slate-500',
        };
    }

    /**
     * Convenience for Blade — user-friendly organizer type.
     */
    public function getTypeAttribute(): string
    {
        return $this->organizer_type;
    }

    /**
     * Convenience for Blade — proposed date (the event's date field).
     */
    public function getProposedDateAttribute(): string
    {
        return $this->date;
    }

    /**
     * Convenience for Blade — organizer logo image URL.
     */
    public function getOrganizerLogoAttribute(): ?string
    {
        return $this->attributes['organizer_logo']
            ?? ($this->relationLoaded('organizer') ? $this->organizer->logo ?? null : null)
            ?? null;
    }

    /**
     * Eager-load / normalize learning list and speakers so Blade loops
     * always receive clean arrays.
     */
    public function loadLearningAndSpeakers(): void
    {
        // speakers and what_you_will_learn are already cast to array;
        // this method is a no-op hook for controller call-sites.
        $this->setAttribute('speakers', $this->speakers);
        $this->setAttribute('what_you_will_learn', $this->what_you_will_learn);
    }
}
