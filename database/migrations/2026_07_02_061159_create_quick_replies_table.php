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
        Schema::create('quick_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('shortcut'); // Contoh: 'ongkir', 'rek-bca'
            $table->text('message');     // Isi text balasan lengkap
            $table->timestamps();

            // Mencegah ada pintasan ganda pada tenant yang sama
            $table->unique(['tenant_id', 'shortcut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_replies');
    }
};
