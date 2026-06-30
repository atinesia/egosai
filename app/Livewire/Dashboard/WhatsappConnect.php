<?php

namespace App\Livewire\Dashboard;

use App\Models\WhatsappSession;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class WhatsappConnect extends Component
{
    public ?WhatsappSession $session = null;
    public ?string $errorMessage = null;

    // public function mount(): void
    // {
    //     $this->session = Auth::user()->tenant->whatsappSession;
    // }

    // public function connect(WhatsappService $wa): void
    // {
    //     $this->errorMessage = null;

    //     if (! $this->session) {
    //         $this->session = WhatsappSession::create([
    //             'session_id' => 'tenant-' . Auth::user()->tenant_id . '-' . Str::random(6),
    //             'status' => 'qr_pending',
    //         ]);
    //     } else {
    //         $this->session->update(['status' => 'qr_pending', 'qr_code' => null]);
    //     }

    //     $result = $wa->startSession($this->session->session_id);
    //     Log::info('WhatsappConnect: startSession result', ['qrcode' => $this->session->qrcode]);
    //     if (! $result['ok']) {
    //         // Gagal menghubungi Node service — jangan biarkan UI nyangkut di
    //         // status "menunggu QR" selamanya, kembalikan ke disconnected
    //         // dan tampilkan alasan yang jelas ke user.
    //         $this->session->update(['status' => 'disconnected']);
    //         $this->errorMessage = $result['message'];
    //         return;
    //     }

    //     $this->session->refresh();
    // }

    // public function disconnect(WhatsappService $wa): void
    // {
    //     $this->errorMessage = null;

    //     if (! $this->session) {
    //         return;
    //     }

    //     $result = $wa->logoutSession($this->session->session_id);

    //     if (! $result['ok']) {
    //         $this->errorMessage = $result['message'];
    //         // Tetap putuskan secara lokal di Laravel meski Node gagal merespons,
    //         // supaya user tidak macet kalau Node service sedang down.
    //     }

    //     $this->session->update(['status' => 'disconnected', 'qr_code' => null, 'phone_number' => null]);
    // }

    // Mendapatkan limit WhatsApp berdasarkan plan tenant
    public function getQuotaLimitProperty(): int
    {
        $plan = Auth::user()->tenant->plan ?? 'trial';

        return match ($plan) {
            'pro' => 3,
            'enterprise' => 5,
            default => 1, // trial atau starter
        };
    }

    // Mendapatkan semua sesi aktif/terdaftar untuk tenant ini
    public function getSessionsProperty()
    {
        return Auth::user()->tenant->whatsappSessions;
    }

    /**
     * Membuat sesi baru atau mereset sesi yang gagal/terputus
     */
    public function connect(WhatsappService $wa, ?int $sessionIdToRetry = null): void
    {
        $this->errorMessage = null;
        $tenant = Auth::user()->tenant;

        if ($sessionIdToRetry) {
            // Jika mereset/mencoba ulang sesi yang sudah ada
            $session = WhatsappSession::where('tenant_id', $tenant->id)->find($sessionIdToRetry);
            if ($session) {
                $session->update(['status' => 'qr_pending', 'qr_code' => null]);
            }
        } else {
            // Validasi limit kuota sebelum membuat sesi baru
            if ($tenant->whatsappSessions()->count() >= $this->quotaLimit) {
                $this->errorMessage = "Batas maksimum akun WhatsApp untuk paket " . strtoupper($tenant->plan) . " telah tercapai ({$this->quotaLimit} akun).";
                return;
            }

            // Buat record sesi baru
            $session = WhatsappSession::create([
                'tenant_id' => $tenant->id,
                'session_id' => 'tenant-' . $tenant->id . '-' . Str::random(6),
                'status' => 'qr_pending',
            ]);
        }

        if (!$session) {
            return;
        }

        $result = $wa->startSession($session->session_id);

        if (!$result['ok']) {
            $session->update(['status' => 'disconnected']);
            $this->errorMessage = $result['message'];
            return;
        }
    }

    /**
     * Memutuskan koneksi spesifik berdasarkan ID sesi
     */
    public function disconnect(WhatsappService $wa, int $id): void
    {
        $this->errorMessage = null;
        $session = WhatsappSession::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (!$session) {
            return;
        }

        $result = $wa->logoutSession($session->session_id);

        if (!$result['ok']) {
            $this->errorMessage = $result['message'];
        }

        // Tetap hapus/putuskan status di database lokal
        $session->update([
            'status' => 'disconnected',
            'qr_code' => null,
            'phone_number' => null
        ]);
    }

    /**
     * Menghapus sesi sepenuhnya dari database jika status disconnected
     */
    public function deleteSession(int $id): void
    {
        $session = WhatsappSession::where('tenant_id', Auth::user()->tenant_id)->find($id);
        if ($session && $session->status === 'disconnected') {
            $session->delete();
        }
    }

    // Dipanggil otomatis oleh wire:poll di view untuk refresh status/QR
    public function refreshStatus(): void
    {
        $this->session?->refresh();
    }

    public function render()
    {
        return view('livewire.dashboard.whatsapp-connect', [
            'sessions' => $this->sessions,
            'quotaLimit' => $this->quotaLimit
        ])->layout('layouts.app');
    }
}
