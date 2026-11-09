<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class University extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'npsn',
        'type',
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
        'gallery',
        'latitude',
        'longitude',
        'map_link',
        'tags',
        'total_students',
        'total_faculties',
        'accreditation',
        'founded',
        'views',
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'gallery' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
            'total_students' => 'integer',
            'total_faculties' => 'integer',
            'founded' => 'integer',
            'views' => 'integer',
        ];
    }
}
