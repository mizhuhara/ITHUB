<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'role', 'status', 'email_verified_at',
    'avatar', 'portfolio_url', 'instagram_url', 'linkedin_url', 'cv_path',
    'org_contact', 'org_phone', 'org_address', 'org_doc',
    'membership_status', 'membership_plan', 'membership_started_at', 'membership_expires_at', 'is_premium_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    const ADMIN_EMAIL = 'admin@indotech.id';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'email_verified_at',
        'avatar',
        'portfolio_url',
        'instagram_url',
        'linkedin_url',
        'cv_path',
        'org_contact',
        'org_phone',
        'org_address',
        'org_doc',
        'membership_status',
        'membership_plan',
        'membership_started_at',
        'membership_expires_at',
        'is_premium_active',
    ];

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            $instLogo = $this->school?->logo_url ?? $this->university?->logo_url ?? $this->company?->logo_url;
            if ($instLogo) {
                if (str_starts_with($instLogo, 'http://') || str_starts_with($instLogo, 'https://')) {
                    return $instLogo;
                }

                return asset(ltrim($instLogo, '/'));
            }

            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        if (str_starts_with($this->avatar, '/storage/')) {
            return asset(ltrim($this->avatar, '/'));
        }

        return asset('storage/'.ltrim($this->avatar, '/'));
    }

    public function getCvUrlAttribute(): ?string
    {
        if (! $this->cv_path) {
            return null;
        }

        if (str_starts_with($this->cv_path, 'http://') || str_starts_with($this->cv_path, 'https://')) {
            return $this->cv_path;
        }

        return asset('storage/'.$this->cv_path);
    }

    public function getCvNameAttribute(): ?string
    {
        if (! $this->cv_path) {
            return null;
        }

        return basename($this->cv_path);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function createdCommunities(): HasMany
    {
        return $this->hasMany(Community::class, 'user_id');
    }

    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class)
            ->withPivot(['role', 'is_banned'])
            ->withTimestamps();
    }

    public function communityMessages(): HasMany
    {
        return $this->hasMany(CommunityMessage::class);
    }

    public function isInstitution(): bool
    {
        return in_array($this->role, ['school', 'university', 'company'], true);
    }

    public function hasActiveMembership(): bool
    {
        if (! $this->isInstitution()) {
            return true;
        }

        // Global master switch: if admin turned off the premium requirement globally, allow all
        if (! AppSetting::isPremiumFeatureEnabled()) {
            return true;
        }

        // If admin turned off premium for this specific institution
        if (! $this->is_premium_active) {
            return false;
        }

        // Check if expiration date has passed
        if ($this->membership_expires_at && $this->membership_expires_at->isPast()) {
            return false;
        }

        return in_array(strtolower((string) $this->membership_status), ['trial', 'active', 'premium'], true);
    }

    public function getMembershipDaysRemainingAttribute(): int
    {
        if (! $this->membership_expires_at) {
            return 0;
        }

        if ($this->membership_expires_at->isPast()) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->membership_expires_at, false) + 1);
    }

    public function getMembershipStatusLabelAttribute(): string
    {
        if (! $this->is_premium_active) {
            return 'Dinonaktifkan Admin';
        }

        if ($this->membership_expires_at && $this->membership_expires_at->isPast()) {
            return 'Kadaluarsa (Harus Member)';
        }

        $status = strtolower((string) ($this->membership_status ?? 'trial'));

        return match ($status) {
            'trial' => 'Trial Gratis 1 Bulan',
            'active', 'premium' => 'Member Aktif',
            'expired' => 'Kadaluarsa',
            'suspended' => 'Ditangguhkan',
            default => ucfirst($status),
        };
    }

    public function getMembershipBadgeClassAttribute(): string
    {
        if (! $this->is_premium_active) {
            return 'bg-rose-50 text-rose-700 border border-rose-200';
        }

        if ($this->membership_expires_at && $this->membership_expires_at->isPast()) {
            return 'bg-amber-50 text-amber-700 border border-amber-200';
        }

        $status = strtolower((string) ($this->membership_status ?? 'trial'));

        return match ($status) {
            'trial' => 'bg-blue-50 text-blue-700 border border-blue-200',
            'active', 'premium' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            default => 'bg-slate-50 text-slate-700 border border-slate-200',
        };
    }

    public function school()
    {
        return $this->hasOne(School::class);
    }

    public function university()
    {
        return $this->hasOne(University::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function membershipPayments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class);
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(OrganizerBankAccount::class)->where('is_active', true)->latestOfMany();
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(OrganizerBankAccount::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'membership_started_at' => 'datetime',
            'membership_expires_at' => 'datetime',
            'is_premium_active' => 'boolean',
        ];
    }
}
