<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Nomor WhatsApp mana yang dipakai untuk conversation ini.
            // nullable supaya data conversation lama (sebelum multi-nomor) tidak rusak.
            $table->foreignId('whatsapp_session_id')
                ->nullable()
                ->after('contact_id')
                ->constrained('whatsapp_sessions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('whatsapp_session_id');
        });
    }
};
