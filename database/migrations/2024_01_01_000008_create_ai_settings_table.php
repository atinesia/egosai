<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('model')->default('llama-3.3-70b-versatile');
            $table->text('system_prompt')->default('Anda adalah asisten customer service yang ramah, jelas, dan membantu pelanggan.');
            $table->decimal('temperature', 3, 2)->default(0.40);
            $table->boolean('is_ai_globally_active')->default(true);
            $table->string('fallback_message')->default('Mohon tunggu, tim kami akan segera membantu Anda.');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
