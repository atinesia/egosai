<?php

use App\Http\Controllers\Api\TripayCallbackController;
use App\Http\Controllers\Api\WhatsappWebhookController;
use Illuminate\Support\Facades\Route;

// Dipanggil oleh Node.js WhatsApp service (diamankan via header X-Webhook-Secret)
Route::post('/webhooks/whatsapp/status', [WhatsappWebhookController::class, 'statusUpdate']);
Route::post('/webhooks/whatsapp/message', [WhatsappWebhookController::class, 'incomingMessage']);
Route::post('/tripay/callback', [TripayCallbackController::class, 'handleCallback']);
