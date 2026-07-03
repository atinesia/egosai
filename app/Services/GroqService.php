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

        $systemPrompt = $aiSetting->system_prompt
            . "\n\nGunakan informasi referensi berikut untuk menjawab pertanyaan pelanggan. "
            . "Jika informasi tidak tersedia di referensi, katakan dengan jujur bahwa kamu akan "
            . "menyambungkan ke tim manusia, jangan mengarang jawaban.\n\n"
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

            return $response->json('choices.0.message.content');
        } catch (\Throwable $e) {
            Log::error('Groq request exception: ' . $e->getMessage());
            return $aiSetting->fallback_message;
        }
    }
}
