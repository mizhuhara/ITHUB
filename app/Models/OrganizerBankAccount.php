<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizerBankAccount extends Model
{
    use HasFactory;

    protected $table = 'organizer_bank_accounts';

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'account_holder',
        'qr_code_image',
        'instructions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
