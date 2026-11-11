<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    use HasFactory;

    public const MAX_ACTIVE_SLIDES = 5;

    protected $fillable = [
        'user_id',
        'title',
        'subtitle',
        'image',
        'link',
        'link_text',
        'is_active',
        'sort_order',
        'status',
        'queue_position',
        'started_at',
        'expires_at',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'queue_position' => 'integer',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeQueued(Builder $query): Builder
    {
        return $query->where('status', 'queued')
            ->orderBy('queue_position')
            ->orderBy('created_at');
    }

    /**
     * Check if this banner is currently active on the homepage slider.
     */
    public function isCurrentlyActive(): bool
    {
        if ($this->status !== 'active' || ! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Synchronize the banner queue:
     * 1. Mark expired active banners as 'expired'
     * 2. Promote top queued banners if active count < MAX_ACTIVE_SLIDES
     * 3. Re-index queue positions
     */
    public static function syncActiveQueue(): void
    {
        // 1. Expire banners whose display time has ended
        static::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update([
                'status' => 'expired',
                'is_active' => false,
            ]);

        // 2. Count active banners
        $activeCount = static::where('status', 'active')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $availableSlots = max(0, self::MAX_ACTIVE_SLIDES - $activeCount);

        // 3. Promote queued banners if slots are open
        if ($availableSlots > 0) {
            $toPromote = static::where('status', 'queued')
                ->orderBy('queue_position')
                ->orderBy('created_at')
                ->take($availableSlots)
                ->get();

            foreach ($toPromote as $banner) {
                $banner->update([
                    'status' => 'active',
                    'is_active' => true,
                    'queue_position' => null,
                    'started_at' => now(),
                    'expires_at' => now()->addDays(7),
                ]);
            }
        }

        // 4. Re-index remaining queued banners
        $remainingQueued = static::where('status', 'queued')
            ->orderBy('queue_position')
            ->orderBy('created_at')
            ->get();

        $pos = 1;
        foreach ($remainingQueued as $qBanner) {
            if ($qBanner->queue_position !== $pos) {
                $qBanner->update(['queue_position' => $pos]);
            }
            $pos++;
        }
    }
}
