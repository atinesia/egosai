<?php

namespace App\Livewire\Dashboard;

use App\Models\Broadcast;
use App\Models\BroadcastLog;
use App\Models\Contact;
use App\Jobs\ProcessBroadcastJob;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class BroadcastManager extends Component
{
    use WithPagination;

    public $broadcasts;
    public  $devices; // Untuk menampung daftar nomor WA
    public string $name, $message;
    public $targetType = 'all'; // Pilihan pengiriman (contoh: all)
    public $selectedSessionId = null; // Menampung ID device pilihan
    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|string|max:100',
        'message' => 'required|string|max:5000',
        'targetType' => 'required|in:all,device',
    ];

    public function render()
    {
        $tenant = Auth::user()->tenant;
        // Mengambil histori broadcast milik tenant
        $this->broadcasts = Broadcast::with('logs')->latest()->get();
        // Ambil semua nomor WA milik tenant untuk pilihan di modal
        $this->devices = $tenant->whatsappSessions()->get();

        return view('livewire.dashboard.broadcast-manager')
            ->layout('layouts.app');
    }

    public function openModal()
    {
        $this->name = '';
        $this->message = '';
        $this->targetType = 'all';
        $this->selectedSessionId = null;
        $this->isModalOpen = true;
    }

    public function sendBroadcast()
    {
        // Validasi kondisional: Jika targetnya device, maka pilihan device wajib diisi
        $this->validate(array_merge($this->rules, [
            'selectedSessionId' => $this->targetType === 'device' ? 'required|exists:whatsapp_sessions,id' : 'nullable'
        ]), [
            'selectedSessionId.required' => 'Silakan pilih nomor WhatsApp pengirim.'
        ]);

        $tenant = Auth::user()->tenant;

        // Jika paket starter nekat menembak payload 'device', paksa kembali ke 'all'
        if ($tenant->plan === 'starter' && $this->targetType === 'device') {
            $this->targetType = 'all';
        }


        $tenantId = Auth::user()->tenant_id;

        // 1. Ambil target kontak sesuai kriteria filter
        $contacts = Contact::where('tenant_id', $tenantId)->get();

        if ($contacts->isEmpty()) {
            session()->flash('error', 'Gagal membuat broadcast. Anda belum memiliki daftar kontak pelanggan.');
            $this->isModalOpen = false;
            return;
        }

        // 2. Buat data Induk Kampanye
        $broadcast = Broadcast::create([
            'tenant_id' => $tenantId,
            'whatsapp_session_id' => $this->targetType === 'device' ? $this->selectedSessionId : null,
            'name' => $this->name,
            'message' => $this->message,
            'target_type' => $this->targetType,
            'status' => 'pending',
            'total_contacts' => $contacts->count(),
        ]);

        // 3. Buat data detail log penerima berstatus 'pending'
        foreach ($contacts as $contact) {
            BroadcastLog::create([
                'tenant_id' => $tenantId,
                'broadcast_id' => $broadcast->id,
                'contact_id' => $contact->id,
                'status' => 'pending',
            ]);
        }

        // 4. Lempar ke background job antrean Laravel Queue
        ProcessBroadcastJob::dispatch($broadcast->id);

        session()->flash('success', 'Kampanye blast berhasil masuk antrean sistem background!');
        $this->isModalOpen = false;
    }
}
