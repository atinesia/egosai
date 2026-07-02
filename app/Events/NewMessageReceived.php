<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Message $message,
        public readonly Conversation $conversation,
    ) {}

    /**
     * Channel privat per tenant — hanya agent dari tenant yang sama
     * yang subscribe dan terima notifikasi ini.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tenant.' . $this->conversation->tenant_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }

    public function broadcastWith(): array
    {
        $contact = $this->conversation->contact;
        $session = $this->conversation->whatsappSession;

        return [
            'conversation_id'  => $this->conversation->id,
            'contact_name'     => $contact->name ?? $contact->display_number,
            'contact_number'   => $contact->display_number,
            'message_preview'  => mb_strimwidth($this->message->content, 0, 80, '...'),
            'session_label'    => $session?->label ?? 'WhatsApp',
            'timestamp'        => $this->message->created_at->toIso8601String(),
        ];
    }
}
