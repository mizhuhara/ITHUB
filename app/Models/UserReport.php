<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReport extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'subject',
        'description',
        'priority',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, string> */
    public static function categoryLabels(): array
    {
        return [
            'Bug' => 'Bug / Error Sistem',
            'Konten' => 'Konten Tidak Sesuai',
            'Teknis' => 'Masalah Teknis',
            'Lainnya' => 'Lainnya',
        ];
    }

    /** @return array<string, string> */
    public static function priorityLabels(): array
    {
        return [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
        ];
    }

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return [
            'pending' => 'Menunggu',
            'investigating' => 'Sedang Ditinjau',
            'resolved' => 'Selesai',
            'closed' => 'Ditutup',
        ];
    }
}
