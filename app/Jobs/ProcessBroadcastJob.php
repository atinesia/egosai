<?php

namespace App\Jobs;

use App\Models\Broadcast;
use App\Models\BroadcastLog;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected mixed $broadcastId;

    public function __construct(mixed $broadcastId)
    {
        $this->broadcastId = $broadcastId;
    }

    public function handle(WhatsappService $waService)
    {
        $broadcast = Broadcast::find($this->broadcastId);
        if (!$broadcast || $broadcast->status !== 'pending') {
            return;
        }

        $broadcast->update(['status' => 'processing']);

        // 1. Ambil nomor WhatsApp yang sedang aktif/connected milik tenant ini
        $activeSessions = $broadcast->tenant->whatsappSessions()
            ->where('status', 'connected')
            ->get();

        if ($activeSessions->isEmpty()) {
            $broadcast->update(['status' => 'failed']);
            Log::error("Broadcast Gagal: Tidak ada sesi WhatsApp yang aktif terhubung untuk tenant ID: " . $broadcast->tenant_id);
            return;
        }

        // 2. Ambil semua log kontak tujuan yang masih berstatus 'pending'
        $pendingLogs = BroadcastLog::withoutGlobalScopes()
            ->where('broadcast_id', $broadcast->id)
            ->where('status', 'pending')
            ->with('contact')
            ->get();

        $sessionCount = $activeSessions->count();
        $rotatedIndex = 0;

        foreach ($pendingLogs as $log) {
            // REFRESH CHECK: Cek jika status broadcast diubah manual/dihentikan di tengah jalan
            $currentStatus = Broadcast::where('id', $this->broadcastId)->value('status');
            if ($currentStatus !== 'processing') {
                break;
            }

            // --- SMART ROTATOR (LOAD BALANCING) ---
            // Bergantian mengambil sesi WhatsApp berdasarkan index perputaran
            $currentSession = $activeSessions[$rotatedIndex % $sessionCount];
            $rotatedIndex++;

            try {
                // Kirim pesan lewat WhatsappService memanfaatkan session_id terpilih
                $result = $waService->sendMessage(
                    $currentSession->session_id,
                    $log->contact->phone_number ?? $log->contact->wa_number,
                    $broadcast->message
                );

                if ($result) {
                    $log->update([
                        'status' => 'sent',
                        'whatsapp_session_id' => $currentSession->id
                    ]);
                    $broadcast->increment('sent_count');
                } else {
                    $log->update([
                        'status' => 'failed',
                        'whatsapp_session_id' => $currentSession->id,
                        'error_message' => $result['message'] ?? 'Gagal mengirim lewat Node service'
                    ]);
                    $broadcast->increment('failed_count');
                }
            } catch (\Exception $e) {
                $log->update([
                    'status' => 'failed',
                    'whatsapp_session_id' => $currentSession->id,
                    'error_message' => $e->getMessage()
                ]);
                $broadcast->increment('failed_count');
            }

            // --- HUMAN-LIKE RANDOM DELAY ---
            // Memberikan jeda waktu acak antara 5 sampai 12 detik antar pengiriman pesan
            sleep(rand(5, 12));
        }

        // 3. Tandai kampanye selesai jika semua log sudah diproses
        $broadcast->refresh();
        $remaining = BroadcastLog::where('broadcast_id', $broadcast->id)->where('status', 'pending')->count();

        if ($remaining === 0) {
            $broadcast->update(['status' => 'completed']);
        }
    }
}
