<?php

namespace App\Livewire\Dashboard;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\WhatsappService;
use Livewire\Attributes\On;
use Livewire\Component;

class Inbox extends Component
{
    public ?int $activeConversationId = null;
    public string $newMessage = '';
    public string $search = '';

    public function selectConversation(int $conversationId): void
    {
        $this->activeConversationId = $conversationId;
    }

    public function toggleAi(): void
    {
        $conversation = $this->activeConversation();
        if (! $conversation) {
            return;
        }

        $conversation->update(['ai_active' => ! $conversation->ai_active]);
    }

    public function sendReply(WhatsappService $wa): void
    {
        $this->validate(['newMessage' => 'required|string|min:1']);

        $conversation = $this->activeConversation();
        if (! $conversation) {
            return;
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'user_id' => auth()->id(),
            'content' => $this->newMessage,
        ]);

        $conversation->update(['last_message_at' => now(), 'status' => 'open']);

        $session = $conversation->tenant->whatsappSession;
        if ($session) {
            $wa->sendMessage($session->session_id, $conversation->contact->wa_number, $this->newMessage);
        }

        $this->newMessage = '';
    }

    public function resolveConversation(): void
    {
        $conversation = $this->activeConversation();
        $conversation?->update(['status' => 'resolved']);
    }

    protected function activeConversation(): ?Conversation
    {
        if (! $this->activeConversationId) {
            return null;
        }

        return Conversation::find($this->activeConversationId);
    }

    public function render()
    {
        $conversations = Conversation::with(['contact', 'latestMessage'])
            ->when($this->search, function ($q) {
                $q->whereHas('contact', function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                        ->orWhere('wa_number', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('last_message_at')
            ->get();

        $activeConversation = $this->activeConversation()?->load(['messages.user', 'contact']);

        return view('livewire.dashboard.inbox', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
        ])->layout('layouts.app');
    }
}
