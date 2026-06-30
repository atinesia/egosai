<?php

// Tambahkan array berikut ke dalam config/services.php milik project Laravel kamu
// (di dalam array `return [ ... ]` yang sudah ada, sejajar dengan 'mailgun', 'postmark', dll)

return [

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
    ],

    'whatsapp_node' => [
        'url' => env('WHATSAPP_NODE_URL', 'http://localhost:3001'),
        'secret' => env('WHATSAPP_WEBHOOK_SECRET'),
    ],

];
