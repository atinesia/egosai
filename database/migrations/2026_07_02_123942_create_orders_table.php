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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique(); // ID Transaksi unik dari Tripay
            $table->string('merchant_ref')->unique(); // ID Invoice dari sistem kita (misal: INV-XXXX)
            $table->string('plan'); // starter, pro, enterprise
            $table->unsignedInteger('amount'); // Nominal harga paket
            $table->string('payment_method'); // Contoh: QRIS, BRIVA, MANDIRIVA
            $table->enum('status', ['unpaid', 'paid', 'expired', 'failed'])->default('unpaid');
            $table->string('checkout_url')->nullable(); // Link pembayaran jika berupa redirect/iframe
            $table->text('qr_url')->nullable(); // Khusus jika bayar pakai QRIS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
