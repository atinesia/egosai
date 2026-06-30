<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'model',
        'system_prompt',
        'temperature',
        'is_ai_globally_active',
        'fallback_message',
    ];

    protected $casts = [
        'temperature' => 'float',
        'is_ai_globally_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($aiSetting) {
            // Jika system_prompt kosong atau null, isi dengan default prompt
            if (empty($aiSetting->system_prompt)) {
                $aiSetting->system_prompt = "Anda adalah AI Agen multi-tenant yang cerdas, profesional, dan siap membantu pelanggan dengan ramah.";
            }
        });
    }
}
