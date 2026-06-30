<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->string('sender_type'); // contact, ai, agent
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('content');
            $table->string('wa_message_id')->nullable();
            $table->string('status')->default('sent'); // sent, delivered, read, failed
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
