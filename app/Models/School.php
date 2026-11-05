<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    protected $fillable = [
        'user_id', 'npsn', 'name', 'institution_type', 'city', 'province', 'location',
        'address', 'latitude', 'longitude', 'map_link', 'status', 'logo_url', 'logo_name',
        'logo_text', 'logo_bg', 'email', 'website', 'phone', 'description', 'gallery',
        'tags', 'competencies', 'total_students', 'industry_partners', 'placement_rate',
        'modern_labs', 'founded', 'accreditation',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'gallery' => 'array',
            'competencies' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        if (! AppSetting::isPremiumFeatureEnabled()) {
            return $query;
        }

        return $query->where(function (Builder $subQuery) {
            $subQuery->whereNull('user_id')
                ->orWhereHas('user', function (Builder $userQuery) {
                    $userQuery->where('is_premium_active', true)
                        ->where(function (Builder $q) {
                            $q->whereNull('membership_expires_at')
                                ->orWhere('membership_expires_at', '>=', now());
                        });
                });
        });
    }
}
