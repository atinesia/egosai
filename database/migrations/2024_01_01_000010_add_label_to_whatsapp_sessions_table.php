<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_sessions', function (Blueprint $table) {
            // Label bebas untuk membedakan nomor (mis. "CS Utama", "Sales", "Toko Bandung")
            $table->string('label')->default('Nomor WhatsApp')->after('session_id');
            // Bisa matikan AI per-nomor, independen dari setting global
            $table->boolean('is_ai_active')->default(true)->after('phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_sessions', function (Blueprint $table) {
            $table->dropColumn(['label', 'is_ai_active']);
        });
    }
};
