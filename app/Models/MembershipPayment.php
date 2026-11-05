<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institution_type',
        'plan',
        'amount',
        'sender_bank',
        'sender_name',
        'sender_account_number',
        'payment_proof',
        'notes',
        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getProofUrlAttribute(): string
    {
        if (str_starts_with($this->payment_proof, 'http://') || str_starts_with($this->payment_proof, 'https://')) {
            return $this->payment_proof;
        }

        return asset('storage/'.$this->payment_proof);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
            'approved' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
            default => 'bg-slate-50 text-slate-700 border border-slate-200',
        };
    }

    public function getPlanLabelAttribute(): string
    {
        return match ($this->plan) {
            '1_month' => 'Paket 1 Bulan',
            '1_year' => 'Paket 1 Tahun (12 Bulan)',
            default => ucfirst(str_replace('_', ' ', $this->plan)),
        };
    }
}
