<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TripayService
{
    protected string $apiKey, $privateKey, $merchantCode, $baseUrl;

    public function __construct()
    {
        $config = config('services.tripay');
        $this->apiKey = $config['api_key'];
        $this->privateKey = $config['private_key'];
        $this->merchantCode = $config['merchant_code'];

        $this->baseUrl = $config['mode'] === 'production'
            ? 'https://tripay.co.id/api/'
            : 'https://tripay.co.id/api-sandbox/';
    }

    /**
     * Membuat Transaksi Closed Payment di Tripay
     */
    public function requestTransaction(array $params)
    {
        $expiry = time() + (24 * 60 * 60); // Waktu kedaluwarsa invoice: 24 Jam

        // Aturan pembuatan signature resmi dari Tripay
        $signature = hash_hmac('sha256', $this->merchantCode . $params['merchant_ref'] . $params['amount'], $this->privateKey);

        $payload = [
            'method'         => $params['method'],
            'merchant_ref'   => $params['merchant_ref'],
            'amount'         => $params['amount'],
            'customer_name'  => $params['customer_name'],
            'customer_email' => $params['customer_email'],
            'order_items'    => [
                [
                    'name'     => 'Paket Langganan ' . strtoupper($params['plan']),
                    'price'    => $params['amount'],
                    'quantity' => 1,
                ]
            ],
            'expired_time'   => $expiry,
            'signature'      => $signature
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->post($this->baseUrl . 'transaction/create', $payload);

        if ($response->successful()) {
            return [
                'ok' => true,
                'data' => $response->json()['data']
            ];
        }

        return [
            'ok' => false,
            'message' => $response->json()['message'] ?? 'Gagal terhubung ke Tripay'
        ];
    }
}
