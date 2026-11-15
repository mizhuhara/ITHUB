<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    use HasFactory;

    protected $table = 'job_listings';

    protected $guarded = ['id'];

    protected $casts = [
        'skills' => 'array',
        'tags' => 'array',
        'requirements' => 'array',
        'responsibilities' => 'array',
        'is_active' => 'boolean',
        'total_views' => 'integer',
        'applicants_count' => 'integer',
    ];

    protected $appends = [
        'date_posted',
        'job_type',
    ];

    protected static function booted(): void
    {
        static::creating(function (JobListing $job) {
            if (empty($job->code)) {
                $job->code = 'JOB-'.rand(1000, 9999);
            }

            if (empty($job->logo_text)) {
                $words = preg_split('/\s+/', trim($job->company ?? ''));
                $initials = '';
                foreach (array_slice($words, 0, 2) as $w) {
                    $initials .= strtoupper(substr($w, 0, 1));
                }
                $job->logo_text = ! empty($initials) ? $initials : 'IT';
            }

            if (empty($job->category)) {
                $typeLower = strtolower($job->type ?? '');
                $locLower = strtolower($job->location ?? '');
                if (str_contains($typeLower, 'intern')) {
                    $job->category = 'internship';
                } elseif (str_contains($typeLower, 'contract') || str_contains($typeLower, 'freelance')) {
                    $job->category = 'freelance';
                } elseif (str_contains($locLower, 'remote')) {
                    $job->category = 'remote';
                } else {
                    $job->category = 'jobs';
                }
            }

            if (empty($job->tab_status)) {
                if ($job->status === 'Draft' || ! $job->is_active) {
                    $job->tab_status = 'drafts';
                } elseif ($job->status === 'Closed') {
                    $job->tab_status = 'closed';
                } else {
                    $job->tab_status = 'active';
                }
            }

            if (empty($job->location_full) && ! empty($job->location)) {
                $job->location_full = $job->location;
            }

            if (empty($job->skills)) {
                $job->skills = ['IT', 'Tech'];
            }

            if (empty($job->tags)) {
                $job->tags = array_filter([$job->department, $job->type, $job->category]);
            }
        });

        static::updating(function (JobListing $job) {
            if ($job->isDirty('status') || $job->isDirty('is_active')) {
                if ($job->status === 'Draft' || ! $job->is_active) {
                    $job->tab_status = 'drafts';
                } elseif ($job->status === 'Closed') {
                    $job->tab_status = 'closed';
                } else {
                    $job->tab_status = 'active';
                }
            }
        });
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getApplyTypeAttribute(): string
    {
        return 'job';
    }

    public function getDatePostedAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : date('M d, Y');
    }

    public function getJobTypeAttribute(): string
    {
        return $this->type ?? 'Full-time';
    }

    public function getCompanyLogoAttribute(): ?string
    {
        if ($this->user_id) {
            $company = Company::where('user_id', $this->user_id)->first();
            if ($company && ! empty($company->logo_url)) {
                $compLogo = $company->logo_url;
                if (str_starts_with($compLogo, 'http://') || str_starts_with($compLogo, 'https://')) {
                    return $compLogo;
                }

                return asset(ltrim($compLogo, '/'));
            }

            $user = $this->user;
            if ($user && ! empty($user->avatar_url)) {
                return $user->avatar_url;
            }

            $university = University::where('user_id', $this->user_id)->first();
            if ($university && ! empty($university->logo_url)) {
                $univLogo = $university->logo_url;
                if (str_starts_with($univLogo, 'http://') || str_starts_with($univLogo, 'https://')) {
                    return $univLogo;
                }

                return asset(ltrim($univLogo, '/'));
            }

            $school = School::where('user_id', $this->user_id)->first();
            if ($school && ! empty($school->logo_url)) {
                $schLogo = $school->logo_url;
                if (str_starts_with($schLogo, 'http://') || str_starts_with($schLogo, 'https://')) {
                    return $schLogo;
                }

                return asset(ltrim($schLogo, '/'));
            }
        }

        if (! empty($this->company)) {
            $company = Company::where('name', $this->company)->first();
            if ($company && ! empty($company->logo_url)) {
                $compLogo = $company->logo_url;
                if (str_starts_with($compLogo, 'http://') || str_starts_with($compLogo, 'https://')) {
                    return $compLogo;
                }

                return asset(ltrim($compLogo, '/'));
            }

            $univ = University::where('name', $this->company)->first();
            if ($univ && ! empty($univ->logo_url)) {
                $univLogo = $univ->logo_url;

                return str_starts_with($univLogo, 'http') ? $univLogo : asset(ltrim($univLogo, '/'));
            }

            $sch = School::where('name', $this->company)->first();
            if ($sch && ! empty($sch->logo_url)) {
                $schLogo = $sch->logo_url;

                return str_starts_with($schLogo, 'http') ? $schLogo : asset(ltrim($schLogo, '/'));
            }
        }

        return null;
    }

    public function getLogoUrlAttribute(?string $value): ?string
    {
        $companyLogo = $this->company_logo;
        if (! empty($companyLogo)) {
            return $companyLogo;
        }

        $banner = $this->attributes['image'] ?? null;
        if (! empty($value) && $value !== $banner) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            return asset(ltrim($value, '/'));
        }

        return null;
    }

    public function getImageAttribute(?string $value): ?string
    {
        if (! empty($value)) {
            return $value;
        }

        return $this->attributes['logo_url'] ?? null;
    }

    public function getLogoColorAttribute(?string $value): string
    {
        if (! empty($value)) {
            return $value;
        }

        $colors = ['#0b57d0', '#e60012', '#ff6b35', '#00aa13', '#7c3aed', '#06b6d4', '#10b981', '#ee4d2d'];
        $index = abs(crc32($this->company ?? 'IndoTech')) % count($colors);

        return $colors[$index];
    }

    public function getDeadlineAttribute(?string $value): string
    {
        if (! empty($value)) {
            return $value;
        }

        return $this->created_at ? $this->created_at->addDays(30)->format('d M Y') : date('d M Y', strtotime('+30 days'));
    }

    public function getSalaryRangeAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return 'Negotiable';
        }

        if (is_numeric($value)) {
            return 'IDR '.number_format((float) $value, 0, ',', '.');
        }

        return $value;
    }

    public function getRequirementsAttribute($value): array
    {
        return $this->normalizeListAttribute($value);
    }

    public function getResponsibilitiesAttribute($value): array
    {
        return $this->normalizeListAttribute($value);
    }

    public function getSkillsAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (empty($value)) {
            return [];
        }
        $decoded = json_decode((string) $value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $value))));
    }

    private function normalizeListAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (empty($value)) {
            return [];
        }
        $decoded = json_decode((string) $value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $str = trim(is_string($decoded) ? $decoded : (string) $value, " \t\n\r\0\x0B\"");

        return array_values(array_filter(array_map(
            fn ($l) => trim(ltrim($l, "-* \t")),
            preg_split('/(?:\\\\n|[\r\n])+/', $str)
        )));
    }
}
