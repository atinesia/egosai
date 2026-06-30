<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $baseUrl;
    protected string $secret;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.whatsapp_node.url'), '/');
        $this->secret = (string) config('services.whatsapp_node.secret');
    }

    /**
     * Minta Node service membuat/memulai sesi WhatsApp baru untuk tenant.
     * Node akan generate QR dan mengirim hasilnya via webhook qr-update.
     *
     * Selalu mengembalikan array ['ok' => bool, 'message' => string, ...data],
     * tidak pernah melempar exception ke pemanggil — supaya kalau Node service
     * down/unreachable, Livewire tetap bisa kasih feedback ke user alih-alih
     * request gagal total dan UI diam-diam balik ke state semula.
     */
    public function startSession(string $sessionId): array
    {
        return $this->call('post', "/sessions/{$sessionId}/start");
    }

    public function logoutSession(string $sessionId): array
    {
        return $this->call('post', "/sessions/{$sessionId}/logout");
    }

    /**
     * Kirim pesan teks ke nomor WA tertentu melalui sesi tenant.
     */
    public function sendMessage(string $sessionId, string $waNumber, string $text): bool
    {
        $result = $this->call('post', "/sessions/{$sessionId}/send", [
            'to' => $waNumber,
            'text' => $text,
        ]);

        return $result['ok'];
    }

    protected function call(string $method, string $path, array $payload = []): array
    {
        if (blank($this->baseUrl)) {
            Log::error('WHATSAPP_NODE_URL belum diatur di .env');
            return ['ok' => false, 'message' => 'URL service WhatsApp belum dikonfigurasi (cek WHATSAPP_NODE_URL di .env).'];
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(15)
                ->connectTimeout(5)
                ->{$method}("{$this->baseUrl}{$path}", $payload);

            if ($response->failed()) {
                $message = $response->status() === 401
                    ? 'Secret tidak cocok antara Laravel dan Node service (cek WHATSAPP_WEBHOOK_SECRET vs WEBHOOK_SECRET).'
                    : "Node service merespons error (HTTP {$response->status()}).";

                Log::error('WhatsappService call gagal', [
                    'path' => $path,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return ['ok' => false, 'message' => $message];
            }

            return array_merge(['ok' => true], $response->json() ?? []);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Tidak bisa terhubung ke Node WhatsApp service di {$this->baseUrl}: " . $e->getMessage());
            return [
                'ok' => false,
                'message' => "Tidak bisa terhubung ke service WhatsApp di {$this->baseUrl}. Pastikan Node.js service sudah dijalankan (`npm start` di folder node-whatsapp-service).",
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsappService exception: ' . $e->getMessage());
            return ['ok' => false, 'message' => 'Terjadi kesalahan tak terduga saat menghubungi service WhatsApp.'];
        }
    }

    protected function headers(): array
    {
        return [
            'X-Webhook-Secret' => $this->secret,
            'Accept' => 'application/json',
        ];
    }
}
