<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            // Jika NULL, artinya broadcast ke "Semua Device" (Gunakan Smart Rotator)
            // Jika terisi, artinya broadcast dikunci hanya lewat nomor tersebut
            $table->foreignId('whatsapp_session_id')->nullable()->after('tenant_id')->constrained()->onDelete('set null');
            $table->string('target_type')->default('all')->after('message'); // 'all' atau 'device'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropForeign(['whatsapp_session_id']);
            $table->dropColumn('whatsapp_session_id');
            $table->dropColumn('target_type');
        });
    }
};
