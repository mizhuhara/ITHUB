<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'npsn',
        'type',
        'industry',
        'city',
        'province',
        'location',
        'address',
        'status',
        'logo_url',
        'logo_name',
        'logo_text',
        'logo_bg',
        'email',
        'website',
        'phone',
        'description',
        'tags',
        'total_employees',
        'founded',
        'projects_completed',
        'specialties',
        'tech_stack',
        'gallery',
        'latitude',
        'longitude',
        'map_link',
        'views',
    ];

    protected $casts = [
        'tags' => 'array',
        'specialties' => 'array',
        'tech_stack' => 'array',
        'gallery' => 'array',
        'total_employees' => 'integer',
        'founded' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'views' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
