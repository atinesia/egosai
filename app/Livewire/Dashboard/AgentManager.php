<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AgentManager extends Component
{
    public $agents;
    public string $name, $email, $password;
    public $selectedAgentId;
    public $isModalOpen = false;
    public $isEditMode = false;

    public function render()
    {
        // Hanya Owner yang boleh mengakses halaman ini
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman manajemen ini.');
        }
        // Owner hanya bisa melihat agen/karyawan yang berada di dalam satu tenant yang sama
        $this->agents = User::where('tenant_id', Auth::user()->tenant_id)
            ->where('role', 'agent')
            ->latest()
            ->get();

        return view('livewire.dashboard.agent-manager')
            ->layout('layouts.app');
    }

    public function openModal()
    {
        // --- BACKEND FITUR GUARD (BATASAN AGEN BERDASARKAN PLAN) ---
        $tenant = Auth::user()->tenant;

        $limit = match ($tenant->plan) {
            'starter' => 1, // Hanya Owner, tidak boleh tambah agen
            'pro' => 5,     // Total maksimal 5 user (Owner + 4 Agen)
            'enterprise' => 999, // Tanpa batas
            default => 1,
        };

        $currentTotalUsers = User::where('tenant_id', $tenant->id)->count();

        if ($currentTotalUsers >= $limit) {
            $msg = $tenant->plan === 'starter' || $tenant->plan === 'trial'
                ? "Paket STARTER tidak mendukung penambahan agen CS. Silakan upgrade ke paket Pro!"
                : "Batas maksimal pengguna tim untuk paket " . strtoupper($tenant->plan) . " ({$limit} user) telah tercapai.";

            session()->flash('error', $msg);
            return;
        }

        $this->resetFields();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function resetFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->selectedAgentId = null;
    }

    public function storeAgent()
    {
        // Validasi input
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', Password::defaults()],
        ]);

        // Double check di backend saat proses submit untuk keamanan ekstra
        $tenant = Auth::user()->tenant;
        $limit = match ($tenant->plan) {
            'starter' => 1,
            'pro' => 5,
            'enterprise' => 999,
            default => 1
        };
        if (User::where('tenant_id', $tenant->id)->count() >= $limit) {
            session()->flash('error', 'Gagal menyimpan. Batas kuota paket telah terlewati.');
            $this->isModalOpen = false;
            return;
        }

        User::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => 'agent', // Dikunci sebagai karyawan CS
            'password' => Hash::make($this->password),
        ]);

        session()->flash('success', 'Karyawan Agen CS baru berhasil didaftarkan!');
        $this->isModalOpen = false;
    }

    public function deleteAgent(int $id)
    {
        $agent = User::where('tenant_id', Auth::user()->tenant_id)
            ->where('id', $id)
            ->where('role', 'agent')
            ->first();

        if ($agent) {
            $agent->delete();
            session()->flash('success', 'Akses agen CS berhasil dicabut.');
        }
    }
}
