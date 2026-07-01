<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'business_category', 'description', 'slug', 'plan',
        'is_active', 'trial_ends_at', 'onboarding_completed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'onboarding_completed_at' => 'datetime',
    ];

    public function isOnboarded(): bool
    {
        return ! is_null($this->onboarding_completed_at);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function knowledgeBases()
    {
        return $this->hasMany(KnowledgeBase::class);
    }

    public function aiSetting()
    {
        return $this->hasOne(AiSetting::class);
    }

    /**
     * @deprecated Tenant sekarang bisa punya lebih dari 1 nomor WhatsApp
     * (paket Pro/Enterprise). Method ini dipertahankan supaya kode lama yang
     * masih panggil `$tenant->whatsappSession` (singular) tidak langsung
     * rusak — selalu mengembalikan nomor yang PERTAMA terhubung saja.
     * Pakai `whatsappSessions()` untuk kode baru.
     */
    public function whatsappSession()
    {
        return $this->hasOne(WhatsappSession::class)->oldestOfMany();
    }

    public function whatsappSessions()
    {
        return $this->hasMany(WhatsappSession::class);
    }

    /**
     * Batas jumlah nomor WhatsApp yang boleh dihubungkan, sesuai paket.
     * null artinya tidak terbatas (Enterprise/custom).
     */
    public function maxWhatsappNumbers(): ?int
    {
        return match ($this->plan) {
            'trial', 'starter' => 1,
            'pro' => 3,
            'enterprise' => null,
            default => 1,
        };
    }

    public function canAddWhatsappNumber(): bool
    {
        $max = $this->maxWhatsappNumbers();

        if (is_null($max)) {
            return true;
        }

        return $this->whatsappSessions()->count() < $max;
    }
}
