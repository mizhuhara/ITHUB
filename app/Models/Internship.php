<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'company',
        'description',
        'profile_picture',
        'location',
        'start_date',
        'end_date',
        'status',
        'total_views',
    ];

    protected $casts = [
        'total_views' => 'integer',
    ];

    protected $appends = [
        'category',
        'type',
        'logo_text',
        'logo_color',
        'logo_url',
        'total_views',
        'date_posted',
        'salary_range',
        'experience',
        'deadline',
        'skills',
        'job_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function getTotalViewsAttribute(): int
    {
        return (int) ($this->attributes['total_views'] ?? 0);
    }

    public function getApplyTypeAttribute(): string
    {
        return 'internship';
    }

    // Compatibility methods for job card template
    public function getCategoryAttribute(): string
    {
        return 'internship';
    }

    public function getTypeAttribute(): string
    {
        return 'Internship';
    }

    public function getLogoTextAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->company ?? ''));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }

        return ! empty($initials) ? $initials : 'IN';
    }

    public function getLogoColorAttribute(): string
    {
        $colors = ['#d97706', '#f59e0b', '#fbbf24', '#fcd34d', '#fef08a'];
        $index = abs(crc32($this->company ?? 'IndoTech')) % count($colors);

        return $colors[$index];
    }

    public function getDatePostedAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : date('M d, Y');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! empty($this->profile_picture)) {
            if (str_starts_with($this->profile_picture, 'http://') || str_starts_with($this->profile_picture, 'https://')) {
                return $this->profile_picture;
            }

            return asset('storage/'.ltrim($this->profile_picture, '/'));
        }

        if ($this->relationLoaded('user') ? $this->user : $this->user()->first()) {
            $user = $this->relationLoaded('user') ? $this->user : $this->user()->first();
            if ($user?->company && ! empty($user->company->logo_url)) {
                $compLogo = $user->company->logo_url;
                if (str_starts_with($compLogo, 'http://') || str_starts_with($compLogo, 'https://')) {
                    return $compLogo;
                }

                return asset('storage/'.ltrim($compLogo, '/'));
            }

            if ($user?->avatar_url) {
                return $user->avatar_url;
            }
        }

        return null;
    }

    public function getImageAttribute(): string
    {
        if (! empty($this->profile_picture)) {
            return '/storage/'.$this->profile_picture;
        }

        return 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=200&fit=crop';
    }

    public function getSalaryRangeAttribute(): string
    {
        return 'Negotiable';
    }

    public function getExperienceAttribute(): string
    {
        return 'Entry';
    }

    public function getDeadlineAttribute(): string
    {
        if (! empty($this->end_date)) {
            return date('d M Y', strtotime($this->end_date));
        }

        return $this->created_at ? $this->created_at->addMonths(3)->format('d M Y') : date('d M Y', strtotime('+3 months'));
    }

    public function getSkillsAttribute(): array
    {
        return ['Internship', 'Opportunity', 'Growth'];
    }

    public function getJobTypeAttribute(): string
    {
        return 'Internship';
    }
}
