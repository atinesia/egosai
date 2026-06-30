<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappSession;
use App\Services\GroqService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsappWebhookController extends Controller
{
    public function __construct(
        protected GroqService $groq,
        protected WhatsappService $wa,
    ) {
    }

    /**
     * Dipanggil Node service tiap kali ada perubahan status QR / koneksi sesi.
     * Body: { session_id, status, qr_code?, phone_number? }
     */
    public function statusUpdate(Request $request)
    {
        $this->verifySecret($request);

        $data = $request->validate([
            'session_id' => 'required|string',
            'status' => 'required|string',
            'qr_code' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        $session = WhatsappSession::withoutGlobalScopes()
            ->where('session_id', $data['session_id'])
            ->first();

        if (! $session) {
            return response()->json(['message' => 'session not found'], 404);
        }

        $session->update([
            'status' => $data['status'],
            'qr_code' => $data['qr_code'] ?? $session->qr_code,
            'phone_number' => $data['phone_number'] ?? $session->phone_number,
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Dipanggil Node service tiap kali ada pesan masuk dari pelanggan.
     * Body: { session_id, from, name?, text, wa_message_id? }
     */
    public function incomingMessage(Request $request)
    {
        $this->verifySecret($request);

        $data = $request->validate([
            'session_id' => 'required|string',
            'from' => 'required|string',
            'name' => 'nullable|string',
            'text' => 'required|string',
            'wa_message_id' => 'nullable|string',
        ]);

        $session = WhatsappSession::withoutGlobalScopes()
            ->where('session_id', $data['session_id'])
            ->first();

        if (! $session) {
            return response()->json(['message' => 'session not found'], 404);
        }

        $tenant = $session->tenant;

        $contact = Contact::withoutGlobalScopes()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'wa_number' => $data['from']],
            ['name' => $data['name'] ?? $data['from']]
        );

        $conversation = Conversation::withoutGlobalScopes()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'contact_id' => $contact->id, 'status' => 'open'],
            ['channel' => 'whatsapp', 'ai_active' => true]
        );
        $conversation->update(['last_message_at' => now()]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'contact',
            'content' => $data['text'],
            'wa_message_id' => $data['wa_message_id'] ?? null,
        ]);

        // Minta balasan AI (akan null kalau AI nonaktif utk conversation/tenant ini)
        $reply = $this->groq->generateReply($conversation, $data['text']);

        if ($reply) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'ai',
                'content' => $reply,
            ]);

            $this->wa->sendMessage($session->session_id, $contact->wa_number, $reply);
        }

        return response()->json(['ok' => true]);
    }

    protected function verifySecret(Request $request): void
    {
        if ($request->header('X-Webhook-Secret') !== config('services.whatsapp_node.secret')) {
            abort(401, 'Invalid webhook secret');
        }
    }
}
