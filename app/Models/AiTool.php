<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiTool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'tagline',
        'pricing',
        'url',
        'logo_url',
        'icon_type',
        'icon_bg',
        'description',
        'overview',
        'what_it_does',
        'capabilities',
        'use_cases',
        'target_audience',
        'action_type',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'use_cases' => 'array',
    ];
}
