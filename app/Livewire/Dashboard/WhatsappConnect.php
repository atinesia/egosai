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

    public function mount(): void
    {
        $this->session = Auth::user()->tenant->whatsappSession;
    }

    public function connect(WhatsappService $wa): void
    {
        $this->errorMessage = null;

        if (! $this->session) {
            $this->session = WhatsappSession::create([
                'session_id' => 'tenant-' . Auth::user()->tenant_id . '-' . Str::random(6),
                'status' => 'qr_pending',
            ]);
        } else {
            $this->session->update(['status' => 'qr_pending', 'qr_code' => null]);
        }

        $result = $wa->startSession($this->session->session_id);
        Log::info('WhatsappConnect: startSession result', ['qrcode' => $this->session->qrcode]);
        if (! $result['ok']) {
            // Gagal menghubungi Node service — jangan biarkan UI nyangkut di
            // status "menunggu QR" selamanya, kembalikan ke disconnected
            // dan tampilkan alasan yang jelas ke user.
            $this->session->update(['status' => 'disconnected']);
            $this->errorMessage = $result['message'];
            return;
        }

        $this->session->refresh();
    }

    public function disconnect(WhatsappService $wa): void
    {
        $this->errorMessage = null;

        if (! $this->session) {
            return;
        }

        $result = $wa->logoutSession($this->session->session_id);

        if (! $result['ok']) {
            $this->errorMessage = $result['message'];
            // Tetap putuskan secara lokal di Laravel meski Node gagal merespons,
            // supaya user tidak macet kalau Node service sedang down.
        }

        $this->session->update(['status' => 'disconnected', 'qr_code' => null, 'phone_number' => null]);
    }

    // Dipanggil otomatis oleh wire:poll di view untuk refresh status/QR
    public function refreshStatus(): void
    {
        $this->session?->refresh();
    }

    public function render()
    {
        return view('livewire.dashboard.whatsapp-connect')->layout('layouts.app');
    }
}
