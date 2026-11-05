<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSeeker extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'location',
        'job_title',
        'skills',
        'experience_level',
        'expected_salary',
        'about',
        'portfolio_url',
        'linkedin_url',
        'photo_url',
        'availability',
        'status',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(?string $value): ?string
    {
        if (! empty($value)) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            return asset('storage/'.$value);
        }

        if ($this->user && $this->user->avatar_url) {
            return $this->user->avatar_url;
        }

        if (! empty($this->email)) {
            $u = User::where('email', $this->email)->first();
            if ($u && $u->avatar_url) {
                return $u->avatar_url;
            }
        }

        return null;
    }
}
