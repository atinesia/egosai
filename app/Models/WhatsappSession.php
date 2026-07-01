<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappSession extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'session_id', 'label', 'status',
        'phone_number', 'qr_code', 'is_ai_active',
    ];

    protected $casts = [
        'is_ai_active' => 'boolean',
    ];

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function isPending(): bool
    {
        return $this->status === 'qr_pending';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'connected'   => 'Terhubung',
            'qr_pending'  => 'Menunggu Scan',
            'disconnected' => 'Terputus',
            default        => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'connected'   => 'teal',
            'qr_pending'  => 'amber',
            'disconnected' => 'slate',
            default        => 'slate',
        };
    }
}
