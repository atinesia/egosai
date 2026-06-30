<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'model', 'system_prompt', 'temperature',
        'is_ai_globally_active', 'fallback_message',
    ];

    protected $casts = [
        'temperature' => 'float',
        'is_ai_globally_active' => 'boolean',
    ];
}
