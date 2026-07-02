<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripayCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        $callbackSignature = $request->header('X-Callback-Signature');
        $json = $request->getContent();

        // Ambil private key dari konfigurasi
        $privateKey = config('services.tripay.private_key');

        // Validasi keaslian data menggunakan Signature Tripay resmi
        $signature = hash_hmac('sha256', $json, $privateKey);

        if ($signature !== $callbackSignature) {
            return response()->json(['ok' => false, 'message' => 'Invalid Signature'], 401);
        }

        $data = json_decode($json, true);
        $merchantRef = $data['merchant_ref'];
        $status = $data['status']; // PAID, EXPIRED, FAILED

        // Cari order berdasarkan invoice merchant_ref
        $order = Order::where('merchant_ref', $merchantRef)->first();

        if (!$order) {
            return response()->json(['ok' => false, 'message' => 'Order Not Found'], 404);
        }

        // Jika order sudah dibayar sebelumnya, hentikan proses agar tidak dobel ekseskusi
        if ($order->status === 'paid') {
            return response()->json(['ok' => true]);
        }

        if ($status === 'PAID') {
            // 1. Update status order menjadi paid
            $order->update(['status' => 'paid']);

            // 2. Berikan update kuota plan dan durasi aktif 30 hari ke Tenant bersangkutan
            $tenant = Tenant::find($order->tenant_id);
            if ($tenant) {
                $tenant->update([
                    'plan' => $order->plan,
                    'expired_at' => now()->addDays(30) // Masa aktif bertambah 30 hari
                ]);
            }
        } elseif ($status === 'EXPIRED') {
            $order->update(['status' => 'expired']);
        } elseif ($status === 'FAILED') {
            $order->update(['status' => 'failed']);
        }

        return response()->json(['ok' => true]);
    }
}
