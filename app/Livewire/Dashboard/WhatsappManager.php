<?php

namespace App\Livewire\Dashboard;

use App\Models\WhatsappSession;
use App\Services\WhatsappService;
use Illuminate\Support\Str;
use Livewire\Component;

class WhatsappManager extends Component
{
    // Form tambah nomor baru
    public bool $showAddForm = false;
    public string $newLabel = '';

    // Nomor mana yang sedang tampil QR-nya
    public ?int $showingQrFor = null;

    // Pesan error / sukses per aksi
    public ?string $errorMessage = null;
    public ?string $successMessage = null;

    public function addNumber(WhatsappService $wa): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $this->validate(['newLabel' => 'required|string|max:60']);

        $tenant = auth()->user()->tenant;

        if (! $tenant->canAddWhatsappNumber()) {
            $max = $tenant->maxWhatsappNumbers();
            $this->errorMessage = "Paket {$tenant->plan} hanya mendukung {$max} nomor WhatsApp. Upgrade paket untuk menambah lebih banyak nomor.";
            return;
        }

        $session = WhatsappSession::create([
            'session_id' => 'tenant-' . $tenant->id . '-' . Str::random(8),
            'label'      => $this->newLabel,
            'status'     => 'qr_pending',
        ]);

        $result = $wa->startSession($session->session_id);

        if (! $result['ok']) {
            $session->delete();
            $this->errorMessage = $result['message'];
            return;
        }

        $this->showingQrFor = $session->id;
        $this->showAddForm = false;
        $this->newLabel = '';
    }

    public function connectExisting(int $sessionId, WhatsappService $wa): void
    {
        $this->errorMessage = null;

        $session = WhatsappSession::findOrFail($sessionId);
        $session->update(['status' => 'qr_pending', 'qr_code' => null]);

        $result = $wa->startSession($session->session_id);

        if (! $result['ok']) {
            $session->update(['status' => 'disconnected']);
            $this->errorMessage = $result['message'];
            return;
        }

        $this->showingQrFor = $session->id;
    }

    public function disconnect(int $sessionId, WhatsappService $wa): void
    {
        $this->errorMessage = null;

        $session = WhatsappSession::findOrFail($sessionId);
        $result = $wa->logoutSession($session->session_id);

        if (! $result['ok']) {
            // Tetap putuskan lokal meskipun Node gagal merespons
            $this->errorMessage = $result['message'];
        }

        $session->update(['status' => 'disconnected', 'qr_code' => null, 'phone_number' => null]);

        if ($this->showingQrFor === $sessionId) {
            $this->showingQrFor = null;
        }
    }

    public function delete(int $sessionId, WhatsappService $wa): void
    {
        $session = WhatsappSession::findOrFail($sessionId);

        // Pastikan logout dulu dari Node kalau masih terhubung
        if (! $session->isConnected() === false) {
            $wa->logoutSession($session->session_id);
        }

        $session->delete();

        if ($this->showingQrFor === $sessionId) {
            $this->showingQrFor = null;
        }
    }

    public function updateLabel(int $sessionId, string $label): void
    {
        $session = WhatsappSession::findOrFail($sessionId);
        $session->update(['label' => $label]);
    }

    public function toggleAi(int $sessionId): void
    {
        $session = WhatsappSession::findOrFail($sessionId);
        $session->update(['is_ai_active' => ! $session->is_ai_active]);
    }

    public function refreshQr(int $sessionId): void
    {
        // Dipanggil oleh wire:poll hanya kalau QR sedang ditampilkan
        $session = WhatsappSession::find($sessionId);

        if (! $session) {
            $this->showingQrFor = null;
            return;
        }

        // Kalau sudah connected setelah scan, tutup panel QR
        if ($session->isConnected()) {
            $this->showingQrFor = null;
            $this->successMessage = "Nomor \"{$session->label}\" ({$session->phone_number}) berhasil terhubung!";
        }
    }

    public function render()
    {
        $tenant = auth()->user()->tenant;
        $sessions = WhatsappSession::orderBy('created_at')->get();

        $max = $tenant->maxWhatsappNumbers();
        $used = $sessions->count();
        $canAdd = $tenant->canAddWhatsappNumber();

        $qrSession = $this->showingQrFor
            ? WhatsappSession::find($this->showingQrFor)
            : null;

        return view('livewire.dashboard.whatsapp-manager', compact(
            'sessions', 'tenant', 'max', 'used', 'canAdd', 'qrSession'
        ))->layout('layouts.app');
    }
}
