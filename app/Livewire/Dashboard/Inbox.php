<?php

namespace App\Livewire\Dashboard;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappSession;
use App\Services\WhatsappService;
use Livewire\Component;

class Inbox extends Component
{
    public ?int $activeConversationId = null;
    public string $newMessage = '';
    public string $search = '';
    public ?int $filterSessionId = null; // null = tampilkan semua nomor

    public function selectConversation(int $conversationId): void
    {
        $this->activeConversationId = $conversationId;
        $this->newMessage = '';
    }

    public function toggleAi(): void
    {
        $conversation = $this->activeConversation();
        if (! $conversation) return;

        $conversation->update(['ai_active' => ! $conversation->ai_active]);
    }

    public function sendReply(WhatsappService $wa): void
    {
        $this->validate(['newMessage' => 'required|string|min:1']);

        $conversation = $this->activeConversation();
        if (! $conversation) return;

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'agent',
            'user_id'         => auth()->id(),
            'content'         => $this->newMessage,
        ]);

        $conversation->update(['last_message_at' => now(), 'status' => 'open']);

        // Kirim lewat sesi WA yang sama dengan yang menerima pesan dari pelanggan ini
        $session = $conversation->whatsappSession;
        if ($session && $session->isConnected()) {
            $wa->sendMessage($session->session_id, $conversation->contact->wa_number, $this->newMessage);
        }

        $this->newMessage = '';
    }

    public function resolveConversation(): void
    {
        $this->activeConversation()?->update(['status' => 'resolved']);
        $this->activeConversationId = null;
    }

    protected function activeConversation(): ?Conversation
    {
        if (! $this->activeConversationId) return null;
        return Conversation::find($this->activeConversationId);
    }

    public function render()
    {
        $sessions = WhatsappSession::orderBy('label')->get();

        $conversations = Conversation::with(['contact', 'latestMessage', 'whatsappSession'])
            ->when($this->filterSessionId, fn ($q) => $q->where('whatsapp_session_id', $this->filterSessionId))
            ->when($this->search, function ($q) {
                $q->whereHas('contact', fn ($q2) =>
                    $q2->where('name', 'like', "%{$this->search}%")
                       ->orWhere('wa_number', 'like', "%{$this->search}%")
                );
            })
            ->orderByDesc('last_message_at')
            ->get();

        $activeConversation = $this->activeConversation()
            ?->load(['messages.user', 'contact', 'whatsappSession']);

        return view('livewire.dashboard.inbox', compact(
            'conversations', 'activeConversation', 'sessions'
        ))->layout('layouts.app');
    }
}
