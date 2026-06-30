<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'wa_number', 'name', 'avatar_url'];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * `wa_number` sekarang menyimpan JID WhatsApp lengkap apa adanya
     * (mis. "62812xxxx@s.whatsapp.net" atau "123456789012345@lid" untuk
     * kontak dengan privasi nomor aktif), BUKAN cuma angka — supaya saat
     * membalas pesan, JID yang dipakai dijamin sama persis dengan yang
     * dikirim WhatsApp, tidak direkonstruksi ulang (itu yang dulu bikin
     * balasan salah sasaran).
     *
     * Accessor ini cuma untuk TAMPILAN di dashboard supaya tidak
     * menampilkan akhiran "@s.whatsapp.net" yang berantakan. Untuk kontak
     * ber-JID @lid, nomor telepon asli memang tidak selalu tersedia dari
     * WhatsApp — dalam kasus itu kita tampilkan label generik saja.
     */
    protected function displayNumber(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->wa_number) {
                    return '-';
                }

                if (str_ends_with($this->wa_number, '@s.whatsapp.net') || str_ends_with($this->wa_number, '@c.us')) {
                    return explode('@', $this->wa_number)[0];
                }

                if (str_ends_with($this->wa_number, '@lid')) {
                    return 'Kontak WhatsApp (privasi nomor aktif)';
                }

                // Data lama dari sebelum perbaikan ini (cuma angka tanpa domain)
                return $this->wa_number;
            },
        );
    }
}
