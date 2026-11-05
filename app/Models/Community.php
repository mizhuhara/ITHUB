<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'type',
        'mode',
        'location',
        'language',
        'members',
        'status',
        'approval_status',
        'description',
        'rules',
        'website',
        'email',
        'logo_url',
        'cover_url',
        'tags',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'resubmit_notes',
    ];

    protected $casts = [
        'members' => 'integer',
        'tags' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function getFormattedModeAttribute(): string
    {
        return match (strtolower((string) $this->mode)) {
            'offline' => 'Offline',
            'hybrid' => 'Hybrid',
            default => 'Online',
        };
    }

    public function getFormattedTypeAttribute(): string
    {
        return match (strtolower((string) $this->type)) {
            'private', 'privat' => 'Privat',
            default => 'Publik',
        };
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'is_banned'])
            ->withTimestamps();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CommunityMessage::class);
    }

    public function canAccessChat(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (! $this->isApproved() || $this->status !== 'active') {
            return false;
        }

        if ($this->members()->where('user_id', $user->id)->wherePivot('is_banned', true)->exists()) {
            return false;
        }

        if ($this->isOwnerOrAdmin($user)) {
            return true;
        }

        if ($this->type === 'private') {
            return $this->members()->where('user_id', $user->id)->wherePivot('is_banned', false)->exists();
        }

        return true;
    }

    public function canSendChatMessage(?User $user): bool
    {
        if (! $this->canAccessChat($user)) {
            return false;
        }

        return $this->isOwnerOrAdmin($user)
            || $this->members()->where('user_id', $user->id)->wherePivot('is_banned', false)->exists();
    }

    public function isOwnerOrAdmin(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        return $this->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function isOwner(?User $user): bool
    {
        return $user && $this->user_id === $user->id;
    }

    public function isModerator(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'moderator')
            ->exists();
    }

    public function canModerate(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->isOwnerOrAdmin($user) || $this->isModerator($user);
    }

    public function getUserRole(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        if ($this->user_id === $user->id) {
            return 'admin';
        }

        $member = $this->members()->where('user_id', $user->id)->first();
        if (! $member) {
            return null;
        }

        return $member->pivot->role ?: 'member';
    }

    public function canKickMember(?User $actor, User $target): bool
    {
        if (! $actor || ! $target) {
            return false;
        }

        // Cannot kick self
        if ($actor->id === $target->id) {
            return false;
        }

        // Cannot kick community creator / owner
        if ($target->id === $this->user_id) {
            return false;
        }

        // Target must be a member
        $targetMembership = $this->members()->where('user_id', $target->id)->first();
        if (! $targetMembership) {
            return false;
        }

        $targetRole = $targetMembership->pivot->role ?: 'member';
        $actorRole = $this->getUserRole($actor);

        if (! $actorRole) {
            return false;
        }

        // Community owner can kick admins, moderators, and members
        if ($actor->id === $this->user_id) {
            return true;
        }

        // Admin can kick moderators and members
        if ($actorRole === 'admin') {
            return in_array($targetRole, ['moderator', 'member'], true);
        }

        // Moderator can only kick regular members
        if ($actorRole === 'moderator') {
            return $targetRole === 'member';
        }

        return false;
    }

    public function canAssignRoles(?User $actor): bool
    {
        if (! $actor) {
            return false;
        }

        // Only community creator / owner can assign or change roles
        return $this->user_id === $actor->id;
    }

    public function canBanMember(?User $actor, User $target): bool
    {
        if (! $actor || ! $target) {
            return false;
        }

        // Cannot ban self
        if ($actor->id === $target->id) {
            return false;
        }

        // Cannot ban community creator / owner
        if ($target->id === $this->user_id) {
            return false;
        }

        // Target must be a member
        $targetMembership = $this->members()->where('user_id', $target->id)->first();
        if (! $targetMembership) {
            return false;
        }

        $targetRole = $targetMembership->pivot->role ?: 'member';
        $actorRole = $this->getUserRole($actor);

        if (! $actorRole) {
            return false;
        }

        // Community owner can ban anyone (except self)
        if ($actor->id === $this->user_id) {
            return true;
        }

        // Admin can ban moderators and regular members
        if ($actorRole === 'admin') {
            return in_array($targetRole, ['moderator', 'member'], true);
        }

        // Moderator can only ban regular members
        if ($actorRole === 'moderator') {
            return $targetRole === 'member';
        }

        return false;
    }
}
