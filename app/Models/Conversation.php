<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'contact_id',
        'whatsapp_session_id',
        'assigned_user_id',
        'channel',
        'status',
        'ai_active',
        'last_message_at',
    ];

    protected $casts = [
        'ai_active' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function whatsappSession()
    {
        return $this->belongsTo(WhatsappSession::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Nonaktifkan AI dan tandai chat perlu penanganan agen manusia.
     */
    public function disableAi(): bool
    {
        return $this->update([
            'ai_active' => false,
            'status' => 'open' // Memastikan status tetap open agar muncul di antrean CS
        ]);
    }

    /**
     * Aktifkan kembali AI (misal setelah agen menyelesaikan chat).
     */
    public function enableAi(): bool
    {
        return $this->update([
            'ai_active' => true,
            'status' => 'pending' // Atau status default lain sesuai alur Anda
        ]);
    }
}
