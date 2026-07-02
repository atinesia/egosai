<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'broadcast_id',
        'contact_id',
        'whatsapp_session_id',
        'status',
        'error_message'
    ];

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function whatsappSession()
    {
        return $this->belongsTo(WhatsappSession::class);
    }
}
