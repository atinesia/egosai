<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('wa_number')->index();
            $table->string('name')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'wa_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
