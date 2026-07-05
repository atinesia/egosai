<?php

namespace App\Services;

use App\Models\AiSetting;
use App\Models\Conversation;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    /**
     * Bangun balasan AI untuk sebuah conversation, dengan context dari
     * knowledge base tenant + riwayat percakapan terakhir.
     */
    public function generateReply(Conversation $conversation, string $incomingMessage): ?string
    {
        $tenant = $conversation->tenant;
        $aiSetting = $tenant->aiSetting ?? AiSetting::firstOrCreate(['tenant_id' => $tenant->id]);

        if (! $aiSetting->is_ai_globally_active || ! $conversation->ai_active) {
            return null;
        }

        $knowledge = KnowledgeBase::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->get()
            ->map(fn($k) => "## {$k->title}\n{$k->content}")
            ->implode("\n\n");

        // Tambahkan instruksi ketat pada system prompt di GroqService.php
        $systemPrompt = $aiSetting->system_prompt
            . "\n\nGunakan informasi referensi berikut untuk menjawab pertanyaan pelanggan. "
            . "Jika informasi tidak tersedia di referensi atau jika pelanggan meminta berbicara dengan manusia/CS/komplain, "
            . "JANGAN mengarang jawaban. Anda WAJIB menjawab dengan awalan tag [HANDOVER] diikuti dengan kalimat permohonan maaf bahwa Anda akan menyambungkan ke agen manusia.\n\n"
            . "Contoh jika tidak tahu: [HANDOVER] Mohon maaf, pertanyaan Anda akan saya sambungkan ke tim admin kami.\n\n"
            . "=== REFERENSI BISNIS ===\n" . ($knowledge ?: '(belum ada knowledge base)');

        // Ambil 10 pesan terakhir sebagai memory percakapan
        $history = $conversation->messages()
            ->latest()
            ->take(10)
            ->get()
            ->reverse()
            ->map(function ($m) {
                return [
                    'role' => $m->sender_type === 'contact' ? 'user' : 'assistant',
                    'content' => $m->content,
                ];
            })
            ->values()
            ->all();

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $history,
            [['role' => 'user', 'content' => $incomingMessage]],
        );

        // Tentukan model berdasarkan paket tenant
        $model = ($tenant->plan === 'starter')
            ? 'llama3-8b-8192'         // Model standar, hemat token, cepat
            : 'llama-3.3-70b-versatile'; // Model raksasa, ultra cerdas untuk Pro/Enterprise

        try {
            $response = Http::withToken(config('services.groq.api_key'))
                ->timeout(30)
                ->post($this->baseUrl, [
                    'model' => $model,
                    // 'model' => $aiSetting->model,
                    'messages' => $messages,
                    'temperature' => (float) $aiSetting->temperature,
                    'max_tokens' => 1024,
                ]);

            if ($response->failed()) {
                Log::error('Groq API error', ['body' => $response->body()]);
                return $aiSetting->fallback_message;
            }

            // Di dalam blok try setelah mendapatkan response sukses dari Groq:
            $replyText = $response->json('choices.0.message.content');

            if ($replyText && str_contains($replyText, '[HANDOVER]')) {
                // Hilangkan tag [HANDOVER] agar tidak terbaca oleh pelanggan di WhatsApp
                $replyText = trim(str_replace('[HANDOVER]', '', $replyText));

                // Matikan AI pada percakapan ini secara otomatis!
                $conversation->update([
                    'ai_active' => false,
                    'status' => 'open' // Pastikan masuk antrean prioritas agen
                ]);
            }
            return $replyText;
        } catch (\Throwable $e) {
            Log::error('Groq request exception: ' . $e->getMessage());
            return $aiSetting->fallback_message;
        }
    }
}
