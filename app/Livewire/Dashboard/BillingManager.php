<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use App\Services\TripayService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillingManager extends Component
{
    public $orders;
    public $selectedPlan = null;
    public $selectedMethod = null;
    public $isCheckoutModalOpen = false;

    // Definisikan opsi channel pembayaran Tripay yang ingin Anda aktifkan (sesuai sandbox/production)
    public $paymentChannels = [
        ['code' => 'QRIS', 'name' => 'QRIS (GoPay, OVO, Dana, LinkAja)', 'group' => 'E-Wallet'],
        ['code' => 'BCAVA', 'name' => 'BCA Virtual Account', 'group' => 'Virtual Account'],
        ['code' => 'BRIVA', 'name' => 'BRI Virtual Account', 'group' => 'Virtual Account'],
        ['code' => 'MANDIRIVA', 'name' => 'Mandiri Virtual Account', 'group' => 'Virtual Account'],
        ['code' => 'BNIVA', 'name' => 'BNI Virtual Account', 'group' => 'Virtual Account'],
    ];

    // Definisikan data pricing paket SaaS Anda
    public $plans = [
        'starter' => ['name' => 'Starter', 'price' => 150000, 'whatsapp_limit' => 1],
        'pro' => ['name' => 'Pro', 'price' => 300000, 'whatsapp_limit' => 3],
        'enterprise' => ['name' => 'Enterprise', 'price' => 750000, 'whatsapp_limit' => 5],
    ];

    public function render()
    {
        // Hanya Owner yang boleh mengakses halaman ini
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman manajemen ini.');
        }
        // Mengambil riwayat tagihan/order milik tenant aktif
        $this->orders = Order::latest()->get();

        return view('livewire.dashboard.billing-manager')
            ->layout('layouts.app');
    }

    public function selectPlan(string $planKey)
    {
        $this->selectedPlan = $planKey;
        $this->isCheckoutModalOpen = true;
    }

    public function processCheckout(TripayService $tripay)
    {
        if (!$this->selectedPlan || !$this->selectedMethod) {
            session()->flash('error', 'Silakan pilih metode pembayaran terlebih dahulu.');
            return;
        }

        $user = Auth::user();
        $tenant = $user->tenant;
        $planData = $this->plans[$this->selectedPlan];
        $merchantRef = 'INV-' . time() . '-' . Str::upper(Str::random(4));

        // 1. Kirim request transaksi ke API Tripay
        $response = $tripay->requestTransaction([
            'method' => $this->selectedMethod,
            'merchant_ref' => $merchantRef,
            'amount' => $planData['price'],
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'plan' => $this->selectedPlan,
        ]);

        if (!$response['ok']) {
            session()->flash('error', $response['message']);
            return;
        }

        $tripayData = $response['data'];

        // 2. Simpan invoice ke dalam tabel orders lokal kita
        Order::create([
            'tenant_id' => $tenant->id,
            'reference' => $tripayData['reference'],
            'merchant_ref' => $merchantRef,
            'plan' => $this->selectedPlan,
            'amount' => $planData['price'],
            'payment_method' => $this->selectedMethod,
            'status' => 'unpaid',
            'checkout_url' => $tripayData['checkout_url'] ?? null,
            'qr_url' => $tripayData['qr_url'] ?? null, // Khusus QRIS jika disupport langsung
        ]);

        session()->flash('success', 'Invoice berhasil dibuat! Silakan lakukan pembayaran.');
        $this->isCheckoutModalOpen = false;

        // Opsional: Jika Tripay mengembalikan checkout_url, Anda bisa redirect user langsung ke sana
        if (!empty($tripayData['checkout_url'])) {
            return redirect()->away($tripayData['checkout_url']);
        }
    }
}
