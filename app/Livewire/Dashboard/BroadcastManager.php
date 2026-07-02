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
    public string $name, $message;
    public $targetType = 'all'; // Pilihan pengiriman (contoh: all)
    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|string|max:100',
        'message' => 'required|string|max:5000',
    ];

    public function render()
    {
        // Mengambil histori broadcast milik tenant
        $this->broadcasts = Broadcast::latest()->get();

        return view('livewire.dashboard.broadcast-manager')
            ->layout('layouts.app');
    }

    public function openModal()
    {
        $this->name = '';
        $this->message = '';
        $this->isModalOpen = true;
    }

    public function sendBroadcast()
    {
        $this->validate();
        $tenantId = Auth::user()->tenant_id;

        // 1. Ambil target kontak sesuai kriteria filter
        $contacts = Contact::get(); // Sementara mengambil semua kontak milik tenant

        if ($contacts->isEmpty()) {
            session()->flash('error', 'Gagal membuat broadcast. Anda belum memiliki daftar kontak pelanggan.');
            $this->isModalOpen = false;
            return;
        }

        // 2. Buat data Induk Kampanye
        $broadcast = Broadcast::create([
            'tenant_id' => $tenantId,
            'name' => $this->name,
            'message' => $this->message,
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
